<?php

namespace App\Http\Controllers;

use App\Models\SmartCrop;
use App\Services\SmartCropAdviceService;
use App\Services\WeatherService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SmartCropGuideController extends Controller
{
    public function __construct(
        private WeatherService $weather,
        private SmartCropAdviceService $advice
    ) {}

    private function profile()
    {
        $profile = auth()->user()->farmerProfile;
        abort_unless($profile, 403);

        return $profile;
    }

    public function index(Request $request): View
    {
        $farmer = $this->profile();
        $weather = $this->weather->forFarmer($farmer);

        $crops = SmartCrop::query()
            ->active()
            ->ordered()
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%'.$request->string('q').'%';
                $q->where(function ($inner) use ($term) {
                    $inner->where('name_en', 'like', $term)
                        ->orWhere('name_ur', 'like', $term)
                        ->orWhere('slug', 'like', $term);
                });
            })
            ->when($request->filled('category') && in_array($request->category, ['vegetable', 'fruit'], true), function ($q) use ($request) {
                $q->where('category', $request->category);
            })
            ->get(['id', 'slug', 'name_en', 'name_ur', 'emoji', 'category', 'sort_order']);

        return view('farmer.smart-crop-guide.index', [
            'crops' => $crops,
            'weather' => $weather,
            'filters' => [
                'q' => (string) $request->get('q', ''),
                'category' => (string) $request->get('category', ''),
            ],
            'showUrl' => url('/farmer/smart-crop-guide'),
        ]);
    }

    public function show(SmartCrop $smartCrop): JsonResponse
    {
        abort_unless($smartCrop->is_active, 404);
        $farmer = $this->profile();
        $weather = $this->weather->forFarmer($farmer);
        $advice = $this->advice->forCrop($smartCrop, $weather);

        return response()->json($smartCrop->toGuidePayload($weather, $advice));
    }
}
