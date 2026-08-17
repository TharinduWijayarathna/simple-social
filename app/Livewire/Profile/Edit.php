<?php

namespace App\Livewire\Profile;

use App\Models\Talent;
use Illuminate\Contracts\View\View;
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
        $this->headline = $profile->headline ?? '';
        $this->bio = $profile->bio ?? '';
        $this->faculty = $profile->faculty ?? '';
        $this->department = $profile->department ?? '';
        $this->birthday = $profile->birthday?->format('Y-m-d') ?? '';
        $this->location = $profile->location ?? '';
        $this->talent_ids = $profile->talents->pluck('id')->all();
    }

    public function save(): void
    {
        $this->authorize('update', auth()->user()->profile);

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'headline' => ['nullable', 'string', 'max:160'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'faculty' => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'birthday' => ['nullable', 'date', 'before:today', 'after:1900-01-01'],
            'location' => ['nullable', 'string', 'max:255'],
            'talent_ids' => ['array', 'max:12'],
            'talent_ids.*' => ['integer', 'exists:talents,id'],
        ]);

        $user = auth()->user();
        $user->update(['name' => $validated['name']]);

        $user->profile->update([
            'headline' => $validated['headline'] ?: null,
            'bio' => $validated['bio'] ?: null,
            'faculty' => $validated['faculty'] ?: null,
            'department' => $validated['department'] ?: null,
            'birthday' => $validated['birthday'] ?: null,
            'location' => $validated['location'] ?: null,
        ]);

        $sync = collect($validated['talent_ids'] ?? [])
            ->values()
            ->mapWithKeys(fn (int $talentId, int $index): array => [
                $talentId => ['is_favorite' => $index === 0],
            ])
            ->all();

        $user->profile->talents()->sync($sync);

        $this->redirect(route('students.show', $user), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.profile.edit', [
            'talents' => Talent::query()->orderBy('name')->get(),
        ]);
    }
}
