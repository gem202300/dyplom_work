<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">
            Noclegi użytkownika: {{ $user->name }} ({{ $user->email }})
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto">
        <div class="bg-white p-6 rounded shadow space-y-6">
            <livewire:noclegi.my-noclegi-grid :userId="$user->id" />
        </div>
    </div>
</x-app-layout>
