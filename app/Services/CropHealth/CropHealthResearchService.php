<?php

namespace App\Services\CropHealth;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Optional AI enrichment for crop-health results.
 * ONLY crops / vegetables / fruits / farm produce. Never off-topic.
 * Falls back silently when no API key is configured.
 */
class CropHealthResearchService
{
    public function enrich(array $result, array $state, string $lang): array
    {
        $payload = $this->askAi($result, $state, $lang);
        if (! $payload) {
            return $result;
        }

        if (! empty($payload['causes']) && is_array($payload['causes'])) {
            $merged = array_values(array_unique(array_filter(array_merge(
                array_map('strval', $payload['causes']),
                $result['causes'] ?? []
            ))));
            $result['causes'] = array_slice($merged, 0, 4);
        }

        if (! empty($payload['checks']) && is_array($payload['checks'])) {
            $checks = [];
            foreach (array_slice($payload['checks'], 0, 5) as $i => $label) {
                $checks[] = ['id' => 'ai_'.$i, 'label' => (string) $label];
            }
            if ($checks !== []) {
                $result['checklist'] = $checks;
            }
        }

        if (! empty($payload['do']) && is_array($payload['do'])) {
            $result['prevention'] = array_slice(array_map('strval', $payload['do']), 0, 5);
        }

        if (! empty($payload['dont']) && is_array($payload['dont'])) {
            $result['avoid'] = array_slice(array_map('strval', $payload['dont']), 0, 4);
        }

        if (! empty($payload['speak']) && is_string($payload['speak'])) {
            $result['speak'] = trim($payload['speak']);
        }

        $result['research'] = true;

        return $result;
    }

    private function askAi(array $result, array $state, string $lang): ?array
    {
        $prompt = $this->buildPrompt($result, $state, $lang);

        $raw = $this->fromGroq($prompt)
            ?? $this->fromGemini($prompt)
            ?? $this->fromOpenAi($prompt);

        if (! $raw) {
            return null;
        }

        $json = $this->parseJson($raw);
        if (! $json) {
            return null;
        }

        // Hard reject off-topic leakage
        $blob = strtolower(json_encode($json, JSON_UNESCAPED_UNICODE) ?: '');
        foreach (['world war', 'cricket', 'politics', 'election', 'bitcoin', 'movie'] as $bad) {
            if (str_contains($blob, $bad)) {
                return null;
            }
        }

        return $json;
    }

    private function buildPrompt(array $result, array $state, string $lang): string
    {
        $langNote = $lang === 'en'
            ? 'Reply JSON values in simple English.'
            : 'Reply JSON values in simple Roman Urdu (Latin script), short farmer language.';

        $ctx = [
            'crop' => $result['crop_name'] ?? ($state['crop'] ?? ''),
            'problem' => $result['problem'] ?? '',
            'farmer_said' => $state['raw'] ?? '',
            'color' => $state['color'] ?? null,
            'part' => $state['part'] ?? null,
            'symptom' => $state['symptom'] ?? null,
            'answers' => $state['answers'] ?? [],
            'known_possible_causes' => $result['causes'] ?? [],
        ];

        return "You are Green Leaf Advisor — a warm, careful plant pharmacist for MarketLink farmers.\n"
            ."SCOPE: ONLY vegetables, fruits, herbs, medicinal plants, soil, watering, sunlight, organic pest care, seasonal farm advice (esp. Pakistan / South Asia).\n"
            ."REFUSE anything else (history, sports, politics, war, general chat).\n"
            ."NEVER claim a confirmed disease diagnosis or that herbs cure diseases. Use 'possible', 'may help', 'traditionally used'.\n"
            ."No pesticide/fertilizer dosage numbers. Safety first for internal herbal use — doctor advice required.\n"
            ."Tone: calm, encouraging, simple everyday language.\n"
            ."{$langNote}\n"
            ."Return ONLY valid JSON object with keys: causes (array of up to 4 short strings), checks (array), do (array), dont (array), speak (one short warm paragraph).\n"
            ."Context:\n".json_encode($ctx, JSON_UNESCAPED_UNICODE);
    }

    private function parseJson(string $raw): ?array
    {
        $raw = trim($raw);
        if (preg_match('/\{.*\}/s', $raw, $m)) {
            $raw = $m[0];
        }
        $data = json_decode($raw, true);

        return is_array($data) ? $data : null;
    }

    private function fromGroq(string $prompt): ?string
    {
        $key = config('services.groq.key') ?: env('GROQ_API_KEY');
        if (! $key) {
            return null;
        }

        try {
            $response = Http::withToken($key)->timeout(12)->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => config('services.groq.model', 'llama-3.3-70b-versatile'),
                'temperature' => 0.2,
                'max_tokens' => 500,
                'messages' => [
                    ['role' => 'system', 'content' => 'Return only JSON. Crop/food/farm scope only.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);
            if (! $response->successful()) {
                return null;
            }

            return trim((string) data_get($response->json(), 'choices.0.message.content')) ?: null;
        } catch (\Throwable $e) {
            Log::warning('CropHealth Groq failed', ['m' => $e->getMessage()]);

            return null;
        }
    }

    private function fromGemini(string $prompt): ?string
    {
        $key = config('services.gemini.key') ?: env('GEMINI_API_KEY');
        if (! $key) {
            return null;
        }

        try {
            $model = config('services.gemini.model', 'gemini-2.0-flash');
            $response = Http::timeout(12)->post(
                'https://generativelanguage.googleapis.com/v1beta/models/'.$model.':generateContent?key='.$key,
                [
                    'contents' => [['parts' => [['text' => $prompt]]]],
                    'generationConfig' => ['temperature' => 0.2, 'maxOutputTokens' => 500],
                ]
            );
            if (! $response->successful()) {
                return null;
            }

            return trim((string) data_get($response->json(), 'candidates.0.content.parts.0.text')) ?: null;
        } catch (\Throwable $e) {
            Log::warning('CropHealth Gemini failed', ['m' => $e->getMessage()]);

            return null;
        }
    }

    private function fromOpenAi(string $prompt): ?string
    {
        $key = config('services.openai.key') ?: env('OPENAI_API_KEY');
        if (! $key) {
            return null;
        }

        try {
            $response = Http::withToken($key)->timeout(12)->post('https://api.openai.com/v1/chat/completions', [
                'model' => config('services.openai.model', 'gpt-4o-mini'),
                'temperature' => 0.2,
                'max_tokens' => 500,
                'messages' => [
                    ['role' => 'system', 'content' => 'Return only JSON. Crop/food/farm scope only.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);
            if (! $response->successful()) {
                return null;
            }

            return trim((string) data_get($response->json(), 'choices.0.message.content')) ?: null;
        } catch (\Throwable $e) {
            Log::warning('CropHealth OpenAI failed', ['m' => $e->getMessage()]);

            return null;
        }
    }
}
