<?php

namespace Database\Seeders;

use App\Models\Achievement;
use Illuminate\Database\Seeder;

class AchievementSeeder extends Seeder
{
    public function run(): void
    {
        $achievements = [
            [
                'name' => 'First Exhibit',
                'slug' => 'first-exhibit',
                'description' => 'Publish your first portfolio piece.',
                'icon' => 'frame',
                'xp_reward' => 25,
                'criteria_type' => 'portfolio_count',
                'criteria_value' => 1,
            ],
            [
                'name' => 'Gallery Regular',
                'slug' => 'gallery-regular',
                'description' => 'Publish five pieces.',
                'icon' => 'gallery',
                'xp_reward' => 50,
                'criteria_type' => 'portfolio_count',
                'criteria_value' => 5,
            ],
            [
                'name' => 'Crowd Favorite',
                'slug' => 'crowd-favorite',
                'description' => 'Collect 10 likes on your work.',
                'icon' => 'heart',
                'xp_reward' => 40,
                'criteria_type' => 'likes_received',
                'criteria_value' => 10,
            ],
            [
                'name' => 'Campus Known',
                'slug' => 'campus-known',
                'description' => 'Gain 5 followers.',
                'icon' => 'users',
                'xp_reward' => 40,
                'criteria_type' => 'followers_count',
                'criteria_value' => 5,
            ],
            [
                'name' => 'Rising Talent',
                'slug' => 'rising-talent',
                'description' => 'Reach 100 XP.',
                'icon' => 'spark',
                'xp_reward' => 30,
                'criteria_type' => 'xp_total',
                'criteria_value' => 100,
            ],
            [
                'name' => 'Show Up',
                'slug' => 'show-up',
                'description' => 'RSVP to your first event.',
                'icon' => 'ticket',
                'xp_reward' => 20,
                'criteria_type' => 'events_rsvped',
                'criteria_value' => 1,
            ],
        ];

        foreach ($achievements as $achievement) {
            Achievement::query()->updateOrCreate(
                ['slug' => $achievement['slug']],
                $achievement,
            );
        }
    }
}
