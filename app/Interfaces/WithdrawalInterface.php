<?php

namespace App\Interfaces;

interface WithdrawalInterface
{
    public function process(array $transaction, array $data);

    //public function getPrivateCustomerFee(array $transaction, array $data);
}
