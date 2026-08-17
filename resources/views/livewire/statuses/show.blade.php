<div class="fixed inset-0 z-40 bg-black text-white">
    <div class="absolute inset-0">
        <img src="{{ $status->imageUrl() }}" alt="" class="size-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-black/40"></div>
    </div>
    <div class="relative flex h-full flex-col justify-between p-5">
        <div class="flex items-center justify-between">
            <a href="{{ route('students.show', $status->user) }}" class="flex items-center gap-3" wire:navigate>
                <span class="flex size-10 items-center justify-center rounded-full bg-studio text-xs font-semibold text-gold ring-2 ring-sky-400">{{ $status->user->initials() }}</span>
                <span>
                    <span class="block font-semibold">{{ $status->user->name }}</span>
                    <span class="block text-xs text-white/70">{{ $status->created_at->diffForHumans() }} · disappears {{ $status->expires_at->diffForHumans() }}</span>
                </span>
            </a>
            <a href="{{ route('home') }}" class="rounded-full bg-white/15 px-3 py-1 text-sm" wire:navigate>Close</a>
        </div>
        @if ($status->caption)
            <p class="text-lg font-medium">{{ $status->caption }}</p>
        @endif
    </div>
</div>
