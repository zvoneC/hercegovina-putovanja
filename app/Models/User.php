<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;

    protected $fillable = ['name', 'username', 'email', 'password', 'role', 'active'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return ['password' => 'hashed', 'active' => 'boolean'];
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function tripItems()
    {
        return $this->hasMany(TripItem::class);
    }

    public function hasPermission(string $permission): bool
    {
        return $this->active && $this->roles()->whereHas('permissions', fn ($query) => $query->where('name', $permission))->exists();
    }

    public function assignRole(string $role): void
    {
        // ENUM služi za prikaz uloge, a povezane tablice određuju dozvole.
        $this->update(['role' => $role]);
        $this->roles()->sync([Role::where('name', $role)->firstOrFail()->id]);
    }
}
