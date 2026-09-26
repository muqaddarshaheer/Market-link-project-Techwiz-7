<?php

namespace App\Services;

/**
 * Builds speech-friendly weather sentences for TTS.
 * Display formatting stays in the UI; this layer is voice-only.
 */
class WeatherSpeechService
{
    /**
     * Natural spoken summary for today's stall weather.
     */
    public function forToday(array $weather, string $lang = 'en'): string
    {
        $lang = $lang === 'ur' ? 'ur' : 'en';

        return $lang === 'ur'
            ? $this->todayUrdu($weather)
            : $this->todayEnglish($weather);
    }

    /**
     * Natural spoken summary for one forecast day.
     */
    public function forDay(array $day, string $lang = 'en'): string
    {
        $lang = $lang === 'ur' ? 'ur' : 'en';

        return $lang === 'ur'
            ? $this->dayUrdu($day)
            : $this->dayEnglish($day);
    }

    /**
     * Last-pass cleanup before audio synthesis (symbols / abbreviations).
     */
    public function normalizeForTts(string $text, string $lang = 'en'): string
    {
        $text = trim(preg_replace('/\s+/u', ' ', $text) ?: '');
        if ($text === '') {
            return '';
        }

        // Expand units that may leak from UI strings.
        $replacements = [
            '/(\d+(?:\.\d+)?)\s*°\s*C\b/iu' => '$1 degrees Celsius',
            '/(\d+(?:\.\d+)?)\s*°\s*F\b/iu' => '$1 degrees Fahrenheit',
            '/(\d+(?:\.\d+)?)\s*C\b(?!\w)/u' => '$1 degrees Celsius',
            '/(\d+(?:\.\d+)?)\s*%/u' => '$1 percent',
            '/(\d+(?:\.\d+)?)\s*km\/h\b/iu' => '$1 kilometers per hour',
            '/(\d+(?:\.\d+)?)\s*mph\b/iu' => '$1 miles per hour',
            '/(\d+(?:\.\d+)?)\s*mm\b/iu' => '$1 millimeters',
            '/(\d+(?:\.\d+)?)\s*hPa\b/iu' => '$1 hectopascals',
            '/\bUV\b/u' => 'U V',
            '/\bAQI\b/u' => 'air quality index',
            '/[°]+/u' => ' degrees ',
        ];

        if ($lang === 'ur') {
            $replacements = [
                '/(\d+(?:\.\d+)?)\s*°\s*C\b/iu' => '$1 ڈگری سینٹی گریڈ',
                '/(\d+(?:\.\d+)?)\s*°\s*F\b/iu' => '$1 ڈگری فارن ہائیٹ',
                '/(\d+(?:\.\d+)?)\s*C\b(?!\w)/u' => '$1 ڈگری سینٹی گریڈ',
                '/(\d+(?:\.\d+)?)\s*%/u' => '$1 فیصد',
                '/(\d+(?:\.\d+)?)\s*km\/h\b/iu' => '$1 کلومیٹر فی گھنٹہ',
                '/(\d+(?:\.\d+)?)\s*mph\b/iu' => '$1 میل فی گھنٹہ',
                '/(\d+(?:\.\d+)?)\s*mm\b/iu' => '$1 ملی میٹر',
                '/(\d+(?:\.\d+)?)\s*hPa\b/iu' => '$1 ہیکٹو پاسکل',
                '/[°]+/u' => ' ڈگری ',
            ];
        }

        foreach ($replacements as $pattern => $replace) {
            $text = preg_replace($pattern, $replace, $text) ?: $text;
        }

        if ($lang === 'ur') {
            $text = $this->westernDigitsToUrduWords($text);
        }

        $text = preg_replace('/\s+/u', ' ', $text) ?: $text;
        $text = preg_replace('/^[\s\.,;:۔]+|[\s\.,;:۔]+$/u', '', $text) ?: $text;

        return $text === '' ? '' : $text.($lang === 'ur' ? '۔' : '.');
    }

