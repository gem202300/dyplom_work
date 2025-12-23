@forelse ($ratings as $rating)
<div class="mb-4 p-4 bg-gray-50 rounded-lg shadow-sm border">
    <div class="flex justify-between items-start mb-3">
        <div>
            <span class="font-semibold text-gray-800">{{ $rating->user->name ?? 'Użytkownik' }}</span>
            <div class="flex items-center gap-2 mt-1">
                @if($rating->rating)
                <div class="flex items-center">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= $rating->rating)
                            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @else
                            <svg class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @endif
                    @endfor
                    <span class="ml-2 text-gray-700">{{ $rating->rating }}/5</span>
                </div>
                @endif
                <span class="text-gray-500 text-sm">•</span>
                <span class="text-gray-500 text-sm">{{ $rating->created_at->format('d.m.Y H:i') }}</span>
            </div>
        </div>

        @auth
        <div class="relative">
            <button type="button" class="text-gray-500 hover:text-gray-700" onclick="toggleOptions({{ $rating->id }})">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                </svg>
            </button>

            <div class="hidden absolute right-0 mt-2 w-40 bg-white border rounded shadow-lg z-10" id="options-menu-{{ $rating->id }}">
                <button type="button" class="block w-full text-left px-4 py-2 hover:bg-gray-100 text-sm" 
                        onclick="toggleReportReasons({{ $rating->id }})">
                    Zgłoś
                </button>
            </div>

            <div class="hidden absolute right-0 mt-2 w-48 bg-white border rounded shadow-lg z-10" id="report-menu-{{ $rating->id }}">
                @foreach(['Rugactwa', 'Nieobiektywna ocena', 'Obraza'] as $reason)
                <form method="POST" action="{{ route('ratings.report', $rating) }}">
                    @csrf
                    <input type="hidden" name="reason" value="{{ $reason }}">
                    <button type="submit" class="block w-full text-left px-4 py-2 hover:bg-gray-100 text-sm">
                        {{ $reason }}
                    </button>
                </form>
                @endforeach
            </div>
        </div>
        @endauth
    </div>

    @if($rating->comment)
        <p class="text-gray-700 mt-3">{{ $rating->comment }}</p>
    @endif
</div>
@empty
<div class="text-center py-8">
    <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
    </svg>
    <p class="text-gray-500 mt-2">Brak opinii. Bądź pierwszy!</p>
</div>
@endforelse