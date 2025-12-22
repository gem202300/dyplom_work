<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Nocleg;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use App\Models\NoclegAvailability;

class NoclegCalendarController extends Controller
{
    public function index(Nocleg $nocleg)
    {
        $month = request('month', now()->format('Y-m'));
        $carbonMonth = Carbon::parse($month);

        $start = $carbonMonth->copy()->startOfMonth();
        $end   = $carbonMonth->copy()->endOfMonth();

        // Завантажуємо availabilities для цього місяця
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
            'end_date'   => 'required|date',
            'persons'    => 'nullable|integer|min:0',
            'action'     => 'required|string', // decrease, increase, block, reset
        ]);

        $period = CarbonPeriod::create($request->start_date, $request->end_date);

        foreach ($period as $date) {
            $availability = NoclegAvailability::firstOrCreate([
                'nocleg_id' => $nocleg->id,
                'date'      => $date->format('Y-m-d'),
            ]);

            switch ($request->action) {

                case 'decrease':
                    $availability->available_capacity =
                        max(0, ($availability->available_capacity ?? $nocleg->capacity) - $request->persons);
                    $availability->is_blocked = false;
                    break;

                case 'increase':
                    $availability->available_capacity =
                        min($nocleg->capacity, ($availability->available_capacity ?? $nocleg->capacity) + $request->persons);
                    $availability->is_blocked = false;
                    break;

                case 'block':
                    $availability->is_blocked = true;
                    $availability->available_capacity = 0;
                    break;

                case 'reset':
                    $availability->is_blocked = false;
                    $availability->available_capacity = $nocleg->capacity;
                    break;
            }

            $availability->save();
        }

        return back()->with('success', 'Dostępność zaktualizowana!');
    }

}
