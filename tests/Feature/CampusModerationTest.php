<?php

use App\Enums\ReportStatus;
use App\Livewire\Campus\Dashboard;
use App\Models\PortfolioItem;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('campus sees a detailed moderation workspace scoped to its own content', function () {
    $campus = User::factory()->campus()->create();
    $campus->update(['campus_id' => $campus->id]);
    $student = User::factory()->student()->create(['campus_id' => $campus->id]);
    $reporter = User::factory()->student()->create(['campus_id' => $campus->id]);
    $reportedItem = PortfolioItem::factory()->for($student)->create(['title' => 'Campus Safety Review']);
    PortfolioItem::factory()->for($student)->draft()->create(['title' => 'Private Student Draft']);

    Report::factory()->create([
        'reporter_id' => $reporter->id,
        'reportable_id' => $reportedItem->id,
        'reportable_type' => $reportedItem->getMorphClass(),
        'reason' => 'harassment_or_bullying',
        'details' => 'This caption targets another student.',
    ]);

    $otherCampus = User::factory()->campus()->create();
    $otherCampus->update(['campus_id' => $otherCampus->id]);
    $otherStudent = User::factory()->student()->create(['campus_id' => $otherCampus->id]);
    $otherItem = PortfolioItem::factory()->for($otherStudent)->create(['title' => 'Other Campus Work']);
    Report::factory()->create([
        'reporter_id' => $otherStudent->id,
        'reportable_id' => $otherItem->id,
        'reportable_type' => $otherItem->getMorphClass(),
    ]);

    Livewire::actingAs($campus)
        ->test(Dashboard::class)
        ->set('activeTab', 'moderation')
        ->assertSee('Reports workspace')
        ->assertSee('Campus Safety Review')
        ->assertSee('Harassment Or Bullying')
        ->assertSee('This caption targets another student.')
        ->assertDontSee('Other Campus Work')
        ->set('moderationContentState', 'invalid')
        ->assertDontSee('Private Student Draft')
        ->set('moderationSearch', 'Safety Review')
        ->assertSee('Campus Safety Review')
        ->set('moderationSearch', 'no matching case')
        ->assertSee('No matching reports');
});

test('campus can document a decision and take down all reports for the same work', function () {
    $campus = User::factory()->campus()->create();
    $campus->update(['campus_id' => $campus->id]);
    $student = User::factory()->student()->create(['campus_id' => $campus->id]);
    $item = PortfolioItem::factory()->for($student)->create();
    $reports = Report::factory()->count(2)->create([
        'reportable_id' => $item->id,
        'reportable_type' => $item->getMorphClass(),
    ]);

    Livewire::actingAs($campus)
        ->test(Dashboard::class)
        ->set('activeTab', 'moderation')
        ->set("moderatorNotes.{$reports->first()->id}", 'Confirmed policy violation after reviewing the original file.')
        ->call('moderateReport', $reports->first()->id, ReportStatus::Actioned->value)
        ->assertHasNoErrors();

    expect($item->fresh()->published_at)->toBeNull()
        ->and($reports->first()->fresh()->moderator_notes)->toBe('Confirmed policy violation after reviewing the original file.')
        ->and($reports->fresh()->pluck('status')->unique()->all())->toBe([ReportStatus::Actioned]);
});

test('campus can moderate selected reports in bulk without removing reviewed content', function () {
    $campus = User::factory()->campus()->create();
    $campus->update(['campus_id' => $campus->id]);
    $student = User::factory()->student()->create(['campus_id' => $campus->id]);
    $items = PortfolioItem::factory()->count(2)->for($student)->create();
    $reports = $items->map(fn (PortfolioItem $item) => Report::factory()->create([
        'reportable_id' => $item->id,
        'reportable_type' => $item->getMorphClass(),
    ]));

    Livewire::actingAs($campus)
        ->test(Dashboard::class)
        ->set('selectedReportIds', $reports->pluck('id')->all())
        ->call('bulkModerateReports', ReportStatus::Reviewed->value)
        ->assertHasNoErrors()
        ->assertSet('selectedReportIds', []);

    expect($reports->fresh()->pluck('status')->unique()->all())->toBe([ReportStatus::Reviewed])
        ->and($items->fresh()->every(fn (PortfolioItem $item): bool => $item->isPublished()))->toBeTrue();
});

