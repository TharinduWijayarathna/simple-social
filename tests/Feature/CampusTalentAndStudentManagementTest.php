<?php

use App\Enums\Role;
use App\Enums\TalentTheme;
use App\Enums\UserStatus;
use App\Livewire\Auth\Login;
use App\Livewire\Campus\Dashboard;
use App\Livewire\Campus\Rankings;
use App\Livewire\Portfolio\Create;
use App\Models\CampusRanking;
use App\Models\Like;
use App\Models\PortfolioItem;
use App\Models\Talent;
use App\Models\TalentCategory;
use App\Models\User;
use Database\Seeders\TalentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('campus can manage custom talents', function () {
    $admin = User::factory()->create([
        'role' => Role::Campus,
        'status' => UserStatus::Approved,
    ]);

    $admin->update(['campus_id' => $admin->id]);

    $this->actingAs($admin);

    Livewire::test(Dashboard::class)
        ->set('activeTab', 'talents')
        ->set('talentName', 'Belly Dancing')
        ->set('talentCategory', 'Performing Arts')
        ->set('talentTheme', 'stage')
        ->set('talentDescription', 'Dance with coordination.')
        ->call('saveTalent')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('talents', [
        'name' => 'Belly Dancing',
        'campus_id' => $admin->id,
        'category' => 'Performing Arts',
    ]);

    $talent = Talent::where('name', 'Belly Dancing')->first();
    expect($talent->slug)->toBe('belly-dancing-'.$admin->id);

    // Edit the talent
    Livewire::test(Dashboard::class)
        ->call('openTalentForm', $talent->id)
        ->assertSet('talentName', 'Belly Dancing')
        ->set('talentName', 'Belly Dancing Pro')
        ->call('saveTalent')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('talents', [
        'id' => $talent->id,
        'name' => 'Belly Dancing Pro',
    ]);

    // Delete the talent
    Livewire::test(Dashboard::class)
        ->call('deleteTalent', $talent->id);

    $this->assertDatabaseMissing('talents', [
        'id' => $talent->id,
    ]);
});

test('custom talents are scoped to the correct campus', function () {
    $admin1 = User::factory()->create([
        'role' => Role::Campus,
        'status' => UserStatus::Approved,
    ]);
    $admin1->update(['campus_id' => $admin1->id]);

    $admin2 = User::factory()->create([
        'role' => Role::Campus,
        'status' => UserStatus::Approved,
    ]);
    $admin2->update(['campus_id' => $admin2->id]);

    $customTalent = Talent::create([
        'name' => 'Magic Show',
        'category' => 'Unique & Hidden',
        'theme' => TalentTheme::Stage,
        'campus_id' => $admin1->id,
    ]);

    $student1 = User::factory()->create([
        'role' => Role::Student,
        'status' => UserStatus::Approved,
        'campus_id' => $admin1->id,
    ]);

    $student2 = User::factory()->create([
        'role' => Role::Student,
        'status' => UserStatus::Approved,
        'campus_id' => $admin2->id,
    ]);

    // Check visibility for student 1
    $this->actingAs($student1);
    Livewire::test(Create::class)
        ->assertSee('Magic Show');

    // Check visibility for student 2 (should not see it)
    $this->actingAs($student2);
    Livewire::test(Create::class)
        ->assertDontSee('Magic Show');
});

test('campus can suspend and unsuspend student profiles', function () {
    $admin = User::factory()->create([
        'role' => Role::Campus,
        'status' => UserStatus::Approved,
    ]);
    $admin->update(['campus_id' => $admin->id]);

    $student = User::factory()->create([
        'role' => Role::Student,
        'status' => UserStatus::Approved,
        'campus_id' => $admin->id,
        'password' => bcrypt('password'),
    ]);

    $this->actingAs($admin);

    // Suspend the student
    Livewire::test(Dashboard::class)
        ->set('activeTab', 'students')
        ->call('suspendStudent', $student->id);

    expect($student->fresh()->status)->toBe(UserStatus::Banned);

    // Suspended student cannot login
    Auth::logout();
    Livewire::test(Login::class)
        ->set('email', $student->email)
        ->set('password', 'password')
        ->call('login')
        ->assertHasErrors(['email']);

    // Unsuspend the student
    $this->actingAs($admin);
    Livewire::test(Dashboard::class)
        ->set('activeTab', 'students')
        ->call('unsuspendStudent', $student->id);

    expect($student->fresh()->status)->toBe(UserStatus::Approved);
});

test('campus can edit and delete system default talents', function () {
    $admin = User::factory()->create([
        'role' => Role::Campus,
        'status' => UserStatus::Approved,
    ]);
    $admin->update(['campus_id' => $admin->id]);

    $systemTalent = Talent::create([
        'name' => 'System Piano',
        'category' => 'Performing Arts',
        'theme' => TalentTheme::Stage,
        'campus_id' => null,
    ]);

    $this->actingAs($admin);

    // Edit system talent
    Livewire::test(Dashboard::class)
        ->call('openTalentForm', $systemTalent->id)
        ->assertSet('talentName', 'System Piano')
        ->set('talentName', 'Grand Piano Performance')
        ->call('saveTalent')
        ->assertHasNoErrors();

    expect($systemTalent->fresh()->name)->toBe('Grand Piano Performance');

    // Delete system talent
    Livewire::test(Dashboard::class)
        ->call('deleteTalent', $systemTalent->id);

    $this->assertDatabaseMissing('talents', [
        'id' => $systemTalent->id,
    ]);
});

