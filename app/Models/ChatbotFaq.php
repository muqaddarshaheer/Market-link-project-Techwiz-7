<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatbotFaq extends Model
{
    protected $fillable = ['question', 'answer', 'category', 'keywords'];

    protected function casts(): array
    {
        return ['keywords' => 'array'];
    }
}
