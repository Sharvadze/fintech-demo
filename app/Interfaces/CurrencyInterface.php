<?php

namespace App\Interfaces;

interface CurrencyInterface
{
    public static function convertToBase(float $amount, string $currency): float;
}
