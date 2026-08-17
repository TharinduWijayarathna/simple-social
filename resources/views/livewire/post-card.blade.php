<article class="feed-card">
    <div class="flex items-center justify-between gap-3 px-4 py-3">
        <a href="{{ route('students.show', $item->user) }}" class="flex items-center gap-3" wire:navigate>
            <span class="flex size-10 items-center justify-center rounded-full bg-studio text-xs font-semibold text-gold">{{ $item->user->initials() }}</span>
            <span>
                <span class="block text-sm font-semibold">{{ $item->user->name }}</span>
                <span class="block text-xs text-mist">{{ $item->talent?->name }} · {{ $item->published_at?->diffForHumans() }}</span>
            </span>
        </a>
        @if ($item->talent)
            <span class="rounded-full bg-wall px-3 py-1 text-[11px] uppercase tracking-wide text-mist">{{ $item->talent->name }}</span>
        @endif
    </div>

    <a href="{{ route('portfolio.show', $item) }}" wire:navigate>
        <img src="{{ $item->displayUrl() }}" alt="{{ $item->title }}" class="{{ $item->feedAspectClass() }} w-full object-cover">
    </a>

    <div class="px-4 py-4">
        <div class="flex items-center gap-4 text-sm">
            @auth
                <button type="button" wire:click="like" class="font-semibold {{ $likedByViewer ? 'text-ember' : 'text-ink' }}">
                    {{ $likedByViewer ? 'Liked' : 'Like' }} · {{ $item->likes_count }}
                </button>
                <span class="text-mist">Comment · {{ $item->comments_count }}</span>
                <button type="button" wire:click="share" class="text-mist hover:text-ink">
                    Share · {{ $item->shares_count }}
                </button>
            @else
                <a href="{{ route('login') }}" class="font-semibold" wire:navigate>Like · {{ $item->likes_count }}</a>
                <a href="{{ route('login') }}" class="text-mist" wire:navigate>Comment · {{ $item->comments_count }}</a>
                <a href="{{ route('login') }}" class="text-mist" wire:navigate>Share · {{ $item->shares_count }}</a>
            @endauth
        </div>
        @if (session('shared_item_id') === $item->id)
            <p class="mt-2 text-xs text-ember">Link copied. Shared with campus.</p>
        @endif
        <h2 class="mt-3 font-display text-xl">{{ $item->title }}</h2>
        @if ($item->description)
            <p class="mt-1 text-sm text-mist">{{ $item->description }}</p>
        @endif

        <ul class="mt-4 flex flex-col gap-2">
            @foreach ($item->comments->take(2) as $comment)
                <li class="text-sm" wire:key="card-comment-{{ $comment->id }}">
                    <span class="font-semibold">{{ $comment->user->name }}</span>
                    <span class="text-mist"> {{ $comment->body }}</span>
                </li>
            @endforeach
        </ul>

        @auth
            <form wire:submit="comment" class="mt-3 flex gap-2">
                <input wire:model="body" type="text" class="field flex-1" placeholder="Write a comment…">
                <button type="submit" class="btn-ghost">Send</button>
            </form>
            @error('body')
                <p class="mt-1 text-xs text-ember">{{ $message }}</p>
            @enderror
        @endauth
    </div>
</article>