    /**
     * Convert Western digits to spoken Urdu words so TTS does not skip or mangle numbers.
     */
    private function westernDigitsToUrduWords(string $text): string
    {
        return preg_replace_callback('/\d+(?:\.\d+)?/u', function (array $m) {
            $raw = $m[0];
            if (str_contains($raw, '.')) {
                [$whole, $frac] = explode('.', $raw, 2);

                return $this->intToUrduWords((int) $whole).' اعشاریہ '.$this->digitsToUrduWords($frac);
            }

            return $this->intToUrduWords((int) $raw);
        }, $text) ?: $text;
    }

    private function digitsToUrduWords(string $digits): string
    {
        $map = ['0' => 'صفر', '1' => 'ایک', '2' => 'دو', '3' => 'تین', '4' => 'چار', '5' => 'پانچ', '6' => 'چھ', '7' => 'سات', '8' => 'آٹھ', '9' => 'نو'];
        $out = [];
        foreach (str_split($digits) as $d) {
            $out[] = $map[$d] ?? $d;
        }

        return implode(' ', $out);
    }

    private function intToUrduWords(int $n): string
    {
        if ($n < 0) {
            return 'منفی '.$this->intToUrduWords(abs($n));
        }

        $ones = [
            0 => 'صفر', 1 => 'ایک', 2 => 'دو', 3 => 'تین', 4 => 'چار', 5 => 'پانچ',
            6 => 'چھ', 7 => 'سات', 8 => 'آٹھ', 9 => 'نو', 10 => 'دس',
            11 => 'گیارہ', 12 => 'بارہ', 13 => 'تیرہ', 14 => 'چودہ', 15 => 'پندرہ',
            16 => 'سولہ', 17 => 'سترہ', 18 => 'اٹھارہ', 19 => 'انیس',
            20 => 'بیس', 21 => 'اکیس', 22 => 'بائیس', 23 => 'تئیس', 24 => 'چوبیس',
            25 => 'پچیس', 26 => 'چھببیس', 27 => 'ستائیس', 28 => 'اٹھائیس', 29 => 'انتیس',
            30 => 'تیس', 31 => 'اکتیس', 32 => 'بتیس', 33 => 'تینتیس', 34 => 'چونتیس',
            35 => 'پینتیس', 36 => 'چھتیس', 37 => 'سینتیس', 38 => 'اڑتیس', 39 => 'انتالیس',
            40 => 'چالیس', 41 => 'اکتالیس', 42 => 'بیالیس', 43 => 'تینتالیس', 44 => 'چوالیس',
            45 => 'پینتالیس', 46 => 'چھیالیس', 47 => 'سینتالیس', 48 => 'اڑتالیس', 49 => 'انچاس',
            50 => 'پچاس', 51 => 'اکاون', 52 => 'باون', 53 => 'ترپن', 54 => 'چون',
            55 => 'پچپن', 56 => 'چھپن', 57 => 'ستاون', 58 => 'اٹھاون', 59 => 'انسٹھ',
            60 => 'ساٹھ', 61 => 'اکسٹھ', 62 => 'باسٹھ', 63 => 'تریسٹھ', 64 => 'چونسٹھ',
            65 => 'پینسٹھ', 66 => 'چھیاسٹھ', 67 => 'سڑسٹھ', 68 => 'اڑسٹھ', 69 => 'انہتر',
            70 => 'ستر', 71 => 'اکہتر', 72 => 'بہتر', 73 => 'تہتر', 74 => 'چوہتر',
            75 => 'پچہتر', 76 => 'چھہتر', 77 => 'ستتر', 78 => 'اٹھتر', 79 => 'اناسی',
            80 => 'اسی', 81 => 'اکیاسی', 82 => 'بیاسی', 83 => 'تراسی', 84 => 'چوراسی',
            85 => 'پچاسی', 86 => 'چھیاسی', 87 => 'ستاسی', 88 => 'اٹھاسی', 89 => 'نواسی',
            90 => 'نوے', 91 => 'اکانوے', 92 => 'بانوے', 93 => 'ترانوے', 94 => 'چورانوے',
            95 => 'پچانوے', 96 => 'چھیانوے', 97 => 'ستانوے', 98 => 'اتھانوے', 99 => 'ننانوے',
        ];

        if (isset($ones[$n])) {
            return $ones[$n];
        }

        if ($n < 1000) {
            $hundreds = intdiv($n, 100);
            $rest = $n % 100;
            $word = $ones[$hundreds].' سو';
            if ($rest > 0) {
                $word .= ' '.$this->intToUrduWords($rest);
            }

            return $word;
        }

        // Rare for weather; keep digits readable rather than inventing long forms.
        return $this->digitsToUrduWords((string) $n);
    }

