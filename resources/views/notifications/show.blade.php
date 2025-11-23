<x-app-layout>
    <x-slot name="header">
         <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
            {{ __('Powiadomienia') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-white dark:bg-gray-900">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">

                <div class="space-y-6">

                    <div class="max-w-3xl mx-auto p-6 bg-white shadow rounded-lg">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h2 class="text-xl font-bold text-black">
                                    {{ data_get($notification->data, 'title') ?? data_get($notification->data, 'text') ?? 'Без назви' }}
                                </h2>
                                <p class="text-sm text-gray-500 mt-1">
                                    Utworzono: {{ $notification->created_at->format('d.m.Y H:i') }}
                                </p>
                            </div>

                            <span
                                class="inline-block px-3 py-1 bg-blue-600 dark:bg-blue-700 text-white text-sm font-medium rounded shadow transition cursor-pointer">
                                New
                            </span>
                        </div>

                        <hr class="my-4 border-gray-200">

                        <div class="mt-4">
                            <div class="w-full bg-white text-black p-6 rounded-lg border border-gray-200 shadow">
                                {{ data_get($notification->data, 'message') ?? data_get($notification->data, 'text') ?? '' }}
                            </div>
                        </div>
                        <div class="mt-6 flex gap-3">
                            <button type="button"
                                    onclick="markAsRead('example-id')"
                                    class="px-4 py-2 bg-blue-600 text-black rounded hover:bg-blue-500 transition">
                                Oznacz jako przeczytane
                            </button>

                            <a href="{{ route('notifications.index') }}" class="px-4 py-2 bg-white text-black border rounded hover:bg-gray-100 transition">
                                Powrót do listy
                            </a>
                        </div>
                    </div>

                   
                </div>

              

            </div>
        </div>
    </div>
</x-app-layout>

<script>
    function markAsRead(id) {
        fetch('/notifications/' + id + '/read', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).then(() => location.reload());
    }
</script>
