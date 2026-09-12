<?php

namespace App\Livewire\Campus;

use App\Actions\AwardXp;
use App\Enums\AnnouncementAudience;
use App\Enums\AnnouncementPriority;
use App\Enums\EventApplicationStatus;
use App\Enums\PortfolioMediaType;
use App\Enums\ReportStatus;
use App\Enums\Role;
use App\Enums\TalentTheme;
use App\Enums\UserStatus;
use App\Enums\XpEventType;
use App\Models\Announcement;
use App\Models\Event;
use App\Models\EventApplication;
use App\Models\PortfolioItem;
use App\Models\Report;
use App\Models\Talent;
use App\Models\TalentCategory;
use App\Models\User;
use App\Models\XpEvent;
use App\Notifications\EventApplicationSelectedNotification;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts::campus-panel')]
#[Title('Campus Dashboard')]
class Dashboard extends Component
{
    use WithPagination;

    #[Url(as: 'tab')]
    public string $activeTab = 'overview';

    #[Url(as: 'q')]
    public string $studentSearch = '';

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

    public ?int $selectedEventId = null;

    public string $talentSubTab = 'talents';

    // Category form state
    public string $categoryName = '';

    public ?int $editingCategoryId = null;

    public bool $showCategoryForm = false;

    // Custom Talent form state
    public string $talentName = '';

    public string $talentCategory = '';

    public string $talentDescription = '';

    public string $talentTheme = 'gallery';

    public ?int $editingTalentId = null;

    public bool $showTalentForm = false;

    #[Url(as: 'announcement_status')]
    public string $announcementStatus = 'all';

    #[Url(as: 'announcement_q')]
    public string $announcementSearch = '';

    public string $announcementTitle = '';

    public string $announcementBody = '';

    public string $announcementPriority = 'standard';

    public string $announcementAudience = 'everyone';

    public string $announcementAudienceValue = '';

    public string $announcementStartsAt = '';

    public string $announcementExpiresAt = '';

    public string $announcementLinkUrl = '';

    public string $announcementLinkLabel = '';

    public bool $announcementPinned = false;

    public ?int $editingAnnouncementId = null;

    public bool $showAnnouncementForm = false;

    public function mount(): void
    {
        abort_unless(auth()->user()->canOrganizeEvents(), 403);

    }

    public function selectEvent(int $eventId): void
    {
        abort_unless(auth()->user()->canOrganizeEvents(), 403);

        $this->authorize('update', Event::query()->findOrFail($eventId));
        $this->selectedEventId = $eventId;
    }

    public function approveStudent(int $userId): void
    {
        $campus = auth()->user();

        abort_unless($campus instanceof User && $campus->isCampus(), 403);

        $user = User::query()
            ->pendingStudentsForCampus($campus->id)
            ->findOrFail($userId);

        $user->update(['status' => UserStatus::Approved]);
    }

    public function rejectStudent(int $userId): void
    {
        $campus = auth()->user();

        abort_unless($campus instanceof User && $campus->isCampus(), 403);

        $user = User::query()
            ->pendingStudentsForCampus($campus->id)
            ->findOrFail($userId);

        $user->update(['status' => UserStatus::Rejected]);
    }

    public function suspendStudent(int $userId): void
    {
        abort_unless(auth()->user()->canOrganizeEvents(), 403);

        $user = User::query()
            ->approvedStudentsForCampus(auth()->id())
            ->findOrFail($userId);

        $user->update(['status' => UserStatus::Banned]);
    }

    public function unsuspendStudent(int $userId): void
    {
        abort_unless(auth()->user()->canOrganizeEvents(), 403);

        $user = User::query()
            ->where('role', Role::Student)
            ->where('campus_id', auth()->id())
            ->where('status', UserStatus::Banned)
            ->findOrFail($userId);

        $user->update(['status' => UserStatus::Approved]);
    }

