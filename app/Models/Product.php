<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'farmer_id', 'market_id', 'category_id', 'name', 'description', 'price', 'unit',
        'stock_quantity', 'image', 'is_available', 'is_sold_out', 'is_featured', 'views_count',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_available' => 'boolean',
            'is_sold_out' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    public function farmer(): BelongsTo
    {
        return $this->belongsTo(FarmerProfile::class, 'farmer_id');
    }

    public function market(): BelongsTo
    {
        return $this->belongsTo(Market::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function averageRating(): float
    {
        return round((float) $this->reviews()->where('status', 'approved')->avg('rating'), 1);
    }

    public function canPurchase(): bool
    {
        return $this->is_available && ! $this->is_sold_out && $this->stock_quantity > 0
            && $this->farmer && $this->farmer->approval_status === 'approved';
    }
}
