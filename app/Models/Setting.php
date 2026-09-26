<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function getValue(string $key, ?string $default = null): ?string
    {
        $all = Cache::remember('settings.all', 300, fn () => self::query()->pluck('value', 'key')->all());

        return $all[$key] ?? $default;
    }

    public static function putMany(array $pairs): void
    {
        foreach ($pairs as $key => $value) {
            self::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        }
        Cache::forget('settings.all');
    }
}
