<?php

namespace Database\Seeders;

use App\Models\Collaboration;
use App\Models\Event;
use App\Models\PortfolioItem;
use App\Models\Talent;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            TalentSeeder::class,
            AchievementSeeder::class,
        ]);

        $admin = User::factory()->superAdmin()->create([
            'name' => 'Super Admin',
            'email' => 'admin@vibecraft.test',
        ]);
        $admin->profile->update([
            'headline' => 'Keeping the campus gallery honest',
            'faculty' => 'Student Affairs',
        ]);

        $campusAdmin = User::factory()->campusAdmin()->create([
            'name' => 'Campus Admin',
            'email' => 'organizer@vibecraft.test',
        ]);
        $campusAdmin->profile->update([
            'headline' => 'Nights, stages, open calls',
            'faculty' => 'Arts',
        ]);

        $talents = Talent::query()->orderBy('id')->get();

        $students = User::factory()->student()->count(8)->create();

        $students->each(function (User $student, int $index) use ($talents): void {
            $primary = $talents[$index % $talents->count()];
            $extra = $talents->where('id', '!=', $primary->id)->random(min(2, $talents->count() - 1));

            $student->profile->update([
                'headline' => fake()->sentence(6),
                'bio' => fake()->paragraph(),
                'faculty' => fake()->randomElement(['Arts', 'Design', 'Media', 'Engineering']),
                'birthday' => fake()->dateTimeBetween('-26 years', '-19 years')->format('Y-m-d'),
                'location' => fake()->city(),
                'experience_level' => 'intermediate',
            ]);

            $sync = collect([$primary, ...$extra])->values()->mapWithKeys(
                fn (Talent $talent, int $talentIndex): array => [
                    $talent->id => ['is_favorite' => $talentIndex === 0],
                ],
            )->all();
            $student->profile->talents()->sync($sync);

            PortfolioItem::factory()
                ->recycle($student)
                ->recycle($primary)
                ->count(4)
                ->create();
        });

        Event::factory()->recycle($campusAdmin)->recycle($talents->first())->count(3)->create();

        Collaboration::factory()->recycle($students->first())->recycle($talents->first())->create([
            'title' => 'Short film night crew',
        ]);
    }
}
