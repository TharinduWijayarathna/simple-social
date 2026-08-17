<?php

namespace App\Policies;

use App\Models\Profile;
use App\Models\User;

class ProfilePolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Profile $profile): bool
    {
        return true;
    }

    public function update(User $user, Profile $profile): bool
    {
        return $user->is($profile->user) || $user->isAdmin();
    }
}
