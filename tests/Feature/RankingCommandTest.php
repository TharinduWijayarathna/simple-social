<?php

use App\Models\User;

test('rankings recompute assigns ranks by xp', function () {
    $first = User::factory()->student()->withXp(300)->create();
    $second = User::factory()->student()->withXp(120)->create();
    $third = User::factory()->student()->withXp(40)->create();

    $this->artisan('rankings:recompute')->assertSuccessful();

    expect($first->fresh()->current_rank)->toBe(1)
        ->and($second->fresh()->current_rank)->toBe(2)
        ->and($third->fresh()->current_rank)->toBe(3);
});
