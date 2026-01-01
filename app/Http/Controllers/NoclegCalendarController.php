<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Nocleg;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use App\Models\NoclegAvailability;
use Illuminate\Validation\ValidationException;

class NoclegCalendarController extends Controller
{
    public function index(Nocleg $nocleg)
    {
        $month = request('month', now()->format('Y-m'));
        $carbonMonth = Carbon::parse($month . '-01');

        $start = $carbonMonth->copy()->startOfMonth();
        $end = $carbonMonth->copy()->endOfMonth();

        $availabilities = $nocleg->availabilities()
            ->whereBetween('date', [$start, $end])
            ->get()
            ->keyBy('date');

        return view('noclegi.calendar', compact('nocleg', 'carbonMonth', 'availabilities'));
    }

    public function update(Request $request, Nocleg $nocleg)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'persons'    => 'required|integer|min:1|max:' . $nocleg->capacity,
            'action'     => 'required|in:decrease,increase,block,reset',
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $persons = (int) $request->persons;
        $action = $request->action;

        $period = CarbonPeriod::create($startDate, $endDate);

        // === ВАЛІДАЦІЯ ДО ЗМІН ===
        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');

            $availability = $nocleg->availabilities()->where('date', $dateStr)->first();

            $currentCapacity = $availability?->available_capacity ?? $nocleg->capacity;
            $isBlocked = $availability?->is_blocked ?? false;

            // Помилка: намагаємося зменшити більше, ніж є
            if ($action === 'decrease' && $currentCapacity < $persons) {
                return back()->with('error', "W dniu {$dateStr} dostępnych jest tylko {$currentCapacity} miejsc — nie można zarezerwować {$persons}.");
            }

            // Помилка: збільшуємо понад максимум
            if ($action === 'increase' && ($currentCapacity + $persons) > $nocleg->capacity) {
                return back()->with('error', "W dniu {$dateStr} dostępnych jest tylko {$currentCapacity} miejsc — nie można zarezerwować {$persons}.");
           
            }

            // Не дозволяємо змінювати кількість в заблокованому дні
            if ($isBlocked && !in_array($action, ['block', 'reset'])) {
                throw ValidationException::withMessages([
                    'action' => "Dzień {$dateStr} jest zablokowany — można tylko zablokować lub przywrócić domyślne."
                ]);
            }
        }

        // === ЗАСТОСОВУЄМО ЗМІНИ ===
        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');

            $availability = NoclegAvailability::firstOrCreate([
                'nocleg_id' => $nocleg->id,
                'date'      => $dateStr,
            ]);

            $currentCapacity = $availability->available_capacity ?? $nocleg->capacity;

            switch ($action) {
                case 'decrease':
                    $availability->available_capacity = max(0, $currentCapacity - $persons);
                    $availability->is_blocked = false;
                    break;

                case 'increase':
                    $availability->available_capacity = min($nocleg->capacity, $currentCapacity + $persons);
                    $availability->is_blocked = false;
                    break;

                case 'block':
                    $availability->available_capacity = 0;
                    $availability->is_blocked = true;
                    break;

                case 'reset':
                    $availability->available_capacity = $nocleg->capacity;
                    $availability->is_blocked = false;
                    break;
            }

            $availability->save();
        }

        return back()->with('success', 'Harmonogram został zaktualizowany!');
    }
}