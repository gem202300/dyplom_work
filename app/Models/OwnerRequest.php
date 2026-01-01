<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OwnerRequest extends Model
{
    protected $fillable = [
        'user_id',
        'phone',
        'reason',
        'accepted_terms',
        'status',
        'rejection_reason',
        'can_resubmit',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
