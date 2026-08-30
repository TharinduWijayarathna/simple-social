<?php

use App\Livewire\Auth\Register;
use App\Models\User;
use App\Notifications\OtpVerificationNotification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

test('registration is blocked for students until the email otp is verified', function () {
    Notification::fake();

    Livewire::test(Register::class)
        ->set('accountType', 'student')
        ->set('name', 'Jane Doe')
        ->set('email', 'jane@campus.edu')
        ->set('universityId', '2024CS002')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->call('register')
        ->assertSet('submitted', false);

    expect(User::query()->where('email', 'jane@campus.edu')->exists())->toBeFalse();
});

test('sending an otp emails a code and verifying it with the wrong code fails', function () {
    Notification::fake();

    $component = Livewire::test(Register::class)
        ->set('accountType', 'student')
        ->set('name', 'Jane Doe')
        ->set('email', 'jane@campus.edu')
        ->call('sendOtp')
        ->assertSet('otpSent', true);

    Notification::assertSentOnDemand(OtpVerificationNotification::class);

    $component
        ->set('otp', '000000')
        ->call('verifyOtp')
        ->assertSet('otpVerified', false)
        ->assertSet('otpError', 'That code is incorrect. Please try again.');
});

test('verifying the correct otp marks the email as verified', function () {
    Notification::fake();

    $component = Livewire::test(Register::class)
        ->set('accountType', 'student')
        ->set('name', 'Jane Doe')
        ->set('email', 'jane@campus.edu')
        ->call('sendOtp');

    $otp = Cache::get('otp:jane@campus.edu');

    $component
        ->set('otp', $otp)
        ->call('verifyOtp')
        ->assertSet('otpVerified', true);
});

test('resending an otp before the throttle window elapses is blocked', function () {
    Notification::fake();

    Livewire::test(Register::class)
        ->set('accountType', 'student')
        ->set('name', 'Jane Doe')
        ->set('email', 'jane@campus.edu')
        ->call('sendOtp')
        ->assertSet('otpSent', true)
        ->call('sendOtp')
        ->assertSet('otpError', 'Please wait a moment before requesting another code.');

    Notification::assertSentOnDemandTimes(OtpVerificationNotification::class, 1);
});
