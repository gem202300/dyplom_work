{{-- Для Attraction — аналогічно --}}
<div class="bg-white shadow-md rounded-lg overflow-hidden border hover:shadow-lg transition-shadow">
    <div class="relative">
        <x-photo-carousel
            :photos="$model->photos"
            :show-rating="false"
            alt="{{ $model->name }}"
            aspect-ratio="aspect-video"
            container-class="rounded-t-lg"
            show-counter="true"
            show-dots="true"
        />

        <a href="{{ route('attractions.show', $model->id) }}"
           class="absolute inset-0 rounded-t-lg z-0"
           aria-label="Przejdź do atrakcji {{ $model->name }}"></a>
    </div>

    <a href="{{ route('attractions.show', $model->id) }}" class="block p-4 space-y-2 text-sm hover:bg-gray-50 transition">
        <h3 class="text-lg font-semibold text-gray-900">{{ $model->name }}</h3>
        <p class="text-gray-600">{{ $model->location }}</p>
        <p class="text-gray-600">
            <strong>Kategorie:</strong> {{ $model->categories->pluck('name')->join(', ') }}
        </p>
        <p class="text-gray-600">
            <strong>Godziny:</strong>
            @if($model->opening_time && $model->closing_time)
                {{ $model->opening_time }} - {{ $model->closing_time }}
            @else
                —
            @endif
        </p>

        @if($model->average_rating)
            <div class="text-sm font-medium text-gray-700 mt-3">
                ⭐ {{ number_format($model->average_rating, 2) }}
            </div>
        @endif
    </a>
</div>