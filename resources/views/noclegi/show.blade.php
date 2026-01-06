<x-app-layout>
    <x-slot name="header">
        {{-- Пустий хедер, назва перенесена в основний контент --}}
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white p-6 rounded-lg shadow space-y-6 text-black">

            {{-- НАЗВА --}}
            <div>
                <h1 style="font-size: 25px; font-weight: 800; margin-bottom: 1rem; color: #1f2937;">
                    {{ $nocleg->title }}
                </h1>

            </div>

            {{-- КАРУСЕЛЬ ФОТО --}}
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
                    
                    {{-- ОСНОВНЕ ФОТО З СІРИМ ФОНОМ --}}
                    <div class="relative bg-gray-200 rounded-xl overflow-hidden border border-gray-300 shadow-lg">
                        <div class="aspect-w-16 aspect-h-9">
                            @foreach($nocleg->photos as $index => $photo)
                                {{-- Фонова фотографія (завжди перша) --}}
                                @if($index === 0)
                                    <div class="absolute inset-0 z-0 opacity-20">
                                        <img src="{{ asset($photo->path) }}"
                                             alt="Zdjęcie tła"
                                             class="w-full h-full object-cover blur-sm"
                                             loading="lazy">
                                    </div>
                                @endif
                                
                                {{-- Активна фотографія --}}
                                <div class="absolute inset-0 transition-all duration-300 ease-in-out z-10"
                                     :class="currentIndex === {{ $index }} 
                                            ? 'opacity-100' 
                                            : 'opacity-0'">
                                    <div class="w-full h-full flex items-center justify-center p-4">
                                        <img src="{{ asset($photo->path) }}"
                                             alt="Zdjęcie noclegu {{ $index + 1 }}"
                                             class="h-full w-auto max-w-full object-contain rounded-lg shadow-xl"
                                             loading="lazy">
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- НАВІГАЦІЙНІ СТРІЛКИ (БІЛІ) --}}
                        @if($nocleg->photos->count() > 1)
                            <div class="absolute inset-0 flex items-center justify-between p-4 z-20">
                                <button @click="prevPhoto()"
                                        class="w-10 h-10 bg-white hover:bg-gray-50 rounded-full flex items-center justify-center shadow-lg transition-all hover:scale-110 active:scale-95 border border-gray-200"
                                        aria-label="Poprzednie zdjęcie">
                                    <svg class="w-5 h-5 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                    </svg>
                                </button>

                                <button @click="nextPhoto()"
                                        class="w-10 h-10 bg-white hover:bg-gray-50 rounded-full flex items-center justify-center shadow-lg transition-all hover:scale-110 active:scale-95 border border-gray-200"
                                        aria-label="Następne zdjęcie">
                                    <svg class="w-5 h-5 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </button>
                            </div>
                        @endif

                        {{-- ІНДИКАТОРИ --}}
                        @if($nocleg->photos->count() > 1)
                            <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 z-20">
                                <div class="flex space-x-2">
                                    @foreach($nocleg->photos as $index => $photo)
                                        <button @click="changePhoto({{ $index }})"
                                                class="w-2.5 h-2.5 rounded-full transition-all duration-300"
                                                :class="currentIndex === {{ $index }} 
                                                       ? 'bg-white scale-125' 
                                                       : 'bg-white/60 hover:bg-white/80'"
                                                aria-label="Przejdź do zdjęcia {{ $index + 1 }}">
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- НОМЕР ФОТО --}}
                        <div class="absolute top-4 right-4 bg-black/70 text-white text-sm px-2 py-1 rounded z-20">
                            <span x-text="currentIndex + 1"></span>/<span x-text="photosCount"></span>
                        </div>
                    </div>

                    {{-- МІНІАТЮРИ --}}
                    @if($nocleg->photos->count() > 1)
                        <div class="max-w-3xl mx-auto">
                            <div class="flex gap-2 overflow-x-auto py-2 px-1 scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100 justify-center">
                                @foreach($nocleg->photos as $index => $photo)
                                    <button @click="changePhoto({{ $index }})"
                                            :class="currentIndex === {{ $index }} 
                                                   ? 'ring-2 ring-blue-500 ring-offset-1' 
                                                   : 'opacity-80 hover:opacity-100'"
                                            class="flex-shrink-0 w-16 h-12 rounded overflow-hidden border border-gray-300 transition-all duration-300 hover:scale-105 relative bg-white">
                                        <img src="{{ asset($photo->path) }}"
                                             alt="Miniatura {{ $index + 1 }}"
                                             class="w-full h-full object-cover"
                                             loading="lazy">
                                        
                                        {{-- ІНДИКАТОР АКТИВНОСТІ --}}
                                        <div class="absolute inset-0 transition-colors"
                                             :class="currentIndex === {{ $index }} ? 'bg-blue-500/10' : ''">
                                        </div>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @else
                <div class="h-60 bg-gradient-to-br from-gray-100 to-gray-200 rounded-xl flex flex-col items-center justify-center border-2 border-dashed border-gray-300">
                    <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-gray-500 font-medium">Brak zdjęć dla tego noclegu</p>
                </div>
            @endif

            {{-- ЛОКАЦІЯ --}}
            <div class="flex items-start gap-2">
                <svg class="w-5 h-5 text-gray-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <p class="text-gray-700">
                    <strong>{{ $nocleg->city }}, {{ $nocleg->street }}</strong>
                </p>
            </div>

            {{-- ІНФОРМАЦІЯ ТА ОЦІНКИ В ОДНІЙ ЛІНІЇ --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- ІНФОРМАЦІЯ --}}
                <div class="bg-gray-50 rounded-xl overflow-hidden shadow-sm">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-5 text-gray-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Informacje
                        </h3>
                        <div class="space-y-4">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Typ obiektu</p>
                                <p class="text-lg font-medium text-gray-800">{{ $nocleg->object_type }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Liczba miejsc</p>
                                <p class="text-lg font-medium text-gray-800">{{ $nocleg->capacity }}</p>
                            </div>
                            @if($nocleg->contact_phone)
                                <div>
                                    <p class="text-sm text-gray-600 mb-1">Telefon kontaktowy</p>
                                    <p class="text-lg font-medium text-gray-800">{{ $nocleg->contact_phone }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- ОЦІНКИ --}}
                <div class="bg-gradient-to-r from-yellow-50 to-orange-50 rounded-xl overflow-hidden border border-yellow-100 shadow-sm">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-5 text-gray-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            Ocena
                        </h3>

                        <div class="space-y-6">
                            @if($nocleg->average_rating)
                                <div class="grid grid-cols-2 gap-6">
                                    <!-- Середня оцінка -->
                                    <div class="bg-white rounded-xl shadow-inner p-6 text-center">
                                        <p class="text-base text-gray-600 mb-3 font-medium">Średnia ocena</p>
                                        <div class="text-5xl font-extrabold text-yellow-600 tracking-tight">
                                            {{ number_format($nocleg->average_rating, 1) }}
                                        </div>
                                    </div>

                                    <!-- Кількість оцінок -->
                                    <div class="bg-white rounded-xl shadow-inner p-6 text-center">
                                        <p class="text-base text-gray-600 mb-3 font-medium">Liczba ocen</p>
                                        <div class="text-5xl font-extrabold text-gray-800 tracking-tight">
                                            {{ $ratings->count() }}
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-12">
                                    <div class="text-5xl font-bold text-gray-300 mb-4">—</div>
                                    <p class="text-xl text-gray-700 font-medium mb-2">
                                        Brak ocen
                                    </p>
                                    <p class="text-base text-gray-500">Bądź pierwszym, który oceni ten nocleg!</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- ОПИС --}}
            @if($nocleg->description)
                <div class="prose max-w-none">
                    <h3 class="text-lg font-semibold mb-2 text-gray-800">Opis</h3>
                    <p class="text-gray-700 leading-relaxed">{{ $nocleg->description }}</p>
                </div>
            @endif

            {{-- ПОСИЛАННЯ --}}
            @if($nocleg->link)
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                    <p class="font-medium text-gray-800 mb-2">Strona obiektu</p>
                    <a href="{{ $nocleg->link }}" 
                       class="text-blue-600 hover:text-blue-800 hover:underline font-medium break-all"
                       target="_blank"
                       rel="noopener noreferrer">
                        {{ $nocleg->link }}
                    </a>
                </div>
            @endif

            {{-- ВИПОСАДЖЕННЯ --}}
            <div class="bg-gray-50 p-6 rounded-xl">
                <h3 class="text-lg font-semibold mb-5 text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Wyposażenie
                </h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                    @if($nocleg->has_kitchen)
                        <div class="flex items-center gap-3 bg-white p-4 rounded-lg shadow-sm border border-gray-100">
                            <span class="text-2xl">🍳</span>
                            <span class="text-gray-700 font-medium">Kuchnia</span>
                        </div>
                    @endif
                    @if($nocleg->has_parking)
                        <div class="flex items-center gap-3 bg-white p-4 rounded-lg shadow-sm border border-gray-100">
                            <span class="text-2xl">🅿️</span>
                            <span class="text-gray-700 font-medium">Parking</span>
                        </div>
                    @endif
                    @if($nocleg->has_bathroom)
                        <div class="flex items-center gap-3 bg-white p-4 rounded-lg shadow-sm border border-gray-100">
                            <span class="text-2xl">🚿</span>
                            <span class="text-gray-700 font-medium">Łazienka</span>
                        </div>
                    @endif
                    @if($nocleg->has_wifi)
                        <div class="flex items-center gap-3 bg-white p-4 rounded-lg shadow-sm border border-gray-100">
                            <span class="text-2xl">📶</span>
                            <span class="text-gray-700 font-medium">Wi-Fi</span>
                        </div>
                    @endif
                    @if($nocleg->has_tv)
                        <div class="flex items-center gap-3 bg-white p-4 rounded-lg shadow-sm border border-gray-100">
                            <span class="text-2xl">📺</span>
                            <span class="text-gray-700 font-medium">Telewizor</span>
                        </div>
                    @endif
                    @if($nocleg->has_balcony)
                        <div class="flex items-center gap-3 bg-white p-4 rounded-lg shadow-sm border border-gray-100">
                            <span class="text-2xl">🌅</span>
                            <span class="text-gray-700 font-medium">Balkon</span>
                        </div>
                    @endif
                </div>

                @if($nocleg->amenities_other)
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <p class="text-sm text-gray-600 mb-2">Inne udogodnienia</p>
                        <p class="text-gray-700">{{ $nocleg->amenities_other }}</p>
                    </div>
                @endif
            </div>

            {{-- КОМПОНЕНТ ОЦІНЮВАННЯ --}}
            <x-ratings :rateable="$nocleg" :ratings="$ratings" />
            
            {{-- КНОПКА ПОВЕРНЕННЯ --}}
            <div class="pt-6 border-t border-gray-200">
                <a href="{{ route('noclegi.index') }}"
                   class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-[1.02] font-semibold group">
                    <svg class="w-5 h-5 mr-3 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Powrót do listy noclegów
                </a>
            </div>
        </div>
    </div>

    <style>
        /* Пропорції 16:9 для основного фото */
        .aspect-w-16 {
            position: relative;
        }
        
        .aspect-h-9 {
            padding-bottom: 56.25%; /* 9/16 = 0.5625 */
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

        /* Стилі для скроллбара мініатюр */
        .scrollbar-thin::-webkit-scrollbar {
            height: 4px;
        }
        
        .scrollbar-thin::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 2px;
        }
        
        .scrollbar-thin::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 2px;
        }
        
        .scrollbar-thin::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        /* Анімації */
        .transition-all {
            transition-property: all;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Тіні та ефекти */
        .shadow-inner {
            box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.06);
        }

        /* Розмиття для фонової фотографії */
        .blur-sm {
            filter: blur(8px);
        }

        /* Адаптивність */
        @media (max-width: 768px) {
            .aspect-h-9 {
                padding-bottom: 66.67%; /* 2:3 на мобільних */
            }
            
            .w-16 {
                width: 3.5rem;
                height: 2.5rem;
            }
            
            .text-5xl {
                font-size: 3rem;
            }
            
            .grid.grid-cols-2 {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .w-10.h-10 {
                width: 2.5rem;
                height: 2.5rem;
            }
            
            .w-5.h-5 {
                width: 1.25rem;
                height: 1.25rem;
            }
            
            .grid-cols-1.lg\:grid-cols-2 {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .grid-cols-2.sm\:grid-cols-3.md\:grid-cols-4 {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 640px) {
            .text-3xl {
                font-size: 1.75rem;
            }
            
            .text-5xl {
                font-size: 2.5rem;
            }
            
            .h-60 {
                height: 14rem;
            }
            
            .grid-cols-2.sm\:grid-cols-3.md\:grid-cols-4 {
                grid-template-columns: 1fr;
            }
        }

        /* Ефекти для кнопок */
        .active\:scale-95:active {
            transform: scale(0.95);
        }

        /* Уніфіковані розміри фото */
        .h-full.w-auto {
            height: 100%;
            width: auto;
            max-width: 100%;
        }
        
        .max-w-full {
            max-width: 100%;
        }
        
        .object-contain {
            object-fit: contain;
        }
    </style>
</x-app-layout>