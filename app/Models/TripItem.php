<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TripItem extends Model
{
    protected $fillable = ['user_id', 'offer_id', 'visit_date', 'persons', 'note'];

    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
