<?php

use App\Enums\EventApplicationStatus;
use App\Livewire\Events\Create;
use App\Livewire\Events\Index;
use App\Livewire\Events\Show;
use App\Models\Event;
use App\Models\EventApplication;
use App\Models\Talent;
use App\Models\User;
use App\Notifications\EventApplicationSelectedNotification;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

test('campus admin can view create event page', function () {
    $campusAdmin = User::factory()->campusAdmin()->create();

    $this->actingAs($campusAdmin)
        ->get(route('events.create'))
        ->assertSuccessful();
});

test('students cannot access create event page', function () {
    $student = User::factory()->student()->create();

    $this->actingAs($student)
        ->get(route('events.create'))
        ->assertForbidden();
});

test('campus admin can create an event with full details and talent requirements', function () {
    $campusAdmin = User::factory()->campusAdmin()->create();
    $talent = Talent::factory()->create(['name' => 'Photographer']);

    $this->actingAs($campusAdmin);

    Livewire::test(Create::class)
        ->set('title', 'VibeCraft Annual Gala 2026')
        ->set('event_type', 'Concert')
        ->set('description', 'Grand annual music and showcase gala.')
        ->set('requirements', 'Must bring own camera gear.')
        ->set('location', 'Main Campus Auditorium')
        ->set('contact_email', 'campus.gala@university.edu')
        ->set('contact_phone', '+1 555-0199')
        ->set('contact_instructions', 'Report to Room 101 at 8 AM.')
        ->set('starts_at', now()->addDays(5)->toDateTimeString())
        ->set('ends_at', now()->addDays(5)->addHours(4)->toDateTimeString())
        ->set('capacity', 200)
        ->set('talent_requirements', [
            ['talent_id' => $talent->id, 'slots' => 3, 'notes' => 'Event Photographer'],
        ])
        ->call('save')
        ->assertHasNoErrors();

    $event = Event::query()->where('title', 'VibeCraft Annual Gala 2026')->first();

    expect($event)->not->toBeNull();
    expect($event->event_type)->toBe('Concert');
    expect($event->contact_email)->toBe('campus.gala@university.edu');
    expect($event->contact_phone)->toBe('+1 555-0199');
    expect($event->requirements)->toBe('Must bring own camera gear.');

    $this->assertDatabaseHas('event_talents', [
        'event_id' => $event->id,
        'talent_id' => $talent->id,
        'slots' => 3,
        'notes' => 'Event Photographer',
    ]);
});

test('student can apply for an event with specific talent role and pitch message', function () {
    $campusAdmin = User::factory()->campusAdmin()->create();
    $talent = Talent::factory()->create(['name' => 'Singer']);
    $event = Event::factory()->create([
        'organizer_id' => $campusAdmin->id,
        'is_published' => true,
    ]);

    $student = User::factory()->student()->create();

    $this->actingAs($student);

    Livewire::test(Show::class, ['event' => $event])
        ->set('selected_talent_id', $talent->id)
        ->set('message', 'I am a vocalist with 3 years of performance experience.')
        ->call('applyOrRsvp')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('event_applications', [
        'event_id' => $event->id,
        'user_id' => $student->id,
        'talent_id' => $talent->id,
        'status' => EventApplicationStatus::Pending->value,
        'message' => 'I am a vocalist with 3 years of performance experience.',
    ]);
});

test('campus admin can select applicant and trigger notification with contact details', function () {
    Notification::fake();

    $campusAdmin = User::factory()->campusAdmin()->create();
    $talent = Talent::factory()->create(['name' => 'Vocalist']);
    $event = Event::factory()->create([
        'organizer_id' => $campusAdmin->id,
        'is_published' => true,
        'contact_email' => 'campus@university.edu',
        'contact_phone' => '+15551234',
        'contact_instructions' => 'Meet at stage 1',
    ]);

    $student = User::factory()->student()->create(['campus_id' => $campusAdmin->id]);
    $application = EventApplication::create([
        'event_id' => $event->id,
        'user_id' => $student->id,
        'talent_id' => $talent->id,
        'status' => EventApplicationStatus::Pending,
        'message' => 'Ready to sing!',
    ]);

    Livewire::actingAs($campusAdmin)
        ->test(Show::class, ['event' => $event])
        ->call('selectCandidate', $application->id)
        ->assertHasNoErrors();

    expect($application->fresh()->status)->toBe(EventApplicationStatus::Accepted);

    Notification::assertSentTo(
        $student,
        EventApplicationSelectedNotification::class,
        function ($notification) use ($event) {
            return $notification->event->id === $event->id;
        }
    );
});

test('chosen student can see campus contact details on event page and index tab', function () {
    $campusAdmin = User::factory()->campusAdmin()->create();
    $event = Event::factory()->create([
        'organizer_id' => $campusAdmin->id,
        'is_published' => true,
        'contact_email' => 'contact.campus@university.edu',
        'contact_phone' => '+1 (555) 999-8888',
        'contact_instructions' => 'Bring student ID and report to backstage.',
    ]);

    $student = User::factory()->student()->create();
    EventApplication::create([
        'event_id' => $event->id,
        'user_id' => $student->id,
        'status' => EventApplicationStatus::Accepted,
    ]);

    $this->actingAs($student);

    // Event Show view
    $this->get(route('events.show', $event))
        ->assertSuccessful()
        ->assertSee('Congratulations! You have been chosen for this event!')
        ->assertSee('contact.campus@university.edu')
        ->assertSee('+1 (555) 999-8888')
        ->assertSee('Bring student ID and report to backstage.');

    // Event Index page with Chosen tab
    Livewire::test(Index::class)
        ->set('activeTab', 'chosen')
        ->assertSee($event->title)
        ->assertSee('contact.campus@university.edu')
        ->assertSee('+1 (555) 999-8888');
});
