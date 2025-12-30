<?php

namespace App\Livewire\Attractions;

use Carbon\Carbon;
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

    // Основні поля
    public $name = '';
    public $location = '';
    public $description = '';
    public $opening_time = '';
    public $closing_time = '';

    // 📍 Координати
    public $latitude = null;
    public $longitude = null;

    // Категорії
    public $selectedCategories = [];
    public $allCategories = [];

    // Фото
    public $photos = [];
    public $photosToDelete = [];

    public function mount(Attraction $attraction = null)
    {
        $this->attraction = $attraction ?? new Attraction();
        $this->allCategories = Category::all();

        if ($this->attraction->exists) {
            $this->name = $this->attraction->name;
            $this->location = $this->attraction->location;
            $this->description = $this->attraction->description;

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

            'opening_time' => 'nullable|date_format:H:i',
            'closing_time' => 'nullable|date_format:H:i',

            // 📍 Координати
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',

            'selectedCategories' => 'array',
            'selectedCategories.*' => 'exists:categories,id',

            'photos' => 'array',
            'photos.*' => 'image|max:2048',
        ];
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

            // 📍 Збереження координат
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ])->save();

        // Категорії
        $this->attraction->categories()->sync($this->selectedCategories);

        // Нові фото
        foreach ($this->photos as $photo) {
            $path = $photo->store('images/attractions', 'public');

            AttractionPhoto::create([
                'attraction_id' => $this->attraction->id,
                'path' => 'storage/' . $path,
            ]);
        }

        $this->photos = [];

        // Видалення фото
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
// Додайте цей метод до класу AttractionForm
public function removePhoto($index)
{
    unset($this->photos[$index]);
    $this->photos = array_values($this->photos); // Переіндексувати масив
}
    public function render()
    {
        return view('livewire.attractions.attraction-form');
    }
}
