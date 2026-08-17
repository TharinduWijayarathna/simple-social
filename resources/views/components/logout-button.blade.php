<form method="POST" action="{{ route('logout') }}" {{ $attributes }}>
    @csrf
    <button type="submit" class="flex size-9 items-center justify-center rounded-full text-mist transition hover:bg-wall hover:text-ink" aria-label="Sign out" title="Sign out">
        <x-icon name="logout" />
    </button>
</form>
