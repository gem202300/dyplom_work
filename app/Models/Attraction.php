<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attraction extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'location', 'description', 'opening_time', 'closing_time'];
    
    public function photos()
    {
        return $this->hasMany(AttractionPhoto::class);
    }
    
    public function dataSource(): \Illuminate\Database\Eloquent\Builder
    {
        return Attraction::query()
            ->with('photos')
            ->withAvg('ratings', 'rating');
    }

    public function ratings()
    {
        return $this->morphMany(\App\Models\Rating::class, 'rateable');
    }
    
    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }
    

    public function getAverageRatingAttribute()
    {
        return $this->ratings()->whereNotNull('rating')->avg('rating');
    }

}
