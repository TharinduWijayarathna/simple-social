<?php

namespace App\Livewire\Events;

use App\Actions\RsvpToEvent;
use App\Models\Event;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::app')]
#[Title('Event')]
class Show extends Component
{
    public Event $event;

    public function mount(Event $event): void
    {
        $this->authorize('view', $event);
        $this->event = $event;
    }

    public function rsvp(RsvpToEvent $rsvpToEvent): void
    {
        $this->authorize('rsvp', $this->event);
        $rsvpToEvent->handle(auth()->user(), $this->event, true);
        session()->flash('status', 'You are going. See you there.');
    }

    public function render(): View
    {
        $this->event->load(['organizer:id,name', 'talent:id,name']);

        return view('livewire.events.show');
    }
}
