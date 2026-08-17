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
                <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-2.5">
                    <a href="{{ route('home') }}" class="font-display text-2xl tracking-tight text-studio" wire:navigate>VibeCraft</a>
                    <nav class="hidden items-center gap-1 md:flex">
                        <a href="{{ route('home') }}" class="rounded-full px-4 py-2 text-sm font-medium {{ request()->routeIs('home') ? 'bg-wall text-ember' : 'text-mist hover:bg-wall hover:text-ink' }}" wire:navigate>Home</a>
                        <a href="{{ route('students.index') }}" class="rounded-full px-4 py-2 text-sm font-medium {{ request()->routeIs('students.*') ? 'bg-wall text-ember' : 'text-mist hover:bg-wall hover:text-ink' }}" wire:navigate>People</a>
                        <a href="{{ route('events.index') }}" class="rounded-full px-4 py-2 text-sm font-medium {{ request()->routeIs('events.*') ? 'bg-wall text-ember' : 'text-mist hover:bg-wall hover:text-ink' }}" wire:navigate>Events</a>
                        @if (auth()->user()->canOrganizeEvents())
                            <a href="{{ route('campus.dashboard') }}" class="rounded-full px-4 py-2 text-sm font-medium {{ request()->routeIs('campus.*') ? 'bg-wall text-ember' : 'text-mist hover:bg-wall hover:text-ink' }}" wire:navigate>Campus</a>
                        @endif
                        @if (auth()->user()->isSuperAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="rounded-full px-4 py-2 text-sm font-medium {{ request()->routeIs('admin.*') ? 'bg-wall text-ember' : 'text-mist hover:bg-wall hover:text-ink' }}" wire:navigate>Admin</a>
                        @endif
                    </nav>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('portfolio.create') }}" class="btn-primary" wire:navigate>Post</a>
                        <a href="{{ route('profile.show') }}" class="flex size-9 items-center justify-center rounded-full bg-studio text-xs font-semibold text-gold" wire:navigate>{{ auth()->user()->initials() }}</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-sm text-mist hover:text-ink">Sign out</button>
                        </form>
                    </div>
                </div>
            </header>
        @else
            <header class="sticky top-0 z-20 flex items-center justify-between border-b border-ink/10 bg-white px-5 py-3">
                <a href="{{ route('home') }}" class="font-display text-2xl tracking-tight text-studio" wire:navigate>VibeCraft</a>
                <div class="flex items-center gap-3 text-sm">
                    <a href="{{ route('login') }}" class="hover:text-ember" wire:navigate>Sign in</a>
                    <a href="{{ route('register') }}" class="btn-primary" wire:navigate>Join campus</a>
                </div>
            </header>
        @endauth

        <main class="pb-20 md:pb-8">
            {{ $slot }}
        </main>

        @auth
            <nav class="fixed inset-x-0 bottom-0 z-30 grid grid-cols-5 border-t border-ink/10 bg-white/95 px-2 py-2 text-[11px] font-medium text-mist backdrop-blur md:hidden">
                <a href="{{ route('home') }}" class="flex flex-col items-center gap-1 {{ request()->routeIs('home') ? 'text-ember' : '' }}" wire:navigate>Home</a>
                <a href="{{ route('students.index') }}" class="flex flex-col items-center gap-1 {{ request()->routeIs('students.index') ? 'text-ember' : '' }}" wire:navigate>People</a>
                <a href="{{ route('portfolio.create') }}" class="flex flex-col items-center gap-1 {{ request()->routeIs('portfolio.create') ? 'text-ember' : '' }}" wire:navigate>Post</a>
                <a href="{{ route('events.index') }}" class="flex flex-col items-center gap-1 {{ request()->routeIs('events.*') ? 'text-ember' : '' }}" wire:navigate>Events</a>
                <a href="{{ route('profile.show') }}" class="flex flex-col items-center gap-1 {{ request()->routeIs('students.show') ? 'text-ember' : '' }}" wire:navigate>Profile</a>
            </nav>
        @endauth

        @livewireScripts
    </body>
</html>
