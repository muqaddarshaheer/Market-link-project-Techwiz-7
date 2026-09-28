<?php

namespace App\Services;

use App\Models\SmartCrop;

class SmartCropAdviceService
{
    public function forCrop(SmartCrop $crop, ?array $weather): array
    {
        if (! $weather) {
            return $this->emptyAdvice();
        }

        $temp = $this->num($weather['temp'] ?? $weather['temp_max'] ?? null);
        $tmax = $this->num($weather['temp_max'] ?? $temp);
        $humidity = $this->num($weather['humidity'] ?? null);
        $rainMm = (float) ($weather['rain_mm'] ?? 0);
        $rainChance = (int) ($weather['rain_chance'] ?? 0);
        $wind = (float) ($weather['wind'] ?? 0);
        $code = (int) ($weather['code'] ?? 0);

        $cropMin = $this->num($crop->temp_min_c);
        $cropMax = $this->num($crop->temp_max_c);
        $rainSensitive = (bool) $crop->sensitive_to_rain;

        $rainLikely = $code >= 61 || $rainMm >= 3 || $rainChance >= 55;
        $heavyRain = $code >= 80 || $rainMm >= 10 || $rainChance >= 75;
        $storm = $code >= 95;
        $hot = ($tmax !== null && $tmax >= 36) || ($cropMax !== null && $temp !== null && $temp > $cropMax);
        $cold = ($cropMin !== null && $temp !== null && $temp < $cropMin) || ($temp !== null && $temp <= 8);
        $dry = ! $rainLikely && $rainChance < 30 && $rainMm < 1;
        $humid = $humidity !== null && $humidity >= 80;
        $windy = $wind >= 35;

        $watering = $this->wateringDecision($rainLikely, $heavyRain, $storm, $hot, $dry, $rainSensitive);
        $headline = $this->headline($storm, $heavyRain, $rainLikely, $hot, $cold, $dry, $windy);
        $actions = $this->actions($crop, $watering, $storm, $heavyRain, $rainLikely, $hot, $cold, $dry, $humid, $windy);

        return [
            'headline' => $headline,
            'watering' => $watering,
            'today' => [
                'do' => $actions['do'],
                'careful' => $actions['careful'],
                'avoid' => $actions['avoid'],
            ],
            'summary' => [
                'en' => $this->summaryBlock('en', $crop, $weather, $watering, $headline),
                'ur' => $this->summaryBlock('ur', $crop, $weather, $watering, $headline),
            ],
            'meta' => [
                'rain_likely' => $rainLikely,
                'heavy_rain' => $heavyRain,
                'hot' => $hot,
                'cold' => $cold,
                'dry' => $dry,
            ],
        ];
    }

    private function wateringDecision(bool $rainLikely, bool $heavyRain, bool $storm, bool $hot, bool $dry, bool $rainSensitive): array
    {
        if ($storm || $heavyRain || ($rainLikely && $rainSensitive)) {
            return [
                'status' => 'no',
                'icon' => '❌',
                'en' => [
                    'label' => 'Do not irrigate today',
                    'reason' => 'Rain is expected and extra irrigation may cause overwatering or root stress.',
                ],
                'ur' => [
                    'label' => 'آج پانی نہ دیں',
                    'reason' => 'بارش متوقع ہے؛ اضافی آبپاشی سے جڑیں خراب ہو سکتی ہیں یا زیادہ نمی ہو جائے گی۔',
                ],
            ];
        }

        if ($hot && $dry) {
            return [
                'status' => 'yes',
                'icon' => '✅',
                'en' => [
                    'label' => 'Irrigation may be required',
                    'reason' => 'Hot and dry conditions — follow this crop’s normal irrigation range and check soil moisture first.',
                ],
                'ur' => [
                    'label' => 'آبپاشی کی ضرورت ہو سکتی ہے',
                    'reason' => 'گرم اور خشک موسم ہے — فصل کی عام آبپاشی کی حد پر عمل کریں اور پہلے مٹی کی نمی چیک کریں۔',
                ],
            ];
        }

        if ($dry) {
            return [
                'status' => 'yes',
                'icon' => '✅',
                'en' => [
                    'label' => 'Normal irrigation OK',
                    'reason' => 'Little rain expected — water according to the crop schedule if the top soil feels dry.',
                ],
                'ur' => [
                    'label' => 'عام آبپاشی ٹھیک ہے',
                    'reason' => 'بارش کم متوقع ہے — اگر اوپری مٹی خشک لگے تو شیڈول کے مطابق پانی دیں۔',
                ],
            ];
        }

        return [
            'status' => 'careful',
            'icon' => '⚠️',
            'en' => [
                'label' => 'Irrigate carefully',
                'reason' => 'Mixed conditions — reduce volume and only water if soil moisture is clearly low.',
            ],
            'ur' => [
                'label' => 'احتیاط سے آبپاشی کریں',
                'reason' => 'موسم ملا جلا ہے — مقدار کم رکھیں اور صرف مٹی خشک ہونے پر پانی دیں۔',
            ],
        ];
    }

