<?php

use App\Enums\Role;
use App\Enums\UserStatus;
use App\Livewire\Auth\Register;
use App\Livewire\Students\Index;
use App\Models\Talent;
use App\Models\User;
use App\Notifications\OtpVerificationNotification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

test('student can register with batch, program, profile type and primary talent', function () {
    Notification::fake();

    $campus = User::factory()->create([
        'role' => Role::CampusAdmin,
        'status' => UserStatus::Approved,
    ]);

    $talent = Talent::query()->where('category', 'Performing Arts')->first()
        ?? Talent::factory()->create(['category' => 'Performing Arts', 'name' => 'Singing']);

    $component = Livewire::test(Register::class)
        ->set('accountType', 'student')
        ->set('name', 'John Doe')
        ->set('email', 'johndoe@campus.edu')
        ->call('sendOtp')
        ->assertHasNoErrors();

    $otp = Cache::get('otp:johndoe@campus.edu');

    Notification::assertSentOnDemand(OtpVerificationNotification::class);

    $component
        ->set('otp', $otp)
        ->call('verifyOtp')
        ->assertHasNoErrors()
        ->set('universityId', '2024CS001')
        ->set('campusId', $campus->id)
        ->set('batch', 'Batch 2024')
        ->set('program', 'BSc Software Engineering')
        ->set('faculty', 'Faculty of Computing')
        ->set('department', 'Software Engineering')
        ->set('profileType', '🎤 Performing Arts Creator Account')
        ->set('primaryTalentId', $talent->id)
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->call('register')
        ->assertHasNoErrors();

    $user = User::query()->where('email', 'johndoe@campus.edu')->first();

    expect($user)->not->toBeNull();
    expect($user->profile)->not->toBeNull();
    expect($user->profile->batch)->toBe('Batch 2024');
    expect($user->profile->program)->toBe('BSc Software Engineering');
    expect($user->profile->profile_type)->toBe('🎤 Performing Arts Creator Account');
    expect($user->profile->primary_talent_id)->toBe($talent->id);
});

test('students directory allows filtering by talent category', function () {
    $campus = User::factory()->create([
        'role' => Role::CampusAdmin,
        'status' => UserStatus::Approved,
    ]);

    $talent = Talent::factory()->create(['name' => 'Singing', 'category' => 'Performing Arts']);

    $student = User::factory()->student()->create(['campus_id' => $campus->id]);
    $student->profile->update([
        'profile_type' => '🎤 Performing Arts Creator Account',
        'primary_talent_id' => $talent->id,
    ]);

    Livewire::test(Index::class)
        ->set('category', 'Performing Arts')
        ->assertSee($student->name);
});
