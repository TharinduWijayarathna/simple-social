<?php

namespace App\Actions;

use App\Enums\EventApplicationStatus;
use App\Enums\XpEventType;
use App\Models\Achievement;
use App\Models\User;
use App\Models\UserAchievement;
use App\Notifications\AchievementUnlockedNotification;
use Illuminate\Support\Facades\DB;

class EvaluateAchievements
{
    public function handle(User $user): void
    {
        $user->loadCount([
            'portfolioItems as published_portfolio_count' => fn ($query) => $query->published(),
            'followers',
            'eventApplications as rsvp_count' => fn ($query) => $query->where('status', EventApplicationStatus::RsvpYes),
        ]);

        $likesReceived = $user->portfolioItems()->withCount('likes')->get()->sum('likes_count');

        $progress = [
            'portfolio_count' => (int) $user->published_portfolio_count,
            'likes_received' => (int) $likesReceived,
            'followers_count' => (int) $user->followers_count,
            'xp_total' => (int) $user->xp,
            'events_rsvped' => (int) $user->rsvp_count,
        ];

        $unlockedIds = UserAchievement::query()
            ->whereBelongsTo($user)
            ->pluck('achievement_id');

        Achievement::query()
            ->whereNotIn('id', $unlockedIds)
            ->get()
            ->each(function (Achievement $achievement) use ($user, $progress): void {
                $current = $progress[$achievement->criteria_type] ?? 0;

                if ($current < $achievement->criteria_value) {
                    return;
                }

                DB::transaction(function () use ($user, $achievement): void {
                    $user->achievements()->attach($achievement->id, [
                        'unlocked_at' => now(),
                    ]);

                    if ($achievement->xp_reward > 0) {
                        $user->xpEvents()->create([
                            'type' => XpEventType::AchievementUnlocked,
                            'points' => $achievement->xp_reward,
                            'source_id' => $achievement->getKey(),
                            'source_type' => $achievement->getMorphClass(),
                        ]);

                        $user->increment('xp', $achievement->xp_reward);
                    }
                });

                $user->notify((new AchievementUnlockedNotification($achievement))->afterCommit());
            });
    }
}
