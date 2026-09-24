<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = ['customer_id'];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function totalAmount(): float
    {
        return (float) $this->items->sum(function (CartItem $item) {
            return $item->quantity * ($item->product->price ?? 0);
        });
    }

    public function itemCount(): int
    {
        return (int) $this->items->sum('quantity');
    }
}
