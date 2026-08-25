<?php

use App\Livewire\Profile\Edit;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

test('user can upload custom profile picture', function () {
    Storage::fake('public');
    $user = User::factory()->student()->create();

    $avatar = UploadedFile::fake()->image('my_avatar.png', 400, 400);

    Livewire::actingAs($user)
        ->test(Edit::class)
        ->set('avatar', $avatar)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('students.show', $user));

    $user->refresh();

    expect($user->profile->avatar_path)->not->toBeNull();
    Storage::disk('public')->assertExists($user->profile->avatar_path);
    expect($user->avatarUrl())->not->toBeNull();
});

test('user can remove custom profile picture', function () {
    Storage::fake('public');
    $user = User::factory()->student()->create();

    $avatar = UploadedFile::fake()->image('my_avatar.png', 400, 400);
    $path = $avatar->store('avatars', 'public');
    $user->profile->update(['avatar_path' => $path]);

    Livewire::actingAs($user)
        ->test(Edit::class)
        ->call('removeAvatar')
        ->assertHasNoErrors();

    $user->refresh();
    expect($user->profile->avatar_path)->toBeNull();
    expect($user->avatarUrl())->toBeNull();
});

test('user avatar is displayed in navbar and feed create post bar when set', function () {
    Storage::fake('public');
    $user = User::factory()->student()->create();

    $avatar = UploadedFile::fake()->image('my_avatar.png', 400, 400);
    $path = $avatar->store('avatars', 'public');
    $user->profile->update(['avatar_path' => $path]);

    $response = $this->actingAs($user)->get(route('home'));

    $response->assertOk();
    $response->assertSee($user->avatarUrl());
});
