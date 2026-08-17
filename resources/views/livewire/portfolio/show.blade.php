<div class="mx-auto max-w-3xl px-4 py-10 lg:px-0">
    <p class="text-xs uppercase tracking-[0.28em] text-ember">{{ $item->talent?->name }}</p>
    <h1 class="mt-2 font-display text-5xl">{{ $item->title }}</h1>
    <p class="mt-3">
        <a href="{{ route('students.show', $item->user) }}" class="text-mist hover:text-ink" wire:navigate>{{ $item->user->name }}</a>
    </p>
    <p class="mt-4 whitespace-pre-wrap text-mist">{{ $item->description }}</p>

    <img src="{{ $item->displayUrl() }}" alt="{{ $item->title }}" class="mt-8 w-full rounded-[1.75rem] object-cover {{ $item->feedAspectClass() }}">

    <div class="mt-6 flex flex-wrap items-center gap-3">
        <button wire:click="like" class="btn-dark inline-flex items-center gap-2">
            <x-icon name="heart" class="size-4" />
            Like
            <span class="text-xs opacity-80">{{ $item->likes_count }}</span>
        </button>
        <button wire:click="share" class="btn-ghost inline-flex items-center gap-2">
            <x-icon name="share" class="size-4" />
            Share
            <span class="text-xs">{{ $item->shares_count }}</span>
        </button>
        <a href="{{ $item->fileUrl() }}" class="text-sm text-ember">Open original</a>
    </div>
    @if (session('shared_item_id') === $item->id)
        <p class="mt-3 text-sm text-ember">Link copied.</p>
    @endif

    <section class="mt-12">
        <h2 class="font-display text-2xl">Comments</h2>
        <form wire:submit="comment" class="mt-4 flex flex-col gap-2">
            <textarea wire:model="body" rows="3" class="field" placeholder="Leave a note for the artist"></textarea>
            @error('body') <span class="text-sm text-ember">{{ $message }}</span> @enderror
            <button type="submit" class="btn-ghost self-start">Post comment</button>
        </form>
        <ul class="mt-4 flex flex-col gap-3">
            @foreach ($item->comments as $comment)
                <li class="rounded-2xl bg-white p-4 text-sm" wire:key="comment-{{ $comment->id }}">
                    <p class="font-medium">{{ $comment->user->name }}</p>
                    <p class="mt-1">{{ $comment->body }}</p>
                </li>
            @endforeach
        </ul>
    </section>
</div>
