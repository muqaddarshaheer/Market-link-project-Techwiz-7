<?php

namespace App\Services\CropHealth;

/**
 * Green Leaf Advisor — warm plant-care specialist for farmers.
 * Vegetables, fruits, herbs, medicinal plants. Possible causes only.
 * No default crop. Dynamic follow-ups. Safety-first.
 */
class CropHealthService
{
    private array $knowledge;

    public function __construct(private ?CropHealthResearchService $research = null)
    {
        $php = resource_path('crop-health/knowledge.php');
        if (is_file($php)) {
            $this->knowledge = require $php;
        } else {
            $json = resource_path('crop-health/knowledge.json');
            $this->knowledge = is_file($json)
                ? (json_decode((string) file_get_contents($json), true) ?: [])
                : [];
        }
        $this->research ??= app(CropHealthResearchService::class);
    }

    public function knowledge(): array
    {
        return $this->knowledge;
    }

    public function turn(array $state, ?string $text, ?string $optionId, ?string $questionId, string $lang, ?array $weather): array
    {
        $lang = in_array($lang, ['en', 'ur', 'roman'], true) ? $lang : 'roman';
        $state = $this->normalizeState($state);
        $justHeard = null;

        if ($text !== null && trim($text) !== '') {
            $justHeard = trim($text);

            // Sticky note save intent (after a prior result)
            if ($this->wantsStickyNote($justHeard) && ! empty($state['last_result'])) {
                $sticky = $this->formatStickyNote($state['last_result'], $lang);
                $confirm = $lang === 'en'
                    ? 'Done — here is your sticky note. You can copy it and keep it somewhere visible.'
                    : 'Ho gaya — yeh aapka sticky note hai. Copy karke kahin dikhne wali jagah chipka lein.';

                return [
                    'status' => 'sticky',
                    'state' => $state,
                    'heard' => $justHeard,
                    'detected' => $this->detectedSummary($state, $lang),
                    'sticky' => $sticky,
                    'assistant_text' => $confirm."\n\n".$sticky['text'],
                    'speak' => $confirm,
                ];
            }

            if (! $this->isCropTopic($justHeard) && empty($state['crop']) && empty($state['symptom'])) {
                $refuse = $lang === 'en'
                    ? 'I specialize only in vegetables, fruits, herbs, and medicinal plants. How can I help you with your plants today?'
                    : 'Main sirf sabziyan, phal, herbs aur medicinal plants mein madad karta hoon. Aaj plants ke bare mein kaise madad karoon?';

                return [
                    'status' => 'ask',
                    'state' => $state,
                    'heard' => $justHeard,
                    'detected' => null,
                    'question' => [
                        'id' => 'start',
                        'prompt' => $refuse,
                        'options' => [],
                    ],
                    'assistant_text' => $refuse,
                    'speak' => $refuse,
                    'off_topic' => true,
                ];
            }

            $beforeCrop = $state['crop'] ?? null;
            $extracted = $this->extract($justHeard);
            $state = $this->mergeExtraction($state, $extracted);
            $state['raw'] = trim(($state['raw'] ?? '').' '.$justHeard);
            if (! empty($state['crop']) && $beforeCrop !== $state['crop'] && $state['crop'] !== 'generic') {
                $state['announce_crop'] = true;
            }
        }

        if ($questionId && $optionId !== null && $optionId !== '') {
            $state = $this->applyAnswer($state, $questionId, $optionId);
        }

        // Never keep a fake default crop.
        if (($state['crop'] ?? null) === 'tomato' && empty($state['raw']) && empty($state['answers'])) {
            $state['crop'] = null;
        }

        $question = $this->nextQuestion($state);
        if ($question !== null) {
            $localized = $this->localizeQuestion($question, $lang);
            $speak = $this->buildAskSpeak($state, $localized['prompt'], $lang);
            $state['announce_crop'] = false;

            return [
                'status' => 'ask',
                'state' => $state,
                'heard' => $justHeard,
                'detected' => $this->detectedSummary($state, $lang),
                'question' => $localized,
                'assistant_text' => $speak,
                'speak' => $speak,
            ];
        }

        // Need at least some problem signal before result
        if ($this->tooLittleInfo($state)) {
            $prompt = $lang === 'en'
                ? 'Don’t worry — tell me what’s happening with your plant. For example: “My onion plants are growing but turning black.”'
                : 'Pareshan na hon — batayein plant mein kya masla hai. Maslan: “Mere piyaz ug rahe hain lekin black color ke.”';
            $starter = [
                'id' => 'start',
                'prompt' => $prompt,
                'options' => [],
            ];

            return [
                'status' => 'ask',
                'state' => $state,
                'heard' => $justHeard,
                'detected' => null,
                'question' => $starter,
                'assistant_text' => $prompt,
                'speak' => $prompt,
            ];
        }

        $result = $this->buildResult($state, $lang, $weather);
        try {
            $result = $this->research->enrich($result, $state, $lang);
        } catch (\Throwable $e) {
            // Keep local result if research fails
        }
        $state['announce_crop'] = false;
        $state['last_result'] = [
            'crop_name' => $result['crop_name'] ?? '',
            'problem' => $result['problem'] ?? '',
            'causes' => $result['causes'] ?? [],
            'prevention' => $result['prevention'] ?? [],
            'avoid' => $result['avoid'] ?? [],
            'routine' => $result['routine'] ?? [],
            'icon' => $result['icon'] ?? '🌱',
        ];

        return [
            'status' => 'result',
            'state' => $state,
            'heard' => $justHeard,
            'detected' => $this->detectedSummary($state, $lang),
            'result' => $result,
            'assistant_text' => $result['speak'],
            'speak' => $result['speak'],
            'sticky_offer' => $result['sticky_offer'] ?? null,
        ];
    }

