<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MapIcon extends Model
{
    protected $fillable = ['name', 'icon_url', 'category_id'];
    
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}