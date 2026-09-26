<?php

namespace App\Services;

use App\Models\AppNotification;
use App\Models\User;

class NotificationService
{
    public function send(User $user, string $type, string $title, string $message, array $data = []): AppNotification
    {
        return AppNotification::query()->create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'is_read' => false,
            'created_at' => now(),
        ]);
    }
}
