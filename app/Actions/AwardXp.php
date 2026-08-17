<?php

namespace App\Actions;

use App\Enums\XpEventType;
use App\Models\User;
use App\Models\XpEvent;
use Illuminate\Database\Eloquent\Model;

class AwardXp
{
    public function __construct(private EvaluateAchievements $evaluateAchievements) {}

    public function handle(User $user, XpEventType $type, ?Model $source = null, ?int $points = null): XpEvent
    {
        $points ??= (int) config('vibecraft.xp.'.$type->value, 0);

        $xpEvent = $user->xpEvents()->create([
            'type' => $type,
            'points' => $points,
            'source_id' => $source?->getKey(),
            'source_type' => $source?->getMorphClass(),
        ]);

        $user->increment('xp', $points);
        $user->refresh();

        $this->evaluateAchievements->handle($user);

        return $xpEvent;
    }
}
