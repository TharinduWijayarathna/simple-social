<?php

namespace App\Http\Controllers\Api\V1\Wearable;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LeaderboardController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $limit = (int) config('vibecraft.wearable.leaderboard_glance_size');

        $users = User::query()
            ->students()
            ->when(
                $request->integer('talent_id'),
                fn ($query, int $talentId) => $query->whereHas(
                    'portfolioItems',
                    fn ($portfolioQuery) => $portfolioQuery->published()->where('talent_id', $talentId),
                ),
            )
            ->orderByDesc('xp')
            ->orderBy('id')
            ->limit($limit)
            ->get(['id', 'name', 'xp', 'current_rank', 'previous_rank']);

        return response()->json([
            'scope' => $request->integer('talent_id') ? 'category' : 'global',
            'entries' => $users->map(fn (User $user): array => [
                'id' => $user->id,
                'name' => $user->name,
                'xp' => $user->xp,
                'rank' => $user->current_rank,
                'rank_change' => $user->rankChange(),
            ])->values(),
        ]);
    }
}
