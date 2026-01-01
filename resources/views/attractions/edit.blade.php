<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Edytuj atrakcję</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto">
        <div class="bg-white p-4 rounded shadow">
            <livewire:attractions.attraction-form :attraction="$attraction" />
        </div>
    </div>
</x-app-layout>