    private function headline(bool $storm, bool $heavyRain, bool $rainLikely, bool $hot, bool $cold, bool $dry, bool $windy): array
    {
        if ($storm) {
            return ['en' => 'Storm risk today', 'ur' => 'آج طوفان کا خطرہ', 'icon' => '⛈️'];
        }
        if ($heavyRain) {
            return ['en' => 'Heavy rain expected', 'ur' => 'تیز بارش متوقع', 'icon' => '🌧️'];
        }
        if ($rainLikely) {
            return ['en' => 'Rain expected today', 'ur' => 'آج بارش متوقع', 'icon' => '🌧️'];
        }
        if ($hot && $dry) {
            return ['en' => 'Hot & dry weather', 'ur' => 'گرم اور خشک موسم', 'icon' => '☀️'];
        }
        if ($hot) {
            return ['en' => 'Hot weather', 'ur' => 'گرم موسم', 'icon' => '🌡️'];
        }
        if ($cold) {
            return ['en' => 'Cool / cold stress risk', 'ur' => 'ٹھنڈ / سردی کا خطرہ', 'icon' => '❄️'];
        }
        if ($windy) {
            return ['en' => 'Windy conditions', 'ur' => 'تیز ہوا کا موسم', 'icon' => '💨'];
        }
        if ($dry) {
            return ['en' => 'Dry & mild weather', 'ur' => 'خشک اور معتدل موسم', 'icon' => '🌤️'];
        }

        return ['en' => 'Fair farming weather', 'ur' => 'کھیتی کے لیے مناسب موسم', 'icon' => '🌤️'];
    }

    private function actions(
        SmartCrop $crop,
        array $watering,
        bool $storm,
        bool $heavyRain,
        bool $rainLikely,
        bool $hot,
        bool $cold,
        bool $dry,
        bool $humid,
        bool $windy
    ): array {
        $doEn = [];
        $doUr = [];
        $careEn = [];
        $careUr = [];
        $avoidEn = [];
        $avoidUr = [];

        if ($watering['status'] === 'yes') {
            $doEn[] = 'Check soil moisture and irrigate if the top layer is dry.';
            $doUr[] = 'مٹی کی نمی چیک کریں؛ اوپری تہہ خشک ہو تو آبپاشی کریں۔';
            $doEn[] = 'Water early morning or late evening.';
            $doUr[] = 'صبح جلدی یا شام کو پانی دیں۔';
        } else {
            $doEn[] = 'Skip irrigation and let rainfall / residual moisture work.';
            $doUr[] = 'آبپاشی چھوڑ دیں؛ بارش یا موجودہ نمی کافی ہو سکتی ہے۔';
        }

        $doEn[] = 'Walk the field and note leaf color, wilting, or standing water.';
        $doUr[] = 'کھیت کا چکر لگائیں — پتے، مرجھانا، یا کھڑا پانی نوٹ کریں۔';

        if ($hot) {
            $careEn[] = 'Shade tender seedlings; harvest in cooler hours.';
            $careUr[] = 'نازک پودوں پر سایہ رکھیں؛ ٹھنڈے وقت میں کٹائی کریں۔';
        }
        if ($cold) {
            $careEn[] = 'Protect young plants from chill overnight if possible.';
            $careUr[] = 'ممکن ہو تو رات کو نوجوان پودوں کو سردی سے بچائیں۔';
        }
        if ($humid || $rainLikely) {
            $careEn[] = 'Watch for fungal spots — improve airflow and avoid wetting leaves further.';
            $careUr[] = 'فنگس داغ دیکھیں — ہوا کا بہاؤ بہتر کریں اور پتوں کو زیادہ گیلا نہ کریں۔';
        }
        if ($windy) {
            $careEn[] = 'Check stakes and supports on tall crops.';
            $careUr[] = 'لمبی فصلوں کے سہارے / کھونٹے چیک کریں۔';
        }

        if ($storm || $heavyRain) {
            $avoidEn[] = 'Avoid spraying, transplanting, or walking wet beds.';
            $avoidUr[] = 'اسپرے، شفٹنگ، یا گیلی زمین پر چلنا موخر کریں۔';
            $avoidEn[] = 'Do not harvest muddy produce for market today if possible.';
            $avoidUr[] = 'ممکن ہو تو گیلی / مٹی آلودہ پیداوار آج مارکیٹ نہ بھیجیں۔';
        } elseif ($watering['status'] === 'no') {
            $avoidEn[] = 'Do not add extra irrigation on top of expected rain.';
            $avoidUr[] = 'متوقع بارش پر اضافی پانی نہ ڈالیں۔';
        }

        if ($hot && $dry) {
            $avoidEn[] = 'Avoid midday irrigation when leaves burn easily.';
            $avoidUr[] = 'دوپہر کی تیز دھوپ میں آبپاشی سے گریز کریں۔';
        }

        if ($doEn === []) {
            $doEn[] = 'Follow the crop’s normal care routine.';
            $doUr[] = 'فصل کی عام دیکھ بھال جاری رکھیں۔';
        }

        return [
            'do' => ['en' => $doEn, 'ur' => $doUr],
            'careful' => ['en' => $careEn, 'ur' => $careUr],
            'avoid' => ['en' => $avoidEn, 'ur' => $avoidUr],
        ];
    }

