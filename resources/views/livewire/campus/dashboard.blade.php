<div class="px-4 py-8 lg:px-10">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs uppercase tracking-[0.28em] text-ember">Campus desk</p>
            <h1 class="font-display text-4xl">Events & participation</h1>
            <p class="mt-2 max-w-xl text-mist">Post campus nights, exhibitions, and open mics. Students join from the feed.</p>
        </div>
        <a href="{{ route('events.create') }}" class="btn-primary" wire:navigate>New event</a>
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-[20rem_minmax(0,1fr)]">
        <ul class="flex flex-col gap-3">
            @forelse ($events as $event)
                <li wire:key="campus-event-{{ $event->id }}">
                    <button type="button" wire:click="selectEvent({{ $event->id }})" class="w-full rounded-2xl border px-4 py-4 text-left {{ $selectedEvent?->is($event) ? 'border-ember bg-white' : 'border-ink/10 bg-white/70' }}">
                        <p class="text-xs uppercase tracking-wide text-mist">{{ $event->starts_at->format('D, M j') }}</p>
                        <p class="mt-1 font-medium">{{ $event->title }}</p>
                        <p class="mt-1 text-xs text-ember">{{ $event->applications_count }} going</p>
                    </button>
                </li>
            @empty
                <li class="rounded-2xl bg-white p-5 text-sm text-mist">No events yet. Post the first campus night.</li>
            @endforelse
        </ul>

        @if ($selectedEvent)
            <section class="rounded-[1.75rem] bg-white p-6">
                <p class="text-xs uppercase tracking-wide text-mist">{{ $selectedEvent->starts_at->format('l, F j · g:ia') }}</p>
                <h2 class="mt-1 font-display text-3xl">{{ $selectedEvent->title }}</h2>
                <p class="mt-2 text-sm text-mist">{{ $selectedEvent->location }} · {{ $selectedEvent->organizer->name }}</p>
                <p class="mt-4 whitespace-pre-wrap text-sm">{{ $selectedEvent->description }}</p>
                <a href="{{ route('events.show', $selectedEvent) }}" class="mt-4 inline-block text-sm text-ember" wire:navigate>Open event page</a>

                <h3 class="mt-8 text-sm font-semibold uppercase tracking-wide text-mist">Participants</h3>
                <ul class="mt-3 divide-y divide-ink/8">
                    @forelse ($selectedEvent->applications as $application)
                        <li class="flex items-center justify-between py-3 text-sm" wire:key="app-{{ $application->id }}">
                            <a href="{{ route('students.show', $application->user) }}" class="font-medium" wire:navigate>{{ $application->user->name }}</a>
                            <span class="text-mist">{{ str_replace('_', ' ', $application->status->value) }}</span>
                        </li>
                    @empty
                        <li class="py-3 text-sm text-mist">Nobody has joined yet.</li>
                    @endforelse
                </ul>
            </section>
        @endif
    </div>
</div>
