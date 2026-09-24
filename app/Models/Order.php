<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    public const STATUSES = [
        'placed', 'accepted', 'declined', 'ready_for_pickup', 'completed', 'cancelled',
    ];

    protected $fillable = [
        'order_number', 'customer_id', 'farmer_id', 'market_id', 'pickup_date',
        'pickup_slot', 'status', 'total_amount', 'customer_note', 'cutoff_time', 'farmer_notes',
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

    public function canModify(): bool
    {
        return in_array($this->status, ['placed', 'accepted'], true)
            && $this->cutoff_time
            && now()->lt($this->cutoff_time);
    }

    public function canCancel(): bool
    {
        return $this->canModify();
    }

    public function canReview(): bool
    {
        return $this->status === 'completed'
            && ! $this->reviews()->where('customer_id', $this->customer_id)->exists();
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'placed' => 'bg-info',
            'accepted' => 'bg-primary',
            'declined' => 'bg-danger',
            'ready_for_pickup' => 'bg-warning text-dark',
            'completed' => 'bg-success',
            'cancelled' => 'bg-secondary',
            default => 'bg-secondary',
        };
    }

    public function statusLabel(): string
    {
        return str_replace('_', ' ', ucfirst($this->status));
    }

    public static function generateOrderNumber(): string
    {
        return 'ML-' . now()->format('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }

    public function timelineSteps(): array
    {
        $flow = ['placed', 'accepted', 'ready_for_pickup', 'completed'];
        $currentIndex = array_search($this->status, $flow, true);

        if (in_array($this->status, ['declined', 'cancelled'], true)) {
            return [
                ['key' => 'placed', 'label' => 'Placed', 'done' => true],
                ['key' => $this->status, 'label' => $this->statusLabel(), 'done' => true, 'failed' => true],
            ];
        }

        return collect($flow)->map(function ($step, $index) use ($currentIndex) {
            return [
                'key' => $step,
                'label' => str_replace('_', ' ', ucfirst($step)),
                'done' => $currentIndex !== false && $index <= $currentIndex,
                'current' => $step === $this->status,
            ];
        })->all();
    }
}
