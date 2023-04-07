<?php

namespace App\Services;

use App\Interfaces\CurrencyInterface;

class CurrencyProcessor implements CurrencyInterface
{
    public static function convertToBase(float $amount, string $currency): float
    {
        $api = file_get_contents(config('bank.currency_api'));
        $json = json_decode($api, true);

        if (isset($json['rates'][$currency])) {
            return $amount / $json['rates'][$currency];
        }

        return 1;
    }
}
