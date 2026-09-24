<?php

namespace App\Http\Controllers;

use App\Models\ChatbotFaq;
use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    public function ask(Request $request)
    {
        $text = strtolower($request->validate(['message' => ['required', 'string', 'max:300']])['message']);
        $faqs = ChatbotFaq::query()->get();
        $best = null;
        $score = 0;

        foreach ($faqs as $faq) {
            $points = 0;
            foreach ($faq->keywords ?? [] as $word) {
                if ($word && str_contains($text, strtolower($word))) {
                    $points += 2;
                }
            }
            if (str_contains(strtolower($faq->question), $text) || str_contains($text, strtolower(strtok($faq->question, ' ')))) {
                $points += 1;
            }
            similar_text($text, strtolower($faq->question), $percent);
            $points += $percent / 40;
            if ($points > $score) {
                $score = $points;
                $best = $faq;
            }
        }

        $history = session('chat_history', []);
        $answer = ($best && $score >= 1.2)
            ? $best->answer
            : 'I can help with market hours, pickup, farmer stalls, and how pre-orders work. Try one of the suggested questions.';

        $history[] = ['q' => $request->message, 'a' => $answer];
        session(['chat_history' => array_slice($history, -12)]);

        return response()->json([
            'answer' => $answer,
            'suggestions' => $faqs->take(4)->pluck('question'),
        ]);
    }
}
