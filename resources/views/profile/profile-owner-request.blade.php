@php
    $user = auth()->user();

    $lastRequest = \App\Models\OwnerRequest::where('user_id', $user->id)
        ->latest()
        ->first();

    $hasPending = $lastRequest && $lastRequest->status === 'pending';
    $blocked = $lastRequest && $lastRequest->status === 'rejected' && !$lastRequest->can_resubmit;
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

                  @if ($hasPending)
                      <button
                          disabled
                          class="inline-flex items-center px-4 py-2 bg-gray-400 text-white font-semibold rounded-lg cursor-not-allowed opacity-70
                                dark:bg-gray-600">
                          {{ __('Zgłoszenie w trakcie rozpatrywania') }}
                      </button>

                  @elseif ($blocked)
                      <button
                          disabled
                          class="inline-flex items-center px-4 py-2 bg-red-500 text-white font-semibold rounded-lg cursor-not-allowed opacity-70
                                dark:bg-red-600">
                          {{ __('Możliwość zgłoszenia została zablokowana') }}
                      </button>

                  @else
                      <a href="{{ route('owner.request.form') }}"
                        class="inline-flex items-center px-4 py-2 bg-black text-white font-semibold rounded-lg shadow
                                hover:bg-gray-800 dark:bg-white dark:text-black dark:hover:bg-gray-200">
                          {{ __('Wyślij zgłoszenie') }}
                      </a>
                  @endif

              </div>
          </x-slot>

    </x-action-section>
    <x-section-border />
@endif
