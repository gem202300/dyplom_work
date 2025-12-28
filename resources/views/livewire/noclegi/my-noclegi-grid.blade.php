<div class="p-6 space-y-4">

    {{-- Пошук та кнопка фільтрів --}}
    <div class="flex justify-between items-center gap-4">
        {{-- Пошук --}}
        <div class="flex-1">
            <input type="text" wire:model.debounce.500ms="search" placeholder="Wpisz tytuł, miasto, ulicę lub opis..."
                class="w-full border rounded-lg p-3 focus:ring-2 focus:ring-blue-400 focus:outline-none" />
        </div>

        {{-- Сортування --}}
        <div class="relative">
            <select wire:model="sortBy" wire:change="resetPage"
                class="border rounded-lg p-3 focus:ring-2 focus:ring-blue-400 focus:outline-none bg-white">
                <option value="created_at">Od najnowszych</option>
                <option value="title">Alfabetycznie (A-Z)</option>
                <option value="average_rating">Najwyżej oceniane</option>
                <option value="capacity">Największa pojemność</option>
            </select>
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
    @if ($showFilters ?? false)
        <div class="bg-white p-6 rounded-lg shadow-lg border">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Фільтр за типом об'єкту з бази даних --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Typ obiektu</label>
                    <div class="border rounded-lg p-2 max-h-44 overflow-y-auto bg-white">
                        @foreach ($types ?? [] as $type)
                            <div class="flex items-center p-2 hover:bg-gray-50 rounded cursor-pointer transition-colors"
                                wire:click="toggleType({{ $type->id }})">
                                <div class="w-6 h-6 mr-3 flex items-center justify-center">
                                    @if (in_array($type->id, $selectedTypeIds ?? []))
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
                                    {{ $type->name }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                    @if (count($selectedTypeIds ?? []) > 0)
                        <p class="text-sm text-gray-500 mt-1">Wybrano: {{ count($selectedTypeIds ?? []) }}</p>
                    @endif
                </div>

                {{-- Фільтр за зручностями --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Wyposażenie</label>
                    <div class="border rounded-lg p-2 max-h-44 overflow-y-auto bg-white">
                        @php
                            $amenities = [
                                'has_kitchen' => '🍳 Kuchnia',
                                'has_parking' => '🅿️ Parking',
                                'has_wifi' => '📶 Wi-Fi',
                                'has_tv' => '📺 TV',
                                'has_balcony' => '🌅 Balkon',
                                'has_bathroom' => '🚿 Łazienka',
                            ];
                        @endphp
                        @foreach ($amenities as $key => $label)
                            <div class="flex items-center p-2 hover:bg-gray-50 rounded cursor-pointer transition-colors"
                                wire:click="toggleAmenity('{{ $key }}')">
                                <div class="w-6 h-6 mr-3 flex items-center justify-center">
                                    @if (in_array($key, $selectedAmenities ?? []))
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
                                    {{ $label }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                    @if (count($selectedAmenities ?? []) > 0)
                        <p class="text-sm text-gray-500 mt-1">Wybrano: {{ count($selectedAmenities ?? []) }}</p>
                    @endif
                </div>

                {{-- Фільтр за місткістю та оцінкою --}}
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pojemność (osób)</label>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="text-xs text-gray-500">Min</label>
                                <input type="number" wire:model="minCapacity" min="1" max="50"
                                    placeholder="1" class="w-full border rounded p-2" />
                            </div>
                            <div>
                                <label class="text-xs text-gray-500">Max</label>
                                <input type="number" wire:model="maxCapacity" min="1" max="50"
                                    placeholder="50" class="w-full border rounded p-2" />
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ocena</label>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="text-xs text-gray-500">Min ocena</label>
                                <input type="number" wire:model="minRating" min="0" max="5"
                                    step="0.5" placeholder="0" class="w-full border rounded p-2" />
                            </div>
                            <div>
                                <label class="text-xs text-gray-500">Max ocena</label>
                                <input type="number" wire:model="maxRating" min="0" max="5"
                                    step="0.5" placeholder="5" class="w-full border rounded p-2" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

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
    @if (
        $search ||
            !empty($selectedTypeIds ?? []) ||
            !empty($selectedAmenities ?? []) ||
            $minCapacity ||
            $maxCapacity ||
            $minRating ||
            $maxRating)
        <div class="bg-blue-50 p-3 rounded-lg">
            <p class="text-sm text-blue-800 mb-2">Aktywne filtry:</p>
            <div class="flex flex-wrap gap-2">
                @if ($search)
                    <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">
                        Szukaj: "{{ $search }}"
                        <button wire:click="$set('search', '')" class="ml-2">×</button>
                    </span>
                @endif

                @foreach ($selectedTypeIds ?? [] as $typeId)
                    @php $type = ($types ?? collect())->firstWhere('id', $typeId); @endphp
                    @if ($type)
                        <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm">
                            {{ $type->name }}
                            <button wire:click="removeType({{ $typeId }})" class="ml-2">×</button>
                        </span>
                    @endif
                @endforeach

                @foreach ($selectedAmenities ?? [] as $amenity)
                    @php
                        $amenityLabels = [
                            'has_kitchen' => '🍳 Kuchnia',
                            'has_parking' => '🅿️ Parking',
                            'has_wifi' => '📶 Wi-Fi',
                            'has_tv' => '📺 TV',
                            'has_balcony' => '🌅 Balkon',
                            'has_bathroom' => '🚿 Łazienka',
                        ];
                    @endphp
                    <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-sm">
                        {{ $amenityLabels[$amenity] ?? $amenity }}
                        <button wire:click="removeAmenity('{{ $amenity }}')" class="ml-2">×</button>
                    </span>
                @endforeach

                @if ($minCapacity)
                    <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm">
                        Min osób: {{ $minCapacity }}
                        <button wire:click="$set('minCapacity', null)" class="ml-2">×</button>
                    </span>
                @endif

                @if ($maxCapacity)
                    <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm">
                        Max osób: {{ $maxCapacity }}
                        <button wire:click="$set('maxCapacity', null)" class="ml-2">×</button>
                    </span>
                @endif

                @if ($minRating)
                    <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm">
                        Min ocena: {{ $minRating }}⭐
                        <button wire:click="$set('minRating', null)" class="ml-2">×</button>
                    </span>
                @endif

                @if ($maxRating)
                    <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm">
                        Max ocena: {{ $maxRating }}⭐
                        <button wire:click="$set('maxRating', null)" class="ml-2">×</button>
                    </span>
                @endif
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($noclegi as $n)
    <div class="bg-white shadow-lg rounded-xl overflow-hidden hover:shadow-2xl transition">

        <a href="{{ route('noclegi.show', $n->id) }}" class="block">
            <div class="aspect-video bg-gray-100 overflow-hidden rounded-t-xl">
                @if ($n->photos->isNotEmpty())
                    <img src="{{ asset($n->photos->first()->path) }}"
                         class="w-full h-full object-cover transition-transform hover:scale-105" />
                @else
                    <div class="flex items-center justify-center h-full text-gray-400 italic">
                        brak zdjęcia
                    </div>
                @endif
            </div>
        </a>

        <div class="p-6 space-y-4">
            <h3 class="text-xl font-semibold text-gray-900">{{ $n->title }}</h3>
            <p class="text-sm text-gray-600">📍 {{ $n->city }}, {{ $n->street }}</p>
            <p class="text-sm text-gray-600"><strong>Typ:</strong> {{ $n->objectType?->name ?? '—' }}</p>
            <p class="text-sm text-gray-600"><strong>Kontakt:</strong> {{ $n->contact_phone ?? '—' }}</p>

            <div class="flex items-center gap-2">
                <span class="text-sm font-medium text-gray-700">⭐ {{ number_format($n->average_rating ?? 0, 2) }}</span>
                <span class="text-sm text-gray-500">• {{ $n->capacity }} osób</span>
            </div>

            {{-- СТАТУС + ПРИЧИНА + КНОПКА ДЛЯ PENDING/REJECTED --}}
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-700">Status:</span>
                    <div>
                        @if ($n->status === 'pending')
                            <span class="px-3 py-1.5 bg-yellow-100 text-yellow-800 rounded-full text-sm font-medium">
                                Oczekuje na zatwierdzenie
                            </span>
                        @elseif($n->status === 'approved')
                            <span class="px-3 py-1.5 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                                Zatwierdzony
                            </span>
                        @else
                            <span class="px-3 py-1.5 bg-red-100 text-red-800 rounded-full text-sm font-medium">
                                Odrzucony
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Причина відхилення --}}
                @if ($n->status === 'rejected' && $n->reject_reason)
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <p class="text-sm font-medium text-red-800 mb-1">Przyczyna odrzucenia:</p>
                        <p class="text-sm text-red-700">{{ $n->reject_reason }}</p>
                    </div>
                @endif

                {{-- Кнопка "Pokaż zgłoszenie" тільки для pending/rejected --}}
                @if (in_array($n->status, ['pending', 'rejected']))
                    <a href="{{ route('noclegi.edit', $n->id) }}"
                       class="inline-flex items-center justify-center gap-2 w-full px-4 py-3 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition font-medium shadow">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Pokaż zgłoszenie i edytuj
                    </a>
                @endif
            </div>

            {{-- Зручності --}}
            <div class="flex flex-wrap gap-2">
                @if ($n->has_kitchen) <span class="px-3 py-1 bg-gray-100 rounded-full text-sm">🍳 Kuchnia</span> @endif
                @if ($n->has_parking) <span class="px-3 py-1 bg-gray-100 rounded-full text-sm">🅿️ Parking</span> @endif
                @if ($n->has_bathroom) <span class="px-3 py-1 bg-gray-100 rounded-full text-sm">🚿 Łazienka</span> @endif
                @if ($n->has_wifi) <span class="px-3 py-1 bg-gray-100 rounded-full text-sm">📶 Wi-Fi</span> @endif
                @if ($n->has_tv) <span class="px-3 py-1 bg-gray-100 rounded-full text-sm">📺 TV</span> @endif
                @if ($n->has_balcony) <span class="px-3 py-1 bg-gray-100 rounded-full text-sm">🌅 Balkon</span> @endif
            </div>
        </div>

        {{-- Нижня панель з діями — КНОПКА "EDYTUJ" ЗАВЖДИ Є --}}
        <div class="p-4 border-t bg-gray-50 grid grid-cols-2 lg:grid-cols-4 gap-3">
            <a href="{{ route('noclegi.show', $n->id) }}"
               class="text-center py-2.5 rounded-lg bg-blue-100 text-blue-700 hover:bg-blue-200 transition font-medium">
                <svg class="w-5 h-5 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                Podgląd
            </a>

            {{-- КНОПКА EDYTUJ — ЗАВЖДИ ВИДИМА --}}
            <a href="{{ route('noclegi.edit', $n->id) }}"
               class="text-center py-2.5 rounded-lg bg-indigo-100 text-indigo-700 hover:bg-indigo-200 transition font-medium">
                <svg class="w-5 h-5 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edytuj
            </a>

            @if ($n->status === 'approved')
                <a href="{{ route('noclegi.calendar', $n->id) }}"
                   class="text-center py-2.5 rounded-lg bg-green-100 text-green-700 hover:bg-green-200 transition font-medium">
                    <svg class="w-5 h-5 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Kalendarz
                </a>
            @endif

            <button wire:click="deleteNocleg({{ $n->id }})" wire:confirm="Na pewno chcesz usunąć ten obiekt?"
                    class="text-center py-2.5 rounded-lg bg-red-100 text-red-700 hover:bg-red-200 transition font-medium">
                <svg class="w-5 h-5 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Usuń
            </button>
        </div>
    </div>
@endforeach
    </div>

    @if ($noclegi->isEmpty())
        <div class="text-center py-12">
            <p class="text-gray-500 text-lg">Brak noclegów spełniających kryteria wyszukiwania.</p>
            @if (
                $search ||
                    !empty($selectedTypeIds ?? []) ||
                    !empty($selectedAmenities ?? []) ||
                    $minCapacity ||
                    $maxCapacity ||
                    $minRating ||
                    $maxRating)
                <button wire:click="resetFilters" class="mt-4 text-blue-600 hover:text-blue-800">
                    Wyczyść filtry
                </button>
            @endif
        </div>
    @endif

    <div class="mt-6">
        {{ $noclegi->links() }}
    </div>
</div>