    private function todayEnglish(array $w): string
    {
        $parts = [];
        $temp = $this->num($w['temp'] ?? null);
        $tmax = $this->num($w['temp_max'] ?? null);
        $tmin = $this->num($w['temp_min'] ?? null);
        $humidity = $this->num($w['humidity'] ?? null);
        $rainChance = $this->num($w['rain_chance'] ?? null);
        $wind = $this->num($w['wind'] ?? null);
        $condition = $this->conditionEnglish($w['summary'] ?? null, $w['code'] ?? null);

        if ($temp !== null) {
            $feel = $this->feelPhraseEnglish($temp, $condition);
            $parts[] = "Right now the temperature is {$temp} degrees Celsius".$feel;
        } elseif ($condition !== '') {
            $parts[] = 'Right now '.$condition;
        } else {
            $parts[] = 'Here is today’s weather for your stall area';
        }

        if ($temp === null && $condition !== '') {
            // already said condition
        } elseif ($condition !== '' && $temp !== null) {
            $parts[count($parts) - 1] .= ', and '.$condition;
        }

        if ($tmax !== null && $tmin !== null && $tmax !== $tmin) {
            $parts[] = "Today’s high is around {$tmax} degrees Celsius, with a low near {$tmin} degrees Celsius";
        }

        if ($rainChance !== null && $rainChance >= 40) {
            $parts[] = $rainChance >= 70
                ? "There’s a strong chance of rain, about {$rainChance} percent"
                : "There’s a chance of rain, about {$rainChance} percent";
        } elseif ($rainChance !== null && $rainChance > 0 && $rainChance < 40) {
            $parts[] = 'Rain looks unlikely for now';
        }

        if ($wind !== null && $wind >= 25) {
            $parts[] = "The wind is moving at about {$wind} kilometers per hour, so it feels quite breezy";
        } elseif ($wind !== null && $wind >= 12) {
            $parts[] = "The wind is around {$wind} kilometers per hour";
        }

        if ($humidity !== null && $humidity >= 70) {
            $parts[] = "Humidity is {$humidity} percent, so the air may feel a bit heavy";
        } elseif ($humidity !== null && $humidity > 0 && $humidity < 35) {
            $parts[] = "Humidity is {$humidity} percent, so the air is fairly dry";
        }

        $crop = trim((string) ($w['crop_note'] ?? ''));
        if ($crop !== '') {
            $parts[] = $this->softenCropEnglish($crop);
        }

        $days = $w['days'] ?? [];
        if (is_array($days) && $days !== []) {
            $wet = 0;
            foreach ($days as $day) {
                if (($this->num($day['rain_chance'] ?? null) ?? 0) >= 45) {
                    $wet++;
                }
            }
            if ($wet > 0) {
                $parts[] = $wet === 1
                    ? 'Over the next seven days, about one day looks rainy'
                    : "Over the next seven days, about {$wet} days look rainy";
            } else {
                $parts[] = 'The next seven days look mostly dry';
            }
        }

        return $this->joinEnglish($parts);
    }

