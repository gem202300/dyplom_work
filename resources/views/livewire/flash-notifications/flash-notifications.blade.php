<div>
    @if (flash()->hasMessages())
        @foreach (flash()->getMessages() as $message)
            <script>
                Wireui.hook('notifications:load', () => {
                    window.$wireui.notify({
                        title: '{{ $message->title }}',
                        description: '{{ $message->description }}',
                        icon: '{{ $message->icon }}'
                    })
                });
            </script>
        @endforeach
    @endif
</div>
