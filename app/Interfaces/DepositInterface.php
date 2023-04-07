<?php

namespace App\Interfaces;

interface DepositInterface
{
    public function process(array $transaction);
}
