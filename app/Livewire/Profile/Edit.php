<?php

namespace App\Livewire\Profile;

use App\Models\Talent;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts::app')]
#[Title('Edit profile')]
class Edit extends Component
{
    use WithFileUploads;

    public mixed $avatar = null;

    public ?string $currentAvatarUrl = null;

    public string $name = '';

    public string $headline = '';

    public string $bio = '';

    public string $faculty = '';

    public string $department = '';

    public string $batch = '';

    public string $program = '';

    public string $profile_type = 'General Student Account';

    public ?int $primary_talent_id = null;

    public string $birthday = '';

    public string $location = '';

    /**
     * @var list<int>
     */
    public array $talent_ids = [];

    public function mount(): void
    {
        $user = auth()->user()->load(['profile.talents']);
        $profile = $user->profile;

        $this->authorize('update', $profile);

        $this->name = $user->name;
        $this->currentAvatarUrl = $user->avatarUrl();
        $this->headline = $profile->headline ?? '';
        $this->bio = $profile->bio ?? '';
        $this->faculty = $profile->faculty ?? '';
        $this->department = $profile->department ?? '';
        $this->batch = $profile->batch ?? '';
        $this->program = $profile->program ?? '';
        $this->profile_type = $profile->profile_type ?? 'General Student Account';
        $this->primary_talent_id = $profile->primary_talent_id;
        $this->birthday = $profile->birthday?->format('Y-m-d') ?? '';
        $this->location = $profile->location ?? '';
        $this->talent_ids = $profile->talents->pluck('id')->all();
    }

    public function removeAvatar(): void
    {
        $user = auth()->user();
        if ($user->profile?->avatar_path) {
            Storage::disk('public')->delete($user->profile->avatar_path);
            $user->profile->update(['avatar_path' => null]);
            $this->currentAvatarUrl = null;
            $this->avatar = null;
        }
    }

    public function save(): void
    {
        $this->authorize('update', auth()->user()->profile);

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'avatar' => ['nullable', 'image', 'max:10240', 'mimes:jpg,jpeg,png,webp'],
            'headline' => ['nullable', 'string', 'max:160'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'faculty' => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'batch' => ['nullable', 'string', 'max:100'],
            'program' => ['nullable', 'string', 'max:255'],
            'profile_type' => ['required', 'string', 'max:255'],
            'primary_talent_id' => ['nullable', 'integer', 'exists:talents,id'],
            'birthday' => ['nullable', 'date', 'before:today', 'after:1900-01-01'],
            'location' => ['nullable', 'string', 'max:255'],
            'talent_ids' => ['array', 'max:12'],
            'talent_ids.*' => ['integer', 'exists:talents,id'],
        ]);

        $user = auth()->user();
        $user->update(['name' => $validated['name']]);

        $profileData = [
            'headline' => $validated['headline'] ?: null,
            'bio' => $validated['bio'] ?: null,
            'faculty' => $validated['faculty'] ?: null,
            'department' => $validated['department'] ?: null,
            'batch' => $validated['batch'] ?: null,
            'program' => $validated['program'] ?: null,
            'profile_type' => $validated['profile_type'],
            'primary_talent_id' => $validated['primary_talent_id'],
            'birthday' => $validated['birthday'] ?: null,
            'location' => $validated['location'] ?: null,
        ];

        if ($this->avatar) {
            if ($user->profile->avatar_path) {
                Storage::disk('public')->delete($user->profile->avatar_path);
            }
            $profileData['avatar_path'] = $this->avatar->store('avatars', 'public');
        }

        $user->profile->update($profileData);

        $sync = collect($validated['talent_ids'] ?? [])
            ->values()
            ->mapWithKeys(fn (int $talentId, int $index): array => [
                $talentId => ['is_favorite' => $index === 0 || $talentId === $validated['primary_talent_id']],
            ])
            ->all();

        $user->profile->talents()->sync($sync);

        $this->redirect(route('students.show', $user), navigate: true);
    }

    public function render(): View
    {
        $campusId = auth()->user()->campus_id;

        return view('livewire.profile.edit', [
            'talentCategories' => Talent::query()->forCampus($campusId)->orderBy('category')->orderBy('name')->get()->groupBy('category'),
            'talents' => Talent::query()->forCampus($campusId)->orderBy('name')->get(),
        ]);
    }
}
