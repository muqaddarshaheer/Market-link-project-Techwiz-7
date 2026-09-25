<?php

namespace App\Http\Controllers;

use App\Models\ChatbotFaq;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    public function ask(Request $request)
    {
        $message = $request->validate(['message' => ['required', 'string', 'max:500']])['message'];
        $faqs = ChatbotFaq::query()->take(8)->get();
        $answer = $this->fromOpenAi($message) ?? $this->fromFaqs($message, $faqs);

        $history = session('chat_history', []);
        $history[] = ['q' => $message, 'a' => $answer];
        session(['chat_history' => array_slice($history, -12)]);

        return response()->json([
            'answer' => $answer,
            'suggestions' => $faqs->take(4)->pluck('question')->values(),
        ]);
    }

    private function fromOpenAi(string $message): ?string
    {
        $key = config('services.openai.key') ?: env('OPENAI_API_KEY');
        if (! $key) {
            return null;
        }

        $catalog = Product::query()
            ->where('is_available', true)
            ->whereHas('farmer', fn ($q) => $q->where('approval_status', 'approved'))
            ->with(['farmer:id,stall_name', 'market:id,name'])
            ->take(12)
            ->get(['id', 'name', 'price', 'unit', 'quality', 'stock_quantity', 'farmer_id', 'market_id'])
            ->map(fn ($p) => $p->name.' · Rs '.number_format((float) $p->price, 0).'/'.$p->unit.' · '.$p->farmer->stall_name.' · '.$p->market->name)
            ->implode("\n");

        try {
            $response = Http::withToken($key)
                ->timeout(12)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => config('services.openai.model', 'gpt-4o-mini'),
                    'temperature' => 0.4,
                    'max_tokens' => 320,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You are MarketLink helper for a Pakistani farmers-market site. Currency is Pakistani Rupees (Rs). Customers pre-order, pick up at the stall, and pay the farmer in person. No delivery and no online payment. Answer clearly in simple English. If asked about products, use this catalog when useful:\n'.$catalog,
                        ],
                        ['role' => 'user', 'content' => $message],
                    ],
                ]);

            if (! $response->successful()) {
                Log::warning('OpenAI chat failed', ['status' => $response->status()]);

                return null;
            }

            return trim((string) data_get($response->json(), 'choices.0.message.content')) ?: null;
        } catch (\Throwable $e) {
            Log::warning('OpenAI chat error', ['message' => $e->getMessage()]);

            return null;
        }
    }

    private function fromFaqs(string $message, $faqs): string
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

        if ($best && $score >= 1.2) {
            return $best->answer;
        }

        return 'I can help with markets, farmers, produce, pickup, and prices in Rs. Add OPENAI_API_KEY in .env for fuller answers, or ask about market hours and pre-orders.';
    }
}
