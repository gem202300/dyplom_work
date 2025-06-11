<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="text-xl font-semibold text-gray-800">Kategorie atrakcji</h2>
            <x-wireui-button href="{{ route('categories.create') }}" primary label="Dodaj kategorię" />
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto">
        <div class="bg-white p-4 rounded shadow">
            <livewire:categories.category-table />
        </div>
    </div>
</x-app-layout>
