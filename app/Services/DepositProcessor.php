<?php

namespace App\Services;

use App\Interfaces\DepositInterface;

class DepositProcessor implements DepositInterface
{
    public function process(array $transaction)
    {
        return $transaction[4] / 100 * config('bank.deposit');
    }
}
