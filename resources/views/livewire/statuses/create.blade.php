<div class="mx-auto max-w-md px-4 py-8">
    <p class="text-xs uppercase tracking-[0.28em] text-ember">Story</p>
    <h1 class="font-display text-4xl">Create story</h1>
    <p class="mt-2 text-mist">It stays on the campus status row for 24 hours, then it disappears.</p>

    <form wire:submit="save" class="mt-6 flex flex-col gap-4">
        <label class="flex flex-col gap-1 text-sm">Photo
            <input type="file" wire:model="image" accept="image/*" class="field">
            <span wire:loading wire:target="image" class="text-mist">Uploading…</span>
            @error('image') <span class="text-ember">{{ $message }}</span> @enderror
        </label>
        <label class="flex flex-col gap-1 text-sm">Caption
            <input wire:model="caption" class="field" maxlength="160" placeholder="Add a line…">
        </label>
        <button type="submit" class="btn-primary">Share to status</button>
    </form>
</div>
