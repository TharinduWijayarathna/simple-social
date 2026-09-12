<?php

use App\Enums\ReportStatus;
use App\Livewire\Admin\Dashboard;
use App\Models\PortfolioItem;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('super admin sees the advanced moderation workspace across every campus', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $firstCampus = User::factory()->campus()->create(['campus_name' => 'North Campus']);
    $secondCampus = User::factory()->campus()->create(['campus_name' => 'South Campus']);
    $firstStudent = User::factory()->student()->create(['campus_id' => $firstCampus->id]);
    $secondStudent = User::factory()->student()->create(['campus_id' => $secondCampus->id]);
    $firstItem = PortfolioItem::factory()->for($firstStudent)->create(['title' => 'North Campus Safety Case']);
    $secondItem = PortfolioItem::factory()->for($secondStudent)->create(['title' => 'South Campus Safety Case']);

    foreach ([$firstItem, $secondItem] as $item) {
        Report::factory()->create([
            'reporter_id' => $item->user_id,
            'reportable_id' => $item->id,
            'reportable_type' => $item->getMorphClass(),
            'reason' => 'harassment_or_bullying',
        ]);
    }

    Livewire::actingAs($superAdmin)
        ->test(Dashboard::class)
        ->set('activeTab', 'moderation')
        ->assertSee('Reports workspace')
        ->assertSee('Content review library')
        ->assertSee('North Campus Safety Case')
        ->assertSee('South Campus Safety Case')
        ->assertSee('North Campus')
        ->assertSee('South Campus')
        ->set('moderationSearch', 'North Campus Safety')
        ->assertViewHas('reports', fn ($reports): bool => $reports->total() === 1
            && $reports->first()->reportable->is($firstItem))
        ->set('moderationSearch', 'no matching report')
        ->assertSee('No matching reports');
});

test('super admin can document decisions and resolve related reports', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $student = User::factory()->student()->create();
    $item = PortfolioItem::factory()->for($student)->create();
    $reports = Report::factory()->count(2)->create([
        'reportable_id' => $item->id,
        'reportable_type' => $item->getMorphClass(),
    ]);

    Livewire::actingAs($superAdmin)
        ->test(Dashboard::class)
        ->set("moderatorNotes.{$reports->first()->id}", 'Confirmed platform policy violation after reviewing the evidence.')
        ->call('moderateReport', $reports->first()->id, ReportStatus::Actioned->value)
        ->assertHasNoErrors();

    expect($item->fresh()->published_at)->toBeNull()
        ->and($reports->first()->fresh()->moderator_notes)->toBe('Confirmed platform policy violation after reviewing the evidence.')
        ->and($reports->fresh()->pluck('status')->unique()->all())->toBe([ReportStatus::Actioned]);
});

test('super admin can bulk review reports without removing their content', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $items = PortfolioItem::factory()->count(2)->create();
    $reports = $items->map(fn (PortfolioItem $item) => Report::factory()->create([
        'reportable_id' => $item->id,
        'reportable_type' => $item->getMorphClass(),
    ]));

    Livewire::actingAs($superAdmin)
        ->test(Dashboard::class)
        ->set('selectedReportIds', $reports->pluck('id')->all())
        ->call('bulkModerateReports', ReportStatus::Reviewed->value)
        ->assertHasNoErrors()
        ->assertSet('selectedReportIds', []);

    expect($reports->fresh()->pluck('status')->unique()->all())->toBe([ReportStatus::Reviewed])
        ->and($items->fresh()->every(fn (PortfolioItem $item): bool => $item->isPublished()))->toBeTrue();
});

test('super admin proactive removal requires a reason and supports restoration', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $item = PortfolioItem::factory()->create(['title' => 'Platform Review Item']);

    $component = Livewire::actingAs($superAdmin)
        ->test(Dashboard::class)
        ->call('openContentReview', $item->id)
        ->set('contentModerationReason', 'Too short')
        ->call('unpublishItem')
        ->assertHasErrors(['contentModerationReason' => 'min'])
        ->set('contentModerationReason', 'This work exposes private contact details without consent.')
        ->call('unpublishItem')
        ->assertHasNoErrors()
        ->assertSet('contentUnderReviewId', null);

    $moderationRecord = Report::query()
        ->where('reportable_id', $item->id)
        ->where('reason', 'proactive_super_admin_review')
        ->firstOrFail();

    expect($item->fresh()->published_at)->toBeNull()
        ->and($moderationRecord->status)->toBe(ReportStatus::Actioned)
        ->and($moderationRecord->details)->toBe('This work exposes private contact details without consent.');

    $component
        ->set('activeTab', 'moderation')
        ->set('moderationContentState', 'removed')
        ->assertSee('Platform Review Item')
        ->assertSee('Restore')
        ->call('republishItem', $item->id)
        ->assertHasNoErrors();

    expect($item->fresh()->isPublished())->toBeTrue();
});
