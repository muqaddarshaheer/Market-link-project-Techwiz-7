<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Market extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'address', 'city', 'operating_days', 'opening_time', 'closing_time',
        'latitude', 'longitude', 'map_provider', 'status', 'image', 'description',
    ];

    protected function casts(): array
    {
        return [
            'operating_days' => 'array',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
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

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function imageUrl(): string
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }

        return asset('images/market-placeholder.svg');
    }

    public function directionsUrl(): ?string
    {
        if ($this->latitude && $this->longitude) {
            return 'https://www.openstreetmap.org/directions?to=' . $this->latitude . '%2C' . $this->longitude;
        }

        return null;
    }
}
