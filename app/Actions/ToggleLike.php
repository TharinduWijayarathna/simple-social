<?php

namespace App\Actions;

use App\Enums\XpEventType;
use App\Models\Like;
use App\Models\PortfolioItem;
use App\Models\User;
use App\Notifications\PortfolioLikedNotification;

class ToggleLike
{
    public function __construct(private AwardXp $awardXp) {}

    /**
     * @return array{liked: bool, like: Like|null}
     */
    public function handle(User $user, PortfolioItem $portfolioItem): array
    {
        $existing = Like::query()
            ->whereBelongsTo($user)
            ->whereMorphedTo('likeable', $portfolioItem)
            ->first();

        if ($existing !== null) {
            $existing->delete();

            return ['liked' => false, 'like' => null];
        }

        $like = $portfolioItem->likes()->create([
            'user_id' => $user->id,
        ]);

        if ($portfolioItem->user_id !== $user->id) {
            $this->awardXp->handle($portfolioItem->user, XpEventType::LikeReceived, $like);
            $portfolioItem->user->notify((new PortfolioLikedNotification($user, $portfolioItem))->afterCommit());
        }

        return ['liked' => true, 'like' => $like];
    }
}
