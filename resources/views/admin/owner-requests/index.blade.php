<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Zgłoszenia właścicieli</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto">
        <div class="bg-white p-4 rounded shadow">
            @livewire('admin.owner-request-table')
        </div>
    </div>
</x-app-layout>
