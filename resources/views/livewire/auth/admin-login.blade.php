<div>
    <div class="mb-8 text-center">
        <p class="text-xs uppercase tracking-[0.28em] text-gold/60">Super admin portal</p>
        <h1 class="mt-2 font-display text-3xl">Admin sign in</h1>
        <p class="mt-1 text-sm text-paper/50">Restricted access. Authorised personnel only.</p>
    </div>

    <form wire:submit="login" class="flex flex-col gap-4">
        <label class="flex flex-col gap-1 text-sm text-paper/70">
            Email
            <input wire:model="email" type="email" class="rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-paper placeholder-paper/30 outline-none focus:border-gold/40 focus:ring-2 focus:ring-gold/20" required>
            @error('email') <span class="text-ember">{{ $message }}</span> @enderror
        </label>
        <label class="flex flex-col gap-1 text-sm text-paper/70">
            Password
            <input wire:model="password" type="password" class="rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-paper placeholder-paper/30 outline-none focus:border-gold/40 focus:ring-2 focus:ring-gold/20" required>
            @error('password') <span class="text-ember">{{ $message }}</span> @enderror
        </label>
        <button type="submit" class="mt-2 rounded-xl bg-gold px-4 py-2.5 text-sm font-semibold text-studio transition hover:bg-gold/90">
            Sign in to admin
        </button>
    </form>
</div>
