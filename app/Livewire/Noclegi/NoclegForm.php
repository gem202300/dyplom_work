<?php

namespace App\Livewire\Noclegi;

use App\Models\Nocleg;
use Livewire\Component;
use App\Models\ObjectType;
use App\Models\NoclegPhoto;
use Livewire\WithFileUploads;
use WireUi\Traits\WireUiActions;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class NoclegForm extends Component
{
    use AuthorizesRequests, WireUiActions, WithFileUploads;

    public ?Nocleg $nocleg = null;

    public $title = '';
    public $description = '';
    public $city = '';
    public $street = '';
    public ?int $object_type_id = null;
    public $objectTypes = [];
    public $capacity = '';
    public $contact_phone = '';
    public $link = '';
    public $amenities = [];       
    public $other_amenities = '';
    public $photos = [];
    public $photosToDelete = [];
    public $allAmenities = [
        'kuchnia' => 'Kuchnia 🍳',
        'parking' => 'Parking 🅿️',
        'lazienka' => 'Łazienka 🚿',
        'wifi' => 'Wi-Fi 📶',
        'tv' => 'Telewizor 📺',
        'balkon' => 'Balkon 🌅',
        'inne' => 'Inne'
    ];
    
    public function mount(Nocleg $nocleg = null)
    {
        $this->nocleg = $nocleg ?? new Nocleg();
        $this->objectTypes = ObjectType::orderBy('name')->get();
        if ($nocleg && $nocleg->exists) {

            $this->title = $nocleg->title;
            $this->description = $nocleg->description;
            $this->city = $nocleg->city;
            $this->street = $nocleg->street;
            $this->object_type_id = $nocleg->object_type_id;
            $this->capacity = $nocleg->capacity;
            $this->contact_phone = $nocleg->contact_phone;
            $this->link = $nocleg->link;

            $this->amenities = [];

            if ($nocleg->has_kitchen) $this->amenities[] = 'kuchnia';
            if ($nocleg->has_parking) $this->amenities[] = 'parking';
            if ($nocleg->has_bathroom) $this->amenities[] = 'lazienka';
            if ($nocleg->has_wifi) $this->amenities[] = 'wifi';
            if ($nocleg->has_tv) $this->amenities[] = 'tv';
            if ($nocleg->has_balcony) $this->amenities[] = 'balkon';
            if (!empty($nocleg->amenities_other)) $this->amenities[] = 'inne';

            $this->other_amenities = $nocleg->amenities_other ?? '';

        } else {
            $this->contact_phone = auth()->user()->phone ?? '';
        }
    }



    public function rules()
    {
        return [
            'title' => [
                'required',
                'regex:/^[\p{L} ]+$/u',
                'max:255'
            ],
            'description' => 'nullable|string',
            'city' => [
                'required',
                'regex:/^[\p{L} ]+$/u',
            ],
            'street' => 'required|string|max:255',
            'object_type_id' => 'required|exists:object_types,id',
            'capacity' => 'required|integer|min:1',
            'contact_phone' => [
                'nullable',
                'regex:/^\+?[0-9]{6,15}$/',
            ],
            'link' => 'nullable|url|max:255',
            'photos' => $this->nocleg->exists
                ? 'nullable|array'
                : 'required|array|min:1',

            'photos.*' => 'image|max:2048',
        ];
    }


    public function submit()
    {
         $this->validate();

        if (!$this->nocleg->exists) {
            $this->nocleg->user_id = auth()->id();
        }

        $this->nocleg->fill([
            'title' => $this->title,
            'description' => $this->description,
            'city' => $this->city,
            'street' => $this->street,
            'object_type_id' => $this->object_type_id,
            'capacity' => $this->capacity,
            'contact_phone' => $this->contact_phone,
            'link' => $this->link,
            'has_kitchen' => in_array('kuchnia', $this->amenities),
            'has_parking' => in_array('parking', $this->amenities),
            'has_bathroom' => in_array('lazienka', $this->amenities),
            'has_wifi' => in_array('wifi', $this->amenities),
            'has_tv' => in_array('tv', $this->amenities),
            'has_balcony' => in_array('balkon', $this->amenities),
            'amenities_other' => $this->other_amenities,
        ])->save();


        foreach ($this->photos as $photo) {
            $path = $photo->store('images/noclegi', 'public');
            NoclegPhoto::create([
                'nocleg_id' => $this->nocleg->id,
                'path' => 'storage/' . $path,
            ]);
        }

        foreach ($this->photosToDelete as $photoId) {
            $photo = NoclegPhoto::find($photoId);
            if ($photo) {
                Storage::disk('public')->delete(str_replace('storage/', '', $photo->path));
                $photo->delete();
            }
        }

        $this->photos = [];
        $this->photosToDelete = [];

        $this->notification()->success(
            $this->nocleg->wasRecentlyCreated ? 'Nocleg został dodany.' : 'Nocleg został zaktualizowany.'
        );

        return redirect()->route('noclegi.index');
    }

    public function deletePhoto($id)
    {
        if (!in_array($id, $this->photosToDelete)) {
            $this->photosToDelete[] = $id;
        }
    }

    public function render()
    {
        return view('livewire.noclegi.nocleg-form');
    }
}
