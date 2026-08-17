<?php

use App\Models\PortfolioItem;
use App\Models\Talent;
use App\Models\User;

test('a talent profile still lists that students work', function () {
    $viewer = User::factory()->student()->create();
    $talent = Talent::factory()->create([
        'name' => 'Visual Arts',
    ]);
    $artist = User::factory()->student()->create();
    $artist->profile->talents()->attach($talent->id, ['is_favorite' => true]);
    PortfolioItem::factory()->recycle($artist)->recycle($talent)->create([
        'title' => 'Oil study',
    ]);

    $this->actingAs($viewer)
        ->get(route('students.show', $artist))
        ->assertOk()
        ->assertSee('Oil study')
        ->assertSee('Visual Arts');
});
