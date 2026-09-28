<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FarmerExpense extends Model
{
    protected $fillable = [
        'farmer_id',
        'title',
        'category',
        'amount',
        'expense_date',
        'crop_name',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'expense_date' => 'date',
        ];
    }

    public function farmer(): BelongsTo
    {
        return $this->belongsTo(FarmerProfile::class, 'farmer_id');
    }

    public static function categories(): array
    {
        return [
            'seed' => 'Seed',
            'fertilizer' => 'Fertilizer',
            'water' => 'Water / irrigation',
            'labor' => 'Labor',
            'transport' => 'Transport',
            'fuel' => 'Fuel',
            'pesticide' => 'Pesticide',
            'tools' => 'Tools / repair',
            'market' => 'Market fees',
            'other' => 'Other',
        ];
    }

    public function categoryLabel(): string
    {
        return self::categories()[$this->category] ?? ucfirst((string) $this->category);
    }
}
