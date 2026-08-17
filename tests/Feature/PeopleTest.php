<?php

use App\Livewire\Students\Index as StudentsIndex;
use App\Models\Follow;
use App\Models\PortfolioItem;
use App\Models\User;
use Livewire\Livewire;

test('students can browse suggested campus people', function () {
    $viewer = User::factory()->student()->create();
    $other = User::factory()->student()->create([
        'name' => 'Iresha Wickramasinghe',
    ]);
    PortfolioItem::factory()->recycle($other)->create([
        'title' => 'Campus mural',
    ]);

    $this->actingAs($viewer)
        ->get(route('students.index'))
        ->assertOk()
        ->assertSee('Suggested for you')
        ->assertSee('Iresha Wickramasinghe')
        ->assertSee('Explore')
        ->assertSee('Campus mural')
        ->assertDontSee($viewer->name);
});

test('students can follow someone from the people page', function () {
    $viewer = User::factory()->student()->create();
    $other = User::factory()->student()->create();

    Livewire::actingAs($viewer)
        ->test(StudentsIndex::class)
        ->call('follow', $other->id)
        ->assertHasNoErrors();

    $this->assertModelExists(
        Follow::query()
            ->whereBelongsTo($viewer, 'follower')
            ->whereBelongsTo($other, 'following')
            ->first(),
    );
});
