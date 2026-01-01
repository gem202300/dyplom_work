<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ObjectType extends Model
{
    use HasFactory;
    protected $fillable = ['name'];

    public function noclegs()
    {
        return $this->hasMany(Nocleg::class);
    }
}
