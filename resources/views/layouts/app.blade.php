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
        <div class="min-h-screen @auth lg:grid lg:grid-cols-[16.5rem_minmax(0,1fr)] @endauth">
            @auth
                <aside class="sticky top-0 z-20 hidden h-screen flex-col border-r border-ink/8 bg-studio text-paper lg:flex">
                    <div class="px-6 pt-8">
                        <a href="{{ route('home') }}" class="font-display text-2xl tracking-tight text-gold" wire:navigate>VibeCraft</a>
                        <p class="mt-1 text-xs text-mist">Campus talent social</p>
                    </div>
                    <nav class="mt-8 flex flex-1 flex-col gap-1 px-3">
                        <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'nav-link-active' : '' }}" wire:navigate>Home</a>
                        <a href="{{ route('portfolio.index') }}" class="nav-link {{ request()->routeIs('portfolio.*') ? 'nav-link-active' : '' }}" wire:navigate>Explore</a>
                        <a href="{{ route('students.index') }}" class="nav-link {{ request()->routeIs('students.*') ? 'nav-link-active' : '' }}" wire:navigate>People</a>
                        <a href="{{ route('events.index') }}" class="nav-link {{ request()->routeIs('events.*') ? 'nav-link-active' : '' }}" wire:navigate>Events</a>
                        <a href="{{ route('collaborations.index') }}" class="nav-link {{ request()->routeIs('collaborations.*') ? 'nav-link-active' : '' }}" wire:navigate>Collabs</a>
                        <a href="{{ route('leaderboard') }}" class="nav-link {{ request()->routeIs('leaderboard') ? 'nav-link-active' : '' }}" wire:navigate>Leaderboard</a>
                        <a href="{{ route('portfolio.create') }}" class="nav-link {{ request()->routeIs('portfolio.create') ? 'nav-link-active' : '' }}" wire:navigate>Post work</a>
                        <a href="{{ route('profile.edit') }}" class="nav-link {{ request()->routeIs('profile.*') ? 'nav-link-active' : '' }}" wire:navigate>Profile</a>
                        @if (auth()->user()->canOrganizeEvents())
                            <a href="{{ route('campus.dashboard') }}" class="nav-link text-gold {{ request()->routeIs('campus.*') ? 'nav-link-active' : '' }}" wire:navigate>Campus desk</a>
                        @endif
                        @if (auth()->user()->isSuperAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="nav-link text-gold {{ request()->routeIs('admin.*') ? 'nav-link-active' : '' }}" wire:navigate>Super admin</a>
                        @endif
                    </nav>
                    <div class="px-5 py-6">
                        <p class="text-sm font-medium">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-mist">{{ auth()->user()->role->label() }}</p>
                        <form method="POST" action="{{ route('logout') }}" class="mt-3">
                            @csrf
                            <button type="submit" class="text-sm text-mist hover:text-paper">Sign out</button>
                        </form>
                    </div>
                </aside>
            @else
                <header class="sticky top-0 z-20 flex items-center justify-between border-b border-ink/10 bg-white px-5 py-3">
                    <a href="{{ route('home') }}" class="font-display text-2xl tracking-tight text-studio" wire:navigate>VibeCraft</a>
                    <div class="flex items-center gap-3 text-sm">
                        <a href="{{ route('login') }}" class="hover:text-ember" wire:navigate>Sign in</a>
                        <a href="{{ route('register') }}" class="btn-primary" wire:navigate>Join campus</a>
                    </div>
                </header>
            @endauth

            <div class="flex min-h-screen flex-col pb-20 lg:pb-0">
                @auth
                    <header class="flex items-center justify-between border-b border-ink/8 bg-wall/80 px-4 py-3 backdrop-blur lg:hidden">
                        <a href="{{ route('home') }}" class="font-display text-xl text-studio" wire:navigate>VibeCraft</a>
                        <a href="{{ route('profile.edit') }}" class="text-sm" wire:navigate>{{ auth()->user()->initials() }}</a>
                    </header>
                @endauth
                <main class="flex-1">
                    {{ $slot }}
                </main>
            </div>
        </div>

        @auth
            <nav class="fixed inset-x-0 bottom-0 z-30 grid grid-cols-5 border-t border-ink/10 bg-white/95 px-2 py-2 text-[11px] font-medium text-mist backdrop-blur lg:hidden">
                <a href="{{ route('home') }}" class="flex flex-col items-center gap-1 {{ request()->routeIs('home') ? 'text-ember' : '' }}" wire:navigate>Home</a>
                <a href="{{ route('portfolio.index') }}" class="flex flex-col items-center gap-1 {{ request()->routeIs('portfolio.*') ? 'text-ember' : '' }}" wire:navigate>Explore</a>
                <a href="{{ route('portfolio.create') }}" class="flex flex-col items-center gap-1 {{ request()->routeIs('portfolio.create') ? 'text-ember' : '' }}" wire:navigate>Post</a>
                <a href="{{ route('events.index') }}" class="flex flex-col items-center gap-1 {{ request()->routeIs('events.*') ? 'text-ember' : '' }}" wire:navigate>Events</a>
                <a href="{{ route('students.index') }}" class="flex flex-col items-center gap-1 {{ request()->routeIs('students.*') ? 'text-ember' : '' }}" wire:navigate>People</a>
            </nav>
        @endauth

        @livewireScripts
    </body>
</html>
