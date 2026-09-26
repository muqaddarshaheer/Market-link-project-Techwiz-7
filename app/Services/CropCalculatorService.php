<?php

namespace App\Services;

/**
 * Farmer-friendly crop cost / revenue estimator.
 * All money results are estimates based on farmer-entered values.
 */
class CropCalculatorService
{
    public function config(): array
    {
        return config('crop_calculator', []);
    }

    /**
     * @param  array<string, mixed>  $input
     * @return array{ok:bool, errors?:array<int,string>, result?:array}
     */
    public function calculate(array $input): array
    {
        $errors = $this->validate($input);
        if ($errors !== []) {
            return ['ok' => false, 'errors' => $errors];
        }

        $cfg = $this->config();
        $cropKey = (string) ($input['crop'] ?? 'other');
        $cropMeta = $cfg['crops'][$cropKey] ?? $cfg['crops']['other'];
        $cropName = $cropKey === 'other' && ! empty($input['crop_other'])
            ? trim((string) $input['crop_other'])
            : (($cropMeta['en'] ?? 'Crop').' / '.($cropMeta['ur'] ?? ''));

        $landAmount = (float) ($input['land_amount'] ?? 0);
        $landUnit = (string) ($input['land_unit'] ?? 'kanal');
        $budget = (float) ($input['budget'] ?? 0);

        $seedQty = (float) ($input['seed_qty'] ?? 0);
        $seedUnit = (string) ($input['seed_unit'] ?? 'kg');
        $seedUnitCost = (float) ($input['seed_unit_cost'] ?? 0);

        $fertilizer = (float) ($input['fertilizer'] ?? 0);
        $water = (float) ($input['water'] ?? 0);
        $labor = (float) ($input['labor'] ?? 0);
        $transport = (float) ($input['transport'] ?? 0);
        $otherCost = (float) ($input['other_cost'] ?? 0);

        $prodQty = (float) ($input['prod_qty'] ?? 0);
        $prodUnit = (string) ($input['prod_unit'] ?? 'kg');
        $price = (float) ($input['price'] ?? 0);
        $priceUnit = (string) ($input['price_unit'] ?? 'per_kg');
        $wastage = max(0, min(50, (float) ($input['wastage'] ?? 0)));

        $seedCost = $seedQty * $seedUnitCost;
        $totalCost = $seedCost + $fertilizer + $water + $labor + $transport + $otherCost;

        $revenueCalc = $this->revenue($prodQty, $prodUnit, $price, $priceUnit, $wastage);
        if (! $revenueCalc['ok']) {
            return ['ok' => false, 'errors' => [$revenueCalc['error']]];
        }

        $sellable = $revenueCalc['sellable'];
        $revenue = $revenueCalc['revenue'];
        $profit = $revenue - $totalCost;
        $isProfit = $profit >= 0;
        $budgetRemaining = $budget - $totalCost;
        $breakEven = $sellable > 0 ? ($totalCost / $sellable) : null;

        $affordableSeedQty = $seedUnitCost > 0
            ? max(0, floor(($budget / $seedUnitCost) * 100) / 100)
            : null;

        $recommended = $this->plans($seedQty, $seedUnitCost, $budget, $prodQty, $price, $prodUnit, $priceUnit, $wastage, $fertilizer, $water, $labor, $transport, $otherCost);

        $status = 'balanced';
        $statusLabel = 'Balanced estimate';
        if ($budgetRemaining < 0) {
            $status = 'over_budget';
            $statusLabel = 'Budget se zyada kharcha (financial risk barh sakta hai)';
        } elseif ($isProfit) {
            $status = 'profit';
            $statusLabel = 'Estimated profit possible (market change ho sakti hai)';
        } else {
            $status = 'loss';
            $statusLabel = 'Estimated loss possible on these numbers';
        }

        $landLabel = $cfg['land_units'][$landUnit]['label'] ?? $landUnit;
        $seedUnitLabel = $cfg['qty_units'][$seedUnit]['label'] ?? $seedUnit;
        $prodUnitLabel = $cfg['qty_units'][$prodUnit]['label'] ?? $prodUnit;
        $priceUnitLabel = $cfg['price_units'][$priceUnit]['label'] ?? $priceUnit;

        return [
            'ok' => true,
            'result' => [
                'crop' => $cropName,
                'crop_icon' => $cropMeta['icon'] ?? '🌱',
                'land' => $this->num($landAmount).' '.$landLabel,
                'budget' => $budget,
                'seed_qty' => $seedQty,
                'seed_unit' => $seedUnitLabel,
                'seed_cost' => $seedCost,
                'costs' => [
                    'seed' => $seedCost,
                    'fertilizer' => $fertilizer,
                    'water' => $water,
                    'labor' => $labor,
                    'transport' => $transport,
                    'other' => $otherCost,
                ],
                'total_cost' => $totalCost,
                'production' => $prodQty,
                'production_unit' => $prodUnitLabel,
                'wastage' => $wastage,
                'sellable' => $sellable,
                'sellable_unit' => $revenueCalc['sellable_unit_label'],
                'price' => $price,
                'price_unit' => $priceUnitLabel,
                'revenue' => $revenue,
                'profit' => abs($profit),
                'is_profit' => $isProfit,
                'budget_remaining' => $budgetRemaining,
                'budget_over' => $budgetRemaining < 0 ? abs($budgetRemaining) : 0,
                'break_even' => $breakEven,
                'break_even_unit' => $revenueCalc['price_per_unit_label'],
                'affordable_seed_qty' => $affordableSeedQty,
                'recommended_seed_qty' => $affordableSeedQty !== null ? min($seedQty, $affordableSeedQty) : $seedQty,
                'quantity_warning' => ($affordableSeedQty !== null && $seedQty > $affordableSeedQty)
                    ? [
                        'over' => true,
                        'extra' => ($seedQty - $affordableSeedQty) * $seedUnitCost,
                        'message' => 'Aapke budget ke liye quantity zyada hai. Budget se zyada kharcha hoga, jis se financial risk barh sakta hai.',
                    ]
                    : ['over' => false],
                'plans' => $recommended,
                'compare' => [
                    'less' => [
                        'title' => 'Agar quantity KAM karein',
                        'points' => [
                            'Lower total cost',
                            'Lower financial exposure',
                            'Potentially lower revenue',
                        ],
                    ],
                    'more' => [
                        'title' => 'Agar quantity ZYADA karein',
                        'points' => [
                            'Higher total cost',
                            'Higher financial exposure',
                            'Potentially higher revenue',
                            'May exceed available budget',
                        ],
                    ],
                ],
                'status' => $status,
                'status_label' => $statusLabel,
                'tips' => [
                    'Yeh sirf estimate hai — exact profit guarantee nahi.',
                    'Market price change ho sakti hai.',
                    'Mausam production ko affect kar sakta hai.',
                    'Wastage ko zaroor consider karein.',
                    'Beej aur harvested crop quantity alag hoti hai.',
                    'Labor, transport aur pani ka kharcha add karein.',
                ],
                'disclaimer' => 'All money figures are estimates based on values you entered. Actual results can change with market, weather, and farm conditions.',
            ],
        ];
    }

