<div>
    <h1 class="font-display text-4xl">Join campus</h1>
    <p class="mt-2 text-mist">Student accounts only. Campus and super admins are appointed.</p>

    <form wire:submit="register" class="mt-8 flex flex-col gap-4">
        <label class="flex flex-col gap-1 text-sm">
            Name
            <input wire:model="name" type="text" class="field" required>
            @error('name') <span class="text-ember">{{ $message }}</span> @enderror
        </label>
        <label class="flex flex-col gap-1 text-sm">
            University email
            <input wire:model="email" type="email" class="field" required>
            @error('email') <span class="text-ember">{{ $message }}</span> @enderror
        </label>
        <label class="flex flex-col gap-1 text-sm">
            Password
            <input wire:model="password" type="password" class="field" required>
            @error('password') <span class="text-ember">{{ $message }}</span> @enderror
        </label>
        <label class="flex flex-col gap-1 text-sm">
            Confirm password
            <input wire:model="password_confirmation" type="password" class="field" required>
        </label>
        <button type="submit" class="btn-primary">
            Create your studio
        </button>
        <p class="text-sm text-mist">Already here? <a href="{{ route('login') }}" class="text-ember" wire:navigate>Sign in</a></p>
    </form>
</div>
