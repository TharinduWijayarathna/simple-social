<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <title>VibeCraft Watch</title>
        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="min-h-screen bg-black text-white">
        <main class="mx-auto flex min-h-screen max-w-xs flex-col items-center justify-center p-4 text-center">
            {{ $slot }}
        </main>
        @livewireScripts
    </body>
</html>
