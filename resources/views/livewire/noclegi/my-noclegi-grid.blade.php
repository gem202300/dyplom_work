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
                {{-- Контейнер для фото --}}
                <div class="relative">
                    {{-- Карусель фото --}}
                    <x-photo-carousel 
                        :photos="$n->photos"
                        :showRating="false"
                        :showDots="true"
                        :rating="$n->average_rating ?? 0"
                        :alt="$n->title"
                        aspectRatio="aspect-video"
                        containerClass="rounded-t-xl"
                        arrowSize="w-8 h-8"
                    />
                </div>

                {{-- Контент під фото --}}
                <div class="p-4 space-y-2">
                    {{-- Заголовок з рейтингом праворуч (ЯК У ПРИКЛАДІ) --}}
                    <div class="flex justify-between items-start">
                        {{-- Назва --}}
                        <h3 class="font-semibold text-lg text-gray-800 flex-1 pr-2">
                            <a href="{{ route('noclegi.show', $n->id) }}" class="hover:text-blue-600 transition">
                                {{ $n->title }}
                            </a>
                        </h3>
                        
                          @if($n->average_rating && $n->average_rating > 0)
                              <div class="bg-white/90 backdrop-blur-sm rounded-lg px-2 py-1 shadow-sm flex items-center gap-1">
                                  <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                      <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                  </svg>
                                  <span class="text-sm font-semibold text-gray-800">{{ number_format($n->average_rating, 1) }}</span>
                              </div>
                          @endif
                    </div>
                    
                    {{-- Місце --}}
                    <p class="text-sm text-gray-600">📍 {{ $n->city }}, {{ $n->street }}</p>
                    
                    {{-- Тип та місткість --}}
                    <p class="text-sm text-gray-600">
                        <span class="font-medium">Typ:</span> {{ $n->objectType?->name ?? '—' }}
                        @if($n->capacity)
                            | <span class="font-medium">Pojemność:</span> {{ $n->capacity }} os.
                        @endif
                    </p>
                    
                    {{-- Контакт --}}
                    <p class="text-sm text-gray-600">
                        <span class="font-medium">Kontakt:</span> {{ $n->contact_phone ?? '—' }}
                    </p>

                    {{-- СТАТУС + ПРИЧИНА --}}
                    <div class="space-y-3 pt-2">
                        <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700">Status:</span>

                        <div class="flex items-center gap-2">
                            @if ($n->status === 'pending')
                                <span class="px-3 py-1.5 bg-yellow-100 text-yellow-800 rounded-full text-sm font-medium">
                                    Oczekuje na zatwierdzenie
                                </span>

                                <a href="{{ route('noclegi.edit', $n->id) }}"
                                  class="text-sm text-blue-600 hover:underline font-medium">
                                    Pokaż zgłoszenie
                                </a>

                            @elseif ($n->status === 'approved')
                                <span class="px-3 py-1.5 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                                    Zatwierdzony
                                </span>

                            @else {{-- rejected --}}
                                <span class="px-3 py-1.5 bg-red-100 text-red-800 rounded-full text-sm font-medium">
                                    Odrzucony
                                </span>

                                <a href="{{ route('noclegi.edit', $n->id) }}"
                                    class="inline-flex items-center gap-1.5
                                            px-3 py-1.5
                                            text-sm font-medium
                                            bg-blue-600 text-white
                                            rounded-lg
                                            hover:bg-blue-700
                                            transition-colors">
                                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                      </svg>
                                      Pokaż zgłoszenie
                                  </a>

                            @endif
                        </div>
                    </div>


                    </div>
                    
                    {{-- Зручності (компактно) --}}
                    <div class="text-xs text-gray-700 flex gap-1 flex-wrap pt-1">
                        @if($n->has_kitchen) <span class="px-1.5 py-0.5 bg-gray-100 rounded">🍳</span> @endif
                        @if($n->has_parking) <span class="px-1.5 py-0.5 bg-gray-100 rounded">🅿️</span> @endif
                        @if($n->has_bathroom) <span class="px-1.5 py-0.5 bg-gray-100 rounded">🚿</span> @endif
                        @if($n->has_wifi) <span class="px-1.5 py-0.5 bg-gray-100 rounded">📶</span> @endif
                        @if($n->has_tv) <span class="px-1.5 py-0.5 bg-gray-100 rounded">📺</span> @endif
                        @if($n->has_balcony) <span class="px-1.5 py-0.5 bg-gray-100 rounded">🌅</span> @endif
                    </div>
                </div>
                @if($n->latitude && $n->longitude && $n->status === 'approved')
                        <button wire:click="showOnMap({{ $n->id }})"
                                class="w-56 py-3 bg-green-600 text-white text-center rounded-lg 
                                      hover:bg-green-700 transition-colors font-medium text-sm
                                      flex items-center justify-center gap-2 ml-4 mt-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                            </svg>
                            Pokaż na mapie
                        </button>
                @endif
                {{-- КНОПКИ ДІЙ (ЯК У ПРИКЛАДІ З КАЛЕНДАРЕМ) --}}
                <div class="px-4 pb-4 pt-3 border-t border-gray-100">
                    <div class="flex items-center justify-between gap-2">
                        <a href="{{ route('noclegi.show', $n->id) }}" 
                           class="flex-1 py-2.5 bg-blue-600 text-white text-center rounded-lg 
                                  hover:bg-blue-700 transition-colors font-medium text-sm">
                            Zobacz więcej
                        </a>
                        
                        {{-- Кнопки управління тільки для адміна --}}
                        @if(auth()->check() && Auth::user()->isAdmin())
                            <div class="flex gap-2">
                                <a href="{{ route('noclegi.edit', $n->id) }}" 
                                   class="w-10 h-10 flex items-center justify-center 
                                          bg-gray-100 text-gray-700 rounded-lg 
                                          hover:bg-gray-200 transition-colors"
                                   title="Edytuj obiekt">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                
                                {{-- Кнопка календаря тільки для approved статусу --}}
                                @if($n->status === 'approved')
                                    <a href="{{ route('noclegi.calendar', $n->id) }}" 
                                       class="w-10 h-10 flex items-center justify-center 
                                              bg-green-100 text-green-700 rounded-lg 
                                              hover:bg-green-200 transition-colors"
                                       title="Kalendarz">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </a>
                                @endif
                                
                                <button
                                    wire:click="deleteNocleg({{ $n->id }})"
                                    wire:loading.attr="disabled"
                                    wire:target="deleteNocleg"
                                    class="w-10 h-10 flex items-center justify-center 
                                          bg-red-100 text-red-700 rounded-lg 
                                          hover:bg-red-200 transition-colors"
                                    title="Usuń obiekt"
                                >

                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if ($noclegi->isEmpty())
        <div class="text-center py-12">
            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <p class="text-gray-500 text-lg mb-2">Brak noclegów spełniających kryteria wyszukiwania.</p>
            @if (
                $search ||
                    !empty($selectedTypeIds ?? []) ||
                    !empty($selectedAmenities ?? []) ||
                    $minCapacity ||
                    $maxCapacity ||
                    $minRating ||
                    $maxRating)
                <button wire:click="resetFilters" 
                        class="text-blue-600 hover:text-blue-800 font-medium">
                    Wyczyść filtry i pokaż wszystkie noclegi
                </button>
            @endif
        </div>
    @endif

    <div class="mt-6">
        {{ $noclegi->links() }}
    </div>
</div>