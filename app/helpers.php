<?php

use App\Support\Money;

if (! function_exists('money')) {
    function money(mixed $amount): string
    {
        return Money::pkr($amount);
    }
}
