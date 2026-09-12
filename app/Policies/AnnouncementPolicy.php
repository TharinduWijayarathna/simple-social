<?php

namespace App\Policies;

use App\Models\Announcement;
use App\Models\User;

class AnnouncementPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isStudent() || $user->canOrganizeEvents();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Announcement $announcement): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->canOrganizeEvents()) {
            return $announcement->campus_id === $user->id;
        }

        return Announcement::query()
            ->active()
            ->visibleTo($user)
            ->whereKey($announcement)
            ->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->canOrganizeEvents();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Announcement $announcement): bool
    {
        return $user->isAdmin() || ($user->canOrganizeEvents() && $announcement->campus_id === $user->id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Announcement $announcement): bool
    {
        return $this->update($user, $announcement);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Announcement $announcement): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Announcement $announcement): bool
    {
        return false;
    }
}
