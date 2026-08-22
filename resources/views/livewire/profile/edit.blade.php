<div class="mx-auto max-w-xl px-4 py-8">
    <p class="text-xs uppercase tracking-[0.28em] text-ember">About you</p>
    <h1 class="font-display text-4xl">Edit profile</h1>
    <p class="mt-2 text-mist">Manage your creator category, campus details, and talents shown on campus.</p>

    @if (session('status'))
        <p class="mt-4 rounded-2xl bg-gold/20 px-4 py-3 text-sm">{{ session('status') }}</p>
    @endif

    <form wire:submit="save" class="mt-6 flex flex-col gap-4">
        {{-- Profile Picture Uploader --}}
        <div class="p-4 rounded-2xl bg-wall/60 border border-ink/10 flex items-center gap-5">
            <div class="relative size-20 shrink-0 overflow-hidden rounded-full bg-studio shadow-md ring-4 ring-amber-400/80">
                @if ($avatar)
                    <img src="{{ $avatar->temporaryUrl() }}" alt="Avatar Preview" class="size-full object-cover">
                @elseif ($currentAvatarUrl)
                    <img src="{{ $currentAvatarUrl }}" alt="Current Avatar" class="size-full object-cover">
                @else
                    <span class="flex size-full items-center justify-center text-2xl font-bold text-gold">{{ auth()->user()->initials() }}</span>
                @endif
            </div>

            <div class="flex-1 space-y-2">
                <label class="block text-xs font-bold text-ink">Profile Picture</label>
                <div class="flex flex-wrap items-center gap-2">
                    <label class="btn-primary cursor-pointer text-xs py-2 px-3 inline-flex items-center gap-1.5">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Upload Photo
                        <input type="file" wire:model="avatar" accept="image/*" class="hidden">
                    </label>

                    @if ($currentAvatarUrl || $avatar)
                        <button type="button" wire:click="removeAvatar" class="btn-ghost text-xs text-ember py-2 px-3">
                            Remove
                        </button>
                    @endif
                </div>
                @error('avatar') <p class="text-ember text-xs">{{ $message }}</p> @enderror
                <p class="text-[10px] text-mist">Recommended: Square JPG, PNG, or WebP up to 10MB.</p>
            </div>
        </div>

        <label class="flex flex-col gap-1 text-sm font-medium">Name
            <input wire:model="name" class="field">
            @error('name') <span class="text-ember text-xs">{{ $message }}</span> @enderror
        </label>
        
        <label class="flex flex-col gap-1 text-sm font-medium">Short description / headline
            <input wire:model="headline" class="field" maxlength="160" placeholder="Painter · 2nd year · ICBT">
            @error('headline') <span class="text-ember text-xs">{{ $message }}</span> @enderror
        </label>

        {{-- Profile Creator Category --}}
        <div class="p-4 rounded-2xl bg-amber-50/60 border border-amber-200 space-y-3">
            <h3 class="text-xs font-bold uppercase tracking-wider text-amber-800">Profile Creator Type</h3>
            
            <label class="flex flex-col gap-1 text-xs font-medium text-ink">
                Profile Category Account
                <select wire:model="profile_type" class="field text-xs" required>
                    <option value="🎤 Performing Arts Creator Account">🎤 Performing Arts Creator Account (Singing, Music, Dance, Comedy)</option>
                    <option value="🎨 Creative & Visual Arts Creator Account">🎨 Creative & Visual Arts Creator Account (Art, Photo, Design)</option>
                    <option value="🏆 Sports & Physical Creator Account">🏆 Sports & Physical Creator Account (Cricket, Football, Yoga)</option>
                    <option value="✨ Unique & Hidden Talents Creator Account">✨ Unique & Hidden Talents Creator Account (Cooking, Magic, Chess)</option>
                    <option value="👤 General Student Account">👤 General Student Account</option>
                </select>
                @error('profile_type') <span class="text-ember text-xs">{{ $message }}</span> @enderror
            </label>

            <div>
                <label class="block text-xs font-medium text-ink mb-1">Primary Talent Focus</label>
                <x-searchable-talent-select wire:model="primary_talent_id" :talents="$talents" :selectedId="$primary_talent_id" :activeCategory="$profile_type" placeholder="Type to search primary talent (e.g. Singing, Photography, Cricket)..." />
                @error('primary_talent_id') <span class="text-ember text-xs">{{ $message }}</span> @enderror
            </div>
        </div>

        {{-- Campus Academic Details --}}
        <div class="p-4 rounded-2xl bg-wall/60 border border-ink/8 space-y-3">
            <h3 class="text-xs font-bold uppercase tracking-wider text-ember">Campus & Program Details</h3>
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <label class="flex flex-col gap-1 text-xs font-medium text-ink">Batch / Intake Year
                    <input wire:model="batch" class="field text-xs" placeholder="e.g. Batch 2024">
                </label>
                <label class="flex flex-col gap-1 text-xs font-medium text-ink">Degree Program
                    <input wire:model="program" class="field text-xs" placeholder="e.g. BSc Software Engineering">
                </label>
            </div>
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <label class="flex flex-col gap-1 text-xs font-medium text-ink">Faculty
                    <input wire:model="faculty" class="field text-xs">
                </label>
                <label class="flex flex-col gap-1 text-xs font-medium text-ink">Department
                    <input wire:model="department" class="field text-xs">
                </label>
            </div>
        </div>

        <label class="flex flex-col gap-1 text-sm font-medium">About
            <textarea wire:model="bio" rows="4" class="field" placeholder="A little more about you"></textarea>
        </label>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <label class="flex flex-col gap-1 text-sm font-medium">Birthday
                <input type="date" wire:model="birthday" class="field">
                @error('birthday') <span class="text-ember text-xs">{{ $message }}</span> @enderror
            </label>
            <label class="flex flex-col gap-1 text-sm font-medium">Lives in
                <input wire:model="location" class="field" placeholder="Colombo">
            </label>
        </div>

        <fieldset class="rounded-2xl border border-ink/10 bg-white p-4">
            <legend class="px-1 text-sm font-bold text-ink">All Secondary Talents</legend>
            <div class="mt-2 grid grid-cols-1 gap-2 sm:grid-cols-2 max-h-60 overflow-y-auto pr-2">
                @foreach ($talents as $talent)
                    <label class="flex items-center gap-2 text-xs" wire:key="talent-{{ $talent->id }}">
                        <input type="checkbox" value="{{ $talent->id }}" wire:model="talent_ids">
                        <span>{{ $talent->name }}</span>
                        <span class="text-[10px] text-mist">({{ $talent->category }})</span>
                    </label>
                @endforeach
            </div>
        </fieldset>

        <button type="submit" class="btn-dark mt-2">Save profile</button>
    </form>

    <div class="mt-6 flex flex-col items-center gap-3">
        <a href="{{ route('profile.show') }}" class="text-sm text-mist" wire:navigate>Back to profile</a>
        <x-logout-button />
    </div>
</div>
