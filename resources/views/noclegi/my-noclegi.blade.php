<x-app-layout>
    <x-slot name="header">
      <div class="flex justify-between items-center">
        <h2 class="text-xl font-semibold text-gray-800">Moje noclegi</h2>
        <x-wireui-button
                primary
                label="Dodaj nocleg"
                href="{{ route('noclegi.create') }}"
            />
            </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto">
        <div class="bg-white p-6 rounded shadow space-y-6">
            <livewire:noclegi.my-noclegi-grid />
        </div>
    </div>
</x-app-layout>
