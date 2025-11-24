<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Szczegóły zgłoszenia
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 space-y-6">

                <div>
                    <h3 class="text-lg font-bold">Użytkownik:</h3>
                    <p>{{ $owner_request->user->name }} ({{ $owner_request->user->email }})</p>
                </div>

                <div>
                    <h3 class="text-lg font-bold">Numer telefonu:</h3>
                    <p>{{ $owner_request->phone }}</p>
                </div>

                <div>
                    <h3 class="text-lg font-bold">Powód zgłoszenia:</h3>
                    <p>{{ $owner_request->reason }}</p>
                </div>

                <div>
                    <h3 class="text-lg font-bold">Status:</h3>
                    <p>{{ ucfirst($owner_request->status) }}</p>
                </div>

                <div class="flex gap-3 mt-4 items-center">
                    @if($owner_request->status === 'pending')
                          <form method="POST"
                                action="{{ route('admin.owner-requests.approve', $owner_request->id) }}">
                              @csrf
                              <button
                                class="px-6 py-2 rounded text-white 
                                      bg-green-600 hover:bg-green-700 
                                      dark:bg-green-700 dark:hover:bg-green-800">
                                Zatwierdź
                              </button>
                          </form>
                          <form method="POST"
                                action="{{ route('admin.owner-requests.reject', $owner_request->id) }}"
                                class="ml-3">
                              @csrf
                              <button
                                  class="px-6 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                                  Odrzuć
                              </button>
                          </form>
                      @endif


                    <a href="{{ route('admin.owner-requests.index') }}"
                       class="px-6 py-2 bg-gray-200 text-black rounded hover:bg-gray-300 transition">
                       Powrót do listy
                    </a>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
