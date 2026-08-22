@props([
    'talents' => [],
    'name' => 'talent_id',
    'selectedId' => null,
    'placeholder' => 'Search and select talent…',
    'activeCategory' => 'All',
])

@php
    $categorized = collect($talents)->groupBy('category');
    $selectedTalent = collect($talents)->firstWhere('id', (int) $selectedId);
@endphp

<div x-data="{
        open: false,
        search: '',
        selectedId: @entangle($attributes->wire('model')),
        selectedName: '{{ $selectedTalent ? e($selectedTalent->name) : '' }}',
        selectedCategory: '{{ $selectedTalent ? e($selectedTalent->category) : '' }}',
        activeCategoryTab: '{{ $activeCategory ?? 'All' }}',
        select(id, name, category) {
            this.selectedId = id;
            this.selectedName = name;
            this.selectedCategory = category;
            this.open = false;
            this.search = '';
        },
        categoryIcon(cat) {
            if (!cat) return '👤';
            if (cat.includes('Performing')) return '🎤';
            if (cat.includes('Creative')) return '🎨';
            if (cat.includes('Sports')) return '🏆';
            if (cat.includes('Unique')) return '✨';
            return '👤';
        },
        isCategoryActive(catName) {
            if (this.activeCategoryTab === 'All') return true;
            if (this.activeCategoryTab.includes('Performing') && catName.includes('Performing')) return true;
            if (this.activeCategoryTab.includes('Creative') && catName.includes('Creative')) return true;
            if (this.activeCategoryTab.includes('Sports') && catName.includes('Sports')) return true;
            if (this.activeCategoryTab.includes('Unique') && catName.includes('Unique')) return true;
            if (this.activeCategoryTab.includes('General') && catName.includes('General')) return true;
            return this.activeCategoryTab === catName;
        }
    }"
    @click.away="open = false"
    class="relative w-full">

    {{-- Trigger Button / Field --}}
    <button type="button"
            @click="open = !open"
            class="field flex w-full items-center justify-between gap-2 text-left shadow-sm hover:border-amber-400 focus:outline-none transition">
        <span x-show="selectedName" class="flex items-center gap-2 truncate font-medium text-ink">
            <span x-text="categoryIcon(selectedCategory)"></span>
            <span x-text="selectedName"></span>
            <span x-text="'(' + selectedCategory + ')'" class="text-xs text-mist font-normal"></span>
        </span>
        <span x-show="!selectedName" class="text-mist font-normal">{{ $placeholder }}</span>
        
        <svg class="size-4 shrink-0 text-mist transition duration-200" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    {{-- Dropdown Popup --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="absolute z-50 mt-1.5 max-h-96 w-full overflow-hidden rounded-2xl border border-ink/10 bg-white p-2.5 shadow-xl ring-1 ring-black/5"
         style="display: none;">
        
        {{-- Category Filter Pills --}}
        <div class="flex gap-1 overflow-x-auto pb-2 border-b border-ink/8 text-[11px] font-bold scrollbar-none">
            <button type="button" @click="activeCategoryTab = 'All'" :class="activeCategoryTab === 'All' ? 'bg-amber-500 text-white shadow-sm' : 'bg-wall text-mist hover:text-ink'" class="rounded-lg px-2.5 py-1 transition shrink-0">All Categories</button>
            <button type="button" @click="activeCategoryTab = 'Performing Arts'" :class="activeCategoryTab.includes('Performing') ? 'bg-amber-500 text-white shadow-sm' : 'bg-wall text-mist hover:text-ink'" class="rounded-lg px-2.5 py-1 transition shrink-0">🎤 Performing Arts</button>
            <button type="button" @click="activeCategoryTab = 'Creative & Visual Arts'" :class="activeCategoryTab.includes('Creative') ? 'bg-amber-500 text-white shadow-sm' : 'bg-wall text-mist hover:text-ink'" class="rounded-lg px-2.5 py-1 transition shrink-0">🎨 Creative Arts</button>
            <button type="button" @click="activeCategoryTab = 'Sports & Physical'" :class="activeCategoryTab.includes('Sports') ? 'bg-amber-500 text-white shadow-sm' : 'bg-wall text-mist hover:text-ink'" class="rounded-lg px-2.5 py-1 transition shrink-0">🏆 Sports</button>
            <button type="button" @click="activeCategoryTab = 'Unique & Hidden'" :class="activeCategoryTab.includes('Unique') ? 'bg-amber-500 text-white shadow-sm' : 'bg-wall text-mist hover:text-ink'" class="rounded-lg px-2.5 py-1 transition shrink-0">✨ Unique Talents</button>
        </div>

        {{-- Search Input --}}
        <div class="relative py-2">
            <svg class="pointer-events-none absolute left-3 top-4 size-4 text-mist" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input x-model="search"
                   x-ref="searchInput"
                   @focus="$el.select()"
                   type="text"
                   class="w-full rounded-xl bg-wall/60 py-2 pl-9 pr-3 text-xs font-medium text-ink placeholder-mist focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-400"
                   placeholder="Search talents inside category (e.g. Singing, Photography)...">
        </div>

        {{-- Categorized Talent List (Filtered to active category ONLY) --}}
        <div class="max-h-60 overflow-y-auto space-y-3 p-1">
            @foreach ($categorized as $category => $groupTalents)
                <div x-data="{
                    matchCategory() {
                        if (!search) return true;
                        const s = search.toLowerCase();
                        if ('{{ strtolower($category) }}'.includes(s)) return true;
                        return {{ json_encode($groupTalents->pluck('name')->all()) }}.some(t => t.toLowerCase().includes(s));
                    }
                }" x-show="isCategoryActive('{{ addslashes($category) }}') && matchCategory()">
                    
                    <div class="sticky top-0 bg-white px-2 py-1 text-[11px] font-bold uppercase tracking-wider text-amber-700 flex items-center gap-1.5 border-b border-ink/5">
                        <span>
                            @if (str_contains($category, 'Performing')) 🎤
                            @elseif (str_contains($category, 'Creative')) 🎨
                            @elseif (str_contains($category, 'Sports')) 🏆
                            @elseif (str_contains($category, 'Unique')) ✨
                            @else 👤
                            @endif
                        </span>
                        <span>{{ $category }}</span>
                    </div>

                    <div class="mt-1 space-y-0.5">
                        @foreach ($groupTalents as $t)
                            <button type="button"
                                    x-show="!search || '{{ strtolower($t->name) }}'.includes(search.toLowerCase()) || '{{ strtolower($category) }}'.includes(search.toLowerCase())"
                                    @click="select({{ $t->id }}, '{{ addslashes($t->name) }}', '{{ addslashes($category) }}')"
                                    class="flex w-full items-center justify-between rounded-xl px-3 py-2 text-left text-xs font-medium transition hover:bg-amber-50 hover:text-amber-900"
                                    :class="{ 'bg-amber-100/70 text-amber-900 font-bold': selectedId == {{ $t->id }} }">
                                <span>{{ $t->name }}</span>
                                <span x-show="selectedId == {{ $t->id }}" class="text-amber-600 font-bold">✓</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
