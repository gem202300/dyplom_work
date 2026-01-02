<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                Szczegóły zgłoszonego komentarza
            </h2>
            <a href="{{ url()->previous() }}">
                <x-wireui-button flat label="Wróć" />
            </a>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto space-y-6">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow border border-gray-200 dark:border-gray-700 space-y-6">

            <div>
                <h3 class="text-lg font-semibold mb-4">Komentarz</h3>
                <div class="space-y-2">
                    <p><strong>Autor:</strong> {{ $rating->user->name ?? 'Użytkownik' }}</p>
                    <p><strong>Ocena:</strong> {{ $rating->rating ?? 'brak' }} / 5</p>
                    <p><strong>Komentarz:</strong> {{ $rating->comment }}</p>
                </div>
            </div>

            <hr class="my-3">

            <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded border max-w-md">
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

            <div>
                <h3 class="text-lg font-semibold mb-2">Zgłoszenia</h3>
                @forelse($rating->reports as $report)
                    <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded border mb-3 max-w-md">
                        <p class="mb-1"><strong>Powód:</strong> {{ $report->reason }}</p>
                        <p class="mb-1 text-sm text-gray-700 dark:text-gray-300">
                            <strong>Zgłosił:</strong> {{ $report->user->name }} ({{ $report->user->email }})
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $report->created_at->format('Y-m-d H:i') }}</p>
                    </div>
                @empty
                    <p class="text-gray-500 dark:text-gray-400">Brak zgłoszeń.</p>
                @endforelse
            </div>

            <hr class="my-3">

            <div class="flex space-x-3 items-center pt-2">
                <!-- Видалити коментар -->
                <form method="POST" action="{{ route('admin.ratings.delete', $rating->id) }}"
                      onsubmit="return confirm('Na pewno chcesz trwale usunąć ten komentarz?')">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition">
                        Usuń komentarz
                    </button>
                </form>

                <!-- Очистити звіти -->
                <form method="POST" action="{{ route('admin.ratings.clear-reports', $rating->id) }}"
                      onsubmit="return confirm('Odrzucić wszystkie zgłoszenia?')">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 transition">
                        Odrzuć zgłoszenia
                    </button>
                </form>

                <!-- Додати заборонене слово -->
                <button type="button" id="openModalBtn" 
                        class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition">
                    Dodaj do słów zakazanych
                </button>
            </div>

            <div id="bannedWordModal" class="mt-6 p-6 bg-gray-50 dark:bg-gray-900 border rounded-lg shadow hidden max-w-md w-full">
                <h2 class="text-xl font-semibold mb-4">Dodaj słowo zakazane</h2>

                <form method="POST" action="{{ route('banned-words.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium mb-1">Słowo</label>
                        <input type="text" name="word" placeholder="Wpisz słowo..." required
                               class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Typ blokady</label>
                        <select name="partial" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="1">Częściowe ukrywanie (zamiana na *)</option>
                            <option value="0">Całkowity zakaz (komentarz odrzucany)</option>
                        </select>
                    </div>

                    <div class="flex justify-end space-x-2">
                        <button type="button" id="cancelBtn" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 transition">
                            Anuluj
                        </button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition">
                            Zapisz
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const openBtn = document.getElementById('openModalBtn');
            const modal = document.getElementById('bannedWordModal');
            const cancelBtn = document.getElementById('cancelBtn');

            if (openBtn && modal && cancelBtn) {
                openBtn.addEventListener('click', () => {
                    modal.classList.remove('hidden');
                });

                cancelBtn.addEventListener('click', () => {
                    modal.classList.add('hidden');
                });

                modal.addEventListener('click', (e) => {
                    if (e.target === modal) {
                        modal.classList.add('hidden');
                    }
                });
            }
        });
    </script>
</x-app-layout>