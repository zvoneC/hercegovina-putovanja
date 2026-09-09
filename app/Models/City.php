<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = ['name', 'description', 'image', 'latitude', 'longitude'];

    public function offers()
    {
        return $this->hasMany(Offer::class);
    }
}
