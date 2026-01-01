@props([
    'rateable'
])

@php
    // Отримуємо початкові відгуки
    $initialQuery = \App\Models\Rating::where('rateable_type', get_class($rateable))
        ->where('rateable_id', $rateable->id)
        ->with('user')
        ->where('is_flagged', false)
        ->orderBy('created_at', 'desc')
        ->paginate(10);
    
    $currentRating = request('rating', '');
    $currentSort = request('sort', 'latest');
@endphp

<div class="bg-white rounded-lg shadow p-6 space-y-6" id="ratings-container">
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
                @if($errors->has('rating'))
                    <p class="text-red-600 text-sm mt-1">{{ $errors->first('rating') }}</p>
                @endif
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

    {{-- Фільтри та сортування для відгуків --}}
    <div class="border-t pt-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
            <h2 class="text-xl font-semibold text-gray-800">Opinie (<span id="ratings-count">{{ $initialQuery->total() }}</span>)</h2>
            
            <div class="flex flex-wrap gap-3">
                {{-- Форма фільтрів --}}
                <div class="flex flex-wrap gap-3" id="ratings-filter-form">
                    {{-- Фільтр за оцінкою --}}
                    <select id="rating-filter" class="border rounded-lg p-2 text-sm">
                        <option value="">Wszystkie oceny</option>
                        <option value="5" {{ $currentRating == '5' ? 'selected' : '' }}>⭐⭐⭐⭐⭐ (5)</option>
                        <option value="4" {{ $currentRating == '4' ? 'selected' : '' }}>⭐⭐⭐⭐ (4)</option>
                        <option value="3" {{ $currentRating == '3' ? 'selected' : '' }}>⭐⭐⭐ (3)</option>
                        <option value="2" {{ $currentRating == '2' ? 'selected' : '' }}>⭐⭐ (2)</option>
                        <option value="1" {{ $currentRating == '1' ? 'selected' : '' }}>⭐ (1)</option>
                    </select>
                    
                    {{-- Сортування --}}
                    <select id="sort-filter" class="border rounded-lg p-2 text-sm">
                        <option value="latest" {{ $currentSort == 'latest' ? 'selected' : '' }}>Najnowsze</option>
                        <option value="oldest" {{ $currentSort == 'oldest' ? 'selected' : '' }}>Najstarsze</option>
                        <option value="highest" {{ $currentSort == 'highest' ? 'selected' : '' }}>Najwyższe oceny</option>
                        <option value="lowest" {{ $currentSort == 'lowest' ? 'selected' : '' }}>Najniższe oceny</option>
                    </select>
                    
                    {{-- Кнопка скидання --}}
                    @if($currentRating || $currentSort != 'latest')
                        <button id="reset-filters" 
                                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm hover:bg-gray-300 transition">
                            Wyczyść
                        </button>
                    @endif
                </div>
            </div>
        </div>

        {{-- Контейнер для відгуків --}}
        <div id="ratings-list">
            @include('components.partials.ratings-list', ['ratings' => $initialQuery])
        </div>

        {{-- Пагінація --}}
        @if($initialQuery->hasPages())
        <div id="ratings-pagination" class="mt-6">
            {{ $initialQuery->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>

<style>
    .loading-spinner {
        display: inline-block;
        width: 24px;
        height: 24px;
        border: 3px solid #f3f3f3;
        border-top: 3px solid #3498db;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin-right: 10px;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .loading-container {
        text-align: center;
        padding: 40px 0;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ratingFilter = document.getElementById('rating-filter');
    const sortFilter = document.getElementById('sort-filter');
    const resetBtn = document.getElementById('reset-filters');
    const ratingsList = document.getElementById('ratings-list');
    const ratingsPagination = document.getElementById('ratings-pagination');
    const ratingsCount = document.getElementById('ratings-count');
    
    let isLoading = false;
    let currentPage = 1;
    
    // Кодування типу моделі для URL
    const rateableType = '{{ str_replace('\\', '\\\\', get_class($rateable)) }}';
    const rateableId = {{ $rateable->id }};
    
    // Функція для завантаження відгуків
    function loadRatings(page = 1) {
        if (isLoading) return;
        
        currentPage = page;
        isLoading = true;
        
        const rating = ratingFilter.value;
        const sort = sortFilter.value;
        
        // Оновлюємо URL без перезавантаження сторінки
        const params = new URLSearchParams();
        if (rating) params.set('rating', rating);
        if (sort && sort !== 'latest') params.set('sort', sort);
        if (page > 1) params.set('page', page);
        
        const url = '{{ url()->current() }}' + (params.toString() ? '?' + params.toString() : '');
        window.history.pushState({}, '', url);
        
        // Показуємо індикатор завантаження
        ratingsList.innerHTML = `
            <div class="loading-container">
                <div class="loading-spinner"></div>
                <p class="text-gray-500 mt-2">Ładowanie opinii...</p>
            </div>
        `;
        
        // AJAX запит
        fetch(`/ratings/filter/${encodeURIComponent(rateableType)}/${rateableId}?${params.toString()}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                ratingsList.innerHTML = data.html;
                ratingsCount.textContent = data.total;
                
                if (data.pagination) {
                    if (ratingsPagination) {
                        ratingsPagination.innerHTML = data.pagination;
                    }
                } else {
                    if (ratingsPagination) {
                        ratingsPagination.innerHTML = '';
                    }
                }
                
                isLoading = false;
            })
            .catch(error => {
                console.error('Error:', error);
                ratingsList.innerHTML = `
                    <div class="text-center py-8">
                        <p class="text-red-500">Wystąpił błąd podczas ładowania opinii.</p>
                        <button onclick="loadRatings(1)" class="mt-2 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                            Spróbuj ponownie
                        </button>
                    </div>
                `;
                isLoading = false;
            });
    }
    
    // Обробники змін фільтрів
    ratingFilter.addEventListener('change', () => {
        loadRatings(1);
    });
    
    sortFilter.addEventListener('change', () => {
        loadRatings(1);
    });
    
    // Обробник скидання фільтрів
    if (resetBtn) {
        resetBtn.addEventListener('click', () => {
            ratingFilter.value = '';
            sortFilter.value = 'latest';
            loadRatings(1);
        });
    }
    
    // Обробник кліків по пагінації
    document.addEventListener('click', function(e) {
        const paginationLink = e.target.closest('.pagination a');
        if (paginationLink) {
            e.preventDefault();
            const url = new URL(paginationLink.href);
            const page = url.searchParams.get('page') || 1;
            loadRatings(page);
        }
    });
});

// Ваш існуючий код для меню опцій
function toggleOptions(id) {
    const menu = document.getElementById('options-menu-' + id);
    if (menu) {
        menu.classList.toggle('hidden');
    }
}

function toggleReportReasons(id) {
    const menu = document.getElementById('options-menu-' + id);
    const reportMenu = document.getElementById('report-menu-' + id);
    
    if (menu) menu.classList.add('hidden');
    if (reportMenu) reportMenu.classList.toggle('hidden');
}

document.addEventListener('click', function(e) {
    document.querySelectorAll('[id^="options-menu-"], [id^="report-menu-"]').forEach(menu => {
        const button = menu.previousElementSibling;
        if (!menu.contains(e.target) && (!button || !button.contains(e.target))) {
            menu.classList.add('hidden');
        }
    });
});
</script>