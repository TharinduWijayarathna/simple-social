<?php

use App\Enums\UserStatus;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Announcements\Index as AnnouncementsIndex;
use App\Livewire\Auth\AdminLogin;
use App\Livewire\Campus\Dashboard as CampusDashboard;
use App\Livewire\Events\Show as EventsShow;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Livewire\Livewire;

test('students cannot open the campus desk or super admin', function () {
    $student = User::factory()->student()->create();

    $this->actingAs($student)
        ->get(route('campus.dashboard'))
        ->assertForbidden();

    $this->actingAs($student)
        ->get(route('admin.dashboard'))
        ->assertForbidden();
});

test('campuses can open the campus desk', function () {
    $campusAdmin = User::factory()->campus()->create();
    Event::factory()->recycle($campusAdmin)->create([
        'title' => 'Open mic night',
    ]);

    $this->actingAs($campusAdmin)
        ->get(route('campus.dashboard'))
        ->assertOk()
        ->assertSee('Open mic night');
});

test('campuses cannot open super admin', function () {
    $this->actingAs(User::factory()->campus()->create())
        ->get(route('admin.dashboard'))
        ->assertForbidden();
});

test('super admins can open the admin dashboard', function () {
    $this->actingAs(User::factory()->superAdmin()->create())
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('Overview');
});

test('super admins can sign in through the admin portal', function () {
    $superAdmin = User::factory()->superAdmin()->create([
        'password' => 'password',
    ]);

    Livewire::test(AdminLogin::class)
        ->set('email', $superAdmin->email)
        ->set('password', 'password')
        ->call('login')
        ->assertHasNoErrors()
        ->assertRedirect(route('admin.dashboard'));

    $this->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('Overview');
});

test('super admins cannot turn a student into a campus without campus registration details', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $student = User::factory()->student()->create();

    Livewire::actingAs($superAdmin)
        ->test(AdminDashboard::class)
        ->call('assignRole', $student->id, 'campus')
        ->assertForbidden();

    expect($student->fresh()->isStudent())->toBeTrue();
});

test('students can join a published campus event from the web', function () {
    $student = User::factory()->student()->create();
    $event = Event::factory()->create([
        'title' => 'Gallery night',
    ]);

    Livewire::actingAs($student)
        ->test(EventsShow::class, ['event' => $event])
        ->call('rsvp')
        ->assertHasNoErrors();

    expect($student->eventApplications()->whereBelongsTo($event)->exists())->toBeTrue();
});

test('super admin can view campuses tab without lazy loading violations', function () {
    Model::preventLazyLoading(true);

    $superAdmin = User::factory()->superAdmin()->create();
    User::factory()->campus()->count(3)->create();

    $this->actingAs($superAdmin)
        ->get(route('admin.dashboard', ['tab' => 'campuses']))
        ->assertOk();
});

test('super admin can manage, approve, reject, ban and unban students across campuses', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $campus = User::factory()->campus()->create();

    $pendingStudent = User::factory()->student()->create([
        'status' => UserStatus::Pending,
        'campus_id' => $campus->id,
    ]);

    $approvedStudent = User::factory()->student()->create([
        'status' => UserStatus::Approved,
        'campus_id' => $campus->id,
    ]);

    // View students tab as super admin
    $this->actingAs($superAdmin)
        ->get(route('admin.dashboard', ['tab' => 'students']))
        ->assertOk()
        ->assertSee($pendingStudent->name)
        ->assertSee($approvedStudent->name);

    // Approve pending student
    Livewire::actingAs($superAdmin)
        ->test(AdminDashboard::class)
        ->call('approveStudent', $pendingStudent->id)
        ->assertHasNoErrors();

    expect($pendingStudent->fresh()->status)->toBe(UserStatus::Approved);

    // Ban approved student
    Livewire::actingAs($superAdmin)
        ->test(AdminDashboard::class)
        ->call('banStudent', $approvedStudent->id)
        ->assertHasNoErrors();

    expect($approvedStudent->fresh()->status)->toBe(UserStatus::Banned);

    // Unban student
    Livewire::actingAs($superAdmin)
        ->test(AdminDashboard::class)
        ->call('unbanStudent', $approvedStudent->id)
        ->assertHasNoErrors();

    expect($approvedStudent->fresh()->status)->toBe(UserStatus::Approved);

    // Reject student
    Livewire::actingAs($superAdmin)
        ->test(AdminDashboard::class)
        ->call('rejectStudent', $pendingStudent->id)
        ->assertHasNoErrors();

    expect($pendingStudent->fresh()->status)->toBe(UserStatus::Rejected);
});

test('campus announcement shows to its students on the announcements page', function () {
    $campusAdmin = User::factory()->campus()->create();
    $student = User::factory()->student()->create(['campus_id' => $campusAdmin->id]);

    Livewire::actingAs($campusAdmin)
        ->test(CampusDashboard::class)
        ->set('announcementTitle', 'Sports fest update')
        ->set('announcementBody', 'Sports fest moved to Friday!')
        ->call('saveAnnouncement', true)
        ->assertHasNoErrors();

    Livewire::actingAs($student)
        ->test(AnnouncementsIndex::class)
        ->assertSee('Sports fest moved to Friday!');
});
