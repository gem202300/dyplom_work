@php
    $user = auth()->user();
    $pendingRequest = \App\Models\OwnerRequest::where('user_id', $user->id)
                        ->where('status', 'pending')
                        ->first();
@endphp

@if (!$user->hasRole('owner'))
    <x-action-section class="mt-10">
        <x-slot name="title">
            {{ __('Zgłoś się jako właściciel obiektu') }}
            
        </x-slot>

        <x-slot name="description">
            {{ __('Jeśli chcesz zostać właścicielem, kliknij poniższy przycisk, aby wysłać zgłoszenie.') }}
        </x-slot>

        <x-slot name="content">
            <div class="mt-3 flex justify-start">

               @if ($pendingRequest)
                    <button
                        disabled
                        class="inline-flex items-center px-4 py-2 bg-black text-white font-semibold rounded-lg cursor-not-allowed opacity-70 
                              dark:bg-white dark:text-black">
                        {{ __('Zgłoszenie w trakcie rozpatrywania') }}
                    </button>
                @else
                    <a href="{{ route('owner.request.form') }}"
                      class="inline-flex items-center px-4 py-2 bg-black text-white font-semibold rounded-lg shadow hover:bg-gray-800
                              dark:bg-white dark:text-black dark:hover:bg-gray-200">
                        {{ __('Wyślij zgłoszenie') }}
                    </a>
                @endif


            </div>
        </x-slot>
    </x-action-section>
    <x-section-border />
@endif
