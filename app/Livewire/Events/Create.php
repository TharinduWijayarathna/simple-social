<?php

namespace App\Livewire\Events;

use App\Models\Talent;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::app')]
#[Title('Create Event')]
class Create extends Component
{
    public string $title = '';

    public string $event_type = 'Showcase';

    public string $description = '';

    public string $requirements = '';

    public ?int $talent_id = null;

    public string $location = '';

    public string $contact_email = '';

    public string $contact_phone = '';

    public string $contact_instructions = '';

    public string $starts_at = '';

    public string $ends_at = '';

    public string $application_deadline = '';

    public ?int $capacity = null;

    public bool $is_published = true;

    /**
     * Array of talent requirements: [['talent_id' => int, 'slots' => int, 'notes' => string]]
     *
     * @var array<int, array{talent_id: int|string|null, slots: int, notes: string}>
     */
    public array $talent_requirements = [];

    public function mount(): void
    {
        abort_unless(auth()->user()->canOrganizeEvents(), 403);
        $this->contact_email = auth()->user()->email;
    }

    public function addTalentRequirement(): void
    {
        $this->talent_requirements[] = [
            'talent_id' => null,
            'slots' => 1,
            'notes' => '',
        ];
    }

    public function removeTalentRequirement(int $index): void
    {
        unset($this->talent_requirements[$index]);
        $this->talent_requirements = array_values($this->talent_requirements);
    }

    public function save(): void
    {
        $validated = $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'event_type' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:5000'],
            'requirements' => ['nullable', 'string', 'max:2000'],
            'talent_id' => ['nullable', 'integer', 'exists:talents,id'],
            'location' => ['required', 'string', 'max:255'],
            'contact_email' => ['required', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'contact_instructions' => ['nullable', 'string', 'max:2000'],
            'starts_at' => ['required', 'date', 'after:now'],
            'ends_at' => ['nullable', 'date', 'after:starts_at'],
            'application_deadline' => ['nullable', 'date', 'before:starts_at'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'is_published' => ['boolean'],
            'talent_requirements' => ['array'],
            'talent_requirements.*.talent_id' => ['required', 'integer', 'exists:talents,id'],
            'talent_requirements.*.slots' => ['required', 'integer', 'min:1'],
            'talent_requirements.*.notes' => ['nullable', 'string', 'max:255'],
        ]);

        $eventData = collect($validated)->except('talent_requirements')->toArray();
        $event = auth()->user()->organizedEvents()->create($eventData);

        // Attach talent requirements if provided
        if (! empty($validated['talent_requirements'])) {
            foreach ($validated['talent_requirements'] as $req) {
                if (! empty($req['talent_id'])) {
                    $event->talents()->attach($req['talent_id'], [
                        'slots' => $req['slots'] ?? 1,
                        'notes' => $req['notes'] ?? null,
                    ]);
                }
            }
        }

        $this->redirect(route('events.show', $event), navigate: true);
    }

    public function render(): View
    {
        $campusId = auth()->user()->campus_id ?? auth()->id();

        return view('livewire.events.create', [
            'talents' => Talent::query()->forCampus($campusId)->orderBy('name')->get(),
            'eventTypes' => [
                'Concert',
                'Exhibition',
                'Workshop',
                'Hackathon',
                'Showcase',
                'Open Mic',
                'Competition',
                'Networking',
                'Audition',
            ],
        ]);
    }
}
