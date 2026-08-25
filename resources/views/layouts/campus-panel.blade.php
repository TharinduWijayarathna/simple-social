<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? 'Campus' }} — VibeCraft</title>
        <link rel="icon" href="/favicon.ico" sizes="any">
        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="min-h-screen bg-wall text-ink antialiased">
        <div class="flex min-h-screen">

            {{-- Sidebar --}}
            <aside class="hidden w-60 flex-shrink-0 flex-col border-r border-ink/10 bg-white lg:flex">
                <div class="flex h-16 items-center gap-2 border-b border-ink/8 px-5">
                    <span class="font-display text-xl text-studio">VibeCraft</span>
                    <span class="rounded-md bg-ember/10 px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wider text-ember">Campus</span>
                </div>

                <nav class="flex-1 px-3 py-5 space-y-0.5">
                    <p class="mb-2 px-3 text-[10px] font-semibold uppercase tracking-[0.18em] text-mist">Management</p>
                    <a href="{{ route('campus.dashboard') }}"
                       class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition
                              {{ request()->routeIs('campus.dashboard') && ! request()->query('tab') ? 'bg-ember/8 text-ember' : 'text-ink/60 hover:bg-ink/5 hover:text-ink' }}"
                       wire:navigate>
                        <svg class="size-4.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Overview
                    </a>
                    <a href="{{ route('campus.dashboard', ['tab' => 'students']) }}"
                       class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition
                              {{ request()->query('tab') === 'students' ? 'bg-ember/8 text-ember' : 'text-ink/60 hover:bg-ink/5 hover:text-ink' }}"
                       wire:navigate>
                        <svg class="size-4.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        Student Management
                    </a>
                    <a href="{{ route('campus.dashboard', ['tab' => 'events']) }}"
                       class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition
                              {{ request()->query('tab') === 'events' ? 'bg-ember/8 text-ember' : 'text-ink/60 hover:bg-ink/5 hover:text-ink' }}"
                       wire:navigate>
                        <svg class="size-4.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Event Management
                    </a>
                </nav>

                <div class="border-t border-ink/8 px-4 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="flex size-8 items-center justify-center overflow-hidden rounded-full bg-studio text-xs font-semibold text-gold">
                                @if (auth()->user()->avatarUrl())
                                    <img src="{{ auth()->user()->avatarUrl() }}" alt="{{ auth()->user()->name }}" class="size-full object-cover rounded-full">
                                @else
                                    {{ auth()->user()->initials() }}
                                @endif
                            </div>
                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-mist">Campus Admin</p>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="flex size-8 items-center justify-center rounded-lg text-mist transition hover:bg-ink/5 hover:text-ink"
                                    title="Sign out">
                                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </aside>

            {{-- Mobile top bar --}}
            <div class="fixed inset-x-0 top-0 z-30 flex h-14 items-center justify-between border-b border-ink/10 bg-white px-4 lg:hidden">
                <div class="flex items-center gap-3">
                    <span class="font-display text-lg text-studio">VibeCraft</span>
                    <span class="rounded-md bg-ember/10 px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wider text-ember">Campus</span>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('campus.dashboard', ['tab' => 'students']) }}" class="text-xs text-mist">Students</a>
                    <a href="{{ route('campus.dashboard', ['tab' => 'events']) }}" class="text-xs text-mist">Events</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-mist hover:text-ink">
                            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>

            {{-- Main content --}}
            <main class="flex-1 overflow-y-auto pt-14 lg:pt-0">
                {{ $slot }}
            </main>
        </div>

        @livewireScripts
    </body>
</html>
