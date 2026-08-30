<?php

namespace App\Livewire;

use App\Models\CampusRanking;
use App\Models\Event;
use App\Models\PortfolioItem;
use App\Models\Status;
use App\Models\User;
use App\Support\CampusRankingLeaders;
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
        $campusId = auth()->check() ? auth()->user()->campus_id : null;

        $topRankings = $campusId
            ? CampusRanking::query()
                ->where('campus_id', $campusId)
                ->where('is_active', true)
                ->with('talent:id,name,category')
                ->inRandomOrder()
                ->limit(2)
                ->get()
                ->map(fn (CampusRanking $ranking): array => [
                    'ranking' => $ranking,
                    'leaders' => CampusRankingLeaders::for($ranking, $campusId, 10),
                ])
            : collect();

        return view('livewire.feed', [
            'posts' => PortfolioItem::query()
                ->published()
                ->with([
                    'user.profile',
                    'talent:id,name,slug,theme',
                ])
                ->latest('published_at')
                ->paginate(12),
            'statuses' => Status::query()
                ->active()
                ->with('user.profile')
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
            'topRankings' => $topRankings,
        ]);
    }
}
