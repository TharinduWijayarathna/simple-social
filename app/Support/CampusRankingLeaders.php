<?php

namespace App\Support;

use App\Models\CampusRanking;
use App\Models\PortfolioItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class CampusRankingLeaders
{
    /**
     * Top students on a campus ranked by likes on posts for a ranking's talent.
     *
     * @return Collection<int, User>
     */
    public static function for(CampusRanking $ranking, int $campusId, int $limit = 10): Collection
    {
        $talentId = $ranking->talent_id;

        return User::query()
            ->where('campus_id', $campusId)
            ->where('role', 'student')
            ->where('status', 'approved')
            ->addSelect([
                'talent_likes_total' => DB::table('likes')
                    ->selectRaw('COALESCE(SUM(1), 0)')
                    ->join('portfolio_items', function ($join) use ($talentId) {
                        $join->on('likes.likeable_id', '=', 'portfolio_items.id')
                            ->where('likes.likeable_type', '=', PortfolioItem::class)
                            ->where('portfolio_items.talent_id', '=', $talentId)
                            ->whereNotNull('portfolio_items.published_at')
                            ->where('portfolio_items.published_at', '<=', now());
                    })
                    ->whereColumn('portfolio_items.user_id', 'users.id'),
            ])
            ->with(['profile' => fn ($query) => $query->select('id', 'user_id', 'avatar_path', 'headline', 'batch', 'program')])
            ->orderByDesc('talent_likes_total')
            ->orderBy('name')
            ->limit($limit)
            ->get();
    }
}
