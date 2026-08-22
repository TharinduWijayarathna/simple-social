<?php

use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Auth\AdminLogin;
use App\Livewire\Events\Show as EventsShow;
use App\Models\Event;
use App\Models\User;
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

test('campus admins can open the campus desk', function () {
    $campusAdmin = User::factory()->campusAdmin()->create();
    Event::factory()->recycle($campusAdmin)->create([
        'title' => 'Open mic night',
    ]);

    $this->actingAs($campusAdmin)
        ->get(route('campus.dashboard'))
        ->assertOk()
        ->assertSee('Open mic night');
});

test('campus admins cannot open super admin', function () {
    $this->actingAs(User::factory()->campusAdmin()->create())
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

test('super admins can appoint campus admins', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $student = User::factory()->student()->create();

    Livewire::actingAs($superAdmin)
        ->test(AdminDashboard::class)
        ->call('assignRole', $student->id, 'campus_admin')
        ->assertHasNoErrors();

    expect($student->fresh()->isCampusAdmin())->toBeTrue();
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
