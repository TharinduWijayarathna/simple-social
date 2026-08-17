<?php

use App\Enums\TalentTheme;
use App\Models\PortfolioItem;
use App\Models\Talent;
use App\Models\User;

test('art profiles render the gallery theme', function () {
    $viewer = User::factory()->student()->create();
    $talent = Talent::factory()->create([
        'name' => 'Visual Arts',
        'theme' => TalentTheme::Gallery,
    ]);
    $artist = User::factory()->student()->create();
    $artist->profile->talents()->attach($talent->id, ['is_favorite' => true]);
    PortfolioItem::factory()->recycle($artist)->recycle($talent)->create([
        'title' => 'Oil study',
    ]);

    $this->actingAs($viewer)
        ->get(route('students.show', $artist))
        ->assertOk()
        ->assertSee('theme-gallery', false)
        ->assertSee('Oil study');
});

test('music profiles render the listening room theme', function () {
    $viewer = User::factory()->student()->create();
    $talent = Talent::factory()->create([
        'name' => 'Music & Audio Production',
        'theme' => TalentTheme::Vinyl,
    ]);
    $musician = User::factory()->student()->create();
    $musician->profile->talents()->attach($talent->id, ['is_favorite' => true]);
    PortfolioItem::factory()->recycle($musician)->recycle($talent)->create();

    $this->actingAs($viewer)
        ->get(route('students.show', $musician))
        ->assertOk()
        ->assertSee('theme-vinyl', false);
});