test('campus can manage talent categories separately', function () {
    $admin = User::factory()->create([
        'role' => Role::Campus,
        'status' => UserStatus::Approved,
    ]);
    $admin->update(['campus_id' => $admin->id]);

    $this->actingAs($admin);

    // Create a new category
    Livewire::test(Dashboard::class)
        ->set('activeTab', 'talents')
        ->set('talentSubTab', 'categories')
        ->set('categoryName', 'Culinary & Baking')
        ->call('saveCategory')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('talent_categories', [
        'name' => 'Culinary & Baking',
        'campus_id' => $admin->id,
    ]);

    $category = TalentCategory::where('name', 'Culinary & Baking')->first();

    // Create a talent under this new category
    Livewire::test(Dashboard::class)
        ->set('activeTab', 'talents')
        ->set('talentSubTab', 'talents')
        ->set('talentName', 'Cake Decorating')
        ->set('talentCategory', 'Culinary & Baking')
        ->set('talentTheme', 'gallery')
        ->call('saveTalent')
        ->assertHasNoErrors();

    $talent = Talent::where('name', 'Cake Decorating')->first();
    expect($talent->category)->toBe('Culinary & Baking');

    // Edit the category name - should sync category on talents
    Livewire::test(Dashboard::class)
        ->call('openCategoryForm', $category->id)
        ->assertSet('categoryName', 'Culinary & Baking')
        ->set('categoryName', 'Culinary Arts & Pastry')
        ->call('saveCategory')
        ->assertHasNoErrors();

    expect($category->fresh()->name)->toBe('Culinary Arts & Pastry');
    expect($talent->fresh()->category)->toBe('Culinary Arts & Pastry');

    // Delete the category
    Livewire::test(Dashboard::class)
        ->call('deleteCategory', $category->id);

    $this->assertDatabaseMissing('talent_categories', [
        'id' => $category->id,
    ]);
    expect($talent->fresh()->category)->toBe('General User');
});

test('seeded talent categories are displayed with their existing talent relationships', function () {
    $admin = User::factory()->campus()->create();
    $admin->update(['campus_id' => $admin->id]);

    $this->seed(TalentSeeder::class);
    $this->actingAs($admin);

    $performingArts = TalentCategory::query()
        ->where('name', 'Performing Arts')
        ->whereNull('campus_id')
        ->firstOrFail();

    expect($performingArts->talents)->toHaveCount(10)
        ->and($performingArts->talents->pluck('category')->unique()->values()->all())
        ->toBe(['Performing Arts']);

    Livewire::test(Dashboard::class)
        ->set('activeTab', 'talents')
        ->set('talentSubTab', 'categories')
        ->assertSee('Talent Categories (5)')
        ->assertSee('Performing Arts')
        ->assertSee('10 talents assigned');
});

test('campus rankings section shows all student ranks with talent xp', function () {
    $admin = User::factory()->create([
        'role' => Role::Campus,
        'status' => UserStatus::Approved,
    ]);
    $admin->update(['campus_id' => $admin->id]);

    $talent = Talent::create([
        'name' => 'Classical Singing',
        'category' => 'Performing Arts',
        'theme' => TalentTheme::Stage,
        'campus_id' => $admin->id,
    ]);

    $student1 = User::factory()->create([
        'role' => Role::Student,
        'status' => UserStatus::Approved,
        'campus_id' => $admin->id,
    ]);
    $student1->profile()->update(['primary_talent_id' => $talent->id]);

    $student2 = User::factory()->create([
        'role' => Role::Student,
        'status' => UserStatus::Approved,
        'campus_id' => $admin->id,
    ]);
    $student2->profile()->update(['primary_talent_id' => $talent->id]);

    // Student 1 has 5 likes
    $post1 = PortfolioItem::factory()->create([
        'user_id' => $student1->id,
        'talent_id' => $talent->id,
        'published_at' => now()->subDay(),
    ]);
    Like::factory()->count(5)->create([
        'likeable_id' => $post1->id,
        'likeable_type' => PortfolioItem::class,
    ]);

    // Student 2 has 2 likes
    $post2 = PortfolioItem::factory()->create([
        'user_id' => $student2->id,
        'talent_id' => $talent->id,
        'published_at' => now()->subDay(),
    ]);
    Like::factory()->count(2)->create([
        'likeable_id' => $post2->id,
        'likeable_type' => PortfolioItem::class,
    ]);

    $ranking = CampusRanking::create([
        'campus_id' => $admin->id,
        'talent_id' => $talent->id,
        'title' => 'Top Classical Singers',
        'is_active' => true,
    ]);

    $this->actingAs($admin);

    Livewire::test(Rankings::class)
        ->assertSee('Top Classical Singers')
        ->assertSee($student1->name)
        ->assertSee('35')
        ->assertSee('XP')
        ->assertSee($student2->name)
        ->assertSee('29');
});
