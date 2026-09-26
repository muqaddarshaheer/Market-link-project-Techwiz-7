<?php

namespace App\Http\Controllers;

use App\Services\CropHealth\CropHealthService;
use App\Services\WeatherService;
use App\Services\WeatherSpeechService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class CropHealthController extends Controller
{
    public function __construct(
        private CropHealthService $health,
        private WeatherService $weather,
    ) {}

    private function profile()
    {
        $profile = auth()->user()->farmerProfile;
        abort_unless($profile, 403);

        return $profile;
    }

    public function index()
    {
        $farmer = $this->profile()->loadMissing('markets');
        // Fresh conversation each visit — never reopen on a leftover crop (e.g. tomato).
        session()->forget('crop_health_state');

        return view('farmer.crop-health.index', [
            'farmer' => $farmer,
            'weather' => $this->weather->forFarmer($farmer),
            'speakUrl' => route('farmer.crop-health.speak'),
            'messageUrl' => route('farmer.crop-health.message'),
            'answerUrl' => route('farmer.crop-health.answer'),
            'photoUrl' => route('farmer.crop-health.photo'),
            'resetUrl' => route('farmer.crop-health.reset'),
            'starterPrompt' => 'Aapke plant mein kya masla hai?',
            'examples' => [
                'Mere piyaz ug rahe hain lekin black color ke.',
                'Mere aloo kale ho rahe hain.',
                'Tomato ke patte yellow hain.',
                'Piyaz ki pattiyan sukh rahi hain.',
                'Tulsi ke patte peele ho rahe hain.',
            ],
        ]);
    }

    public function message(Request $request)
    {
        $farmer = $this->profile();
        $data = $request->validate([
            'text' => ['required', 'string', 'max:500'],
            'lang' => ['nullable', 'in:ur,en,roman'],
        ]);

        $lang = $data['lang'] ?? 'roman';
        $state = session('crop_health_state', []);
        $weather = $this->weather->forFarmer($farmer);

        $result = $this->health->turn($state, $data['text'], null, null, $lang, $weather);
        session(['crop_health_state' => $result['state']]);

        return response()->json($result);
    }

    public function answer(Request $request)
    {
        $farmer = $this->profile();
        $data = $request->validate([
            'question_id' => ['required', 'string', 'max:40'],
            'option_id' => ['required', 'string', 'max:40'],
            'lang' => ['nullable', 'in:ur,en,roman'],
        ]);

        $lang = $data['lang'] ?? 'roman';
        $state = session('crop_health_state', []);
        $weather = $this->weather->forFarmer($farmer);

        $result = $this->health->turn(
            $state,
            null,
            $data['option_id'],
            $data['question_id'],
            $lang,
            $weather
        );
        session(['crop_health_state' => $result['state']]);

        return response()->json($result);
    }

    public function photo(Request $request)
    {
        $this->profile();
        $data = $request->validate([
            'photo' => ['required', 'image', 'max:5120'],
            'lang' => ['nullable', 'in:ur,en'],
        ]);

        $lang = $data['lang'] ?? 'ur';
        $path = $request->file('photo')->store('crop-health/'.auth()->id(), 'public');

        $state = session('crop_health_state', []);
        $state['photo'] = true;
        $state['photo_path'] = $path;
        session(['crop_health_state' => $state]);

        return response()->json([
            'ok' => true,
            'url' => Storage::disk('public')->url($path),
            'note' => $lang === 'en'
                ? 'Photo saved. Visual signs help, but this is not a guaranteed diagnosis.'
                : 'تصویر محفوظ ہو گئی۔ یہ مددگار ہے مگر یقینی تشخیص نہیں۔',
        ]);
    }

    public function reset()
    {
        $this->profile();
        session()->forget('crop_health_state');

        return response()->json(['ok' => true]);
    }

    public function speak(Request $request)
    {
        $this->profile();

        $data = $request->validate([
            'text' => ['required', 'string', 'max:280'],
            'lang' => ['nullable', 'in:ur,en,hi'],
        ]);

        $lang = $data['lang'] ?? 'ur';
        $speech = app(WeatherSpeechService::class);
        $normalizeLang = $lang === 'en' ? 'en' : 'ur';
        $text = $speech->normalizeForTts(trim($data['text']), $normalizeLang);
        $text = trim(preg_replace('/\s+/u', ' ', $text) ?: '');
        $text = mb_substr($text, 0, 180);
        if ($text === '') {
            return response('Voice unavailable', 422);
        }

        $voices = $normalizeLang === 'en' ? ['en'] : ['ur', 'hi'];
        foreach ($voices as $tl) {
            $body = $this->fetchTtsAudio($text, $tl);
            if ($body !== null) {
                return response($body, 200, [
                    'Content-Type' => 'audio/mpeg',
                    'Cache-Control' => 'private, max-age=120',
                ]);
            }
        }

        return response('Voice unavailable', 502);
    }

    private function fetchTtsAudio(string $text, string $tl): ?string
    {
        $http = Http::timeout(12)
            ->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
                'Accept' => '*/*',
                'Referer' => 'https://translate.google.com/',
            ]);

        if (app()->environment('local')) {
            $http = $http->withoutVerifying();
        }

        $minBytes = $tl === 'en'
            ? max(600, (int) (mb_strlen($text) * 35))
            : max(2000, (int) (mb_strlen($text) * 60));

        foreach (['tw-ob', 'gtx'] as $client) {
            $response = $http->get('https://translate.google.com/translate_tts', [
                'ie' => 'UTF-8',
                'client' => $client,
                'tl' => $tl,
                'q' => $text,
                'ttsspeed' => $tl === 'ur' ? '0.88' : '0.95',
            ]);

            if ($response->successful() && strlen($response->body()) >= $minBytes) {
                return $response->body();
            }
        }

        return null;
    }
}
