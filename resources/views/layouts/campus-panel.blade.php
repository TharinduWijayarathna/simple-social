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
        <div class="flex min-h-screen lg:h-screen lg:overflow-hidden">

            {{-- Sidebar --}}
            <aside class="hidden w-60 flex-none flex-col border-r border-ink/10 bg-white lg:sticky lg:top-0 lg:flex lg:h-screen">
                <div class="flex h-16 shrink-0 items-center gap-2 border-b border-ink/8 px-5">
                    <span class="font-display text-xl text-studio">VibeCraft</span>
                    <span class="rounded-md bg-ember/10 px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wider text-ember">Campus</span>
                </div>

                <nav class="min-h-0 flex-1 overflow-y-auto px-3 py-5 space-y-0.5">
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
                    <a href="{{ route('campus.dashboard', ['tab' => 'talents']) }}"
                       class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition
                              {{ request()->query('tab') === 'talents' ? 'bg-ember/8 text-ember' : 'text-ink/60 hover:bg-ink/5 hover:text-ink' }}"
                       wire:navigate>
                        <x-icon name="sparkles" class="size-4.5 shrink-0" />
                        Talent Management
                    </a>
                    <a href="{{ route('campus.dashboard', ['tab' => 'moderation']) }}"
                       class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition
                              {{ request()->query('tab') === 'moderation' ? 'bg-ember/8 text-ember' : 'text-ink/60 hover:bg-ink/5 hover:text-ink' }}"
                       wire:navigate>
                        <svg class="size-4.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                        </svg>
                        Moderation
                    </a>
                    <a href="{{ route('campus.dashboard', ['tab' => 'analytics']) }}"
                       class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition
                              {{ request()->query('tab') === 'analytics' ? 'bg-ember/8 text-ember' : 'text-ink/60 hover:bg-ink/5 hover:text-ink' }}"
                       wire:navigate>
                        <svg class="size-4.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v18h18M8 17V9m5 8V5m5 12v-6" />
                        </svg>
                        Analytics
                    </a>
                    <a href="{{ route('campus.rankings') }}"
                       class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition
                              {{ request()->routeIs('campus.rankings') ? 'bg-ember/8 text-ember' : 'text-ink/60 hover:bg-ink/5 hover:text-ink' }}"
                       wire:navigate>
                        <svg class="size-4.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 007.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 002.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 012.916.52 6.003 6.003 0 01-5.395 4.972m0 0a6.726 6.726 0 01-2.749 1.35m0 0a6.772 6.772 0 01-3.044 0" />
                        </svg>
                        Rankings
                    </a>
                    <a href="{{ route('campus.dashboard', ['tab' => 'announcement']) }}"
                       class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition
                              {{ request()->query('tab') === 'announcement' ? 'bg-ember/8 text-ember' : 'text-ink/60 hover:bg-ink/5 hover:text-ink' }}"
                       wire:navigate>
                        <svg class="size-4.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065zM15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Announcement
                    </a>
                </nav>

                <div class="border-t border-ink/8 px-4 py-4">
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex min-w-0 flex-1 items-center gap-2.5">
                            <div class="flex size-8 shrink-0 items-center justify-center overflow-hidden rounded-full bg-studio text-xs font-semibold text-gold">
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
                        <form method="POST" action="{{ route('logout') }}" class="shrink-0">
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
                <div class="flex shrink-0 items-center gap-3">
                    <span class="font-display text-lg text-studio">VibeCraft</span>
                    <span class="rounded-md bg-ember/10 px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wider text-ember">Campus</span>
                </div>
                <div class="min-w-0 flex-1 overflow-x-auto">
                    <nav class="flex min-w-max items-center gap-3">
                        <a href="{{ route('campus.dashboard') }}" class="text-xs {{ request()->routeIs('campus.dashboard') && ! request()->query('tab') ? 'font-medium text-ember' : 'text-mist' }}">Overview</a>
                        <a href="{{ route('campus.dashboard', ['tab' => 'students']) }}" class="text-xs {{ request()->query('tab') === 'students' ? 'text-ember font-medium' : 'text-mist' }}">Students</a>
                        <a href="{{ route('campus.dashboard', ['tab' => 'events']) }}" class="text-xs {{ request()->query('tab') === 'events' ? 'text-ember font-medium' : 'text-mist' }}">Events</a>
                        <a href="{{ route('campus.dashboard', ['tab' => 'talents']) }}" class="text-xs {{ request()->query('tab') === 'talents' ? 'text-ember font-medium' : 'text-mist' }}">Talents</a>
                        <a href="{{ route('campus.dashboard', ['tab' => 'moderation']) }}" class="text-xs {{ request()->query('tab') === 'moderation' ? 'text-ember font-medium' : 'text-mist' }}">Moderation</a>
                        <a href="{{ route('campus.dashboard', ['tab' => 'analytics']) }}" class="text-xs {{ request()->query('tab') === 'analytics' ? 'text-ember font-medium' : 'text-mist' }}">Analytics</a>
                        <a href="{{ route('campus.dashboard', ['tab' => 'announcement']) }}" class="text-xs {{ request()->query('tab') === 'announcement' ? 'text-ember font-medium' : 'text-mist' }}">Announcement</a>
                        <a href="{{ route('campus.rankings') }}" class="text-xs {{ request()->routeIs('campus.rankings') ? 'text-ember font-medium' : 'text-mist' }}">Rankings</a>
                    </nav>
                </div>
                <div class="shrink-0">
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
            <main class="min-w-0 flex-1 overflow-y-auto pt-14 lg:pt-0">
                {{ $slot }}
            </main>
        </div>

        @livewireScripts
    </body>
</html>
