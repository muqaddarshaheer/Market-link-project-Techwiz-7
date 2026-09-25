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
        $data = $request->validate([
            'message' => ['required', 'string', 'max:500'],
            'lang' => ['nullable', 'in:en,ur'],
        ]);
        $message = $data['message'];
        $lang = $data['lang'] ?? 'en';
        $faqs = ChatbotFaq::query()->take(12)->get();

        if (! $this->isMarketLinkTopic($message)) {
            return response()->json([
                'answer' => $lang === 'ur'
                    ? 'میں صرف MarketLink میں مدد کرتا ہوں — مارکیٹ، کسان، پیداوار، پک اپ، روپے کی قیمتیں، اکاؤنٹ اور آرڈرز۔ پلیٹ فارم کے بارے میں پوچھیں۔'
                    : 'I only help with MarketLink — markets, farmers, produce, pickup, prices in Rs, accounts, and orders. Ask something about the platform.',
                'suggestions' => $lang === 'ur'
                    ? ['پک اپ کیسے ہوتا ہے؟', 'آج قیمتیں کیا ہیں؟', 'کون سے کسان کھلے ہیں؟', 'اکاؤنٹ کیسے بنائیں؟']
                    : ['How does pickup work?', 'What costs Rs today?', 'Which farmers are open?', 'How do I create an account?'],
                'provider' => 'guard',
            ]);
        }

        $answer = $this->fromGroq($message, $lang)
            ?? $this->fromGemini($message, $lang)
            ?? $this->fromOpenAi($message, $lang)
            ?? $this->smartLocal($message, $faqs, $lang);

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
            // Urdu / Roman Urdu
            'مارکیٹ', 'کسان', 'پیداوار', 'پک اپ', 'آرڈر', 'قیمت', 'روپے', 'اکاؤنٹ', 'لاگ ان',
            'منڈی', 'سبزی', 'پھل', 'ادائیگی', 'جمع', 'رزرو',
            'kisaan', 'mandi', 'qeemat', 'rupay', 'account', 'pickup', 'sabzi', 'phal',
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

    private function systemPrompt(string $lang = 'en'): string
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

        $language = $lang === 'ur'
            ? 'Reply in clear Urdu (Urdu script). Keep MarketLink English names as-is when useful.'
            : 'Reply in clear simple English.';

        return 'You are MarketLink helper only. Answer ONLY about MarketLink: markets, farmers, produce, pickup, Rs prices, accounts, and orders. '
            .'If the question is off-topic, politely refuse and steer back to MarketLink. '
            .$language.' '
            .'Currency is Pakistani Rupees (Rs). Pickup and pay at the stall — no delivery, no online payment. Be short and clear. '
            .'Markets: '.$markets.'. Farmers: '.$farmers.". Catalog:\n".$catalog;
    }

    private function fromGroq(string $message, string $lang = 'en'): ?string
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
                        ['role' => 'system', 'content' => $this->systemPrompt($lang)],
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

    private function fromGemini(string $message, string $lang = 'en'): ?string
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
                        'parts' => [['text' => $this->systemPrompt($lang)."\n\nUser: ".$message]],
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

    private function fromOpenAi(string $message, string $lang = 'en'): ?string
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
                        ['role' => 'system', 'content' => $this->systemPrompt($lang)],
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

    private function smartLocal(string $message, $faqs, string $lang = 'en'): string
    {
        $this->lastProvider = 'local';
        $ur = $lang === 'ur';
        $text = strtolower(trim($message));

        if (preg_match('/^(hi|hello|hey|salam|assalam|aoa|السلام|سلام)\b/u', $text)) {
            return $ur
                ? 'السلام علیکم — میں صرف MarketLink میں مدد کرتا ہوں: مارکیٹ، کسان، پیداوار، پک اپ اور روپے کی قیمتیں۔ کیا پوچھنا ہے؟'
                : 'Hi — I help with MarketLink only: markets, farmers, produce, pickup, and Rs prices. What do you need?';
        }

        if (str_contains($text, 'pickup') || str_contains($text, 'collect') || str_contains($text, 'delivery')
            || str_contains($text, 'پک اپ') || str_contains($text, 'جمع')) {
            return $ur
                ? 'MarketLink صرف پک اپ ہے۔ آن لائن ریزرو کریں، سٹال پر جائیں، اور کسان کو روپے میں ادا کریں۔ ڈیلیوری یا آن لائن ادائیگی نہیں۔'
                : 'MarketLink is pickup only. Reserve online, collect at the stall, and pay the farmer in person in Rs. No delivery, no online payment.';
        }

        if (str_contains($text, 'price') || str_contains($text, 'rs') || str_contains($text, 'rupee') || str_contains($text, 'cost')
            || str_contains($text, 'قیمت') || str_contains($text, 'روپے')) {
            $items = Product::query()->where('is_available', true)->orderBy('price')->take(5)->get(['name', 'price', 'unit']);
            if ($items->isEmpty()) {
                return $ur
                    ? 'قیمتیں ہر پروڈکٹ پیج پر پاکستانی روپے (Rs) میں لکھی ہوتی ہیں۔'
                    : 'Prices are shown in Pakistani Rupees (Rs) on each product page.';
            }

            $lines = $items->map(fn ($p) => '• '.$p->name.' — Rs '.number_format((float) $p->price, 0).'/'.$p->unit)->implode("\n");

            return ($ur ? "آج کی قیمتیں (Rs):\n" : "Live prices in Rs:\n").$lines;
        }

        if (str_contains($text, 'farmer') || str_contains($text, 'stall') || str_contains($text, 'کسان') || str_contains($text, 'kisaan')) {
            $list = FarmerProfile::query()->where('approval_status', 'approved')->take(5)->pluck('stall_name')->implode(', ');

            return $list !== ''
                ? ($ur ? 'منظور شدہ سٹالز: '.$list.'۔ Farmers صفحہ کھول کر کال یا پروڈکٹس دیکھیں۔' : 'Approved stalls: '.$list.'. Open Farmers to call or browse products.')
                : ($ur ? 'کسان کی درخواستیں ایڈمن کی منظوری کے بعد لائیو ہوتی ہیں۔' : 'Farmer applications wait for admin approval before going live.');
        }

        if (str_contains($text, 'market') || str_contains($text, 'bazaar') || str_contains($text, 'مارکیٹ') || str_contains($text, 'منڈی')) {
            $names = Market::query()->where('status', 'active')->take(6)->pluck('name')->implode(', ');

            return $names !== ''
                ? ($ur ? 'فعال مارکیٹس: '.$names.'۔ ریزرو سے پہلے دن چیک کریں۔' : 'Active markets: '.$names.'. Check days on each market page before you reserve.')
                : ($ur ? 'قریبی پک اپ دنوں کے لیے Markets صفحہ دیکھیں۔' : 'Browse the Markets page for nearby pickup days.');
        }

        if (str_contains($text, 'pin') || str_contains($text, 'login') || str_contains($text, 'account') || str_contains($text, 'register')
            || str_contains($text, 'اکاؤنٹ') || str_contains($text, 'لاگ ان')) {
            return $ur
                ? 'ای میل + 4 ہندسوں کا PIN سے سائن اپ کریں۔ کسٹمر فوراً خرید سکتا ہے؛ کسان کو ایڈمن منظوری درکار ہے۔'
                : 'Sign up with email + a 4-digit PIN. Customers shop right away; farmers need admin approval.';
        }

        $faq = $this->fromFaqs($message, $faqs);
        if ($faq !== null) {
            return $faq;
        }

        $token = strtok(preg_replace('/[^a-z0-9\s]/i', ' ', $text) ?: '', ' ');
        if ($token && strlen($token) > 2) {
            $product = Product::query()->where('is_available', true)->where('name', 'like', '%'.$token.'%')->with('farmer')->first();
            if ($product) {
                $line = $product->name.' Rs '.number_format((float) $product->price, 0).'/'.$product->unit.' — '.$product->farmer->stall_name;

                return $ur
                    ? $line.'۔ پک اپ ریزرو کرنے کے لیے پروڈکٹ پیج کھولیں۔'
                    : $line.'. Open the product page to reserve pickup.';
            }
        }

        return $ur
            ? 'MarketLink مارکیٹ، کسان، پیداوار، پک اپ یا روپے کی قیمتوں کے بارے میں پوچھیں — یہی میری مدد ہے۔'
            : 'Ask about MarketLink markets, farmers, produce, pickup, or Rs prices — that is what I can help with.';
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
