<div class="px-4 py-8 lg:px-10">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="font-display text-4xl">Explore</h1>
            <p class="mt-1 text-mist">Every published piece from campus, by talent.</p>
        </div>
        <a href="{{ route('portfolio.create') }}" class="btn-primary" wire:navigate>Upload</a>
    </div>

    <div class="mt-6 flex flex-wrap gap-2">
        <button wire:click="$set('talent_id', null)" class="rounded-full border px-3 py-1 text-sm {{ $talent_id === null ? 'border-ember bg-ember text-white' : 'border-ink/15 bg-white' }}">All</button>
        @foreach ($talents as $talent)
            <button wire:key="filter-{{ $talent->id }}" wire:click="$set('talent_id', {{ $talent->id }})" class="rounded-full border px-3 py-1 text-sm {{ $talent_id === $talent->id ? 'border-ember bg-ember text-white' : 'border-ink/15 bg-white' }}">
                {{ $talent->name }}
            </button>
        @endforeach
    </div>

    <div class="mt-8 columns-1 gap-4 sm:columns-2 lg:columns-3">
        @foreach ($items as $item)
            <a href="{{ route('portfolio.show', $item) }}" class="mb-4 block break-inside-avoid overflow-hidden rounded-[1.5rem] bg-white" wire:key="item-{{ $item->id }}" wire:navigate>
                <img src="{{ $item->displayUrl() }}" alt="{{ $item->title }}" class="w-full object-cover">
                <div class="p-4">
                    <p class="text-xs uppercase tracking-wide text-mist">{{ $item->talent?->name }}</p>
                    <h2 class="mt-1 font-display text-xl">{{ $item->title }}</h2>
                    <p class="mt-2 text-sm text-mist">{{ $item->user->name }} · {{ $item->likes_count }} likes</p>
                </div>
            </a>
        @endforeach
    </div>

    <div class="mt-6">{{ $items->links() }}</div>
</div>
