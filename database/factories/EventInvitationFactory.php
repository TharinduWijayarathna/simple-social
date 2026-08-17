<?php

namespace Database\Factories;

use App\Enums\InvitationStatus;
use App\Models\Event;
use App\Models\EventInvitation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EventInvitation>
 */
class EventInvitationFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'user_id' => User::factory(),
            'invited_by' => User::factory()->organizer(),
            'status' => InvitationStatus::Pending,
        ];
    }
}
