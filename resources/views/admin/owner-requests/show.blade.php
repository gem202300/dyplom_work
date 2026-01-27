<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center p-4 bg-gray-100 dark:bg-gray-900 rounded-lg">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                Szczegóły zgłoszenia o rolę właściciela
            </h2>
            <a href="{{ route('admin.owner-requests.index') }}">
                <x-wireui-button flat label="Wróć do listy" icon="arrow-left" class="px-4 py-2" />
            </a>
        </div>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto space-y-8">

            <!-- Основний контент -->
            <div class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-lg p-6 space-y-8">

                <!-- Інформація про користувача та телефон -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-3 p-4 bg-gray-50 dark:bg-gray-900 rounded-xl">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            Użytkownik
                        </h3>
                        <p class="text-gray-700 dark:text-gray-300">
                            <span class="font-medium">Nazwa:</span> {{ $ownerRequest->user->name }}
                        </p>
                        <p class="text-gray-700 dark:text-gray-300">
                            <span class="font-medium">Email:</span> {{ $ownerRequest->user->email }}
                        </p>
                    </div>

                    <div class="space-y-3 p-4 bg-gray-50 dark:bg-gray-900 rounded-xl">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            Numer telefonu
                        </h3>
                        <p class="text-gray-700 dark:text-gray-300 text-lg">
                            {{ $ownerRequest->phone ?? '—' }}
                        </p>
                    </div>
                </div>

                <!-- Причина заявки -->
                <div class="space-y-4 p-4 bg-gray-50 dark:bg-gray-900 rounded-xl">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        Powód zgłoszenia
                    </h3>
                    <!-- Додано клас whitespace-pre-line для коректного відображення переносів -->
                    <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line leading-relaxed">
                        {{ $ownerRequest->reason }}
                    </p>
                </div>

                <!-- Статус -->
                <div class="space-y-4 p-4 bg-gray-50 dark:bg-gray-900 rounded-xl">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        Status
                    </h3>

                    <span class="inline-block px-4 py-2 rounded-full text-white font-medium text-sm
                        @if($ownerRequest->status === 'pending') bg-yellow-600
                        @elseif($ownerRequest->status === 'approved') bg-green-600
                        @elseif($ownerRequest->status === 'rejected') bg-red-600
                        @endif">
                        {{ ucfirst(__($ownerRequest->status)) }}
                    </span>

                    @if($ownerRequest->status === 'rejected' && $ownerRequest->rejection_reason)
                        <div class="mt-4 rounded-xl border border-red-200 dark:border-red-800
                                bg-red-50 dark:bg-red-900/20 p-4">

                            <div class="flex items-start gap-3">
                                <!-- Ліва смужка -->
                                <div class="w-1 bg-red-500 rounded-full mt-1 flex-shrink-0 h-full"></div>

                                <div class="space-y-2">
                                    <h4 class="font-semibold text-red-700 dark:text-red-400 text-sm uppercase tracking-wide">
                                        Powód odrzucenia
                                    </h4>
                                    <!-- Тут також виправлено відображення тексту -->
                                    <p class="text-gray-800 dark:text-gray-200 leading-relaxed whitespace-pre-line">
                                        {{ $ownerRequest->rejection_reason }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Кнопки дій -->
                @if($ownerRequest->status === 'pending')
                    <div class="pt-6 flex flex-wrap justify-start gap-4 border-t border-gray-200 dark:border-gray-700">

                        <form method="POST"
                            action="{{ route('admin.owner-requests.approve', $ownerRequest->id) }}"
                            onsubmit="return confirm('Na pewno chcesz zatwierdzić ten wniosek?')">
                            @csrf
                            <x-wireui-button
                                positive
                                type="submit"
                                label="Zatwierdź wniosek"
                                icon="check"
                                class="inline-flex px-6 py-3"
                            />
                        </form>

                        <x-wireui-button
                            negative
                            id="rejectBtn"
                            label="Odrzuć wniosek"
                            icon="x-mark"
                            class="inline-flex px-6 py-3"
                        />
                    </div>
                @endif

            </div>
        </div>
    </div>

    <!-- Модальне вікно відхилення -->
    <div id="rejectModal"
        class="fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-md w-full">

            <div class="p-6 space-y-6">

                <!-- Заголовок -->
                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                    Odrzucenie wniosku
                </h3>

                <form method="POST"
                    action="{{ route('admin.owner-requests.reject', $ownerRequest->id) }}"
                    class="space-y-6">
                    @csrf

                    <!-- Причина відхилення -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Powód odrzucenia <span class="text-red-500">*</span>
                        </label>
                        <textarea
                            name="rejection_reason"
                            required
                            rows="4"
                            placeholder="Podaj szczegółowy powód odrzucenia..."
                            class="w-full rounded-lg border border-gray-300 dark:border-gray-600
                                px-4 py-3
                                focus:outline-none focus:ring-2 focus:ring-red-500
                                dark:bg-gray-700 dark:text-gray-200 resize-none leading-relaxed"></textarea>
                    </div>

                    <!-- Чекбокс -->
                    <div class="flex items-center">
                        <label class="flex items-center cursor-pointer select-none gap-3">
                            <input type="checkbox" name="can_resubmit" value="1" checked
                                class="rounded border-gray-300 dark:border-gray-600
                                    text-red-600 focus:ring-red-500 h-4 w-4">
                            <span class="text-gray-700 dark:text-gray-300">
                                Pozwól użytkownikowi wysłać wniosek ponownie
                            </span>
                        </label>
                    </div>

                    <!-- Кнопки -->
                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <button type="button" id="cancelBtn"
                            class="px-5 py-2.5 border border-gray-300 dark:border-gray-600
                                text-gray-700 dark:text-gray-300
                                rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700
                                transition font-medium">
                            Anuluj
                        </button>
                        <button type="submit"
                            class="px-5 py-2.5 bg-red-600 text-white
                                rounded-lg hover:bg-red-700
                                transition font-medium">
                            Potwierdź odrzucenie
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript для модалки -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const rejectBtn = document.getElementById('rejectBtn');
            const cancelBtn = document.getElementById('cancelBtn');
            const modal = document.getElementById('rejectModal');

            if (rejectBtn) {
                rejectBtn.addEventListener('click', () => {
                    modal.classList.remove('hidden');
                });
            }

            if (cancelBtn) {
                cancelBtn.addEventListener('click', () => {
                    modal.classList.add('hidden');
                });
            }

            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.classList.add('hidden');
                }
            });

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                    modal.classList.add('hidden');
                }
            });
        });
    </script>
</x-app-layout>