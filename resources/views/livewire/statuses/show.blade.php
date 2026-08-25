<div class="fixed inset-0 z-50 bg-black/95 text-white flex items-center justify-center p-2 sm:p-4">
    <div class="relative h-[88vh] w-full max-w-md sm:max-w-lg rounded-3xl bg-black overflow-hidden shadow-2xl border border-white/15 flex flex-col justify-between p-5">
        {{-- Media Background --}}
        <div class="absolute inset-0 z-0 flex items-center justify-center bg-black">
            @if ($status->isVideo())
                <video src="{{ $status->mediaUrl() }}" autoplay loop muted playsinline class="size-full object-contain mx-auto"></video>
            @else
                <img src="{{ $status->imageUrl() }}" alt="" class="size-full object-contain mx-auto">
            @endif
            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-transparent to-black/60 pointer-events-none"></div>
        </div>

        {{-- Top Progress Bar & Header --}}
        <div class="relative z-10 space-y-3">
            {{-- Story Progress Bar --}}
            <div class="h-1 w-full bg-white/30 rounded-full overflow-hidden">
                <div class="h-full bg-amber-400 w-full"></div>
            </div>

            {{-- Story Header --}}
            <div class="flex items-center justify-between">
                <a href="{{ route('students.show', $status->user) }}" class="flex items-center gap-3 group" wire:navigate>
                    <span class="flex size-10 items-center justify-center overflow-hidden rounded-full bg-studio text-xs font-bold text-gold ring-2 ring-amber-400 shadow-md transition group-hover:scale-105">
                        @if ($status->user->avatarUrl())
                            <img src="{{ $status->user->avatarUrl() }}" alt="{{ $status->user->name }}" class="size-full object-cover rounded-full">
                        @else
                            {{ $status->user->initials() }}
                        @endif
                    </span>
                    <div>
                        <span class="block font-bold text-white text-sm drop-shadow">{{ $status->user->name }}</span>
                        <span class="block text-[11px] text-white/80 drop-shadow">{{ $status->created_at->diffForHumans() }} · 24h Campus Story</span>
                    </div>
                </a>
                
                <a href="{{ route('home') }}" class="flex size-9 items-center justify-center rounded-full bg-black/60 text-white border border-white/20 backdrop-blur hover:bg-white/20 transition text-sm font-bold" wire:navigate title="Close">
                    ✕
                </a>
            </div>
        </div>

        {{-- Story Caption Footer --}}
        @if ($status->caption)
            <div class="relative z-10 rounded-2xl bg-black/75 p-4 backdrop-blur border border-white/15 shadow-xl">
                <p class="text-sm font-medium leading-relaxed text-white drop-shadow">{{ $status->caption }}</p>
            </div>
        @endif
    </div>
</div>
