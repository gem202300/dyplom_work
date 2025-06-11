<?php

namespace App\Livewire\Attractions;

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
    use AuthorizesRequests, WireUiActions, WithFileUploads;

    public ?Attraction $attraction = null;

    public $name = '';
    public $location = '';
    public $description = '';
    public $opening_time = '';
    public $closing_time = '';
    public $photos = [];
    public $selectedCategories = [];
    public $allCategories = [];
    public $photosToDelete = [];
    
    public function mount(Attraction $attraction = null)
    {
    $this->attraction = $attraction ?? new Attraction();

        $this->attraction = $attraction;
        $this->allCategories = Category::all();

        if ($attraction->exists) {
            $this->name = $attraction->name;
            $this->location = $attraction->location;
            $this->description = $attraction->description;
            $this->opening_time = $attraction->opening_time ? \Carbon\Carbon::parse($attraction->opening_time)->format('H:i') : '';
            $this->closing_time = $attraction->closing_time ? \Carbon\Carbon::parse($attraction->closing_time)->format('H:i') : '';
            $this->selectedCategories = $attraction->categories->pluck('id')->toArray();
        }
    }

    public function rules()
    {
        return [
        'name' => 'required|string|max:255',
        'location' => 'required|string',
        'description' => 'nullable|string',
        'opening_time' => 'nullable|date_format:H:i',
        'closing_time' => 'nullable|date_format:H:i',
        'selectedCategories' => 'array',
        'photos.*' => 'image|max:2048',
        ];
    }

    public function submit()
    {
        $this->authorize($this->attraction->exists ? 'update' : 'create', $this->attraction ?: Attraction::class);

        $this->validate();

        $this->attraction->fill([
            'name' => $this->name,
            'location' => $this->location,
            'description' => $this->description,
            'opening_time' => $this->opening_time,
            'closing_time' => $this->closing_time,
        ])->save();

        $this->attraction->categories()->sync($this->selectedCategories);

        foreach ($this->photos as $photo) {
            $path = $photo->store('images/attractions', 'public');
            AttractionPhoto::create([
                'attraction_id' => $this->attraction->id,
                'path' => 'storage/' . $path,
            ]);
        }

        $this->photos = [];

        flash(
            $this->attraction->wasRecentlyCreated ? 'Atrakcja została dodana.' : 'Atrakcja została zaktualizowana.',
            '',
            'success'
        );
        foreach ($this->photosToDelete as $photoId) {
            $photo = AttractionPhoto::find($photoId);
            if ($photo) {
                Storage::disk('public')->delete(str_replace('storage/', '', $photo->path));
                $photo->delete();
            }
        }


        return redirect()->route('attractions.index');
    }

    public function deletePhoto($id)
    {
        $photo = AttractionPhoto::findOrFail($id);
        $this->authorize('delete', $photo);

        if (!in_array($id, $this->photosToDelete)) {
            $this->photosToDelete[] = $id;
        }

    }


    public function render()
    {
        return view('livewire.attractions.attraction-form');
    }
}
