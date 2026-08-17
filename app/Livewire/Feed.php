<?php

namespace App\Livewire;

use App\Models\Event;
use App\Models\PortfolioItem;
use App\Models\Status;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts::app')]
#[Title('Home')]
class Feed extends Component
{
    use WithPagination;

    public function render(): View
    {
        return view('livewire.feed', [
            'posts' => PortfolioItem::query()
                ->published()
                ->with([
                    'user:id,name',
                    'talent:id,name,slug,theme',
                ])
                ->latest('published_at')
                ->paginate(12),
            'statuses' => Status::query()
                ->active()
                ->with('user:id,name')
                ->latest()
                ->get()
                ->unique('user_id')
                ->values(),
            'upcomingEvents' => Event::query()
                ->published()
                ->upcoming()
                ->with('organizer:id,name')
                ->limit(4)
                ->get(),
            'risingStudents' => User::query()
                ->students()
                ->with('profile:id,user_id,headline')
                ->orderByDesc('xp')
                ->limit(5)
                ->get(),
        ]);
    }
}
