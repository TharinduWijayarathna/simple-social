<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? 'Admin' }} — VibeCraft</title>
        <link rel="icon" href="/favicon.ico" sizes="any">
        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="min-h-screen bg-studio-deep text-paper antialiased">
        <div class="flex min-h-screen">

            {{-- Sidebar --}}
            <aside class="hidden w-64 flex-shrink-0 flex-col border-r border-white/8 bg-studio lg:flex">
                <div class="flex h-16 items-center gap-2.5 border-b border-white/8 px-6">
                    <span class="font-display text-xl text-gold">VibeCraft</span>
                    <span class="rounded-md bg-gold/15 px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wider text-gold">Admin</span>
                </div>

                <nav class="flex-1 px-3 py-5 space-y-0.5">
                    <p class="mb-2 px-3 text-[10px] font-semibold uppercase tracking-[0.18em] text-white/30">Platform</p>
                    <a href="{{ route('admin.dashboard') }}"
                       class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition
                              {{ request()->routeIs('admin.dashboard') ? 'bg-white/10 text-gold' : 'text-white/60 hover:bg-white/8 hover:text-white' }}"
                       wire:navigate>
                        <svg class="size-4.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Overview
                    </a>
                    <a href="{{ route('admin.dashboard', ['tab' => 'students']) }}"
                       class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition
                              {{ request()->query('tab') === 'students' ? 'bg-white/10 text-gold' : 'text-white/60 hover:bg-white/8 hover:text-white' }}"
                       wire:navigate>
                        <svg class="size-4.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        Student Management
                    </a>
                    <a href="{{ route('admin.dashboard', ['tab' => 'campuses']) }}"
                       class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition
                              {{ request()->query('tab') === 'campuses' ? 'bg-white/10 text-gold' : 'text-white/60 hover:bg-white/8 hover:text-white' }}"
                       wire:navigate>
                        <svg class="size-4.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        Campus Management
                    </a>
                    <a href="{{ route('admin.dashboard', ['tab' => 'users']) }}"
                       class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition
                              {{ request()->query('tab') === 'users' ? 'bg-white/10 text-gold' : 'text-white/60 hover:bg-white/8 hover:text-white' }}"
                       wire:navigate>
                        <svg class="size-4.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Users
                    </a>
                    <a href="{{ route('admin.dashboard', ['tab' => 'moderation']) }}"
                       class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition
                              {{ request()->query('tab') === 'moderation' ? 'bg-white/10 text-gold' : 'text-white/60 hover:bg-white/8 hover:text-white' }}"
                       wire:navigate>
                        <svg class="size-4.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                        </svg>
                        Moderation
                    </a>
                    <a href="{{ route('admin.dashboard', ['tab' => 'analytics']) }}"
                       class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition
                              {{ request()->query('tab') === 'analytics' ? 'bg-white/10 text-gold' : 'text-white/60 hover:bg-white/8 hover:text-white' }}"
                       wire:navigate>
                        <svg class="size-4.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v18h18M8 17V9m5 8V5m5 12v-6" />
                        </svg>
                        Analytics
                    </a>
                    <a href="{{ route('admin.dashboard', ['tab' => 'settings']) }}"
                       class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition
                              {{ request()->query('tab') === 'settings' ? 'bg-white/10 text-gold' : 'text-white/60 hover:bg-white/8 hover:text-white' }}"
                       wire:navigate>
                        <svg class="size-4.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065zM15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Settings
                    </a>
                </nav>

                <div class="border-t border-white/8 px-4 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="flex size-8 items-center justify-center overflow-hidden rounded-full bg-gold/20 text-xs font-semibold text-gold">
                                @if (auth()->user()->avatarUrl())
                                    <img src="{{ auth()->user()->avatarUrl() }}" alt="{{ auth()->user()->name }}" class="size-full object-cover rounded-full">
                                @else
                                    {{ auth()->user()->initials() }}
                                @endif
                            </div>
                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium text-paper">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-white/40">Super Admin</p>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="flex size-8 items-center justify-center rounded-lg text-white/40 transition hover:bg-white/10 hover:text-white"
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
            <div class="fixed inset-x-0 top-0 z-30 flex h-14 items-center justify-between border-b border-white/8 bg-studio px-4 lg:hidden">
                <span class="font-display text-lg text-gold">VibeCraft <span class="text-xs font-sans text-white/40">Admin</span></span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-white/50 hover:text-white">
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </button>
                </form>
            </div>

            {{-- Main content --}}
            <main class="flex-1 overflow-y-auto pt-14 lg:pt-0">
                <div class="min-h-full bg-wall text-ink">
                    {{ $slot }}
                </div>
            </main>
        </div>

        @livewireScripts
    </body>
</html>
