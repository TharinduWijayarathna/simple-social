<?php

use App\Enums\EventApplicationStatus;
use App\Models\Event;
use App\Models\User;

test('students can rsvp to a published event from the wearable endpoint', function () {
    $student = User::factory()->student()->create();
    $event = Event::factory()->create();

    $this->withToken(deviceToken($student))
        ->postJson("/api/v1/wearable/events/{$event->id}/rsvp")
        ->assertSuccessful()
        ->assertJsonPath('status', EventApplicationStatus::RsvpYes->value);

    expect($student->fresh()->xp)->toBe(10);
    $this->assertDatabaseHas('event_applications', [
        'event_id' => $event->id,
        'user_id' => $student->id,
        'status' => EventApplicationStatus::RsvpYes->value,
    ]);
});