    private function todayUrdu(array $w): string
    {
        $parts = [];
        $temp = $this->num($w['temp'] ?? null);
        $tmax = $this->num($w['temp_max'] ?? null);
        $tmin = $this->num($w['temp_min'] ?? null);
        $humidity = $this->num($w['humidity'] ?? null);
        $rainChance = $this->num($w['rain_chance'] ?? null);
        $wind = $this->num($w['wind'] ?? null);
        $condition = $this->conditionUrdu($w['summary_ur'] ?? null, $w['code'] ?? null, $w['summary'] ?? null);

        if ($temp !== null) {
            $feel = $this->feelPhraseUrdu($temp);
            $parts[] = "ابھی درجہ حرارت {$temp} ڈگری سینٹی گریڈ ہے{$feel}";
        } elseif ($condition !== '') {
            $parts[] = 'ابھی '.$condition;
        } else {
            $parts[] = 'آج آپ کے علاقے کا موسم یہ ہے';
        }

        if ($condition !== '' && $temp !== null) {
            $parts[] = $condition;
        }

        if ($tmax !== null && $tmin !== null && $tmax !== $tmin) {
            $parts[] = "آج زیادہ درجہ حرارت تقریباً {$tmax} ڈگری، اور کم تقریباً {$tmin} ڈگری رہ سکتا ہے";
        }

        if ($rainChance !== null && $rainChance >= 40) {
            $parts[] = $rainChance >= 70
                ? "آج بارش کا کافی امکان ہے، تقریباً {$rainChance} فیصد"
                : "آج بارش کا امکان ہے، تقریباً {$rainChance} فیصد";
        } elseif ($rainChance !== null && $rainChance > 0 && $rainChance < 40) {
            $parts[] = 'ابھی بارش کا امکان کم ہے';
        }

        if ($wind !== null && $wind >= 25) {
            $parts[] = "ہوا کافی تیز ہے، تقریباً {$wind} کلومیٹر فی گھنٹہ";
        } elseif ($wind !== null && $wind >= 12) {
            $parts[] = "ہوا کی رفتار تقریباً {$wind} کلومیٹر فی گھنٹہ ہے";
        }

        if ($humidity !== null && $humidity >= 70) {
            $parts[] = "ہوا میں نمی {$humidity} فیصد ہے، اس لیے موسم بھاری لگ سکتا ہے";
        } elseif ($humidity !== null && $humidity > 0 && $humidity < 35) {
            $parts[] = "ہوا میں نمی {$humidity} فیصد ہے، موسم خشک لگ رہا ہے";
        }

        $crop = trim((string) ($w['crop_note_ur'] ?? $w['crop_note'] ?? ''));
        if ($crop !== '') {
            $parts[] = $this->softenCropUrdu($crop);
        }

        $days = $w['days'] ?? [];
        if (is_array($days) && $days !== []) {
            $wet = 0;
            foreach ($days as $day) {
                if (($this->num($day['rain_chance'] ?? null) ?? 0) >= 45) {
                    $wet++;
                }
            }
            if ($wet > 0) {
                $parts[] = $wet === 1
                    ? 'اگلے سات دنوں میں ایک دن بارش ممکن ہے'
                    : "اگلے سات دنوں میں تقریباً {$wet} دن بارش ممکن ہے";
            } else {
                $parts[] = 'اگلے سات دن زیادہ تر خشک رہ سکتے ہیں';
            }
        }

        return $this->joinUrdu($parts);
    }

    private function dayEnglish(array $d): string
    {
        $label = trim((string) ($d['label'] ?? 'This day'));
        if ($label === 'Today') {
            $label = 'Today';
        }

        $parts = [];
        $condition = $this->conditionEnglish($d['summary'] ?? null, $d['code'] ?? null);
        $tmax = $this->num($d['temp_max'] ?? null);
        $tmin = $this->num($d['temp_min'] ?? null);
        $rainChance = $this->num($d['rain_chance'] ?? null);
        $wind = $this->num($d['wind'] ?? null);

        if ($condition !== '') {
            $parts[] = "{$label}, {$condition}";
        } else {
            $parts[] = "Here is the weather for {$label}";
        }

        if ($tmax !== null && $tmin !== null) {
            $parts[] = "The high should be around {$tmax} degrees Celsius, and the low near {$tmin} degrees Celsius";
        } elseif ($tmax !== null) {
            $parts[] = "The high should be around {$tmax} degrees Celsius";
        } elseif ($tmin !== null) {
            $parts[] = "The low should be near {$tmin} degrees Celsius";
        }

        if ($rainChance !== null && $rainChance >= 40) {
            $parts[] = "There’s about a {$rainChance} percent chance of rain";
        } elseif ($rainChance !== null && $rainChance < 40) {
            $parts[] = 'Rain looks unlikely';
        }

        if ($wind !== null && $wind >= 25) {
            $parts[] = "Wind may reach about {$wind} kilometers per hour";
        }

        return $this->joinEnglish($parts);
    }