    private function wantsStickyNote(string $text): bool
    {
        $n = $this->normalizeText($text);
        if (preg_match('/^(yes|ok|okay|haan|han|ji|jee|save|theek|thik|جی|ہاں|ٹھیک)(\s|$)/u', $n)) {
            return true;
        }

        return (bool) preg_match('/\b(sticky\s*note|save\s*(it|note)|note\s*bana|chipka)\b/u', $n);
    }

    private function formatStickyNote(array $last, string $lang): array
    {
        $title = $lang === 'en'
            ? ($last['crop_name'] ?? 'Plant').' care'
            : ($last['crop_name'] ?? 'Plant').' ka khayal';
        $routine = $last['routine'] ?? [];
        $morning = $routine['morning'] ?? ($lang === 'en' ? 'Check leaves and soil moisture.' : 'Patte aur mitti ki nami check karein.');
        $evening = $routine['evening'] ?? ($lang === 'en' ? 'Look for pests under leaves.' : 'Patton ke neeche keere dekhein.');
        $fewDays = $routine['every_few_days'] ?? ($lang === 'en' ? 'Water only if soil is dry above.' : 'Upar ki mitti dry ho to hi pani dein.');
        $weekly = $routine['weekly'] ?? ($lang === 'en' ? 'Clean around plants; remove damaged parts.' : 'Plant ke aas-paas saaf rakhein; kharab hisse hataein.');
        $main = ! empty($last['prevention'][0])
            ? $last['prevention'][0]
            : (! empty($last['causes'][0]) ? $last['causes'][0] : ($lang === 'en' ? 'Monitor and keep conditions steady.' : 'Regular check karein aur halaat stable rakhein.'));
        $warn = ! empty($last['avoid'][0])
            ? $last['avoid'][0]
            : ($lang === 'en'
                ? 'No random pesticide/fertilizer. Herbal remedies are complementary — ask a doctor before internal use.'
                : 'Random dawa/khad na dein. Herbal remedies complementary hain — andar istemal se pehle doctor se mashwara.');

        $text = "📌 STICKY NOTE – {$title}\n"
            ."──────────────────────────────\n"
            .'Plant / Problem: '.($last['crop_name'] ?? 'Plant').' — '.($last['problem'] ?? '')."\n"
            ."Main Solution: {$main}\n"
            ."Daily Routine:\n"
            ."• Morning: {$morning}\n"
            ."• Evening: {$evening}\n"
            ."• Every 3–4 days: {$fewDays}\n"
            ."• Weekly: {$weekly}\n"
            ."Important Warnings: {$warn}\n"
            ."──────────────────────────────\n"
            .($lang === 'en'
                ? 'You can copy this and stick it somewhere visible!'
                : 'Isko copy karke kahin dikhne wali jagah chipka lein!');

        return [
            'title' => $title,
            'text' => $text,
            'plant' => $last['crop_name'] ?? '',
            'problem' => $last['problem'] ?? '',
            'icon' => $last['icon'] ?? '🌱',
        ];
    }

