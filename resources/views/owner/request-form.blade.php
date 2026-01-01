@php
    $lastRequest = \App\Models\OwnerRequest::where('user_id', auth()->id())
        ->latest()
        ->first();
@endphp

<x-app-layout>
    <div class="max-w-xl mx-auto mt-10 bg-white shadow p-6 rounded-xl">

        <h2 class="text-xl font-semibold mb-5">Wniosek o rolę właściciela</h2>

        @if(session('status'))
            <div class="mb-4 text-green-600">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('owner.request.submit') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium">Numer telefonu</label>
                <input type="text"
                    name="phone"
                    value="{{ old('phone', $lastRequest?->phone) }}"
                    class="w-full mt-1 rounded"
                    required>

                @error('phone') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium">Dlaczego chcesz zostać właścicielem?</label>
                <textarea name="reason" class="w-full mt-1 rounded" rows="4" required>{{ old('reason', $lastRequest?->reason) }}</textarea>


                @error('reason') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4 flex items-center">
                <input type="checkbox" name="terms" id="terms" class="mr-2" required>
                <label for="terms" class="text-sm">Akceptuję regulamin i politykę prywatności</label>
            </div>

            @error('terms') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror

            <button class="px-4 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-700
                          dark:bg-white dark:text-black dark:hover:bg-gray-200">
                Wyślij wniosek
            </button>

        </form>

    </div>
</x-app-layout>
