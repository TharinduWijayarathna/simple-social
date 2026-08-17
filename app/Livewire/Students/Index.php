<?php

namespace App\Livewire\Students;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts::app')]
#[Title('Talents')]
class Index extends Component
{
    use WithPagination;

    public function render(): View
    {
        return view('livewire.students.index', [
            'students' => User::query()
                ->students()
                ->with(['profile.talents', 'portfolioItems' => fn ($query) => $query->published()->with('talent:id,name,theme')->latest('published_at')])
                ->latest()
                ->paginate(12),
        ]);
    }
}
