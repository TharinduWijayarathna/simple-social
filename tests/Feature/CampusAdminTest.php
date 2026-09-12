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
use Illuminate\Database\Eloquent\ModelNotFoundException;
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

test('super admins approve campuses but cannot approve or reject students', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $campus = User::factory()->campus()->pending()->create();

    $pendingStudent = User::factory()->student()->create([
        'status' => UserStatus::Pending,
        'campus_id' => $campus->id,
    ]);

    Livewire::actingAs($superAdmin)
        ->test(AdminDashboard::class)
        ->set('activeTab', 'students')
        ->assertSet('activeTab', 'overview')
        ->assertDontSee('Student Management')
        ->set('activeTab', 'campuses')
        ->call('approveCampus', $campus->id)
        ->assertHasNoErrors();

    expect($campus->fresh()->status)->toBe(UserStatus::Approved);

    Livewire::actingAs($superAdmin)
        ->test(CampusDashboard::class)
        ->call('approveStudent', $pendingStudent->id)
        ->assertForbidden();

    Livewire::actingAs($superAdmin)
        ->test(CampusDashboard::class)
        ->call('rejectStudent', $pendingStudent->id)
        ->assertForbidden();

    expect($pendingStudent->fresh()->status)->toBe(UserStatus::Pending);
});

test('a campus can only approve or reject its own pending students', function () {
    $campus = User::factory()->campus()->create();
    $otherCampus = User::factory()->campus()->create();
    $studentToApprove = User::factory()->student()->pending()->create(['campus_id' => $campus->id]);
    $studentToReject = User::factory()->student()->pending()->create(['campus_id' => $campus->id]);
    $otherStudent = User::factory()->student()->pending()->create(['campus_id' => $otherCampus->id]);

    Livewire::actingAs($campus)
        ->test(CampusDashboard::class)
        ->call('approveStudent', $studentToApprove->id)
        ->assertHasNoErrors()
        ->call('rejectStudent', $studentToReject->id)
        ->assertHasNoErrors();

    expect(fn () => Livewire::actingAs($campus)
        ->test(CampusDashboard::class)
        ->call('approveStudent', $otherStudent->id))
        ->toThrow(ModelNotFoundException::class);

    expect($studentToApprove->fresh()->status)->toBe(UserStatus::Approved)
        ->and($studentToReject->fresh()->status)->toBe(UserStatus::Rejected)
        ->and($otherStudent->fresh()->status)->toBe(UserStatus::Pending);
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
