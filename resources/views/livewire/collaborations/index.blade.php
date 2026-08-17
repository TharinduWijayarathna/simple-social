<div class="px-4 py-8 lg:px-10">
    <h1 class="font-display text-4xl">Open collaborations</h1>
    <p class="mt-1 text-mist">Find a crew for the next show, film, or drop.</p>

    <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-2">
        @foreach ($collaborations as $collaboration)
            <a href="{{ route('collaborations.show', $collaboration) }}" class="rounded-[1.5rem] bg-white p-6" wire:key="collab-{{ $collaboration->id }}" wire:navigate>
                <p class="text-xs uppercase tracking-wide text-mist">{{ $collaboration->status->value }}</p>
                <h2 class="mt-1 font-display text-2xl">{{ $collaboration->title }}</h2>
                <p class="mt-2 text-sm text-mist">{{ $collaboration->owner->name }} · {{ $collaboration->members_count }} members</p>
            </a>
        @endforeach
    </div>

    <div class="mt-6">{{ $collaborations->links() }}</div>
</div>
