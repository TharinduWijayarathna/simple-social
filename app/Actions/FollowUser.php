<?php

namespace App\Actions;

use App\Enums\XpEventType;
use App\Models\Follow;
use App\Models\User;
use App\Notifications\UserFollowedNotification;
use InvalidArgumentException;

class FollowUser
{
    public function __construct(private AwardXp $awardXp) {}

    /**
     * @return array{following: bool, follow: Follow|null}
     */
    public function handle(User $follower, User $following): array
    {
        if ($follower->is($following)) {
            throw new InvalidArgumentException('Users cannot follow themselves.');
        }

        $existing = Follow::query()
            ->where('follower_id', $follower->id)
            ->where('following_id', $following->id)
            ->first();

        if ($existing !== null) {
            $existing->delete();

            return ['following' => false, 'follow' => null];
        }

        $follow = Follow::query()->create([
            'follower_id' => $follower->id,
            'following_id' => $following->id,
        ]);

        $this->awardXp->handle($following, XpEventType::FollowReceived, $follow);
        $following->notify((new UserFollowedNotification($follower))->afterCommit());

        return ['following' => true, 'follow' => $follow];
    }
}
