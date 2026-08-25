<?php

use App\Enums\Role;
use App\Enums\TalentTheme;
use App\Enums\UserStatus;
use App\Livewire\Auth\Login;
use App\Livewire\Campus\Dashboard;
use App\Livewire\Portfolio\Create;
use App\Models\Talent;
use App\Models\TalentCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('campus admin can manage custom talents for their campus', function () {
    $admin = User::factory()->create([
        'role' => Role::CampusAdmin,
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
        'role' => Role::CampusAdmin,
        'status' => UserStatus::Approved,
    ]);
    $admin1->update(['campus_id' => $admin1->id]);

    $admin2 = User::factory()->create([
        'role' => Role::CampusAdmin,
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

test('campus admin can ban and unban student profiles', function () {
    $admin = User::factory()->create([
        'role' => Role::CampusAdmin,
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

    // Ban the student
    Livewire::test(Dashboard::class)
        ->set('activeTab', 'students')
        ->call('banStudent', $student->id);

    expect($student->fresh()->status)->toBe(UserStatus::Banned);

    // Banned student cannot login
    Auth::logout();
    Livewire::test(Login::class)
        ->set('email', $student->email)
        ->set('password', 'password')
        ->call('login')
        ->assertHasErrors(['email']);

    // Unban the student
    $this->actingAs($admin);
    Livewire::test(Dashboard::class)
        ->set('activeTab', 'students')
        ->call('unbanStudent', $student->id);

    expect($student->fresh()->status)->toBe(UserStatus::Approved);
});

test('campus admin can edit and delete system default talents', function () {
    $admin = User::factory()->create([
        'role' => Role::CampusAdmin,
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

test('campus admin can manage talent categories separately', function () {
    $admin = User::factory()->create([
        'role' => Role::CampusAdmin,
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
