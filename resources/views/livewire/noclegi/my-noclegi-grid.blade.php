<div class="p-6 space-y-4">

    <div class="flex justify-end items-center gap-2">
      <input type="text" wire:model.debounce.500ms="search"
            placeholder="Wyszukaj po tytule, mieście lub ulicy"
            class="border rounded-lg p-2 w-40 focus:ring-2 focus:ring-blue-400 focus:outline-none"/>

      <button wire:click="$toggle('showFilters')" 
              class="bg-blue-600 text-black px-4 py-2 rounded-lg shadow hover:bg-blue-700 transition">
          Filtruj
      </button>
    </div>


    <x-dialog-modal wire:model.defer="showFilters">
        <x-slot name="title">Filtry</x-slot>
        <x-slot name="content">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Typ obiektu</label>
                    <select wire:model="type" class="border rounded p-2 w-full">
                        <option value="">Wszystkie</option>
                        <option value="domki">Domki</option>
                        <option value="hotel">Hotel</option>
                        <option value="pokoje prywatne">Pokoje prywatne</option>
                        <option value="apartament">Apartament</option>
                    </select>
                </div>
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-button wire:click="$set('showFilters', false)" flat label="Zamknij"/>
            <x-button wire:click="$set('showFilters', false)" primary label="Zastosuj"/>
        </x-slot>
    </x-dialog-modal>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($noclegi as $n)
            <div class="bg-white shadow-lg rounded-xl overflow-hidden hover:shadow-2xl transition">

                <a href="{{ route('noclegi.show', $n->id) }}" class="block">
                    <div class="aspect-video bg-gray-100 overflow-hidden rounded-t-xl">
                        @if($n->photos->isNotEmpty())
                            <img src="{{ asset($n->photos->first()->path) }}"
                                 class="w-full h-full object-cover transition-transform hover:scale-105"/>
                        @else
                            <div class="flex items-center justify-center h-full text-gray-400 italic">
                                brak zdjęcia
                            </div>
                        @endif
                    </div>
                </a>

                <div class="p-4 space-y-2">
                    <h3 class="text-lg font-semibold">{{ $n->title }}</h3>
                    <p class="text-sm text-gray-600">📍 {{ $n->city }}, {{ $n->street }}</p>
                    <p class="text-sm text-gray-600"><strong>Typ:</strong> {{ $n->object_type }}</p>
                    <p class="text-sm text-gray-600"><strong>Kontakt:</strong> {{ $n->contact_phone ?? '—' }}</p>
                <div class="text-sm font-medium text-gray-700">
                    ⭐ {{ number_format($n->average_rating ?? 0, 2) }}
                </div>
                <div class="text-sm font-medium text-gray-700">
                    Status: 
                    @if($n->status === 'pending') 
                        <span class="px-2 py-1 bg-yellow-200 text-yellow-700 rounded">Oczekuje na zatwierdzenie</span>
                    @elseif($n->status === 'approved') 
                        <span class="px-2 py-1 bg-green-200 text-green-700 rounded">Zatwierdzony</span>
                    @else 
                        <span class="px-2 py-1 bg-red-200 text-red-700 rounded">Odrzucony</span>
                    @endif
                </div>

                    <div class="text-sm text-gray-700 flex gap-2">
                        @if($n->has_kitchen) 🍳 @endif
                        @if($n->has_parking) 🅿️ @endif
                        @if($n->has_bathroom) 🚿 @endif
                        @if($n->has_wifi) 📶 @endif
                        @if($n->has_tv) 📺 @endif
                        @if($n->has_balcony) 🌅 @endif
                    </div>
                </div>

                <div class="p-4 border-t flex items-center justify-between space-x-2">
                    <a href="{{ route('noclegi.show', $n->id) }}" class="flex-1 text-center py-2 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition">
                        <x-wireui-icon name="eye" class="w-5 h-5 inline"/>
                    </a>
                    <a href="{{ route('noclegi.edit', $n->id) }}" class="flex-1 text-center py-2 rounded-lg bg-gray-50 text-gray-600 hover:bg-gray-100 transition">
                        <x-wireui-icon name="pencil" class="w-5 h-5 inline"/>
                    </a>
                    <button wire:click="deleteNocleg({{ $n->id }})" 
                            wire:confirm='Czy na pewno chcesz usunąć "{{ $n->title }}"?'
                            class="flex-1 text-center py-2 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition">
                        <x-wireui-icon name="trash" class="w-5 h-5 inline"/>
                    </button>
                    <a href="{{ route('noclegi.calendar', $n->id) }}"
                      class="flex-1 text-center py-2 rounded-lg bg-green-50 text-green-600 hover:bg-green-100 transition">
                        <x-wireui-icon name="calendar" class="w-5 h-5 inline"/>
                    </a>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-6">
        {{ $noclegi->links() }}
    </div>
</div>