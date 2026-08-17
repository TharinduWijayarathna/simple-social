<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\PortfolioItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function store(Request $request, PortfolioItem $portfolioItem): JsonResponse
    {
        $this->authorize('view', $portfolioItem);

        $validated = $request->validate([
            'score' => ['required', 'integer', 'min:1', 'max:5'],
        ]);

        $rating = $portfolioItem->ratings()->updateOrCreate(
            ['user_id' => $request->user()->id],
            ['score' => $validated['score']],
        );

        return response()->json([
            'score' => $rating->score,
        ]);
    }
}
