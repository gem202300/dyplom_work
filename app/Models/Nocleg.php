<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nocleg extends Model
{
    protected $fillable = [
        'user_id',
        'status',
        'title',
        'description',
        'capacity',
        'object_type',
        'city',
        'street',
        'location',
        'contact_phone',
        'link',

        'has_kitchen',
        'has_parking',
        'has_bathroom',
        'has_wifi',
        'has_tv',
        'has_balcony',

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

    public function average_rating()
    {
        return $this->ratings()->avg('rating');
    }
    public function getAverageRatingAttribute()
    {
        return $this->ratings()->whereNotNull('rating')->avg('rating');
    }
}
