<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">{{ $nocleg->title }}</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white p-6 rounded-lg shadow space-y-6 text-black">

            <p class="text-sm text-gray-600 flex items-center gap-2">
                📍 <strong>{{ $nocleg->city }}, {{ $nocleg->street }}</strong>
            </p>

            @if($nocleg->description)
                <div>
                    <p class="text-gray-800 leading-relaxed">{{ $nocleg->description }}</p>
                </div>
            @endif

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
                    
                    {{-- ГОЛОВНЕ ФОТО --}}
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

                        {{-- СТРІЛКИ НАВІГАЦІЇ НА ФОТО --}}
                        @if($nocleg->photos->count() > 1)
                            <div class="absolute inset-0 flex items-center justify-between p-3 z-20">
                                {{-- Ліва стрілка --}}
                                <button x-show="currentIndex > 0"
                                        @click="prevPhoto()"
                                        class="w-10 h-10 bg-white/80 hover:bg-white rounded-full flex items-center justify-center shadow-lg transition-all hover:scale-110">
                                    <svg class="w-5 h-5 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                    </svg>
                                </button>

                                {{-- Права стрілка --}}
                                <button x-show="currentIndex < photosCount - 1"
                                        @click="nextPhoto()"
                                        class="w-10 h-10 bg-white/80 hover:bg-white rounded-full flex items-center justify-center shadow-lg transition-all hover:scale-110 ml-auto">
                                    <svg class="w-5 h-5 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </button>
                            </div>
                        @endif

                        {{-- КРАПОЧКИ-ІНДИКАТОРИ --}}
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

                        {{-- НОМЕР ФОТО --}}
                        <div class="absolute top-3 right-3 bg-black/50 text-white text-xs px-2 py-1 rounded z-20">
                            <span x-text="currentIndex + 1"></span>/<span x-text="photosCount"></span>
                        </div>
                    </div>

                    {{-- ГАЛЕРЕЯ МІНІАТЮР --}}
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
                                        
                                        {{-- ІНДИКАТОР НА МІНІАТЮРІ --}}
                                        <div class="absolute inset-0 transition-opacity duration-300"
                                             :class="currentIndex === {{ $index }} 
                                                    ? 'bg-blue-500/20' 
                                                    : 'group-hover:bg-blue-500/10'">
                                        </div>
                                        
                                        {{-- НОМЕР НА МІНІАТЮРІ --}}
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

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4">

                <div class="p-4 bg-gray-50 rounded-lg">
                    <p><strong>Typ obiektu:</strong> {{ $nocleg->object_type }}</p>
                </div>

                <div class="p-4 bg-gray-50 rounded-lg">
                    <p><strong>Liczba miejsc:</strong> {{ $nocleg->capacity }}</p>
                </div>

                <div class="p-4 bg-gray-50 rounded-lg">
                    <p><strong>Telefon kontaktowy:</strong> 
                        {{ $nocleg->contact_phone ?? '—' }}
                    </p>
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

            {{-- ОЦІНКИ --}}
            <div>
                <h3 class="text-lg font-semibold mb-3 text-gray-800">Ocena</h3>
                <div class="flex items-center space-x-4">
                    @if($nocleg->average_rating)
                        <div class="text-3xl font-bold text-yellow-600">
                            {{ number_format($nocleg->average_rating, 1) }}
                        </div>
                        <div class="flex items-center">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= floor($nocleg->average_rating))
                                    <svg class="w-6 h-6 text-yellow-500 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                        <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                                    </svg>
                                @elseif($i - 0.5 <= $nocleg->average_rating)
                                    <svg class="w-6 h-6 text-yellow-500 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                        <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                                    </svg>
                                @else
                                    <svg class="w-6 h-6 text-gray-300 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                        <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                                    </svg>
                                @endif
                            @endfor
                        </div>
                        <div class="text-sm text-gray-600">
                            ({{ $ratings->count() }} {{ trans_choice('ocena|oceny|ocen', $ratings->count()) }})
                        </div>
                    @else
                        <p class="text-gray-700 text-lg">
                            Średnia ocena: <strong>Brak ocen</strong>
                        </p>
                    @endif
                </div>
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

            {{-- КОМПОНЕНТ ДЛЯ ДОДАВАННЯ ОЦІНОК --}}
            <x-ratings :rateable="$nocleg" :ratings="$ratings" />
            
            {{-- КНОПКА ПОВЕРНЕННЯ --}}
            <div class="pt-6">
                <a href="{{ route('noclegi.index') }}"
                   class="inline-flex items-center px-5 py-3 bg-indigo-600 text-white rounded-lg shadow hover:bg-indigo-700 transition duration-200 font-medium">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Powrót do listy noclegów
                </a>
            </div>
        </div>
    </div>

    <style>
        /* Фіксовані пропорції для фото (16:10) */
        .aspect-w-16 {
            position: relative;
            padding-bottom: 62.5%; /* 10/16 = 0.625 */
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
        
        /* Обмеження ширини для каруселі */
        .max-w-3xl {
            max-width: 48rem; /* 768px */
        }
        
        /* Плавні переходи */
        .transition-all {
            transition-property: all;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 200ms;
        }
        
        /* Приховування стандартного скролбару для мініатюр */
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
        
        /* Медіа-запити для адаптивності */
        @media (max-width: 768px) {
            .max-w-3xl {
                max-width: 100%;
            }
            
            .flex-shrink-0.w-16 {
                width: 14px;
                height: 12px;
            }
            
            .absolute.top-1.right-1.w-4.h-4 {
                width: 3px;
                height: 3px;
                font-size: 8px;
            }
        }
        
        .bg-white\/80 {
            background-color: rgba(255, 255, 255, 0.8);
        }
        
        .hover\:scale-110:hover {
            transform: scale(1.1);
        }
    </style>
</x-app-layout>