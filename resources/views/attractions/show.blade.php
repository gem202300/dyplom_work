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

            <div>
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Opinie</h2>

                @forelse ($ratings as $rating)
                    <div class="mb-4 p-4 bg-gray-100 rounded shadow">
                        <div class="flex justify-between items-center mb-2">
                            <span class="font-semibold">{{ $rating->user->name ?? 'Użytkownik' }}</span>
                            <span class="text-sm text-gray-600">
                                {{ $rating->created_at->format('Y-m-d H:i') }}
                            </span>
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

            @auth
                <div class="bg-gray-50 p-6 rounded shadow space-y-4">
                    <h3 class="text-lg font-semibold">Dodaj swoją opinię</h3>

                    <form method="POST" action="{{ route('ratings.store') }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="rateable_type" value="App\Models\Attraction">
                        <input type="hidden" name="rateable_id" value="{{ $attraction->id }}">

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
                                      placeholder="Podziel się swoją opinią..."></textarea>
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

            <div class="pt-4">
                <a href="{{ route('attractions.index') }}"
                   class="inline-block px-5 py-2 bg-indigo-600 text-white rounded-md shadow hover:bg-indigo-700 transition">
                    ← Powrót do listy atrakcji
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
