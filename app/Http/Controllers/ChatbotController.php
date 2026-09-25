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
        $message = $request->validate(['message' => ['required', 'string', 'max:800']])['message'];
        $faqs = ChatbotFaq::query()->take(12)->get();

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

        return 'You are MarketLink AI for a Pakistani farmers-market site. '
            .'Currency is Pakistani Rupees (Rs). Customers pre-order, pick up at the stall, and pay the farmer in person. '
            .'No delivery and no online payment. Answer any helpful question clearly in simple English (or Urdu if the user writes Urdu). '
            .'Be concise. Markets: '.$markets.'. Farmers: '.$farmers.". Catalog:\n".$catalog;
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
                    'temperature' => 0.5,
                    'max_tokens' => 420,
                    'messages' => [
                        ['role' => 'system', 'content' => $this->systemPrompt()],
                        ['role' => 'user', 'content' => $message],
                    ],
                ]);

            if (! $response->successful()) {
                Log::warning('Groq chat failed', ['status' => $response->status()]);

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
                    'generationConfig' => ['temperature' => 0.5, 'maxOutputTokens' => 420],
                ]);

            if (! $response->successful()) {
                Log::warning('Gemini chat failed', ['status' => $response->status()]);

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
                    'temperature' => 0.45,
                    'max_tokens' => 420,
                    'messages' => [
                        ['role' => 'system', 'content' => $this->systemPrompt()],
                        ['role' => 'user', 'content' => $message],
                    ],
                ]);

            if (! $response->successful()) {
                Log::warning('OpenAI chat failed', ['status' => $response->status()]);

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

        if (preg_match('/^(hi|hello|hey|salam|assalam|aoa)\b/', $text)) {
            return 'Hello! I am MarketLink AI. Ask about markets, farmers, produce prices in Rs, pickup, cooking tips, or anything else around fresh food.';
        }

        if (preg_match('/(\d+)\s*([+\-*\x{00d7}\/x])\s*(\d+)/u', $text, $m)) {
            $a = (float) $m[1];
            $b = (float) $m[3];
            $op = $m[2];
            $result = match ($op) {
                '+' => $a + $b,
                '-' => $a - $b,
                '*', 'x', '×' => $a * $b,
                '/' => $b == 0.0 ? null : $a / $b,
                default => null,
            };
            if ($result !== null) {
                return 'That works out to '.$result.'.';
            }
        }

        if (str_contains($text, 'pickup') || str_contains($text, 'collect') || str_contains($text, 'delivery')) {
            return 'MarketLink is pickup only. Reserve online, collect at the market stall, and pay the farmer in person in Rs. There is no delivery and no online payment.';
        }

        if (str_contains($text, 'price') || str_contains($text, 'rs') || str_contains($text, 'rupee') || str_contains($text, 'cost')) {
            $items = Product::query()->where('is_available', true)->orderBy('price')->take(5)->get(['name', 'price', 'unit']);
            if ($items->isEmpty()) {
                return 'Prices are shown in Pakistani Rupees (Rs) on each product page.';
            }

            return "Here are a few live prices in Rs:\n".$items->map(fn ($p) => '• '.$p->name.' — Rs '.number_format((float) $p->price, 0).'/'.$p->unit)->implode("\n");
        }

        if (str_contains($text, 'farmer') || str_contains($text, 'stall')) {
            $names = FarmerProfile::query()->where('approval_status', 'approved')->take(5)->get(['stall_name']);
            $list = $names->pluck('stall_name')->implode(', ');

            return $list !== ''
                ? 'Approved stalls right now include: '.$list.'. Open the Farmers page to call a stall or see products.'
                : 'Farmer applications are reviewed by admin before stalls go live.';
        }

        if (str_contains($text, 'market') || str_contains($text, 'bazaar')) {
            $names = Market::query()->where('status', 'active')->take(6)->pluck('name')->implode(', ');

            return $names !== ''
                ? 'Active markets: '.$names.'. Check operating days on each market page before you reserve.'
                : 'Browse the Markets page for nearby pickup days.';
        }

        if (str_contains($text, 'cook') || str_contains($text, 'recipe') || str_contains($text, 'how to')) {
            return 'Quick kitchen tip: rinse produce, keep roots cool and dry, and cook leafy greens the same day for best flavour. For MarketLink pickups, bring a bag and pay the farmer at the stall in Rs.';
        }

        if (str_contains($text, 'weather') || str_contains($text, 'rain') || str_contains($text, 'heat')) {
            return 'On hot or rainy market days, collect early in your pickup window so produce stays fresh. Farmers pack the morning of pickup — timing still matters.';
        }

        if (str_contains($text, 'pin') || str_contains($text, 'login') || str_contains($text, 'account')) {
            return 'Accounts use email + a 4-digit PIN. Create a customer account to track orders, or apply as a farmer (admin approval required).';
        }

        $faq = $this->fromFaqs($message, $faqs);
        if ($faq !== null) {
            return $faq;
        }

        $product = Product::query()
            ->where('is_available', true)
            ->where('name', 'like', '%'.strtok($text, ' ').'%')
            ->with('farmer')
            ->first();
        if ($product) {
            return $product->name.' is listed at Rs '.number_format((float) $product->price, 0).'/'.$product->unit
                .' from '.$product->farmer->stall_name.'. Open the product page to reserve a pickup.';
        }

        return 'I can help with markets, farmers, produce, Rs prices, pickup rules, cooking tips, and general questions. '
            .'Add GROQ_API_KEY, GEMINI_API_KEY, or OPENAI_API_KEY in .env for fuller AI answers. What would you like to know?';
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
