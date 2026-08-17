<div class="mx-auto max-w-xl px-4 py-8">
    <p class="text-xs uppercase tracking-[0.28em] text-ember">New post</p>
    <h1 class="font-display text-4xl">Share with campus</h1>
    <p class="mt-2 text-mist">Photo or clip, a caption, and the talent it belongs to — like Instagram, for your university.</p>

    <form wire:submit="save" class="mt-6 flex flex-col gap-4">
        <label class="flex flex-col gap-1 text-sm">Photo or file
            <input type="file" wire:model="file" class="field">
            <span wire:loading wire:target="file" class="text-mist">Uploading…</span>
            @error('file') <span class="text-ember">{{ $message }}</span> @enderror
        </label>
        <label class="flex flex-col gap-1 text-sm">Title
            <input wire:model="title" class="field" placeholder="Give this post a name">
            @error('title') <span class="text-ember">{{ $message }}</span> @enderror
        </label>
        <label class="flex flex-col gap-1 text-sm">Caption
            <textarea wire:model="description" rows="4" class="field" placeholder="Write a caption…"></textarea>
        </label>
        <label class="flex flex-col gap-1 text-sm">Talent
            <select wire:model="talent_id" class="field">
                <option value="">Select a talent</option>
                @foreach ($talents as $talent)
                    <option value="{{ $talent->id }}">{{ $talent->name }}</option>
                @endforeach
            </select>
        </label>
        <label class="flex flex-col gap-1 text-sm">Type
            <select wire:model="media_type" class="field">
                <option value="image">Photo</option>
                <option value="video">Video</option>
                <option value="audio">Audio</option>
                <option value="document">Document</option>
            </select>
        </label>
        <button type="submit" class="btn-primary">Share post</button>
    </form>
</div>
