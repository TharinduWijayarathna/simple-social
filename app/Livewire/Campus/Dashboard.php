<?php

namespace App\Livewire\Campus;

use App\Actions\AwardXp;
use App\Enums\EventApplicationStatus;
use App\Enums\UserStatus;
use App\Enums\XpEventType;
use App\Models\Event;
use App\Models\EventApplication;
use App\Models\User;
use App\Notifications\EventApplicationSelectedNotification;
use Illuminate\Contracts\View\View;
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

        $events = Event::query()
            ->when($campusUser->isCampusAdmin(), fn ($query) => $query->whereBelongsTo($campusUser, 'organizer'))
            ->with(['talent:id,name', 'organizer:id,name'])
            ->withCount('applications')
            ->latest('starts_at')
            ->get();

        $selectedEvent = $events->firstWhere('id', $this->selectedEventId) ?? $events->first();

        if ($selectedEvent !== null) {
            $selectedEvent->load(['applications.user:id,name,email', 'applications.talent:id,name']);
        }

        $pendingStudents = User::query()
            ->pendingStudentsForCampus(auth()->id())
            ->latest()
            ->get();

        $approvedStudents = User::query()
            ->approvedStudentsForCampus(auth()->id())
            ->latest()
            ->get();

        return view('livewire.campus.dashboard', [
            'events' => $events,
            'selectedEvent' => $selectedEvent,
            'pendingStudents' => $pendingStudents,
            'approvedStudents' => $approvedStudents,
            'totalStudents' => $approvedStudents->count(),
            'totalPending' => $pendingStudents->count(),
            'totalEvents' => $events->count(),
        ]);
    }
}
