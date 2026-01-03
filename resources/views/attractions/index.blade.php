<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="text-xl font-semibold text-gray-800">Atrakcje</h2>
            @if (Auth::user()->isAdmin())
                <x-wireui-button href="{{ route('attractions.create') }}" primary label="Dodaj atrakcję" />
            @endif
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto">
        <div class="bg-white p-4 rounded shadow">
            <livewire:attractions.attractions-grid />
        </div>
    </div>
</x-app-layout>
