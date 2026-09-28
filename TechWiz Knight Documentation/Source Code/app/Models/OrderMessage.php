<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderMessage extends Model
{
    protected $fillable = [
        'order_id',
        'user_id',
        'guest_name',
        'body',
        'is_read',
    ];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function senderLabel(): string
    {
        if ($this->user_id) {
            return $this->sender->name ?? 'User';
        }

        return $this->guest_name ?: 'Guest';
    }

    public function isMine(?int $userId = null, bool $asGuest = false): bool
    {
        if ($asGuest) {
            return $this->user_id === null;
        }

        $uid = $userId ?? auth()->id();
        if (! $uid) {
            return false;
        }

        return (int) $this->user_id === (int) $uid;
    }
}
