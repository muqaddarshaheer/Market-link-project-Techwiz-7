<?php

namespace App\Services;

use App\Models\FarmerProfile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WeatherService
{
    public function forFarmer(FarmerProfile $farmer): ?array
    {
        $coords = $this->resolveCoords($farmer);
        if (! $coords) {
            return null;
        }

        $key = 'weather:v5:'.round($coords['lat'], 2).':'.round($coords['lng'], 2).':'.now()->format('Y-m-d');
        $cacheHours = max(1, (int) config('weather.cache_hours', 2));

        return Cache::remember($key, now()->addHours($cacheHours), function () use ($coords) {
            try {
                $timeout = max(3, (int) config('weather.timeout', 10));
                $request = Http::timeout($timeout);
                if (app()->environment('local')) {
                    $request = $request->withoutVerifying();
                }

                $params = [
                    'latitude' => $coords['lat'],
                    'longitude' => $coords['lng'],
                    'current' => 'temperature_2m,relative_humidity_2m,precipitation,weather_code,wind_speed_10m',
                    'daily' => 'weather_code,temperature_2m_max,temperature_2m_min,precipitation_sum,precipitation_probability_max,wind_speed_10m_max',
                    'timezone' => 'auto',
                    'forecast_days' => 7,
                ];

                if ($apiKey = config('weather.api_key')) {
                    $params['apikey'] = $apiKey;
                }

                $response = $request->get(config('weather.forecast_url', 'https://api.open-meteo.com/v1/forecast'), $params);

                if (! $response->successful()) {
                    return $this->fallbackWeather($coords['label']);
                }

                $json = $response->json();
                $days = $this->buildDays($json);
                if ($days === []) {
                    return $this->fallbackWeather($coords['label']);
                }

                $today = $days[0];
                $payload = $this->payload(
                    $coords['label'],
                    data_get($json, 'current.temperature_2m', $today['temp_max']),
                    $today['temp_max'],
                    $today['temp_min'],
                    data_get($json, 'current.relative_humidity_2m'),
                    $today['rain_mm'],
                    $today['rain_chance'],
                    $today['wind'],
                    $today['code']
                );
                $payload['days'] = $days;
                $payload['sky'] = $today['sky'] ?? $this->skyClass($today['code']);
                $payload['skill_en'] = $today['skill_en'] ?? '';
                $payload['skill_ur'] = $today['skill_ur'] ?? '';
                $payload['skill_tags'] = $today['skill_tags'] ?? [];
                $payload['date_full'] = $today['date_full'] ?? '';
                $payload['date_full_ur'] = $today['date_full_ur'] ?? '';
                $payload['as_of'] = now()->toIso8601String();

                return $payload;
            } catch (\Throwable $e) {
                Log::warning('Weather fetch failed: '.$e->getMessage());

                return $this->fallbackWeather($coords['label']);
            }
        });
    }

    private function buildDays(array $json): array
    {
        $dates = data_get($json, 'daily.time', []);
        if (! is_array($dates) || $dates === []) {
            return [];
        }

        $days = [];
        foreach ($dates as $i => $date) {
            $code = (int) data_get($json, "daily.weather_code.$i", 0);
            $rain = (float) data_get($json, "daily.precipitation_sum.$i", 0);
            $rainChance = (int) data_get($json, "daily.precipitation_probability_max.$i", 0);
            $wind = (float) data_get($json, "daily.wind_speed_10m_max.$i", 0);
            $tmax = data_get($json, "daily.temperature_2m_max.$i");
            $tmin = data_get($json, "daily.temperature_2m_min.$i");
            $carbon = \Carbon\Carbon::parse($date);
            $risk = $this->riskLevel($code, $rain, $rainChance, $wind);

            $days[] = array_merge([
                'date' => $date,
                'label' => $i === 0 ? 'Today' : $carbon->format('D'),
                'label_ur' => $i === 0 ? 'آج' : $this->weekdayUr($carbon->dayOfWeek),
                'day_num' => $carbon->format('j'),
                'code' => $code,
                'summary' => $this->summary($code),
                'summary_ur' => $this->summaryUr($code),
                'icon' => $this->icon($code),
                'temp_max' => $tmax !== null ? round((float) $tmax) : null,
                'temp_min' => $tmin !== null ? round((float) $tmin) : null,
                'rain_mm' => $rain,
                'rain_chance' => $rainChance,
                'wind' => round($wind),
                'risk' => $risk,
                'sky' => $this->skyClass($code),
            ], $this->dateLabels($carbon, $i === 0, $risk, $code, $rainChance, $tmax !== null ? (float) $tmax : null, $wind));
        }

        return $days;
    }

    private function weekdayUr(int $dayOfWeek): string
    {
        return match ($dayOfWeek) {
            0 => 'اتوار',
            1 => 'پیر',
            2 => 'منگل',
            3 => 'بدھ',
            4 => 'جمعرات',
            5 => 'جمعہ',
            6 => 'ہفتہ',
            default => '',
        };
    }

    private function dateLabels(\Carbon\Carbon $carbon, bool $isToday, string $risk, int $code, int $rainChance, ?float $tmax = null, float $wind = 0): array
    {
        $monthsUr = [
            1 => 'جنوری', 2 => 'فروری', 3 => 'مارچ', 4 => 'اپریل',
            5 => 'مئی', 6 => 'جون', 7 => 'جولائی', 8 => 'اگست',
            9 => 'ستمبر', 10 => 'اکتوبر', 11 => 'نومبر', 12 => 'دسمبر',
        ];
        $weekdayEn = $carbon->format('l');
        $weekdayUr = $this->weekdayUr($carbon->dayOfWeek);
        $monthUr = $monthsUr[(int) $carbon->format('n')] ?? $carbon->format('F');
        $skills = $this->farmerSkills($risk, $code, $rainChance, $tmax, $wind);

        return [
            'month' => $carbon->format('M'),
            'month_ur' => $monthUr,
            'date_short' => $carbon->format('j M'),
            'date_short_ur' => $carbon->format('j').' '.$monthUr,
            'date_full' => ($isToday ? 'Today' : $weekdayEn).', '.$carbon->format('j F Y'),
            'date_full_ur' => ($isToday ? 'آج' : $weekdayUr).'، '.$carbon->format('j').' '.$monthUr.' '.$carbon->format('Y'),
            'weekday_full' => $weekdayEn,
            'weekday_full_ur' => $weekdayUr,
            'tip' => $this->dayFarmTip($risk, $code, $rainChance, false),
            'tip_ur' => $this->dayFarmTip($risk, $code, $rainChance, true),
            'skill_en' => $skills['en'],
            'skill_ur' => $skills['ur'],
            'skill_tags' => $skills['tags'],
        ];
    }

    private function farmerSkills(string $risk, int $code, int $rainChance, ?float $tmax, float $wind): array
    {
        $tags = [];
        $en = [];
        $ur = [];

        if ($risk === 'high' || $code >= 80 || $rainChance >= 70) {
            $tags = ['protect', 'pause_water'];
            $en[] = 'Protect crops — cover tender plants';
            $en[] = 'Pause irrigation until rain passes';
            $ur[] = 'فصل بچائیں — نرم پودے ڈھانپیں';
            $ur[] = 'بارش تک آبپاشی روکیں';
        } elseif ($risk === 'watch' || $rainChance >= 45) {
            $tags = ['watch', 'light_water'];
            $en[] = 'Skip extra watering — soil may stay wet';
            $en[] = 'Check drainage in low spots';
            $ur[] = 'اضافی پانی نہ دیں — مٹی گیلی رہ سکتی ہے';
            $ur[] = 'نچلی جگہوں کا نکاس چیک کریں';
        } elseif ($tmax !== null && $tmax >= 35) {
            $tags = ['heat', 'water_cool'];
            $en[] = 'Water early morning or evening';
            $en[] = 'Shade young seedlings from harsh sun';
            $ur[] = 'صبح یا شام پانی دیں';
            $ur[] = 'چھوٹے پودوں کو تیز دھوپ سے بچائیں';
        } elseif ($wind >= 35) {
            $tags = ['wind', 'support'];
            $en[] = 'Stake tall plants against wind';
            $en[] = 'Delay spray until wind drops';
            $ur[] = 'لمبے پودوں کو سہارا دیں';
            $ur[] = 'ہوا کم ہونے تک سپرے ملتوی کریں';
        } else {
            $tags = ['care', 'normal'];
            $en[] = 'Normal field care — weed & check pests';
            $en[] = 'Good day for light work and market packing';
            $ur[] = 'معمولی دیکھ بھال — گوڈی اور کیڑے چیک';
            $ur[] = 'ہلکے کام اور منڈی پیکنگ کا اچھا دن';
        }

        return [
            'en' => implode(' · ', $en),
            'ur' => implode(' · ', $ur),
            'tags' => $tags,
        ];
    }

    private function skyClass(int $code): string
    {
        return match (true) {
            $code === 0 => 'sky-clear',
            $code <= 3 => 'sky-partly',
            $code <= 48 => 'sky-fog',
            $code <= 67 => 'sky-rain',
            $code <= 77 => 'sky-cold',
            $code <= 99 => 'sky-storm',
            default => 'sky-partly',
        };
    }

    private function dayFarmTip(string $risk, int $code, int $rainChance, bool $urdu): string
    {
        if ($risk === 'high' || $code >= 80 || $rainChance >= 70) {
            return $urdu
                ? 'تیز بارش ممکن — آبپاشی روکیں، نرم پودے ڈھانپیں۔'
                : 'Heavy weather risk — pause irrigation; cover tender plants.';
        }
        if ($risk === 'watch' || $rainChance >= 45) {
            return $urdu
                ? 'بارش کا امکان — اضافی پانی نہ دیں؛ نمی دیکھیں۔'
                : 'Rain possible — skip extra watering; watch moisture.';
        }

        return $urdu
            ? 'موسم مناسب — شیڈول کے مطابق دیکھ بھال کریں۔'
            : 'Fair conditions — follow your normal care schedule.';
    }

    private function payload(
        string $place,
        $temp,
        $tmax,
        $tmin,
        $humidity,
        float $rain,
        int $rainChance,
        float $wind,
        int $code
    ): array {
        return [
            'place' => $place,
            'temp' => $temp !== null ? round((float) $temp) : null,
            'temp_max' => $tmax !== null ? round((float) $tmax) : null,
            'temp_min' => $tmin !== null ? round((float) $tmin) : null,
            'humidity' => $humidity,
            'rain_mm' => $rain,
            'rain_chance' => $rainChance,
            'wind' => round($wind),
            'code' => $code,
            'summary' => $this->summary($code),
            'summary_ur' => $this->summaryUr($code),
            'icon' => $this->icon($code),
            'crop_note' => $this->cropAdvice($code, $rain, $rainChance, $wind, $tmax),
            'crop_note_ur' => $this->cropAdviceUr($code, $rain, $rainChance, $wind, $tmax),
            'risk' => $this->riskLevel($code, $rain, $rainChance, $wind),
            'days' => [],
        ];
    }

    private function fallbackWeather(string $place): array
    {
        $payload = $this->payload($place, 24, 28, 18, 55, 0.0, 15, 12.0, 2);
        $days = [];
        $codes = [2, 1, 3, 61, 2, 80, 0];
        for ($i = 0; $i < 7; $i++) {
            $carbon = now()->addDays($i);
            $code = $codes[$i] ?? 2;
            $rainChance = match (true) {
                $code >= 80 => 75,
                $code >= 61 => 55,
                $code >= 3 => 30,
                default => 12 + ($i * 3),
            };
            $tmax = (float) (28 - ($i % 3) + ($code === 0 ? 2 : 0));
            $wind = (float) (12 + ($i * 2));
            $risk = $this->riskLevel($code, $rainChance > 60 ? 8.0 : 0.5, $rainChance, $wind);
            $days[] = array_merge([
                'date' => $carbon->toDateString(),
                'label' => $i === 0 ? 'Today' : $carbon->format('D'),
                'label_ur' => $i === 0 ? 'آج' : $this->weekdayUr($carbon->dayOfWeek),
                'day_num' => $carbon->format('j'),
                'code' => $code,
                'summary' => $this->summary($code),
                'summary_ur' => $this->summaryUr($code),
                'icon' => $this->icon($code),
                'temp_max' => round($tmax),
                'temp_min' => round(18 - ($i % 2)),
                'rain_mm' => $rainChance > 50 ? 4.0 : 0.0,
                'rain_chance' => $rainChance,
                'wind' => round($wind),
                'risk' => $risk,
                'sky' => $this->skyClass($code),
            ], $this->dateLabels($carbon, $i === 0, $risk, $code, $rainChance, $tmax, $wind));
        }
        $payload['days'] = $days;
        $payload['sky'] = $days[0]['sky'] ?? 'sky-partly';
        $payload['skill_en'] = $days[0]['skill_en'] ?? '';
        $payload['skill_ur'] = $days[0]['skill_ur'] ?? '';
        $payload['skill_tags'] = $days[0]['skill_tags'] ?? [];
        $payload['date_full'] = $days[0]['date_full'] ?? '';
        $payload['date_full_ur'] = $days[0]['date_full_ur'] ?? '';
        $payload['as_of'] = now()->toIso8601String();

        return $payload;
    }

    private function resolveCoords(FarmerProfile $farmer): ?array
    {
        if ($farmer->latitude && $farmer->longitude) {
            return [
                'lat' => (float) $farmer->latitude,
                'lng' => (float) $farmer->longitude,
                'label' => $farmer->address ?: $farmer->stall_name,
            ];
        }

        $market = $farmer->markets()->first();
        if ($market && $market->latitude && $market->longitude) {
            return [
                'lat' => (float) $market->latitude,
                'lng' => (float) $market->longitude,
                'label' => trim(($market->city ? $market->city.', ' : '').$market->name),
            ];
        }

        if ($market && $market->city) {
            $geo = $this->geocodeCity($market->city);
            if ($geo) {
                $geo['label'] = $market->city.($market->name ? ' · '.$market->name : '');

                return $geo;
            }
        }

        return [
            'lat' => 30.2672,
            'lng' => -97.7431,
            'label' => $farmer->address ?: 'Your market area',
        ];
    }

    private function geocodeCity(string $city): ?array
    {
        $key = 'geo:'.md5(strtolower($city));

        return Cache::remember($key, now()->addDays(7), function () use ($city) {
            try {
                $request = Http::timeout(5);
                if (app()->environment('local')) {
                    $request = $request->withoutVerifying();
                }
                $geoParams = [
                    'name' => $city,
                    'count' => 1,
                    'language' => 'en',
                    'format' => 'json',
                ];
                if ($apiKey = config('weather.api_key')) {
                    $geoParams['apikey'] = $apiKey;
                }

                $response = $request->get(config('weather.geocode_url', 'https://geocoding-api.open-meteo.com/v1/search'), $geoParams);
                $hit = data_get($response->json(), 'results.0');
                if (! $hit) {
                    return null;
                }

                return [
                    'lat' => (float) $hit['latitude'],
                    'lng' => (float) $hit['longitude'],
                    'label' => $city,
                ];
            } catch (\Throwable $e) {
                return null;
            }
        });
    }

    private function summary(int $code): string
    {
        return match (true) {
            $code === 0 => 'Clear skies',
            $code <= 3 => 'Partly cloudy',
            $code <= 48 => 'Foggy / misty',
            $code <= 57 => 'Drizzle expected',
            $code <= 67 => 'Rain likely',
            $code <= 77 => 'Snow / ice risk',
            $code <= 82 => 'Heavy showers',
            $code <= 99 => 'Thunderstorm risk',
            default => 'Check conditions',
        };
    }

    private function summaryUr(int $code): string
    {
        return match (true) {
            $code === 0 => 'صاف موسم',
            $code <= 3 => 'جزوی بادل',
            $code <= 48 => 'دھند / کہر',
            $code <= 57 => 'ہلکی بوندا باندی',
            $code <= 67 => 'بارش کا امکان',
            $code <= 77 => 'برف / برفانی خطرہ',
            $code <= 82 => 'تیز بارش',
            $code <= 99 => 'طوفان / گرج چمک',
            default => 'موسم چیک کریں',
        };
    }

    private function cropAdviceUr(int $code, float $rain, int $rainChance, float $wind, $tmax): string
    {
        if ($code >= 95 || $rain >= 15 || $rainChance >= 80) {
            return 'تیز بارش یا طوفان ہو سکتا ہے — نرم پودے ڈھانپ لیں، گیلی زمین پر کٹائی ملتوی کریں، اور ہلکی چادریں مضبوط باندھ لیں۔';
        }
        if ($code >= 80 || $rain >= 5 || $rainChance >= 55) {
            return 'بوندا باندی متوقع ہے — پودوں کو بچائیں، پتوں کی نمی دیکھیں، اور پیکنگ اندر کرنے کا منصوبہ رکھیں۔';
        }
        if ($wind >= 40) {
            return 'آج تیز ہوا چلے گی — لمبی فصلیں سہارا دیں اور مارکیٹ سے پہلے سٹال کی چھت چیک کریں۔';
        }
        if ($tmax !== null && (float) $tmax >= 36) {
            return 'گرم دن ہے — صبح پانی دیں، نازک پیداوار پر سایہ رکھیں، اور کٹی ہوئی چیزیں ٹھنڈی جگہ رکھیں۔';
        }
        if ($code >= 45 && $code <= 48) {
            return 'صبح دھند ہے — نظر کم ہو تو جلدی کٹائی نہ کریں؛ ورنہ فصل ٹھیک رہنی چاہیے۔';
        }

        return 'زیادہ تر فصلوں کے لیے موسم مناسب ہے — کٹائی، پیکنگ اور کھلی مارکیٹ کے لیے اچھا دن ہے۔';
    }

    private function icon(int $code): string
    {
        return match (true) {
            $code === 0 => 'bi-sun',
            $code <= 3 => 'bi-cloud-sun',
            $code <= 48 => 'bi-cloud-fog',
            $code <= 67 => 'bi-cloud-rain',
            $code <= 77 => 'bi-snow',
            $code <= 99 => 'bi-cloud-lightning-rain',
            default => 'bi-cloud',
        };
    }

    private function cropAdvice(int $code, float $rain, int $rainChance, float $wind, $tmax): string
    {
        if ($code >= 95 || $rain >= 15 || $rainChance >= 80) {
            return 'Heavy rain or storms possible — cover tender beds, delay harvest if fields are muddy, and secure lightweight covers.';
        }
        if ($code >= 80 || $rain >= 5 || $rainChance >= 55) {
            return 'Showers expected — protect seedlings, watch for leaf wetness on greens, and plan indoor packing if needed.';
        }
        if ($wind >= 40) {
            return 'Strong wind today — stake tall crops and check stall canopies before market hours.';
        }
        if ($tmax !== null && (float) $tmax >= 36) {
            return 'Hot day ahead — water early, shade delicate produce, and keep harvested stock cool.';
        }
        if ($code >= 45 && $code <= 48) {
            return 'Foggy morning — delay early harvest if visibility is low; crops look fine otherwise.';
        }

        return 'Mild conditions for most crops — good day for harvest, packing, and open-air market display.';
    }

    private function riskLevel(int $code, float $rain, int $rainChance, float $wind): string
    {
        if ($code >= 95 || $rain >= 15 || $rainChance >= 80 || $wind >= 50) {
            return 'high';
        }
        if ($code >= 61 || $rain >= 3 || $rainChance >= 45 || $wind >= 35) {
            return 'watch';
        }

        return 'ok';
    }
}
