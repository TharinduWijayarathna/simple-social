<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? 'VibeCraft' }} — {{ config('app.name', 'VibeCraft') }}</title>
        <link rel="icon" href="/favicon.ico" sizes="any">
        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="min-h-screen bg-wall text-ink">
        @auth
            <header class="sticky top-0 z-20 border-b border-ink/10 bg-white/95 backdrop-blur">
                <div class="page-shell flex items-center justify-between gap-4 py-2.5">
                    <a href="{{ route('home') }}" class="font-display text-2xl tracking-tight text-studio" wire:navigate>VibeCraft</a>
                    <nav class="hidden items-center gap-1 md:flex">
                        <x-nav-icon :href="route('home')" icon="home" label="Home" :active="request()->routeIs('home')" />
                        <x-nav-icon :href="route('students.index')" icon="people" label="People" :active="request()->routeIs('students.index')" />
                        <x-nav-icon :href="route('events.index')" icon="calendar" label="Events" :active="request()->routeIs('events.*')" />
                        @if (auth()->user()->canOrganizeEvents())
                            <x-nav-icon :href="route('campus.dashboard')" icon="building" label="Campus" :active="request()->routeIs('campus.*')" />
                        @endif
                        @if (auth()->user()->isSuperAdmin())
                            <x-nav-icon :href="route('admin.dashboard')" icon="shield" label="Admin" :active="request()->routeIs('admin.*')" />
                        @endif
                    </nav>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('portfolio.create') }}" class="flex size-10 items-center justify-center rounded-full bg-ember text-white transition hover:bg-ember/90 {{ request()->routeIs('portfolio.create') ? 'ring-2 ring-ember/30' : '' }}" title="Post" aria-label="Post" wire:navigate>
                            <x-icon name="plus" class="size-5" />
                        </a>
                        <a href="{{ route('profile.show') }}" class="flex size-9 items-center justify-center rounded-full bg-studio text-xs font-semibold text-gold {{ request()->routeIs('students.show') ? 'ring-2 ring-ember ring-offset-2' : '' }}" title="Profile" aria-label="Profile" wire:navigate>{{ auth()->user()->initials() }}</a>
                        <x-logout-button />
                    </div>
                </div>
            </header>
        @else
            <header class="sticky top-0 z-20 border-b border-ink/10 bg-white">
                <div class="page-shell flex items-center justify-between py-3">
                    <a href="{{ route('home') }}" class="font-display text-2xl tracking-tight text-studio" wire:navigate>VibeCraft</a>
                    <div class="flex items-center gap-3 text-sm">
                        <a href="{{ route('login') }}" class="hover:text-ember" wire:navigate>Sign in</a>
                        <a href="{{ route('register') }}" class="btn-primary" wire:navigate>Sign up</a>
                    </div>
                </div>
            </header>
        @endauth

        <main class="pb-20 md:pb-8">
            {{ $slot }}
        </main>

        @auth
            <nav class="fixed inset-x-0 bottom-0 z-30 grid grid-cols-5 border-t border-ink/10 bg-white/95 px-2 py-1.5 backdrop-blur md:hidden">
                <x-nav-icon :href="route('home')" icon="home" label="Home" :active="request()->routeIs('home')" class="mx-auto" />
                <x-nav-icon :href="route('students.index')" icon="people" label="People" :active="request()->routeIs('students.index')" class="mx-auto" />
                <a href="{{ route('portfolio.create') }}" class="mx-auto flex size-10 items-center justify-center rounded-full bg-ember text-white {{ request()->routeIs('portfolio.create') ? 'ring-2 ring-ember/30' : '' }}" title="Post" aria-label="Post" wire:navigate>
                    <x-icon name="plus" class="size-5" />
                </a>
                <x-nav-icon :href="route('events.index')" icon="calendar" label="Events" :active="request()->routeIs('events.*')" class="mx-auto" />
                <x-nav-icon :href="route('profile.show')" icon="user" label="Profile" :active="request()->routeIs('students.show')" class="mx-auto" />
            </nav>
        @endauth

        @livewireScripts
    </body>
</html>