    /**
     * Parse rough voice / text into calculator fields (Roman Urdu + English).
     *
     * @return array<string, mixed>
     */
    public function parseVoice(string $text): array
    {
        $norm = mb_strtolower(trim(preg_replace('/\s+/u', ' ', $text) ?: ''), 'UTF-8');
        $out = [];

        $cropMap = [
            'potato' => ['aloo', 'alu', 'potato', 'آلو'],
            'wheat' => ['gandum', 'wheat', 'گندم'],
            'rice' => ['chawal', 'rice', 'چاول'],
            'corn' => ['makai', 'corn', 'مکئی'],
            'tomato' => ['tamatar', 'tomato', 'ٹماٹر'],
            'onion' => ['piyaz', 'pyaz', 'onion', 'پیاز'],
            'carrot' => ['gajar', 'carrot', 'گاجر'],
            'cucumber' => ['kheera', 'cucumber', 'کھیرا'],
            'chili' => ['mirch', 'chili', 'مرچ'],
            'spinach' => ['palak', 'spinach', 'پالک'],
            'eggplant' => ['baingan', 'eggplant', 'بینگن'],
            'mango' => ['aam', 'mango', 'آم'],
            'apple' => ['seb', 'apple', 'سیب'],
            'kinnow' => ['kinnow', 'orange', 'کنو'],
            'watermelon' => ['tarbooz', 'watermelon', 'تربوز'],
            'grapes' => ['angoor', 'grapes', 'انگور'],
        ];
        foreach ($cropMap as $id => $words) {
            foreach ($words as $w) {
                if (str_contains($norm, mb_strtolower($w, 'UTF-8'))) {
                    $out['crop'] = $id;
                    break 2;
                }
            }
        }

        if (preg_match('/(\d+(?:\.\d+)?)\s*(kanal|acre|killa|hectare|marla|bigha|murabba)/u', $norm, $m)) {
            $out['land_amount'] = (float) $m[1];
            $unit = $m[2];
            $out['land_unit'] = match ($unit) {
                'acre', 'killa' => 'acre',
                'hectare' => 'hectare',
                'marla' => 'marla_272',
                'bigha' => 'bigha_punjab',
                'murabba' => 'murabba',
                default => 'kanal',
            };
        }

        if (preg_match('/(\d+(?:\.\d+)?)\s*(hazaar|hazār|ہزار)/u', $norm, $m)) {
            $out['budget'] = ((float) $m[1]) * 1000;
        } elseif (preg_match('/(?:budget|rupay|rupees|rs\.?)\s*(\d+(?:\.\d+)?)/u', $norm, $m)) {
            $out['budget'] = (float) $m[1];
        } elseif (preg_match('/(\d+(?:\.\d+)?)\s*(?:rupay|rupees|rs\.?|budget)/u', $norm, $m)) {
            $out['budget'] = (float) $m[1];
        }

        if (preg_match('/(\d+(?:\.\d+)?)\s*(kg|kilo|kilogram|maund)/u', $norm, $m)) {
            $out['seed_qty'] = (float) $m[1];
            $out['seed_unit'] = str_contains($m[2], 'maund') ? 'maund' : 'kg';
        }

        if (preg_match('/(?:kilo|kg|ek\s*kilo).*?(?:rs\.?|rupay)?\s*(\d+(?:\.\d+)?)/u', $norm, $m)
            || preg_match('/(\d+(?:\.\d+)?)\s*(?:rs\.?|rupay).*?(?:kilo|kg)/u', $norm, $m)) {
            $out['seed_unit_cost'] = (float) $m[1];
        }

        if (preg_match('/(?:bech|sell|price|rate).*?(\d+(?:\.\d+)?)/u', $norm, $m)) {
            $out['price'] = (float) $m[1];
            $out['price_unit'] = 'per_kg';
        }

        return $out;
    }

