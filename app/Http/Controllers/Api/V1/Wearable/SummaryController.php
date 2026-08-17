<?php

namespace App\Http\Controllers\Api\V1\Wearable;

use App\Enums\EventApplicationStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Wearable\SummaryResource;
use App\Models\Event;
use App\Models\PortfolioItem;
use Illuminate\Http\Request;

class SummaryController extends Controller
{
    public function __invoke(Request $request): SummaryResource
    {
        $user = $request->user()->load(['profile.talents']);

        $nextEvent = Event::query()
            ->published()
            ->upcoming()
            ->whereHas(
                'applications',
                fn ($query) => $query->whereBelongsTo($user)->where('status', EventApplicationStatus::RsvpYes),
            )
            ->first(['id', 'title', 'starts_at', 'location']);

        $latestItems = PortfolioItem::query()
            ->published()
            ->whereBelongsTo($user)
            ->latest('published_at')
            ->limit(3)
            ->get(['id', 'title', 'thumbnail_path']);

        $favorites = $user->profile?->talents
            ->filter(fn ($talent): bool => (bool) $talent->pivot->is_favorite)
            ->take((int) config('vibecraft.wearable.favorite_talent_limit'))
            ->map(fn ($talent): array => [
                'id' => $talent->id,
                'name' => $talent->name,
                'slug' => $talent->slug,
            ])
            ->values();

        return SummaryResource::make([
            'xp' => $user->xp,
            'rank' => $user->current_rank,
            'rank_change' => $user->rankChange(),
            'unread_count' => $user->unreadNotifications()->count(),
            'next_event' => $nextEvent === null ? null : [
                'id' => $nextEvent->id,
                'title' => $nextEvent->title,
                'starts_at' => $nextEvent->starts_at->toIso8601String(),
                'location' => $nextEvent->location,
            ],
            'latest_portfolio' => $latestItems->map(fn (PortfolioItem $item): array => [
                'id' => $item->id,
                'title' => $item->title,
                'thumbnail_url' => $item->thumbnailUrl(),
            ])->values(),
            'favorite_talents' => $favorites,
        ]);
    }
}
