<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NoclegAvailability extends Model
{
    protected $fillable = [
        'nocleg_id',
        'date',
        'available_capacity',
        'is_blocked',
    ];

    public function nocleg()
    {
        return $this->belongsTo(Nocleg::class);
    }
}
