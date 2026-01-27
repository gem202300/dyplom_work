<div class="p-6 bg-white rounded-lg shadow-md">
    @if ($nocleg->exists && $nocleg->status === 'rejected')
        <div class="mb-8 p-6 bg-red-50 border-2 border-red-300 rounded-xl">
            <div class="flex items-start gap-4">
                <div class="shrink-0">
                    <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-xl font-bold text-red-800 mb-3">
                        Zgłoszenie zostało odrzucone
                    </h3>
                    <p class="text-red-700 text-base leading-relaxed mb-4">
                        <strong>Powód:</strong> {{ $reject_reason }}
                    </p>
                    <p class="text-red-600 text-sm">
                        Prosimy o wprowadzenie poprawek i ponowne przesłanie obiektu do moderacji.
                    </p>
                </div>
            </div>
        </div>
    @endif

    @if ($nocleg->exists && $nocleg->status === 'pending')
        <div class="mb-8 p-6 bg-amber-50 border-2 border-amber-300 rounded-xl">
            <div class="flex items-center gap-4">
                <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-amber-800 font-medium">
                    Obiekt oczekuje na zatwierdzenie. Po zapisaniu zmian zostanie ponownie przesłany do moderacji.
                </p>
            </div>
        </div>
    @endif

    <form wire:submit.prevent="submit" enctype="multipart/form-data" class="space-y-5">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">
            {{ $nocleg->exists ? 'Edytuj nocleg' : 'Dodaj nocleg' }}
        </h2>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Tytuł <span class="text-red-500">*</span>
            </label>
            <input type="text" wire:model.defer="title"
                   class="w-full border border-gray-300 rounded-md px-4 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500" />
            @error('title') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Opis</label>
            <textarea wire:model.defer="description" rows="4"
                      class="w-full border border-gray-300 rounded-md px-4 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 resize-none"
                      placeholder="Opisz nocleg..."></textarea>
            @error('description') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Miasto <span class="text-red-500">*</span>
                </label>
                <input type="text" wire:model.defer="city"
                       class="w-full border border-gray-300 rounded-md px-4 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500" />
                @error('city') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Ulica <span class="text-red-500">*</span>
                </label>
                <input type="text" wire:model.defer="street"
                       class="w-full border border-gray-300 rounded-md px-4 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500" />
                @error('street') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
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
        {{-- ДОДАНО: Вибор іконки для мапи --}}
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">
        Ikona na mapie <span class="text-red-500">*</span>
        <span class="text-xs text-gray-500 font-normal">(będzie użyta do wyświetlania na mapie)</span>
    </label>
    
    {{-- Підказка для вибору іконки --}}
    @if($object_type_id)
        <div class="mb-3">
            <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg border border-blue-100">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-blue-800">Chcesz wybrać ikonę automatycznie?</p>
                        <p class="text-xs text-blue-600">System może zaproponować ikonę na podstawie typu obiektu.</p>
                    </div>
                </div>
                <button type="button" 
                        wire:click="suggestIconByType"
                        class="flex items-center gap-1 px-3 py-1.5 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                    Podpowiedź
                </button>
            </div>
        </div>
    @endif
  
    {{-- Вибір іконки --}}
    <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-3 p-4 border border-gray-200 rounded-lg bg-gray-50">
        @foreach($mapIcons as $icon)
            <label class="relative cursor-pointer group">
                <input type="radio"
                       name="map_icon"
                       value="{{ $icon->icon_url }}"
                       wire:model="mapIcon"
                       class="sr-only peer">

                <div class="border-2 border-gray-300 rounded-lg p-4 flex flex-col items-center justify-center transition-all duration-300
                            hover:border-gray-400 hover:bg-gray-100
                            peer-checked:border-blue-600 peer-checked:bg-blue-50 peer-checked:ring-4 peer-checked:ring-blue-200">
                    
                    <!-- Іконка -->
                    <img src="{{ $icon->icon_url }}"
                         alt="{{ $icon->name }}"
                         class="w-12 h-12 object-contain filter grayscale transition-all duration-300
                                group-hover:grayscale-0
                                peer-checked:grayscale-0">
                    <!-- Назва -->
                    <span class="mt-2 text-xs text-gray-600 text-center">{{ $icon->name }}</span>
                </div>
            </label>
        @endforeach
    </div>
    
    {{-- Підгляд вибраної іконки --}}
    @if($mapIcon)
        <div class="mt-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="relative">
                        <img src="{{ $mapIcon }}" alt="Wybrana ikona" class="w-10 h-10 object-contain filter grayscale">
                        <div class="absolute -top-1 -right-1 w-4 h-4 bg-blue-600 rounded-full flex items-center justify-center">
                            <svg class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-700">Wybrana ikona</p>
                        <p class="text-xs text-gray-500">Ta ikona będzie wyświetlana na mapie</p>
                    </div>
                </div>
                <button type="button" 
                        wire:click="$set('mapIcon', '')"
                        class="flex items-center gap-1 px-3 py-1.5 text-sm bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Zmień
                </button>
            </div>
        </div>
    @else
        <div class="mt-3 p-3 bg-yellow-50 rounded-lg border border-yellow-200">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <p class="text-sm text-yellow-700">Nie wybrano ikony. Wybierz jedną z ikon powyżej.</p>
            </div>
        </div>
    @endif
    
    @error('mapIcon') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
