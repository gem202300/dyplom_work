<div class="p-6 space-y-4">

    <!-- Верхня панель: пошук + кнопка фільтрів -->
    <div class="flex justify-end items-center gap-2">
        <input type="text" wire:model.debounce.500ms="search" 
               placeholder="Пошук по назві або локації"
               class="border rounded-lg p-2 w-40 focus:ring-2 focus:ring-blue-400 focus:outline-none"/>

        <button wire:click="$toggle('showFilters')" 
                class="bg-blue-600 text-black px-4 py-2 rounded-lg shadow hover:bg-blue-700 transition">
            Фільтруй
        </button>
    </div>

    <!-- Модалка фільтрів -->
    <x-dialog-modal wire:model.defer="showFilters">
        <x-slot name="title">Фільтри</x-slot>
        <x-slot name="content">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Категорії</label>
                    <select wire:model="selectedCategories" multiple class="border rounded p-2 w-full">
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Мін. оцінка</label>
                        <input type="number" wire:model="minRating" min="0" max="5" step="0.1"
                               class="border rounded p-2 w-24"/>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Макс. оцінка</label>
                        <input type="number" wire:model="maxRating" min="0" max="5" step="0.1"
                               class="border rounded p-2 w-24"/>
                    </div>
                </div>
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-button wire:click="$set('showFilters', false)" flat label="Закрити"/>
            <x-button wire:click="$set('showFilters', false)" primary label="Застосувати"/>
        </x-slot>
    </x-dialog-modal>

    <!-- Сітка атракцій -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($attractions as $a)
            <div class="bg-white shadow-lg rounded-xl overflow-hidden hover:shadow-2xl transition">
                <a href="{{ route('attractions.show', $a->id) }}" class="block">
                    <div class="aspect-video bg-gray-100 overflow-hidden rounded-t-xl">
                        @if($a->photos->isNotEmpty())
                            <img src="{{ asset($a->photos->first()->path) }}" 
                                 class="w-full h-full object-cover transition-transform hover:scale-105"/>
                        @else
                            <div class="flex items-center justify-center h-full text-gray-400 italic">
                                немає фото
                            </div>
                        @endif
                    </div>
                </a>

                <div class="p-4 space-y-1">
                    <h3 class="text-lg font-semibold">
                        <a href="{{ route('attractions.show', $a->id) }}" class="hover:underline">{{ $a->name }}</a>
                    </h3>
                    <p class="text-sm text-gray-500">{{ $a->location }}</p>
                    <div class="text-sm text-gray-600">
                        <strong>Категорії:</strong> {{ $a->categories->pluck('name')->join(', ') }}
                    </div>
                    <div class="text-sm text-gray-600">
                        <strong>Години:</strong>
                        @if($a->opening_time && $a->closing_time)
                            {{ $a->opening_time }} - {{ $a->closing_time }}
                        @else — @endif
                    </div>
                    <div class="text-sm font-medium text-gray-700">
                        ⭐ {{ number_format($a->ratings_avg_rating ?? 0, 2) }}
                    </div>
                </div>

                <div class="p-4 border-t flex items-center justify-between space-x-2">
                    <a href="{{ route('attractions.show', $a->id) }}" class="flex-1 text-center py-2 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition">
                        <x-wireui-icon name="eye" class="w-5 h-5 inline"/>
                    </a>
                    <a href="{{ route('attractions.edit', $a->id) }}" class="flex-1 text-center py-2 rounded-lg bg-gray-50 text-gray-600 hover:bg-gray-100 transition">
                        <x-wireui-icon name="pencil" class="w-5 h-5 inline"/>
                    </a>
                    <button wire:click="$dispatch('deleteAttractionAction', { attraction: {{ $a->id }} })"
                            class="flex-1 text-center py-2 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition">
                        <x-wireui-icon name="trash" class="w-5 h-5 inline"/>
                    </button>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-6">
        {{ $attractions->links() }}
    </div>
</div>
