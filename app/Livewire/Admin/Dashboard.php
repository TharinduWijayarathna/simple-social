<?php

namespace App\Livewire\Admin;

use App\Enums\PortfolioMediaType;
use App\Enums\ReportStatus;
use App\Enums\Role;
use App\Enums\UserStatus;
use App\Models\Event;
use App\Models\PortfolioItem;
use App\Models\Report;
use App\Models\Setting;
use App\Models\Talent;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts::admin-panel')]
#[Title('Admin Dashboard')]
class Dashboard extends Component
{
    use WithPagination;

    /** @var list<string> */
    private const array AVAILABLE_TABS = [
        'overview',
        'campuses',
        'users',
        'moderation',
        'analytics',
        'settings',
    ];

    #[Url(as: 'tab')]
    public string $activeTab = 'overview';

    #[Url(as: 'q')]
    public string $userSearch = '';

    #[Url(as: 'report_status')]
    public string $moderationStatus = 'pending';

    #[Url(as: 'report_q')]
    public string $moderationSearch = '';

    #[Url(as: 'content_state')]
    public string $moderationContentState = 'published';

    #[Url(as: 'content_type')]
    public string $moderationContentType = 'all';

    #[Url(as: 'content_q')]
    public string $moderationContentSearch = '';

    /** @var array<int|string, string> */
    public array $moderatorNotes = [];

    /** @var list<int|string> */
    public array $selectedReportIds = [];

    public ?int $contentUnderReviewId = null;

    public string $contentModerationReason = '';

    public string $announcementMessage = '';

    public bool $announcementEnabled = false;

    public function mount(): void
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        $this->normalizeActiveTab();

