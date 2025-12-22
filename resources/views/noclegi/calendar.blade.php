<x-app-layout>
    <x-slot name="header"></x-slot>
    
    <div class="min-h-screen bg-white dark:bg-gray-900 p-4 md:p-6">

        <div class="max-w-7xl mx-auto">
            
            {{-- КАЛЕНДАР + ПАНЕЛЬ БРОНЮВАННЯ --}}
            <div class="flex flex-col lg:flex-row gap-6 mb-6 justify-center">
                
                {{-- КАЛЕНДАР (70%) --}}
                <div class="flex-shrink-0 flex-grow lg:flex-[2] min-w-[400px] max-w-[900px]">
                    <div class="space-y-4">
                        {{-- Заголовок календаря --}}
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                                <div>
                                    <h1 class="text-xl md:text-2xl font-bold text-gray-900">{{ $nocleg->title }}</h1>
                                    <p class="text-gray-600 mt-1 text-sm">
                                        Kalendarz dostępności • Pojemność: 
                                        <span class="font-semibold">{{ $nocleg->capacity }} osób</span>
                                    </p>
                                </div>
                                <div class="flex items-center gap-4">
                                    <span class="text-sm text-gray-500 bg-white px-3 py-1.5 rounded-lg border border-gray-200">
                                        {{ $carbonMonth->translatedFormat('F Y') }}
                                    </span>
                                    <div class="flex items-center gap-1">
                                        <a href="?month={{ $carbonMonth->copy()->subMonth()->format('Y-m') }}"
                                           class="p-1.5 hover:bg-gray-100 rounded transition">
                                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                            </svg>
                                        </a>
                                        <a href="?month={{ $carbonMonth->copy()->addMonth()->format('Y-m') }}"
                                           class="p-1.5 hover:bg-gray-100 rounded transition">
                                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Календар --}}
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                            {{-- Дні тижня --}}
                            <div class="grid grid-cols-7 gap-2 mb-6">
                                @foreach(['Pn', 'Wt', 'Śr', 'Cz', 'Pt', 'So', 'N'] as $day)
                                    <div class="text-center text-2xl font-semibold text-gray-700 py-3 px-1">
                                        {{ $day }}
                                    </div>
                                @endforeach
                            </div>

                            {{-- Дні місяця --}}
                            <div class="grid grid-cols-7 gap-1">
                                @php
                                    $firstDayOfWeek = $carbonMonth->copy()->startOfMonth()->dayOfWeekIso;
                                    $totalDays = $carbonMonth->daysInMonth;
                                @endphp

                                {{-- Порожні комірки --}}
                                @for ($i = 1; $i < $firstDayOfWeek; $i++)
                                    <div class="h-16"></div>
                                @endfor

                                {{-- Дні місяця --}}
                                @for ($day = 1; $day <= $totalDays; $day++)
                                  @php
                                      $date = $carbonMonth->copy()->day($day)->format('Y-m-d');
                                      $availability = $availabilities[$date] ?? null;
                                      $currentCapacity = $availability->available_capacity ?? $nocleg->capacity;
                                      $isBlocked = $availability?->is_blocked ?? false;
                                      $isToday = $date === now()->format('Y-m-d');

                                      $stateClasses = [
                                          'blocked' => [
                                              'text' => 'text-red-700',
                                              'bg' => 'bg-red-50',
                                              'status' => 'text-red-600',
                                              'indicator' => 'bg-red-600' 
                                          ],
                                          'empty' => [
                                              'text' => 'text-red-600',
                                              'bg' => 'bg-red-50',
                                              'status' => 'text-red-600',
                                              'indicator' => 'bg-red-500' 
                                          ],
                                          'full' => [
                                              'text' => 'text-gray-800',
                                              'bg' => 'bg-white',
                                              'status' => 'text-green-600',
                                              'indicator' => 'bg-green-500' 
                                          ],
                                          'partial' => [
                                              'text' => 'text-gray-800',
                                              'bg' => 'bg-white',
                                              'status' => 'text-yellow-600',
                                              'indicator' => 'bg-yellow-500' 
                                          ]
                                      ];
                                      
                                      if ($isBlocked) {
                                          $state = $stateClasses['blocked'];
                                      } elseif ($currentCapacity == 0) {
                                          $state = $stateClasses['empty'];
                                      } elseif ($currentCapacity == $nocleg->capacity) {
                                          $state = $stateClasses['full'];
                                      } else {
                                          $state = $stateClasses['partial'];
                                      }
                                      
                                      $baseClasses = 'h-16 flex flex-col items-center justify-center cursor-pointer border rounded transition-all duration-150 hover:bg-gray-50';
                                      $borderClass = $isToday ? 'border-blue-300 bg-blue-50' : 'border-gray-200';
                                      $ringClass = $isToday ? 'ring-1 ring-blue-300' : '';
                                      $textClass = $isToday ? 'text-blue-700' : $state['text'];
                                  @endphp

                                  <div class="relative" onclick="selectDate('{{ $date }}')" data-date="{{ $date }}">
                                      <div class="{{ $baseClasses }} {{ $borderClass }} {{ $state['bg'] }} {{ $ringClass }}">
                                          
                                          <div class="text-lg font-bold {{ $textClass }}">{{ $day }}</div>
                                          
                                          <div class="mt-1">
                                              <div class="w-7 h-1.5 {{ $state['indicator'] }} rounded-full shadow-sm"></div>
                                          </div>
                                          
                                          <div class="text-xs font-semibold {{ $state['status'] }} mt-0.5">
                                              @if($isBlocked)
                                                  Zablokowane
                                              @elseif($currentCapacity == 0)
                                                  Brak
                                              @elseif($currentCapacity == $nocleg->capacity)
                                                  Wolne ({{ $currentCapacity }})
                                              @else
                                                  Częściowo ({{ $currentCapacity }})
                                              @endif
                                          </div>
                                      </div>
                                      
                                      <div class="absolute top-0 right-0 w-2 h-2 bg-blue-500 rounded-full opacity-0"
                                          id="indicator-{{ $date }}"></div>
                                  </div>
                              @endfor
                            </div>

                            {{-- Легенда --}}
                        <div class="mt-4 flex items-center gap-6 text-sm font-medium">
                            <div class="flex items-center gap-2">
                                <div class="w-5 h-3 bg-green-500 rounded shadow-sm"></div>
                                <span class="text-gray-700">Wolne</span>
                            </div>

                            <div class="flex items-center gap-2">
                                <div class="w-5 h-3 bg-yellow-500 rounded shadow-sm"></div>
                                <span class="text-gray-700">Częściowo</span>
                            </div>

                            <div class="flex items-center gap-2">
                                <div class="w-5 h-3 bg-red-500 rounded shadow-sm"></div>
                                <span class="text-gray-700">Brak</span>
                            </div>

                            <div class="flex items-center gap-2">
                                <div class="w-5 h-3 bg-red-600 rounded shadow-sm"></div>
                                <span class="text-gray-700">Zablokowane</span>
                            </div>
                        </div>

                        </div>
                    </div>
                </div>

                {{-- ПАНЕЛЬ БРОНЮВАННЯ (30%) --}}
                <div class="flex-shrink-0 flex-grow lg:flex-[1] min-w-[250px] max-w-[450px]">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 h-full">
                        <h3 class="text-sm font-semibold text-gray-900 mb-4 uppercase tracking-wider">
                            Zarządzaj dostępnością
                        </h3>

                        {{-- Повідомлення --}}
                        @if (session('success'))
                            <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="text-green-700 text-xs">{{ session('success') }}</span>
                                </div>
                            </div>
                        @endif

                        {{-- Форма --}}
                        <form method="POST" action="{{ route('noclegi.calendar.update', $nocleg->id) }}" class="space-y-4">
                            @csrf

                            {{-- Вибраний період --}}
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Okres</label>
                                <div class="space-y-2">
                                    <input type="date" name="start_date" required id="start_date"
                                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition">
                                    <input type="date" name="end_date" required id="end_date"
                                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition">
                                </div>
                            </div>

                            {{-- Кількість осіб --}}
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">
                                    Ilość osób (max: {{ $nocleg->capacity }})
                                </label>
                                <input type="number" name="persons" min="0" max="{{ $nocleg->capacity }}" value="1"
                                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition">
                            </div>

                            {{-- Дія --}}
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Rodzaj akcji</label>
                                <div class="space-y-1.5">
                                    @foreach([
                                        ['value' => 'decrease', 'color' => 'blue', 'label' => 'Zablokuj miejsca'],
                                        ['value' => 'increase', 'color' => 'green', 'label' => 'Zwolnij miejsca'],
                                        ['value' => 'block', 'color' => 'red', 'label' => 'Zablokuj całość'],
                                        ['value' => 'reset', 'color' => 'gray', 'label' => 'Przywróć domyślne']
                                    ] as $action)
                                        <label class="flex items-center p-2 border border-gray-200 rounded hover:bg-{{ $action['color'] }}-50 transition cursor-pointer">
                                            <input type="radio" name="action" value="{{ $action['value'] }}" 
                                                   {{ $action['value'] == 'decrease' ? 'checked' : '' }}
                                                   class="mr-2 text-{{ $action['color'] }}-600">
                                            <span class="text-xs font-medium">{{ $action['label'] }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Кнопка --}}
                            <div class="pt-2">
                                <button type="submit"
                                        class="w-full bg-gray-800 hover:bg-gray-900 text-white text-xs font-medium py-2.5 px-4 rounded transition duration-200">
                                    Zastosuj zmiany
                                </button>
                            </div>
                            
                            <p class="text-xs text-gray-500 text-center mt-2">
                                Kliknij w dzień w kalendarzu, aby wybrać
                            </p>
                        </form>

                        {{-- Інформація про вибір --}}
                        <div class="mt-6 pt-4 border-t border-gray-200">
                            <h4 class="text-xs font-semibold text-gray-700 mb-2 uppercase">Wybrane</h4>
                            <div id="selection-info" class="text-xs text-gray-600">
                                Kliknij w dzień w kalendarzu
                            </div>
                            <div class="mt-2 text-xs text-gray-500">
                                <div>• Kliknij raz: wybierz początek</div>
                                <div>• Kliknij drugi raz: wybierz koniec</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- СТАТИСТИКА --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Statystyki dostępności</h3>
                        <p class="text-sm text-gray-500 mt-1">Stan na {{ $carbonMonth->translatedFormat('F Y') }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="text-xs text-gray-500 bg-gray-50 px-3 py-1.5 rounded-lg border">
                            <span class="font-medium">{{ $nocleg->capacity }}</span> miejsc całkowicie
                        </div>
                    </div>
                </div>

                {{-- Основні метрики --}}
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    @php
                        $totalDays = $carbonMonth->daysInMonth;
                        $blockedDays = $availabilities->where('is_blocked', true)->count();
                        $fullDays = $availabilities->where('available_capacity', $nocleg->capacity)->count();
                        $partialDays = $availabilities->where('available_capacity', '>', 0)
                                                      ->where('available_capacity', '<', $nocleg->capacity)
                                                      ->count();
                        $emptyDays = $availabilities->where('available_capacity', 0)
                                                    ->where('is_blocked', false)
                                                    ->count();
                        
                        $percentages = [
                            'full' => $totalDays > 0 ? round(($fullDays / $totalDays) * 100) : 0,
                            'partial' => $totalDays > 0 ? round(($partialDays / $totalDays) * 100) : 0,
                            'empty' => $totalDays > 0 ? round(($emptyDays / $totalDays) * 100) : 0,
                            'blocked' => $totalDays > 0 ? round(($blockedDays / $totalDays) * 100) : 0
                        ];
                        
                        $stats = [
                            [
                                'count' => $fullDays,
                                'label' => 'Dni wolne',
                                'percent' => $percentages['full'],
                                'color' => 'green',
                                'icon' => 'M5 13l4 4L19 7'
                            ],
                            [
                                'count' => $partialDays,
                                'label' => 'Częściowo',
                                'percent' => $percentages['partial'],
                                'color' => 'yellow',
                                'icon' => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z'
                            ],
                            [
                                'count' => $emptyDays,
                                'label' => 'Brak miejsc',
                                'percent' => $percentages['empty'],
                                'color' => 'red',
                                'icon' => 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636'
                            ],
                            [
                                'count' => $blockedDays,
                                'label' => 'Zablokowane',
                                'percent' => $percentages['blocked'],
                                'color' => 'purple',
                                'icon' => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z'
                            ]
                        ];
                    @endphp
                    
                    @foreach($stats as $stat)
                        <div class="bg-gradient-to-br from-{{ $stat['color'] }}-50 to-{{ $stat['color'] }}-25 rounded-xl border border-{{ $stat['color'] }}-100 p-4">
                            <div class="flex items-start justify-between">
                                <div>
                                    <div class="text-2xl font-bold text-{{ $stat['color'] }}-700">{{ $stat['count'] }}</div>
                                    <div class="text-sm font-medium text-{{ $stat['color'] }}-800 mt-1">{{ $stat['label'] }}</div>
                                    <div class="text-xs text-{{ $stat['color'] }}-600 mt-2">{{ $stat['percent'] }}% miesiąca</div>
                                </div>
                                <div class="w-10 h-10 bg-{{ $stat['color'] }}-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-{{ $stat['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $stat['icon'] }}" />
                                    </svg>
                                </div>
                            </div>
                            <div class="mt-4">
                                <div class="h-1.5 w-full bg-{{ $stat['color'] }}-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-{{ $stat['color'] }}-500 rounded-full" style="width: {{ $stat['percent'] }}%"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <script>
        let selectedStartDate = null;
        let selectedEndDate = null;
        let isSelectingRange = false;

        function selectDate(date) {
            const dateStr = date;
            
            if (!selectedStartDate) {
                // Перший клік - початок діапазону
                selectedStartDate = dateStr;
                selectedEndDate = null;
                isSelectingRange = true;
                
                updateDateInputs(dateStr, dateStr);
                highlightDate(dateStr, true);
                updateSelectionInfo(dateStr, null);
                
            } else if (isSelectingRange && !selectedEndDate) {
                // Другий клік - кінець діапазону
                selectedEndDate = dateStr;
                isSelectingRange = false;
                
                let start = new Date(selectedStartDate);
                let end = new Date(selectedEndDate);
                
                if (start > end) {
                    [start, end] = [end, start];
                    [selectedStartDate, selectedEndDate] = [selectedEndDate, selectedStartDate];
                }
                
                updateDateInputs(selectedStartDate, selectedEndDate);
                highlightDateRange(start, end);
                updateSelectionInfo(selectedStartDate, selectedEndDate);
                
            } else {
                // Третій клік - скидання і новий вибір
                clearSelection();
                selectedStartDate = dateStr;
                selectedEndDate = null;
                isSelectingRange = true;
                
                updateDateInputs(dateStr, dateStr);
                highlightDate(dateStr, true);
                updateSelectionInfo(dateStr, null);
            }
        }

        function highlightDate(date, isStart = false) {
            clearIndicators();
            
            const indicator = document.getElementById(`indicator-${date}`);
            if (indicator) indicator.style.opacity = '1';
            
            const element = document.querySelector(`[data-date="${date}"] div`);
            if (element && isStart) {
                element.classList.add('ring-1', 'ring-blue-400');
            }
        }

        function highlightDateRange(start, end) {
            clearIndicators();
            clearRings();
            
            let current = new Date(start);
            while (current <= end) {
                const dateStr = current.toISOString().split('T')[0];
                const indicator = document.getElementById(`indicator-${dateStr}`);
                if (indicator) indicator.style.opacity = '1';
                
                const element = document.querySelector(`[data-date="${dateStr}"] div`);
                if (element) element.classList.add('ring-1', 'ring-blue-300');
                
                current.setDate(current.getDate() + 1);
            }
        }

        function clearIndicators() {
            document.querySelectorAll('[id^="indicator-"]').forEach(indicator => {
                indicator.style.opacity = '0';
            });
        }

        function clearRings() {
            document.querySelectorAll('[data-date] div').forEach(element => {
                element.classList.remove('ring-1', 'ring-blue-300', 'ring-blue-400');
            });
        }

        function clearSelection() {
            clearIndicators();
            clearRings();
        }

        function updateDateInputs(start, end) {
            document.getElementById('start_date').value = start;
            document.getElementById('end_date').value = end;
        }

        function updateSelectionInfo(start, end) {
            const infoDiv = document.getElementById('selection-info');
            
            if (start && end) {
                const startDate = new Date(start);
                const endDate = new Date(end);
                const diffTime = Math.abs(endDate - startDate);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                
                infoDiv.innerHTML = `
                    <div class="font-medium">${start} — ${end}</div>
                    <div class="mt-1">${diffDays} dni</div>
                `;
            } else if (start) {
                infoDiv.innerHTML = `
                    <div class="font-medium">${start}</div>
                    <div class="mt-1">Kliknij dzień końcowy</div>
                `;
            } else {
                infoDiv.innerHTML = 'Kliknij w dzień w kalendarzu';
            }
        }

        // Ініціалізація
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().split('T')[0];
            updateDateInputs(today, today);
            updateSelectionInfo(today, today);
            highlightDate(today, true);
            selectedStartDate = today;
        });
    </script>

    <style>
        [data-date] div {
            transition: all 0.15s ease;
        }
        
        [data-date]:hover div {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        input[type="date"] {
            font-size: 13px;
        }
        
        input[type="radio"] {
            width: 14px;
            height: 14px;
        }
        
        @media (max-width: 768px) {
            .grid-cols-7 {
                grid-template-columns: repeat(7, 1fr);
            }
            
            [data-date] div {
                height: 3.5rem;
                padding: 0.25rem;
            }
        }
    </style>
</x-app-layout>