@props([
    'photos' => [],
    'showRating' => false,
    'rating' => null,
    'alt' => '',
    'aspectRatio' => 'aspect-video',
    'containerClass' => '',
    'arrowSize' => 'w-8 h-8',
    'ratingBadgePosition' => 'top-3 right-3',
    'showDots' => false,
    'showCounter' => false,
])

<div {{ $attributes->merge(['class' => 'relative bg-gray-100 overflow-hidden ' . $containerClass]) }}
     x-data="{
         currentPhoto: 0,
         photosCount: {{ count($photos) }},
         nextPhoto() {
             this.currentPhoto = (this.currentPhoto + 1) % this.photosCount;
         },
         prevPhoto() {
             this.currentPhoto = (this.currentPhoto - 1 + this.photosCount) % this.photosCount;
         }
     }"
     x-bind:class="photosCount ? '{{ $aspectRatio }}' : ''">
    
    {{-- Фото --}}
    @if(count($photos) > 0)
        @foreach($photos as $index => $photo)
            <div class="absolute inset-0 transition-opacity duration-300 ease-in-out"
                 x-show="currentPhoto === {{ $index }}"
                 x-transition:enter="transition-opacity duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0">
                <img src="{{ is_string($photo) ? $photo : asset($photo->path ?? $photo['path']) }}" 
                     alt="{{ $alt }} - zdjęcie {{ $index + 1 }}"
                     class="w-full h-full object-cover">
            </div>
        @endforeach
    @else
        {{-- Заглушка, якщо немає фото --}}
        <div class="w-full h-full flex items-center justify-center text-gray-400">
            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
        </div>
    @endif

    {{-- Стрілочки навігації --}}
    @if(count($photos) > 1)
        <div class="absolute inset-0 flex items-center justify-between p-2 z-20">
            <button x-show="currentPhoto > 0"
                    @click="prevPhoto()"
                    class="{{ $arrowSize }} bg-white hover:bg-gray-100 rounded-full flex items-center justify-center shadow-lg transition-all hover:scale-110 border border-gray-200">
                <svg class="w-5 h-5 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>

            <button x-show="currentPhoto < photosCount - 1"
                    @click="nextPhoto()"
                    class="{{ $arrowSize }} bg-white hover:bg-gray-100 rounded-full flex items-center justify-center shadow-lg transition-all hover:scale-110 border border-gray-200 ml-auto">
                <svg class="w-5 h-5 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
        </div>
    @endif

    {{-- Бейдж рейтингу з 100% білим фоном --}}
    @if($showRating && $rating)
        <div class="absolute {{ $ratingBadgePosition }} z-30">
            <div class="bg-white px-3 py-1.5 rounded-full shadow-md flex items-center gap-1 border border-gray-200">
                <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                </svg>
                <span class="font-bold text-gray-800">
                    {{ number_format($rating, 1) }}
                </span>
            </div>
        </div>
    @endif

    {{-- Крапки-індикатори (опційно) --}}
    @if($showDots && count($photos) > 1)
        <div class="absolute bottom-3 left-1/2 transform -translate-x-1/2 z-20">
            <div class="flex space-x-2">
                @foreach($photos as $index => $photo)
                    <button @click="currentPhoto = {{ $index }}"
                            class="w-2.5 h-2.5 rounded-full transition-all duration-300"
                            :class="currentPhoto === {{ $index }} 
                                   ? 'bg-white scale-125' 
                                   : 'bg-white/60 hover:bg-white/80'">
                    </button>
                @endforeach
            </div>
        </div>
    @endif

    @if($showCounter && count($photos) > 1)
        <div class="absolute top-3 right-3 bg-black/50 text-white text-xs px-2 py-1 rounded z-20">
            <span x-text="currentPhoto + 1"></span>/<span x-text="photosCount"></span>
        </div>
    @endif
    {{ $slot }}
</div>