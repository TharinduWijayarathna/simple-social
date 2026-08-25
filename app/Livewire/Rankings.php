<?php

namespace App\Livewire;

use App\Models\CampusRanking;
use App\Models\PortfolioItem;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::app')]
#[Title('Rankings')]
class Rankings extends Component
{
    /** Re-render triggered by PostCard after a like is toggled. */
    #[On('like-toggled')]
    public function refresh(): void
    {
        // render() is automatically called after this method returns
    }

    public function render(): View
    {
        /** @var User $currentUser */
        $currentUser = auth()->user();

        $campusId = $currentUser->campus_id;

        $rankings = CampusRanking::query()
            ->where('campus_id', $campusId)
            ->where('is_active', true)
            ->with('talent:id,name,category')
            ->orderBy('title')
            ->get();

        // For each ranking, compute top-10 students by likes on their posts with that talent
        $rankingsWithLeaders = $rankings->map(function (CampusRanking $ranking) use ($campusId) {
            $talentId = $ranking->talent_id;

            // Get users that belong to this campus, ranked by likes on all their posts, filtered by their primary talent category
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
                ->limit(10)
                ->get();

            return [
                'ranking' => $ranking,
                'leaders' => $leaders,
            ];
        });

        return view('livewire.rankings', [
            'rankingsWithLeaders' => $rankingsWithLeaders,
        ]);
    }
}
