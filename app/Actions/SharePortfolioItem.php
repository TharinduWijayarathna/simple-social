<?php

namespace App\Actions;

use App\Models\PortfolioItem;
use App\Models\User;

class SharePortfolioItem
{
    public function handle(User $user, PortfolioItem $portfolioItem): string
    {
        abort_unless($portfolioItem->isPublished() || $user->is($portfolioItem->user) || $user->isAdmin(), 403);

        $portfolioItem->shares()->firstOrCreate([
            'user_id' => $user->id,
        ]);

        return route('portfolio.show', $portfolioItem);
    }
}
