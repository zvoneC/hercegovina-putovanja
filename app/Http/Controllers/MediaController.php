<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function show(string $file)
    {
        abort_unless(preg_match('/^[a-zA-Z0-9]+\.(jpg|jpeg|png|webp|pdf)$/', $file), 404);
        $path = 'uploads/'.$file;
        abort_unless(Storage::disk('public')->exists($path), 404);
        $headers = ['X-Content-Type-Options' => 'nosniff', 'Content-Security-Policy' => "default-src 'none'; sandbox"];

        return str_ends_with($file, '.pdf') ? Storage::disk('public')->download($path, 'brosura.pdf', $headers) : response()->file(Storage::disk('public')->path($path), $headers);
    }
}
