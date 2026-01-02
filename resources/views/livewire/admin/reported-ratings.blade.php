<div class="p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Zgłoszone komentarze</h1>
        <p class="text-gray-600 mt-1">Przeglądaj i zarządzaj zgłoszonymi komentarzami</p>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <span class="text-green-800 font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($reports as $report)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-200">
                
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-2">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800 text-sm">{{ $report->rating->user->name ?? 'Użytkownik' }}</h3>
                                <p class="text-xs text-gray-500">
                                    {{ $report->created_at->format('d.m.Y H:i') }}
                                </p>
                            </div>
                        </div>
                        
                        <div class="flex items-center bg-gray-100 px-2 py-1 rounded-lg">
                            <svg class="w-4 h-4 text-yellow-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                            <span class="font-bold text-gray-800 text-sm">{{ $report->rating->rating ?? '0' }}/5</span>
                        </div>
                    </div>
                </div>

                <div class="p-4 space-y-3">
                    <div class="flex items-center text-sm text-gray-600">
                        <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                        </svg>
                        <span>{{ class_basename($report->rating->rateable_type) }} #{{ $report->rating->rateable_id }}</span>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600 mb-1 font-medium">Komentarz:</p>
                        <div class="bg-gray-50 p-3 rounded-lg border border-gray-100">
                            <p class="text-gray-800 text-sm leading-relaxed">{{ $report->rating->comment }}</p>
                        </div>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600 mb-1 font-medium">Powód zgłoszenia:</p>
                        <div class="inline-flex items-center px-3 py-1 bg-red-50 border border-red-100 rounded-full">
                            <div class="w-2 h-2 bg-red-500 rounded-full mr-2"></div>
                            <span class="text-red-700 text-sm font-medium">{{ $report->reason }}</span>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-2 pt-2">
                        <button wire:click="deleteRating({{ $report->rating->id }})"
                                wire:confirm="Czy na pewno chcesz usunąć ten komentarz?"
                                class="inline-flex items-center justify-center px-3 py-2 bg-red-500 hover:bg-red-600 text-white text-sm font-medium rounded-lg transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Usuń
                        </button>

                        <button wire:click="clearReport({{ $report->rating->id }})"
                                wire:confirm="Czy na pewno chcesz odrzucić to zgłoszenie?"
                                class="inline-flex items-center justify-center px-3 py-2 bg-green-500 hover:bg-green-600 text-white text-sm font-medium rounded-lg transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Odrzuć
                        </button>

                        <a href="{{ route('admin.ratings.report.details', $report->rating->id) }}" 
                          class="inline-flex items-center justify-center px-3 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-lg transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Szczegóły
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full flex justify-center items-center py-20">
                <div class="text-center">
                    <div class="w-20 h-20 mx-auto mb-4 text-gray-400">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Brak zgłoszonych komentarzy</h3>
                    <p class="text-gray-500">Nie ma żadnych zgłoszonych komentarzy do przeglądu.</p>
                </div>
            </div>


        @endforelse
    </div>

    @if($reports->hasPages())
        <div class="mt-8">
            {{ $reports->links() }}
        </div>
    @endif
</div>