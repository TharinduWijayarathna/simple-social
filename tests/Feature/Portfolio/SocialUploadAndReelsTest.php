<?php

use App\Enums\PortfolioMediaType;
use App\Livewire\Portfolio\Create;
use App\Models\PortfolioItem;
use App\Models\Status;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

test('user can access creation studio page', function () {
    $user = User::factory()->student()->create();

    $this->actingAs($user)
        ->get(route('portfolio.create'))
        ->assertSuccessful()
        ->assertSee('Share with Campus');
});

test('user can upload a 24h campus story via upload studio', function () {
    Storage::fake('public');
    $user = User::factory()->student()->create();

    $file = UploadedFile::fake()->image('story_pic.jpg', 600, 800);

    Livewire::actingAs($user)
        ->test(Create::class)
        ->set('upload_type', 'story')
        ->set('description', 'Late night campus coding session!')
        ->set('file', $file)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('home'));

    $status = Status::query()->where('user_id', $user->id)->first();

    expect($status)->not->toBeNull();
    expect($status->caption)->toBe('Late night campus coding session!');
    expect($status->isActive())->toBeTrue();
});

test('user can upload a video reel portfolio post via upload studio', function () {
    Storage::fake('public');
    $user = User::factory()->student()->create();

    $file = UploadedFile::fake()->create('dance_reel.mp4', 2000, 'video/mp4');

    Livewire::actingAs($user)
        ->test(Create::class)
        ->set('upload_type', 'post')
        ->set('title', 'Campus Dance Showcase Reel')
        ->set('description', 'Check out my solo dance performance!')
        ->set('file', $file)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('home'));

    $post = PortfolioItem::query()->where('title', 'Campus Dance Showcase Reel')->first();

    expect($post)->not->toBeNull();
    expect($post->isVideo())->toBeTrue();
    expect($post->media_type)->toBe(PortfolioMediaType::Video);
});

test('feed page displays instagram reel video player for video posts', function () {
    Storage::fake('public');
    $user = User::factory()->student()->create();

    $post = PortfolioItem::factory()->create([
        'user_id' => $user->id,
        'title' => 'Sample Music Reel',
        'media_type' => PortfolioMediaType::Video,
        'file_path' => 'portfolio/sample.mp4',
        'published_at' => now(),
    ]);

    $this->actingAs($user)
        ->get(route('home'))
        ->assertSuccessful()
        ->assertSee('Sample Music Reel')
        ->assertSee('REEL 🎥');
});