    /**
     * Only farm / crop / produce topics.
     */
    public function isCropTopic(string $text): bool
    {
        $norm = $this->normalizeText($text);

        // Clear off-topic blockers
        if (preg_match('/\b(world\s*war|cricket|football|election|politics|bitcoin|movie|film|song|joke|history of|president)\b/u', $norm)) {
            return false;
        }

        // Crop aliases
        foreach ($this->knowledge['crops'] ?? [] as $id => $crop) {
            if (in_array($id, ['generic', 'other'], true)) {
                continue;
            }
            foreach ($crop['aliases'] ?? [] as $alias) {
                $a = $this->normalizeText((string) $alias);
                if ($a !== '' && str_contains($norm, $a)) {
                    return true;
                }
            }
        }

        $farmWords = [
            'fasal', 'crop', 'plant', 'pauda', 'pauday', 'patta', 'patte', 'patti', 'pattiyan',
            'leaf', 'leaves', 'keere', 'keera', 'pest', 'insect', 'bimari', 'disease', 'daag', 'dagh',
            'peela', 'peele', 'yellow', 'kala', 'kale', 'black', 'brown', 'sukh', 'murjha', 'wilt',
            'pani', 'water', 'mitti', 'soil', 'khad', 'fertilizer', 'beej', 'seed', 'jar', 'jarr',
            'root', 'ug', 'ugrah', 'grow', 'growing', 'masla', 'problem', 'sabzi', 'vegetable',
            'phal', 'fruit', 'farm', 'kisan', 'field', 'zameen', 'nami', 'baarish', 'mausam',
            'fungus', 'mold', 'rot', 'sarr', 'bulb', 'stem', 'tana', 'color', 'rang',
            'herb', 'herbs', 'jadi', 'booti', 'medicinal', 'tulsi', 'pudina', 'mint', 'neem',
            'methi', 'dhania', 'coriander', 'podina', 'aloe', 'lemongrass', 'basil',
        ];
        foreach ($farmWords as $w) {
            if (str_contains($norm, $w)) {
                return true;
            }
        }

        return false;
    }

    public function extract(string $text): array
    {
        $norm = $this->normalizeText($text);
        $out = [
            'crop' => null,
            'part' => null,
            'symptom' => null,
            'color' => null,
            'texture' => null,
            'spread' => null,
            'weather_hint' => null,
            'leaf_age' => null,
            'soil_moisture' => null,
            'dry_pattern' => null,
            'pest_seen' => null,
            'spot_color' => null,
            'light_exposure' => null,
        ];

        foreach ($this->knowledge['crops'] ?? [] as $id => $crop) {
            if (in_array($id, ['generic', 'other'], true)) {
                continue;
            }
            foreach ($crop['aliases'] ?? [] as $alias) {
                $a = $this->normalizeText((string) $alias);
                if ($a !== '' && (str_contains($norm, $a) || preg_match('/\b'.preg_quote($a, '/').'\b/u', $norm))) {
                    $out['crop'] = $id;
                    break 2;
                }
            }
        }

        // "black color ke", "kala rang", "dark color"
        if (preg_match('/\b(black|kala|kale|kaala|kaale|siyah|dark)\b/u', $norm)
            || preg_match('/(black|kala|kale).{0,12}(color|rang|colour)/u', $norm)
            || preg_match('/(color|rang|colour).{0,12}(black|kala|kale)/u', $norm)) {
            $out['color'] = 'black';
        }

        $colorMap = [
            'yellow' => ['yellow', 'peela', 'peele', 'peelay', 'pila', 'pile'],
            'green' => ['green', 'hara', 'hari'],
            'brown' => ['brown', 'bhura', 'bhure'],
            'white' => ['white', 'safed'],
            'black' => ['black', 'kala', 'kale', 'kaala', 'kaale', 'dark', 'siyah'],
        ];
        if (empty($out['color'])) {
            foreach ($colorMap as $color => $words) {
                foreach ($words as $w) {
                    if (str_contains($norm, $w)) {
                        $out['color'] = $color;
                        break 2;
                    }
                }
            }
        }

        $partMap = [
            'leaf' => ['pattay', 'patte', 'patta', 'pattiyan', 'leaf', 'leaves', 'tops'],
            'stem' => ['stem', 'tana', 'tane', 'gardan', 'neck'],
            'tuber_inside' => ['andar', 'inside'],
            'tuber_outside' => ['bahar', 'outside', 'skin'],
            'fruit' => ['phal', 'fruit', 'bulb', 'ganda'],
            'whole' => ['poora plant', 'whole plant', 'poora pauda'],
        ];
        foreach ($partMap as $part => $words) {
            foreach ($words as $w) {
                if (str_contains($norm, $w)) {
                    $out['part'] = $part;
                    break 2;
                }
            }
        }

        if (preg_match('/\b(soft|naram)\b/u', $norm)) {
            $out['texture'] = 'soft';
        } elseif (preg_match('/\b(hard|sakht)\b/u', $norm)) {
            $out['texture'] = 'hard';
        }

        // Symptom ids — order matters for specificity
        if (preg_match('/(keere?|insect|pest|sundi)/u', $norm)) {
            $out['symptom'] = 'pest';
        } elseif (preg_match('/(daag|dagh|spot)/u', $norm)) {
            $out['symptom'] = 'spots';
        } elseif (preg_match('/(sukh|dry|murjha|wilt)/u', $norm)) {
            $out['symptom'] = 'drying';
        } elseif (($out['color'] ?? null) === 'black') {
            $out['symptom'] = 'black_change';
        } elseif (($out['color'] ?? null) === 'yellow' || preg_match('/(peela|yellow)/u', $norm)) {
            $out['symptom'] = 'yellow_leaf';
            $out['color'] = $out['color'] ?? 'yellow';
            $out['part'] = $out['part'] ?? 'leaf';
        } elseif (preg_match('/(barh\s*nahi|nahi\s*barh|kamzor|stunted|not growing)/u', $norm)) {
            $out['symptom'] = 'yellow_leaf';
        } elseif (preg_match('/\b(ug|ugrah|grow|growing)\b/u', $norm) && ! empty($out['color'])) {
            // "ug rahe hain lekin black" → treat as color change on growing plant
            $out['symptom'] = ($out['color'] === 'black') ? 'black_change' : 'yellow_leaf';
            $out['part'] = $out['part'] ?? 'whole';
        }

        if (preg_match('/(baarish|rain)/u', $norm)) {
            $out['weather_hint'] = 'rain';
        } elseif (preg_match('/(garmi|heat)/u', $norm)) {
            $out['weather_hint'] = 'heat';
        }

        return array_filter($out, fn ($v) => $v !== null && $v !== '');
    }

