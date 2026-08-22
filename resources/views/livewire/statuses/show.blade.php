<div class="fixed inset-0 z-40 bg-black text-white">
    <div class="absolute inset-0">
        @if ($status->isVideo())
            <video src="{{ $status->mediaUrl() }}" autoplay loop muted playsinline class="size-full object-cover"></video>
        @else
            <img src="{{ $status->imageUrl() }}" alt="" class="size-full object-cover">
        @endif
        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-black/40"></div>
    </div>
    <div class="relative flex h-full flex-col justify-between p-5 md:p-8">
        <div class="flex items-center justify-between">
            <a href="{{ route('students.show', $status->user) }}" class="flex items-center gap-3" wire:navigate>
                <span class="flex size-10 items-center justify-center rounded-full bg-studio text-xs font-semibold text-gold ring-2 ring-amber-400">{{ $status->user->initials() }}</span>
                <span>
                    <span class="block font-semibold text-white drop-shadow">{{ $status->user->name }}</span>
                    <span class="block text-xs text-white/80 drop-shadow">{{ $status->created_at->diffForHumans() }} · 24h Campus Story</span>
                </span>
            </a>
            <a href="{{ route('home') }}" class="rounded-full bg-white/20 px-4 py-1.5 text-xs font-bold text-white backdrop-blur hover:bg-white/30 transition" wire:navigate>Close ✕</a>
        </div>
        @if ($status->caption)
            <div class="max-w-xl rounded-2xl bg-black/40 p-4 backdrop-blur border border-white/10">
                <p class="text-base font-semibold leading-relaxed text-white drop-shadow">{{ $status->caption }}</p>
            </div>
        @endif
    </div>
</div>
