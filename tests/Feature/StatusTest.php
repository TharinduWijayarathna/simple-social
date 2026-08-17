<?php

use App\Livewire\Statuses\Create as StatusCreate;
use App\Models\Status;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

test('active campus statuses appear under the composer', function () {
    $viewer = User::factory()->student()->create();
    $author = User::factory()->student()->create([
        'name' => 'Iresha Wickramasinghe',
    ]);
    Status::factory()->recycle($author)->create();

    $this->actingAs($viewer)
        ->get(route('home'))
        ->assertOk()
        ->assertSee('Create story')
        ->assertSee('Iresha Wickramasinghe');
});

test('expired statuses disappear from the homepage after a day', function () {
    $viewer = User::factory()->student()->create();
    $status = Status::factory()->expired()->create();

    $this->actingAs($viewer)
        ->get(route('home'))
        ->assertOk()
        ->assertDontSee('/status/'.$status->id, false);
});

test('students can share a status that expires in 24 hours', function () {
    Storage::fake('public');
    $user = User::factory()->student()->create();

    Livewire::actingAs($user)
        ->test(StatusCreate::class)
        ->set('caption', 'Campus night')
        ->set('image', UploadedFile::fake()->image('story.jpg'))
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('home'));

    $status = Status::query()->whereBelongsTo($user)->first();

    expect($status)->not->toBeNull()
        ->and($status->caption)->toBe('Campus night')
        ->and($status->expires_at->greaterThan(now()->addHours(23)))->toBeTrue()
        ->and($status->expires_at->lessThanOrEqualTo(now()->addDay()))->toBeTrue();
});

test('an expired status cannot be opened', function () {
    $user = User::factory()->student()->create();
    $status = Status::factory()->expired()->create();

    $this->actingAs($user)
        ->get(route('status.show', $status))
        ->assertNotFound();
});
