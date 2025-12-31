<div class="p-6 space-y-6">
    <div class="flex justify-between items-center gap-4">

        <div class="flex-1">
            <input type="text" wire:model.debounce.500ms="search" placeholder="Wpisz tytuł, miasto, ulicę lub opis..."
                class="w-full border rounded-lg p-3 focus:ring-2 focus:ring-blue-400 focus:outline-none" />
        </div>

        <div class="relative">
            <select wire:model="sortBy" wire:change="resetPage"
                class="border rounded-lg p-3 focus:ring-2 focus:ring-blue-400 focus:outline-none bg-white">
                <option value="created_at">Od najnowszych</option>
                <option value="title">Alfabetycznie (A-Z)</option>
                <option value="average_rating">Najwyżej oceniane</option>
                <option value="capacity">Największa pojemność</option>
            </select>
        </div>

        <button wire:click="$toggle('showFilters')"
            class="bg-blue-600 text-white px-4 py-3 rounded-lg shadow hover:bg-blue-700 transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
            </svg>
            Filtry
        </button>
    </div>

    @if ($showFilters ?? false)
        <div class="bg-white p-6 rounded-lg shadow-lg border">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
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

    @if ($noclegi->count() > 0)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            @foreach ($noclegi as $n)
                <div class="bg-white shadow-md rounded-lg overflow-hidden border">
                    {{-- Карусель фото --}}
                    
                        <x-photo-carousel 
                            :photos="$n->photos"
                            :showRating="true"
                            :rating="$n->average_rating ?? 0"
                            :alt="$n->title"
                            aspectRatio="aspect-video"
                            containerClass="rounded-t-lg"
                            arrowSize="w-6 h-6"
                            ratingBadgePosition="top-3 right-3"
                            showDots="true"
                        />
                    

                    <div class="p-4 space-y-1 text-sm">
                        <h3 class="text-lg font-semibold">{{ $n->title }}</h3>
                        <p class="text-gray-600">📍 {{ $n->city }}, {{ $n->street }}</p>
                        <p class="text-gray-600">
                            <strong>Typ:</strong> {{ $n->objectType?->name ?? '—' }}
                        </p>
                        <p class="text-gray-600">
                            <strong>Kontakt:</strong> {{ $n->contact_phone ?? '—' }}
                        </p>
                        <div class="text-sm font-medium text-gray-700">
                            ⭐ {{ number_format($n->average_rating ?? 0, 2) }}
                        </div>

                        <div class="text-sm text-gray-700 flex gap-2 flex-wrap">
                            @if ($n->has_kitchen)
                                <span class="px-2 py-1 bg-gray-100 rounded">🍳 Kuchnia</span>
                            @endif
                            @if ($n->has_parking)
                                <span class="px-2 py-1 bg-gray-100 rounded">🅿️ Parking</span>
                            @endif
                            @if ($n->has_bathroom)
                                <span class="px-2 py-1 bg-gray-100 rounded">🚿 Łazienka</span>
                            @endif
                            @if ($n->has_wifi)
                                <span class="px-2 py-1 bg-gray-100 rounded">📶 Wi-Fi</span>
                            @endif
                            @if ($n->has_tv)
                                <span class="px-2 py-1 bg-gray-100 rounded">📺 TV</span>
                            @endif
                            @if ($n->has_balcony)
                                <span class="px-2 py-1 bg-gray-100 rounded">🌅 Balkon</span>
                            @endif
                        </div>
                        <p class="text-gray-500 text-xs mt-2">
                            Dodano: {{ $n->created_at->format('d.m.Y') }} |
                            Właściciel: {{ $n->user?->name ?? 'Brak' }}
                        </p>
                    </div>

                    <div class="p-4 pt-0 flex gap-3">
                        <button wire:click="approveNocleg({{ $n->id }})" wire:loading.attr="disabled"
                            class="flex-1 bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition font-medium text-center">
                            Zatwierdź
                        </button>

                        <button wire:click="openRejectModal({{ $n->id }})"
                            class="flex-1 bg-red-600 text-white py-2 rounded-lg hover:bg-red-700 transition font-medium text-center">
                            Odrzuć
                        </button>
                    </div>

                    <div class="px-4 pb-4">
                        <a href="{{ route('admin.noclegi.details', $n->id) }}"
                            class="block text-center bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition font-medium">
                            Szczegóły
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $noclegi->links() }}
        </div>
    @else
        <div class="text-center py-16">
            <p class="text-gray-500 text-lg">Brak obiektów oczekujących na moderację.</p>
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

    @if ($rejectModal)
        <div class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl shadow-2xl max-w-md w-full p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">
                    Odrzucenie obiektu
                </h3>

                <div class="mb-6">
                    <label for="rejectReason" class="block text-sm font-medium text-gray-700 mb-2">
                        Uzasadnienie odrzucenia <span class="text-red-600">*</span>
                    </label>
                    <textarea id="rejectReason" wire:model.live="rejectReason" rows="3"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg
                              focus:outline-none focus:ring-2 focus:ring-red-500 resize-none"
                        placeholder="Np. Brak zdjęć, niepełny opis..."></textarea>

                    @error('rejectReason')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end gap-3">
                    <button wire:click="$set('rejectModal', false)"
                        class="px-5 py-2.5 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition font-medium">
                        Anuluj
                    </button>

                    <button wire:click="rejectNocleg" wire:loading.attr="disabled"
                        class="px-5 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium">
                        Odrzuć obiekt
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>