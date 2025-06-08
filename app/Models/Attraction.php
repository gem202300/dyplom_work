<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attraction extends Model
{
    use HasFactory;
    public function photos()
    {
        return $this->hasMany(AttractionPhoto::class);
    }
    
    public function dataSource(): \Illuminate\Database\Eloquent\Builder
    {
        return Attraction::query()->with('photos');
    }

}
