<?php

namespace App\Livewire\Events;

use App\Models\Event;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::app')]
#[Title('Events')]
class Index extends Component
{
    public function render(): View
    {
        return view('livewire.events.index', [
            'events' => Event::query()
                ->published()
                ->upcoming()
                ->with(['organizer:id,name', 'talent:id,name'])
                ->paginate(12),
        ]);
    }
}
