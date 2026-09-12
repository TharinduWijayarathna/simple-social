<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $title }} · {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-canvas text-ink antialiased">
    <main class="flex min-h-screen items-center justify-center px-6 py-16">
        <section class="w-full max-w-2xl overflow-hidden rounded-3xl border border-ink/10 bg-white shadow-xl shadow-ink/5">
            <div class="h-2 bg-ember"></div>
            <div class="px-7 py-12 text-center sm:px-14 sm:py-16">
                <div class="mx-auto flex size-16 items-center justify-center rounded-2xl bg-ember/10 text-2xl text-ember">✦</div>
                <p class="mt-7 text-xs font-bold uppercase tracking-[0.25em] text-ember">Scheduled platform work</p>
                <h1 class="mt-3 font-serif text-4xl leading-tight sm:text-5xl">{{ $title }}</h1>
                <p class="mx-auto mt-5 max-w-lg text-base leading-7 text-mist">{{ $message }}</p>
                @if (filled($supportEmail))
                    <p class="mt-8 text-sm text-mist">Need help? <a href="mailto:{{ $supportEmail }}" class="font-semibold text-ember hover:underline">{{ $supportEmail }}</a></p>
                @endif
                <p class="mt-10 text-xs text-mist">Please check back soon.</p>
            </div>
        </section>
    </main>
</body>
</html>
