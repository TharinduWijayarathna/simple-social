<?php

namespace App\Livewire;

use App\Models\CampusRanking;
use App\Models\Event;
use App\Models\PortfolioItem;
use App\Models\Status;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
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
        $campusId = auth()->check() ? auth()->user()->campus_id : User::first()?->campus_id;

        $topRankings = $campusId ? CampusRanking::query()
            ->where('campus_id', $campusId)
            ->where('is_active', true)
            ->with('talent:id,name,category')
            ->inRandomOrder()
            ->limit(2)
            ->get()
            ->map(function (CampusRanking $ranking) use ($campusId) {
                $talentId = $ranking->talent_id;

                $leaders = User::query()
                    ->where('campus_id', $campusId)
                    ->where('role', 'student')
                    ->where('status', 'approved')
                    ->whereHas('profile', function ($query) use ($talentId) {
                        $query->where('primary_talent_id', $talentId);
                    })
                    ->addSelect([
                        'talent_likes_total' => DB::table('likes')
                            ->selectRaw('COALESCE(SUM(1), 0)')
                            ->join('portfolio_items', function ($join) {
                                $join->on('likes.likeable_id', '=', 'portfolio_items.id')
                                    ->where('likes.likeable_type', '=', PortfolioItem::class)
                                    ->whereNotNull('portfolio_items.published_at')
                                    ->where('portfolio_items.published_at', '<=', now());
                            })
                            ->whereColumn('portfolio_items.user_id', 'users.id'),
                    ])
                    ->with('profile:id,user_id,avatar_path,headline')
                    ->orderByDesc('talent_likes_total')
                    ->limit(3)
                    ->get();

                return [
                    'ranking' => $ranking,
                    'leaders' => $leaders,
                ];
            }) : collect();

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
