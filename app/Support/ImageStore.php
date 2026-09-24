<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageStore
{
    public static function put(?UploadedFile $file, string $folder, ?string $existing = null): ?string
    {
        if (! $file) {
            return $existing;
        }

        if ($existing && ! str_starts_with($existing, 'images/') && Storage::disk('public')->exists($existing)) {
            Storage::disk('public')->delete($existing);
        }

        return $file->store($folder, 'public');
    }

    public static function url(?string $path, string $fallback = 'images/placeholder.svg'): string
    {
        if (! $path) {
            return asset($fallback);
        }
        if (str_starts_with($path, 'images/') || str_starts_with($path, 'http')) {
            return str_starts_with($path, 'http') ? $path : asset($path);
        }

        return asset('storage/'.$path);
    }

    public static function name(string $prefix): string
    {
        return $prefix.'-'.Str::uuid();
    }
}
