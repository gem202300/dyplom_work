<div class="mb-4 p-4 bg-gray-100 rounded shadow">
    <div class="flex justify-between items-center mb-2">
        <span class="font-semibold">{{ $rating->user->name }}</span>
        <span class="text-sm text-gray-600">{{ $rating->created_at->format('Y-m-d H:i') }}</span>
    </div>

    @if($rating->rating)
        <p class="mb-2">Ocena: {{ $rating->rating }} / 5</p>
    @endif

    @if($rating->comment)
        <p>{{ $rating->comment }}</p>
    @endif
</div>
