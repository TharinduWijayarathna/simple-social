<?php

namespace App\Livewire\Campus;

use App\Enums\Role;
use App\Enums\UserStatus;
use App\Models\Event;
use App\Models\User;
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

        $user = User::query()->pendingStudents()->findOrFail($userId);
        $user->update(['status' => UserStatus::Approved]);
    }

    public function rejectStudent(int $userId): void
    {
        abort_unless(auth()->user()->canOrganizeEvents(), 403);

        $user = User::query()->pendingStudents()->findOrFail($userId);
        $user->update(['status' => UserStatus::Rejected]);
    }

    public function render(): View
    {
        $user = auth()->user();

        $events = Event::query()
            ->when($user->isCampusAdmin(), fn ($query) => $query->whereBelongsTo($user, 'organizer'))
            ->with(['talent:id,name', 'organizer:id,name'])
            ->withCount('applications')
            ->latest('starts_at')
            ->get();

        $selectedEvent = $events->firstWhere('id', $this->selectedEventId) ?? $events->first();

        if ($selectedEvent !== null) {
            $selectedEvent->load(['applications.user:id,name']);
        }

        $pendingStudents = User::query()->pendingStudents()->latest()->get();
        $approvedStudents = User::query()
            ->where('role', Role::Student)
            ->where('status', UserStatus::Approved)
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
