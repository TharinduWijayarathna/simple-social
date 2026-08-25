<?php

namespace App\Livewire\Campus;

use App\Actions\AwardXp;
use App\Enums\EventApplicationStatus;
use App\Enums\Role;
use App\Enums\TalentTheme;
use App\Enums\UserStatus;
use App\Enums\XpEventType;
use App\Models\Event;
use App\Models\EventApplication;
use App\Models\Talent;
use App\Models\TalentCategory;
use App\Models\User;
use App\Notifications\EventApplicationSelectedNotification;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts::campus-panel')]
#[Title('Campus Dashboard')]
class Dashboard extends Component
{
    #[Url(as: 'tab')]
    public string $activeTab = 'overview';

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

    public function banStudent(int $userId): void
    {
        abort_unless(auth()->user()->canOrganizeEvents(), 403);

        $user = User::query()
            ->where('campus_id', auth()->user()->campus_id)
            ->where('role', Role::Student)
            ->findOrFail($userId);

        $user->update(['status' => UserStatus::Banned]);
    }

    public function unbanStudent(int $userId): void
    {
        abort_unless(auth()->user()->canOrganizeEvents(), 403);

        $user = User::query()
            ->where('campus_id', auth()->user()->campus_id)
            ->where('role', Role::Student)
            ->findOrFail($userId);

        $user->update(['status' => UserStatus::Approved]);
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

        $bannedStudents = User::query()
            ->bannedStudentsForCampus($campusUser->id)
            ->with('profile.primaryTalentModel')
            ->latest()
            ->get();

        $talents = Talent::query()
            ->forCampus($campusId)
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        $categories = TalentCategory::query()
            ->forCampus($campusId)
            ->withCount(['talents' => fn ($q) => $q->forCampus($campusId)])
            ->orderBy('name')
            ->get();

        return view('livewire.campus.dashboard', [
            'events' => $events,
            'selectedEvent' => $selectedEvent,
            'pendingStudents' => $pendingStudents,
            'approvedStudents' => $approvedStudents,
            'bannedStudents' => $bannedStudents,
            'talents' => $talents,
            'categories' => $categories,
            'totalStudents' => $approvedStudents->count(),
            'totalPending' => $pendingStudents->count(),
            'totalBanned' => $bannedStudents->count(),
            'totalEvents' => $events->count(),
        ]);
    }
}
