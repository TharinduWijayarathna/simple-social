<?php

use App\Enums\DevicePlatform;
use App\Enums\DeviceType;
use App\Models\User;

test('students can register and receive a device token', function () {
    $response = $this->postJson('/api/v1/auth/register', [
        'name' => 'Maya Chen',
        'email' => 'maya@campus.test',
        'password' => 'password',
        'password_confirmation' => 'password',
        'device_name' => 'Pixel Watch',
        'device_type' => DeviceType::Wearable->value,
        'device_platform' => DevicePlatform::WearOs->value,
    ]);

    $response->assertCreated()
        ->assertJsonPath('token_type', 'Bearer')
        ->assertJsonPath('user.name', 'Maya Chen')
        ->assertJsonPath('user.role', 'student');

    expect($response->json('token'))->toContain('|');
    $this->assertModelExists(User::query()->where('email', 'maya@campus.test')->first());
});

test('students can login and receive a device token', function () {
    $user = User::factory()->create([
        'email' => 'maya@campus.test',
    ]);

    $this->postJson('/api/v1/auth/login', [
        'email' => $user->email,
        'password' => 'password',
        'device_name' => 'Watch',
        'device_type' => DeviceType::Wearable->value,
        'device_platform' => DevicePlatform::WearOs->value,
    ])->assertSuccessful()
        ->assertJsonPath('user.id', $user->id);
});

test('logout revokes the current device token', function () {
    $user = User::factory()->create();
    $token = deviceToken($user);

    $logout = $this->withToken($token)
        ->postJson('/api/v1/auth/logout');

    $logout->assertSuccessful();

    $this->withToken($token)
        ->getJson('/api/v1/auth/me')
        ->assertUnauthorized();
});
