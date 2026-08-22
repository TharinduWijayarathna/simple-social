<?php

namespace Database\Seeders;

use App\Enums\TalentTheme;
use App\Models\Talent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TalentSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Performing Arts' => [
                'theme' => TalentTheme::Stage,
                'items' => [
                    'Singing',
                    'Dancing',
                    'Rap / Beatboxing',
                    'Playing Musical Instruments',
                    'Acting / Drama',
                    'Stand-up Comedy',
                    'Mimicry',
                    'Poetry / Spoken Word',
                    'Public Speaking',
                    'Storytelling',
                ],
            ],
            'Creative & Visual Arts' => [
                'theme' => TalentTheme::Gallery,
                'items' => [
                    'Drawing / Sketching',
                    'Painting',
                    'Photography',
                    'Videography',
                    'Graphic Design',
                    'Digital Art',
                    'Calligraphy / Lettering',
                    'Makeup / Face Painting',
                    'Fashion Designing',
                    'Crafting',
                ],
            ],
            'Sports & Physical' => [
                'theme' => TalentTheme::Grid,
                'items' => [
                    'Cricket',
                    'Football',
                    'Basketball',
                    'Volleyball',
                    'Badminton',
                    'Table Tennis',
                    'Athletics',
                    'Swimming',
                    'Martial Arts',
                    'Fitness / Yoga',
                ],
            ],
            'Unique & Hidden' => [
                'theme' => TalentTheme::Social,
                'items' => [
                    'Cooking / Baking',
                    'Magic',
                    'Origami',
                    'Speed Cubing',
                    'Chess',
                    'Car / Bike Knowledge',
                    'Gardening',
                    'Handcrafts',
                    'Fashion Styling',
                    'Nail Art',
                    'Hair Styling',
                ],
            ],
            'General User' => [
                'theme' => TalentTheme::Social,
                'items' => [
                    'General Creator',
                ],
            ],
        ];

        foreach ($categories as $categoryName => $group) {
            foreach ($group['items'] as $talentName) {
                Talent::query()->updateOrCreate(
                    ['slug' => Str::slug($talentName)],
                    [
                        'name' => $talentName,
                        'category' => $categoryName,
                        'description' => $talentName.' talent on campus.',
                        'theme' => $group['theme'],
                    ],
                );
            }
        }
    }
}
