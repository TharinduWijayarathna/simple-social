<?php

use App\Enums\UserStatus;
use App\Livewire\Admin\Dashboard;
use App\Mail\SmtpConfigurationTest;
use App\Models\Event;
use App\Models\PortfolioItem;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

test('super admin can inspect hold and reactivate a campus', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $campus = User::factory()->campus()->create([
        'campus_name' => 'Professional Arts Campus',
        'campus_phone' => '+94 11 555 0199',
        'campus_address' => '42 Gallery Road, Colombo',
        'campus_website' => 'https://arts.example.test',
    ]);
    $student = User::factory()->student()->create(['campus_id' => $campus->id]);
    PortfolioItem::factory()->for($student)->create();
    Event::factory()->for($campus, 'organizer')->create();

    $component = Livewire::actingAs($superAdmin)
        ->test(Dashboard::class)
        ->set('activeTab', 'campuses')
        ->assertSee('Professional Arts Campus')
        ->assertSee('+94 11 555 0199')
        ->assertSee('Place on hold')
        ->call('holdCampus', $campus->id)
        ->assertHasNoErrors();

    expect($campus->fresh()->status)->toBe(UserStatus::Banned);

    $component->call('reactivateCampus', $campus->id)->assertHasNoErrors();

    expect($campus->fresh()->status)->toBe(UserStatus::Approved);
});

test('user management groups students by campus and supports campus filtering', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $north = User::factory()->campus()->create(['campus_name' => 'North Campus']);
    $south = User::factory()->campus()->create(['campus_name' => 'South Campus']);
    User::factory()->student()->create(['name' => 'North Student', 'campus_id' => $north->id]);
    User::factory()->student()->create(['name' => 'South Student', 'campus_id' => $south->id]);

    Livewire::actingAs($superAdmin)
        ->test(Dashboard::class)
        ->set('activeTab', 'users')
        ->assertSee('North Campus')
        ->assertSee('South Campus')
        ->set('userCampus', (string) $north->id)
        ->assertSee('North Student')
        ->assertDontSee('South Student');
});

test('analytics presents platform engagement and campus performance', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $campus = User::factory()->campus()->create(['campus_name' => 'Creative Campus']);
    $student = User::factory()->student()->create(['campus_id' => $campus->id]);
    PortfolioItem::factory()->for($student)->create();

    Livewire::actingAs($superAdmin)
        ->test(Dashboard::class)
        ->set('activeTab', 'analytics')
        ->assertSee('Registration growth')
        ->assertSee('Engagement mix')
        ->assertSee('Campus performance')
        ->assertSee('Creative Campus');
});

test('super admin can enable the under construction page while retaining admin access', function () {
    $superAdmin = User::factory()->superAdmin()->create();

    Setting::set('site_under_construction', '1');
    Setting::set('under_construction_title', 'A better VibeCraft is on the way');
    Setting::set('under_construction_message', 'We are completing a planned platform upgrade.');

    $this->get('/login')
        ->assertServiceUnavailable()
        ->assertSee('A better VibeCraft is on the way');

    $this->get('/admin/login')->assertOk();
    $this->actingAs($superAdmin)->get('/admin')->assertOk();

    Setting::set('site_under_construction', '0');
});

test('smtp settings are encrypted and a test message can be sent', function () {
    Mail::fake();
    $superAdmin = User::factory()->superAdmin()->create();

    Livewire::actingAs($superAdmin)
        ->test(Dashboard::class)
        ->set('activeTab', 'settings')
        ->assertDontSee('Site announcement')
        ->set('smtpHost', 'smtp.example.test')
        ->set('smtpPort', 587)
        ->set('smtpUsername', 'mailer@example.test')
        ->set('smtpPassword', 'smtp-secret-password')
        ->set('smtpScheme', 'tls')
        ->set('smtpFromAddress', 'hello@example.test')
        ->set('smtpFromName', 'VibeCraft Mail')
        ->set('smtpTestRecipient', 'owner@example.test')
        ->call('sendSmtpTest')
        ->assertHasNoErrors()
        ->assertSee('Test email sent');

    $storedPassword = Setting::query()->where('key', 'smtp_password')->value('value');

    expect($storedPassword)->not->toBe('smtp-secret-password')
        ->and(Crypt::decryptString($storedPassword))->toBe('smtp-secret-password');

    Mail::assertSent(SmtpConfigurationTest::class, fn (SmtpConfigurationTest $mail): bool => $mail->hasTo('owner@example.test'));
});
