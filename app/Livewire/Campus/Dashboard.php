<?php

namespace App\Livewire\Campus;

use App\Actions\AwardXp;
use App\Enums\EventApplicationStatus;
use App\Enums\ReportStatus;
use App\Enums\Role;
use App\Enums\TalentTheme;
use App\Enums\UserStatus;
use App\Enums\XpEventType;
use App\Models\Event;
use App\Models\EventApplication;
use App\Models\PortfolioItem;
use App\Models\Report;
use App\Models\Setting;
use App\Models\Talent;
use App\Models\TalentCategory;
use App\Models\User;
use App\Models\XpEvent;
use App\Notifications\EventApplicationSelectedNotification;
use Illuminate\Contracts\View\View;
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

    public string $announcementMessage = '';

    public bool $announcementEnabled = false;

    public function mount(): void
    {
        abort_unless(auth()->user()->canOrganizeEvents(), 403);

        $this->announcementMessage = Setting::get($this->announcementMessageKey(), '') ?? '';
        $this->announcementEnabled = Setting::get($this->announcementEnabledKey()) === '1';
    }

    protected function announcementMessageKey(): string
    {
        return 'campus_announcement_message_'.auth()->id();
    }

    protected function announcementEnabledKey(): string
    {
        return 'campus_announcement_enabled_'.auth()->id();
    }

    public function selectEvent(int $eventId): void
    {
        abort_unless(auth()->user()->canOrganizeEvents(), 403);

        $this->authorize('update', Event::query()->findOrFail($eventId));
        $this->selectedEventId = $eventId;
    }

    public function approveStudent(int $userId): void
    {
        abort_unless(auth()->user()->canOrganizeEvents(), 403);

        $user = User::query()
            ->pendingStudentsForCampus(auth()->id())
            ->findOrFail($userId);

        $user->update(['status' => UserStatus::Approved]);
    }

    public function rejectStudent(int $userId): void
    {
        abort_unless(auth()->user()->canOrganizeEvents(), 403);

        $user = User::query()
            ->pendingStudentsForCampus(auth()->id())
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

    public function unpublishItem(int $itemId): void
    {
        abort_unless(auth()->user()->canOrganizeEvents(), 403);

        PortfolioItem::query()->findOrFail($itemId)->update(['published_at' => null]);
    }

    public function moderateReport(int $reportId, string $status): void
    {
        abort_unless(auth()->user()->canOrganizeEvents(), 403);

        $report = Report::query()
            ->whereHasMorph('reportable', [PortfolioItem::class], function ($query) {
                $query->whereHas('user', fn ($query) => $query->where('campus_id', auth()->id()));
            })
            ->findOrFail($reportId);

        $report->update(['status' => ReportStatus::from($status)]);

        if ($report->status === ReportStatus::Actioned) {
            $report->loadMissing('reportable');

            if ($report->reportable instanceof PortfolioItem) {
                $report->reportable->update(['published_at' => null]);
            }
        }
    }

    public function saveAnnouncement(): void
    {
        abort_unless(auth()->user()->canOrganizeEvents(), 403);

        $this->validate([
            'announcementMessage' => ['nullable', 'string', 'max:280'],
        ]);

        Setting::set($this->announcementMessageKey(), $this->announcementMessage);
        Setting::set($this->announcementEnabledKey(), $this->announcementEnabled ? '1' : '0');
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

    public function render(): View
    {
        $campusUser = auth()->user();
        $campusId = $campusUser->campus_id ?? $campusUser->id;

        $events = Event::query()
            ->when($campusUser->isCampusAdmin(), fn ($query) => $query->whereBelongsTo($campusUser, 'organizer'))
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

        $recentItems = PortfolioItem::query()
            ->published()
            ->whereHas('user', fn ($query) => $query->where('campus_id', auth()->id()))
            ->with('user:id,name')
            ->latest('published_at')
            ->limit(20)
            ->get();

        $reports = Report::query()
            ->pending()
            ->whereHasMorph('reportable', [PortfolioItem::class], function ($query) {
                $query->whereHas('user', fn ($query) => $query->where('campus_id', auth()->id()));
            })
            ->with(['reporter:id,name', 'reportable'])
            ->latest()
            ->limit(20)
            ->get();

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
            'recentItems' => $recentItems,
            'reports' => $reports,
            'categories' => $categories,
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
