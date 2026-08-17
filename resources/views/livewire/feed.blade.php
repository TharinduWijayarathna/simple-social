<div class="mx-auto max-w-6xl px-4 py-6">
    <div class="grid gap-6 lg:grid-cols-[minmax(0,38rem)_18rem] lg:justify-center">
        <div class="flex flex-col gap-4">
            @auth
                <div class="rounded-2xl bg-[#242526] px-4 py-3 text-white">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('profile.show') }}" class="flex size-10 shrink-0 items-center justify-center rounded-full bg-studio text-xs font-semibold text-gold" wire:navigate>{{ auth()->user()->initials() }}</a>
                        <a href="{{ route('portfolio.create') }}" class="flex-1 rounded-full bg-[#3a3b3c] px-4 py-2.5 text-sm text-white/70" wire:navigate>What's on your mind, {{ auth()->user()->name }}?</a>
                        <div class="hidden items-center gap-3 sm:flex">
                            <a href="{{ route('portfolio.create') }}" class="text-rose-400" title="Video" wire:navigate>
                                <svg class="size-6" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17 10.5V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-3.5l4 4v-11z"/></svg>
                            </a>
                            <a href="{{ route('portfolio.create') }}" class="text-emerald-400" title="Photo" wire:navigate>
                                <svg class="size-6" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M21 19V5a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2M8.5 13.5l2.5 3.01L14.5 12l4.5 6H5z"/></svg>
                            </a>
                            <a href="{{ route('portfolio.create') }}" class="text-amber-400" title="Feeling" wire:navigate>
                                <svg class="size-6" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2m-2 7.5A1.5 1.5 0 1 1 8.5 11 1.5 1.5 0 0 1 10 9.5m7.07 5.75C15.9 16.85 14.1 18 12 18s-3.9-1.15-5.07-2.75a.75.75 0 0 1 1.2-.9C9.1 15.6 10.45 16.5 12 16.5s2.9-.9 3.87-2.15a.75.75 0 1 1 1.2.9M15.5 11A1.5 1.5 0 1 1 17 9.5 1.5 1.5 0 0 1 15.5 11"/></svg>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="relative">
                    <div class="flex gap-3 overflow-x-auto pb-2">
                        <a href="{{ route('status.create') }}" class="relative h-48 w-28 shrink-0 overflow-hidden rounded-2xl bg-[#3a3b3c]" wire:navigate>
                            <span class="flex h-32 items-center justify-center bg-[#242526] text-lg font-semibold text-gold">{{ auth()->user()->initials() }}</span>
                            <span class="absolute bottom-[3.25rem] left-1/2 flex size-8 -translate-x-1/2 items-center justify-center rounded-full bg-sky-500 text-xl font-bold text-white ring-4 ring-[#242526]">+</span>
                            <span class="absolute inset-x-0 bottom-0 px-2 pb-2 text-center text-xs font-semibold text-white">Create story</span>
                        </a>

                        @foreach ($statuses as $status)
                            <a href="{{ route('status.show', $status) }}" class="relative h-48 w-28 shrink-0 overflow-hidden rounded-2xl" wire:key="status-{{ $status->id }}" wire:navigate>
                                <img src="{{ $status->imageUrl() }}" alt="" class="size-full object-cover">
                                <span class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-black/20"></span>
                                <span class="absolute left-2 top-2 flex size-8 items-center justify-center rounded-full bg-studio text-[10px] font-semibold text-gold ring-2 ring-sky-400">{{ $status->user->initials() }}</span>
                                @if ($status->created_at->isToday())
                                    <span class="absolute right-2 top-2 rounded bg-black/55 px-1.5 py-0.5 text-[10px] font-semibold text-white">Today</span>
                                @endif
                                <span class="absolute inset-x-2 bottom-2 text-xs font-semibold leading-tight text-white">{{ $status->user->name }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="feed-card px-5 py-5">
                    <p class="text-xs uppercase tracking-[0.28em] text-ember">Campus only</p>
                    <h1 class="mt-2 font-display text-3xl">University talent, on one wall.</h1>
                    <p class="mt-2 text-sm text-mist">A Facebook-style feed for your campus. Join to post, like, and share a 24-hour status.</p>
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
