<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'farmer_id', 'market_id', 'category_id', 'name', 'description', 'price',
        'unit', 'stock_quantity', 'image', 'is_available', 'is_sold_out',
        'is_featured', 'views_count', 'weekly_stock_template',
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

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true)
            ->where('is_sold_out', false)
            ->where('stock_quantity', '>', 0);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function isInStock(): bool
    {
        return $this->is_available && ! $this->is_sold_out && $this->stock_quantity > 0;
    }

    public function averageRating(): float
    {
        return (float) $this->reviews()->where('status', 'approved')->avg('rating') ?: 0;
    }

    public function imageUrl(): string
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }

        return asset('images/product-placeholder.svg');
    }

    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    public function syncStockFlags(): void
    {
        if ($this->stock_quantity <= 0) {
            $this->update(['is_sold_out' => true, 'is_available' => false]);
        }
    }
}
