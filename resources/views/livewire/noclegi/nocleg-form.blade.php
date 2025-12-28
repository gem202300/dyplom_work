<div class="p-6 bg-white rounded-lg shadow-md">

  @if ($nocleg->exists && $nocleg->status === 'rejected')
                <div class="mb-8 p-6 bg-red-50 border-2 border-red-300 rounded-xl">
                    <div class="flex items-start gap-4">
                        <div class="shrink-0">
                            <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-red-800 mb-3">
                                Zgłoszenie zostało odrzucone
                            </h3>
                            <p class="text-red-700 text-base leading-relaxed mb-4">
                                <strong>Powód:</strong> {{ $reject_reason }}

                            </p>
                            <p class="text-red-600 text-sm">
                                Prosimy o wprowadzenie poprawek i ponowne przesłanie obiektu do moderacji.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Нагадування для pending --}}
            @if ($nocleg->exists && $nocleg->status === 'pending')
                <div class="mb-8 p-6 bg-amber-50 border-2 border-amber-300 rounded-xl">
                    <div class="flex items-center gap-4">
                        <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-amber-800 font-medium">
                            Obiekt oczekuje na zatwierdzenie. Po zapisaniu zmian zostanie ponownie przesłany do moderacji.
                        </p>
                    </div>
                </div>
            @endif
    <form wire:submit.prevent="submit" enctype="multipart/form-data" class="space-y-5">

        <h2 class="text-2xl font-bold text-gray-800 mb-4">
            {{ $nocleg->exists ? 'Edytuj nocleg' : 'Dodaj nocleg' }}
        </h2>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tytuł</label>
            <input type="text" wire:model.defer="title"
                   class="w-full bg-white text-black border border-gray-400 rounded-md px-4 py-2" />
            @error('title') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Opis</label>
            <textarea wire:model.defer="description"
                      class="w-full bg-white text-black border border-gray-400 rounded-md px-4 py-2 resize-none"></textarea>
            @error('description') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Miasto</label>
                <input type="text" wire:model.defer="city"
                       class="w-full bg-white text-black border border-gray-400 rounded-md px-4 py-2" />
                @error('city') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ulica</label>
                <input type="text" wire:model.defer="street"
                       class="w-full bg-white text-black border border-gray-400 rounded-md px-4 py-2" />
                @error('street') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Typ obiektu</label>
                <select wire:model.defer="object_type_id"
                        class="w-full bg-white text-black border border-gray-400 rounded-md px-4 py-2">
                    <option value="">Wybierz typ</option>

                    @foreach($objectTypes as $type)
                        <option value="{{ $type->id }}">
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
                @error('object_type_id') 
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p> 
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Liczba miejsc</label>
                <input type="number" wire:model.defer="capacity" min="1"
                       class="w-full bg-white text-black border border-gray-400 rounded-md px-4 py-2" />
                @error('capacity') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Telefon kontaktowy</label>
                <input type="text" wire:model.defer="contact_phone"
                       class="w-full bg-white text-black border border-gray-400 rounded-md px-4 py-2" />
                @error('contact_phone') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Link do strony</label>
                <input type="url" wire:model.defer="link"
                       class="w-full bg-white text-black border border-gray-400 rounded-md px-4 py-2" />
                @error('link') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
      <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Wyposażenie</label>

          <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
              @foreach($allAmenities as $key => $label)
                  @if($key !== 'inne')
                      <label class="flex items-center space-x-2">
                          <input type="checkbox" value="{{ $key }}" wire:model="amenities" class="rounded border-gray-400">
                          <span>{{ $label }}</span>
                      </label>
                  @endif
              @endforeach
          </div>

          @error('amenities') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
      </div>

      <div class="mt-2">
          <label class="block text-sm font-medium text-gray-700 mb-1">Inne udogodnienia</label>
          <input type="text" wire:model.defer="other_amenities"
                class="w-full bg-white text-black border border-gray-400 rounded-md px-4 py-2" />
      </div>
        <div class="border-2 border-dashed border-gray-400 rounded-lg p-6 text-center cursor-pointer">
            <label class="block text-sm font-medium text-gray-700 mb-2">Dodaj zdjęcia</label>
            <input type="file" multiple wire:model="photos" id="photoUpload" class="hidden" />
            <label for="photoUpload"
                   class="block w-full py-10 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200">
                 Kliknij lub przeciągnij pliki tutaj
            </label>
            @error('photos') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        @if($nocleg->exists && $nocleg->photos->isNotEmpty())
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                @foreach($nocleg->photos->whereNotIn('id', $photosToDelete) as $photo)
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

        <div class="flex justify-end gap-4 mt-6">
            <a href="{{ route('noclegi.index') }}"
              class="px-5 py-2 bg-gray-600 text-black rounded-md shadow hover:bg-gray-500 transition">
                Anuluj
            </a>

            <button type="submit"
                    class="px-5 py-2 bg-indigo-600 text-white rounded-md shadow hover:bg-indigo-700 transition">
                {{ $nocleg->exists ? 'Zapisz zmiany' : 'Dodaj nocleg' }}
            </button>
        </div>

    </form>
</div>
