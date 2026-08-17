<?php

use App\Models\Event;
use App\Models\PortfolioItem;
use App\Models\User;

test('wearable summary returns a compact payload', function () {
    $user = User::factory()->student()->withXp(120)->create([
        'current_rank' => 4,
        'previous_rank' => 6,
    ]);

    PortfolioItem::factory()->recycle($user)->create();
    Event::factory()->create();

    $this->withToken(deviceToken($user))
        ->getJson('/api/v1/wearable/summary')
        ->assertSuccessful()
        ->assertJsonPath('data.xp', 120)
        ->assertJsonPath('data.rank', 4)
        ->assertJsonPath('data.rank_change', 2)
        ->assertJsonStructure([
            'data' => ['xp', 'rank', 'rank_change', 'unread_count', 'next_event', 'latest_portfolio', 'favorite_talents'],
        ]);
});

test('wearable leaderboard returns at most five entries', function () {
    User::factory()->student()->count(8)->create();
    $user = User::factory()->student()->create();

    $this->withToken(deviceToken($user))
        ->getJson('/api/v1/wearable/leaderboard')
        ->assertSuccessful()
        ->assertJsonPath('scope', 'global');

    expect($this->withToken(deviceToken($user))->getJson('/api/v1/wearable/leaderboard')->json('entries'))
        ->toHaveCount(5);
});

test('guests cannot read wearable endpoints', function () {
    $this->getJson('/api/v1/wearable/summary')->assertUnauthorized();
});
