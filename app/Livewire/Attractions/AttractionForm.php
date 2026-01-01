<?php

namespace App\Livewire\Attractions;

use Carbon\Carbon;
use App\Models\MapIcon;
use Livewire\Component;
use App\Models\Category;
use App\Models\Attraction;
use Livewire\WithFileUploads;
use App\Models\AttractionPhoto;
use WireUi\Traits\WireUiActions;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AttractionForm extends Component
{
    use AuthorizesRequests, WithFileUploads, WireUiActions;

    public ?Attraction $attraction = null;

    // Podstawowe pola
    public $name = '';
    public $location = '';
    public $description = '';
    public $opening_time = '';
    public $closing_time = '';
    public $mapIcon = '';

    // 📍 Współrzędne
    public $latitude = null;
    public $longitude = null;

    // Kategorie i ikony
    public $selectedCategories = [];
    public $allCategories = [];
    public $mapIcons = [];
    public $suggestedIcon = null;
    public $showIconDropdown = false;

    // Zdjęcia
    public $photos = [];
    public $photosToDelete = [];

    public function mount(Attraction $attraction = null)
    {
        $this->attraction = $attraction ?? new Attraction();
        $this->allCategories = Category::all();
        
        // ЗМІНА: Тільки іконки для атракцій (з категоріями)
        $this->mapIcons = MapIcon::whereNotNull('category_id')->get();

        if ($this->attraction->exists) {
            $this->name = $this->attraction->name;
            $this->location = $this->attraction->location;
            $this->description = $this->attraction->description;
            $this->mapIcon = $this->attraction->map_icon;

            $this->opening_time = $this->attraction->opening_time
                ? Carbon::parse($this->attraction->opening_time)->format('H:i')
                : '';

            $this->closing_time = $this->attraction->closing_time
                ? Carbon::parse($this->attraction->closing_time)->format('H:i')
                : '';

            $this->latitude = $this->attraction->latitude;
            $this->longitude = $this->attraction->longitude;

            $this->selectedCategories = $this->attraction
                ->categories
                ->pluck('id')
                ->toArray();
        }
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'description' => 'nullable|string',
            'mapIcon' => 'required|string',

            'opening_time' => 'nullable|date_format:H:i',
            'closing_time' => 'nullable|date_format:H:i',

            // 📍 Współrzędne
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',

            'selectedCategories' => 'array',
            'selectedCategories.*' => 'exists:categories,id',

            'photos' => 'array',
            'photos.*' => 'image|max:2048',
        ];
    }

    // Automatyczne sugerowanie ikony na podstawie pierwszej kategorii
    public function updatedSelectedCategories()
    {
        if (count($this->selectedCategories) > 0) {
            $this->suggestIconByCategory();
        }
    }

    public function suggestIconByCategory()
    {
        if (empty($this->selectedCategories)) {
            $this->suggestedIcon = null;
            return;
        }

        // Шукаємо іконку для кожної категорії по черзі
        foreach ($this->selectedCategories as $categoryId) {
            $suggested = MapIcon::where('category_id', $categoryId)->first();
            
            if ($suggested) {
                $this->suggestedIcon = $suggested;
                return;
            }
        }
        
        // Якщо немає іконки для конкретної категорії, беремо першу іконку для атракцій
        $this->suggestedIcon = MapIcon::whereNotNull('category_id')->first();
    }

    public function useSuggestedIcon()
    {
        if ($this->suggestedIcon) {
            $this->mapIcon = $this->suggestedIcon->icon_url;
        }
    }

    public function submit()
    {
        $this->authorize(
            $this->attraction->exists ? 'update' : 'create',
            $this->attraction->exists ? $this->attraction : Attraction::class
        );

        $this->validate();

        $this->attraction->fill([
            'name' => $this->name,
            'location' => $this->location,
            'description' => $this->description,
            'opening_time' => $this->opening_time,
            'closing_time' => $this->closing_time,
            'map_icon' => $this->mapIcon,

            // 📍 Zapis współrzędnych
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ])->save();

        // Kategorie
        $this->attraction->categories()->sync($this->selectedCategories);

        // Nowe zdjęcia
        foreach ($this->photos as $photo) {
            $path = $photo->store('images/attractions', 'public');

            AttractionPhoto::create([
                'attraction_id' => $this->attraction->id,
                'path' => 'storage/' . $path,
            ]);
        }

        $this->photos = [];

        // Usuwanie zdjęć
        foreach ($this->photosToDelete as $photoId) {
            $photo = AttractionPhoto::find($photoId);

            if ($photo) {
                Storage::disk('public')->delete(
                    str_replace('storage/', '', $photo->path)
                );
                $photo->delete();
            }
        }

        flash(
            $this->attraction->wasRecentlyCreated
                ? 'Atrakcja została dodana.'
                : 'Atrakcja została zaktualizowana.',
            '',
            'success'
        );

        return redirect()->route('attractions.index');
    }

    public function deletePhoto(int $id)
    {
        $photo = AttractionPhoto::findOrFail($id);
        $this->authorize('delete', $photo);

        if (!in_array($id, $this->photosToDelete)) {
            $this->photosToDelete[] = $id;
        }
    }

    public function removePhoto($index)
    {
        unset($this->photos[$index]);
        $this->photos = array_values($this->photos);
    }
    
    public function render()
    {
        return view('livewire.attractions.attraction-form');
    }
}