        $this->announcementMessage = Setting::get('announcement_message', '') ?? '';
        $this->announcementEnabled = Setting::get('announcement_enabled') === '1';
    }

    public function updatedActiveTab(): void
    {
        $this->normalizeActiveTab();
    }

    public function updatedModerationSearch(): void
    {
        $this->resetPage(pageName: 'reportsPage');
    }

    public function updatedModerationStatus(): void
    {
        $this->selectedReportIds = [];
        $this->resetPage(pageName: 'reportsPage');
    }

    public function updatedModerationContentSearch(): void
    {
        $this->resetPage(pageName: 'contentPage');
    }

    public function updatedModerationContentState(): void
    {
        $this->resetPage(pageName: 'contentPage');
    }

    public function updatedModerationContentType(): void
    {
        $this->resetPage(pageName: 'contentPage');
    }

    public function assignRole(int $userId, string $role): void
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        $user = User::query()->findOrFail($userId);

        abort_if($user->is(auth()->user()), 403);

        $newRole = Role::from($role);

        abort_if($newRole === Role::Campus, 403);

        $user->update(['role' => $newRole]);
    }

    public function approveCampus(int $userId): void
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        $user = User::query()->where('role', Role::Campus)->findOrFail($userId);
        $user->update(['status' => UserStatus::Approved]);
    }

    public function rejectCampus(int $userId): void
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        $user = User::query()->where('role', Role::Campus)->findOrFail($userId);
        $user->update(['status' => UserStatus::Rejected]);
    }

    public function banUser(int $userId): void
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        $user = User::query()->findOrFail($userId);

        abort_if($user->is(auth()->user()), 403);

        $user->update(['status' => UserStatus::Banned]);
    }

    public function unbanUser(int $userId): void
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        $user = User::query()->findOrFail($userId);
        $user->update(['status' => UserStatus::Approved]);
    }

    public function deleteUser(int $userId): void
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        $user = User::query()->findOrFail($userId);

        abort_if($user->is(auth()->user()), 403);

        $user->delete();
    }

    public function openContentReview(int $itemId): void
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        $item = PortfolioItem::query()
            ->published()
            ->with('user:id,campus_id')
            ->findOrFail($itemId);

        $this->authorize('moderate', $item);
        $this->resetErrorBag();
        $this->contentUnderReviewId = $item->id;
        $this->contentModerationReason = '';
    }

    public function closeContentReview(): void
    {
        $this->contentUnderReviewId = null;
        $this->contentModerationReason = '';
        $this->resetErrorBag();
    }

    public function unpublishItem(): void
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);
        abort_unless($this->contentUnderReviewId !== null, 422);

        $validated = $this->validate([
            'contentModerationReason' => ['required', 'string', 'min:10', 'max:1000'],
        ]);

        $item = PortfolioItem::query()
            ->published()
            ->with('user:id,campus_id')
            ->findOrFail($this->contentUnderReviewId);

        $this->authorize('moderate', $item);

        DB::transaction(function () use ($item, $validated): void {
            $item->update(['published_at' => null]);

            $item->reports()->create([
                'reporter_id' => auth()->id(),
                'reason' => 'proactive_super_admin_review',
                'details' => $validated['contentModerationReason'],
                'status' => ReportStatus::Actioned,
                'moderator_notes' => $validated['contentModerationReason'],
            ]);
        });

        $this->closeContentReview();
        session()->flash('moderation-status', 'The work was unpublished and the reason was added to platform moderation history.');
    }

    public function republishItem(int $itemId): void
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        $item = PortfolioItem::query()
            ->whereNull('published_at')
            ->whereHas('reports', fn (Builder $query) => $query->where('status', ReportStatus::Actioned))
            ->with('user:id,campus_id')
            ->findOrFail($itemId);

        $this->authorize('moderate', $item);
        $item->update(['published_at' => now()]);

        session()->flash('moderation-status', 'The work is published again. Its moderation history has been preserved.');
    }

    public function moderateReport(int $reportId, string $status): void
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        $reportStatus = ReportStatus::tryFrom($status);
        abort_unless($reportStatus !== null, 422);

        $report = $this->platformReportsQuery()
            ->with('reportable')
            ->findOrFail($reportId);

        $this->authorize('moderate', $report);

        $this->validate([
            "moderatorNotes.{$reportId}" => ['nullable', 'string', 'max:2000'],
        ]);

        $moderatorNote = filled($this->moderatorNotes[$reportId] ?? null)
            ? Str::squish($this->moderatorNotes[$reportId])
            : $report->moderator_notes;

        DB::transaction(function () use ($report, $reportStatus, $moderatorNote): void {
            $report->update([
                'status' => $reportStatus,
                'moderator_notes' => $moderatorNote,
            ]);

            if ($reportStatus === ReportStatus::Actioned && $report->reportable instanceof PortfolioItem) {
                $report->reportable->update(['published_at' => null]);

                Report::query()
                    ->pending()
                    ->where('reportable_type', $report->reportable_type)
                    ->where('reportable_id', $report->reportable_id)
                    ->update([
                        'status' => ReportStatus::Actioned,
                        'moderator_notes' => $moderatorNote,
                    ]);
            }
        });

        unset($this->moderatorNotes[$reportId]);
        $this->selectedReportIds = array_values(array_diff($this->selectedReportIds, [$reportId, (string) $reportId]));
        session()->flash('moderation-status', match ($reportStatus) {
            ReportStatus::Pending => 'The report was reopened and returned to the queue.',
            ReportStatus::Reviewed => 'The report was marked as reviewed.',
            ReportStatus::Dismissed => 'The report was dismissed.',
            ReportStatus::Actioned => 'The reported work was taken down and related reports were resolved.',
        });
    }

    public function saveModeratorNote(int $reportId): void
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        $report = $this->platformReportsQuery()->findOrFail($reportId);
        $this->authorize('moderate', $report);

        $validated = $this->validate([
            "moderatorNotes.{$reportId}" => ['nullable', 'string', 'max:2000'],
        ]);

        $report->update([
            'moderator_notes' => filled($validated['moderatorNotes'][$reportId] ?? null)
                ? Str::squish($validated['moderatorNotes'][$reportId])
                : null,
        ]);

        unset($this->moderatorNotes[$reportId]);
        session()->flash('moderation-status', 'Moderator note saved.');
    }

    public function bulkModerateReports(string $status): void
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        $reportStatus = ReportStatus::tryFrom($status);
        abort_unless(in_array($reportStatus, [ReportStatus::Reviewed, ReportStatus::Dismissed, ReportStatus::Actioned], true), 422);

        $reportIds = collect($this->selectedReportIds)
            ->filter(fn (mixed $reportId): bool => filter_var($reportId, FILTER_VALIDATE_INT) !== false)
            ->map(fn (mixed $reportId): int => (int) $reportId)
            ->unique()
            ->values();

        if ($reportIds->isEmpty()) {
            $this->addError('selectedReportIds', 'Select at least one report.');

            return;
        }

        $reports = $this->platformReportsQuery()
            ->whereKey($reportIds)
            ->with('reportable')
            ->get();

        abort_unless($reports->count() === $reportIds->count(), 404);

        DB::transaction(function () use ($reports, $reportStatus): void {
            foreach ($reports as $report) {
                $this->authorize('moderate', $report);
                $report->update(['status' => $reportStatus]);

                if ($reportStatus === ReportStatus::Actioned && $report->reportable instanceof PortfolioItem) {
                    $report->reportable->update(['published_at' => null]);

                    Report::query()
                        ->pending()
                        ->where('reportable_type', $report->reportable_type)
                        ->where('reportable_id', $report->reportable_id)
                        ->update(['status' => ReportStatus::Actioned]);
                }
            }
        });

        $processedCount = $reports->count();
        $this->selectedReportIds = [];
        $this->resetErrorBag('selectedReportIds');
        session()->flash('moderation-status', "{$processedCount} reports were updated.");
    }

    public function saveSettings(): void
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        $this->validate([
            'announcementMessage' => ['nullable', 'string', 'max:280'],
        ]);

        Setting::set('announcement_message', $this->announcementMessage);
        Setting::set('announcement_enabled', $this->announcementEnabled ? '1' : '0');
    }

    public function render(): View
    {
        /** @var Collection<int, User> $users */
        $users = User::query()
            ->when($this->userSearch !== '', fn ($query) => $query->where(fn ($query) => $query
                ->where('name', 'like', "%{$this->userSearch}%")
                ->orWhere('email', 'like', "%{$this->userSearch}%")))
            ->latest()
            ->paginate(15, pageName: 'usersPage');

        $reportStatus = ReportStatus::tryFrom($this->moderationStatus);
        $moderationSearch = Str::squish($this->moderationSearch);

        $reports = $this->platformReportsQuery()
            ->when($this->moderationStatus !== 'all', fn (Builder $query) => $query->where(
                'status',
                $reportStatus ?? ReportStatus::Pending,
            ))
            ->when($moderationSearch !== '', function (Builder $query) use ($moderationSearch): void {
                $query->where(function (Builder $query) use ($moderationSearch): void {
                    $query->where('reason', 'like', "%{$moderationSearch}%")
                        ->orWhere('details', 'like', "%{$moderationSearch}%")
                        ->orWhere('moderator_notes', 'like', "%{$moderationSearch}%")
                        ->orWhereHas('reporter', fn (Builder $query) => $query->where('name', 'like', "%{$moderationSearch}%"))
                        ->orWhereHasMorph('reportable', [PortfolioItem::class], function (Builder $query) use ($moderationSearch): void {
                            $query->where(function (Builder $query) use ($moderationSearch): void {
                                $query->where('title', 'like', "%{$moderationSearch}%")
                                    ->orWhere('description', 'like', "%{$moderationSearch}%")
                                    ->orWhereHas('user', fn (Builder $query) => $query->where('name', 'like', "%{$moderationSearch}%"));
                            });
                        });
                });
            })
            ->with(['reporter.profile', 'reportable', 'reportable.talent', 'reportable.user.profile', 'reportable.user.campus'])
            ->latest()
            ->paginate(8, pageName: 'reportsPage');

        $contentState = in_array($this->moderationContentState, ['published', 'removed', 'all'], true)
            ? $this->moderationContentState
            : 'published';
        $contentMediaType = PortfolioMediaType::tryFrom($this->moderationContentType);
        $contentSearch = Str::squish($this->moderationContentSearch);

        $moderationItems = PortfolioItem::query()
            ->when($contentState === 'published', fn (Builder $query) => $query->published())
            ->when($contentState === 'removed', fn (Builder $query) => $query
                ->whereNull('published_at')
                ->whereHas('reports', fn (Builder $query) => $query->where('status', ReportStatus::Actioned)))
            ->when($contentState === 'all', function (Builder $query): void {
                $query->where(function (Builder $query): void {
                    $query->published()
                        ->orWhere(function (Builder $query): void {
                            $query->whereNull('published_at')
                                ->whereHas('reports', fn (Builder $query) => $query->where('status', ReportStatus::Actioned));
                        });
                });
            })
            ->when($contentMediaType !== null, fn (Builder $query) => $query->where('media_type', $contentMediaType))
            ->when($contentSearch !== '', function (Builder $query) use ($contentSearch): void {
                $query->where(function (Builder $query) use ($contentSearch): void {
                    $query->where('title', 'like', "%{$contentSearch}%")
                        ->orWhere('description', 'like', "%{$contentSearch}%")
                        ->orWhereHas('user', fn (Builder $query) => $query->where('name', 'like', "%{$contentSearch}%"))
                        ->orWhereHas('talent', fn (Builder $query) => $query->where('name', 'like', "%{$contentSearch}%"));
                });
            })
            ->with(['talent:id,name', 'user.profile', 'user.campus'])
            ->withCount([
                'reports',
                'reports as pending_reports_count' => fn (Builder $query) => $query->where('status', ReportStatus::Pending),
            ])
            ->latest('updated_at')
            ->paginate(9, pageName: 'contentPage');

        $pendingReportsCount = $this->platformReportsQuery()->pending()->count();
        $resolvedReportsLast30Days = $this->platformReportsQuery()
            ->whereIn('status', [ReportStatus::Reviewed, ReportStatus::Dismissed, ReportStatus::Actioned])
            ->where('updated_at', '>=', now()->subDays(30))
            ->count();
        $publishedItemsCount = PortfolioItem::query()->published()->count();
        $removedItemsCount = PortfolioItem::query()
            ->whereNull('published_at')
            ->whereHas('reports', fn (Builder $query) => $query->where('status', ReportStatus::Actioned))
            ->count();

        return view('livewire.admin.dashboard', [
            'totalUsers' => User::query()->count(),
            'totalStudents' => User::query()->students()->count(),
            'totalCampuses' => User::query()->where('role', Role::Campus)->where('status', UserStatus::Approved)->count(),
            'totalItems' => PortfolioItem::query()->published()->count(),
            'totalEvents' => Event::query()->published()->count(),
            'totalBanned' => User::query()->where('status', UserStatus::Banned)->count(),
            'pendingCampuses' => User::query()->pendingCampuses()->with('profile')->latest()->get(),
            'approvedCampuses' => User::query()
                ->where('role', Role::Campus)
                ->where('status', UserStatus::Approved)
                ->with('profile')
                ->latest()
                ->get(),
            'categories' => Talent::query()
                ->withCount(['portfolioItems as published_items_count' => fn ($query) => $query->published()])
                ->orderByDesc('published_items_count')
                ->get(),
            'overviewReports' => $this->platformReportsQuery()
                ->pending()
                ->with('reporter:id,name')
                ->latest()
                ->limit(10)
                ->get(),
            'reports' => $reports,
            'moderationItems' => $moderationItems,
            'pendingReportsCount' => $pendingReportsCount,
            'resolvedReportsLast30Days' => $resolvedReportsLast30Days,
            'publishedItemsCount' => $publishedItemsCount,
            'removedItemsCount' => $removedItemsCount,
            'users' => $users,
            'newUsersLast7Days' => User::query()->where('created_at', '>=', now()->subDays(7))->count(),
            'newUsersLast30Days' => User::query()->where('created_at', '>=', now()->subDays(30))->count(),
        ]);
    }

    private function normalizeActiveTab(): void
    {
        if (! in_array($this->activeTab, self::AVAILABLE_TABS, true)) {
            $this->activeTab = 'overview';
        }
    }

    /** @return Builder<Report> */
    private function platformReportsQuery(): Builder
    {
        return Report::query()->where('reportable_type', (new PortfolioItem)->getMorphClass());
    }
}
