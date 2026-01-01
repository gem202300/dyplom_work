<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
            {{ __('Powiadomienia') }}
        </h2>

    </x-slot>

    <div class="py-12 bg-white dark:bg-gray-900"> 
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6"> 
                
                <form method="GET" action="{{ route('notifications.index') }}"
                    class="flex flex-wrap items-center gap-3 mb-6 w-full">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('notifications.index') }}"
                            class="px-3 py-1 border rounded bg-white hover:bg-gray-50 transition">Wszystkie</a>
                        <a href="{{ route('notifications.index', array_merge(request()->query(), ['type' => 'info'])) }}"
                            class="px-3 py-1 border rounded bg-white hover:bg-gray-50 transition">Info</a>
                    </div>

                    <div class="ml-auto flex items-center gap-2">
                        <input type="date" name="from" value="{{ request('from') }}"
                            class="border px-2 py-1 rounded bg-white">
                        <input type="date" name="to" value="{{ request('to') }}"
                            class="border px-2 py-1 rounded bg-white">
                        <button type="submit"
                            class="px-4 py-1 bg-white text-gray-800 border rounded hover:bg-gray-50 transition">Filtruj</button>
                    </div>
                </form>

                <div class="space-y-4">
    @foreach ($notifications as $n)
        <a href="{{ route('notifications.show', $n->id) }}" id="notif-{{ $n->id }}"
            class="block bg-white rounded-lg shadow hover:bg-gray-50 transition p-6 text-gray-900">

            <div class="flex justify-between items-start gap-4">
                <div class="flex-1">
                    <h3 class="font-semibold text-lg flex items-center gap-3">
                        {{ data_get($n->data, 'title') ?? 'Без назви' }}
                    </h3>
                    <p class="mt-1 text-black">
                        {{ data_get($n->data, 'message') ?? 'Немає тексту повідомлення' }}
                    </p>
                </div>

                @if (!$n->read_at)
                  <span
                  class="inline-block px-3 py-1 bg-blue-600 dark:bg-blue-700 text-white text-sm font-medium rounded shadow transition cursor-pointer">
                  New
                </span>


                  @endif

            </div>

            <div class="mt-4 flex items-center justify-between">
                <p class="text-xs text-gray-400">
                    {{ $n->created_at->diffForHumans() }}
                </p>

                @if (!$n->read_at)
                    <button type="button"
                            onclick="event.preventDefault(); event.stopPropagation(); markAsRead('{{ $n->id }}')"
                            class="ml-4 px-4 py-2 bg-white text-black border border-black rounded hover:bg-gray-50 transition">
                        Oznacz jako przeczytane
                    </button>
                @endif
            </div>
        </a>
    @endforeach
</div>


                <div class="mt-6">
                    {{ $notifications->links() }}
                </div>

                <button onclick="markAll()"
                    class="mt-6 px-4 py-2 bg-white text-gray-800 border rounded hover:bg-gray-50 transition">
                    Oznacz wszystkie jako przeczytane
                </button>

            </div>
        </div>
    </div>

   <script>
    function markAsRead(id) {
        fetch('/notifications/' + id + '/read', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).then(() => {
            const notif = document.getElementById('notif-' + id);
            if(notif){
                const badge = notif.querySelector('span');
                if(badge) badge.remove();
                const button = notif.querySelector('button');
                if(button) button.remove();
            }
            Livewire.dispatch('notification-read');
        });
    }

    function markAll() {
        fetch('/notifications/read-all', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).then(() => {
            document.querySelectorAll('[id^="notif-"]').forEach(el => {
                const badge = el.querySelector('span');
                const button = el.querySelector('button');

                if (badge) badge.remove();
                if (button) button.remove();
            });
            Livewire.dispatch('notification-read');
        });
    }
</script>

</x-app-layout>
