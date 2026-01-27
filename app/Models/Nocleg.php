<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Nocleg extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'status',
        'title',
        'latitude',
        'longitude',
        'description',
        'capacity',
        'city',
        'street',
        'location',
        'contact_phone',
        'link',
        'object_type_id',
        'reject_reason',
        'has_kitchen',
        'has_parking',
        'has_bathroom',
        'has_wifi',
        'has_tv',
        'has_balcony',
        'map_icon',
        'amenities_other',
    ];

    public function photos()
    {
        return $this->hasMany(NoclegPhoto::class);
    }
    public function ratings()
    {
        return $this->morphMany(Rating::class, 'rateable');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function availabilities()
    {
        return $this->hasMany(NoclegAvailability::class);
    }
    public function objectType()
    {
        return $this->belongsTo(ObjectType::class);
    }
    public function availabilityForDate($date)
    {
        return $this->availabilities()->where('date', $date)->first();
    }

    public function average_rating()
    {
        return $this->ratings()->avg('rating');
    }
    public function getAverageRatingAttribute()
    {
        return $this->ratings()->whereNotNull('rating')->avg('rating');
    }
}
