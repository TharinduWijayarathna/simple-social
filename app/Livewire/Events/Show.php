<?php

namespace App\Livewire\Events;

use App\Actions\AwardXp;
use App\Enums\EventApplicationStatus;
use App\Enums\XpEventType;
use App\Models\Event;
use App\Models\EventApplication;
use App\Notifications\EventApplicationSelectedNotification;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::app')]
#[Title('Event Details')]
class Show extends Component
{
    public Event $event;

    public ?int $selected_talent_id = null;

    public string $message = '';

    public string $applicantFilter = 'all'; // 'all', 'pending', 'accepted', 'declined'

    public function mount(Event $event): void
    {
        $this->authorize('view', $event);
        $this->event = $event;

        // Pre-fill user's application details if available
        $userApp = $event->applications()->where('user_id', auth()->id())->first();
        if ($userApp) {
            $this->selected_talent_id = $userApp->talent_id;
            $this->message = $userApp->message ?? '';
        }
    }

    public function rsvp(AwardXp $awardXp): void
    {
        $this->applyOrRsvp($awardXp);
    }

    public function applyOrRsvp(AwardXp $awardXp): void
    {
        $this->authorize('rsvp', $this->event);

        $this->validate([
            'selected_talent_id' => ['nullable', 'integer', 'exists:talents,id'],
            'message' => ['nullable', 'string', 'max:1000'],
        ]);

        $application = EventApplication::query()->updateOrCreate(
            [
                'event_id' => $this->event->id,
                'user_id' => auth()->id(),
            ],
            [
                'talent_id' => $this->selected_talent_id,
                'message' => $this->message,
                'status' => EventApplicationStatus::Pending,
            ]
        );

        $awardXp->handle(auth()->user(), XpEventType::EventRsvp, $this->event);

        session()->flash('status', 'Your application/RSVP has been submitted successfully to campus organizers.');
    }

    public function cancelApplication(): void
    {
        $this->authorize('rsvp', $this->event);

        EventApplication::query()
            ->where('event_id', $this->event->id)
            ->where('user_id', auth()->id())
            ->delete();

        $this->selected_talent_id = null;
        $this->message = '';

        session()->flash('status', 'Your application/RSVP has been withdrawn.');
    }

    public function selectCandidate(int $applicationId, AwardXp $awardXp): void
    {
        abort_unless(auth()->user()->is($this->event->organizer) || auth()->user()->isAdmin(), 403);

        $application = EventApplication::query()
            ->with(['user', 'talent'])
            ->where('event_id', $this->event->id)
            ->findOrFail($applicationId);

        $application->update([
            'status' => EventApplicationStatus::Accepted,
        ]);

        if ($application->user) {
            // Dispatch selection notification to student
            $application->user->notify(
                new EventApplicationSelectedNotification($this->event, $application->talent?->name)
            );

            // Award bonus XP for being selected for campus event
            $awardXp->handle($application->user, XpEventType::EventRsvp, $this->event);

            session()->flash('status', "{$application->user->name} has been selected for this event! Notification and campus contact details sent.");
        }
    }

    public function declineCandidate(int $applicationId): void
    {
        abort_unless(auth()->user()->is($this->event->organizer) || auth()->user()->isAdmin(), 403);

        $application = EventApplication::query()
            ->with(['user', 'talent'])
            ->where('event_id', $this->event->id)
            ->findOrFail($applicationId);

        $application->update([
            'status' => EventApplicationStatus::Declined,
        ]);

        $userName = $application->user?->name ?? 'Student';
        session()->flash('status', "Application for {$userName} has been marked as declined.");
    }

    public function resetCandidate(int $applicationId): void
    {
        abort_unless(auth()->user()->is($this->event->organizer) || auth()->user()->isAdmin(), 403);

        $application = EventApplication::query()
            ->where('event_id', $this->event->id)
            ->findOrFail($applicationId);

        $application->update([
            'status' => EventApplicationStatus::Pending,
        ]);
    }

    public function render(): View
    {
        $this->event->load([
            'organizer',
            'talent',
            'talents',
            'applications.user',
            'applications.talent',
        ]);

        $userApplication = auth()->check()
            ? $this->event->applications->firstWhere('user_id', auth()->id())
            : null;

        $filteredApplications = $this->event->applications
            ->when($this->applicantFilter === 'pending', fn ($c) => $c->where('status', EventApplicationStatus::Pending))
            ->when($this->applicantFilter === 'accepted', fn ($c) => $c->where('status', EventApplicationStatus::Accepted))
            ->when($this->applicantFilter === 'declined', fn ($c) => $c->where('status', EventApplicationStatus::Declined));

        return view('livewire.events.show', [
            'userApplication' => $userApplication,
            'filteredApplications' => $filteredApplications,
            'isOrganizer' => auth()->check() && (auth()->user()->is($this->event->organizer) || auth()->user()->isAdmin()),
        ]);
    }
}
