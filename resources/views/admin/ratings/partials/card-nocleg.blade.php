<div class="bg-white shadow-md rounded-lg overflow-hidden border">

    <a href="{{ route('noclegi.show', $model->id) }}" class="block">
        <div class="h-40 bg-gray-100 overflow-hidden">
            @if($model->photos->isNotEmpty())
                <img src="{{ asset($model->photos->first()->path) }}"
                     class="w-full h-full object-cover"/>
            @else
                <div class="flex items-center justify-center h-full text-gray-400 italic">
                    brak zdjęcia
                </div>
            @endif
        </div>
    </a>

    <div class="p-4 space-y-1 text-sm">
        <h3 class="text-lg font-semibold">{{ $model->title }}</h3>
        <p class="text-gray-600">📍 {{ $model->city }}, {{ $model->street }}</p>
        <p class="text-gray-600"><strong>Typ:</strong> {{ $model->object_type }}</p>
        <p class="text-gray-600"><strong>Kontakt:</strong> {{ $model->contact_phone ?? '—' }}</p>

        <div class="text-sm font-medium text-gray-700">
            ⭐ {{ number_format($model->average_rating ?? 0, 2) }}
        </div>
    </div>
</div>
