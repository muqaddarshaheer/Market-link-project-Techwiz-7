<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FarmerLand extends Model
{
    protected $fillable = [
        'farmer_id',
        'name',
        'area_amount',
        'area_unit',
        'crop_name',
        'crop_key',
        'planted_on',
        'expected_harvest',
        'stage',
        'soil_type',
        'notes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'area_amount' => 'decimal:2',
            'planted_on' => 'date',
            'expected_harvest' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function farmer(): BelongsTo
    {
        return $this->belongsTo(FarmerProfile::class, 'farmer_id');
    }

    public function stageLabel(): string
    {
        return match ($this->stage) {
            'planted' => 'Just planted',
            'growing' => 'Growing',
            'flowering' => 'Flowering',
            'ready' => 'Ready to harvest',
            'harvested' => 'Harvested',
            default => 'Empty land',
        };
    }

    public function stageLabelUr(): string
    {
        return match ($this->stage) {
            'planted' => 'ابھی لگائی',
            'growing' => 'بڑھ رہی ہے',
            'flowering' => 'پھول آ رہے ہیں',
            'ready' => 'کٹائی تیار',
            'harvested' => 'کاٹ لی',
            default => 'خالی زمین',
        };
    }

    public function areaLabel(): string
    {
        if ($this->area_amount === null) {
            return '—';
        }

        return rtrim(rtrim(number_format((float) $this->area_amount, 2), '0'), '.').' '.$this->area_unit;
    }

    public function daysPlanted(): ?int
    {
        if (! $this->planted_on) {
            return null;
        }

        return max(0, $this->planted_on->diffInDays(now()));
    }

    public function daysToHarvest(): ?int
    {
        if (! $this->expected_harvest) {
            return null;
        }

        return (int) now()->startOfDay()->diffInDays($this->expected_harvest->startOfDay(), false);
    }

    public function careTip(): string
    {
        if ($this->stage === 'empty' || ! $this->crop_name) {
            return 'Choose a crop and planting date for this land.';
        }

        $days = $this->daysPlanted() ?? 0;

        return match ($this->stage) {
            'planted' => $days <= 7
                ? 'Keep soil moist; protect new plants from harsh sun.'
                : 'Check germination; thin weak seedlings if crowded.',
            'growing' => 'Weed lightly and water deep, not too often.',
            'flowering' => 'Avoid heavy fertilizer; watch pests on flowers.',
            'ready' => 'Harvest in cool hours; pack for market same day.',
            'harvested' => 'Rest soil or plan the next crop soon.',
            default => 'Update crop stage as the field changes.',
        };
    }

    public function careTipUr(): string
    {
        if ($this->stage === 'empty' || ! $this->crop_name) {
            return 'اس زمین کے لیے فصل اور بوائی کی تاریخ لکھیں۔';
        }

        $days = $this->daysPlanted() ?? 0;

        return match ($this->stage) {
            'planted' => $days <= 7
                ? 'مٹی نم رکھیں؛ تیز دھوپ سے بچائیں۔'
                : 'اگاو دیکھیں؛ کمزور پودے الگ کریں۔',
            'growing' => 'ہلکی گوڈی کریں؛ گہرا پانی دیں، بار بار نہیں۔',
            'flowering' => 'بھاری کھاد نہ دیں؛ پھولوں پر کیڑے دیکھیں۔',
            'ready' => 'ٹھنڈے وقت کاٹیں؛ اسی دن منڈی کے لیے پیک کریں۔',
            'harvested' => 'زمین آرام دیں یا اگلی فصل کا منصوبہ بنائیں۔',
            default => 'فصل کی حالت اپڈیٹ کرتے رہیں۔',
        };
    }

    public static function suggestHarvestDate(?Carbon $planted, ?string $cropKey): ?Carbon
    {
        if (! $planted) {
            return null;
        }

        $days = match ($cropKey) {
            'potato' => 90,
            'wheat' => 120,
            'rice' => 110,
            'corn' => 95,
            'tomato' => 75,
            'onion' => 100,
            'carrot' => 80,
            'cucumber' => 55,
            'chili' => 90,
            'spinach' => 45,
            'eggplant' => 85,
            'broccoli' => 70,
            'mango', 'apple', 'kinnow', 'grapes' => 180,
            'watermelon' => 90,
            default => 75,
        };

        return $planted->copy()->addDays($days);
    }
}
