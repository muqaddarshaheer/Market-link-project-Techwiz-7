<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Market extends Model
{
    protected $fillable = [
        'name', 'address', 'city', 'operating_days', 'opening_time', 'closing_time',
        'latitude', 'longitude', 'map_provider', 'status', 'image',
    ];

    protected function casts(): array
    {
        return [
            'operating_days' => 'array',
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    public function farmers(): BelongsToMany
    {
        return $this->belongsToMany(FarmerProfile::class, 'farmer_markets', 'market_id', 'farmer_id')
            ->withPivot(['stall_number', 'operating_day', 'pickup_notes'])
            ->withTimestamps();
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function hoursLabel(): string
    {
        return substr((string) $this->opening_time, 0, 5).' – '.substr((string) $this->closing_time, 0, 5);
    }
}
