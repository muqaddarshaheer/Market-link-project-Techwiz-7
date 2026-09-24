<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FarmerProfile extends Model
{
    protected $fillable = [
        'user_id', 'stall_name', 'business_description', 'contact_person', 'operating_days',
        'address', 'latitude', 'longitude', 'approval_status', 'logo', 'pickup_slots',
        'cutoff_hours', 'weekly_stock_template',
    ];

    protected function casts(): array
    {
        return [
            'operating_days' => 'array',
            'pickup_slots' => 'array',
            'weekly_stock_template' => 'array',
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'farmer_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'farmer_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'farmer_id');
    }

    public function markets(): BelongsToMany
    {
        return $this->belongsToMany(Market::class, 'farmer_markets', 'farmer_id', 'market_id')
            ->withPivot(['stall_number', 'operating_day', 'pickup_notes'])
            ->withTimestamps();
    }

    public function averageRating(): float
    {
        return round((float) $this->reviews()->where('status', 'approved')->avg('rating'), 1);
    }

    public function isApproved(): bool
    {
        return $this->approval_status === 'approved';
    }

    public function slots(): array
    {
        return $this->pickup_slots ?: [
            ['label' => '08:00-10:00'],
            ['label' => '10:00-12:00'],
            ['label' => '12:00-14:00'],
        ];
    }
}