test('campus can save notes dismiss reports and reopen cases', function () {
    $campus = User::factory()->campus()->create();
    $campus->update(['campus_id' => $campus->id]);
    $student = User::factory()->student()->create(['campus_id' => $campus->id]);
    $item = PortfolioItem::factory()->for($student)->create();
    $report = Report::factory()->create([
        'reporter_id' => $student->id,
        'reportable_id' => $item->id,
        'reportable_type' => $item->getMorphClass(),
    ]);

    $component = Livewire::actingAs($campus)
        ->test(Dashboard::class)
        ->set("moderatorNotes.{$report->id}", 'Reporter contacted; no policy violation found.')
        ->call('saveModeratorNote', $report->id)
        ->assertHasNoErrors()
        ->set("moderatorNotes.{$report->id}", 'Reporter contacted; no policy violation found.')
        ->call('moderateReport', $report->id, ReportStatus::Dismissed->value);

    expect($report->fresh()->status)->toBe(ReportStatus::Dismissed)
        ->and($report->fresh()->moderator_notes)->toBe('Reporter contacted; no policy violation found.')
        ->and($item->fresh()->isPublished())->toBeTrue();

    $component->call('moderateReport', $report->id, ReportStatus::Pending->value);

    expect($report->fresh()->status)->toBe(ReportStatus::Pending);
});

test('proactive content removal requires a reason and removed work can be restored', function () {
    $campus = User::factory()->campus()->create();
    $campus->update(['campus_id' => $campus->id]);
    $student = User::factory()->student()->create(['campus_id' => $campus->id]);
    $item = PortfolioItem::factory()->for($student)->create(['title' => 'Proactive Review Item']);

    $component = Livewire::actingAs($campus)
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
        ->where('reason', 'proactive_campus_review')
        ->firstOrFail();

    expect($item->fresh()->published_at)->toBeNull()
        ->and($moderationRecord->status)->toBe(ReportStatus::Actioned)
        ->and($moderationRecord->details)->toBe('This work exposes private contact details without consent.');

    $component
        ->set('activeTab', 'moderation')
        ->set('moderationContentState', 'removed')
        ->assertSee('Proactive Review Item')
        ->assertSee('Restore')
        ->call('republishItem', $item->id);

    expect($item->fresh()->isPublished())->toBeTrue();
});

test('campus cannot moderate content or reports belonging to another campus', function () {
    $campus = User::factory()->campus()->create();
    $campus->update(['campus_id' => $campus->id]);
    $otherCampus = User::factory()->campus()->create();
    $otherCampus->update(['campus_id' => $otherCampus->id]);
    $otherStudent = User::factory()->student()->create(['campus_id' => $otherCampus->id]);
    $otherItem = PortfolioItem::factory()->for($otherStudent)->create();
    $otherReport = Report::factory()->create([
        'reporter_id' => $otherStudent->id,
        'reportable_id' => $otherItem->id,
        'reportable_type' => $otherItem->getMorphClass(),
    ]);

    expect(fn () => Livewire::actingAs($campus)
        ->test(Dashboard::class)
        ->call('openContentReview', $otherItem->id))
        ->toThrow(ModelNotFoundException::class);

    expect(fn () => Livewire::actingAs($campus)
        ->test(Dashboard::class)
        ->call('moderateReport', $otherReport->id, ReportStatus::Actioned->value))
        ->toThrow(ModelNotFoundException::class);

    expect($otherItem->fresh()->isPublished())->toBeTrue()
        ->and($otherReport->fresh()->status)->toBe(ReportStatus::Pending);
});
