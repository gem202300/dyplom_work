<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RatingReport extends Model
{
    use HasFactory;

    protected $fillable = ['rating_id', 'user_id', 'reason'];

    public function rating()
    {
        return $this->belongsTo(Rating::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
