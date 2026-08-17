<div class="px-4 py-8 lg:px-10">
    <p class="text-xs uppercase tracking-[0.28em] text-ember">Campus</p>
    <h1 class="font-display text-4xl">People</h1>
    <p class="mt-2 text-mist">Every talent has its own room. Step into a studio.</p>
    <div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($students as $student)
            <a href="{{ route('students.show', $student) }}" class="overflow-hidden rounded-[1.5rem] bg-white" wire:key="stu-{{ $student->id }}" wire:navigate>
                @if ($student->portfolioItems->first())
                    <img src="{{ $student->portfolioItems->first()->displayUrl() }}" alt="" class="h-44 w-full object-cover">
                @else
                    <div class="h-44 bg-studio"></div>
                @endif
                <div class="p-5">
                    <h2 class="font-display text-2xl">{{ $student->name }}</h2>
                    <p class="mt-1 text-sm text-mist">{{ $student->profile?->headline }}</p>
                    <p class="mt-2 text-xs uppercase tracking-wide text-ember">{{ $student->profile?->primaryTalent()?->name }}</p>
                </div>
            </a>
        @endforeach
    </div>
    <div class="mt-6">{{ $students->links() }}</div>
</div>
