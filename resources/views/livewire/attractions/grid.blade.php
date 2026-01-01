<div class="p-6 space-y-6">
    {{-- Пошук та фільтри (взято з твого другого коду) --}}
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

    {{-- Сітка атракцій (взято з твого коду) --}}
    @if($attractions->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($attractions as $attraction)
                <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition-shadow duration-300 overflow-hidden border border-gray-100">
                    {{-- Карусель з фото --}}
                    <div class="relative aspect-video bg-gray-100 overflow-hidden">
                        @if($attraction->photos->isNotEmpty())
                            <div class="relative h-full"
                                 x-data="{
                                     currentPhoto: 0,
                                     photosCount: {{ $attraction->photos->count() }},
                                     nextPhoto() {
                                         this.currentPhoto = (this.currentPhoto + 1) % this.photosCount;
                                     },
                                     prevPhoto() {
                                         this.currentPhoto = (this.currentPhoto - 1 + this.photosCount) % this.photosCount;
                                     }
                                 }">
                                @foreach($attraction->photos as $index => $photo)
                                    <div class="absolute inset-0 transition-opacity duration-300 ease-in-out"
                                         :class="currentPhoto === {{ $index }} ? 'opacity-100 z-10' : 'opacity-0 z-0'">
                                        <img src="{{ asset($photo->path) }}" 
                                             alt="{{ $attraction->name }} - zdjęcie {{ $index + 1 }}"
                                             class="w-full h-full object-cover">
                                    </div>
                                @endforeach

                                @if($attraction->photos->count() > 1)
                                    <div class="absolute inset-0 flex items-center justify-between p-2 z-20">
                                        <button x-show="currentPhoto > 0"
                                                @click="prevPhoto()"
                                                class="w-8 h-8 bg-white/80 hover:bg-white rounded-full flex items-center justify-center shadow-md transition-all hover:scale-110">
                                            <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                            </svg>
                                        </button>

                                        <button x-show="currentPhoto < photosCount - 1"
                                                @click="nextPhoto()"
                                                class="w-8 h-8 bg-white/80 hover:bg-white rounded-full flex items-center justify-center shadow-md transition-all hover:scale-110 ml-auto">
                                            <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        @endif

                        {{-- Бейдж рейтингу --}}
                        <div class="absolute top-3 right-3 bg-white/90 backdrop-blur-sm px-3 py-1.5 rounded-full shadow-md z-30">
                            <div class="flex items-center gap-1">
                                <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                                <span class="font-bold text-gray-800">
                                    {{ number_format($attraction->average_rating ?? 0, 1) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Інформація -->
                    <div class="p-4 space-y-3">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 line-clamp-1">
                                <a href="{{ route('attractions.show', $attraction->id) }}" class="hover:underline">
                                    {{ $attraction->name }}
                                </a>
                            </h3>
                            <div class="flex items-center gap-1 text-sm text-gray-600 mt-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span class="line-clamp-1">{{ $attraction->location }}</span>
                            </div>
                        </div>

                        <p class="text-sm text-gray-700 line-clamp-2">{{ $attraction->description }}</p>

                        @if($attraction->categories->isNotEmpty())
                            <div class="flex flex-wrap gap-1">
                                @foreach($attraction->categories as $category)
                                    <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded-full">
                                        {{ $category->name }}
                                    </span>
                                @endforeach
                            </div>
                        @endif

                        @if($attraction->opening_time || $attraction->closing_time)
                            <div class="text-sm text-gray-600">
                                <div class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>
                                        @if($attraction->opening_time && $attraction->closing_time)
                                            {{ \Carbon\Carbon::parse($attraction->opening_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($attraction->closing_time)->format('H:i') }}
                                        @elseif($attraction->opening_time)
                                            Od {{ \Carbon\Carbon::parse($attraction->opening_time)->format('H:i') }}
                                        @elseif($attraction->closing_time)
                                            Do {{ \Carbon\Carbon::parse($attraction->closing_time)->format('H:i') }}
                                        @endif
                                    </span>
                                </div>
                            </div>
                        @endif

                        @if(auth()->check() && !$attraction->is_active)
                            <div class="inline-flex items-center gap-1 px-2 py-1 bg-red-100 text-red-700 text-xs rounded-full">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Ukryta
                            </div>
                        @endif
                    </div>

                    <!-- Кнопки дій -->
                    <div class="px-4 pb-4 pt-2 border-t border-gray-100">
                        <div class="flex items-center justify-between gap-2">
                            <a href="{{ route('attractions.show', $attraction->id) }}" 
                               class="flex-1 py-2 bg-blue-600 text-white text-center rounded-lg hover:bg-blue-700 transition font-medium text-sm">
                                Zobacz
                            </a>
                            
                            @if(auth()->check())
                                <div class="flex gap-2">
                                    <a href="{{ route('attractions.edit', $attraction->id) }}" 
                                       class="w-10 h-10 flex items-center justify-center bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition"
                                       title="Edytuj">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    
                                    <button wire:click="toggleActive({{ $attraction->id }})"
                                            class="w-10 h-10 flex items-center justify-center {{ $attraction->is_active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' }} rounded-lg transition"
                                            title="{{ $attraction->is_active ? 'Ukryj' : 'Aktywuj' }}">
                                        @if($attraction->is_active)
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L6.59 6.59m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                            </svg>
                                        @else
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        @endif
                                    </button>
                                    
                                    <button wire:click="deleteAttraction({{ $attraction->id }})"
                                            onclick="return confirm('Czy na pewno chcesz usunąć tę atrakcję?')"
                                            class="w-10 h-10 flex items-center justify-center bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition"
                                            title="Usuń">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-12">
            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <p class="text-gray-500 text-lg mb-2">Brak atrakcji spełniających kryteria wyszukiwania.</p>
            @if($search || !empty($selectedCategories) || $minRating || $maxRating)
                <button wire:click="resetFilters" 
                        class="text-blue-600 hover:text-blue-800 font-medium">
                    Wyczyść filtry i pokaż wszystkie atrakcje
                </button>
            @endif
        </div>
    @endif

    {{-- Пагінація --}}
    @if($attractions->hasPages())
        <div class="mt-8">
            {{ $attractions->links() }}
        </div>
    @endif
</div>