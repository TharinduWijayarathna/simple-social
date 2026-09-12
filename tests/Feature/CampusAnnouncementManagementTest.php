<?php

use App\Enums\AnnouncementAudience;
use App\Enums\AnnouncementPriority;
use App\Livewire\Campus\Dashboard;
use App\Models\Announcement;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Livewire;

test('campus can create targeted announcement drafts and publish them', function () {
    $campus = User::factory()->campus()->create();
    $student = User::factory()->student()->create(['campus_id' => $campus->id]);
    $student->profile()->update(['batch' => '2026']);

    Livewire::actingAs($campus)
        ->test(Dashboard::class)
        ->set('activeTab', 'announcement')
        ->call('openAnnouncementForm')
        ->set('announcementTitle', 'Portfolio review week')
        ->set('announcementBody', 'Book your review slot before Friday at 5 PM.')
        ->set('announcementPriority', AnnouncementPriority::Important->value)
        ->set('announcementAudience', AnnouncementAudience::Batch->value)
        ->set('announcementAudienceValue', '2026')
        ->set('announcementExpiresAt', now()->addWeek()->format('Y-m-d\TH:i'))
        ->set('announcementPinned', true)
        ->call('saveAnnouncement', false)
        ->assertHasNoErrors()
        ->assertSet('showAnnouncementForm', false);

    $announcement = Announcement::query()->sole();

    expect($announcement->campus_id)->toBe($campus->id)
        ->and($announcement->audience)->toBe(AnnouncementAudience::Batch)
        ->and($announcement->audience_value)->toBe('2026')
        ->and($announcement->published_at)->toBeNull()
        ->and($announcement->is_pinned)->toBeTrue();

    Livewire::actingAs($campus)
        ->test(Dashboard::class)
        ->call('publishAnnouncement', $announcement->id)
        ->assertHasNoErrors();

    expect($announcement->fresh()->status())->toBe('active');
});

test('campus can schedule edit unpublish and delete its announcements', function () {
    $campus = User::factory()->campus()->create();
    $announcement = Announcement::factory()->recycle($campus)->draft()->create();
    $startsAt = now()->addDay()->startOfHour();

    Livewire::actingAs($campus)
        ->test(Dashboard::class)
        ->call('openAnnouncementForm', $announcement->id)
        ->set('announcementTitle', 'Updated schedule')
        ->set('announcementStartsAt', $startsAt->format('Y-m-d\TH:i'))
        ->set('announcementExpiresAt', $startsAt->copy()->addDay()->format('Y-m-d\TH:i'))
        ->call('saveAnnouncement', true)
        ->assertHasNoErrors();

    expect($announcement->fresh()->title)->toBe('Updated schedule')
        ->and($announcement->fresh()->status())->toBe('scheduled');

    Livewire::actingAs($campus)
        ->test(Dashboard::class)
        ->call('unpublishAnnouncement', $announcement->id)
        ->assertHasNoErrors();

    expect($announcement->fresh()->published_at)->toBeNull();

    Livewire::actingAs($campus)
        ->test(Dashboard::class)
        ->call('deleteAnnouncement', $announcement->id)
        ->assertHasNoErrors();

    expect(Announcement::query()->whereKey($announcement)->exists())->toBeFalse();
});

test('announcement editor validates audience timing and links', function () {
    $campus = User::factory()->campus()->create();

    Livewire::actingAs($campus)
        ->test(Dashboard::class)
        ->set('announcementTitle', '')
        ->set('announcementBody', '')
        ->set('announcementAudience', AnnouncementAudience::Batch->value)
        ->set('announcementExpiresAt', now()->subDay()->format('Y-m-d\TH:i'))
        ->set('announcementLinkUrl', 'javascript:alert(1)')
        ->call('saveAnnouncement', true)
        ->assertHasErrors([
            'announcementTitle' => 'required',
            'announcementBody' => 'required',
            'announcementAudienceValue' => 'required',
            'announcementExpiresAt' => 'after',
            'announcementLinkUrl' => 'url',
        ]);
});

test('campuses cannot manage another campus announcement', function () {
    $owner = User::factory()->campus()->create();
    $otherCampus = User::factory()->campus()->create();
    $announcement = Announcement::factory()->recycle($owner)->create();

    expect(fn () => Livewire::actingAs($otherCampus)
        ->test(Dashboard::class)
        ->call('deleteAnnouncement', $announcement->id))
        ->toThrow(ModelNotFoundException::class);

    expect($announcement->fresh())->not->toBeNull();
});
