<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class LeaderboardController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $users = User::query()
            ->students()
            ->with('profile')
            ->when(
                $request->integer('talent_id'),
                fn ($query, int $talentId) => $query->whereHas(
                    'portfolioItems',
                    fn ($portfolioQuery) => $portfolioQuery->published()->where('talent_id', $talentId),
                ),
            )
            ->orderByDesc('xp')
            ->orderBy('id')
            ->paginate(20);

        return UserResource::collection($users);
    }
}
