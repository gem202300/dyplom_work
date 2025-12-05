<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">{{ $attraction->name }}</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto">
        <div class="bg-white p-6 rounded-lg shadow space-y-6 text-black">

            <p class="text-sm text-gray-600">{{ $attraction->location }}</p>

            <div>
                <p>{{ $attraction->description }}</p>
            </div>

            <p class="font-semibold">
                Godziny otwarcia:
                {{ $attraction->opening_time ? \Carbon\Carbon::parse($attraction->opening_time)->format('H:i') : '—' }}
                -
                {{ $attraction->closing_time ? \Carbon\Carbon::parse($attraction->closing_time)->format('H:i') : '—' }}
            </p>

            @if($attraction->photos->isNotEmpty())
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    @foreach($attraction->photos as $photo)
                        <img src="{{ asset($photo->path) }}"
                             alt="Zdjęcie atrakcji"
                             class="rounded-md shadow object-cover w-full h-48">
                    @endforeach
                </div>
            @endif

            <div>
                <h3 class="text-lg font-semibold mb-2 text-gray-800">Kategorie</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($attraction->categories as $category)
                        <span class="px-3 py-1 bg-indigo-100 text-indigo-800 text-sm rounded-full">
                            {{ $category->name }}
                        </span>
                    @endforeach
                </div>
            </div>

            <div>
                <h3 class="text-xl font-semibold text-gray-800">Ocena</h3>
                <p class="text-lg">
                    Średnia ocena:
                    <strong>
                        {{ $attraction->average_rating ? number_format($attraction->average_rating, 2) : 'Brak ocen' }}
                    </strong>
                </p>
            </div>
            
            <x-ratings :rateable="$attraction" :ratings="$ratings" />
            
            <div class="pt-4">
                <a href="{{ route('attractions.index') }}"
                   class="inline-block px-5 py-2 bg-indigo-600 text-white rounded-md shadow hover:bg-indigo-700 transition">
                    ← Powrót do listy atrakcji
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
