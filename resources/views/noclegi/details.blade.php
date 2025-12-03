<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">{{ $nocleg->title }}</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto">
        <div class="bg-white p-6 rounded-lg shadow space-y-6 text-black">

            <p class="text-sm text-gray-600 flex items-center gap-2">
                📍 <strong>{{ $nocleg->city }}, {{ $nocleg->street }}</strong>
            </p>

            @if($nocleg->user)
                <div class="pt-6 bg-gray-50 p-4 rounded-lg shadow">
                    <h3 class="text-lg font-semibold mb-2">Dodane przez</h3>
                    <p><strong>Imię:</strong> {{ $nocleg->user->name }}</p>
                    <p><strong>Email:</strong> {{ $nocleg->user->email }}</p>
                    <p><strong>Telefon:</strong> {{ $nocleg->user->phone ?? '—' }}</p>
                </div>
            @endif

            @if($nocleg->description)
                <div>
                    <p class="text-gray-800 leading-relaxed">{{ $nocleg->description }}</p>
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
                    <p><strong>Telefon kontaktowy:</strong> {{ $nocleg->contact_phone ?? '—' }}</p>
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

            <div class="pt-4 flex flex-wrap gap-2 items-center">
                <form method="POST" action="{{ route('admin.noclegi.approve', $nocleg->id) }}">
                    @csrf
                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded shadow hover:bg-green-600 transition">
                        Zatwierdź
                    </button>
                </form>

                <form method="POST" action="{{ route('admin.noclegi.reject', $nocleg->id) }}">
                    @csrf
                    <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded shadow hover:bg-red-600 transition">
                        Odrzuć
                    </button>
                </form>

                <a href="{{ route('admin.noclegi.index') }}"
                  class="inline-block px-5 py-2 bg-indigo-600 text-white rounded-md shadow hover:bg-indigo-700 transition">
                    ← Powrót do listy noclegów
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
