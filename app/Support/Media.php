<?php

namespace App\Support;

class Media
{
    public static function url(?string $path): string
    {
        if (! $path) {
            return asset('images/placeholder.svg');
        }

        return str_starts_with($path, 'uploads/') ? route('media', basename($path)) : asset($path);
    }
}