    private function normalizeState(array $state): array
    {
        $keys = [
            'crop', 'part', 'symptom', 'color', 'texture', 'spread', 'weather_hint',
            'leaf_age', 'soil_moisture', 'dry_pattern', 'pest_seen', 'spot_color', 'light_exposure',
            'raw',
        ];
        $out = [];
        foreach ($keys as $k) {
            $out[$k] = $state[$k] ?? ($k === 'raw' ? '' : null);
        }
        $out['answers'] = is_array($state['answers'] ?? null) ? $state['answers'] : [];
        $out['photo'] = (bool) ($state['photo'] ?? false);
        $out['asked'] = is_array($state['asked'] ?? null) ? array_values($state['asked']) : [];
        $out['announce_crop'] = (bool) ($state['announce_crop'] ?? false);
        $out['followups_done'] = (int) ($state['followups_done'] ?? 0);
        $out['last_result'] = is_array($state['last_result'] ?? null) ? $state['last_result'] : null;

        return $out;
    }

    private function mergeExtraction(array $state, array $extracted): array
    {
        foreach ($extracted as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }
            if (empty($state[$key]) || in_array($key, ['part', 'color', 'symptom', 'texture', 'spread'], true)) {
                $state[$key] = $value;
            }
        }

        return $state;
    }

    private function applyAnswer(array $state, string $questionId, string $optionId): array
    {
        $state['answers'][$questionId] = $optionId;
        $state['asked'][] = $questionId;
        $state['followups_done'] = (int) ($state['followups_done'] ?? 0) + 1;

        if ($optionId === 'unknown') {
            return $state;
        }

        $map = [
            'crop' => 'crop',
            'part' => 'part',
            'texture' => 'texture',
            'spread' => 'spread',
            'spread_others' => 'spread',
            'leaf_age' => 'leaf_age',
            'soil_moisture' => 'soil_moisture',
            'dry_pattern' => 'dry_pattern',
            'pest_seen' => 'pest_seen',
            'spot_color' => 'spot_color',
            'light_exposure' => 'light_exposure',
        ];

        if ($questionId === 'crop') {
            $state['crop'] = $optionId === 'unknown' ? null : ($optionId === 'other' ? 'generic' : $optionId);
            $state['announce_crop'] = ! empty($state['crop']);
        } elseif ($questionId === 'part_generic') {
            $state['part'] = $optionId;
        } elseif (isset($map[$questionId])) {
            $state[$map[$questionId]] = $optionId;
        } else {
            // followup id may equal field name
            $state[$questionId] = $optionId;
        }

        return $state;
    }

    private function nextQuestion(array $state): ?array
    {
        $asked = $state['asked'] ?? [];

        // 1) Crop required if unknown
        if (empty($state['crop']) && ! in_array('crop', $asked, true)) {
            // Only ask crop after farmer said something, or if they answered nothing yet but we need crop
            if (! empty($state['raw']) || ! empty($state['symptom']) || ! empty($state['color'])) {
                return $this->knowledge['crop_picker'] ?? null;
            }

            return null; // UI starter handles empty open
        }

        if (($state['crop'] ?? null) === 'unknown') {
            $state['crop'] = null;
        }

        $cropId = $state['crop'] ?? null;
        if (! $cropId) {
            return null;
        }

        $crop = $this->knowledge['crops'][$cropId] ?? $this->knowledge['crops']['generic'] ?? null;
        if (! $crop) {
            return null;
        }

        $symptom = $this->resolveSymptom($crop, $state);
        if (! $symptom) {
            // Have crop but no symptom yet — ask open-ended via starter, or one generic part question
            if (! in_array('part_generic', $asked, true) && empty($state['part']) && empty($state['symptom']) && empty($state['color'])) {
                return [
                    'id' => 'part_generic',
                    'en' => 'Where do you see the problem?',
                    'ur' => 'Masla plant ke kis hisay mein hai?',
                    'roman' => 'Masla plant ke kis hisay mein hai?',
                    'options' => [
                        ['id' => 'leaf', 'en' => 'Leaves', 'ur' => 'Pattay', 'icon' => '🌿'],
                        ['id' => 'stem', 'en' => 'Stem', 'ur' => 'Tana', 'icon' => '🌱'],
                        ['id' => 'fruit', 'en' => 'Fruit / produce', 'ur' => 'Phal', 'icon' => '🍎'],
                        ['id' => 'whole', 'en' => 'Whole plant', 'ur' => 'Poora plant', 'icon' => '🪴'],
                        ['id' => 'unknown', 'en' => 'Pata nahi', 'ur' => 'Pata nahi', 'icon' => '❓'],
                    ],
                ];
            }

            return null;
        }

        // Max 2 follow-ups then answer (farmer-friendly)
        if ((int) ($state['followups_done'] ?? 0) >= 2) {
            return null;
        }

        foreach ($symptom['followups'] ?? [] as $fu) {
            $qid = $fu['id'] ?? null;
            $field = $fu['field'] ?? $qid;
            if (! $qid || in_array($qid, $asked, true)) {
                continue;
            }
            if (! empty($state[$field])) {
                continue;
            }
            if (! empty($fu['when']) && is_array($fu['when'])) {
                $ok = true;
                foreach ($fu['when'] as $k => $v) {
                    if (($state[$k] ?? null) !== $v) {
                        $ok = false;
                        break;
                    }
                }
                if (! $ok) {
                    continue;
                }
            }

            return $fu;
        }

        return null;
    }

    private function tooLittleInfo(array $state): bool
    {
        return empty($state['raw'])
            && empty($state['crop'])
            && empty($state['symptom'])
            && empty($state['color'])
            && empty($state['answers']);
    }

    private function localizeQuestion(array $question, string $lang): array
    {
        if ($lang === 'en') {
            $prompt = $question['en'] ?? $question['roman'] ?? $question['ur'] ?? '';
        } elseif ($lang === 'ur') {
            $prompt = $question['ur'] ?? $question['roman'] ?? $question['en'] ?? '';
        } else {
            $prompt = $question['roman'] ?? $question['ur'] ?? $question['en'] ?? '';
        }

        $options = [];
        foreach ($question['options'] ?? [] as $opt) {
            if ($lang === 'en') {
                $label = $opt['en'] ?? $opt['ur'] ?? '';
            } else {
                $label = $opt['ur'] ?? $opt['en'] ?? '';
            }
            $options[] = [
                'id' => $opt['id'],
                'label' => $label,
                'icon' => $opt['icon'] ?? '',
            ];
        }

        return [
            'id' => $question['id'] ?? 'q',
            'prompt' => $prompt,
            'options' => $options,
        ];
    }

    private function detectedSummary(array $state, string $lang): ?array
    {
        if (empty($state['crop']) || in_array($state['crop'], ['generic', 'other'], true)) {
            return null;
        }
        $crop = $this->knowledge['crops'][$state['crop']] ?? null;
        if (! $crop) {
            return null;
        }

        return [
            'crop_id' => $state['crop'],
            'name' => $lang === 'en' ? ($crop['name_en'] ?? '') : ($crop['name_ur'] ?? $crop['name_en'] ?? ''),
            'icon' => $crop['icon'] ?? '🌱',
            'color' => $state['color'] ?? null,
            'part' => $state['part'] ?? null,
            'symptom' => $state['symptom'] ?? null,
        ];
    }

    private function buildAskSpeak(array $state, string $prompt, string $lang): string
    {
        $parts = [];
        if (! empty($state['announce_crop']) && ! empty($state['crop'])) {
            $crop = $this->knowledge['crops'][$state['crop']] ?? null;
            $name = $lang === 'en'
                ? ($crop['name_en'] ?? 'your plant')
                : ($crop['name_ur'] ?? $crop['name_en'] ?? 'aapka plant');
            $parts[] = $lang === 'en'
                ? "Don’t worry — looks like {$name}. Let’s fix this step by step."
                : "Pareshan na hon — ye {$name} lag raha hai. Aaiye qadam ba qadam dekhte hain.";
        }
        $parts[] = $prompt;

        return trim(implode(' ', $parts));
    }

    private function buildResult(array $state, string $lang, ?array $weather): array
    {
        $cropId = $state['crop'] ?? 'generic';
        if ($cropId === 'other' || $cropId === 'unknown') {
            $cropId = 'generic';
        }
        $crop = $this->knowledge['crops'][$cropId] ?? $this->knowledge['crops']['generic'];
        $symptom = $this->resolveSymptom($crop, $state);

        $causes = [];
        $checklist = [];
        $prevention = [];
        $avoid = [];
        $nutrition = $lang === 'en'
            ? 'Nutrition is only one possibility — check water and visible symptoms before adding fertilizer.'
            : 'Nutrition sirf ek possibility hai — khad se pehle pani aur symptoms check karein.';
        $spread = false;
        $problem = $lang === 'en' ? 'Problem reported — needs checking' : 'Masla bataya gaya — check zaroori';

        if ($symptom) {
            $problem = $lang === 'en'
                ? ($symptom['label']['en'] ?? 'Observed symptoms')
                : ($symptom['label']['ur'] ?? $symptom['label']['en'] ?? 'Masla');
            foreach ($symptom['causes'] ?? [] as $c) {
                $causes[] = $lang === 'en' ? ($c['en'] ?? '') : ($c['ur'] ?? $c['en'] ?? '');
            }
            foreach ($symptom['checklist'] ?? [] as $item) {
                $checklist[] = [
                    'id' => $item['id'],
                    'label' => $lang === 'en' ? ($item['en'] ?? '') : ($item['ur'] ?? $item['en'] ?? ''),
                ];
            }
            foreach ($symptom['prevention'] ?? [] as $p) {
                $prevention[] = $lang === 'en' ? ($p['en'] ?? '') : ($p['ur'] ?? $p['en'] ?? '');
            }
            foreach ($symptom['avoid'] ?? [] as $a) {
                $avoid[] = $lang === 'en' ? ($a['en'] ?? '') : ($a['ur'] ?? $a['en'] ?? '');
            }
            $nutrition = $lang === 'en'
                ? ($symptom['nutrition_note']['en'] ?? $nutrition)
                : ($symptom['nutrition_note']['ur'] ?? $symptom['nutrition_note']['en'] ?? $nutrition);
            $spread = (bool) ($symptom['spread'] ?? false);
        }

        // Context refinements (still possible causes, not confirmation)
        if (($state['texture'] ?? null) === 'soft' && $cropId === 'potato') {
            array_unshift($causes, $lang === 'en'
                ? 'Soft inside could relate to soft rot / breakdown — separate and inspect carefully.'
                : 'Andar soft soft-rot/breakdown se related ho sakta hai — alag karke carefully dekhein.');
        }
        if (($state['light_exposure'] ?? null) === 'yes' && ($state['part'] ?? null) === 'tuber_outside') {
            array_unshift($causes, $lang === 'en'
                ? 'Light/sun exposure can change potato skin color — not automatically a disease.'
                : 'Dhoop/roshni se skin color change ho sakta hai — ye hamesha bimari nahi.');
        }
        if (($state['soil_moisture'] ?? null) === 'wet') {
            array_unshift($causes, $lang === 'en'
                ? 'Very wet soil may be relevant — check drainage.'
                : 'Bohat geeli mitti relevant ho sakti hai — drainage check karein.');
        }
        if (($state['soil_moisture'] ?? null) === 'dry') {
            array_unshift($causes, $lang === 'en'
                ? 'Very dry soil / water stress may be relevant.'
                : 'Bohat sukhi mitti / pani stress relevant ho sakta hai.');
        }

        if ($causes === []) {
            $causes[] = $lang === 'en'
                ? 'More than one cause is possible — water, nutrients, pests, disease, or weather stress. Further checking is needed.'
                : 'Kai wajah mumkin hain — pani, ghizaiyat, keere, bimari ya mausam. Mazeed check zaroori hai.';
        }

        $causes = array_values(array_unique(array_filter($causes)));
        $causes = array_slice($causes, 0, 4);

        $weatherNote = $this->weatherNote($weather, $state['weather_hint'] ?? null, $lang);
        $season = $lang === 'en'
            ? ($crop['season']['en'] ?? '')
            : ($crop['season']['ur'] ?? $crop['season']['en'] ?? '');

        $spreadWarning = null;
        if ($spread || ($state['spread'] ?? null) === 'yes') {
            $spreadWarning = $lang === 'en'
                ? 'In some conditions this problem may also appear on nearby plants. Monitor closely and keep field hygiene.'
                : 'Kuch conditions mein ye masla qareebi plants mein bhi ho sakta hai. Qareeb se dekhein aur field saaf rakhein.';
        }

        $photoNote = ! empty($state['photo'])
            ? ($lang === 'en'
                ? 'Photo received — helpful visual clue, not a guaranteed diagnosis.'
                : 'Photo mil gayi — madadgar hai, lekin guaranteed diagnosis nahi.')
            : null;

        if ($avoid === []) {
            $avoid = [
                $lang === 'en'
                    ? 'Do not apply random fertilizer/pesticide without checking.'
                    : 'Bina check kiye random khad/dawa na dein.',
            ];
        }

        $speak = $this->buildSpeak($crop, $problem, $causes, $lang);

        $routine = [
            'morning' => $lang === 'en'
                ? 'Quick look at leaves and new growth.'
                : 'Subah patte aur nayi growth dekhein.',
            'evening' => $lang === 'en'
                ? 'Check undersides of leaves for pests.'
                : 'Sham ko patton ke neeche keere check karein.',
            'every_few_days' => $lang === 'en'
                ? 'Water only when the top soil feels dry — avoid waterlogging.'
                : 'Upar ki mitti dry ho to hi pani dein — pani khara na hone dein.',
            'weekly' => $lang === 'en'
                ? 'Clear weeds/debris around plants; separate any soft or foul-smelling produce.'
                : 'Plant ke aas-paas saaf rakhein; soft/badbudar produce alag karein.',
        ];

        $stickyOffer = $lang === 'en'
            ? 'Would you like me to save this as a sticky note for you?'
            : 'Kya aap chahein ke main yeh sticky note bana doon? Bolain “haan” ya “save”.';

        return [
            'crop_id' => $cropId,
            'crop_name' => $lang === 'en' ? ($crop['name_en'] ?? 'Plant') : ($crop['name_ur'] ?? $crop['name_en'] ?? 'Plant'),
            'icon' => $crop['icon'] ?? '🌱',
            'advisor' => 'Green Leaf Advisor',
            'problem' => $problem,
            'causes' => $causes,
            'checklist' => $checklist,
            'prevention' => $prevention,
            'avoid' => $avoid,
            'nutrition' => $nutrition,
            'season' => $season,
            'weather' => $weatherNote,
            'spread_warning' => $spreadWarning,
            'photo_note' => $photoNote,
            'routine' => $routine,
            'sticky_offer' => $stickyOffer,
            'disclaimer' => $lang === 'en'
                ? 'Possible causes only — not a confirmed disease diagnosis. No random chemicals. Herbal remedies may help traditionally and are complementary — consult a doctor before internal use (especially if pregnant, on medication, or unwell).'
                : 'Sirf possible wajahain — confirmed bimari nahi. Random chemical na dein. Herbal remedies traditionally madadgar ho sakti hain (complementary) — andar istemal se pehle doctor se mashwara (khas kar pregnancy, dawaiyan, ya bimari).',
            'speak' => $speak,
            'sources' => $crop['sources'] ?? ($this->knowledge['meta']['sources'] ?? []),
        ];
    }

    private function resolveSymptom(array $crop, array $state): ?array
    {
        $symptoms = $crop['symptoms'] ?? [];
        $wanted = $state['symptom'] ?? null;

        if ($wanted) {
            foreach ($symptoms as $s) {
                if (($s['id'] ?? '') === $wanted) {
                    return $s;
                }
            }
            // Map legacy ids / color aliases
            $aliases = [
                'black_tuber' => 'black_change',
                'black' => 'black_change',
                'yellow' => 'yellow_leaf',
            ];
            $mapped = $aliases[$wanted] ?? null;
            if ($mapped) {
                foreach ($symptoms as $s) {
                    if (($s['id'] ?? '') === $mapped) {
                        return $s;
                    }
                }
            }
        }

        // Color fallback when symptom id missing on this crop
        if (($state['color'] ?? null) === 'black') {
            foreach ($symptoms as $s) {
                if (($s['id'] ?? '') === 'black_change') {
                    return $s;
                }
            }
        }
        if (($state['color'] ?? null) === 'yellow') {
            foreach ($symptoms as $s) {
                if (($s['id'] ?? '') === 'yellow_leaf') {
                    return $s;
                }
            }
        }

        $hay = $this->normalizeText(($state['raw'] ?? '').' '.($state['color'] ?? ''));
        foreach ($symptoms as $s) {
            foreach ($s['match'] ?? [] as $m) {
                if (str_contains($hay, $this->normalizeText((string) $m))) {
                    return $s;
                }
            }
        }

        // Last resort: pull shared black/yellow builders if crop module lacked them
        if (($state['color'] ?? null) === 'black' || $wanted === 'black_change') {
            $helpers = resource_path('crop-health/crops/_helpers.php');
            if (is_file($helpers)) {
                require_once $helpers;
                if (function_exists('cha_black_change_symptom')) {
                    return cha_black_change_symptom();
                }
            }
        }

        return null;
    }

    private function weatherNote(?array $weather, ?string $hint, string $lang): ?string
    {
        if (! $weather && ! $hint) {
            return null;
        }
        $rain = (int) ($weather['rain_chance'] ?? 0);
        $temp = isset($weather['temp']) ? (int) $weather['temp'] : null;

        if ($hint === 'rain' || $rain >= 50) {
            return $lang === 'en'
                ? 'Recent / likely rain may be relevant. Check soil moisture and drainage.'
                : 'Recent / mumkin barish relevant ho sakti hai. Mitti nami aur drainage check karein.';
        }
        if ($hint === 'heat' || ($temp !== null && $temp >= 36)) {
            return $lang === 'en'
                ? 'High temperature may be relevant. Check moisture and plant stress.'
                : 'Zyada garmi relevant ho sakti hai. Nami aur plant stress check karein.';
        }
        if ($weather) {
            return $lang === 'en'
                ? 'Local weather context is available — use it while checking moisture and stress.'
                : 'Local mausam ki maloomat maujood hain — nami/stress check mein use karein.';
        }

        return null;
    }

    private function buildSpeak(array $crop, string $problem, array $causes, string $lang): string
    {
        $name = $lang === 'en' ? ($crop['name_en'] ?? 'your plant') : ($crop['name_ur'] ?? 'aapka plant');
        $top = array_slice(array_values(array_filter($causes)), 0, 2);
        if ($lang === 'en') {
            return "Don’t worry — {$problem} on {$name} is common. Possible reasons: "
                .implode('. ', $top)
                .'. These are possibilities only, not a confirmed disease. Follow the simple steps on screen, then tell me if you want a sticky note.';
        }

        return "Pareshan na hon — {$name} mein “{$problem}” aksar hota hai. Mumkin wajahain: "
            .implode('. ', $top)
            .'. Ye confirmed bimari nahi. Screen par simple steps dekhein — sticky note chahiye to “haan” bolain.';
    }

    private function normalizeText(string $text): string
    {
        $text = mb_strtolower(trim($text), 'UTF-8');

        return preg_replace('/\s+/u', ' ', $text) ?: $text;
    }
}
