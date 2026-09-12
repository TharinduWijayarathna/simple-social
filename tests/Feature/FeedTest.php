<?php

use App\Livewire\Feed;
use App\Livewire\PostCard;
use App\Models\Follow;
use App\Models\PortfolioItem;
use App\Models\Share;
use App\Models\User;
use Livewire\Livewire;

test('the homepage shows published campus work without signing in', function () {
    PortfolioItem::factory()->create([
        'title' => 'Campus mural',
    ]);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Campus mural')
        ->assertSee('University talent, on one wall.');
});

test('students can like comment and share a homepage post', function () {
    $viewer = User::factory()->student()->create();
    $item = PortfolioItem::factory()->create();

    Livewire::actingAs($viewer)
        ->test(PostCard::class, ['item' => $item])
        ->call('like')
        ->assertHasNoErrors();

    expect($item->likes()->whereBelongsTo($viewer)->exists())->toBeTrue();

    Livewire::actingAs($viewer)
        ->test(PostCard::class, ['item' => $item])
        ->set('body', 'Stunning work')
        ->call('comment')
        ->assertHasNoErrors();

    expect($item->comments()->where('body', 'Stunning work')->exists())->toBeTrue();

    Livewire::actingAs($viewer)
        ->test(PostCard::class, ['item' => $item])
        ->call('share')
        ->assertDispatched('share-copied');

    $this->assertModelExists(
        Share::query()
            ->whereBelongsTo($viewer)
            ->whereBelongsTo($item)
            ->first(),
    );
});

test('students can follow a rising student from the people on campus rail', function () {
    $viewer = User::factory()->student()->create(['xp' => 0]);
    $risingStudent = User::factory()->student()->create(['xp' => 500]);

    Livewire::actingAs($viewer)
        ->test(Feed::class)
        ->call('follow', $risingStudent->id)
        ->assertHasNoErrors();

    $this->assertModelExists(
        Follow::query()
            ->where('follower_id', $viewer->id)
            ->where('following_id', $risingStudent->id)
            ->first(),
    );
});
