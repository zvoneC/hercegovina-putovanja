<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    protected $fillable = ['city_id', 'category_id', 'title', 'description', 'image', 'brochure', 'price', 'duration', 'active'];

    protected function casts(): array
    {
        return ['price' => 'decimal:2', 'active' => 'boolean'];
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function scopeFiltered($query, array $filters)
    {
        return $query->where('active', true)
            ->when($filters['q'] ?? null, fn ($q, $text) => $q->where(function ($q) use ($text) {
                $q->where('title', 'like', '%'.$text.'%')->orWhereHas('city', fn ($q) => $q->where('name', 'like', '%'.$text.'%'));
            }))
            ->when($filters['city'] ?? null, fn ($q, $id) => $q->where('city_id', $id))
            ->when($filters['category'] ?? null, fn ($q, $id) => $q->where('category_id', $id))
            ->when(isset($filters['price']) && $filters['price'] !== '', fn ($q) => $q->where('price', '<=', $filters['price']))
            ->when($filters['duration'] ?? null, fn ($q, $hours) => $q->where('duration', '<=', $hours));
    }
}
