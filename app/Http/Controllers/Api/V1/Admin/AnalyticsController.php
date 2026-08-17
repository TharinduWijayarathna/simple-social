<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Enums\ReportStatus;
use App\Http\Controllers\Controller;
use App\Models\Like;
use App\Models\PortfolioItem;
use App\Models\Report;
use App\Models\Talent;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class AnalyticsController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $this->authorize('moderate', Report::class);

        $popularCategories = Talent::query()
            ->withCount(['portfolioItems as published_items_count' => fn ($query) => $query->published()])
            ->orderByDesc('published_items_count')
            ->limit(8)
            ->get(['id', 'name', 'slug']);

        return response()->json([
            'users' => User::query()->count(),
            'students' => User::query()->students()->count(),
            'portfolio_items' => PortfolioItem::query()->published()->count(),
            'likes' => Like::query()->count(),
            'pending_reports' => Report::query()->pending()->count(),
            'open_reports' => Report::query()->where('status', ReportStatus::Pending)->count(),
            'popular_categories' => $popularCategories,
        ]);
    }
}
