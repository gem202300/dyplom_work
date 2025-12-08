@props([
    'rateable', 
    'ratings'   
])

@auth
<div class="bg-gray-50 p-6 rounded shadow space-y-4">
    <h3 class="text-lg font-semibold">Dodaj swoją opinię</h3>

    <form method="POST" action="{{ route('ratings.store') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="rateable_type" value="{{ get_class($rateable) }}">
        <input type="hidden" name="rateable_id" value="{{ $rateable->id }}">

        <div>
            <label for="rating" class="block text-sm font-medium">Ocena (1–5, opcjonalnie)</label>
            <input id="rating" name="rating" type="number" min="1" max="5"
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow px-3 py-2"
                  value="{{ old('rating') }}">
        </div>

        <div>
            <label for="comment" class="block text-sm font-medium">Komentarz</label>
            <textarea id="comment" name="comment" rows="4"
                      class="mt-1 block w-full border border-gray-300 rounded-md shadow px-3 py-2"
                      placeholder="Podziel się swoją opinią...">{{ old('comment') }}</textarea>

            @if($errors->has('comment'))
                <p class="text-red-600 text-sm mt-1">{{ $errors->first('comment') }}</p>
            @endif

            @if(session('success'))
                <p class="text-green-600 text-sm mt-1">{{ session('success') }}</p>
            @endif
        </div>

        <button type="submit"
                class="px-5 py-2 bg-indigo-600 text-white rounded-md shadow hover:bg-indigo-700 transition">
            Wyślij opinię
        </button>
    </form>
</div>
@else
<p class="text-sm text-gray-600">
    Aby dodać opinię, <a href="{{ route('login') }}" class="text-indigo-600 underline">zaloguj się</a>.
</p>
@endauth

<div class="pt-6">
    <h2 class="text-xl font-semibold text-gray-800 mb-4">Opinie</h2>

    @forelse ($ratings as $rating)
    <div class="mb-4 p-4 bg-gray-100 rounded shadow relative">
        <div class="flex justify-between items-center mb-2">
            <span class="font-semibold">{{ $rating->user->name ?? 'Użytkownik' }}</span>
            <span class="text-sm text-gray-600">{{ $rating->created_at->format('Y-m-d H:i') }}</span>

            @auth
            <div class="relative inline-block text-left">
                <button type="button" class="text-gray-500 hover:text-gray-700" onclick="toggleOptions({{ $rating->id }})">
                    &#x22EE;
                </button>

                <div class="hidden absolute right-0 mt-2 w-40 bg-white border rounded shadow-lg z-50" id="options-menu-{{ $rating->id }}">
                    <button type="button" class="block w-full text-left px-4 py-2 hover:bg-gray-100" 
                            onclick="toggleReportReasons({{ $rating->id }})">
                        Zgłoś
                    </button>
                    <a href="#" class="block px-4 py-2 hover:bg-gray-100">Edytuj</a>
                    <a href="#" class="block px-4 py-2 hover:bg-gray-100">Usuń</a>
                </div>

                <div class="hidden absolute right-0 mt-2 w-56 bg-white border rounded shadow-lg z-50" id="report-menu-{{ $rating->id }}">
                    @foreach(['Rugactwa', 'Nieobiektywna ocena', 'Obraza'] as $reason)
                    <form method="POST" action="{{ route('ratings.report', $rating) }}">
                        @csrf
                        <input type="hidden" name="reason" value="{{ $reason }}">
                        <button type="submit" class="block w-full text-left px-4 py-2 hover:bg-gray-100">
                            {{ $reason }}
                        </button>
                    </form>
                    @endforeach
                </div>
            </div>
            @endauth
        </div>

        @if($rating->rating)
            <p class="mb-2">Ocena: {{ $rating->rating }} / 5</p>
        @endif

        @if($rating->comment)
            <p>{{ $rating->comment }}</p>
        @endif
    </div>
    @empty
    <p class="text-gray-500">Brak opinii.</p>
    @endforelse

    <div class="mt-4">
        {{ $ratings->links() }}
    </div>
</div>

<script>
function toggleOptions(id) {
    const menu = document.getElementById('options-menu-' + id);
    menu.classList.toggle('hidden');

    const reportMenu = document.getElementById('report-menu-' + id);
    if(reportMenu && !reportMenu.classList.contains('hidden')) {
        reportMenu.classList.add('hidden');
    }
}

function toggleReportReasons(id) {
    const reportMenu = document.getElementById('report-menu-' + id);
    reportMenu.classList.toggle('hidden');
}

document.addEventListener('click', function(e) {
    document.querySelectorAll('[id^="options-menu-"]').forEach(menu => {
        const id = menu.id.replace('options-menu-', '');
        const reportMenu = document.getElementById('report-menu-' + id);

        if (!menu.contains(e.target) && !reportMenu.contains(e.target) && !menu.previousElementSibling.contains(e.target)) {
            menu.classList.add('hidden');
            reportMenu.classList.add('hidden');
        }
    });
});
</script>