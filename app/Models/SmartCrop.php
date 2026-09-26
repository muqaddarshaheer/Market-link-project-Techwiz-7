<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SmartCrop extends Model
{
    protected $fillable = [
        'slug',
        'name_en',
        'name_ur',
        'emoji',
        'category',
        'is_active',
        'sort_order',
        'temp_min_c',
        'temp_max_c',
        'ideal_humidity_min',
        'ideal_humidity_max',
        'sensitive_to_rain',
        'details_en',
        'details_ur',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sensitive_to_rain' => 'boolean',
            'temp_min_c' => 'float',
            'temp_max_c' => 'float',
            'details_en' => 'array',
            'details_ur' => 'array',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name_en');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function localizedDetails(string $lang = 'en'): array
    {
        $details = $lang === 'ur' ? ($this->details_ur ?? []) : ($this->details_en ?? []);

        return is_array($details) ? $details : [];
    }

    public function toGuidePayload(?array $weather, array $advice): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'emoji' => $this->emoji,
            'category' => $this->category,
            'name' => [
                'en' => $this->name_en,
                'ur' => $this->name_ur,
            ],
            'details' => [
                'en' => $this->details_en ?? [],
                'ur' => $this->details_ur ?? [],
            ],
            'thresholds' => [
                'temp_min_c' => $this->temp_min_c,
                'temp_max_c' => $this->temp_max_c,
                'ideal_humidity_min' => $this->ideal_humidity_min,
                'ideal_humidity_max' => $this->ideal_humidity_max,
                'sensitive_to_rain' => $this->sensitive_to_rain,
            ],
            'weather' => $weather,
            'advice' => $advice,
        ];
    }
}
