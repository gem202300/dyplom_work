<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">{{ $nocleg->title }}</h2>
    </x-slot>

<div class="py-6 max-w-7xl mx-auto">
    <div class="bg-white p-6 rounded-lg shadow space-y-6 text-black">

        <p class="text-sm text-gray-600 flex items-center gap-2">
            📍 <strong>{{ $nocleg->city }}, {{ $nocleg->street }}</strong>
        </p>

        @if($nocleg->description)
            <div>
                <p class="text-gray-800 leading-relaxed">{{ $nocleg->description }}</p>
            </div>
        @endif

        <div class="pt-4">
            <h3 class="text-lg font-semibold">Ocena</h3>
            <p class="text-lg">
                Średnia ocena:
                <strong>
                    {{ $nocleg->average_rating ? number_format($nocleg->average_rating, 2) : 'Brak ocen' }}
                </strong>
            </p>
        </div>

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

        @if($nocleg->photos->isNotEmpty())
            <h3 class="text-lg font-semibold pt-6">Zdjęcia</h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                @foreach($nocleg->photos as $photo)
                    <img src="{{ asset($photo->path) }}" alt="Zdjęcie noclegu" class="rounded-md shadow object-cover w-full h-48">
                @endforeach
            </div>
        @endif

        @auth
            <div class="bg-gray-50 p-6 rounded shadow space-y-4">
                <h3 class="text-lg font-semibold">Dodaj swoją opinię</h3>

                <form method="POST" action="{{ route('ratings.store') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="rateable_type" value="App\Models\Nocleg">
                    <input type="hidden" name="rateable_id" value="{{ $nocleg->id }}">

                    <div>
                        <label for="rating" class="block text-sm font-medium">Ocena (1–5, opcjonalnie)</label>
                        <input id="rating" name="rating" type="number" min="1" max="5"
                              class="mt-1 block w-full border border-gray-300 rounded-md shadow px-3 py-2"
                              value="{{ old('rating') }}">
                    </div>

                    <div>
                        <label for="comment" class="block text-sm font-medium">Komentarz</label>
                        <textarea id="comment" name="comment" rows="4"
                                  class="mt-1 block w-full border border-gray-300 rounded-md shadow px-3 py-2"
                                  placeholder="Podziel się swoją opinią...">{{ old('comment') }}</textarea>
                    </div>

                    <button type="submit"
                            class="px-5 py-2 bg-indigo-600 text-white rounded-md shadow hover:bg-indigo-700 transition">
                        Wyślij opinię
                    </button>
                </form>
            </div>
        @else
            <p class="text-sm text-gray-600">
                Aby dodać opinię, <a href="{{ route('login') }}" class="text-indigo-600 underline">zaloguj się</a>.
            </p>
        @endauth

        <div class="pt-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Opinie</h2>

            @forelse ($ratings as $rating)
                <div class="mb-4 p-4 bg-gray-100 rounded shadow">
                    <div class="flex justify-between items-center mb-2">
                        <span class="font-semibold">{{ $rating->user->name ?? 'Użytkownik' }}</span>
                        <span class="text-sm text-gray-600">{{ $rating->created_at->format('Y-m-d H:i') }}</span>
                    </div>

                    @if($rating->rating)
                        <p class="mb-2">Ocena: {{ $rating->rating }} / 5</p>
                    @endif

                    @if($rating->comment)
                        <p>{{ $rating->comment }}</p>
                    @endif
                </div>
            @empty
                <p class="text-gray-500">Brak opinii.</p>
            @endforelse

            <div class="mt-4">
                {{ $ratings->links() }}
            </div>
        </div>

        

        <div class="pt-4">
            <a href="{{ route('noclegi.index') }}"
               class="inline-block px-5 py-2 bg-indigo-600 text-white rounded-md shadow hover:bg-indigo-700 transition">
                ← Powrót do listy noclegów
            </a>
        </div>

    </div>
</div>

</x-app-layout>
