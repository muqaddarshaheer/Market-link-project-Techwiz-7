<?php

namespace App\Http\Controllers;

use App\Models\SmartCrop;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminSmartCropController extends Controller
{
    public function index(): View
    {
        $crops = SmartCrop::query()->ordered()->get();

        return view('admin.smart-crops.index', compact('crops'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug($data['name_en']);
        $data['details_en'] = $this->detailsFromRequest($request, 'en');
        $data['details_ur'] = $this->detailsFromRequest($request, 'ur');
        $data['is_active'] = $request->boolean('is_active', true);
        $data['sensitive_to_rain'] = $request->boolean('sensitive_to_rain', true);
        $data['sort_order'] = (int) ($data['sort_order'] ?? (SmartCrop::query()->max('sort_order') + 1));

        SmartCrop::query()->create($data);

        return back()->with('success', 'Crop added to Smart Crop Guide.');
    }

    public function update(Request $request, SmartCrop $smartCrop): RedirectResponse
    {
        $data = $this->validated($request, $smartCrop->id);
        if (($data['name_en'] ?? '') !== $smartCrop->name_en) {
            $data['slug'] = $this->uniqueSlug($data['name_en'], $smartCrop->id);
        }
        $data['details_en'] = $this->detailsFromRequest($request, 'en');
        $data['details_ur'] = $this->detailsFromRequest($request, 'ur');
        $data['is_active'] = $request->boolean('is_active');
        $data['sensitive_to_rain'] = $request->boolean('sensitive_to_rain');

        $smartCrop->update($data);

        return back()->with('success', 'Crop updated.');
    }

    public function destroy(SmartCrop $smartCrop): RedirectResponse
    {
        $smartCrop->delete();

        return back()->with('success', 'Crop removed.');
    }

    private function validated(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'name_en' => ['required', 'string', 'max:120'],
            'name_ur' => ['required', 'string', 'max:120'],
            'emoji' => ['nullable', 'string', 'max:16'],
            'category' => ['required', 'in:vegetable,fruit'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'temp_min_c' => ['nullable', 'numeric', 'between:-10,50'],
            'temp_max_c' => ['nullable', 'numeric', 'between:-10,55'],
            'ideal_humidity_min' => ['nullable', 'integer', 'min:0', 'max:100'],
            'ideal_humidity_max' => ['nullable', 'integer', 'min:0', 'max:100'],
        ]);
    }

    private function detailsFromRequest(Request $request, string $lang): array
    {
        $p = $lang.'_';

        return [
            'water' => [
                'amount' => $this->clean($request->input($p.'water_amount', '')),
                'frequency' => $this->clean($request->input($p.'water_frequency', '')),
                'best_time' => $this->clean($request->input($p.'water_best_time', '')),
                'warning' => $this->clean($request->input($p.'water_warning', '')),
            ],
            'soil' => [
                'type' => $this->clean($request->input($p.'soil_type', '')),
                'requirements' => $this->clean($request->input($p.'soil_requirements', '')),
            ],
            'temperature' => [
                'range' => $this->clean($request->input($p.'temp_range', '')),
                'warning' => $this->clean($request->input($p.'temp_warning', '')),
            ],
            'growth' => [
                'period' => $this->clean($request->input($p.'growth_period', '')),
                'harvest' => $this->clean($request->input($p.'growth_harvest', '')),
                'care' => $this->clean($request->input($p.'growth_care', '')),
            ],
            'problems' => [
                'pests' => $this->clean($request->input($p.'problems_pests', '')),
                'diseases' => $this->clean($request->input($p.'problems_diseases', '')),
                'prevention' => $this->clean($request->input($p.'problems_prevention', '')),
            ],
        ];
    }

    private function clean(mixed $value): string
    {
        return trim(strip_tags((string) $value));
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'crop';
        $slug = $base;
        $i = 2;
        while (
            SmartCrop::query()
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = $base.'-'.$i;
            $i++;
        }

        return $slug;
    }
}
