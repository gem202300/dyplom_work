<?php

namespace App\Livewire\Noclegi;

use App\Models\Nocleg;
use App\Models\MapIcon;
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
    public ?string $reject_reason = null;
    public $title = '';
    public $description = '';
    public $city = '';
    public $street = '';
    public $latitude = null;
    public $longitude = null;
    public ?int $object_type_id = null;
    public $objectTypes = [];
    public $capacity = '';
    public $contact_phone = '';
    public $link = '';
    public $amenities = [];       
    public $other_amenities = '';
    public $photos = [];
    public $photosToDelete = [];
    
    public ?string $mapIcon = null;
    public $mapIcons = [];
    
    public $allAmenities = [
        'kuchnia' => 'Kuchnia 🍳',
        'parking' => 'Parking 🅿️',
        'lazienka' => 'Łazienka 🚿',
        'wifi' => 'Wi-Fi 📶',
        'tv' => 'Telewizor 📺',
        'balkon' => 'Balkon 🌅',
        'inne' => 'Inne'
    ];
    
    public function mount(Nocleg $nocleg)
    {
        $this->nocleg = $nocleg;
        $this->objectTypes = ObjectType::orderBy('name')->get();
        
        // Завантажуємо всі іконки, що підходять для ночлегів
        // Простіше: беремо всі іконки, а потім фільтруємо
        $this->mapIcons = MapIcon::where(function($query) {
            $query->whereNull('category_id') // Іконки без категорій
                  ->orWhere('category_id', 0); // Або з категорією 0
        })
        ->orderBy('name')
        ->get();

        if ($nocleg->exists) {
            $this->title = $nocleg->title;
            $this->description = $nocleg->description;
            $this->city = $nocleg->city;
            $this->street = $nocleg->street;
            $this->latitude = $nocleg->latitude;
            $this->longitude = $nocleg->longitude;
            $this->object_type_id = $nocleg->object_type_id;
            $this->capacity = $nocleg->capacity;
            $this->contact_phone = $nocleg->contact_phone;
            $this->link = $nocleg->link;
            $this->reject_reason = $nocleg->reject_reason;
            $this->mapIcon = $nocleg->map_icon;
            
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
        $rules = [
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
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
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
        
        // Додаємо валідацію іконки тільки якщо іконки завантажені
        if (!empty($this->mapIcons)) {
            $rules['mapIcon'] = 'required|string';
        }
        
        return $rules;
    }

    public function submit()
    {
        $this->validate();

        if (!$this->nocleg->exists) {
            $this->nocleg->user_id = auth()->id();
        }
        if ($this->nocleg->exists && $this->nocleg->status === 'rejected') {
            $this->nocleg->status = 'pending';
            $this->nocleg->reject_reason = null;
            $this->reject_reason = null;
        }

        $this->nocleg->fill([
            'title' => $this->title,
            'description' => $this->description,
            'city' => $this->city,
            'street' => $this->street,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'object_type_id' => $this->object_type_id,
            'capacity' => $this->capacity,
            'contact_phone' => $this->contact_phone,
            'link' => $this->link,
            'map_icon' => $this->mapIcon,
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

    public function removePhoto($index)
    {
        unset($this->photos[$index]);
        $this->photos = array_values($this->photos);
    }

    public function deletePhoto($id)
    {
        if (!in_array($id, $this->photosToDelete)) {
            $this->photosToDelete[] = $id;
        }
    }
    
    // Метод для вибору іконки за типом об'єкту
    public function suggestIconByType()
    {
        $type = ObjectType::find($this->object_type_id);
        
        if (!$type || empty($this->mapIcons)) {
            return;
        }
        
        $typeName = strtolower($type->name);
        
        // Автоматичний вибір іконки на основі типу
        foreach ($this->mapIcons as $icon) {
            $iconName = strtolower($icon->name);
            
            if (str_contains($typeName, 'hotel') && str_contains($iconName, 'hotel')) {
                $this->mapIcon = $icon->icon_url;
                break;
            }
            if (str_contains($typeName, 'apart') && str_contains($iconName, 'apart')) {
                $this->mapIcon = $icon->icon_url;
                break;
            }
            if (str_contains($typeName, 'dom') && str_contains($iconName, 'dom')) {
                $this->mapIcon = $icon->icon_url;
                break;
            }
            if (str_contains($typeName, 'hostel') && str_contains($iconName, 'hostel')) {
                $this->mapIcon = $icon->icon_url;
                break;
            }
        }
        
        if ($this->mapIcon) {
            $this->notification()->info(
                'Ikona została wybrana automatycznie na podstawie typu obiektu.'
            );
        }
    }

    public function render()
    {
        return view('livewire.noclegi.nocleg-form');
    }
}