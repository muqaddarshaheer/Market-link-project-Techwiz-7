<?php

namespace App\Http\Controllers;

use App\Models\ChatbotFaq;
use App\Models\FarmerProfile;
use App\Models\Market;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    public function ask(Request $request)
    {
        $message = $request->validate(['message' => ['required', 'string', 'max:500']])['message'];
        $faqs = ChatbotFaq::query()->take(12)->get();

        if (! $this->isMarketLinkTopic($message)) {
            return response()->json([
                'answer' => 'I only help with MarketLink — markets, farmers, produce, pickup, prices in Rs, accounts, and orders. Ask something about the platform.',
                'suggestions' => ['How does pickup work?', 'What costs Rs today?', 'Which farmers are open?', 'How do I create an account?'],
                'provider' => 'guard',
            ]);
        }

        $answer = $this->fromGroq($message)
            ?? $this->fromGemini($message)
            ?? $this->fromOpenAi($message)
            ?? $this->smartLocal($message, $faqs);

        $history = session('chat_history', []);
        $history[] = ['q' => $message, 'a' => $answer];
        session(['chat_history' => array_slice($history, -12)]);

        return response()->json([
            'answer' => $answer,
            'suggestions' => $faqs->take(4)->pluck('question')->values(),
            'provider' => $this->lastProvider,
        ]);
    }

    private string $lastProvider = 'local';

    private function isMarketLinkTopic(string $message): bool
    {
        $text = strtolower($message);

        if (preg_match('/^(hi|hello|hey|salam|assalam|aoa|thanks|thank you|shukriya)\b/u', $text)) {
            return true;
        }

        $needles = [
            'marketlink', 'market', 'farmer', 'stall', 'produce', 'product', 'pickup', 'pick up', 'collect',
            'order', 'cart', 'rs', 'rupee', 'price', 'pin', 'login', 'register', 'account', 'customer',
            'harvest', 'crop', 'tomato', 'honey', 'egg', 'bread', 'vegetable', 'fruit', 'organic',
            'delivery', 'payment', 'pay', 'reservation', 'reserve', 'cutoff', 'slot', 'quality',
            'admin', 'dashboard', 'favorite', 'review', 'contact', 'bazaar', 'farm',
        ];

        foreach ($needles as $word) {
            if (str_contains($text, $word)) {
                return true;
            }
        }

        // Live catalog match
        $token = strtok(preg_replace('/[^a-z0-9\s]/i', ' ', $text) ?: '', ' ');
        if ($token && strlen($token) > 2) {
            $hit = Product::query()->where('name', 'like', '%'.$token.'%')->exists();
            if ($hit) {
                return true;
            }
        }

        return false;
    }

    private function systemPrompt(): string
    {
        $catalog = Product::query()
            ->where('is_available', true)
            ->whereHas('farmer', fn ($q) => $q->where('approval_status', 'approved'))
            ->with(['farmer:id,stall_name', 'market:id,name'])
            ->take(14)
            ->get(['id', 'name', 'price', 'unit', 'quality', 'stock_quantity', 'farmer_id', 'market_id'])
            ->map(fn ($p) => $p->name.' · Rs '.number_format((float) $p->price, 0).'/'.$p->unit.' · '.$p->farmer->stall_name)
            ->implode("\n");

        $markets = Market::query()->where('status', 'active')->take(8)->pluck('name')->implode(', ');
        $farmers = FarmerProfile::query()->where('approval_status', 'approved')->take(8)->pluck('stall_name')->implode(', ');

        return 'You are MarketLink helper only. Answer ONLY about MarketLink: markets, farmers, produce, pickup, Rs prices, accounts, and orders. '
            .'If the question is off-topic (news, homework, jokes, coding, general knowledge), politely refuse and steer back to MarketLink. '
            .'Currency is Pakistani Rupees (Rs). Pickup and pay at the stall — no delivery, no online payment. Be short and clear. '
            .'Markets: '.$markets.'. Farmers: '.$farmers.". Catalog:\n".$catalog;
    }

    private function fromGroq(string $message): ?string
    {
        $key = config('services.groq.key') ?: env('GROQ_API_KEY');
        if (! $key) {
            return null;
        }

        try {
            $response = Http::withToken($key)
                ->timeout(10)
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => config('services.groq.model', 'llama-3.3-70b-versatile'),
                    'temperature' => 0.3,
                    'max_tokens' => 280,
                    'messages' => [
                        ['role' => 'system', 'content' => $this->systemPrompt()],
                        ['role' => 'user', 'content' => $message],
                    ],
                ]);

            if (! $response->successful()) {
                return null;
            }

            $text = trim((string) data_get($response->json(), 'choices.0.message.content'));
            if ($text !== '') {
                $this->lastProvider = 'groq';

                return $text;
            }
        } catch (\Throwable $e) {
            Log::warning('Groq chat error', ['message' => $e->getMessage()]);
        }

        return null;
    }

    private function fromGemini(string $message): ?string
    {
        $key = config('services.gemini.key') ?: env('GEMINI_API_KEY');
        if (! $key) {
            return null;
        }

        try {
            $model = config('services.gemini.model', 'gemini-2.0-flash');
            $response = Http::timeout(10)
                ->post('https://generativelanguage.googleapis.com/v1beta/models/'.$model.':generateContent?key='.$key, [
                    'contents' => [[
                        'parts' => [['text' => $this->systemPrompt()."\n\nUser: ".$message]],
                    ]],
                    'generationConfig' => ['temperature' => 0.3, 'maxOutputTokens' => 280],
                ]);

            if (! $response->successful()) {
                return null;
            }

            $text = trim((string) data_get($response->json(), 'candidates.0.content.parts.0.text'));
            if ($text !== '') {
                $this->lastProvider = 'gemini';

                return $text;
            }
        } catch (\Throwable $e) {
            Log::warning('Gemini chat error', ['message' => $e->getMessage()]);
        }

        return null;
    }

    private function fromOpenAi(string $message): ?string
    {
        $key = config('services.openai.key') ?: env('OPENAI_API_KEY');
        if (! $key) {
            return null;
        }

        try {
            $response = Http::withToken($key)
                ->timeout(12)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => config('services.openai.model', 'gpt-4o-mini'),
                    'temperature' => 0.3,
                    'max_tokens' => 280,
                    'messages' => [
                        ['role' => 'system', 'content' => $this->systemPrompt()],
                        ['role' => 'user', 'content' => $message],
                    ],
                ]);

            if (! $response->successful()) {
                return null;
            }

            $text = trim((string) data_get($response->json(), 'choices.0.message.content'));
            if ($text !== '') {
                $this->lastProvider = 'openai';

                return $text;
            }
        } catch (\Throwable $e) {
            Log::warning('OpenAI chat error', ['message' => $e->getMessage()]);
        }

        return null;
    }

    private function smartLocal(string $message, $faqs): string
    {
        $this->lastProvider = 'local';
        $text = strtolower(trim($message));

        if (preg_match('/^(hi|hello|hey|salam|assalam|aoa)\b/u', $text)) {
            return 'Hi — I help with MarketLink only: markets, farmers, produce, pickup, and Rs prices. What do you need?';
        }

        if (str_contains($text, 'pickup') || str_contains($text, 'collect') || str_contains($text, 'delivery')) {
            return 'MarketLink is pickup only. Reserve online, collect at the stall, and pay the farmer in person in Rs. No delivery, no online payment.';
        }

        if (str_contains($text, 'price') || str_contains($text, 'rs') || str_contains($text, 'rupee') || str_contains($text, 'cost')) {
            $items = Product::query()->where('is_available', true)->orderBy('price')->take(5)->get(['name', 'price', 'unit']);
            if ($items->isEmpty()) {
                return 'Prices are shown in Pakistani Rupees (Rs) on each product page.';
            }

            return "Live prices in Rs:\n".$items->map(fn ($p) => '• '.$p->name.' — Rs '.number_format((float) $p->price, 0).'/'.$p->unit)->implode("\n");
        }

        if (str_contains($text, 'farmer') || str_contains($text, 'stall')) {
            $list = FarmerProfile::query()->where('approval_status', 'approved')->take(5)->pluck('stall_name')->implode(', ');

            return $list !== ''
                ? 'Approved stalls: '.$list.'. Open Farmers to call or browse products.'
                : 'Farmer applications wait for admin approval before going live.';
        }

        if (str_contains($text, 'market') || str_contains($text, 'bazaar')) {
            $names = Market::query()->where('status', 'active')->take(6)->pluck('name')->implode(', ');

            return $names !== ''
                ? 'Active markets: '.$names.'. Check days on each market page before you reserve.'
                : 'Browse the Markets page for nearby pickup days.';
        }

        if (str_contains($text, 'pin') || str_contains($text, 'login') || str_contains($text, 'account') || str_contains($text, 'register')) {
            return 'Sign up with email + a 4-digit PIN. Customers shop right away; farmers need admin approval.';
        }

        $faq = $this->fromFaqs($message, $faqs);
        if ($faq !== null) {
            return $faq;
        }

        $token = strtok(preg_replace('/[^a-z0-9\s]/i', ' ', $text) ?: '', ' ');
        if ($token && strlen($token) > 2) {
            $product = Product::query()->where('is_available', true)->where('name', 'like', '%'.$token.'%')->with('farmer')->first();
            if ($product) {
                return $product->name.' is Rs '.number_format((float) $product->price, 0).'/'.$product->unit
                    .' from '.$product->farmer->stall_name.'. Open the product page to reserve pickup.';
            }
        }

        return 'Ask about MarketLink markets, farmers, produce, pickup, or Rs prices — that is what I can help with.';
    }

    private function fromFaqs(string $message, $faqs): ?string
    {
        $text = strtolower($message);
        $best = null;
        $score = 0;

        foreach ($faqs as $faq) {
            $points = 0;
            foreach ($faq->keywords ?? [] as $word) {
                if ($word && str_contains($text, strtolower($word))) {
                    $points += 2;
                }
            }
            similar_text($text, strtolower($faq->question), $percent);
            $points += $percent / 40;
            if ($points > $score) {
                $score = $points;
                $best = $faq;
            }
        }

        return ($best && $score >= 1.2) ? $best->answer : null;
    }
}
