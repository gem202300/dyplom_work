<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800">
                Noclegi
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
         <div class="py-6 max-w-7xl mx-auto">
          <div class="bg-white p-6 rounded shadow">
              <livewire:noclegi.noclegi-grid />
          </div>
    </div>
    </div>
</x-app-layout>
