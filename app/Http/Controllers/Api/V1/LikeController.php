<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\ToggleLike;
use App\Http\Controllers\Controller;
use App\Models\PortfolioItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function store(Request $request, PortfolioItem $portfolioItem, ToggleLike $toggleLike): JsonResponse
    {
        $this->authorize('view', $portfolioItem);

        $result = $toggleLike->handle($request->user(), $portfolioItem);

        return response()->json([
            'liked' => $result['liked'],
            'likes_count' => $portfolioItem->likes()->count(),
        ]);
    }
}
