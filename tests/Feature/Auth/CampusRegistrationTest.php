<?php

use App\Enums\Role;
use App\Enums\UserStatus;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Auth\Register;
use App\Models\User;
use Livewire\Livewire;

test('a campus can register with its details for super admin approval', function () {
    Livewire::test(Register::class)
        ->set('accountType', 'campus')
        ->set('name', 'Nimali Perera')
        ->set('email', 'hello@centralarts.edu')
        ->set('campusName', 'Central Arts Campus')
        ->set('campusPhone', '+94 11 234 5678')
        ->set('campusAddress', '12 Creative Avenue, Colombo 07')
        ->set('campusWebsite', 'https://centralarts.edu')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->call('register')
        ->assertHasNoErrors()
        ->assertSet('submitted', true);

    $campus = User::query()->where('email', 'hello@centralarts.edu')->firstOrFail();

    expect($campus->role)->toBe(Role::Campus)
        ->and($campus->status)->toBe(UserStatus::Pending)
        ->and($campus->campus_name)->toBe('Central Arts Campus')
        ->and($campus->campus_phone)->toBe('+94 11 234 5678')
        ->and($campus->campus_address)->toBe('12 Creative Avenue, Colombo 07')
        ->and($campus->campus_website)->toBe('https://centralarts.edu')
        ->and($campus->profile)->not->toBeNull();

    Livewire::test(Register::class)
        ->assertDontSee('Central Arts Campus');
});

test('an approved campus appears in the campus list and student registration dropdown', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $campus = User::factory()->campus()->pending()->create([
        'name' => 'Nimali Perera',
        'email' => 'hello@centralarts.edu',
        'campus_name' => 'Central Arts Campus',
        'campus_phone' => '+94 11 234 5678',
        'campus_address' => '12 Creative Avenue, Colombo 07',
        'campus_website' => 'https://centralarts.edu',
    ]);

    Livewire::actingAs($superAdmin)
        ->test(AdminDashboard::class)
        ->set('activeTab', 'campuses')
        ->assertSee('Central Arts Campus')
        ->assertSee('12 Creative Avenue, Colombo 07')
        ->call('approveCampus', $campus->id)
        ->assertHasNoErrors();

    expect($campus->fresh()->status)->toBe(UserStatus::Approved);

    Livewire::test(Register::class)
        ->assertSee('Central Arts Campus');
});
