<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Szczegóły zgłoszenia
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 space-y-6">

                {{-- Інформація про користувача --}}
                <div>
                    <h3 class="text-lg font-bold">Użytkownik:</h3>
                    <p>{{ $owner_request->user->name }} ({{ $owner_request->user->email }})</p>
                </div>

                {{-- Телефон --}}
                <div>
                    <h3 class="text-lg font-bold">Numer telefonu:</h3>
                    <p>{{ $owner_request->phone }}</p>
                </div>

                {{-- Причина --}}
                <div>
                    <h3 class="text-lg font-bold">Powód zgłoszenia:</h3>
                    <p>{{ $owner_request->reason }}</p>
                </div>

                {{-- Статус --}}
                <div>
                    <h3 class="text-lg font-bold">Status:</h3>
                    <p>{{ ucfirst($owner_request->status) }}</p>
                </div>

                {{-- Кнопки --}}
                <div class="flex gap-3 mt-4 items-center">
                    @if($owner_request->status === 'pending')
                        <button
                          wire:click="$emit('approveRequest', {{ $owner_request->id }})"
                          class="px-6 py-2 bg-green-600 text-white font-semibold rounded hover:bg-green-700 dark:bg-green-600 dark:hover:bg-green-700 transition">
                          Zatwierdź
                         </button>

                        <button
                            wire:click="$emit('rejectRequest', {{ $owner_request->id }})"
                            class="px-6 py-2 bg-red-600 text-white font-semibold rounded hover:bg-red-700 transition">
                            Odrzuć
                        </button>
                    @endif

                    <a href="{{ route('admin.owner-requests.index') }}"
                       class="px-6 py-2 bg-gray-200 text-black rounded hover:bg-gray-300 transition">
                       Powrót do listy
                    </a>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
