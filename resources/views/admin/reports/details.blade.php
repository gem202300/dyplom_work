<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800">Szczegóły komentarza</h2>
            <a href="{{ url()->previous() }}">
                <x-wireui-button flat label="Wróć" />
            </a>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto space-y-6">

        <div class="bg-white p-6 rounded shadow space-y-4 border">

            <h3 class="text-lg font-semibold mb-2">Komentarz</h3>

            <p><strong>Autor:</strong> {{ $rating->user->name ?? 'Użytkownik' }}</p>
            <p><strong>Ocena:</strong> {{ $rating->rating ?? 'brak' }} / 5</p>
            <p><strong>Komentarz:</strong> {{ $rating->comment }}</p>

            <hr class="my-3">

            <div class="bg-gray-50 p-4 rounded border max-w-md">
                @php
                    $model = $rating->rateable;
                    $type = class_basename($rating->rateable_type);
                @endphp

                <h3 class="font-semibold mb-2">Komentarz dotyczy obiektu:</h3>

                <div class="max-w-sm">
                    @if($type === 'Nocleg')
                        @include('admin.ratings.partials.card-nocleg')
                    @else
                        @include('admin.ratings.partials.card-attraction')
                    @endif
                </div>
            </div>

            <hr class="my-3">

            <h3 class="text-lg font-semibold mb-2">Zgłoszenia</h3>
            @forelse($rating->reports as $report)
                <div class="p-4 bg-gray-50 rounded border mb-3 max-w-md">
                    <p class="mb-1"><strong>Powód:</strong> {{ $report->reason }}</p>
                    <p class="mb-1 text-sm text-gray-700">
                        <strong>Zgłosił:</strong> {{ $report->user->name }} ({{ $report->user->email }})
                    </p>
                    <p class="text-sm text-gray-600">{{ $report->created_at->format('Y-m-d H:i') }}</p>
                </div>
            @empty
                <p class="text-gray-500">Brak zgłoszeń.</p>
            @endforelse

            <hr class="my-3">

            <div class="flex space-x-3 items-center pt-2">
                <form method="POST" action="{{ route('ratings.delete', $rating->id) }}">
                  @csrf
                  <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                      Usuń komentarz
                  </button>
              </form>

              <form method="POST" action="{{ route('ratings.clear-reports', $rating->id) }}">
                  @csrf
                  <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                      Odrzuć zgłoszenia
                  </button>
              </form>


                <x-wireui-button id="openModalBtn" label="Dodaj do słów zakazanych" primary />
            </div>

            <div id="bannedWordModal" class="mt-4 p-6 bg-gray-50 border rounded shadow hidden max-w-md w-full">
                <h2 class="text-xl font-semibold mb-4">Dodaj słowo zakazane</h2>

                <form method="POST" action="{{ route('banned-words.store') }}" class="space-y-4">
                    @csrf

                    <x-input 
                        label="Słowo" 
                        name="word" 
                        placeholder="Wpisz słowo..." 
                        required
                        class="w-full border border-gray-300 text-gray-900" 
                        style="background-color: white; color: #111827;"
                    />

                    <x-select 
                        label="Typ blokady" 
                        name="partial" 
                        :options="[1 => 'Częściowe ukrywanie (zamiana na *)', 0 => 'Całkowity zakaz (komentarz odrzucany)']" 
                        class="w-full border border-gray-300 text-gray-900" 
                        style="background-color: white; color: #111827;"
                    />



                    <div class="flex space-x-2 justify-end">
                        <button type="button" id="cancelBtn" class="px-4 py-2 bg-gray-200 rounded">Anuluj</button>
                        <x-wireui-button primary type="submit" label="Zapisz" />
                    </div>
                </form>
            </div>

        </div>
    </div>

    <script>
        const openBtn = document.getElementById('openModalBtn');
        const modal = document.getElementById('bannedWordModal');
        const cancelBtn = document.getElementById('cancelBtn');

        openBtn.addEventListener('click', () => modal.classList.remove('hidden'));
        cancelBtn.addEventListener('click', () => modal.classList.add('hidden'));
    </script>
</x-app-layout>
