<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\AwardXp;
use App\Enums\XpEventType;
use App\Http\Controllers\Controller;
use App\Models\PortfolioItem;
use App\Notifications\PortfolioCommentedNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, PortfolioItem $portfolioItem, AwardXp $awardXp): JsonResponse
    {
        $this->authorize('view', $portfolioItem);

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $comment = $portfolioItem->comments()->create([
            'user_id' => $request->user()->id,
            'body' => $validated['body'],
        ]);

        if ($portfolioItem->user_id !== $request->user()->id) {
            $awardXp->handle($portfolioItem->user, XpEventType::CommentReceived, $comment);
            $portfolioItem->user->notify((new PortfolioCommentedNotification($request->user(), $portfolioItem, $comment))->afterCommit());
        }

        return response()->json([
            'id' => $comment->id,
            'body' => $comment->body,
        ], 201);
    }
}
