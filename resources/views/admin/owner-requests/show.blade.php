<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                Szczegóły zgłoszenia o rolę właściciela
            </h2>
            <a href="{{ route('admin.owner-requests.index') }}">
                <x-wireui-button flat label="Wróć do listy" icon="arrow-left" />
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-8 space-y-8">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-bold mb-2 text-gray-900 dark:text-gray-100">Użytkownik</h3>
                        <p class="text-gray-700 dark:text-gray-300">
                            <strong>Nazwa:</strong> {{ $ownerRequest->user->name }}
                        </p>
                        <p class="text-gray-700 dark:text-gray-300">
                            <strong>Email:</strong> {{ $ownerRequest->user->email }}
                        </p>
                    </div>

                    <div>
                        <h3 class="text-lg font-bold mb-2 text-gray-900 dark:text-gray-100">Numer telefonu</h3>
                        <p class="text-gray-700 dark:text-gray-300">{{ $ownerRequest->phone ?? '—' }}</p>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-bold mb-2 text-gray-900 dark:text-gray-100">Powód zgłoszenia</h3>
                    <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $ownerRequest->reason }}</p>
                </div>

                <div>
                    <h3 class="text-lg font-bold mb-2 text-gray-900 dark:text-gray-100">Status</h3>
                    <span class="inline-block px-4 py-2 rounded-full text-white font-medium text-sm
                        @if($ownerRequest->status === 'pending') bg-yellow-600
                        @elseif($ownerRequest->status === 'approved') bg-green-600
                        @elseif($ownerRequest->status === 'rejected') bg-red-600
                        @endif">
                        {{ ucfirst($ownerRequest->status) }}
                    </span>

                    @if($ownerRequest->status === 'rejected' && $ownerRequest->rejection_reason)
                        <div class="mt-4">
                            <h4 class="font-semibold text-red-700 dark:text-red-400">Powód odrzucenia:</h4>
                            <p class="text-gray-700 dark:text-gray-300 mt-1">{{ $ownerRequest->rejection_reason }}</p>
                        </div>
                    @endif
                </div>

                @if($ownerRequest->status === 'pending')
                    <div class="flex flex-wrap gap-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                      
                        <form method="POST" action="{{ route('admin.owner-requests.approve', $ownerRequest->id) }}"
                              onsubmit="return confirm('Na pewno chcesz zatwierdzić ten wniosek?')">
                            @csrf
                            <x-wireui-button positive type="submit" label="Zatwierdź wniosek" icon="check" />
                        </form>

                        <x-wireui-button negative id="rejectBtn" label="Odrzuć wniosek" icon="x-mark" />
                    </div>
                @endif

            </div>
        </div>
    </div>

    <div id="rejectModal"
         class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-2xl max-w-lg w-full p-8">
            <h3 class="text-xl font-bold mb-6 text-gray-900 dark:text-gray-100">Odrzucenie wniosku</h3>

            <form method="POST" action="{{ route('admin.owner-requests.reject', $ownerRequest->id) }}">
                @csrf

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Powód odrzucenia <span class="text-red-500">*</span>
                    </label>
                    <textarea name="rejection_reason"
                              required
                              rows="5"
                              placeholder="Podaj szczegółowy powód odrzucenia..."
                              class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-3 
                                     focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500
                                     dark:bg-gray-700 dark:text-gray-200"></textarea>
                </div>

                <div class="mb-8">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="can_resubmit" value="1" checked
                               class="rounded border-gray-300 dark:border-gray-600 text-red-600 focus:ring-red-500">
                        <span class="ml-3 text-gray-700 dark:text-gray-300">
                            Pozwól użytkownikowi wysłać wniosek ponownie
                        </span>
                    </label>
                </div>

                <div class="flex justify-end gap-4">
                    <button type="button" id="cancelBtn"
                            class="px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 
                                   rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        Anuluj
                    </button>
                    <button type="submit"
                            class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                        Potwierdź odrzucenie
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const rejectBtn = document.getElementById('rejectBtn');
            const cancelBtn = document.getElementById('cancelBtn');
            const modal = document.getElementById('rejectModal');

            if (rejectBtn && modal) {
                rejectBtn.addEventListener('click', () => {
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                });
            }

            if (cancelBtn) {
                cancelBtn.addEventListener('click', () => {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                });
            }

            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }
            });

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }
            });
        });
    </script>
</x-app-layout>