</div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Typ obiektu <span class="text-red-500">*</span>
                </label>
                <select wire:model.defer="object_type_id"
                        class="w-full border border-gray-300 rounded-md px-4 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 bg-white">
                    <option value="">Wybierz typ</option>
                    @foreach($objectTypes as $type)
                        <option value="{{ $type->id }}">
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
                @error('object_type_id') 
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p> 
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Liczba miejsc <span class="text-red-500">*</span>
                </label>
                <input type="number" wire:model.defer="capacity" min="1"
                       class="w-full border border-gray-300 rounded-md px-4 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500" />
                @error('capacity') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Telefon kontaktowy</label>
                <input type="text" wire:model.defer="contact_phone"
                       class="w-full border border-gray-300 rounded-md px-4 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500" />
                @error('contact_phone') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Link do strony</label>
                <input type="url" wire:model.defer="link"
                       class="w-full border border-gray-300 rounded-md px-4 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500" />
                @error('link') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Wyposażenie</label>

            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                @foreach($allAmenities as $key => $label)
                    @if($key !== 'inne')
                        <label class="flex items-center space-x-2 p-2 hover:bg-gray-50 rounded">
                            <input type="checkbox" value="{{ $key }}" wire:model="amenities" 
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="text-gray-700">{{ $label }}</span>
                        </label>
                    @endif
                @endforeach
            </div>

            @error('amenities') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Inne udogodnienia</label>
            <input type="text" wire:model.defer="other_amenities"
                  class="w-full border border-gray-300 rounded-md px-4 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                  placeholder="Np. basen, siłownia, sauna..." />
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Zdjęcia @if(!$nocleg->exists) <span class="text-red-500">*</span> @endif
            </label>
            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition-colors">
                <input type="file" multiple wire:model="photos" id="photoUpload" class="hidden" />
                <label for="photoUpload" class="cursor-pointer block">
                    <div class="flex flex-col items-center justify-center py-4">
                        <svg class="w-10 h-10 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <p class="text-gray-700">Kliknij lub przeciągnij zdjęcia</p>
                        <p class="text-gray-500 text-sm mt-1">PNG, JPG do 2MB</p>
                    </div>
                </label>
            </div>
            @error('photos') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            
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
        @if($nocleg->exists && $nocleg->photos->isNotEmpty())
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Aktualne zdjęcia</label>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @foreach($nocleg->photos->whereNotIn('id', $photosToDelete) as $photo)
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

        <div class="flex justify-end gap-4 mt-8 pt-6 border-t border-gray-200">
            <a href="{{ route('noclegi.index') }}"
               class="px-6 py-2.5 bg-gray-100 text-gray-800 rounded-lg hover:bg-gray-200 transition-colors font-medium">
                Anuluj
            </a>

            <button type="submit"
                    class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                {{ $nocleg->exists ? 'Zapisz zmiany' : 'Dodaj nocleg' }}
            </button>
        </div>
    </form>

    {{-- MapLibre --}}
    <link rel="stylesheet" href="https://unpkg.com/maplibre-gl@4.7.0/dist/maplibre-gl.css">
    <script src="https://unpkg.com/maplibre-gl@4.7.0/dist/maplibre-gl.js"></script>

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
                    color: '#3B82F6' // Синій колір
                })
                .setLngLat([lng, lat])
                .addTo(map);

                // Функція синхронізації координат
                function sync(lngLat) {
                    // Оновлюємо Livewire
                    const livewire = Livewire.find(@this.__instance.id);
                    if (livewire) {
                        livewire.set('latitude', lngLat.lat);
                        livewire.set('longitude', lngLat.lng);
                    }

                    // Оновлюємо підпис
                    const latPreview = document.getElementById('lat-preview');
                    const lngPreview = document.getElementById('lng-preview');
                    if (latPreview && lngPreview) {
                        latPreview.textContent = lngLat.lat.toFixed(6);
                        lngPreview.textContent = lngLat.lng.toFixed(6);
                    }
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