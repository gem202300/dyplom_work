<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Szczegóły zgłoszenia
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 space-y-6">

                <div>
                    <h3 class="text-lg font-bold">Użytkownik:</h3>
                    <p>{{ $owner_request->user->name }} ({{ $owner_request->user->email }})</p>
                </div>

                <div>
                    <h3 class="text-lg font-bold">Numer telefonu:</h3>
                    <p>{{ $owner_request->phone }}</p>
                </div>

                <div>
                    <h3 class="text-lg font-bold">Powód zgłoszenia:</h3>
                    <p>{{ $owner_request->reason }}</p>
                </div>

                <div>
                    <h3 class="text-lg font-bold">Status:</h3>
                    <p>{{ ucfirst($owner_request->status) }}</p>
                </div>

                <div class="flex gap-3 mt-4 items-center">
                    @if ($owner_request->status === 'pending')
                        {{-- Кнопка "Zatwierdź" --}}
                        <form method="POST" action="{{ route('admin.owner-requests.approve', $owner_request->id) }}">
                            @csrf
                            <button
                                class="px-6 py-2 rounded text-white 
                                      bg-green-600 hover:bg-green-700 
                                      dark:bg-green-700 dark:hover:bg-green-800">
                                Zatwierdź
                            </button>
                        </form>

                        {{-- Кнопка "Odrzuć" (відкриває модальне вікно) --}}
                        <button id="rejectBtn" class="px-6 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                            Odrzuć
                        </button>

                        {{-- Модальне вікно для відмови --}}
                        <div id="rejectModal"
                            class="fixed inset-0 bg-black bg-opacity-25 hidden z-50 flex items-center justify-center p-4">
                            <div class="bg-white rounded-lg shadow-xl max-w-sm w-full p-6">
                                <h3 class="text-lg font-bold mb-4">Odrzucenie wniosku</h3>

                                <form method="POST"
                                    action="{{ route('admin.owner-requests.reject', $owner_request->id) }}">
                                    @csrf

                                    <div class="mb-4">
                                        <label class="block font-medium text-gray-700 mb-2">
                                            Powód odrzucenia <span class="text-red-500">*</span>
                                        </label>
                                        <textarea name="rejection_reason" required rows="4" placeholder="Podaj szczegółowy powód odrzucenia wniosku..."
                                            class="w-full rounded border border-gray-300 p-3 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:outline-none"></textarea>
                                        @error('rejection_reason')
                                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="mb-6">
                                        <label class="flex items-center">
                                            <input type="checkbox" name="can_resubmit" value="1" checked
                                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                            <span class="ml-2 text-gray-700 text-sm">
                                                Pozwól użytkownikowi wysłać wniosek ponownie
                                            </span>
                                        </label>
                                    </div>

                                    <div class="flex justify-end gap-3">
                                        <button type="button" id="cancelBtn"
                                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded hover:bg-gray-50 transition">
                                            Anuluj
                                        </button>
                                        <button type="submit"
                                            class="px-6 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">
                                            Potwierdź odrzucenie
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif

                    <a href="{{ route('admin.owner-requests.index') }}"
                        class="px-6 py-2 bg-gray-200 text-black rounded hover:bg-gray-300 transition">
                        Powrót do listy
                    </a>
                </div>

            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const rejectBtn = document.getElementById('rejectBtn');
            const cancelBtn = document.getElementById('cancelBtn');
            const modal = document.getElementById('rejectModal');
            const form = modal.querySelector('form');

            // Відкрити модальне вікно
            if (rejectBtn) {
                rejectBtn.addEventListener('click', function() {
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                });
            }

            // Закрити модальне вікно
            if (cancelBtn) {
                cancelBtn.addEventListener('click', function() {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                });
            }

            // Закрити при кліку на темний фон
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }
            });

            // Закрити при натисканні Escape
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }
            });

            // Валідація форми перед відправленням
            if (form) {
                form.addEventListener('submit', function(e) {
                    const textarea = this.querySelector('textarea[name="rejection_reason"]');
                    if (!textarea.value.trim()) {
                        e.preventDefault();
                        textarea.focus();
                        textarea.classList.add('border-red-500');
                    }
                });
            }
        });
    </script>
</x-app-layout>
