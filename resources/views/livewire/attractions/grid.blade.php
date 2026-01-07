<div class="p-6 space-y-6">
    {{-- Пошук та фільтри --}}
    <div class="flex justify-between items-center gap-4 mb-6">
        {{-- Пошук БЕЗ іконки --}}
        <div class="flex-1">
            <input type="text" wire:model.debounce.500ms="search"
                   placeholder="Wpisz słowa kluczowe (np. 'zamek', 'jezioro')"
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg 
                          bg-white focus:ring-2 focus:ring-blue-400 focus:border-blue-400 
                          focus:outline-none transition-colors" />
        </div>

        {{-- Кнопка фільтрів --}}
        <button wire:click="$toggle('showFilters')"
                class="bg-blue-600 text-white px-4 py-3 rounded-lg shadow hover:bg-blue-700 
                       transition-all duration-200 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
            </svg>
            Filtry
            @if($search || !empty($selectedCategories) || $minRating || ($maxRating && $maxRating != 5))
                <span class="inline-flex items-center justify-center w-5 h-5 ml-1 text-xs 
                            bg-white/30 rounded-full">
                    {{ ($search?1:0) + count($selectedCategories) + ($minRating?1:0) + ($maxRating && $maxRating != 5 ? 1 : 0) }}
                </span>
            @endif
        </button>
    </div>

    {{-- Випадаюче вікно фільтрів --}}
    @if ($showFilters)
        <div class="bg-white p-6 rounded-lg shadow-lg border border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Фільтр за категоріями --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Typ atrakcji</label>
                    <div class="border border-gray-300 rounded-lg p-2 bg-white"
                         style="max-height: 180px; overflow-y: auto;">
                        @foreach ($categories as $category)
                            <div class="flex items-center p-2 hover:bg-gray-50 rounded cursor-pointer transition-colors"
                                 wire:click="toggleCategory({{ $category->id }})">
                                <div class="w-5 h-5 mr-3 flex items-center justify-center">
                                    @if (in_array($category->id, $selectedCategories))
                                        <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                  d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                  clip-rule="evenodd" />
                                        </svg>
                                    @else
                                        <div class="w-4 h-4 border border-gray-300 rounded-sm"></div>
                                    @endif
                                </div>
                                <span class="text-sm text-gray-700">
                                    {{ $category->name }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                    @if (count($selectedCategories) > 0)
                        <p class="text-sm text-gray-500 mt-2">Wybrano: {{ count($selectedCategories) }}</p>
                    @endif
                </div>

                {{-- Фільтр за оцінкою --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Ocena</label>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Min ocena</label>
                            <div class="flex items-center gap-3">
                                <input type="range" wire:model.live="minRating" min="0" max="5" step="0.5"
                                       class="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer"
                                       id="minRatingSlider">
                                <span class="text-sm font-medium text-blue-600 w-12 text-center"
                                      id="minRatingValue">
                                    {{ $minRating ?? 0 }}⭐
                                </span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Max ocena</label>
                            <div class="flex items-center gap-3">
                                <input type="range" wire:model.live="maxRating" min="0" max="5" step="0.5"
                                       class="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer"
                                       id="maxRatingSlider">
                                <span class="text-sm font-medium text-blue-600 w-12 text-center"
                                      id="maxRatingValue">
                                    {{ $maxRating ?? 5 }}⭐
                                </span>
                            </div>
                        </div>
                        @if($hasRatingError)
                            <p class="text-sm text-red-600 mt-2">Min ocena nie może być wyższa niż max ocena!</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Кнопки дій фільтрів --}}
            <div class="flex justify-between items-center mt-8 pt-6 border-t border-gray-200">
                <button wire:click="resetFilters"
                        class="text-gray-600 hover:text-gray-800 px-4 py-2 rounded-lg 
                               hover:bg-gray-100 transition-colors">
                    Wyczyść filtry
                </button>
                <button wire:click="applyFilters"
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 
                               transition-colors {{ $hasRatingError ? 'opacity-50 cursor-not-allowed' : '' }}"
                        @if($hasRatingError) disabled @endif>
                    Zastosuj
                </button>
            </div>
        </div>
    @endif

    {{-- Активні фільтри --}}
    @if ($search || !empty($selectedCategories) || $minRating || ($maxRating && $maxRating != 5))
        <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm font-medium text-blue-800">Aktywne filtry:</p>
                <button wire:click="resetFilters"
                        class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                    Wyczyść wszystkie
                </button>
            </div>
            <div class="flex flex-wrap gap-2">
                @if ($search)
                    <span class="inline-flex items-center bg-white border border-blue-200 
                                text-blue-700 px-3 py-1.5 rounded-full text-sm">
                        🔍 "{{ $search }}"
                        <button wire:click="$set('search', '')" 
                                class="ml-1.5 text-blue-400 hover:text-blue-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </span>
                @endif

                @foreach ($selectedCategories as $catId)
                    @php $cat = $categories->firstWhere('id', $catId); @endphp
                    @if ($cat)
                        <span class="inline-flex items-center bg-white border border-green-200 
                                    text-green-700 px-3 py-1.5 rounded-full text-sm">
                           {{ $cat->name }}
                            <button wire:click="removeCategory({{ $catId }})" 
                                    class="ml-1.5 text-green-400 hover:text-green-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </span>
                    @endif
                @endforeach

                @if ($minRating)
                    <span class="inline-flex items-center bg-white border border-yellow-200 
                                text-yellow-700 px-3 py-1.5 rounded-full text-sm">
                        Min: {{ $minRating }}⭐
                        <button wire:click="$set('minRating', null)" 
                                class="ml-1.5 text-yellow-400 hover:text-yellow-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </span>
                @endif

                @if ($maxRating && $maxRating != 5)
                    <span class="inline-flex items-center bg-white border border-yellow-200 
                                text-yellow-700 px-3 py-1.5 rounded-full text-sm">
                        Max: {{ $maxRating }}⭐
                        <button wire:click="$set('maxRating', null)" 
                                class="ml-1.5 text-yellow-400 hover:text-yellow-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </span>
                @endif
            </div>
        </div>
    @endif

    {{-- Сітка атракцій - 3 колонки --}}
    @if($attractions->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($attractions as $attraction)
                {{-- ЗМІНА: Показуємо атракцію тільки якщо вона активна АБО користувач адмін --}}
                @if($attraction->is_active || (auth()->check() && Auth::user()->isAdmin()))
                    {{-- Картка атракції --}}
                    <div class="bg-white shadow-lg rounded-xl overflow-hidden hover:shadow-2xl transition-all duration-300 border border-gray-100">
                      {{-- Карусель з фото БЕЗ рейтингу --}}
                      <x-photo-carousel 
                          :photos="$attraction->photos" 
                          :showRating="false"
                          :rating="$attraction->rating"
                          :alt="$attraction->name"
                          containerClass="aspect-[4/3]"
                          :showDots="true"
                          :showCounter="false"
                          wire:key="carousel-{{ $attraction->id }}"
                      />

                      {{-- Інформація --}}
                      <div class="p-4 space-y-3">
                          {{-- Весь внутрішній контент залишається без змін --}}
                          <div class="flex items-start justify-between gap-2">
                              <div class="flex-1">
                                  <h3 class="text-lg font-bold text-gray-900 line-clamp-1">
                                      <a href="{{ route('attractions.show', $attraction->id) }}" 
                                        class="hover:text-blue-600 transition-colors">
                                          {{ $attraction->name }}
                                      </a>
                                  </h3>
                                  <div class="flex items-center gap-1 text-sm text-gray-600 mt-1">
                                      <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                      </svg>
                                      <span class="line-clamp-1">{{ $attraction->location }}</span>
                                  </div>
                              </div>
                              
                              @if($attraction->average_rating)
                                    <div class="flex-shrink-0">
                                  <div class="bg-white/90 backdrop-blur-sm rounded-lg px-2 py-1 shadow-sm flex items-center gap-1">
                                      <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                          <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                      </svg>
                                      <span class="text-sm font-semibold text-gray-800">
                                          {{ number_format($attraction->average_rating ?? 0, 1) }}
                                      </span>
                                  </div>
                              </div>
                              @endif
                          </div>

                          <p class="text-sm text-gray-700 line-clamp-2">
                              {{ $attraction->description }}
                          </p>

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
                              <div class="flex items-center text-sm text-gray-600">
                                  <svg class="w-4 h-4 mr-1.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                  </svg>
                                  <span>
                                      @if($attraction->opening_time && $attraction->closing_time)
                                          {{ \Carbon\Carbon::parse($attraction->opening_time)->format('H:i') }} - 
                                          {{ \Carbon\Carbon::parse($attraction->closing_time)->format('H:i') }}
                                      @elseif($attraction->opening_time)
                                          Od {{ \Carbon\Carbon::parse($attraction->opening_time)->format('H:i') }}
                                      @elseif($attraction->closing_time)
                                          Do {{ \Carbon\Carbon::parse($attraction->closing_time)->format('H:i') }}
                                      @endif
                                  </span>
                              </div>
                          @endif

                          @if(auth()->check() && Auth::user()->isAdmin() && !$attraction->is_active)
                              <div class="inline-flex items-center gap-1 px-2 py-1 bg-red-100 
                                          text-red-700 text-xs rounded-full border border-red-200">
                                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                  </svg>
                                  Ukryta
                              </div>
                          @endif
                      </div>
                      @if($attraction->latitude && $attraction->longitude)
                          <button wire:click="showOnMap({{ $attraction->id }})"
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
                      {{-- Кнопки дій --}}
                      <div class="px-4 pb-4 pt-3 border-t border-gray-100">
                          <div class="flex items-center justify-between gap-2">
                              <a href="{{ route('attractions.show', $attraction->id) }}" 
                                class="flex-1 py-2.5 bg-blue-600 text-white text-center rounded-lg 
                                        hover:bg-blue-700 transition-colors font-medium text-sm">
                                  Zobacz więcej
                              </a>
                               
                              @if(auth()->check() && Auth::user()->isAdmin())
                                  <div class="flex gap-2">
                                      <a href="{{ route('attractions.edit', $attraction->id) }}" 
                                        class="w-10 h-10 flex items-center justify-center 
                                                bg-gray-100 text-gray-700 rounded-lg 
                                                hover:bg-gray-200 transition-colors"
                                        title="Edytuj">
                                          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                          </svg>
                                      </a>
                                      
                                      <button wire:click="toggleActive({{ $attraction->id }})"
                                              class="w-10 h-10 flex items-center justify-center 
                                                    {{ $attraction->is_active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' }} 
                                                    rounded-lg transition-colors"
                                              title="{{ $attraction->is_active ? 'Ukryj' : 'Aktywuj' }}">
                                          @if($attraction->is_active)
                                              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L6.59 6.59m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                              </svg>
                                          @else
                                              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                              </svg>
                                          @endif
                                      </button>
                                      
                                      <button wire:click="attemptDelete({{ $attraction->id }})"
                                              class="w-10 h-10 flex items-center justify-center 
                                                    bg-red-100 text-red-700 rounded-lg 
                                                    hover:bg-red-200 transition-colors cursor-pointer"
                                              title="Usuń">
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
                @endif
            @endforeach
        </div>
    @else
        <div class="text-center py-12">
            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                      d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-gray-500 text-lg mb-3">Brak atrakcji spełniających kryteria wyszukiwania.</p>
            @if($search || !empty($selectedCategories) || $minRating || $maxRating)
                <button wire:click="resetFilters" 
                        class="text-blue-600 hover:text-blue-800 font-medium transition-colors">
                    Wyczyść filtry i pokaż wszystkie atrakcje
                </button>
            @endif
        </div>
    @endif

    @if($attractions->hasPages())
        <div class="mt-8">
            {{ $attractions->links() }}
        </div>
    @endif

    <script>
        document.addEventListener('livewire:initialized', () => {
            const minRatingSlider = document.getElementById('minRatingSlider');
            const minRatingValue = document.getElementById('minRatingValue');
            
            if (minRatingSlider && minRatingValue) {
                minRatingSlider.addEventListener('input', function() {
                    minRatingValue.textContent = this.value + '⭐';
                });
            }
            
            const maxRatingSlider = document.getElementById('maxRatingSlider');
            const maxRatingValue = document.getElementById('maxRatingValue');
            
            if (maxRatingSlider && maxRatingValue) {
                maxRatingSlider.addEventListener('input', function() {
                    maxRatingValue.textContent = this.value + '⭐';
                });
            }
        });
    </script>
</div>