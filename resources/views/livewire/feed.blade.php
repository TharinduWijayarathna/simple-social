<div class="mx-auto max-w-6xl px-4 py-6">
    <div class="grid gap-6 lg:grid-cols-[minmax(0,38rem)_18rem] lg:justify-center">
        <div class="flex flex-col gap-5">
            @auth
                <a href="{{ route('portfolio.create') }}" class="feed-card flex items-center gap-3 px-4 py-3" wire:navigate>
                    <span class="flex size-10 items-center justify-center rounded-full bg-studio text-xs font-semibold text-gold">{{ auth()->user()->initials() }}</span>
                    <span class="flex-1 rounded-full bg-wall px-4 py-3 text-sm text-mist">What's on your mind, {{ auth()->user()->name }}?</span>
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
                <livewire:post-card :item="$item" wire:key="home-post-{{ $item->id }}" />
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
                <h2 class="font-display text-lg text-gold">People on campus</h2>
                <ol class="mt-3 flex flex-col gap-3 text-sm">
                    @foreach ($risingStudents as $student)
                        <li class="flex justify-between gap-3" wire:key="rail-student-{{ $student->id }}">
                            <a href="{{ route('students.show', $student) }}" class="hover:text-gold" wire:navigate>{{ $student->name }}</a>
                            <span class="text-mist">{{ $student->profile?->headline }}</span>
                        </li>
                    @endforeach
                </ol>
            </section>
        </aside>
    </div>
</div>
