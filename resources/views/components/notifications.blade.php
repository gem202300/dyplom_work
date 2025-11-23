@auth
    @if (auth()->user()->unreadNotifications->count() > 0)
        <div class="fixed top-5 right-5 w-80 space-y-3 z-50">
            @foreach (auth()->user()->unreadNotifications as $notification)
                <div x-data="{ show: true }" x-show="show" x-transition
                    class="p-4 rounded-xl shadow-lg border bg-white flex justify-between items-start">
                    <div class="text-gray-800">
                        @php
                            $notifTitle = data_get($notification->data, 'title');
                            $notifMessage =
                                data_get($notification->data, 'message') ?? data_get($notification->data, 'text');
                        @endphp

                        @if ($notifTitle)
                            <div class="font-semibold">{{ $notifTitle }}</div>
                            @if ($notifMessage)
                                <div class="text-sm mt-1">{{ $notifMessage }}</div>
                            @endif
                        @else
                            <div>{{ $notifMessage ?? '' }}</div>
                        @endif
                    </div>

                    <button class="text-gray-400 hover:text-gray-600 ml-3"
                        onclick="markNotificationRead('{{ $notification->id }}')">
                        ✕
                    </button>
                </div>
            @endforeach
        </div>
    @endif
@endauth

<script>
    function markNotificationRead(id) {
        fetch('/notifications/read/' + id, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        });

        document.querySelector(`button[onclick="markNotificationRead('${id}')"]`)
            .closest('div')
            .remove();
    }
</script>
