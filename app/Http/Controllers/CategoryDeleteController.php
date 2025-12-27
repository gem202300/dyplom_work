<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryDeleteController extends Controller
{
    public function show(Category $category)
    {
        if (Category::count() === 1) {
            return redirect()->route('categories.index')
                ->with('error', 'Nie można usunąć ostatniej kategorii w systemie.');
        }

        $needsReplacement = $category->attractions()->has('categories', '=', 1)->exists();

        $otherCategories = Category::where('id', '!=', $category->id)->pluck('name', 'id');

        return view('categories.delete', compact('category', 'needsReplacement', 'otherCategories'));
    }

    public function destroy(Request $request, Category $category)
    {
        if (Category::count() === 1) {
            return back()->with('error', 'Nie można usunąć ostatniej kategorii.');
        }

        $request->validate([
            'replacement_category_id' => 'required_if:needs_replacement,true|exists:categories,id|different:category.id',
        ]);

        $replacementId = $request->input('replacement_category_id');

        if ($replacementId) {
            $newCategory = Category::findOrFail($replacementId);

            foreach ($category->attractions as $attraction) {
                $attraction->categories()->syncWithoutDetaching($newCategory->id);
            }
        }

        $category->attractions()->detach();
        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', 'Kategoria usunięta pomyślnie.');
    }
}