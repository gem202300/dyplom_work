<div class="p-6 space-y-4">

    {{-- Пошук та кнопка фільтрів --}}
    <div class="flex justify-between items-center gap-4">
        {{-- Пошук --}}
        <div class="flex-1">
            <input type="text" wire:model.debounce.500ms="search"
                placeholder="Wpisz słowa kluczowe (np. 'zamek', 'jezioro')"
                class="w-full border rounded-lg p-3 focus:ring-2 focus:ring-blue-400 focus:outline-none" />
        </div>

        {{-- Кнопка фільтрів --}}
        <button wire:click="$toggle('showFilters')"
            class="bg-blue-600 text-white px-4 py-3 rounded-lg shadow hover:bg-blue-700 transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
            </svg>
            Filtry
        </button>
    </div>

    {{-- Випадаюче вікно фільтрів --}}
    @if ($showFilters)
        <div class="bg-white p-6 rounded-lg shadow-lg border">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Фільтр за категоріями --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Typ atrakcji</label>
                    <div class="border rounded-lg p-2 max-h-44 overflow-y-auto bg-white">
                        @foreach ($categories as $category)
                            <div class="flex items-center p-2 hover:bg-gray-50 rounded cursor-pointer transition-colors"
                                wire:click="toggleCategory({{ $category->id }})">
                                {{-- Галочка або порожнє місце для вирівнювання --}}
                                <div class="w-6 h-6 mr-3 flex items-center justify-center">
                                    @if (in_array($category->id, $selectedCategories))
                                        <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    @else
                                        <div class="w-5 h-5 border border-gray-300 rounded"></div>
                                    @endif
                                </div>

                                <label class="cursor-pointer text-sm flex-1">
                                    {{ $category->name }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                    @if (count($selectedCategories) > 0)
                        <p class="text-sm text-gray-500 mt-1">Wybrano: {{ count($selectedCategories) }}</p>
                    @endif
                </div>

                {{-- Фільтр за оцінкою --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ocena</label>
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm text-gray-500">Min ocena</label>
                            <input type="number" wire:model="minRating" min="0" max="5" step="0.5"
                                placeholder="0" class="w-full border rounded p-2" />
                        </div>

                        <div>
                            <label class="text-sm text-gray-500">Max ocena</label>
                            <input type="number" wire:model="maxRating" min="0" max="5" step="0.5"
                                placeholder="5" class="w-full border rounded p-2" />
                        </div>
                    </div>
                </div>
            </div>

            {{-- Кнопки дій фільтрів --}}
            <div class="flex justify-between items-center mt-6 pt-4 border-t">
                <button wire:click="resetFilters"
                    class="text-gray-600 hover:text-gray-800 px-4 py-2 rounded hover:bg-gray-100">
                    Wyczyść filtry
                </button>
                <button wire:click="$set('showFilters', false)"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Zastosuj
                </button>
            </div>
        </div>
    @endif

    {{-- Активні фільтри --}}
    @if ($search || !empty($selectedCategories) || $minRating || $maxRating)
        <div class="bg-blue-50 p-3 rounded-lg">
            <p class="text-sm text-blue-800 mb-2">Aktywne filtry:</p>
            <div class="flex flex-wrap gap-2">
                @if ($search)
                    <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">
                        Szukaj: "{{ $search }}"
                        <button wire:click="$set('search', '')" class="ml-2">×</button>
                    </span>
                @endif

                @foreach ($selectedCategories as $catId)
                    @php $cat = $categories->firstWhere('id', $catId); @endphp
                    @if ($cat)
                        <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm">
                            {{ $cat->name }}
                            <button wire:click="removeCategory({{ $catId }})" class="ml-2">×</button>
                        </span>
                    @endif
                @endforeach

                @if ($minRating)
                    <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm">
                        Min: {{ $minRating }}⭐
                        <button wire:click="$set('minRating', null)" class="ml-2">×</button>
                    </span>
                @endif

                @if ($maxRating)
                    <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm">
                        Max: {{ $maxRating }}⭐
                        <button wire:click="$set('maxRating', null)" class="ml-2">×</button>
                    </span>
                @endif
            </div>
        </div>
    @endif

    {{-- Решта вашого коду залишається без змін --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($attractions as $a)
            <div class="bg-white shadow-lg rounded-xl overflow-hidden hover:shadow-2xl transition">
                <a href="{{ route('attractions.show', $a->id) }}" class="block">
                    <div class="aspect-video bg-gray-100 overflow-hidden rounded-t-xl">
                        @if ($a->photos->isNotEmpty())
                            <img src="{{ asset($a->photos->first()->path) }}"
                                class="w-full h-full object-cover transition-transform hover:scale-105" />
                        @else
                            <div class="flex items-center justify-center h-full text-gray-400 italic">
                                brak zdjęcia
                            </div>
                        @endif
                    </div>
                </a>

                <div class="p-4 space-y-1">
                    <div class="flex justify-between items-start">
                        <h3 class="text-lg font-semibold">
                            <a href="{{ route('attractions.show', $a->id) }}"
                                class="hover:underline">{{ $a->name }}</a>
                        </h3>
                        <span
                            class="text-xs px-2 py-1 rounded-full {{ $a->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $a->is_active ? 'Aktywna' : 'Ukryta' }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-500">{{ $a->location }}</p>
                    <div class="text-sm text-gray-600">
                        <strong>Kategorie:</strong> {{ $a->categories->pluck('name')->join(', ') }}
                    </div>
                    <div class="text-sm text-gray-600">
                        <strong>Godziny:</strong>
                        @if ($a->opening_time && $a->closing_time)
                            {{ $a->opening_time }} - {{ $a->closing_time }}
                        @else
                            —
                        @endif
                    </div>
                    <div class="text-sm font-medium text-gray-700">
                        ⭐ {{ number_format($a->average_rating ?? 0, 2) }}
                    </div>
                </div>

                <div class="p-4 border-t flex items-center justify-between space-x-2">
                    <a href="{{ route('attractions.show', $a->id) }}"
                        class="flex-1 text-center py-2 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition">
                        <x-wireui-icon name="eye" class="w-5 h-5 inline" />
                    </a>
                    <a href="{{ route('attractions.edit', $a->id) }}"
                        class="flex-1 text-center py-2 rounded-lg bg-gray-50 text-gray-600 hover:bg-gray-100 transition">
                        <x-wireui-icon name="pencil" class="w-5 h-5 inline" />
                    </a>
                    <button wire:click="toggleActive({{ $a->id }})"
                        class="flex-1 text-center py-2 rounded-lg {{ $a->is_active ? 'bg-yellow-50 text-yellow-600 hover:bg-yellow-100' : 'bg-green-50 text-green-600 hover:bg-green-100' }} transition">
                        <x-wireui-icon name="{{ $a->is_active ? 'eye-slash' : 'eye' }}" class="w-5 h-5 inline" />
                    </button>
                    <button wire:click="deleteAttraction({{ $a->id }})"
                        wire:confirm="Czy na pewno chcesz usunąć {{ $a->name }}?"
                        class="flex-1 text-center py-2 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition">
                        <x-wireui-icon name="trash" class="w-5 h-5 inline" />
                    </button>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-6">
        {{ $attractions->links() }}
    </div>
</div>
