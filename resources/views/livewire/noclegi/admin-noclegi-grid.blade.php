<div class="p-6 space-y-4 bg-white rounded shadow">

    <div class="flex justify-end items-center gap-2">
        <input type="text" wire:model.debounce.500ms="search"
               placeholder="Wyszukaj po tytule, mieście lub ulicy"
               class="border rounded-lg p-2 w-40 focus:ring-2 focus:ring-blue-400 focus:outline-none"/>

        <button wire:click="$toggle('showFilters')" 
                class="bg-blue-600 text-black px-4 py-2 rounded-lg shadow hover:bg-blue-700 transition">
            Filtruj
        </button>

    </div>
    @if($noclegi->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($noclegi as $n)
                <div class="bg-white shadow-lg rounded-xl overflow-hidden hover:shadow-2xl transition">

                    <a href="#" class="block">
                        <div class="aspect-video bg-gray-100 overflow-hidden rounded-t-xl">
                            @if($n->photos->isNotEmpty())
                                <img src="{{ asset($n->photos->first()->path) }}"
                                     class="w-full h-full object-cover"/>
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
                        <p class="text-sm text-gray-600"><strong>Typ:</strong> {{ $n->objectType?->name }}</p>
                    </div>

                    <div class="p-4 border-t flex gap-2">
                        <button wire:click="approveNocleg({{ $n->id }})"
                                class="bg-green-500 text-white px-3 py-1 rounded">
                            Zatwierdź
                        </button>

                        <button wire:click="rejectNocleg({{ $n->id }})"
                                class="bg-red-500 text-white px-3 py-1 rounded">
                            Odrzuć
                        </button>
                        <a href="{{ route('admin.noclegi.details', $n->id) }}"
                          class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 transition">
                            Szczegóły
                        </a>


                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $noclegi->links() }}
        </div>
    @else
        <div class="flex items-center justify-center min-h-[24rem] p-6 text-gray-400 italic text-lg">
            Brak noclegów do zatwierdzenia
        </div>
    @endif

</div>
