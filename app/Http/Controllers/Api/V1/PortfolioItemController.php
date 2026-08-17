<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\StorePortfolioItem;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StorePortfolioItemRequest;
use App\Http\Resources\Api\V1\PortfolioItemResource;
use App\Models\PortfolioItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class PortfolioItemController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $items = PortfolioItem::query()
            ->published()
            ->with(['user:id,name,role,xp,current_rank,previous_rank', 'talent:id,name,slug'])
            ->withCount(['likes', 'comments'])
            ->when($request->integer('talent_id'), fn ($query, int $talentId) => $query->where('talent_id', $talentId))
            ->latest('published_at')
            ->paginate(20);

        return PortfolioItemResource::collection($items);
    }

    public function store(StorePortfolioItemRequest $request, StorePortfolioItem $storePortfolioItem): JsonResponse
    {
        $item = $storePortfolioItem->handle($request->user(), $request->validated());

        return PortfolioItemResource::make($item->load(['user', 'talent']))
            ->response()
            ->setStatusCode(201);
    }

    public function show(PortfolioItem $portfolioItem): PortfolioItemResource
    {
        $this->authorize('view', $portfolioItem);

        $portfolioItem->load(['user:id,name,role,xp,current_rank,previous_rank', 'talent:id,name,slug'])
            ->loadCount(['likes', 'comments']);

        return PortfolioItemResource::make($portfolioItem);
    }

    public function destroy(Request $request, PortfolioItem $portfolioItem): Response
    {
        $this->authorize('delete', $portfolioItem);

        $portfolioItem->delete();

        return response()->noContent();
    }
}
