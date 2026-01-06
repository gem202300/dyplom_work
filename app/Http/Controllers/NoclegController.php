<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Nocleg;
use Illuminate\Http\Request;
use App\Models\NoclegAvailability;

class NoclegController extends Controller
{
    
    
    public function index()
    {
        $noclegi = Nocleg::where('status', 'approved')->get();
        return view('noclegi.index', compact('noclegi'));
    }

    public function show(Nocleg $nocleg, Request $request)
    {
        if ($nocleg->status !== 'approved' && !auth()->check()) {
            abort(404);
        }

        $ratings = $nocleg->ratings()->latest()->paginate(5);
        $currentMonth = $request->get('month', date('Y-m'));
        $carbonMonth = Carbon::parse($currentMonth);
        $firstDayOfWeek = $carbonMonth->copy()->startOfMonth()->dayOfWeekIso;

        return view('noclegi.show', compact('nocleg', 'ratings', 'carbonMonth', 'firstDayOfWeek'));
    }

    public function details(Nocleg $nocleg)
    {
        $this->authorize('view', $nocleg);  
        $nocleg->load('photos', 'user');

        return view('noclegi.details', compact('nocleg'));
    }


    public function create()
    {
        $this->authorize('create', Nocleg::class);
        return view('noclegi.create');
    }

    public function edit(Nocleg $nocleg)
    {
        $this->authorize('update', $nocleg);
        return view('noclegi.edit', compact('nocleg'));
    }
    
    public function getCalendarData(Nocleg $nocleg, Request $request)
    {
        // Перевіряємо, чи об'єкт затверджений
        if ($nocleg->status !== 'approved') {
            return response()->json(['error' => 'Nocleg nie jest zatwierdzony'], 403);
        }

        $currentMonth = $request->get('month', date('Y-m'));
        $carbonMonth = Carbon::parse($currentMonth);
        
        $firstDayOfWeek = $carbonMonth->copy()->startOfMonth()->dayOfWeekIso;
        $totalDays = $carbonMonth->daysInMonth;
        
        // Отримуємо доступності для поточного місяця
        $availabilities = NoclegAvailability::where('nocleg_id', $nocleg->id)
            ->whereMonth('date', $carbonMonth->month)
            ->whereYear('date', $carbonMonth->year)
            ->get()
            ->keyBy(function($item) {
                return Carbon::parse($item->date)->format('Y-m-d');
            });
        
        $days = [];
        for ($day = 1; $day <= $totalDays; $day++) {
            $date = $carbonMonth->copy()->day($day)->format('Y-m-d');
            $availability = $availabilities[$date] ?? null;
            $currentCapacity = $availability->available_capacity ?? $nocleg->capacity;
            $isBlocked = $availability?->is_blocked ?? false;
            $isToday = $date === now()->format('Y-m-d');
            $isPast = $date < now()->format('Y-m-d');
            
            // Для користувача: заблоковане = 0 місць
            if ($isBlocked) {
                $currentCapacity = 0;
            }
            
            $days[] = [
                'date' => $date,
                'number' => $day,
                'capacity' => $currentCapacity,
                'isBlocked' => $isBlocked,
                'isToday' => $isToday,
                'isPast' => $isPast,
            ];
        }
        
        return response()->json([
            'monthName' => $carbonMonth->translatedFormat('F Y'),
            'emptyDays' => $firstDayOfWeek - 1,
            'days' => $days,
            'noclegCapacity' => $nocleg->capacity,
        ]);
    }
}