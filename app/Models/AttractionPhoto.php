<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AttractionPhoto extends Model
{
    use HasFactory;
    public function attraction()
    {
        return $this->belongsTo(Attraction::class);
    }

}
