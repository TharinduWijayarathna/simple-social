<?php

namespace App\Livewire\Profile;

use App\Enums\ExperienceLevel;
use App\Models\Talent;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::app')]
#[Title('Edit profile')]
class Edit extends Component
{
    public string $name = '';

    public string $headline = '';

    public string $bio = '';

    public string $faculty = '';

    public string $department = '';

    public string $experience_level = 'beginner';

    /**
     * @var list<int>
     */
    public array $talent_ids = [];

    /**
     * @var list<int>
     */
    public array $favorite_talent_ids = [];

    public function mount(): void
    {
        $user = auth()->user()->load(['profile.talents']);
        $profile = $user->profile;

        $this->name = $user->name;
        $this->headline = $profile->headline ?? '';
        $this->bio = $profile->bio ?? '';
        $this->faculty = $profile->faculty ?? '';
        $this->department = $profile->department ?? '';
        $this->experience_level = $profile->experience_level->value;
        $this->talent_ids = $profile->talents->pluck('id')->all();
        $this->favorite_talent_ids = $profile->talents
            ->filter(fn ($talent): bool => (bool) $talent->pivot->is_favorite)
            ->pluck('id')
            ->all();
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'headline' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'faculty' => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'experience_level' => ['required', Rule::enum(ExperienceLevel::class)],
            'talent_ids' => ['array', 'max:12'],
            'talent_ids.*' => ['integer', 'exists:talents,id'],
            'favorite_talent_ids' => ['array', 'max:'.config('vibecraft.wearable.favorite_talent_limit')],
            'favorite_talent_ids.*' => ['integer', 'exists:talents,id'],
        ]);

        $user = auth()->user();
        $user->update(['name' => $validated['name']]);

        $user->profile->update([
            'headline' => $validated['headline'] ?: null,
            'bio' => $validated['bio'] ?: null,
            'faculty' => $validated['faculty'] ?: null,
            'department' => $validated['department'] ?: null,
            'experience_level' => $validated['experience_level'],
        ]);

        $sync = collect($validated['talent_ids'] ?? [])
            ->mapWithKeys(fn (int $talentId): array => [
                $talentId => ['is_favorite' => in_array($talentId, $validated['favorite_talent_ids'] ?? [], true)],
            ])
            ->all();

        $user->profile->talents()->sync($sync);

        session()->flash('status', 'Profile updated.');
    }

    public function render(): View
    {
        return view('livewire.profile.edit', [
            'talents' => Talent::query()->orderBy('name')->get(),
        ]);
    }
}
