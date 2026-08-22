<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? 'VibeCraft Admin' }} — {{ config('app.name', 'VibeCraft') }}</title>
        <link rel="icon" href="/favicon.ico" sizes="any">
        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="min-h-screen bg-studio text-paper">
        <div class="flex min-h-screen flex-col items-center justify-center px-6 py-12">
            <div class="w-full max-w-sm">
                <p class="mb-8 text-center font-display text-3xl text-gold">VibeCraft</p>
                {{ $slot }}
            </div>
        </div>
        @livewireScripts
    </body>
</html>
