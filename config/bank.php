<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Bank Defaults
    |--------------------------------------------------------------------------
    |
    | This options control the Bank's deposit, withdraw and commission fees
    |
    */


    'default_currency' => 'EUR',
    'deposit' => 0.03, //% percent

    'withdraw' => [
        'private' => [
            'fee' => 0.3, //% percent,
            'free_amount' => 1000, //i.e 1000.00 EUR for a week (from Monday to Sunday) is free of charge,
            'num_of_free_operations' => 3, //Number of free withdraw operations per a week
        ],
        'business' => [
            'fee' => 0.5, //% percent
        ],
    ],

    'commission_fee_round_up_dec' => 2, //Commission fees are rounded up to currency's decimal places. For example, 0.023 EUR should be rounded up to 0.03 EUR

    'currency_api' => 'https://developers.paysera.com/tasks/api/currency-exchange-rates', //Currency API endpoint
];
