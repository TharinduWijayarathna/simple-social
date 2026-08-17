@props([
    'href',
    'icon',
    'label',
    'active' => false,
])

<a href="{{ $href }}" {{ $attributes->class([
    'flex size-10 items-center justify-center rounded-full transition',
    'bg-wall text-ember' => $active,
    'text-mist hover:bg-wall hover:text-ink' => ! $active,
]) }} wire:navigate title="{{ $label }}" aria-label="{{ $label }}">
    <x-icon :name="$icon" :solid="$active" class="size-6" />
</a>
