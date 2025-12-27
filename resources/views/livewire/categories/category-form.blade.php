<div class="p-2">
    <form wire:submit.prevent="submit">
        <x-wireui-input
            label="Nazwa kategorii"
            name="name"
            wire:model.defer="name"
            placeholder="Wprowadź nazwę"
            class="mb-4"
        />
        @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

        <div class="flex justify-end gap-4 mt-6">
            {{-- Кнопка Anuluj --}}
            <a href="{{ route('categories.index') }}"
               class="inline-block px-5 py-2 border border-black bg-gray-200 dark:bg-gray-700 dark:text-white hover:bg-gray-300 dark:hover:bg-gray-600 font-semibold rounded shadow transition">
                Anuluj
            </a>

            {{-- Кнопка Submit --}}
            <button type="submit"
                class="inline-block px-5 py-2 border border-black bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded shadow transition"
                style="color: rgb(0, 0, 0) !important; visibility: visible !important;">
                Dodaj
            </button>
        </div>
    </form>
</div>
