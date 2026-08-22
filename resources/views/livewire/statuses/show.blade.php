<div class="fixed inset-0 z-40 bg-black/95 text-white flex items-center justify-center p-4 md:p-8">
    <div class="relative max-h-[90vh] max-w-5xl w-auto h-auto rounded-3xl bg-black/80 overflow-hidden shadow-2xl border border-white/10 flex flex-col justify-between p-6">
        {{-- Media Background (Natural Aspect Ratio) --}}
        <div class="absolute inset-0 z-0 flex items-center justify-center bg-black">
            @if ($status->isVideo())
                <video src="{{ $status->mediaUrl() }}" autoplay loop muted playsinline class="max-h-[90vh] max-w-full w-auto h-auto object-contain mx-auto"></video>
            @else
                <img src="{{ $status->imageUrl() }}" alt="" class="max-h-[90vh] max-w-full w-auto h-auto object-contain mx-auto">
            @endif
            <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-transparent to-black/50 pointer-events-none"></div>
        </div>

        {{-- Story Header --}}
        <div class="relative z-10 flex items-center justify-between">
            <a href="{{ route('students.show', $status->user) }}" class="flex items-center gap-3" wire:navigate>
                <span class="flex size-10 items-center justify-center rounded-full bg-studio text-xs font-bold text-gold ring-2 ring-amber-400 shadow-md">{{ $status->user->initials() }}</span>
                <div>
                    <span class="block font-bold text-white text-sm drop-shadow">{{ $status->user->name }}</span>
                    <span class="block text-xs text-white/80 drop-shadow">{{ $status->created_at->diffForHumans() }} · 24h Campus Story</span>
                </div>
            </a>
            
            <a href="{{ route('home') }}" class="rounded-full bg-black/60 px-4 py-2 text-xs font-bold text-white border border-white/20 backdrop-blur hover:bg-white/20 transition" wire:navigate>
                Close ✕
            </a>
        </div>

        {{-- Story Caption Footer --}}
        @if ($status->caption)
            <div class="relative z-10 max-w-xl rounded-2xl bg-black/70 p-4 backdrop-blur border border-white/15 shadow-lg">
                <p class="text-sm md:text-base font-medium leading-relaxed text-white drop-shadow">{{ $status->caption }}</p>
            </div>
        @endif
    </div>
</div>
