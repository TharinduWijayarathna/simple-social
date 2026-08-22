<div class="mx-auto max-w-5xl px-4 py-8">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-ink/10 pb-6">
        <div>
            <p class="text-xs uppercase tracking-[0.28em] text-ember font-semibold">VibeCraft Creation Studio</p>
            <h1 class="font-display text-4xl mt-1">Share with Campus</h1>
            <p class="mt-1 text-mist">Post a permanent portfolio showcase or a 24-hour campus story clip.</p>
        </div>
        <a href="{{ route('home') }}" class="rounded-xl border border-ink/15 px-4 py-2 text-sm font-medium text-ink transition hover:bg-white shrink-0" wire:navigate>
            ← Cancel
        </a>
    </div>

    {{-- Mode Selector Tabs --}}
    <div class="mt-6 flex justify-center">
        <div class="inline-flex rounded-2xl bg-wall p-1.5 border border-ink/10 shadow-inner">
            <button type="button" 
                    wire:click="$set('upload_type', 'post')"
                    class="flex items-center gap-2.5 rounded-xl px-6 py-3 text-sm font-bold transition {{ $upload_type === 'post' ? 'bg-ember text-white shadow-md' : 'text-mist hover:text-ink' }}">
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                Portfolio Post / Reel
            </button>
            <button type="button" 
                    wire:click="$set('upload_type', 'story')"
                    class="flex items-center gap-2.5 rounded-xl px-6 py-3 text-sm font-bold transition {{ $upload_type === 'story' ? 'bg-ember text-white shadow-md' : 'text-mist hover:text-ink' }}">
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                📸 24h Campus Story
            </button>
        </div>
    </div>

    <form wire:submit="save" class="mt-8 grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        {{-- Form Controls (Left Column) --}}
        <div class="lg:col-span-7 space-y-6 bg-white rounded-3xl p-6 md:p-8 border border-ink/10 shadow-sm">
            @if ($upload_type === 'story')
                <div class="rounded-2xl bg-amber-500/10 border border-amber-400/30 p-4 text-xs text-amber-900 font-medium flex items-center gap-2">
                    <span class="text-base">⏳</span>
                    Stories disappear after 24 hours and appear at the top story reel on the campus feed.
                </div>
            @endif

            {{-- Media Drag & Drop Zone --}}
            <div>
                <label class="block text-sm font-semibold text-ink mb-2">Media File (Photo or Video clip)</label>
                <div class="relative flex flex-col items-center justify-center rounded-3xl border-2 border-dashed border-ink/20 bg-wall/40 p-8 text-center transition hover:bg-wall/80">
                    <input type="file" wire:model="file" class="absolute inset-0 z-10 size-full opacity-0 cursor-pointer" accept="image/*,video/*,audio/*,.pdf">
                    
                    @if ($file)
                        <div class="relative z-0 max-h-64 overflow-hidden rounded-2xl border border-ink/10 shadow-sm">
                            @if (str_starts_with($file->getMimeType(), 'video/'))
                                <video src="{{ $file->temporaryUrl() }}" controls class="max-h-60 rounded-2xl object-cover"></video>
                            @elseif (str_starts_with($file->getMimeType(), 'image/'))
                                <img src="{{ $file->temporaryUrl() }}" alt="Preview" class="max-h-60 rounded-2xl object-cover">
                            @else
                                <div class="p-6 text-sm font-semibold text-ink">📁 {{ $file->getClientOriginalName() }}</div>
                            @endif
                        </div>
                        <p class="mt-3 text-xs text-mist font-medium">Click or drag to change media file</p>
                    @else
                        <div class="flex size-14 items-center justify-center rounded-2xl bg-white text-ember shadow-sm mb-3">
                            <svg class="size-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        </div>
                        <p class="text-sm font-bold text-ink">Upload Photo or Video Reel</p>
                        <p class="mt-1 text-xs text-mist">PNG, JPG, MP4, MOV, WEBM up to 100MB</p>
                    @endif

                    <div wire:loading wire:target="file" class="absolute inset-0 z-20 flex items-center justify-center bg-white/90 rounded-3xl backdrop-blur-sm">
                        <div class="flex items-center gap-2 text-sm font-bold text-ember">
                            <svg class="size-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Uploading Media…
                        </div>
                    </div>
                </div>
                @error('file') <span class="mt-1 block text-xs font-semibold text-ember">{{ $message }}</span> @enderror
            </div>

            @if ($upload_type === 'post')
                {{-- Title --}}
                <div>
                    <label class="block text-sm font-semibold text-ink">Title / Headline <span class="text-ember">*</span></label>
                    <input type="text" wire:model.live="title" placeholder="Give your showcase post a title..." class="field mt-1">
                    @error('title') <span class="mt-1 block text-xs font-semibold text-ember">{{ $message }}</span> @enderror
                </div>

                {{-- Caption --}}
                <div>
                    <label class="block text-sm font-semibold text-ink">Description / Caption</label>
                    <textarea wire:model.live="description" rows="3" placeholder="Tell the story behind your creation, techniques used, or credits..." class="field mt-1"></textarea>
                </div>

                {{-- Talent Category --}}
                <div>
                    <label class="block text-sm font-semibold text-ink">Talent Category</label>
                    <select wire:model="talent_id" class="field mt-1">
                        <option value="">Select Talent Category</option>
                        @foreach ($talents as $t)
                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Media Type Format Selector --}}
                <div>
                    <label class="block text-sm font-semibold text-ink mb-2">Format Type</label>
                    <div class="grid grid-cols-4 gap-2">
                        @foreach (['image' => '📷 Photo', 'video' => '🎥 Video Reel', 'audio' => '🎵 Audio Track', 'document' => '📄 Document'] as $key => $label)
                            <button type="button" 
                                    wire:click="$set('media_type', '{{ $key }}')"
                                    class="rounded-xl border py-2.5 text-xs font-bold transition text-center
                                           {{ $media_type === $key ? 'border-ember bg-ember/10 text-ember' : 'border-ink/10 text-mist hover:text-ink' }}">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>
            @else
                {{-- Story Caption --}}
                <div>
                    <label class="block text-sm font-semibold text-ink">Story Caption</label>
                    <textarea wire:model.live="description" rows="3" placeholder="Add a quick caption for your 24h story clip..." class="field mt-1"></textarea>
                    @error('description') <span class="mt-1 block text-xs font-semibold text-ember">{{ $message }}</span> @enderror
                </div>
            @endif

            <button type="submit" class="w-full rounded-2xl bg-ember py-3.5 font-bold text-white shadow-md transition hover:bg-ember/90">
                {{ $upload_type === 'story' ? '🚀 Post 24h Campus Story' : '✨ Publish Portfolio Showcase' }}
            </button>
        </div>

        {{-- Live Mockup Preview Card (Right Column) --}}
        <div class="lg:col-span-5 space-y-3 sticky top-24">
            <p class="text-xs font-bold uppercase tracking-wider text-mist">Live Feed Preview</p>

            @if ($upload_type === 'story')
                {{-- Story Card Mockup --}}
                <div class="relative mx-auto h-96 w-56 overflow-hidden rounded-3xl border-2 border-amber-400 bg-black shadow-xl">
                    @if ($file && str_starts_with($file->getMimeType(), 'video/'))
                        <video src="{{ $file->temporaryUrl() }}" autoplay loop muted class="size-full object-cover"></video>
                    @elseif ($file && str_starts_with($file->getMimeType(), 'image/'))
                        <img src="{{ $file->temporaryUrl() }}" alt="Story Preview" class="size-full object-cover">
                    @else
                        <div class="flex size-full items-center justify-center bg-gradient-to-br from-indigo-900 via-purple-900 to-slate-900 p-6 text-center text-white/50 text-xs">
                            Upload a photo or video to preview story card
                        </div>
                    @endif

                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-black/30 pointer-events-none"></div>

                    <div class="absolute left-3 top-3 flex items-center gap-2">
                        <span class="flex size-8 items-center justify-center rounded-full bg-studio text-[10px] font-bold text-gold ring-2 ring-amber-400">
                            {{ auth()->user()->initials() }}
                        </span>
                        <span class="text-xs font-bold text-white drop-shadow">{{ auth()->user()->name }}</span>
                    </div>

                    <div class="absolute right-3 top-3 rounded-full bg-black/60 px-2 py-0.5 text-[10px] font-bold text-amber-400">
                        24h Story
                    </div>

                    @if ($description || $title)
                        <div class="absolute bottom-4 left-3 right-3 text-xs font-medium text-white drop-shadow">
                            {{ $title ? $title.' — ' : '' }}{{ $description }}
                        </div>
                    @endif
                </div>
            @else
                {{-- Post Card Mockup --}}
                <div class="rounded-3xl border border-ink/10 bg-white p-4 shadow-sm space-y-3">
                    <div class="flex items-center gap-3 border-b border-ink/8 pb-3">
                        <span class="flex size-9 items-center justify-center rounded-full bg-studio text-xs font-bold text-gold">
                            {{ auth()->user()->initials() }}
                        </span>
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-ink truncate">{{ auth()->user()->name }}</p>
                            <p class="text-[10px] text-mist">Just now · Campus Showcase</p>
                        </div>
                    </div>

                    <div class="relative overflow-hidden rounded-2xl bg-wall/40 min-h-[160px] flex items-center justify-center p-2">
                        @if ($file && str_starts_with($file->getMimeType(), 'video/'))
                            <video src="{{ $file->temporaryUrl() }}" controls class="w-full h-auto max-h-72 object-contain rounded-2xl"></video>
                            <span class="absolute top-2 right-2 rounded-full bg-black/70 px-2 py-0.5 text-[10px] font-bold text-white">REEL 🎥</span>
                        @elseif ($file && str_starts_with($file->getMimeType(), 'image/'))
                            <img src="{{ $file->temporaryUrl() }}" alt="Preview" class="w-full h-auto max-h-72 object-contain rounded-2xl">
                        @else
                            <span class="text-xs text-mist font-medium">Media preview container</span>
                        @endif
                    </div>

                    <div>
                        <h3 class="font-display font-bold text-base text-ink">{{ $title ?: 'Post Title' }}</h3>
                        <p class="text-xs text-mist line-clamp-2 mt-0.5">{{ $description ?: 'Post description preview will appear here...' }}</p>
                    </div>
                </div>
            @endif
        </div>
    </form>
</div>