    public function removeStudent(int $userId): void
    {
        abort_unless(auth()->user()->canOrganizeEvents(), 403);

        $user = User::query()
            ->where('role', Role::Student)
            ->where('campus_id', auth()->id())
            ->findOrFail($userId);

        $user->delete();
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

    public function openContentReview(int $itemId): void
    {
        $item = $this->campusPortfolioItemsQuery()
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
        abort_unless($this->contentUnderReviewId !== null, 422);

        $validated = $this->validate([
            'contentModerationReason' => ['required', 'string', 'min:10', 'max:1000'],
        ]);

        $item = $this->campusPortfolioItemsQuery()
            ->published()
            ->with('user:id,campus_id')
            ->findOrFail($this->contentUnderReviewId);

        $this->authorize('moderate', $item);

        DB::transaction(function () use ($item, $validated): void {
            $item->update(['published_at' => null]);

            $item->reports()->create([
                'reporter_id' => auth()->id(),
                'reason' => 'proactive_campus_review',
                'details' => $validated['contentModerationReason'],
                'status' => ReportStatus::Actioned,
                'moderator_notes' => $validated['contentModerationReason'],
            ]);
        });

        $this->closeContentReview();
        session()->flash('moderation-status', 'The work was unpublished and the reason was added to moderation history.');
    }

    public function republishItem(int $itemId): void
    {
        $item = $this->campusPortfolioItemsQuery()
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
        $reportStatus = ReportStatus::tryFrom($status);
        abort_unless($reportStatus !== null, 422);

        $report = $this->campusReportsQuery()
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
        $report = $this->campusReportsQuery()->findOrFail($reportId);
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

        $reports = $this->campusReportsQuery()
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

    public function updatedAnnouncementSearch(): void
    {
        $this->resetPage(pageName: 'announcementsPage');
    }

    public function updatedAnnouncementStatus(): void
    {
        $this->resetPage(pageName: 'announcementsPage');
    }

    public function updatedAnnouncementAudience(): void
    {
        $this->announcementAudienceValue = '';
    }

    public function openAnnouncementForm(?int $announcementId = null): void
    {
        $this->resetErrorBag();
        $this->resetAnnouncementForm();

        if ($announcementId !== null) {
            $announcement = $this->campusAnnouncementsQuery()->findOrFail($announcementId);
            $this->authorize('update', $announcement);
            $this->editingAnnouncementId = $announcement->id;
            $this->announcementTitle = $announcement->title;
            $this->announcementBody = $announcement->body;
            $this->announcementPriority = $announcement->priority->value;
            $this->announcementAudience = $announcement->audience->value;
            $this->announcementAudienceValue = $announcement->audience_value ?? '';
            $this->announcementStartsAt = $announcement->starts_at?->format('Y-m-d\TH:i') ?? '';
            $this->announcementExpiresAt = $announcement->expires_at?->format('Y-m-d\TH:i') ?? '';
            $this->announcementLinkUrl = $announcement->link_url ?? '';
            $this->announcementLinkLabel = $announcement->link_label ?? '';
            $this->announcementPinned = $announcement->is_pinned;
        }

        $this->showAnnouncementForm = true;
    }

    public function closeAnnouncementForm(): void
    {
        $this->showAnnouncementForm = false;
        $this->resetAnnouncementForm();
        $this->resetErrorBag();
    }

    public function saveAnnouncement(bool $publish = false): void
    {
        $this->authorize('create', Announcement::class);

        $audience = AnnouncementAudience::tryFrom($this->announcementAudience);
        $audienceValues = $audience === null || $audience === AnnouncementAudience::Everyone
            ? []
            : $this->announcementAudienceValues($audience);

        $validated = $this->validate([
            'announcementTitle' => ['required', 'string', 'max:120'],
            'announcementBody' => ['required', 'string', 'max:5000'],
            'announcementPriority' => ['required', Rule::enum(AnnouncementPriority::class)],
            'announcementAudience' => ['required', Rule::enum(AnnouncementAudience::class)],
            'announcementAudienceValue' => [
                Rule::requiredIf($audience !== null && $audience !== AnnouncementAudience::Everyone),
                'nullable',
                'string',
                'max:255',
                Rule::in($audienceValues),
            ],
            'announcementStartsAt' => ['nullable', 'date'],
            'announcementExpiresAt' => [
                'nullable',
                Rule::date()->after($this->announcementStartsAt !== '' ? $this->announcementStartsAt : now()),
            ],
            'announcementLinkUrl' => ['nullable', 'url:http,https', 'max:2048'],
            'announcementLinkLabel' => ['nullable', 'string', 'max:60', 'required_with:announcementLinkUrl'],
            'announcementPinned' => ['boolean'],
        ]);

        $announcement = $this->editingAnnouncementId === null
            ? new Announcement(['campus_id' => auth()->id()])
            : $this->campusAnnouncementsQuery()->findOrFail($this->editingAnnouncementId);

        $this->authorize($announcement->exists ? 'update' : 'create', $announcement);

        $announcement->fill([
            'title' => Str::squish($validated['announcementTitle']),
            'body' => trim($validated['announcementBody']),
            'priority' => AnnouncementPriority::from($validated['announcementPriority']),
            'audience' => AnnouncementAudience::from($validated['announcementAudience']),
            'audience_value' => filled($validated['announcementAudienceValue'] ?? null) ? $validated['announcementAudienceValue'] : null,
            'starts_at' => filled($validated['announcementStartsAt'] ?? null) ? $validated['announcementStartsAt'] : null,
            'expires_at' => filled($validated['announcementExpiresAt'] ?? null) ? $validated['announcementExpiresAt'] : null,
            'link_url' => filled($validated['announcementLinkUrl'] ?? null) ? $validated['announcementLinkUrl'] : null,
            'link_label' => filled($validated['announcementLinkLabel'] ?? null) ? Str::squish($validated['announcementLinkLabel']) : null,
            'is_pinned' => $validated['announcementPinned'],
            'published_at' => $publish ? ($announcement->published_at ?? now()) : null,
        ])->save();

        $message = $publish
            ? (($announcement->starts_at?->isFuture() ?? false) ? 'Announcement scheduled successfully.' : 'Announcement published successfully.')
            : 'Announcement saved as a draft.';

        $this->closeAnnouncementForm();
        session()->flash('announcement-status', $message);
    }

    public function publishAnnouncement(int $announcementId): void
    {
        $announcement = $this->campusAnnouncementsQuery()->findOrFail($announcementId);
        $this->authorize('update', $announcement);
        $announcement->update(['published_at' => now()]);
        session()->flash('announcement-status', $announcement->starts_at?->isFuture() ? 'Announcement scheduled.' : 'Announcement published.');
    }

    public function unpublishAnnouncement(int $announcementId): void
    {
        $announcement = $this->campusAnnouncementsQuery()->findOrFail($announcementId);
        $this->authorize('update', $announcement);
        $announcement->update(['published_at' => null]);
        session()->flash('announcement-status', 'Announcement moved back to drafts. Student read history was preserved.');
    }

    public function deleteAnnouncement(int $announcementId): void
    {
        $announcement = $this->campusAnnouncementsQuery()->findOrFail($announcementId);
        $this->authorize('delete', $announcement);
        $announcement->delete();
        session()->flash('announcement-status', 'Announcement deleted.');
    }

    public function openCategoryForm(?int $categoryId = null): void
    {
        $this->resetErrorBag();
        if ($categoryId) {
            $campusId = auth()->user()->campus_id ?? auth()->id();
            $category = TalentCategory::query()
                ->forCampus($campusId)
                ->findOrFail($categoryId);

            $this->editingCategoryId = $category->id;
            $this->categoryName = $category->name;
        } else {
            $this->editingCategoryId = null;
            $this->categoryName = '';
        }
        $this->showCategoryForm = true;
    }

    public function closeCategoryForm(): void
    {
        $this->showCategoryForm = false;
        $this->editingCategoryId = null;
        $this->categoryName = '';
    }

    public function saveCategory(): void
    {
        abort_unless(auth()->user()->canOrganizeEvents(), 403);

        $campusId = auth()->user()->campus_id ?? auth()->id();

        $rules = [
            'categoryName' => [
                'required',
                'string',
                'max:255',
                Rule::unique('talent_categories', 'name')
                    ->where(function ($query) use ($campusId) {
                        return $query->where(function ($q) use ($campusId) {
                            $q->whereNull('campus_id')->orWhere('campus_id', $campusId);
                        });
                    })
                    ->ignore($this->editingCategoryId),
            ],
        ];

        $this->validate($rules);

        if ($this->editingCategoryId) {
            $category = TalentCategory::query()
                ->forCampus($campusId)
                ->findOrFail($this->editingCategoryId);

            $oldName = $category->name;
            $category->update([
                'name' => $this->categoryName,
            ]);

            Talent::query()
                ->forCampus($campusId)
                ->where('category', $oldName)
                ->update(['category' => $this->categoryName]);
        } else {
            TalentCategory::create([
                'name' => $this->categoryName,
                'campus_id' => $campusId,
            ]);
        }

        $this->closeCategoryForm();
        session()->flash('talent-status', 'Category saved successfully.');
    }

    public function deleteCategory(int $categoryId): void
    {
        abort_unless(auth()->user()->canOrganizeEvents(), 403);

        $campusId = auth()->user()->campus_id ?? auth()->id();

        $category = TalentCategory::query()
            ->forCampus($campusId)
            ->findOrFail($categoryId);

        $categoryName = $category->name;
        $category->delete();

        Talent::query()
            ->forCampus($campusId)
            ->where('category', $categoryName)
            ->update(['category' => 'General User']);

        session()->flash('talent-status', 'Category deleted successfully.');
    }

    public function openTalentForm(?int $talentId = null): void
    {
        $this->resetErrorBag();
        if ($talentId) {
            $campusId = auth()->user()->campus_id ?? auth()->id();
            $talent = Talent::query()
                ->forCampus($campusId)
                ->findOrFail($talentId);

            $this->editingTalentId = $talent->id;
            $this->talentName = $talent->name;
            $this->talentCategory = $talent->category;
            $this->talentDescription = $talent->description ?? '';
            $this->talentTheme = $talent->theme->value;
        } else {
            $this->editingTalentId = null;
            $this->talentName = '';
            $this->talentCategory = '';
            $this->talentDescription = '';
            $this->talentTheme = 'gallery';
        }
        $this->showTalentForm = true;
    }

    public function closeTalentForm(): void
    {
        $this->showTalentForm = false;
        $this->editingTalentId = null;
        $this->talentName = '';
        $this->talentCategory = '';
        $this->talentDescription = '';
        $this->talentTheme = 'gallery';
    }

    public function saveTalent(): void
    {
        abort_unless(auth()->user()->canOrganizeEvents(), 403);

        $campusId = auth()->user()->campus_id ?? auth()->id();

        $rules = [
            'talentName' => [
                'required',
                'string',
                'max:255',
                Rule::unique('talents', 'name')
                    ->where(function ($query) use ($campusId) {
                        return $query->where(function ($q) use ($campusId) {
                            $q->whereNull('campus_id')->orWhere('campus_id', $campusId);
                        });
                    })
                    ->ignore($this->editingTalentId),
            ],
            'talentCategory' => 'required|string|max:255',
            'talentDescription' => 'nullable|string|max:1000',
            'talentTheme' => ['required', Rule::enum(TalentTheme::class)],
        ];

        $this->validate($rules);

        if ($this->editingTalentId) {
            $talent = Talent::query()
                ->forCampus($campusId)
                ->findOrFail($this->editingTalentId);

            $slug = Str::slug($this->talentName);
            if ($talent->campus_id) {
                $slug .= '-'.$talent->campus_id;
            }

            $talent->update([
                'name' => $this->talentName,
                'slug' => $slug,
                'category' => $this->talentCategory,
                'description' => $this->talentDescription,
                'theme' => TalentTheme::from($this->talentTheme),
            ]);
        } else {
            Talent::create([
                'name' => $this->talentName,
                'category' => $this->talentCategory,
                'description' => $this->talentDescription,
                'theme' => TalentTheme::from($this->talentTheme),
                'campus_id' => $campusId,
            ]);
        }

        $this->closeTalentForm();
        session()->flash('talent-status', 'Talent saved successfully.');
    }

    public function deleteTalent(int $talentId): void
    {
        abort_unless(auth()->user()->canOrganizeEvents(), 403);

        $campusId = auth()->user()->campus_id ?? auth()->id();

        $talent = Talent::query()
            ->forCampus($campusId)
            ->findOrFail($talentId);

        $talent->delete();
        session()->flash('talent-status', 'Talent deleted successfully.');
    }

    public function selectCandidate(int $applicationId, AwardXp $awardXp): void
    {
        abort_unless(auth()->user()->canOrganizeEvents(), 403);

        $application = EventApplication::query()->findOrFail($applicationId);
        $this->authorize('update', $application->event);

        $application->update([
            'status' => EventApplicationStatus::Accepted,
        ]);

        $application->user->notify(
            new EventApplicationSelectedNotification($application->event, $application->talent?->name)
        );

        $awardXp->handle($application->user, XpEventType::EventRsvp, $application->event);

        session()->flash('status', "{$application->user->name} has been selected for {$application->event->title}!");
    }

    public function declineCandidate(int $applicationId): void
    {
        abort_unless(auth()->user()->canOrganizeEvents(), 403);

        $application = EventApplication::query()->findOrFail($applicationId);
        $this->authorize('update', $application->event);

        $application->update([
            'status' => EventApplicationStatus::Declined,
        ]);
    }

    private function resetAnnouncementForm(): void
    {
        $this->editingAnnouncementId = null;
        $this->announcementTitle = '';
        $this->announcementBody = '';
        $this->announcementPriority = AnnouncementPriority::Standard->value;
        $this->announcementAudience = AnnouncementAudience::Everyone->value;
        $this->announcementAudienceValue = '';
        $this->announcementStartsAt = '';
        $this->announcementExpiresAt = '';
        $this->announcementLinkUrl = '';
        $this->announcementLinkLabel = '';
        $this->announcementPinned = false;
    }

    /**
     * @return Builder<Announcement>
     */
    private function campusAnnouncementsQuery(): Builder
    {
        return Announcement::query()->where('campus_id', auth()->id());
    }

    /** @return list<string> */
    private function announcementAudienceValues(AnnouncementAudience $audience): array
    {
        $column = match ($audience) {
            AnnouncementAudience::Batch => 'batch',
            AnnouncementAudience::Faculty => 'faculty',
            AnnouncementAudience::Department => 'department',
            AnnouncementAudience::Program => 'program',
            AnnouncementAudience::Everyone => null,
        };

        if ($column === null) {
            return [];
        }

        return User::query()
            ->withoutGlobalScopes()
            ->where('role', Role::Student)
            ->where('campus_id', auth()->id())
            ->join('profiles', 'profiles.user_id', '=', 'users.id')
            ->whereNotNull("profiles.{$column}")
            ->where("profiles.{$column}", '!=', '')
            ->distinct()
            ->orderBy("profiles.{$column}")
            ->pluck("profiles.{$column}")
            ->map(fn (mixed $value): string => (string) $value)
            ->values()
            ->all();
    }

    /**
     * @return Builder<PortfolioItem>
     */
    private function campusPortfolioItemsQuery(): Builder
    {
        return PortfolioItem::query()
            ->whereHas('user', fn (Builder $query) => $query->where('campus_id', auth()->id()));
    }

    /**
     * @return Builder<Report>
     */
    private function campusReportsQuery(): Builder
    {
        return Report::query()
            ->whereHasMorph('reportable', [PortfolioItem::class], function (Builder $query): void {
                $query->whereHas('user', fn (Builder $query) => $query->where('campus_id', auth()->id()));
            });
    }

    public function render(): View
    {
        $campusUser = auth()->user();
        $campusId = $campusUser->campus_id ?? $campusUser->id;

        $events = Event::query()
            ->when($campusUser->isCampus(), fn ($query) => $query->whereBelongsTo($campusUser, 'organizer'))
            ->with(['talent:id,name', 'organizer.profile'])
            ->withCount('applications')
            ->latest('starts_at')
            ->get();

        $selectedEvent = $events->firstWhere('id', $this->selectedEventId) ?? $events->first();

        if ($selectedEvent !== null) {
            $selectedEvent->load(['applications.user.profile', 'applications.talent:id,name']);
        }

        $pendingStudents = User::query()
            ->pendingStudentsForCampus($campusUser->id)
            ->with('profile.primaryTalentModel')
            ->latest()
            ->get();

        $approvedStudents = User::query()
            ->approvedStudentsForCampus($campusUser->id)
            ->with('profile.primaryTalentModel')
            ->latest()
            ->get();

        $manageableStudents = User::query()
            ->where('role', Role::Student)
            ->where('campus_id', auth()->id())
            ->whereIn('status', [UserStatus::Approved, UserStatus::Banned])
            ->when($this->studentSearch !== '', fn ($query) => $query->where(fn ($query) => $query
                ->where('name', 'like', "%{$this->studentSearch}%")
                ->orWhere('email', 'like', "%{$this->studentSearch}%")))
            ->latest()
            ->paginate(15, pageName: 'studentsPage');

        $reportStatus = ReportStatus::tryFrom($this->moderationStatus);
        $moderationSearch = Str::squish($this->moderationSearch);

        $reports = $this->campusReportsQuery()
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
            ->with(['reporter.profile', 'reportable', 'reportable.talent', 'reportable.user.profile'])
            ->latest()
            ->paginate(8, pageName: 'reportsPage');

        $contentState = in_array($this->moderationContentState, ['published', 'removed', 'all'], true)
            ? $this->moderationContentState
            : 'published';
        $contentMediaType = PortfolioMediaType::tryFrom($this->moderationContentType);
        $contentSearch = Str::squish($this->moderationContentSearch);

        $moderationItems = $this->campusPortfolioItemsQuery()
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
            ->with(['talent:id,name', 'user.profile'])
            ->withCount([
                'reports',
                'reports as pending_reports_count' => fn (Builder $query) => $query->where('status', ReportStatus::Pending),
            ])
            ->latest('updated_at')
            ->paginate(9, pageName: 'contentPage');

        $pendingReportsCount = $this->campusReportsQuery()->pending()->count();
        $resolvedReportsLast30Days = $this->campusReportsQuery()
            ->whereIn('status', [ReportStatus::Reviewed, ReportStatus::Dismissed, ReportStatus::Actioned])
            ->where('updated_at', '>=', now()->subDays(30))
            ->count();
        $publishedItemsCount = $this->campusPortfolioItemsQuery()->published()->count();
        $removedItemsCount = $this->campusPortfolioItemsQuery()
            ->whereNull('published_at')
            ->whereHas('reports', fn (Builder $query) => $query->where('status', ReportStatus::Actioned))
            ->count();

        $talents = Talent::query()
            ->forCampus($campusId)
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        $talentCategories = TalentCategory::query()
            ->forCampus($campusId)
            ->withCount(['talents' => fn ($q) => $q->forCampus($campusId)])
            ->orderBy('name')
            ->get();

        $categories = Talent::query()
            ->withCount(['portfolioItems as published_items_count' => fn ($query) => $query
                ->published()
                ->whereHas('user', fn ($query) => $query->where('campus_id', auth()->id()))])
            ->orderByDesc('published_items_count')
            ->get();

        $announcementSearch = Str::squish($this->announcementSearch);
        $announcements = $this->campusAnnouncementsQuery()
            ->when($announcementSearch !== '', fn (Builder $query) => $query->where(fn (Builder $query) => $query
                ->where('title', 'like', "%{$announcementSearch}%")
                ->orWhere('body', 'like', "%{$announcementSearch}%")))
            ->when($this->announcementStatus === 'draft', fn (Builder $query) => $query->whereNull('published_at'))
            ->when($this->announcementStatus === 'scheduled', fn (Builder $query) => $query
                ->whereNotNull('published_at')
                ->where('starts_at', '>', now()))
            ->when($this->announcementStatus === 'active', fn (Builder $query) => $query->active())
            ->when($this->announcementStatus === 'expired', fn (Builder $query) => $query
                ->whereNotNull('published_at')
                ->whereNotNull('expires_at')
                ->where('expires_at', '<=', now()))
            ->withCount([
                'recipients as read_count' => fn (Builder $query) => $query->whereNotNull('announcement_user.read_at'),
            ])
            ->orderByDesc('is_pinned')
            ->latest('updated_at')
            ->paginate(8, pageName: 'announcementsPage');

        $allAnnouncements = $this->campusAnnouncementsQuery()->get();
        $selectedAudience = AnnouncementAudience::tryFrom($this->announcementAudience) ?? AnnouncementAudience::Everyone;

        return view('livewire.campus.dashboard', [
            'events' => $events,
            'selectedEvent' => $selectedEvent,
            'pendingStudents' => $pendingStudents,
            'approvedStudents' => $approvedStudents,
            'manageableStudents' => $manageableStudents,
            'talents' => $talents,
            'talentCategories' => $talentCategories,
            'totalStudents' => $approvedStudents->count(),
            'totalPending' => $pendingStudents->count(),
            'totalEvents' => $events->count(),
            'moderationItems' => $moderationItems,
            'reports' => $reports,
            'pendingReportsCount' => $pendingReportsCount,
            'resolvedReportsLast30Days' => $resolvedReportsLast30Days,
            'publishedItemsCount' => $publishedItemsCount,
            'removedItemsCount' => $removedItemsCount,
            'categories' => $categories,
            'announcements' => $announcements,
            'announcementStats' => [
                'active' => $allAnnouncements->filter(fn (Announcement $announcement) => $announcement->status() === 'active')->count(),
                'scheduled' => $allAnnouncements->filter(fn (Announcement $announcement) => $announcement->status() === 'scheduled')->count(),
                'draft' => $allAnnouncements->filter(fn (Announcement $announcement) => $announcement->status() === 'draft')->count(),
                'expired' => $allAnnouncements->filter(fn (Announcement $announcement) => $announcement->status() === 'expired')->count(),
            ],
            'announcementAudienceValues' => $this->announcementAudienceValues($selectedAudience),
            'newStudentsLast7Days' => User::query()
                ->where('role', Role::Student)
                ->where('campus_id', auth()->id())
                ->where('created_at', '>=', now()->subDays(7))
                ->count(),
            'newStudentsLast30Days' => User::query()
                ->where('role', Role::Student)
                ->where('campus_id', auth()->id())
                ->where('created_at', '>=', now()->subDays(30))
                ->count(),
            'topStudents' => User::query()
                ->approvedStudentsForCampus(auth()->id())
                ->ranked()
                ->limit(5)
                ->get(),
            'xpEarnedLast30Days' => XpEvent::query()
                ->whereHas('user', fn ($query) => $query->where('campus_id', auth()->id()))
                ->where('created_at', '>=', now()->subDays(30))
                ->sum('points'),
            'itemsPublishedLast30Days' => PortfolioItem::query()
                ->published()
                ->whereHas('user', fn ($query) => $query->where('campus_id', auth()->id()))
                ->where('published_at', '>=', now()->subDays(30))
                ->count(),
            'eventApplicationsTotal' => EventApplication::query()
                ->whereHas('event', fn ($query) => $query->whereBelongsTo($campusUser, 'organizer'))
                ->count(),
            'eventApplicationsAccepted' => EventApplication::query()
                ->whereHas('event', fn ($query) => $query->whereBelongsTo($campusUser, 'organizer'))
                ->where('status', EventApplicationStatus::Accepted)
                ->count(),
            'weeklyPublishedCounts' => collect(range(5, 0))->map(function (int $weeksAgo) {
                $start = now()->subWeeks($weeksAgo)->startOfWeek();
                $end = now()->subWeeks($weeksAgo)->endOfWeek();

                return [
                    'label' => $start->format('M j'),
                    'count' => PortfolioItem::query()
                        ->published()
                        ->whereHas('user', fn ($query) => $query->where('campus_id', auth()->id()))
                        ->whereBetween('published_at', [$start, $end])
                        ->count(),
                ];
            }),
        ]);
    }
}
