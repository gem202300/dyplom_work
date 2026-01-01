<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NoclegPhoto extends Model
{
    protected $fillable = [
        'nocleg_id',
        'path'
    ];

    public function nocleg()
    {
        return $this->belongsTo(Nocleg::class);
    }
}
