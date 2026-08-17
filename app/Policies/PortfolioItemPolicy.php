<?php

namespace App\Policies;

use App\Models\PortfolioItem;
use App\Models\User;

class PortfolioItemPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, PortfolioItem $portfolioItem): bool
    {
        if ($portfolioItem->isPublished()) {
            return true;
        }

        return $user !== null && ($user->is($portfolioItem->user) || $user->isAdmin());
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, PortfolioItem $portfolioItem): bool
    {
        return $user->is($portfolioItem->user) || $user->isAdmin();
    }

    public function delete(User $user, PortfolioItem $portfolioItem): bool
    {
        return $this->update($user, $portfolioItem);
    }
}
