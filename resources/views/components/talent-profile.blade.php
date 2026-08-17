@props([
    'student',
    'theme',
    'isFollowing' => false,
])

<div class="theme-{{ $theme->value }} min-h-[calc(100vh-4rem)]">
    @if ($theme->value === 'gallery')
        <div class="mx-auto max-w-6xl px-6 py-12">
            <p class="text-xs uppercase tracking-[0.35em] text-brass">{{ $theme->label() }}</p>
            <div class="mt-4 flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <h1 class="font-display text-6xl">{{ $student->name }}</h1>
                    <p class="mt-3 max-w-2xl text-lg text-ink/70">{{ $student->profile?->headline }}</p>
                    <p class="mt-4 max-w-2xl text-sm leading-7 text-ink/70">{{ $student->profile?->bio }}</p>
                </div>
                <div class="flex items-center gap-3">
                    @if (! auth()->user()->is($student))
                        <button wire:click="follow" class="btn-dark">{{ $isFollowing ? 'Following' : 'Follow' }}</button>
                    @endif
                    <p class="text-sm text-mist">{{ $student->xp }} XP</p>
                </div>
            </div>
            <div class="mt-12 columns-1 gap-5 sm:columns-2 lg:columns-3">
                @foreach ($student->portfolioItems as $item)
                    <a href="{{ route('portfolio.show', $item) }}" class="mb-5 block break-inside-avoid" wire:key="gallery-{{ $item->id }}" wire:navigate>
                        <figure class="overflow-hidden rounded-sm bg-white p-3 shadow-[0_20px_40px_-28px_rgba(40,24,12,0.55)]">
                            <img src="{{ $item->displayUrl() }}" alt="{{ $item->title }}" class="w-full object-cover">
                            <figcaption class="mt-3 font-display text-lg">{{ $item->title }}</figcaption>
                        </figure>
                    </a>
                @endforeach
            </div>
        </div>
    @elseif ($theme->value === 'darkroom')
        <div class="mx-auto max-w-6xl px-6 py-12">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-xs uppercase tracking-[0.4em] text-gold">{{ $theme->label() }}</p>
                    <h1 class="mt-3 font-display text-6xl">{{ $student->name }}</h1>
                    <p class="mt-3 max-w-xl text-paper/70">{{ $student->profile?->bio }}</p>
                </div>
                @if (! auth()->user()->is($student))
                    <button wire:click="follow" class="btn-primary">{{ $isFollowing ? 'Following' : 'Follow' }}</button>
                @endif
            </div>
            @if ($student->portfolioItems->first())
                <a href="{{ route('portfolio.show', $student->portfolioItems->first()) }}" class="mt-10 block" wire:navigate>
                    <img src="{{ $student->portfolioItems->first()->displayUrl() }}" alt="{{ $student->portfolioItems->first()->title }}" class="max-h-[70vh] w-full object-cover">
                </a>
            @endif
            <div class="mt-4 grid grid-cols-2 gap-3 md:grid-cols-4">
                @foreach ($student->portfolioItems->skip(1) as $item)
                    <a href="{{ route('portfolio.show', $item) }}" wire:key="dark-{{ $item->id }}" wire:navigate>
                        <img src="{{ $item->displayUrl() }}" alt="{{ $item->title }}" class="aspect-square w-full object-cover grayscale hover:grayscale-0">
                    </a>
                @endforeach
            </div>
        </div>
    @elseif ($theme->value === 'vinyl')
        <div class="mx-auto max-w-5xl px-6 py-12">
            <p class="text-xs uppercase tracking-[0.4em] text-gold">{{ $theme->label() }}</p>
            <div class="mt-4 flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
                <h1 class="font-display text-6xl">{{ $student->name }}</h1>
                @if (! auth()->user()->is($student))
                    <button wire:click="follow" class="btn-primary">{{ $isFollowing ? 'Following' : 'Follow' }}</button>
                @endif
            </div>
            <p class="mt-3 max-w-xl text-paper/70">{{ $student->profile?->headline }}</p>
            <div class="mt-12 grid grid-cols-2 gap-8 md:grid-cols-3">
                @foreach ($student->portfolioItems as $item)
                    <a href="{{ route('portfolio.show', $item) }}" class="group text-center" wire:key="vinyl-{{ $item->id }}" wire:navigate>
                        <img src="{{ $item->displayUrl() }}" alt="{{ $item->title }}" class="mx-auto aspect-square w-full rounded-full object-cover shadow-2xl ring-8 ring-black/40 group-hover:ring-gold/40">
                        <p class="mt-4 font-display text-xl">{{ $item->title }}</p>
                    </a>
                @endforeach
            </div>
        </div>
    @elseif ($theme->value === 'stage')
        <div class="mx-auto max-w-5xl px-6 py-16 text-center">
            <p class="text-xs uppercase tracking-[0.45em] text-gold">{{ $theme->label() }}</p>
            <h1 class="mt-4 font-display text-7xl">{{ $student->name }}</h1>
            <p class="mx-auto mt-4 max-w-2xl text-paper/70">{{ $student->profile?->bio }}</p>
            @if (! auth()->user()->is($student))
                <button wire:click="follow" class="btn-primary mt-6">{{ $isFollowing ? 'Following' : 'Follow' }}</button>
            @endif
            <div class="mt-14 flex flex-col gap-8">
                @foreach ($student->portfolioItems as $item)
                    <a href="{{ route('portfolio.show', $item) }}" class="overflow-hidden rounded-[2rem] border border-gold/20 bg-black/30" wire:key="stage-{{ $item->id }}" wire:navigate>
                        <img src="{{ $item->displayUrl() }}" alt="{{ $item->title }}" class="aspect-[16/8] w-full object-cover">
                        <p class="py-4 font-display text-2xl">{{ $item->title }}</p>
                    </a>
                @endforeach
            </div>
        </div>
    @elseif ($theme->value === 'grid')
        <div class="mx-auto max-w-6xl px-6 py-12">
            <div class="flex flex-col gap-4 border-b border-ink/15 pb-8 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-xs uppercase tracking-[0.3em] text-mist">{{ $theme->label() }}</p>
                    <h1 class="mt-2 text-5xl font-semibold tracking-tight">{{ $student->name }}</h1>
                    <p class="mt-2 text-mist">{{ $student->profile?->headline }}</p>
                </div>
                @if (! auth()->user()->is($student))
                    <button wire:click="follow" class="btn-dark">{{ $isFollowing ? 'Following' : 'Follow' }}</button>
                @endif
            </div>
            <div class="mt-8 grid grid-cols-2 gap-1 md:grid-cols-3">
                @foreach ($student->portfolioItems as $item)
                    <a href="{{ route('portfolio.show', $item) }}" wire:key="grid-{{ $item->id }}" wire:navigate>
                        <img src="{{ $item->displayUrl() }}" alt="{{ $item->title }}" class="aspect-square w-full object-cover">
                    </a>
                @endforeach
            </div>
        </div>
    @elseif ($theme->value === 'social')
        <div class="mx-auto max-w-4xl px-6 py-12">
            <div class="flex items-center gap-6">
                <span class="flex size-24 items-center justify-center rounded-full bg-ember text-2xl font-bold text-white">{{ $student->initials() }}</span>
                <div>
                    <h1 class="text-3xl font-semibold">{{ $student->name }}</h1>
                    <p class="text-sm text-mist">{{ $student->profile?->headline }}</p>
                    @if (! auth()->user()->is($student))
                        <button wire:click="follow" class="btn-primary mt-3">{{ $isFollowing ? 'Following' : 'Follow' }}</button>
                    @endif
                </div>
            </div>
            <p class="mt-6 max-w-xl">{{ $student->profile?->bio }}</p>
            <div class="mt-8 grid grid-cols-3 gap-1">
                @foreach ($student->portfolioItems as $item)
                    <a href="{{ route('portfolio.show', $item) }}" wire:key="social-{{ $item->id }}" wire:navigate>
                        <img src="{{ $item->displayUrl() }}" alt="{{ $item->title }}" class="aspect-square w-full object-cover">
                    </a>
                @endforeach
            </div>
        </div>
    @elseif ($theme->value === 'cinema')
        <div class="py-12">
            <div class="mx-auto max-w-5xl px-6 text-center">
                <p class="text-xs uppercase tracking-[0.5em] text-gold">{{ $theme->label() }}</p>
                <h1 class="mt-4 font-display text-6xl">{{ $student->name }}</h1>
                <p class="mt-3 text-paper/60">{{ $student->profile?->headline }}</p>
                @if (! auth()->user()->is($student))
                    <button wire:click="follow" class="btn-primary mt-6">{{ $isFollowing ? 'Following' : 'Follow' }}</button>
                @endif
            </div>
            <div class="mt-12 flex flex-col gap-10">
                @foreach ($student->portfolioItems as $item)
                    <a href="{{ route('portfolio.show', $item) }}" class="block" wire:key="cinema-{{ $item->id }}" wire:navigate>
                        <img src="{{ $item->displayUrl() }}" alt="{{ $item->title }}" class="aspect-video w-full object-cover">
                        <p class="mt-3 px-6 font-display text-2xl">{{ $item->title }}</p>
                    </a>
                @endforeach
            </div>
        </div>
    @else
        <div class="mx-auto max-w-6xl px-6 py-12">
            <div class="grid gap-10 lg:grid-cols-[0.9fr_1.1fr] lg:items-end">
                <div>
                    <p class="text-xs uppercase tracking-[0.4em] text-mist">{{ $theme->label() }}</p>
                    <h1 class="mt-3 font-display text-7xl leading-[0.9]">{{ $student->name }}</h1>
                    <p class="mt-6 max-w-md text-lg">{{ $student->profile?->bio }}</p>
                    @if (! auth()->user()->is($student))
                        <button wire:click="follow" class="btn-dark mt-6">{{ $isFollowing ? 'Following' : 'Follow' }}</button>
                    @endif
                </div>
                @if ($student->portfolioItems->first())
                    <a href="{{ route('portfolio.show', $student->portfolioItems->first()) }}" wire:navigate>
                        <img src="{{ $student->portfolioItems->first()->displayUrl() }}" alt="{{ $student->portfolioItems->first()->title }}" class="aspect-[3/4] w-full object-cover">
                    </a>
                @endif
            </div>
            <div class="mt-10 grid grid-cols-2 gap-4 md:grid-cols-4">
                @foreach ($student->portfolioItems->skip(1) as $item)
                    <a href="{{ route('portfolio.show', $item) }}" wire:key="edit-{{ $item->id }}" wire:navigate>
                        <img src="{{ $item->displayUrl() }}" alt="{{ $item->title }}" class="aspect-[3/4] w-full object-cover">
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</div>
