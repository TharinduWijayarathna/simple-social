<?php

namespace App\Policies;

use App\Models\Collaboration;
use App\Models\User;

class CollaborationPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Collaboration $collaboration): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Collaboration $collaboration): bool
    {
        return $user->is($collaboration->owner) || $user->isAdmin();
    }

    public function delete(User $user, Collaboration $collaboration): bool
    {
        return $this->update($user, $collaboration);
    }

    public function respondToRequests(User $user, Collaboration $collaboration): bool
    {
        return $this->update($user, $collaboration);
    }
}
