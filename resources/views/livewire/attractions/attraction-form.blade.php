<div class="p-6 bg-white rounded-lg shadow-md">
    <form wire:submit.prevent="submit" enctype="multipart/form-data" class="space-y-5">

        <h2 class="text-2xl font-bold text-gray-800 mb-4">
            {{ $attraction->exists ? __('Edycja atrakcji') : __('Dodaj atrakcję') }}
        </h2>

        {{-- Nazwa --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nazwa atrakcji</label>
            <input type="text" wire:model.defer="name"
                   class="w-full bg-white text-black border border-gray-400 rounded-md px-4 py-2" />
            @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Lokalizacja --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Lokalizacja</label>
            <input type="text" wire:model.defer="location"
                   class="w-full bg-white text-black border border-gray-400 rounded-md px-4 py-2" />
            @error('location') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Opis --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Opis</label>
            <textarea wire:model.defer="description"
                      class="w-full bg-white text-black border border-gray-400 rounded-md px-4 py-2 resize-none"></textarea>
            @error('description') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Godziny --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Godzina otwarcia</label>
                <input type="time" wire:model.defer="opening_time"
                       class="w-full bg-white text-black border border-gray-400 rounded-md px-4 py-2" />
                @error('opening_time') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Godzina zamknięcia</label>
                <input type="time" wire:model.defer="closing_time"
                       class="w-full bg-white text-black border border-gray-400 rounded-md px-4 py-2" />
                @error('closing_time') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

       {{-- Kategorie --}}
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Kategorie</label>
    <div x-data="{ open: false }" class="relative">
        <button type="button" @click="open = !open"
            class="w-full bg-white text-black border border-gray-400 rounded-md px-4 py-2 text-left">
            {{ count($selectedCategories) ? 'Wybrane: ' . count($selectedCategories) : 'Wybierz kategorie' }}
        </button>
        <div x-show="open" @click.outside="open = false"
            class="absolute z-10 mt-2 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto w-full">
            @foreach($allCategories as $category)
                <label class="flex items-center px-4 py-2">
                    <input type="checkbox" value="{{ $category->id }}" wire:model.defer="selectedCategories"
                        class="mr-2">
                    {{ $category->name }}
                </label>
            @endforeach
        </div>
    </div>
    @error('selectedCategories') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
</div>



        {{-- Upload --}}
        <div class="border-2 border-dashed border-gray-400 rounded-lg p-6 text-center cursor-pointer">
            <label class="block text-sm font-medium text-gray-700 mb-2">📷 Dodaj zdjęcia</label>
            <input type="file" multiple wire:model="photos" id="photoUpload" class="hidden" />
            <label for="photoUpload"
                   class="block w-full py-10 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200">
                📁 Kliknij lub przeciągnij pliki tutaj
            </label>
            @error('photos') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Galeria --}}
        @if($attraction->exists && $attraction->photos->isNotEmpty())
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                @foreach($attraction->photos->whereNotIn('id', $photosToDelete) as $photo)
                    <div class="relative group">
                        <img src="{{ asset($photo->path) }}" class="w-full h-32 object-cover rounded shadow" />
                        <button wire:click.prevent="deletePhoto({{ $photo->id }})"
                                class="absolute top-2 right-2 bg-red-600 text-white p-1 rounded-full shadow hover:bg-red-700">
                            &times;
                        </button>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Akcje --}}
        <div class="flex justify-end gap-4 mt-6">
            <a href="{{ route('attractions.index') }}"
              class="px-5 py-2 bg-gray-600 text-black rounded-md shadow hover:bg-gray-500 transition">
                Anuluj
            </a>

            <button type="submit"
                    class="px-5 py-2 bg-indigo-600 text-white rounded-md shadow hover:bg-indigo-700 transition">
                {{ $attraction->exists ? 'Zapisz zmiany' : 'Dodaj atrakcję' }}
            </button>
        </div>
    </form>
</div>
