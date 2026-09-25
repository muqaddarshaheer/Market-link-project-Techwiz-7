<?php

namespace App\Support;

class Money
{
    public static function pkr(mixed $amount): string
    {
        return 'Rs '.number_format((float) $amount, 0);
    }
}
