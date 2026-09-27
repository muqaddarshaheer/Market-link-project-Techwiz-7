<?php

namespace App\Http\Controllers;

use App\Models\FarmerLand;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FarmerLandController extends Controller
{
    private function profile()
    {
        $profile = auth()->user()->farmerProfile;
        abort_unless($profile, 403);

        return $profile;
    }

    public function index()
    {
        $farmer = $this->profile();
        $crops = config('crop_calculator.crops', []);

        return view('farmer.lands.index', [
            'farmer' => $farmer,
            'lands' => $farmer->lands()->latest()->get(),
            'crops' => $crops,
            'units' => ['marla', 'kanal', 'acre', 'bigha', 'hectare'],
            'stages' => [
                'empty' => 'Empty',
                'planted' => 'Just planted',
                'growing' => 'Growing',
                'flowering' => 'Flowering',
                'ready' => 'Ready to harvest',
                'harvested' => 'Harvested',
            ],
            'soils' => ['loamy', 'clay', 'sandy', 'silt', 'other'],
        ]);
    }

    public function store(Request $request)
    {
        $farmer = $this->profile();
        $data = $this->validated($request);

        if (empty($data['expected_harvest']) && ! empty($data['planted_on'])) {
            $data['expected_harvest'] = FarmerLand::suggestHarvestDate(
                \Carbon\Carbon::parse($data['planted_on']),
                $data['crop_key'] ?? null
            );
        }

        if (($data['stage'] ?? 'empty') === 'empty') {
            $data['crop_name'] = $data['crop_name'] ?? null;
        }

        $farmer->lands()->create($data);

        return back()->with('success', 'Zameen save ho gayi.');
    }

    public function update(Request $request, FarmerLand $land)
    {
        $farmer = $this->profile();
        abort_unless($land->farmer_id === $farmer->id, 403);

        $data = $this->validated($request, true);

        if (empty($data['expected_harvest']) && ! empty($data['planted_on']) && empty($land->expected_harvest)) {
            $data['expected_harvest'] = FarmerLand::suggestHarvestDate(
                \Carbon\Carbon::parse($data['planted_on']),
                $data['crop_key'] ?? $land->crop_key
            );
        }

        $land->update($data);

        return back()->with('success', 'Zameen update ho gayi.');
    }

    public function destroy(FarmerLand $land)
    {
        $farmer = $this->profile();
        abort_unless($land->farmer_id === $farmer->id, 403);
        $land->delete();

        return back()->with('success', 'Zameen hata di.');
    }

    private function validated(Request $request, bool $updating = false): array
    {
        $crops = array_keys(config('crop_calculator.crops', []));
        $cropKeys = array_values(array_filter($crops, fn ($k) => $k !== 'other'));

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'area_amount' => ['nullable', 'numeric', 'min:0.01', 'max:99999'],
            'area_unit' => ['required', Rule::in(['marla', 'kanal', 'acre', 'bigha', 'hectare'])],
            'crop_key' => ['nullable', Rule::in($cropKeys)],
            'crop_name' => ['nullable', 'string', 'max:120'],
            'planted_on' => ['nullable', 'date'],
            'expected_harvest' => array_values(array_filter([
                'nullable',
                'date',
                $request->filled('planted_on') ? 'after_or_equal:planted_on' : null,
            ])),
            'stage' => ['required', Rule::in(['empty', 'planted', 'growing', 'flowering', 'ready', 'harvested'])],
            'soil_type' => ['nullable', Rule::in(['loamy', 'clay', 'sandy', 'silt', 'other'])],
            'notes' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        if (! empty($data['crop_key']) && empty($data['crop_name'])) {
            $data['crop_name'] = config('crop_calculator.crops.'.$data['crop_key'].'.en')
                ?? $data['crop_key'];
        }

        if (($data['stage'] ?? '') === 'empty') {
            $data['crop_key'] = null;
            $data['crop_name'] = null;
            $data['planted_on'] = null;
            $data['expected_harvest'] = null;
        }

        $data['is_active'] = $request->boolean('is_active', true);

        return $data;
    }
}
