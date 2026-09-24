<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatbotFaq extends Model
{
    use HasFactory;

    protected $fillable = ['question', 'answer', 'category', 'keywords'];

    protected function casts(): array
    {
        return [
            'keywords' => 'array',
        ];
    }

    /**
     * Find the best FAQ match for a user query using simple keyword scoring.
     */
    public static function matchQuery(string $query): ?self
    {
        $query = strtolower(trim($query));
        $faqs = self::all();
        $best = null;
        $bestScore = 0;

        foreach ($faqs as $faq) {
            $score = 0;
            $keywords = array_map('strtolower', $faq->keywords ?? []);
            $keywords[] = strtolower($faq->question);

            foreach ($keywords as $keyword) {
                if ($keyword !== '' && str_contains($query, $keyword)) {
                    $score += strlen($keyword);
                }
            }

            if ($score > $bestScore) {
                $bestScore = $score;
                $best = $faq;
            }
        }

        return $bestScore > 0 ? $best : null;
    }
}
