<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Rating extends Model
{
    use HasFactory;
    protected $fillable = [
        'rating', 'comment', 'user_id', 'rateable_id', 'rateable_type', 'is_flagged'
    ];
    
    public function rateable()
    {
        return $this->morphTo();
    }
    public function reports()
    {
        return $this->hasMany(RatingReport::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
