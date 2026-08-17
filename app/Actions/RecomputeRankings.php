<?php

namespace App\Actions;

use App\Models\User;

class RecomputeRankings
{
    public function handle(): int
    {
        $rank = 0;

        User::query()
            ->students()
            ->orderByDesc('xp')
            ->orderBy('id')
            ->chunk(100, function ($users) use (&$rank): void {
                foreach ($users as $user) {
                    $rank++;

                    $user->forceFill([
                        'previous_rank' => $user->current_rank,
                        'current_rank' => $rank,
                    ])->save();
                }
            });

        return $rank;
    }
}
