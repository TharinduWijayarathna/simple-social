<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>VibeCraft — Campus talent gallery</title>
        <link rel="icon" href="/favicon.ico" sizes="any">
        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-studio text-paper">
        <header class="mx-auto flex max-w-6xl items-center justify-between px-6 py-6">
            <p class="font-display text-2xl tracking-tight text-gold">VibeCraft</p>
            <nav class="flex items-center gap-4 text-sm">
                @auth
                    <a href="{{ route('home') }}" class="hover:text-gold">Open home</a>
                @else
                    <a href="{{ route('login') }}" class="hover:text-gold">Sign in</a>
                    <a href="{{ route('register') }}" class="btn-primary">Join campus</a>
                @endauth
            </nav>
        </header>

        <main class="mx-auto max-w-6xl px-6 pb-24 pt-10">
            <div class="grid gap-12 lg:grid-cols-[1.1fr_0.9fr] lg:items-center">
                <div>
                    <p class="text-sm uppercase tracking-[0.28em] text-gold">Your university. Your gallery.</p>
                    <h1 class="mt-4 font-display text-5xl leading-[1.05] lg:text-6xl">Show the campus what you make.</h1>
                    <p class="mt-5 max-w-xl text-lg text-mist">A private social gallery for students — art walls, listening rooms, stages, lookbooks. Like Instagram, but only your campus, and every talent gets its own space.</p>
                    <div class="mt-8 flex flex-wrap gap-3">
                        <a href="{{ route('register') }}" class="btn-primary">Create your studio</a>
                        <a href="{{ route('login') }}" class="btn-ghost border-white/20 text-paper hover:bg-white/10">Sign in</a>
                    </div>
                    <ul class="mt-10 grid gap-3 text-sm text-mist sm:grid-cols-3">
                        <li class="rounded-2xl border border-white/10 bg-white/5 p-4">Art, music, dance, film, fashion — themed profiles</li>
                        <li class="rounded-2xl border border-white/10 bg-white/5 p-4">A campus feed with like, comment, and share</li>
                        <li class="rounded-2xl border border-white/10 bg-white/5 p-4">Campus events you can actually join</li>
                    </ul>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <img src="https://picsum.photos/seed/vc-art/600/800" alt="" class="h-72 w-full rounded-[1.75rem] object-cover">
                    <img src="https://picsum.photos/seed/vc-stage/600/500" alt="" class="mt-10 h-56 w-full rounded-[1.75rem] object-cover">
                    <img src="https://picsum.photos/seed/vc-film/600/500" alt="" class="-mt-8 h-56 w-full rounded-[1.75rem] object-cover">
                    <img src="https://picsum.photos/seed/vc-look/600/800" alt="" class="h-72 w-full rounded-[1.75rem] object-cover">
                </div>
            </div>
        </main>
    </body>
</html>
