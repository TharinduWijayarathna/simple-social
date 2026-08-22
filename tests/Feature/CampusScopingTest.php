<?php

use App\Models\Collaboration;
use App\Models\Event;
use App\Models\PortfolioItem;
use App\Models\Status;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('students can only see resources belonging to their own campus', function () {
    // Create Campuses (Campus Admin users)
    $campusA = User::factory()->campusAdmin()->create(['name' => 'ICBT Campus']);
    $campusB = User::factory()->campusAdmin()->create(['name' => 'SLIIT Campus']);

    // Create Students for Campus A
    $studentA1 = User::factory()->student()->create([
        'name' => 'John A1',
        'campus_id' => $campusA->id,
    ]);
    $studentA2 = User::factory()->student()->create([
        'name' => 'Alice A2',
        'campus_id' => $campusA->id,
    ]);

    // Create Student for Campus B
    $studentB = User::factory()->student()->create([
        'name' => 'Bob B1',
        'campus_id' => $campusB->id,
    ]);

    // Create Portfolio Items
    $itemA = PortfolioItem::factory()->create([
        'title' => 'Artwork A',
        'user_id' => $studentA2->id,
        'published_at' => now(),
    ]);
    $itemB = PortfolioItem::factory()->create([
        'title' => 'Artwork B',
        'user_id' => $studentB->id,
        'published_at' => now(),
    ]);

    // Create Status stories
    $statusA = Status::factory()->create([
        'caption' => 'Vibes A',
        'user_id' => $studentA2->id,
        'expires_at' => now()->addDay(),
    ]);
    $statusB = Status::factory()->create([
        'caption' => 'Vibes B',
        'user_id' => $studentB->id,
        'expires_at' => now()->addDay(),
    ]);

    // Create Events
    $eventA = Event::factory()->create([
        'title' => 'Concert A',
        'organizer_id' => $campusA->id,
        'is_published' => true,
        'starts_at' => now()->addDays(2),
    ]);
    $eventB = Event::factory()->create([
        'title' => 'Concert B',
        'organizer_id' => $campusB->id,
        'is_published' => true,
        'starts_at' => now()->addDays(2),
    ]);

    // Create Collaborations
    $collabA = Collaboration::factory()->create([
        'title' => 'Project A',
        'owner_id' => $studentA2->id,
    ]);
    $collabB = Collaboration::factory()->create([
        'title' => 'Project B',
        'owner_id' => $studentB->id,
    ]);

    // Act as Student A1 and assert campus scoping is enforced
    $this->actingAs($studentA1);

    // 1. User Scoping
    $users = User::all();
    expect($users->pluck('id'))->toContain($studentA1->id, $studentA2->id, $campusA->id)
        ->not->toContain($studentB->id, $campusB->id);

    // 2. Portfolio Item Scoping
    $items = PortfolioItem::all();
    expect($items->pluck('id'))->toContain($itemA->id)
        ->not->toContain($itemB->id);

    // 3. Status Scoping
    $statuses = Status::all();
    expect($statuses->pluck('id'))->toContain($statusA->id)
        ->not->toContain($statusB->id);

    // 4. Event Scoping
    $events = Event::all();
    expect($events->pluck('id'))->toContain($eventA->id)
        ->not->toContain($eventB->id);

    // 5. Collaboration Scoping
    $collabs = Collaboration::all();
    expect($collabs->pluck('id'))->toContain($collabA->id)
        ->not->toContain($collabB->id);
});
