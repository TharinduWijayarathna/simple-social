<?php

use App\Livewire\Profile\Edit;
use App\Models\PortfolioItem;
use App\Models\User;
use Livewire\Livewire;

test('a student profile shows their posts and about info', function () {
    $viewer = User::factory()->student()->create();
    $student = User::factory()->student()->create();
    $student->profile->update([
        'headline' => 'Campus painter',
        'birthday' => '2003-05-12',
        'location' => 'Colombo',
    ]);
    PortfolioItem::factory()->recycle($student)->create([
        'title' => 'Oil study',
    ]);

    $this->actingAs($viewer)
        ->get(route('students.show', $student))
        ->assertOk()
        ->assertSee('Campus painter')
        ->assertSee('Oil study')
        ->assertSee('Born May 12')
        ->assertSee('Lives in Colombo');
});

test('students can update a short description and birthday', function () {
    $user = User::factory()->student()->create();

    Livewire::actingAs($user)
        ->test(Edit::class)
        ->set('name', $user->name)
        ->set('headline', 'Dancer at ICBT')
        ->set('birthday', '2004-02-20')
        ->set('location', 'Kandy')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('students.show', $user));

    $profile = $user->fresh()->profile;

    expect($profile->headline)->toBe('Dancer at ICBT')
        ->and($profile->location)->toBe('Kandy')
        ->and($profile->birthday->toDateString())->toBe('2004-02-20');
});

test('opening profile takes a student to their own facebook-style page', function () {
    $user = User::factory()->student()->create();

    $this->actingAs($user)
        ->get(route('profile.show'))
        ->assertRedirect(route('students.show', $user));
});
