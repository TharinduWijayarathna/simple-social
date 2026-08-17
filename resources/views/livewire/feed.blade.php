<div class="px-4 py-6 lg:px-10 lg:py-8">
    <div class="mx-auto grid max-w-6xl gap-8 lg:grid-cols-[minmax(0,38rem)_18rem] lg:justify-center">
        <div class="flex flex-col gap-6">
            @auth
                <a href="{{ route('portfolio.create') }}" class="feed-card flex items-center gap-3 px-4 py-3" wire:navigate>
                    <span class="flex size-10 items-center justify-center rounded-full bg-studio text-xs font-semibold text-gold">{{ auth()->user()->initials() }}</span>
                    <span class="flex-1 rounded-full bg-wall px-4 py-3 text-sm text-mist">Share your talent with campus…</span>
                    <span class="btn-primary">Post</span>
                </a>
            @else
                <div class="feed-card px-5 py-5">
                    <p class="text-xs uppercase tracking-[0.28em] text-ember">Campus only</p>
                    <h1 class="mt-2 font-display text-3xl">University talent, on one wall.</h1>
                    <p class="mt-2 text-sm text-mist">A Facebook-style feed for your campus — art, music, dance, film, and more. Join to like, comment, and post.</p>
                    <div class="mt-4 flex gap-3">
                        <a href="{{ route('register') }}" class="btn-primary" wire:navigate>Join campus</a>
                        <a href="{{ route('login') }}" class="btn-ghost" wire:navigate>Sign in</a>
                    </div>
                </div>
            @endauth

            @forelse ($posts as $item)
                <article class="feed-card" wire:key="post-{{ $item->id }}">
                    <div class="flex items-center justify-between gap-3 px-4 py-3">
                        <a href="{{ route('students.show', $item->user) }}" class="flex items-center gap-3" wire:navigate>
                            <span class="flex size-10 items-center justify-center rounded-full bg-studio text-xs font-semibold text-gold">{{ $item->user->initials() }}</span>
                            <span>
                                <span class="block text-sm font-semibold">{{ $item->user->name }}</span>
                                <span class="block text-xs text-mist">{{ $item->talent?->name }} · {{ $item->published_at?->diffForHumans() }}</span>
                            </span>
                        </a>
                        <span class="rounded-full bg-wall px-3 py-1 text-[11px] uppercase tracking-wide text-mist">{{ $item->talent?->theme?->label() }}</span>
                    </div>

                    <a href="{{ route('portfolio.show', $item) }}" wire:navigate>
                        <img src="{{ $item->displayUrl() }}" alt="{{ $item->title }}" class="{{ $item->feedAspectClass() }} w-full object-cover">
                    </a>

                    <div class="px-4 py-4">
                        <div class="flex items-center gap-4 text-sm">
                            @auth
                                <button type="button" wire:click="like({{ $item->id }})" class="font-semibold {{ $item->liked_by_viewer ? 'text-ember' : 'text-ink' }}">
                                    {{ $item->liked_by_viewer ? 'Liked' : 'Like' }} · {{ $item->likes_count }}
                                </button>
                                <span class="text-mist">Comment · {{ $item->comments_count }}</span>
                                <button type="button" wire:click="share({{ $item->id }})" class="text-mist hover:text-ink">
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
                                <li class="text-sm" wire:key="feed-comment-{{ $comment->id }}">
                                    <span class="font-semibold">{{ $comment->user->name }}</span>
                                    <span class="text-mist"> {{ $comment->body }}</span>
                                </li>
                            @endforeach
                        </ul>

                        @auth
                            <form wire:submit="comment({{ $item->id }})" class="mt-3 flex gap-2">
                                <input wire:model="commentDrafts.{{ $item->id }}" type="text" class="field flex-1" placeholder="Write a comment…">
                                <button type="submit" class="btn-ghost">Send</button>
                            </form>
                            @error('commentDrafts.'.$item->id)
                                <p class="mt-1 text-xs text-ember">{{ $message }}</p>
                            @enderror
                        @endauth
                    </div>
                </article>
            @empty
                <p class="rounded-[1.75rem] bg-white p-8 text-mist">The campus wall is empty. Be the first to post.</p>
            @endforelse

            <div>{{ $posts->links() }}</div>
        </div>

        <aside class="hidden lg:flex lg:flex-col lg:gap-5">
            <section class="rounded-[1.5rem] bg-white p-5">
                <h2 class="font-display text-lg">Campus nights</h2>
                <ul class="mt-3 flex flex-col gap-3">
                    @forelse ($upcomingEvents as $event)
                        <li wire:key="rail-event-{{ $event->id }}">
                            <a href="{{ route('events.show', $event) }}" class="block text-sm" wire:navigate>
                                <span class="font-medium">{{ $event->title }}</span>
                                <span class="mt-0.5 block text-xs text-mist">{{ $event->starts_at->format('D, M j · g:ia') }}</span>
                            </a>
                        </li>
                    @empty
                        <li class="text-sm text-mist">No upcoming events yet.</li>
                    @endforelse
                </ul>
                <a href="{{ route('events.index') }}" class="mt-4 inline-block text-xs uppercase tracking-wide text-ember" wire:navigate>All events</a>
            </section>
            <section class="rounded-[1.5rem] bg-studio p-5 text-paper">
                <h2 class="font-display text-lg text-gold">Rising on campus</h2>
                <ol class="mt-3 flex flex-col gap-3 text-sm">
                    @foreach ($risingStudents as $student)
                        <li class="flex justify-between gap-3" wire:key="rail-student-{{ $student->id }}">
                            <a href="{{ route('students.show', $student) }}" class="hover:text-gold" wire:navigate>{{ $student->name }}</a>
                            <span class="text-mist">{{ $student->xp }} XP</span>
                        </li>
                    @endforeach
                </ol>
            </section>
        </aside>
    </div>
</div>