    private function dayUrdu(array $d): string
    {
        $label = trim((string) ($d['label_ur'] ?? $d['label'] ?? 'اس دن'));
        if (($d['label'] ?? '') === 'Today' || $label === 'آج') {
            $label = 'آج';
        }

        $parts = [];
        $condition = $this->conditionUrdu($d['summary_ur'] ?? null, $d['code'] ?? null, $d['summary'] ?? null);
        $tmax = $this->num($d['temp_max'] ?? null);
        $tmin = $this->num($d['temp_min'] ?? null);
        $rainChance = $this->num($d['rain_chance'] ?? null);
        $wind = $this->num($d['wind'] ?? null);

        if ($condition !== '') {
            $parts[] = "{$label} {$condition}";
        } else {
            $parts[] = "{$label} کا موسم یہ ہے";
        }

        if ($tmax !== null && $tmin !== null) {
            $parts[] = "زیادہ درجہ حرارت تقریباً {$tmax} ڈگری سینٹی گریڈ، اور کم تقریباً {$tmin} ڈگری رہ سکتا ہے";
        } elseif ($tmax !== null) {
            $parts[] = "زیادہ درجہ حرارت تقریباً {$tmax} ڈگری سینٹی گریڈ رہ سکتا ہے";
        } elseif ($tmin !== null) {
            $parts[] = "کم درجہ حرارت تقریباً {$tmin} ڈگری سینٹی گریڈ رہ سکتا ہے";
        }

        if ($rainChance !== null && $rainChance >= 40) {
            $parts[] = "بارش کا امکان تقریباً {$rainChance} فیصد ہے";
        } elseif ($rainChance !== null && $rainChance < 40) {
            $parts[] = 'بارش کا امکان کم ہے';
        }

        if ($wind !== null && $wind >= 25) {
            $parts[] = "ہوا تقریباً {$wind} کلومیٹر فی گھنٹہ تک تیز ہو سکتی ہے";
        }

        return $this->joinUrdu($parts);
    }

    private function conditionEnglish(?string $summary, mixed $code): string
    {
        $summary = trim((string) $summary);
        $code = is_numeric($code) ? (int) $code : null;

        if ($code !== null) {
            return match (true) {
                $code === 0 => 'the sky is clear',
                $code <= 3 => 'the sky is partly cloudy',
                $code <= 48 => 'it looks foggy or misty',
                $code <= 57 => 'light drizzle is possible',
                $code <= 67 => 'rain is likely',
                $code <= 77 => 'there is a risk of snow or icy weather',
                $code <= 82 => 'heavy showers are possible',
                $code <= 99 => 'thunderstorms are possible',
                default => $summary !== '' ? strtolower($summary) : '',
            };
        }

        if ($summary === '') {
            return '';
        }

        $map = [
            'Clear skies' => 'the sky is clear',
            'Partly cloudy' => 'the sky is partly cloudy',
            'Foggy / misty' => 'it looks foggy or misty',
            'Drizzle expected' => 'light drizzle is possible',
            'Rain likely' => 'rain is likely',
            'Snow / ice risk' => 'there is a risk of snow or icy weather',
            'Heavy showers' => 'heavy showers are possible',
            'Thunderstorm risk' => 'thunderstorms are possible',
        ];

        return $map[$summary] ?? strtolower($summary);
    }