    private function validate(array $input): array
    {
        $errors = [];
        $cfg = $this->config();

        $crop = (string) ($input['crop'] ?? '');
        if ($crop === '' || ! isset($cfg['crops'][$crop])) {
            $errors[] = 'Fasal select karein.';
        }
        if ($crop === 'other' && trim((string) ($input['crop_other'] ?? '')) === '') {
            $errors[] = 'Other crop ka naam likhein.';
        }

        if ((float) ($input['land_amount'] ?? 0) <= 0) {
            $errors[] = 'Zameen ka raqba 0 se zyada hona chahiye.';
        }
        if (! isset($cfg['land_units'][(string) ($input['land_unit'] ?? '')])) {
            $errors[] = 'Zameen ki unit select karein.';
        }
        if ((float) ($input['budget'] ?? -1) < 0) {
            $errors[] = 'Budget 0 ya us se zyada hona chahiye.';
        }
        if ((float) ($input['seed_qty'] ?? 0) < 0 || (float) ($input['seed_unit_cost'] ?? 0) < 0) {
            $errors[] = 'Beej quantity / cost galat nahi ho sakti.';
        }
        if ((float) ($input['prod_qty'] ?? 0) <= 0) {
            $errors[] = 'Expected production 0 nahi ho sakti.';
        }
        if ((float) ($input['price'] ?? 0) <= 0) {
            $errors[] = 'Please enter expected selling price.';
        }

        $prodUnit = (string) ($input['prod_unit'] ?? 'kg');
        $priceUnit = (string) ($input['price_unit'] ?? 'per_kg');
        $prodFamily = $cfg['qty_units'][$prodUnit]['family'] ?? null;
        $priceFamily = $cfg['price_units'][$priceUnit]['family'] ?? null;
        if ($prodFamily && $priceFamily && $prodFamily !== $priceFamily) {
            $errors[] = 'Production unit aur selling price unit match nahi kar rahe. Unit check karein (maslan Kg ke sath Per Kg).';
        }

        return $errors;
    }

