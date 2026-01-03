<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">{{ $nocleg->title }}</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white p-6 rounded-lg shadow space-y-6 text-black">

            <!-- Informacje o noclegu -->
            <p class="text-sm text-gray-600 flex items-center gap-2">
                📍 <strong>{{ $nocleg->city }}, {{ $nocleg->street }}</strong>
            </p>

            @if($nocleg->user)
                <div class="pt-6 bg-gray-50 p-4 rounded-lg shadow">
                    <h3 class="text-lg font-semibold mb-2">Dodane przez</h3>
                    <p><strong>Imię:</strong> {{ $nocleg->user->name }}</p>
                    <p><strong>Email:</strong> {{ $nocleg->user->email }}</p>
                    <p><strong>Telefon:</strong> {{ $nocleg->user->phone ?? '—' }}</p>
                </div>
            @endif

            @if($nocleg->description)
                <div>
                    <p class="text-gray-800 leading-relaxed">{{ $nocleg->description }}</p>
                </div>
            @endif

            <!-- Karuzela zdjęć -->
            @if($nocleg->photos->isNotEmpty())
                <div class="space-y-4"
                     x-data="{
                         currentIndex: 0,
                         photosCount: {{ $nocleg->photos->count() }},
                         changePhoto(index) {
                             this.currentIndex = index;
                         },
                         nextPhoto() {
                             this.currentIndex = (this.currentIndex + 1) % this.photosCount;
                         },
                         prevPhoto() {
                             this.currentIndex = (this.currentIndex - 1 + this.photosCount) % this.photosCount;
                         }
                     }">
                    
                    <!-- Główne zdjęcie -->
                    <div class="relative bg-gray-100 rounded-lg overflow-hidden border border-gray-200 max-w-3xl mx-auto">
                        <div class="aspect-w-16 aspect-h-10">
                            @foreach($nocleg->photos as $index => $photo)
                                <div class="absolute inset-0 transition-opacity duration-300"
                                     :class="currentIndex === {{ $index }} ? 'opacity-100 z-10' : 'opacity-0 z-0'">
                                    <img src="{{ asset($photo->path) }}"
                                         alt="Zdjęcie noclegu {{ $index + 1 }}"
                                         class="w-full h-full object-cover">
                                </div>
                            @endforeach
                        </div>

                        <!-- Strzałki nawigacji -->
                        @if($nocleg->photos->count() > 1)
                            <div class="absolute inset-0 flex items-center justify-between p-3 z-20">
                                <button x-show="currentIndex > 0"
                                        @click="prevPhoto()"
                                        class="w-10 h-10 bg-white/80 hover:bg-white rounded-full flex items-center justify-center shadow-lg transition-all hover:scale-110">
                                    <svg class="w-5 h-5 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                    </svg>
                                </button>

                                <button x-show="currentIndex < photosCount - 1"
                                        @click="nextPhoto()"
                                        class="w-10 h-10 bg-white/80 hover:bg-white rounded-full flex items-center justify-center shadow-lg transition-all hover:scale-110 ml-auto">
                                    <svg class="w-5 h-5 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </button>
                            </div>
                        @endif

                        <!-- Kropki-indykatory -->
                        @if($nocleg->photos->count() > 1)
                            <div class="absolute bottom-3 left-1/2 transform -translate-x-1/2 z-20">
                                <div class="flex space-x-2">
                                    @foreach($nocleg->photos as $index => $photo)
                                        <button @click="changePhoto({{ $index }})"
                                                class="w-2.5 h-2.5 rounded-full transition-all duration-300"
                                                :class="currentIndex === {{ $index }} 
                                                       ? 'bg-white scale-125' 
                                                       : 'bg-white/60 hover:bg-white/80'">
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Numer zdjęcia -->
                        <div class="absolute top-3 right-3 bg-black/50 text-white text-xs px-2 py-1 rounded z-20">
                            <span x-text="currentIndex + 1"></span>/<span x-text="photosCount"></span>
                        </div>
                    </div>

                    <!-- Galeria miniatur -->
                    @if($nocleg->photos->count() > 1)
                        <div class="max-w-3xl mx-auto">
                            <div class="flex gap-2 overflow-x-auto py-2 px-1 scrollbar-hide justify-center">
                                @foreach($nocleg->photos as $index => $photo)
                                    <button @click="changePhoto({{ $index }})"
                                            class="flex-shrink-0 w-16 h-14 rounded overflow-hidden border-2 transition-all relative group"
                                            :class="currentIndex === {{ $index }} 
                                                   ? 'border-blue-500 shadow' 
                                                   : 'border-gray-300 hover:border-blue-300'">
                                        <img src="{{ asset($photo->path) }}"
                                             alt="Miniatura {{ $index + 1 }}"
                                             class="w-full h-full object-cover">
                                        
                                        <div class="absolute inset-0 transition-opacity duration-300"
                                             :class="currentIndex === {{ $index }} 
                                                    ? 'bg-blue-500/20' 
                                                    : 'group-hover:bg-blue-500/10'">
                                        </div>
                                        
                                        <div class="absolute top-1 right-1 w-4 h-4 rounded-full bg-blue-500 text-white text-[10px] flex items-center justify-center font-bold"
                                             :class="currentIndex === {{ $index }} ? 'opacity-100' : 'opacity-0 group-hover:opacity-100'">
                                            {{ $index + 1 }}
                                        </div>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @else
                <div class="h-48 bg-gray-100 rounded-lg flex items-center justify-center border border-gray-200 max-w-3xl mx-auto">
                    <p class="text-gray-500">Brak zdjęć dla tego noclegu</p>
                </div>
            @endif

            <!-- Szczegóły noclegu -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4">
                <div class="p-4 bg-gray-50 rounded-lg">
                    <p><strong>Typ obiektu:</strong> {{ $nocleg->object_type }}</p>
                </div>

                <div class="p-4 bg-gray-50 rounded-lg">
                    <p><strong>Liczba miejsc:</strong> {{ $nocleg->capacity }}</p>
                </div>

                <div class="p-4 bg-gray-50 rounded-lg">
                    <p><strong>Telefon kontaktowy:</strong> {{ $nocleg->contact_phone ?? '—' }}</p>
                </div>

                @if($nocleg->link)
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p>
                            <strong>Strona obiektu:</strong><br>
                            <a href="{{ $nocleg->link }}" class="text-blue-600 hover:underline" target="_blank">
                                {{ $nocleg->link }}
                            </a>
                        </p>
                    </div>
                @endif
            </div>

            <div class="pt-4">
                <h3 class="text-lg font-semibold mb-2">Wyposażenie</h3>
                <div class="flex flex-wrap gap-3 text-xl">
                    @if($nocleg->has_kitchen) <span title="Kuchnia">🍳</span> @endif
                    @if($nocleg->has_parking) <span title="Parking">🅿️</span> @endif
                    @if($nocleg->has_bathroom) <span title="Łazienka">🚿</span> @endif
                    @if($nocleg->has_wifi) <span title="Wi-Fi">📶</span> @endif
                    @if($nocleg->has_tv) <span title="Telewizor">📺</span> @endif
                    @if($nocleg->has_balcony) <span title="Balkon">🌅</span> @endif
                </div>

                @if($nocleg->amenities_other)
                    <p class="mt-3 text-gray-700"><strong>Inne:</strong> {{ $nocleg->amenities_other }}</p>
                @endif
            </div>

            <!-- Przyciski akcji administratora -->
            <div class="pt-4 flex flex-wrap gap-2 items-center">
                <!-- Formularz zatwierdzenia -->
                <form method="POST" action="{{ route('admin.noclegi.approve', $nocleg->id) }}" class="inline">
                    @csrf
                    <button type="submit" 
                            onclick="return confirm('Czy na pewno chcesz zatwierdzić ten nocleg?')"
                            class="bg-green-500 text-white px-4 py-2 rounded shadow hover:bg-green-600 transition">
                        Zatwierdź
                    </button>
                </form>

                <!-- Przycisk otwierający modal odrzucenia -->
                <button type="button" onclick="showRejectModal()"
                        class="bg-red-500 text-white px-4 py-2 rounded shadow hover:bg-red-600 transition">
                    Odrzuć
                </button>

                <!-- Powrót do listy -->
                <a href="{{ route('admin.noclegi.index') }}"
                   class="inline-flex items-center px-5 py-3 bg-indigo-600 text-white rounded-lg shadow hover:bg-indigo-700 transition duration-200 font-medium">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Powrót do listy noclegów
                </a>
            </div>
        </div>
    </div>

    <!-- Modal odrzucenia -->
    <div id="rejectModal" class="hidden fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full p-6">
            <h3 class="text-xl font-bold text-gray-900 mb-4">
                Odrzucenie obiektu
            </h3>

            <form method="POST" action="{{ route('admin.noclegi.reject', $nocleg->id) }}" id="rejectForm">
                @csrf
                <div class="mb-6">
                    <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                        Uzasadnienie odrzucenia <span class="text-red-600">*</span>
                    </label>
                    <textarea id="reason" name="reason" rows="3" required
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg
                              focus:outline-none focus:ring-2 focus:ring-red-500 resize-none"
                        placeholder="Np. Brak zdjęć, niepełny opis..."></textarea>
                    <p id="rejectError" class="mt-2 text-sm text-red-600 hidden"></p>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" onclick="hideRejectModal()"
                        class="px-5 py-2.5 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition font-medium">
                        Anuluj
                    </button>

                    <button type="submit"
                        class="px-5 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium">
                        Odrzuć obiekt
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showRejectModal() {
            document.getElementById('rejectModal').classList.remove('hidden');
        }

        function hideRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
            document.getElementById('reason').value = '';
            document.getElementById('rejectError').classList.add('hidden');
        }

        // Walidacja formularza przed wysłaniem
        document.getElementById('rejectForm').addEventListener('submit', function(e) {
            const reason = document.getElementById('reason').value.trim();
            const errorEl = document.getElementById('rejectError');
            
            if (!reason) {
                e.preventDefault();
                errorEl.textContent = 'Podaj uzasadnienie odrzucenia.';
                errorEl.classList.remove('hidden');
            }
        });

        // Zamykanie modala po kliknięciu w tło
        document.getElementById('rejectModal').addEventListener('click', function(e) {
            if (e.target === this) {
                hideRejectModal();
            }
        });

        // Zamykanie modala klawiszem ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                hideRejectModal();
            }
        });
    </script>

    <style>
        .aspect-w-16 {
            position: relative;
            padding-bottom: 62.5%;
        }
        
        .aspect-w-16 > * {
            position: absolute;
            height: 100%;
            width: 100%;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
        }
        
        .max-w-3xl {
            max-width: 48rem;
        }
        
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
        
        @media (max-width: 768px) {
            .max-w-3xl {
                max-width: 100%;
            }
            
            .flex-shrink-0.w-16 {
                width: 14px;
                height: 12px;
            }
        }
    </style>
</x-app-layout>