<div class="page-shell py-8" wire:poll.2s>

    <div class="mb-8 flex items-end justify-between gap-4">
        <div>
            <h1 class="font-display text-4xl tracking-tight">Campus Rankings</h1>
            <p class="mt-1 text-mist">Top performers ranked by likes on their talent posts.</p>
        </div>
    </div>

    @if ($rankingsWithLeaders->isEmpty())
        <div class="flex flex-col items-center justify-center rounded-3xl border border-dashed border-ink/15 bg-white py-24 text-center">
            <div class="mb-4 text-amber-500 flex justify-center">
                <x-icon name="trophy" class="size-16" />
            </div>
            <p class="text-xl font-semibold">No rankings yet</p>
            <p class="mt-2 text-mist">Your campus admin hasn't set up any talent rankings yet.</p>
        </div>
    @else
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($rankingsWithLeaders as $item)
                @php
                    $ranking = $item['ranking'];
                    $leaders = $item['leaders'];
                @endphp
                <div class="overflow-hidden rounded-3xl border border-ink/8 bg-white shadow-sm">

                    {{-- Card header --}}
                    <div class="relative overflow-hidden bg-gradient-to-br from-ember via-ember/80 to-amber-500 px-6 py-5">
                        <div class="relative z-10 flex items-start justify-between gap-4">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-widest text-white/60">{{ $ranking->talent->category }}</p>
                                <h2 class="mt-0.5 text-xl font-bold text-white">{{ $ranking->title }}</h2>
                                <p class="mt-1 text-sm text-white/70">{{ $ranking->talent->name }} · Top 10</p>
                            </div>
                            <x-icon name="trophy" class="size-10 text-white/60" />
                        </div>
                        {{-- Decorative circle --}}
                        <div class="absolute -bottom-6 -right-6 size-24 rounded-full bg-white/10"></div>
                    </div>

                    {{-- Leaderboard --}}
                    @if ($leaders->isEmpty())
                        <div class="px-6 py-10 text-center text-sm text-mist">
                            No posts with this talent yet.
                        </div>
                    @else
                        <ol class="divide-y divide-ink/6">
                            @foreach ($leaders as $i => $student)
                                <li class="flex items-center gap-3 px-5 py-3.5" wire:key="rank-{{ $ranking->id }}-{{ $student->id }}">

                                    {{-- Rank number / medal --}}
                                    <div class="flex w-7 shrink-0 items-center justify-center text-center">
                                        @if ($i === 0)
                                            <span class="flex size-6 items-center justify-center rounded-full bg-amber-500 text-xs font-bold text-white shadow-sm ring-2 ring-amber-300">1</span>
                                        @elseif ($i === 1)
                                            <span class="flex size-6 items-center justify-center rounded-full bg-slate-400 text-xs font-bold text-white shadow-sm ring-2 ring-slate-300">2</span>
                                        @elseif ($i === 2)
                                            <span class="flex size-6 items-center justify-center rounded-full bg-amber-700 text-xs font-bold text-white shadow-sm ring-2 ring-amber-600">3</span>
                                        @else
                                            <span class="text-sm font-bold text-mist">{{ $i + 1 }}</span>
                                        @endif
                                    </div>

                                    {{-- Avatar --}}
                                    <div class="flex size-9 shrink-0 items-center justify-center overflow-hidden rounded-full bg-studio text-xs font-semibold text-gold">
                                        @if ($student->profile?->avatar_path)
                                            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($student->profile->avatar_path) }}"
                                                 alt="{{ $student->name }}"
                                                 class="size-full object-cover rounded-full">
                                        @else
                                            {{ $student->initials() }}
                                        @endif
                                    </div>

                                    {{-- Name & info --}}
                                    <div class="min-w-0 flex-1">
                                        <a href="{{ route('students.show', $student) }}"
                                           class="block truncate text-sm font-semibold hover:text-ember transition-colors"
                                           wire:navigate>
                                            {{ $student->name }}
                                        </a>
                                        @if ($student->profile?->headline)
                                            <p class="truncate text-xs text-mist">{{ $student->profile->headline }}</p>
                                        @endif
                                    </div>

                                    {{-- Like count --}}
                                    <div class="shrink-0 text-right">
                                        <p class="text-sm font-bold text-ember">{{ number_format($student->talent_likes_total ?? 0) }}</p>
                                        <p class="text-[10px] text-mist">likes</p>
                                    </div>
                                </li>
                            @endforeach
                        </ol>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
