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

    public static function picture(?string $stored, string $name): string
    {
        if ($stored) {
            return self::url($stored);
        }

        $key = strtolower($name);
        foreach (self::catalog() as $needle => $file) {
            if (str_contains($key, $needle) && is_file(public_path('images/produce/'.$file))) {
                return asset('images/produce/'.$file);
            }
        }

        return asset('images/placeholder.svg');
    }

    private static function catalog(): array
    {
        return [
            'cherry tomato' => 'cherry-tomatoes.jpg',
            'rainbow chard' => 'rainbow-chard.jpg',
            'potato' => 'potatoes.jpg',
            'sweet corn' => 'sweet-corn.jpg',
            'peach' => 'peaches.jpg',
            'blueberr' => 'blueberries.jpg',
            'goat cheese' => 'goat-cheese.jpg',
            'milk' => 'milk.jpg',
            'sourdough' => 'sourdough.jpg',
            'cinnamon' => 'cinnamon-rolls.jpg',
            'basil' => 'basil.jpg',
            'mint' => 'mint.jpg',
            'rosemary' => 'rosemary.jpg',
            'duck egg' => 'duck-eggs.jpg',
            'egg' => 'eggs.jpg',
            'clover honey' => 'clover-honey.jpg',
            'wildflower' => 'honey.jpg',
            'honey' => 'honey.jpg',
            'salad' => 'salad.jpg',
            'riverside' => 'market.jpg',
            'downtown' => 'market.jpg',
            'eastside' => 'field.jpg',
            'oak hill' => 'farm.jpg',
            'green row' => 'farm.jpg',
            'ortega' => 'honey-stall.jpg',
            'shah' => 'herbs.jpg',
        ];
    }

    public static function name(string $prefix): string
    {
        return $prefix.'-'.Str::uuid();
    }
}
