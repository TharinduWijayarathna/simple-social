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
    <body class="min-h-screen bg-studio text-paper">
        <div class="grid min-h-screen lg:grid-cols-2">
            <aside class="relative hidden overflow-hidden lg:block">
                <img src="https://picsum.photos/seed/vibecraft-hall/1400/1800" alt="" class="absolute inset-0 size-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-studio via-studio/40 to-studio/20"></div>
                <div class="relative flex h-full flex-col justify-between p-10">
                    <a href="{{ route('home') }}" class="font-display text-3xl text-gold">VibeCraft</a>
                    <div>
                        <p class="text-sm uppercase tracking-[0.28em] text-gold">Campus only</p>
                        <h1 class="mt-3 max-w-md font-display text-5xl leading-tight">A gallery for every talent on campus.</h1>
                    </div>
                </div>
            </aside>
            <main class="flex min-h-screen flex-col justify-center bg-wall px-6 py-12 text-ink">
                <div class="mx-auto w-full max-w-md">
                    <a href="{{ route('home') }}" class="font-display text-2xl text-studio lg:hidden">VibeCraft</a>
                    {{ $slot }}
                </div>
            </main>
        </div>
        @livewireScripts
    </body>
</html>
