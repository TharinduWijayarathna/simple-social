<?php

namespace App\Livewire\Events;

use App\Models\Talent;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::app')]
#[Title('Create event')]
class Create extends Component
{
    public string $title = '';

    public string $description = '';

    public ?int $talent_id = null;

    public string $location = '';

    public string $starts_at = '';

    public string $ends_at = '';

    public bool $is_published = true;

    public function mount(): void
    {
        abort_unless(auth()->user()->canOrganizeEvents(), 403);
    }

    public function save(): void
    {
        $validated = $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'talent_id' => ['nullable', 'integer', 'exists:talents,id'],
            'location' => ['nullable', 'string', 'max:255'],
            'starts_at' => ['required', 'date', 'after:now'],
            'ends_at' => ['nullable', 'date', 'after:starts_at'],
            'is_published' => ['boolean'],
        ]);

        $event = auth()->user()->organizedEvents()->create($validated);

        $this->redirect(route('events.show', $event), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.events.create', [
            'talents' => Talent::query()->orderBy('name')->get(),
        ]);
    }
}
