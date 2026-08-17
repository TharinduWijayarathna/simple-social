<?php

use App\Livewire\Auth\Login;
use App\Livewire\Feed;
use App\Models\PortfolioItem;
use App\Models\User;
use Livewire\Livewire;

test('the home page shows campus posts', function () {
    PortfolioItem::factory()->create([
        'title' => 'Campus mural',
    ]);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Campus mural');
});

test('guests can view the login page', function () {
    $this->get(route('login'))->assertOk();
});

test('students sign in to the campus home feed', function () {
    $user = User::factory()->create([
        'email' => 'studio@campus.test',
    ]);

    Livewire::test(Login::class)
        ->set('email', $user->email)
        ->set('password', 'password')
        ->call('login')
        ->assertHasNoErrors()
        ->assertRedirect(route('home'));

    $this->assertAuthenticatedAs($user);
});

test('guests are redirected away from private campus pages', function () {
    $this->get(route('profile.edit'))->assertRedirect(route('login'));
});

test('authenticated students can open the home feed', function () {
    $user = User::factory()->student()->create();

    $this->actingAs($user)
        ->get(route('home'))
        ->assertOk();

    Livewire::actingAs($user)
        ->test(Feed::class)
        ->assertOk();
});
