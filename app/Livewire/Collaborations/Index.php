<?php

namespace App\Livewire\Collaborations;

use App\Models\Collaboration;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::app')]
#[Title('Collaborations')]
class Index extends Component
{
    public function render(): View
    {
        return view('livewire.collaborations.index', [
            'collaborations' => Collaboration::query()
                ->with(['owner:id,name', 'talent:id,name'])
                ->withCount('members')
                ->latest()
                ->paginate(12),
        ]);
    }
}
