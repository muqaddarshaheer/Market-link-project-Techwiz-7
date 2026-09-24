<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FarmerProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'stall_name', 'business_description', 'contact_person',
        'operating_days', 'address', 'latitude', 'longitude',
        'approval_status', 'logo', 'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'operating_days' => 'array',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function markets(): BelongsToMany
    {
        return $this->belongsToMany(Market::class, 'farmer_markets', 'farmer_id', 'market_id')
            ->withPivot(['stall_number', 'operating_day', 'pickup_notes'])
            ->withTimestamps();
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

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class, 'farmer_id');
    }

    public function isApproved(): bool
    {
        return $this->approval_status === 'approved';
    }

    public function averageRating(): float
    {
        return (float) $this->reviews()->where('status', 'approved')->avg('rating') ?: 0;
    }

    public function logoUrl(): string
    {
        if ($this->logo) {
            return asset('storage/' . $this->logo);
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->stall_name) . '&background=40916c&color=fff';
    }

    public function scopeApproved($query)
    {
        return $query->where('approval_status', 'approved');
    }
}
