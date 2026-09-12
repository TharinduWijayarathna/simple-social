<?php

use App\Enums\AnnouncementAudience;
use App\Enums\AnnouncementPriority;
use App\Livewire\Announcements\Banner;
use App\Livewire\Announcements\Index;
use App\Models\Announcement;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Livewire;

test('student announcement page only shows active notices for their campus and audience', function () {
    $campus = User::factory()->campus()->create(['campus_name' => 'North Campus']);
    $otherCampus = User::factory()->campus()->create();
    $student = User::factory()->student()->create(['campus_id' => $campus->id]);
    $student->profile()->update(['batch' => '2026', 'program' => 'Design']);

    $visible = Announcement::factory()->recycle($campus)->create(['title' => 'All-student update']);
    Announcement::factory()->recycle($campus)->create([
        'title' => 'Your batch deadline',
        'audience' => AnnouncementAudience::Batch,
        'audience_value' => '2026',
    ]);
    Announcement::factory()->recycle($campus)->create([
        'title' => 'Other batch only',
        'audience' => AnnouncementAudience::Batch,
        'audience_value' => '2025',
    ]);
    Announcement::factory()->recycle($otherCampus)->create(['title' => 'Another campus notice']);
    Announcement::factory()->recycle($campus)->draft()->create(['title' => 'Private draft']);
    Announcement::factory()->recycle($campus)->create(['title' => 'Future notice', 'starts_at' => now()->addDay()]);
    Announcement::factory()->recycle($campus)->create(['title' => 'Expired notice', 'expires_at' => now()->subMinute()]);

    Livewire::actingAs($student)
        ->test(Index::class)
        ->assertSee($visible->title)
        ->assertSee('Your batch deadline')
        ->assertDontSee('Other batch only')
        ->assertDontSee('Another campus notice')
        ->assertDontSee('Private draft')
        ->assertDontSee('Future notice')
        ->assertDontSee('Expired notice');
});

test('students can mark announcements read archive them and restore them', function () {
    $campus = User::factory()->campus()->create();
    $student = User::factory()->student()->create(['campus_id' => $campus->id]);
    $announcement = Announcement::factory()->recycle($campus)->create();

    Livewire::actingAs($student)
        ->test(Index::class)
        ->call('markAsRead', $announcement->id)
        ->assertHasNoErrors();

    $pivot = $announcement->recipients()->whereKey($student)->firstOrFail()->pivot;
    expect($pivot->read_at)->not->toBeNull()
        ->and($pivot->dismissed_at)->toBeNull();

    Livewire::actingAs($student)
        ->test(Index::class)
        ->call('dismiss', $announcement->id)
        ->assertDontSee($announcement->title)
        ->set('filter', 'dismissed')
        ->assertSee($announcement->title)
        ->call('restore', $announcement->id)
        ->assertDontSee($announcement->title);

    expect($announcement->recipients()->whereKey($student)->firstOrFail()->pivot->dismissed_at)->toBeNull();
});

test('the announcement banner prioritizes urgent pinned notices and can be dismissed', function () {
    $campus = User::factory()->campus()->create();
    $student = User::factory()->student()->create(['campus_id' => $campus->id]);
    Announcement::factory()->recycle($campus)->create(['title' => 'Standard notice']);
    $urgent = Announcement::factory()->recycle($campus)->pinned()->create([
        'title' => 'Campus closes early',
        'priority' => AnnouncementPriority::Urgent,
    ]);

    Livewire::actingAs($student)
        ->test(Banner::class)
        ->assertSee('Campus closes early')
        ->assertDontSee('Standard notice')
        ->call('dismiss', $urgent->id)
        ->assertDontSee('Campus closes early')
        ->assertSee('Standard notice');
});

test('students cannot read or dismiss announcements outside their audience', function () {
    $campus = User::factory()->campus()->create();
    $student = User::factory()->student()->create(['campus_id' => $campus->id]);
    $announcement = Announcement::factory()->recycle($campus)->create([
        'audience' => AnnouncementAudience::Batch,
        'audience_value' => 'not-their-batch',
    ]);

    expect(fn () => Livewire::actingAs($student)
        ->test(Index::class)
        ->call('dismiss', $announcement->id))
        ->toThrow(ModelNotFoundException::class);

    expect($announcement->recipients()->whereKey($student)->exists())->toBeFalse();
});

test('student announcement route is protected and available in the website', function () {
    $student = User::factory()->student()->create();

    $this->get(route('announcements.index'))->assertRedirect(route('login'));
    $this->actingAs($student)->get(route('announcements.index'))->assertOk()->assertSee('Campus noticeboard');
});
