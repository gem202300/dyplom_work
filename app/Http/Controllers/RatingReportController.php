<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class RatingReportController extends Controller
{
    public function delete(Rating $rating): RedirectResponse
    {
        $rating->delete();

        return redirect()->route('admin.ratings.reports')
                         ->with('success', 'Komentarz został usunięty.');
    }

    public function clearReports(Rating $rating): RedirectResponse
    {
        $rating->reports()->delete();
        $rating->is_flagged = false;
        $rating->save();

        return redirect()->route('admin.ratings.reports')
                         ->with('success', 'Zgłoszenia zostały wyczyszczone.');
    }

    public function details(Rating $rating)
    {
        $rating->load(['user', 'reports.user', 'rateable']);
        return view('admin.reports.details', compact('rating'));
    }
}
