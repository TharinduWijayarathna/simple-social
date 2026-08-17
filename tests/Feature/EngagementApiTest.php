<?php

use App\Models\PortfolioItem;
use App\Models\User;
use App\Notifications\PortfolioLikedNotification;
use Illuminate\Support\Facades\Notification;

test('liking a portfolio item awards xp to the owner', function () {
    Notification::fake();

    $owner = User::factory()->student()->create();
    $fan = User::factory()->student()->create();
    $item = PortfolioItem::factory()->recycle($owner)->create();

    $this->withToken(deviceToken($fan))
        ->postJson("/api/v1/portfolio-items/{$item->id}/likes")
        ->assertSuccessful()
        ->assertJsonPath('liked', true);

    expect($owner->fresh()->xp)->toBe(2);
    Notification::assertSentTo($owner, PortfolioLikedNotification::class);
});

test('students can follow another student', function () {
    Notification::fake();

    $artist = User::factory()->student()->create();
    $fan = User::factory()->student()->create();

    $this->withToken(deviceToken($fan))
        ->postJson("/api/v1/students/{$artist->id}/follow")
        ->assertSuccessful()
        ->assertJsonPath('following', true);

    expect($artist->fresh()->xp)->toBe(3);
});
