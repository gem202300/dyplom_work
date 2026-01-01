<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Usuwanie kategorii</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow sm:rounded-lg p-6">
            <h3 class="text-lg font-medium text-red-600 mb-4">
                Czy na pewno chcesz usunąć kategorię: <strong>{{ $category->name }}</strong>?
            </h3>

            @if($needsReplacement)
                <div class="mb-6 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 rounded">
                    <p class="text-sm text-yellow-800 dark:text-yellow-200">
                        Uwaga: Niektóre atrakcje mają tylko tę kategorię.
                        Musisz wybrać kategorię zastępczą, do której je przeniesiemy.
                    </p>
                </div>

                <form method="POST" action="{{ route('categories.delete', $category) }}">
                    @csrf
                    @method('POST')

                    <input type="hidden" name="needs_replacement" value="true">

                    <x-select
                        label="Kategoria zastępcza"
                        name="replacement_category_id"
                        :options="$otherCategories"
                        required
                        class="mb-6"
                    />

                    <div class="flex gap-4">
                        <x-button type="submit" red>
                            Przenieś i usuń
                        </x-button>

                        <a href="{{ route('categories.index') }}" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded">
                            Anuluj
                        </a>
                    </div>
                </form>
            @else
                <p class="mb-6 text-gray-700">
                    Żadna atrakcja nie straci kategorii. Możesz bezpiecznie usunąć.
                </p>

                <form method="POST" action="{{ route('categories.delete', $category) }}" class="inline">
                    @csrf
                    @method('POST')

                    <div class="flex gap-4">
                        <x-button type="submit" red>
                            Tak, usuń kategorię
                        </x-button>

                        <a href="{{ route('categories.index') }}" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded">
                            Anuluj
                        </a>
                    </div>
                </form>
            @endif
        </div>
    </div>
</x-app-layout>