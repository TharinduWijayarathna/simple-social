<?php

namespace Database\Seeders;

use App\Actions\RecomputeRankings;
use App\Enums\CollaborationStatus;
use App\Enums\ExperienceLevel;
use App\Enums\PortfolioMediaType;
use App\Enums\UserStatus;
use App\Enums\XpEventType;
use App\Models\CampusRanking;
use App\Models\Collaboration;
use App\Models\Comment;
use App\Models\Event;
use App\Models\Follow;
use App\Models\Like;
use App\Models\PortfolioItem;
use App\Models\Status;
use App\Models\Talent;
use App\Models\User;
use App\Models\XpEvent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoCampusSeeder extends Seeder
{
    private const PASSWORD = 'password';

    /**
     * @var list<array{name: string, email: string, role: string, campus: string, password: string}>
     */
    private array $credentials = [];

    public function run(): void
    {
        $talents = Talent::query()->orderBy('id')->get();

        if ($talents->isEmpty()) {
            $this->command?->error('No talents found. Run TalentSeeder first.');

            return;
        }

        $admin = $this->createSuperAdmin();
        $campuses = $this->createCampuses();

        /** @var Collection<int, User> $allStudents */
        $allStudents = collect();

        foreach ($campuses as $campus) {
            $students = $this->createStudentsForCampus(
                campus: $campus['admin'],
                campusKey: $campus['key'],
                campusLabel: $campus['name'],
                count: $campus['student_count'],
                talents: $talents,
            );

            $allStudents = $allStudents->merge($students);
            $this->seedCampusEvents($campus['admin'], $talents, $campus['name']);
            $this->seedCampusRankings($campus['admin'], $students, $campus['name']);
        }

        $this->seedSocialGraph($allStudents);
        $this->seedCollaborations($allStudents, $talents);
        $this->awardXpAndRank($allStudents);

        $this->writeCredentialsMarkdown($admin, $campuses, $allStudents);

        $this->command?->info('Demo data ready: 3 campuses, '.$allStudents->count().' students.');
        $this->command?->info('Credentials written to DEMO_ACCOUNTS.md');
    }

    private function createSuperAdmin(): User
    {
        $admin = User::factory()->superAdmin()->create([
            'name' => 'Super Admin',
            'email' => 'admin@vibecraft.test',
            'password' => Hash::make(self::PASSWORD),
            'status' => UserStatus::Approved,
            'xp' => 0,
        ]);

        $admin->profile->update([
            'headline' => 'Platform steward for Sri Lankan campuses',
            'bio' => 'Approves campus admins and keeps VibeCraft fair across ICBT, NSBM, and SLIIT.',
            'faculty' => 'Student Affairs',
            'location' => 'Colombo',
            'experience_level' => ExperienceLevel::Advanced,
            'avatar_path' => $this->unsplash('photo-1560250097-0b93528c311a', 400, 400),
        ]);

        $this->credentials[] = [
            'name' => $admin->name,
            'email' => $admin->email,
            'role' => 'Super Admin',
            'campus' => '—',
            'password' => self::PASSWORD,
        ];

        return $admin;
    }

    /**
     * @return list<array{key: string, name: string, student_count: int, admin: User}>
     */
    private function createCampuses(): array
    {
        $definitions = [
            [
                'key' => 'icbt',
                'name' => 'ICBT',
                'student_count' => 20,
                'admin_name' => 'Chamari Wickramasinghe',
                'admin_email' => 'campus.icbt@vibecraft.test',
                'location' => 'Colombo 04',
                'headline' => 'ICBT campus desk — open mics & gallery nights',
            ],
            [
                'key' => 'nsbm',
                'name' => 'NSBM',
                'student_count' => 10,
                'admin_name' => 'Ruwan Jayawardena',
                'admin_email' => 'campus.nsbm@vibecraft.test',
                'location' => 'Homagama',
                'headline' => 'NSBM green campus talent desk',
            ],
            [
                'key' => 'sliit',
                'name' => 'SLIIT',
                'student_count' => 10,
                'admin_name' => 'Nadeesha Fernando',
                'admin_email' => 'campus.sliit@vibecraft.test',
                'location' => 'Malabe',
                'headline' => 'SLIIT creative & tech talent desk',
            ],
        ];

        $campuses = [];

        foreach ($definitions as $index => $definition) {
            $admin = User::factory()->campusAdmin()->create([
                'name' => $definition['admin_name'],
                'email' => $definition['admin_email'],
                'password' => Hash::make(self::PASSWORD),
                'status' => UserStatus::Approved,
                'university_id' => strtoupper($definition['key']).'-ADMIN',
            ]);

            $admin->profile->update([
                'headline' => $definition['headline'],
                'bio' => 'Campus admin for '.$definition['name'].'. Approves students and publishes campus events.',
                'faculty' => 'Student Affairs',
                'location' => $definition['location'],
                'experience_level' => ExperienceLevel::Advanced,
                'avatar_path' => $this->avatarUrl($index + 10),
            ]);

            $this->credentials[] = [
                'name' => $admin->name,
                'email' => $admin->email,
                'role' => 'Campus Admin',
                'campus' => $definition['name'],
                'password' => self::PASSWORD,
            ];

            $campuses[] = [
                'key' => $definition['key'],
                'name' => $definition['name'],
                'student_count' => $definition['student_count'],
                'admin' => $admin,
            ];
        }

        return $campuses;
    }

    /**
     * @param  Collection<int, Talent>  $talents
     * @return Collection<int, User>
     */
    private function createStudentsForCampus(
        User $campus,
        string $campusKey,
        string $campusLabel,
        int $count,
        Collection $talents,
    ): Collection {
        $names = $this->sriLankanNames();
        $students = collect();

        for ($i = 0; $i < $count; $i++) {
            $name = $names[($campusKey === 'icbt' ? $i : ($campusKey === 'nsbm' ? $i + 20 : $i + 30)) % count($names)];
            $emailLocal = Str::of($name)
                ->lower()
                ->replace(' ', '.')
                ->ascii()
                ->toString();

            $student = User::factory()->student()->create([
                'name' => $name,
                'email' => $emailLocal.'@'.$campusKey.'.vibecraft.test',
                'password' => Hash::make(self::PASSWORD),
                'status' => UserStatus::Approved,
                'campus_id' => $campus->id,
                'university_id' => strtoupper($campusKey).'-'.str_pad((string) ($i + 1), 3, '0', STR_PAD_LEFT),
                'xp' => 0,
            ]);

            $primaryPool = $this->popularTalents($talents);
            $primary = $primaryPool[$i % $primaryPool->count()];
            $extras = $primaryPool->where('id', '!=', $primary->id)->values();
            if ($extras->count() < 2) {
                $extras = $extras->merge(
                    $talents->where('id', '!=', $primary->id)->values()->shuffle()->take(2 - $extras->count())
                );
            }

            $student->profile->update([
                'headline' => $this->headlineFor($primary->name, $campusLabel),
                'bio' => $this->bioFor($name, $campusLabel, $primary->name),
                'faculty' => fake()->randomElement(['Arts', 'Design', 'Media', 'Engineering', 'Business', 'Computing', 'Science']),
                'department' => fake()->randomElement(['Undergraduate', 'Foundation', 'Postgraduate']),
                'batch' => fake()->randomElement(['2023', '2024', '2025']),
                'program' => fake()->randomElement(['BSc', 'BA', 'HND', 'Diploma']),
                'birthday' => fake()->dateTimeBetween('-25 years', '-19 years')->format('Y-m-d'),
                'location' => fake()->randomElement(['Colombo', 'Kandy', 'Galle', 'Negombo', 'Kurunegala', 'Matara', 'Jaffna', 'Gampaha']),
                'experience_level' => fake()->randomElement([
                    ExperienceLevel::Beginner,
                    ExperienceLevel::Intermediate,
                    ExperienceLevel::Advanced,
                ]),
                'primary_talent_id' => $primary->id,
                'avatar_path' => $this->avatarUrl($i + ($campusKey === 'icbt' ? 0 : ($campusKey === 'nsbm' ? 40 : 60))),
            ]);

            $sync = collect([$primary, ...$extras])->unique('id')->values()->mapWithKeys(
                fn (Talent $talent, int $talentIndex): array => [
                    $talent->id => ['is_favorite' => $talentIndex === 0],
                ],
            )->all();

            $student->profile->talents()->sync($sync);

            // Primary talent posts
            $portfolioCount = fake()->numberBetween(2, 3);
            for ($p = 0; $p < $portfolioCount; $p++) {
                $this->createPortfolioItem($student, $primary, $p);
            }

            // Extra posts across ranking talents so every board can fill Top 10
            foreach ($primaryPool->where('id', '!=', $primary->id)->values() as $extraIndex => $extraTalent) {
                $this->createPortfolioItem($student, $extraTalent, 10 + $extraIndex);
            }

            $this->createStory($student, $primary, $i + ($campusKey === 'icbt' ? 0 : ($campusKey === 'nsbm' ? 40 : 60)));

            $this->credentials[] = [
                'name' => $student->name,
                'email' => $student->email,
                'role' => 'Student',
                'campus' => $campusLabel,
                'password' => self::PASSWORD,
            ];

            $students->push($student);
        }

        return $students;
    }

    private function createPortfolioItem(User $student, Talent $talent, int $index): PortfolioItem
    {
        $image = $this->portfolioImage($talent->category ?? 'Creative & Visual Arts', $student->id * 10 + $index);

        return PortfolioItem::query()->create([
            'user_id' => $student->id,
            'talent_id' => $talent->id,
            'title' => $this->portfolioTitle($talent->name, $index),
            'description' => 'A '.$talent->name.' piece shared from campus — shot and edited for VibeCraft.',
            'media_type' => PortfolioMediaType::Image,
            'file_path' => $image,
            'thumbnail_path' => $image,
            'mime_type' => 'image/jpeg',
            'file_size' => fake()->numberBetween(120_000, 1_800_000),
            'published_at' => now()->subDays(fake()->numberBetween(0, 40)),
        ]);
    }

    private function createStory(User $student, Talent $talent, int $index): Status
    {
        return Status::query()->create([
            'user_id' => $student->id,
            'caption' => fake()->randomElement([
                $talent->name.' vibes today',
                'Campus life · '.$talent->name,
                'Behind the scenes',
                'Practice session',
                'Late night studio',
                'Ready for open mic',
            ]),
            'image_path' => $this->storyImage($index),
            'media_type' => 'image',
            'expires_at' => now()->addHours(fake()->numberBetween(8, 48)),
        ]);
    }

    /**
     * @param  Collection<int, Talent>  $talents
     */
    private function seedCampusEvents(User $campusAdmin, Collection $talents, string $campusName): void
    {
        $events = [
            [
                'title' => $campusName.' Open Mic Night',
                'description' => 'Sing, spit verses, or bring your band. Sign up at the campus desk.',
                'location' => $campusName.' Main Hall',
            ],
            [
                'title' => $campusName.' Art Walk',
                'description' => 'Gallery night for painters, photographers, and digital artists.',
                'location' => $campusName.' Gallery',
            ],
        ];

        foreach ($events as $index => $event) {
            $startsAt = now()->addDays(7 + $index * 5)->setTime(18, 0);

            Event::query()->create([
                'organizer_id' => $campusAdmin->id,
                'talent_id' => $talents[$index % $talents->count()]->id,
                'title' => $event['title'],
                'description' => $event['description'],
                'location' => $event['location'],
                'cover_image' => $this->unsplash(
                    $index === 0 ? 'photo-1470229722913-7c0e2dbbafd3' : 'photo-1460661419201-fd4cecdf8a8b',
                    1200,
                    630,
                ),
                'starts_at' => $startsAt,
                'ends_at' => $startsAt->copy()->addHours(3),
                'application_deadline' => $startsAt->copy()->subDay(),
                'capacity' => 80,
                'is_published' => true,
            ]);
        }
    }

    /**
     * Prefer a smaller talent set so campus rankings have multiple competitors.
     *
     * @param  Collection<int, Talent>  $talents
     * @return Collection<int, Talent>
     */
    private function popularTalents(Collection $talents): Collection
    {
        // Two dense boards so each campus ranking can show a full Top 10.
        $preferred = [
            'Photography',
            'Cricket',
        ];

        $pool = $talents->whereIn('name', $preferred)->values();

        return $pool->isNotEmpty() ? $pool : $talents->take(2)->values();
    }

    /**
     * @param  Collection<int, User>  $campusStudents
     */
    private function seedCampusRankings(User $campusAdmin, Collection $campusStudents, string $campusName): void
    {
        $primaryTalentIds = $campusStudents
            ->map(fn (User $student): ?int => $student->profile?->primary_talent_id)
            ->filter()
            ->unique()
            ->values();

        $talents = Talent::query()->whereIn('id', $primaryTalentIds)->get()->keyBy('id');

        foreach ($primaryTalentIds as $talentId) {
            $talent = $talents->get($talentId);

            if ($talent === null) {
                continue;
            }

            CampusRanking::query()->updateOrCreate(
                [
                    'campus_id' => $campusAdmin->id,
                    'talent_id' => $talent->id,
                ],
                [
                    'title' => $campusName.' '.$talent->name.' Rankings',
                    'is_active' => true,
                ],
            );
        }
    }

    /**
     * @param  Collection<int, User>  $students
     */
    private function seedSocialGraph(Collection $students): void
    {
        $students->groupBy('campus_id')->each(function (Collection $campusStudents): void {
            $items = PortfolioItem::query()
                ->whereIn('user_id', $campusStudents->pluck('id'))
                ->get();

            foreach ($campusStudents as $student) {
                $others = $campusStudents->where('id', '!=', $student->id)->values();

                if ($others->isEmpty()) {
                    continue;
                }

                $followTargets = $others->shuffle()->take(min($others->count(), fake()->numberBetween(4, min(12, $others->count()))));
                foreach ($followTargets as $target) {
                    Follow::query()->firstOrCreate([
                        'follower_id' => $student->id,
                        'following_id' => $target->id,
                    ]);
                }

                $campusItems = $items->where('user_id', '!=', $student->id)->values();

                if ($campusItems->isEmpty()) {
                    continue;
                }

                // Like most campus posts so rankings have real scores (weighted so some creators rise).
                foreach ($campusItems as $item) {
                    $ownerBoost = $campusStudents->search(fn (User $peer): bool => $peer->id === $item->user_id);
                    $likeChance = 55 + (($ownerBoost === false ? 0 : (int) $ownerBoost) % 7) * 5;

                    if (! fake()->boolean(min(95, $likeChance))) {
                        continue;
                    }

                    Like::query()->firstOrCreate([
                        'user_id' => $student->id,
                        'likeable_id' => $item->id,
                        'likeable_type' => (new PortfolioItem)->getMorphClass(),
                    ]);
                }

                $commentTargets = $campusItems->shuffle()->take(min($campusItems->count(), fake()->numberBetween(2, 5)));
                foreach ($commentTargets as $item) {
                    Comment::query()->create([
                        'user_id' => $student->id,
                        'commentable_id' => $item->id,
                        'commentable_type' => (new PortfolioItem)->getMorphClass(),
                        'body' => fake()->randomElement([
                            'This is fire!',
                            'Love the composition.',
                            'Campus pride right here.',
                            'Need a collab soon.',
                            'So clean — respect.',
                            'Big vibes from this one.',
                        ]),
                    ]);
                }
            }
        });
    }

    /**
     * @param  Collection<int, User>  $students
     * @param  Collection<int, Talent>  $talents
     */
    private function seedCollaborations(Collection $students, Collection $talents): void
    {
        $students->groupBy('campus_id')->each(function (Collection $campusStudents, int $index) use ($talents): void {
            $owner = $campusStudents->first();

            if ($owner === null) {
                return;
            }

            Collaboration::query()->create([
                'owner_id' => $owner->id,
                'talent_id' => $talents[$index % $talents->count()]->id,
                'title' => fake()->randomElement([
                    'Short film night crew',
                    'Acoustic collab session',
                    'Campus mural project',
                    'Dance showcase squad',
                    'Photo walk collective',
                ]),
                'description' => 'Looking for creative students from our campus to ship something memorable.',
                'status' => CollaborationStatus::Open,
            ]);
        });
    }

    /**
     * @param  Collection<int, User>  $students
     */
    private function awardXpAndRank(Collection $students): void
    {
        foreach ($students as $student) {
            $portfolioCount = $student->portfolioItems()->count();
            $likesReceived = Like::query()
                ->where('likeable_type', (new PortfolioItem)->getMorphClass())
                ->whereIn('likeable_id', $student->portfolioItems()->pluck('id'))
                ->count();
            $followers = Follow::query()->where('following_id', $student->id)->count();
            $commentsReceived = Comment::query()
                ->where('commentable_type', (new PortfolioItem)->getMorphClass())
                ->whereIn('commentable_id', $student->portfolioItems()->pluck('id'))
                ->count();

            $xp = ($portfolioCount * 25) + ($likesReceived * 5) + ($followers * 10) + ($commentsReceived * 8);

            if ($portfolioCount > 0) {
                XpEvent::query()->create([
                    'user_id' => $student->id,
                    'type' => XpEventType::PortfolioPublished,
                    'points' => $portfolioCount * 25,
                ]);
            }

            if ($likesReceived > 0) {
                XpEvent::query()->create([
                    'user_id' => $student->id,
                    'type' => XpEventType::LikeReceived,
                    'points' => $likesReceived * 5,
                ]);
            }

            if ($followers > 0) {
                XpEvent::query()->create([
                    'user_id' => $student->id,
                    'type' => XpEventType::FollowReceived,
                    'points' => $followers * 10,
                ]);
            }

            $student->forceFill(['xp' => $xp])->save();
        }

        app(RecomputeRankings::class)->handle();
    }

    /**
     * @param  list<array{key: string, name: string, student_count: int, admin: User}>  $campuses
     * @param  Collection<int, User>  $students
     */
    private function writeCredentialsMarkdown(User $admin, array $campuses, Collection $students): void
    {
        $lines = [
            '# VibeCraft Demo Accounts',
            '',
            'Generated by `DemoCampusSeeder`. All demo passwords are **`password`**.',
            '',
            'After seeding run:',
            '',
            '```bash',
            'php artisan migrate:fresh --seed',
            '```',
            '',
            '## Super Admin',
            '',
            '| Name | Email | Password | Portal |',
            '| --- | --- | --- | --- |',
            "| {$admin->name} | `{$admin->email}` | `password` | `/admin/login` |",
            '',
            '## Campus Admins',
            '',
            '| Campus | Name | Email | Password | Portal |',
            '| --- | --- | --- | --- | --- |',
        ];

        foreach ($campuses as $campus) {
            $a = $campus['admin'];
            $lines[] = "| {$campus['name']} | {$a->name} | `{$a->email}` | `password` | `/login` → campus desk |";
        }

        $lines[] = '';
        $lines[] = '## Students by campus';
        $lines[] = '';
        $lines[] = 'Total: **'.$students->count().'** students (ICBT 20 · NSBM 10 · SLIIT 10).';
        $lines[] = '';

        foreach ($campuses as $campus) {
            $campusStudents = $students->where('campus_id', $campus['admin']->id)->values();
            $lines[] = '### '.$campus['name'].' ('.$campusStudents->count().')';
            $lines[] = '';
            $lines[] = '| Name | Email | University ID | Password |';
            $lines[] = '| --- | --- | --- | --- |';

            foreach ($campusStudents as $student) {
                $lines[] = "| {$student->name} | `{$student->email}` | `{$student->university_id}` | `password` |";
            }

            $lines[] = '';
        }

        $lines[] = '## Quick login tips';
        $lines[] = '';
        $lines[] = '- Student / campus admin: http://127.0.0.1:8000/login';
        $lines[] = '- Super admin: http://127.0.0.1:8000/admin/login';
        $lines[] = '- Demo images use [Unsplash](https://unsplash.com) URLs (avatars, portfolio, stories, event covers).';
        $lines[] = '';

        File::put(base_path('DEMO_ACCOUNTS.md'), implode("\n", $lines));
    }

    /**
     * @return list<string>
     */
    private function sriLankanNames(): array
    {
        return [
            'Kasun Perera',
            'Nimali Silva',
            'Tharindu Fernando',
            'Sachini Jayawardena',
            'Nuwan Bandara',
            'Thilini Wickramasinghe',
            'Chamath Rathnayake',
            'Madhavi Gunasekara',
            'Dilshan Dissanayake',
            'Chathurika Wijesinghe',
            'Isuru Herath',
            'Ishara Senanayake',
            'Shehan Karunaratne',
            'Dinithi Abeysekera',
            'Amila Weerasinghe',
            'Harshani Pathirana',
            'Ruwan Amarasinghe',
            'Sewwandi Liyanage',
            'Sandun Rajapaksa',
            'Kavindi Samarasinghe',
            'Kavindu Ekanayake',
            'Nilushi Fonseka',
            'Malith Cooray',
            'Amaya Ranasinghe',
            'Asanka Mendis',
            'Yasara de Silva',
            'Duminda Jayasuriya',
            'Anushka Gamage',
            'Hasitha Nanayakkara',
            'Sanduni Alwis',
            'Praveen Kulasekara',
            'Nethmi Rodrigo',
            'Lakshan Pieris',
            'Hiruni Dias',
            'Buddika Seneviratne',
            'Piumi Chandrasekara',
            'Roshan Athukorala',
            'Shanika Bogahawatta',
            'Gayan Hettiarachchi',
            'Imasha Kaluarachchi',
            'Sanjaya Wijeratne',
            'Tharushi Maduranga',
            'Viraj Gunawardana',
            'Nadeesha Kumari',
            'Chaminda Basnayake',
            'Ayomi Tennakoon',
            'Suresh Ranatunga',
            'Melani Hapuarachchi',
            'Janith Withanage',
            'Oshadi Meegoda',
        ];
    }

    private function headlineFor(string $talent, string $campus): string
    {
        return fake()->randomElement([
            "{$talent} creator at {$campus}",
            "Building a {$talent} portfolio on campus",
            "{$campus} · {$talent}",
            "From practice room to feed — {$talent}",
        ]);
    }

    private function bioFor(string $name, string $campus, string $talent): string
    {
        return "{$name} studies at {$campus} and shares {$talent} work on VibeCraft. Always down for campus collabs and open calls.";
    }

    private function portfolioTitle(string $talent, int $index): string
    {
        $suffixes = ['Study', 'Session', 'Sketch', 'Cut', 'Frame', 'Draft', 'Live take', 'Studio piece'];

        return $talent.' · '.$suffixes[$index % count($suffixes)];
    }

    private function avatarUrl(int $seed): string
    {
        $photos = [
            'photo-1507003211169-0a1dd7228f2d',
            'photo-1494790108377-be9c29b29330',
            'photo-1500648767791-00dcc994a43e',
            'photo-1438761681033-6461ffad8d80',
            'photo-1472099645785-5658abf4ff4e',
            'photo-1544005313-94ddf0286df2',
            'photo-1534528741775-53994a69daeb',
            'photo-1506794778202-cad84cf45f1d',
            'photo-1539571696357-5a69c17a67c6',
            'photo-1517841905240-472988babdf9',
            'photo-1524504388940-b1c1722653e1',
            'photo-1529626455594-4ff0802cfb7e',
            'photo-1531123897727-8f129e1688ce',
            'photo-1504257432389-52343af06d0e',
            'photo-1519345182560-3f2917c472ef',
            'photo-1488426862026-3ee34a7d66df',
            'photo-1463453091185-61582044d556',
            'photo-1492562080023-ab3db95bfbce',
            'photo-1507591064344-4c6ce005b128',
            'photo-1487412720507-e7ab37603c6f',
        ];

        return $this->unsplash($photos[$seed % count($photos)], 400, 400);
    }

    private function portfolioImage(string $category, int $seed): string
    {
        $byCategory = [
            'Performing Arts' => [
                'photo-1511671782779-c97d3d27a1d4',
                'photo-1493225457124-a3eb161ffa5f',
                'photo-1514320291840-2e0a9bf2a9ae',
                'photo-1508700115892-45ecd05ae2ad',
            ],
            'Creative & Visual Arts' => [
                'photo-1460661419201-fd4cecdf8a8b',
                'photo-1513364776144-60967b0f800f',
                'photo-1452860606245-08befc0ff44b',
                'photo-1541961017774-22349e4a1262',
            ],
            'Sports & Physical' => [
                'photo-1461896836934-ffe607ba6851',
                'photo-1579952363873-27f3bade9f55',
                'photo-1552674605-db6ffd4facb5',
                'photo-1517649763962-0c623066013b',
            ],
            'Unique & Hidden' => [
                'photo-1556910103-1c02745aae4d',
                'photo-1517248135467-4c7edcad34c4',
                'photo-1504674900247-0877df9cc836',
                'photo-1414235077428-338989a2e8c0',
            ],
            'General User' => [
                'photo-1522202176988-66273c2fd55f',
                'photo-1523240795612-9a054b0db644',
                'photo-1517486808906-6ca8b3f04846',
                'photo-1523050854058-8df90110c9f1',
            ],
        ];

        $photos = $byCategory[$category] ?? $byCategory['Creative & Visual Arts'];

        return $this->unsplash($photos[$seed % count($photos)], 900, 1100);
    }

    private function storyImage(int $seed): string
    {
        // Stable Unsplash campus/lifestyle shots (with UI onerror → picsum fallback).
        $photos = [
            'photo-1523050854058-8df90110c9f1',
            'photo-1541339907198-e816be447fa8',
            'photo-1522202176988-66273c2fd55f',
            'photo-1517486808906-6ca8b3f04846',
            'photo-1523240795612-9a054b0db644',
            'photo-1475721027785-f74eccf877e2',
            'photo-1500530855697-b586d89ba3ee',
            'photo-1498243691581-b145c3f54a5a',
            'photo-1519389950473-47ba0277781c',
            'photo-1523580494863-6f3031224fd3',
            'photo-1524178232363-1fb2b075b655',
            'photo-1511632765486-a01980e36a16',
        ];

        return $this->unsplash($photos[$seed % count($photos)], 720, 1280);
    }

    private function unsplash(string $photoId, int $width, int $height): string
    {
        return "https://images.unsplash.com/{$photoId}?auto=format&fit=crop&w={$width}&h={$height}&q=80";
    }
}
