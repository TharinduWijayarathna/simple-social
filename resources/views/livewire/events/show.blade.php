<div class="mx-auto max-w-2xl px-4 py-10 lg:px-0">
    <p class="text-xs uppercase tracking-[0.28em] text-ember">{{ $event->starts_at->format('l, F j · g:ia') }}</p>
    <h1 class="mt-2 font-display text-5xl">{{ $event->title }}</h1>
    <p class="mt-3 text-mist">{{ $event->location }} · Hosted by {{ $event->organizer->name }}</p>
    <p class="mt-6 whitespace-pre-wrap leading-7">{{ $event->description }}</p>

    @if (session('status'))
        <p class="mt-4 rounded-2xl bg-gold/20 px-4 py-3 text-sm">{{ session('status') }}</p>
    @endif

    <button wire:click="rsvp" class="btn-primary mt-8">I'm going</button>
</div>