    private function revenue(float $prodQty, string $prodUnit, float $price, string $priceUnit, float $wastage): array
    {
        $cfg = $this->config();
        $prodMeta = $cfg['qty_units'][$prodUnit] ?? null;
        $priceMeta = $cfg['price_units'][$priceUnit] ?? null;
        if (! $prodMeta || ! $priceMeta) {
            return ['ok' => false, 'error' => 'Production / price unit invalid hai.'];
        }

        $sellableQty = $prodQty * (1 - ($wastage / 100));
        $family = $prodMeta['family'];

        if ($family === 'mass') {
            $kg = $sellableQty * (float) $prodMeta['to_kg'];
            $pricePerKg = $price * (float) $priceMeta['per_kg_factor'];
            $revenue = $kg * $pricePerKg;

            return [
                'ok' => true,
                'sellable' => $sellableQty,
                'sellable_unit_label' => $prodMeta['label'],
                'revenue' => $revenue,
                'price_per_unit_label' => $priceMeta['label'],
            ];
        }

        // count family: 1 production unit × price per same discrete unit
        $map = [
            'crate' => 'per_crate',
            'plant' => 'per_plant',
            'other' => 'per_other',
            'packet' => 'per_other',
        ];
        $expected = $map[$prodUnit] ?? null;
        if ($expected && $priceUnit !== $expected && $priceUnit !== 'per_other') {
            return [
                'ok' => false,
                'error' => 'Production '.$prodMeta['label'].' mein hai lekin selling price alag unit pe hai. Unit check karein.',
            ];
        }

        return [
            'ok' => true,
            'sellable' => $sellableQty,
            'sellable_unit_label' => $prodMeta['label'],
            'revenue' => $sellableQty * $price,
            'price_per_unit_label' => $priceMeta['label'],
        ];
    }

    private function plans(
        float $seedQty,
        float $seedUnitCost,
        float $budget,
        float $prodQty,
        float $price,
        string $prodUnit,
        string $priceUnit,
        float $wastage,
        float $fertilizer,
        float $water,
        float $labor,
        float $transport,
        float $otherCost
    ): array {
        $fixed = $fertilizer + $water + $labor + $transport + $otherCost;
        $make = function (float $factor, string $key, string $title, string $blurb) use ($seedQty, $seedUnitCost, $budget, $prodQty, $price, $prodUnit, $priceUnit, $wastage, $fixed) {
            $sq = max(0, $seedQty * $factor);
            $seedCost = $sq * $seedUnitCost;
            $total = $seedCost + $fixed * $factor;
            $rev = $this->revenue($prodQty * $factor, $prodUnit, $price, $priceUnit, $wastage);
            $revenue = $rev['ok'] ? $rev['revenue'] : 0;
            $profit = $revenue - $total;

            return [
                'key' => $key,
                'title' => $title,
                'blurb' => $blurb,
                'seed_qty' => $sq,
                'total_cost' => $total,
                'revenue' => $revenue,
                'profit' => $profit,
                'within_budget' => $total <= $budget,
            ];
        };

        return [
            $make(0.7, 'low', 'Low cost plan', 'Kam kharcha, kam risk — production bhi kam ho sakti hai'),
            $make(1.0, 'balanced', 'Balanced plan', 'Aapka current estimate'),
            $make(1.3, 'high', 'High input plan', 'Zyada kharcha / zyada potential — budget risk check karein'),
        ];
    }

    private function num(float $n): string
    {
        if (abs($n - round($n)) < 0.001) {
            return (string) (int) round($n);
        }

        return rtrim(rtrim(number_format($n, 2, '.', ''), '0'), '.');
    }
}
