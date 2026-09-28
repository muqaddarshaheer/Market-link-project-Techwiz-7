<?php

namespace App\Http\Controllers;

use App\Services\CropCalculatorService;
use Illuminate\Http\Request;

class CropCalculatorController extends Controller
{
    public function __construct(private CropCalculatorService $calculator) {}

    private function profile()
    {
        $profile = auth()->user()->farmerProfile;
        abort_unless($profile, 403);

        return $profile;
    }

    public function index()
    {
        $this->profile();
        $cfg = $this->calculator->config();

        return view('farmer.crop-calculator.index', [
            'crops' => $cfg['crops'] ?? [],
            'cropInsights' => $cfg['crop_insights'] ?? [],
            'landUnits' => $cfg['land_units'] ?? [],
            'landHelpers' => $cfg['land_helpers'] ?? [],
            'qtyUnits' => $cfg['qty_units'] ?? [],
            'priceUnits' => $cfg['price_units'] ?? [],
            'calculateUrl' => route('farmer.crop-calculator.calculate'),
        ]);
    }

    public function calculate(Request $request)
    {
        $this->profile();

        $data = $request->validate([
            'crop' => ['required', 'string', 'max:40'],
            'crop_other' => ['nullable', 'string', 'max:80'],
            'land_amount' => ['required', 'numeric', 'min:0.01', 'max:1000000'],
            'land_unit' => ['required', 'string', 'max:40'],
            'budget' => ['required', 'numeric', 'min:0', 'max:100000000'],
            'seed_qty' => ['nullable', 'numeric', 'min:0', 'max:100000000'],
            'seed_unit' => ['required', 'string', 'max:40'],
            'seed_unit_cost' => ['nullable', 'numeric', 'min:0', 'max:100000000'],
            'fertilizer' => ['nullable', 'numeric', 'min:0', 'max:100000000'],
            'water' => ['nullable', 'numeric', 'min:0', 'max:100000000'],
            'labor' => ['nullable', 'numeric', 'min:0', 'max:100000000'],
            'transport' => ['nullable', 'numeric', 'min:0', 'max:100000000'],
            'other_cost' => ['nullable', 'numeric', 'min:0', 'max:100000000'],
            'prod_qty' => ['required', 'numeric', 'min:0.01', 'max:100000000'],
            'prod_unit' => ['required', 'string', 'max:40'],
            'price' => ['required', 'numeric', 'min:0.01', 'max:100000000'],
            'price_unit' => ['required', 'string', 'max:40'],
            'wastage' => ['nullable', 'numeric', 'min:0', 'max:50'],
        ]);

        $result = $this->calculator->calculate($data);

        return response()->json($result);
    }
}