    private function conditionUrdu(?string $summaryUr, mixed $code, ?string $summaryEn = null): string
    {
        $code = is_numeric($code) ? (int) $code : null;

        if ($code !== null) {
            return match (true) {
                $code === 0 => 'موسم صاف ہے',
                $code <= 3 => 'آسمان پر کچھ بادل ہیں',
                $code <= 48 => 'دھند یا کہر ہو سکتی ہے',
                $code <= 57 => 'ہلکی بوندا باندی ہو سکتی ہے',
                $code <= 67 => 'بارش کا امکان ہے',
                $code <= 77 => 'برف یا بہت ٹھنڈ کا خطرہ ہے',
                $code <= 82 => 'تیز بارش ہو سکتی ہے',
                $code <= 99 => 'طوفان یا گرج چمک کا امکان ہے',
                default => trim((string) $summaryUr),
            };
        }

        $summaryUr = trim((string) $summaryUr);
        if ($summaryUr !== '') {
            $map = [
                'صاف موسم' => 'موسم صاف ہے',
                'جزوی بادل' => 'آسمان پر کچھ بادل ہیں',
                'دھند / کہر' => 'دھند یا کہر ہو سکتی ہے',
                'ہلکی بوندا باندی' => 'ہلکی بوندا باندی ہو سکتی ہے',
                'بارش کا امکان' => 'بارش کا امکان ہے',
                'برف / برفانی خطرہ' => 'برف یا بہت ٹھنڈ کا خطرہ ہے',
                'تیز بارش' => 'تیز بارش ہو سکتی ہے',
                'طوفان / گرج چمک' => 'طوفان یا گرج چمک کا امکان ہے',
            ];

            return $map[$summaryUr] ?? $summaryUr;
        }

        return $this->conditionEnglish($summaryEn, null) !== ''
            ? 'موسم تبدیل ہو رہا ہے'
            : '';
    }

    private function feelPhraseEnglish(int $temp, string $condition): string
    {
        if ($temp >= 36) {
            return ', so it feels quite hot outside';
        }
        if ($temp >= 30) {
            return ', and it feels warm outside';
        }
        if ($temp <= 10) {
            return ', so it feels quite cold outside';
        }
        if ($temp <= 16) {
            return ', and it feels cool outside';
        }

        return '';
    }

    private function feelPhraseUrdu(int $temp): string
    {
        if ($temp >= 36) {
            return '، اس لیے باہر کافی گرمی ہے';
        }
        if ($temp >= 30) {
            return '، موسم گرم لگ رہا ہے';
        }
        if ($temp <= 10) {
            return '، اس لیے موسم کافی ٹھنڈا ہے';
        }
        if ($temp <= 16) {
            return '، موسم کچھ ٹھنڈا لگ رہا ہے';
        }

        return '';
    }

    private function softenCropEnglish(string $note): string
    {
        $note = trim($note);
        if ($note === '') {
            return '';
        }

        // Keep crop advice, but remove report-like labels.
        $note = preg_replace('/^(Crop note|Advice)\s*[:\-]\s*/iu', '', $note) ?: $note;

        return 'For your crops, '.$this->lcfirst($note);
    }

    private function softenCropUrdu(string $note): string
    {
        $note = trim($note);
        if ($note === '') {
            return '';
        }

        $note = preg_replace('/^(فصل کا مشورہ|مشورہ)\s*[:\-]\s*/u', '', $note) ?: $note;

        return 'فصلوں کے لیے: '.$note;
    }

    private function joinEnglish(array $parts): string
    {
        $parts = array_values(array_filter(array_map(function ($p) {
            $p = preg_replace('/^[\s\.]+|[\s\.]+$/u', '', (string) $p) ?: '';

            return $p;
        }, $parts)));
        if ($parts === []) {
            return 'Weather details are not available right now.';
        }

        $text = implode('. ', $parts).'.';
        $text = preg_replace('/\.\s*\./u', '.', $text) ?: $text;

        return $this->normalizeForTts($text, 'en');
    }

    private function joinUrdu(array $parts): string
    {
        $parts = array_values(array_filter(array_map(function ($p) {
            $p = preg_replace('/^[\s\.۔]+|[\s\.۔]+$/u', '', (string) $p) ?: '';

            return $p;
        }, $parts)));
        if ($parts === []) {
            return 'ابھی موسم کی تفصیل دستیاب نہیں۔';
        }

        $text = implode('۔ ', $parts).'۔';
        $text = preg_replace('/۔\s*۔/u', '۔', $text) ?: $text;

        return $this->normalizeForTts($text, 'ur');
    }

    private function num(mixed $value): ?int
    {
        if ($value === null || $value === '' || ! is_numeric($value)) {
            return null;
        }

        $n = (float) $value;
        if (! is_finite($n)) {
            return null;
        }

        return (int) round($n);
    }

    private function lcfirst(string $text): string
    {
        if ($text === '') {
            return '';
        }

        return mb_strtolower(mb_substr($text, 0, 1)).mb_substr($text, 1);
    }
}
