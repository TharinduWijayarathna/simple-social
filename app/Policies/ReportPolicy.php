<?php

namespace App\Policies;

use App\Models\PortfolioItem;
use App\Models\Report;
use App\Models\User;

class ReportPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, Report $report): bool
    {
        return $user->isAdmin() || $user->is($report->reporter);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Report $report): bool
    {
        return $user->isAdmin();
    }

    public function moderate(User $user, Report $report): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->isCampus()
            && $report->reportable_type === (new PortfolioItem)->getMorphClass()
            && PortfolioItem::query()
                ->whereKey($report->reportable_id)
                ->whereHas('user', fn ($query) => $query->where('campus_id', $user->id))
                ->exists();
    }
}