    private function summaryBlock(string $lang, SmartCrop $crop, array $weather, array $watering, array $headline): array
    {
        $w = $watering[$lang];
        $place = $weather['place'] ?? '';
        $summary = $lang === 'ur'
            ? ($weather['summary_ur'] ?? $weather['summary'] ?? '')
            : ($weather['summary'] ?? '');

        if ($lang === 'ur') {
            return [
                'watering' => '💧 آبپاشی: '.$w['label'],
                'reason' => '🌧️ وجہ: '.$w['reason'],
                'soil' => '🌱 مٹی: نمی پر نظر رکھیں',
                'weather' => '☀️ موسم: '.$summary.($place ? ' · '.$place : ''),
                'headline' => ($headline['icon'] ?? '').' '.($headline['ur'] ?? ''),
            ];
        }

        return [
            'watering' => '💧 Watering: '.$w['label'],
            'reason' => '🌧️ Reason: '.$w['reason'],
            'soil' => '🌱 Soil: Keep monitoring moisture',
            'weather' => '☀️ Weather: '.$summary.($place ? ' · '.$place : ''),
            'headline' => ($headline['icon'] ?? '').' '.($headline['en'] ?? ''),
        ];
    }

    private function emptyAdvice(): array
    {
        return [
            'headline' => [
                'en' => 'Weather unavailable',
                'ur' => 'موسم دستیاب نہیں',
                'icon' => '❔',
            ],
            'watering' => [
                'status' => 'careful',
                'icon' => '⚠️',
                'en' => [
                    'label' => 'Use local judgment',
                    'reason' => 'Live weather could not be loaded — follow the crop guide and check your field.',
                ],
                'ur' => [
                    'label' => 'مقامی صورتحال دیکھیں',
                    'reason' => 'لائیو موسم نہیں ملا — فصل گائیڈ اور کھیت کی حقیقت کے مطابق فیصلہ کریں۔',
                ],
            ],
            'today' => [
                'do' => [
                    'en' => ['Review crop care notes and check soil by hand.'],
                    'ur' => ['فصل کی ہدایات پڑھیں اور ہاتھ سے مٹی چیک کریں۔'],
                ],
                'careful' => ['en' => [], 'ur' => []],
                'avoid' => ['en' => [], 'ur' => []],
            ],
            'summary' => [
                'en' => [
                    'watering' => '💧 Watering: Use local judgment',
                    'reason' => '🌧️ Reason: Live weather unavailable',
                    'soil' => '🌱 Soil: Keep monitoring moisture',
                    'weather' => '☀️ Weather: Not loaded',
                    'headline' => '❔ Weather unavailable',
                ],
                'ur' => [
                    'watering' => '💧 آبپاشی: مقامی صورتحال دیکھیں',
                    'reason' => '🌧️ وجہ: لائیو موسم دستیاب نہیں',
                    'soil' => '🌱 مٹی: نمی پر نظر رکھیں',
                    'weather' => '☀️ موسم: لوڈ نہیں ہوا',
                    'headline' => '❔ موسم دستیاب نہیں',
                ],
            ],
            'meta' => [],
        ];
    }

    private function num(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (float) $value;
    }
}
