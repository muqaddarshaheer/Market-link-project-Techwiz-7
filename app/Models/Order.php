<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'order_number', 'customer_id', 'farmer_id', 'market_id', 'pickup_date', 'pickup_slot',
        'status', 'total_amount', 'customer_note', 'cutoff_time', 'farmer_notes',
    ];

    protected function casts(): array
    {
        return [
            'pickup_date' => 'date',
            'cutoff_time' => 'datetime',
            'total_amount' => 'decimal:2',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function farmer(): BelongsTo
    {
        return $this->belongsTo(FarmerProfile::class, 'farmer_id');
    }

    public function market(): BelongsTo
    {
        return $this->belongsTo(Market::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function isOpenForChange(): bool
    {
        return in_array($this->status, ['placed', 'accepted'], true)
            && $this->cutoff_time
            && now()->lt($this->cutoff_time);
    }

    public static function statusSteps(): array
    {
        return ['placed', 'accepted', 'ready_for_pickup', 'completed'];
    }
}
