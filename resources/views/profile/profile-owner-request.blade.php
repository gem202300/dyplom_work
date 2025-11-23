@php
    $user = auth()->user();
@endphp

@if (!$user->hasRole('owner'))
    <x-action-section class="mt-10">
        <x-slot name="title">
            {{ __('Zgłoś się jako właściciel') }}
        </x-slot>

        <x-slot name="description">
            {{ __('Jeśli chcesz zostać właścicielem, kliknij poniższy przycisk, aby wysłać zgłoszenie.') }}
        </x-slot>

        <x-slot name="content">
            <div class="mt-3 flex justify-start">
                <a href="{{ route('owner.request.form') }}"
                  class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg shadow hover:bg-blue-700
                          dark:bg-white dark:text-black dark:hover:bg-gray-200">
                    {{ __('Wyślij zgłoszenie') }}
                </a>

            </div>
        </x-slot>
    </x-action-section>
@endif
