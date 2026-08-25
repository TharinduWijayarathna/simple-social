<div class="page-shell py-6">
    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_18rem]">
        <div class="flex flex-col gap-4">
            @auth
                <div class="feed-card px-4 py-3">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('profile.show') }}" class="flex size-10 shrink-0 items-center justify-center overflow-hidden rounded-full bg-studio text-xs font-semibold text-gold" wire:navigate>
                            @if (auth()->user()->avatarUrl())
                                <img src="{{ auth()->user()->avatarUrl() }}" alt="{{ auth()->user()->name }}" class="size-full object-cover rounded-full">
                            @else
                                {{ auth()->user()->initials() }}
                            @endif
                        </a>
                        <a href="{{ route('portfolio.create') }}" class="flex-1 rounded-full bg-wall px-4 py-2.5 text-sm text-mist" wire:navigate>What's on your mind, {{ auth()->user()->name }}?</a>
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
                        <a href="{{ route('portfolio.create', ['type' => 'story']) }}" class="relative w-32 aspect-[9/16] shrink-0 overflow-hidden rounded-2xl border-2 border-dashed border-ember/30 bg-wall/60 group transition hover:border-amber-400 flex flex-col items-center justify-center text-center p-3" wire:navigate>
                            <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-ember text-white font-bold text-lg shadow-sm mb-2">+</span>
                            <span class="block text-xs font-bold text-ink">Create story</span>
                            <span class="block text-[10px] text-mist mt-0.5">24h 9:16 Clip</span>
                        </a>

                        @foreach ($statuses as $status)
                            <a href="{{ route('status.show', $status) }}" class="relative w-32 aspect-[9/16] shrink-0 overflow-hidden rounded-2xl border-2 border-amber-400/80 shadow-md transition hover:scale-[1.02]" wire:key="status-{{ $status->id }}" wire:navigate>
                                @if ($status->isVideo())
                                    <video src="{{ $status->mediaUrl() }}" class="size-full object-cover aspect-[9/16]" muted></video>
                                    <span class="absolute top-2 right-2 flex size-5 items-center justify-center rounded-full bg-black/60 text-white">
                                        <x-icon name="video" class="size-3" />
                                    </span>
                                @else
                                    <img src="{{ $status->imageUrl() }}" alt="" class="size-full object-cover">
                                @endif
                                <span class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-black/20"></span>
                                @if ($status->user->avatarUrl())
                                    <img src="{{ $status->user->avatarUrl() }}" alt="{{ $status->user->name }}" class="absolute left-2.5 top-2.5 size-7 rounded-full object-cover ring-2 ring-amber-400">
                                @else
                                    <span class="absolute left-2.5 top-2.5 flex size-7 items-center justify-center rounded-full bg-studio text-[10px] font-semibold text-gold ring-2 ring-amber-400">{{ $status->user->initials() }}</span>
                                @endif
                                <span class="absolute inset-x-2.5 bottom-2.5 text-xs font-bold leading-tight text-white drop-shadow truncate">{{ $status->user->name }}</span>
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

            @foreach ($topRankings as $item)
                @php
                    $ranking = $item['ranking'];
                    $leaders = $item['leaders'];
                @endphp
                <section class="overflow-hidden rounded-[1.5rem] border border-ink/8 bg-white shadow-sm">
                    <div class="relative overflow-hidden bg-gradient-to-br from-studio-deep via-studio to-black px-5 py-4 border-b border-gold/15">
                        <div class="relative z-10 flex items-center justify-between">
                            <div>
                                <h2 class="font-display text-lg tracking-tight text-paper">{{ $ranking->title }}</h2>
                                <p class="text-xs font-semibold text-gold uppercase tracking-wide">{{ $ranking->talent->category }}</p>
                            </div>
                            <x-icon name="trophy" class="size-6 text-gold" />
                        </div>
                        <div class="absolute -bottom-4 -right-4 size-16 rounded-full bg-gold/10"></div>
                    </div>
                    
                    @if ($leaders->isEmpty())
                        <div class="px-5 py-6 text-center text-xs text-mist">
                            No posts for this talent yet.
                        </div>
                    @else
                        <ol class="divide-y divide-ink/6">
                            @foreach ($leaders as $i => $student)
                                <li class="flex items-center gap-3 px-4 py-2.5" wire:key="feed-rank-{{ $ranking->id }}-{{ $student->id }}">
                                    <div class="flex w-5 shrink-0 items-center justify-center text-center">
                                        @if ($i === 0)
                                            <span class="flex size-5 items-center justify-center rounded-full bg-amber-500 text-[10px] font-bold text-white shadow-sm ring-1 ring-amber-400">1</span>
                                        @elseif ($i === 1)
                                            <span class="flex size-5 items-center justify-center rounded-full bg-slate-400 text-[10px] font-bold text-white shadow-sm ring-1 ring-slate-300">2</span>
                                        @elseif ($i === 2)
                                            <span class="flex size-5 items-center justify-center rounded-full bg-amber-700 text-[10px] font-bold text-white shadow-sm ring-1 ring-amber-600">3</span>
                                        @endif
                                    </div>
                                    <div class="flex size-7 shrink-0 items-center justify-center overflow-hidden rounded-full bg-studio text-[10px] font-semibold text-gold">
                                        @if ($student->profile?->avatar_path)
                                            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($student->profile->avatar_path) }}"
                                                 alt="{{ $student->name }}"
                                                 class="size-full object-cover rounded-full">
                                        @else
                                            {{ $student->initials() }}
                                        @endif
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <a href="{{ route('students.show', $student) }}"
                                           class="block truncate text-xs font-semibold hover:text-ember transition-colors"
                                           wire:navigate>
                                            {{ $student->name }}
                                        </a>
                                    </div>
                                    <div class="shrink-0 text-right">
                                        <p class="text-xs font-bold text-ember">{{ number_format($student->talent_likes_total ?? 0) }}</p>
                                    </div>
                                </li>
                            @endforeach
                        </ol>
                        <div class="border-t border-ink/6 px-4 py-2 text-center bg-wall/30">
                            <a href="{{ route('rankings') }}" class="text-[11px] font-semibold uppercase tracking-wider text-mist hover:text-ember transition-colors" wire:navigate>View Full Ranking</a>
                        </div>
                    @endif
                </section>
            @endforeach
        </aside>
    </div>
</div>
