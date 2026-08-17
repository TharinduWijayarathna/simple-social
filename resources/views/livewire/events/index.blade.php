<div class="px-4 py-8 lg:px-10">
    <div class="flex items-end justify-between gap-4">
        <div>
            <p class="text-xs uppercase tracking-[0.28em] text-ember">Campus</p>
            <h1 class="font-display text-4xl">Events</h1>
            <p class="mt-1 text-mist">Exhibitions, open mics, showcases — join from here.</p>
        </div>
        @if (auth()->user()->canOrganizeEvents())
            <a href="{{ route('events.create') }}" class="btn-dark" wire:navigate>Create event</a>
        @endif
    </div>

    <div class="mt-8 grid grid-cols-1 gap-4 md:grid-cols-2">
        @foreach ($events as $event)
            <a href="{{ route('events.show', $event) }}" class="rounded-[1.5rem] bg-white p-6" wire:key="event-{{ $event->id }}" wire:navigate>
                <p class="text-xs uppercase tracking-wide text-mist">{{ $event->starts_at->format('D, M j · g:ia') }}</p>
                <h2 class="mt-2 font-display text-2xl">{{ $event->title }}</h2>
                <p class="mt-2 text-sm text-mist">{{ $event->location }} · {{ $event->organizer->name }}</p>
            </a>
        @endforeach
    </div>

    <div class="mt-6">{{ $events->links() }}</div>
</div>
