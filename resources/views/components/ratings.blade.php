@props([
    'rateable', // передаємо модель: Nocleg або Attraction
    'ratings'   // колекція рейтинґів
])

@auth
<div class="bg-gray-50 p-6 rounded shadow space-y-4">
    <h3 class="text-lg font-semibold">Dodaj swoją opinię</h3>

    <form method="POST" action="{{ route('ratings.store') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="rateable_type" value="{{ get_class($rateable) }}">
        <input type="hidden" name="rateable_id" value="{{ $rateable->id }}">

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
