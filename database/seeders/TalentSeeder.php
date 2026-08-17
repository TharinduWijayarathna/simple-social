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
        $talents = [
            ['name' => 'Visual Arts', 'theme' => TalentTheme::Gallery],
            ['name' => 'Photography & Videography', 'theme' => TalentTheme::Darkroom],
            ['name' => 'Music & Audio Production', 'theme' => TalentTheme::Vinyl],
            ['name' => 'Performing Arts', 'theme' => TalentTheme::Stage],
            ['name' => 'Digital Design', 'theme' => TalentTheme::Grid],
            ['name' => 'Content Creation', 'theme' => TalentTheme::Social],
            ['name' => 'Film & Video Production', 'theme' => TalentTheme::Cinema],
            ['name' => 'Fashion & Styling', 'theme' => TalentTheme::Editorial],
        ];

        foreach ($talents as $talent) {
            Talent::query()->updateOrCreate(
                ['slug' => Str::slug($talent['name'])],
                [
                    'name' => $talent['name'],
                    'description' => $talent['name'].' on campus.',
                    'theme' => $talent['theme'],
                ],
            );
        }
    }
}
