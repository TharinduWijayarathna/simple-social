<?php

namespace App\Livewire\Events;

use App\Enums\EventApplicationStatus;
use App\Models\Event;
use App\Models\EventApplication;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts::app')]
#[Title('Campus Events')]
class Index extends Component
{
    #[Url(as: 'tab')]
    public string $activeTab = 'all'; // 'all', 'my_rsvps', 'chosen', 'my_campus'

    #[Url(as: 'search')]
    public string $search = '';

    public ?int $selectedTalentId = null;

    public function render(): View
    {
        $user = auth()->user();

        // 1. All Published Events
        $allEvents = Event::query()
            ->published()
            ->when($this->search !== '', function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', "%{$this->search}%")
                        ->orWhere('location', 'like', "%{$this->search}%")
                        ->orWhere('description', 'like', "%{$this->search}%");
                });
            })
            ->when($this->selectedTalentId, fn ($query) => $query->where('talent_id', $this->selectedTalentId))
            ->with(['organizer:id,name', 'talent:id,name', 'talents:id,name'])
            ->withCount('applications')
            ->orderBy('starts_at')
            ->paginate(12);

        // 2. User's Applications / RSVPs
        $myApplications = $user ? EventApplication::query()
            ->where('user_id', $user->id)
            ->with(['event.organizer:id,name', 'event.talent:id,name', 'talent:id,name'])
            ->latest()
            ->get() : collect();

        // 3. User's Chosen Events
        $chosenApplications = $user ? EventApplication::query()
            ->where('user_id', $user->id)
            ->where('status', EventApplicationStatus::Accepted)
            ->with(['event.organizer:id,name', 'event.talent:id,name', 'talent:id,name'])
            ->latest()
            ->get() : collect();

        // 4. Campus Organizers' Created Events
        $myCampusEvents = ($user && $user->canOrganizeEvents()) ? Event::query()
            ->where('organizer_id', $user->id)
            ->with(['applications.user:id,name', 'talent:id,name'])
            ->withCount('applications')
            ->latest('starts_at')
            ->get() : collect();

        return view('livewire.events.index', [
            'events' => $allEvents,
            'myApplications' => $myApplications,
            'chosenApplications' => $chosenApplications,
            'myCampusEvents' => $myCampusEvents,
            'chosenCount' => $chosenApplications->count(),
            'myAppCount' => $myApplications->count(),
        ]);
    }
}
