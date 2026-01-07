{{-- Для Nocleg --}}
<div class="bg-white shadow-md rounded-lg overflow-hidden border hover:shadow-lg transition-shadow">
    <!-- Обгортка для каруселі -->
    <div class="relative">
        <x-photo-carousel
            :photos="$model->photos"
            :show-rating="false"
            alt="{{ $model->title }}"
            aspect-ratio="aspect-video"
            container-class="rounded-t-lg"
            show-counter="true"
            show-dots="true"
        />

        <!-- Посилання ТІЛЬКИ на фото (не перекриває стрілки та крапки) -->
        <a href="{{ route('noclegi.show', $model->id) }}"
           class="absolute inset-0 rounded-t-lg z-0"
           aria-label="Przejdź do noclegu {{ $model->title }}"></a>
    </div>

    <!-- Нижня частина — теж клікабельна -->
    <a href="{{ route('noclegi.show', $model->id) }}" class="block p-4 space-y-2 text-sm hover:bg-gray-50 transition">
        <h3 class="text-lg font-semibold text-gray-900">{{ $model->title }}</h3>
        <p class="text-gray-600">📍 {{ $model->city }}, {{ $model->street }}</p>
        <p class="text-gray-600"><strong>Typ:</strong> {{ $model->object_type }}</p>
        <p class="text-gray-600"><strong>Kontakt:</strong> {{ $model->contact_phone ?? '—' }}</p>

        @if($model->average_rating)
            <div class="text-sm font-medium text-gray-700 mt-3">
                ⭐ {{ number_format($model->average_rating, 2) }}
            </div>
        @endif
    </a>
</div>