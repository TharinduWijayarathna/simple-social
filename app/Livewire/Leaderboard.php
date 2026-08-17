<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts::app')]
#[Title('Leaderboard')]
class Leaderboard extends Component
{
    use WithPagination;

    public function render(): View
    {
        return view('livewire.leaderboard', [
            'students' => User::query()
                ->students()
                ->with('profile')
                ->orderByDesc('xp')
                ->orderBy('id')
                ->paginate(20),
        ]);
    }
}
