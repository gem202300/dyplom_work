<div class="p-6 bg-white rounded-lg shadow-md">
    <form wire:submit.prevent="submit" enctype="multipart/form-data" class="space-y-5">

        <h2 class="text-2xl font-bold text-gray-800 mb-4">
            {{ $attraction->exists ? __('Edycja atrakcji') : __('Dodaj atrakcję') }}
        </h2>

        {{-- Nazwa --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Nazwa atrakcji <span class="text-red-500">*</span>
            </label>
            <input type="text" wire:model.defer="name"
                   class="w-full border border-gray-300 rounded-md px-4 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Lokalizacja (tekst) --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Adres <span class="text-red-500">*</span>
            </label>
            <input type="text" wire:model.defer="location"
                   class="w-full border border-gray-300 rounded-md px-4 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            @error('location') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- 📍 MAPA --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Wybierz lokalizację na mapie
                <span class="text-xs text-gray-500 font-normal">(kliknij na mapę)</span>
            </label>

            <div id="location-map"
                 wire:ignore
                 style="width:100%; height:350px;"
                 class="border border-gray-300 rounded-lg">
            </div>

            <div class="flex gap-6 mt-2 text-sm text-gray-600">
                <span>Lat: <strong id="lat-preview" class="font-mono">{{ $latitude ? number_format($latitude, 6) : 'nie wybrano' }}</strong></span>
                <span>Lng: <strong id="lng-preview" class="font-mono">{{ $longitude ? number_format($longitude, 6) : 'nie wybrano' }}</strong></span>
            </div>

            <p class="text-xs text-gray-500 mt-1">Możesz też przeciągnąć znacznik na mapie</p>

            @error('latitude') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            @error('longitude') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Opis --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Opis</label>
            <textarea wire:model.defer="description" rows="4"
                      class="w-full border border-gray-300 rounded-md px-4 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 resize-none"
                      placeholder="Opisz atrakcję..."></textarea>
            @error('description') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Godziny --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Godzina otwarcia</label>
                <input type="time" wire:model.defer="opening_time"
                       class="w-full border border-gray-300 rounded-md px-4 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                @error('opening_time') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Godzina zamknięcia</label>
                <input type="time" wire:model.defer="closing_time"
                       class="w-full border border-gray-300 rounded-md px-4 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                @error('closing_time') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Kategorie --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Kategorie <span class="text-red-500">*</span>
            </label>
            <div x-data="{ open: false }" class="relative">
                <button type="button" @click="open = !open"
                        class="w-full border border-gray-300 rounded-md px-4 py-2 text-left focus:border-blue-500 focus:ring-1 focus:ring-blue-500 flex justify-between items-center">
                    <span>
                        {{ count($selectedCategories) ? 'Wybrane (' . count($selectedCategories) . ')' : 'Wybierz kategorie' }}
                    </span>
                    <span :class="{'rotate-180': open}" class="transition-transform">▼</span>
                </button>

                <div x-show="open" @click.outside="open = false" x-cloak
                     class="absolute z-10 mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto w-full">
                    @foreach($allCategories as $category)
                        <label class="flex items-center px-4 py-2 hover:bg-gray-50 cursor-pointer">
                            <input type="checkbox"
                                   value="{{ $category->id }}"
                                   wire:model.defer="selectedCategories"
                                   class="mr-2 rounded text-blue-600 focus:ring-blue-500">
                            <span class="text-gray-700">{{ $category->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
            @error('selectedCategories') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Upload --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Zdjęcia</label>
            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition-colors">
                <input type="file" multiple wire:model="photos" id="photoUpload" class="hidden">
                <label for="photoUpload" class="cursor-pointer block">
                    <div class="flex flex-col items-center justify-center py-4">
                        <svg class="w-10 h-10 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <p class="text-gray-700">Kliknij lub przeciągnij zdjęcia</p>
                        <p class="text-gray-500 text-sm mt-1">PNG, JPG, GIF do 2MB</p>
                    </div>
                </label>
            </div>
            @error('photos.*') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            
            {{-- Podgląd nowych zdjęć --}}
            @if($photos)
                <div class="mt-3">
                    <p class="text-sm text-gray-600 mb-2">Nowe zdjęcia ({{ count($photos) }}):</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($photos as $index => $photo)
                            <div class="relative">
                                <img src="{{ $photo->temporaryUrl() }}" 
                                     class="w-20 h-20 object-cover rounded border">
                                <button type="button" wire:click="removePhoto({{ $index }})"
                                        class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs hover:bg-red-600">
                                    ×
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- Galeria istniejących zdjęć --}}
        @if($attraction->exists && $attraction->photos->isNotEmpty())
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Aktualne zdjęcia</label>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @foreach($attraction->photos->whereNotIn('id', $photosToDelete) as $photo)
                        <div class="relative group">
                            <img src="{{ asset($photo->path) }}"
                                 class="w-full h-32 object-cover rounded-lg border">
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100">
                                <button type="button"
                                        wire:click="deletePhoto({{ $photo->id }})"
                                        class="bg-red-600 text-white p-2 rounded-full hover:bg-red-700 transition-colors"
                                        title="Usuń zdjęcie">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Akcje --}}
        <div class="flex justify-end gap-4 mt-8 pt-6 border-t border-gray-200">
            <a href="{{ route('attractions.index') }}"
               class="px-6 py-2.5 bg-gray-100 text-gray-800 rounded-lg hover:bg-gray-200 transition-colors font-medium">
                Anuluj
            </a>

            <button type="submit"
                    class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                {{ $attraction->exists ? 'Zapisz zmiany' : 'Dodaj atrakcję' }}
            </button>
        </div>

    </form>

    {{-- MapLibre --}}
    <link rel="stylesheet" href="https://unpkg.com/maplibre-gl@4.7.0/dist/maplibre-gl.css">
    <script src="https://unpkg.com/maplibre-gl@4.7.0/dist/maplibre-gl.js"></script>

    <style>
        [x-cloak] { display: none !important; }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                const lat = @json($latitude ?? 52.2297);
                const lng = @json($longitude ?? 21.0122);

                // Створення мапи з 3D ефектом
                const map = new maplibregl.Map({
                    container: 'location-map',
                    style: 'https://api.maptiler.com/maps/streets-v2/style.json?key=uJDiq16jXWiNZLGeCJ0m',
                    center: [lng, lat],
                    zoom: 14,
                    pitch: 45, // Додаємо 3D нахил
                    bearing: 0
                });

                // Додаємо навігацію
                map.addControl(new maplibregl.NavigationControl());

                // Створюємо маркер
                const marker = new maplibregl.Marker({ 
                    draggable: true,
                    color: '#3B82F6' // Синій колір замість чорного
                })
                .setLngLat([lng, lat])
                .addTo(map);

                // Функція синхронізації координат
                function sync(lngLat) {
                    // Оновлюємо Livewire
                    Livewire.find(@this.__instance.id).set('latitude', lngLat.lat);
                    Livewire.find(@this.__instance.id).set('longitude', lngLat.lng);

                    // Оновлюємо підпис з форматуванням
                    document.getElementById('lat-preview').innerText = lngLat.lat.toFixed(6);
                    document.getElementById('lng-preview').innerText = lngLat.lng.toFixed(6);
                }

                // Події маркера
                marker.on('dragend', () => sync(marker.getLngLat()));
                
                // Подія кліку на карті
                map.on('click', e => {
                    marker.setLngLat(e.lngLat);
                    sync(e.lngLat);
                    
                    // Невелика анімація перельоту
                    map.flyTo({
                        center: e.lngLat,
                        zoom: 15,
                        duration: 1000
                    });
                });

                // Автоматичний ресайз мапи
                setTimeout(() => map.resize(), 200);

            }, 300);
        });
    </script>

</div>