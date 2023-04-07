<?php

namespace App\Services;

use App\Interfaces\CurrencyInterface;
use App\Interfaces\WithdrawalInterface as InterfacesWithdrawalInterface;
use DateTime;

class WithdrawalProcessor implements InterfacesWithdrawalInterface
{
    public function __construct(
        protected CurrencyInterface $currency,
    ) {
    }

    public function process(array $transaction, array $data)
    {
        switch($transaction[2]) {
            case 'private':
                return $this->getPrivateCustomerFee($transaction, $data);
                break;

            case 'business':
                return $this->getBusinessCustomerFee($transaction);
                break;
        }
    }

    /**
     * Get private customer withdrawal fee.
     * Idea is to compute the balance based on the used amount within the week and apply the commision rate.
     */

    public function getPrivateCustomerFee(array $transaction, array $data): float
    {
        $userId = $transaction[1];
        $transactionDate = $transaction[0];
        $transactionCurrency = $transaction[5];
        $transactionAmount = $transaction[4];

        $amount = $transactionCurrency == config('bank.default_currency') ? floatval($transactionAmount) : $this->currency::convertToBase(floatval($transactionAmount), $transactionCurrency);

        $withdrawalsThisWeek = $this->getWithdrawalsThisWeek($userId, $transactionDate, $amount, $data);
        $weeklyBalance = config('bank.withdraw.private.free_amount') - $withdrawalsThisWeek['total']; //remaning weekly limit
        $remaningWeeklyBalance = $weeklyBalance < 0 ? 0 : $weeklyBalance;

        $balance = $amount - $remaningWeeklyBalance;

        switch($balance) {
            case 0:
                return 0;
                break;

            case $balance > 0:
                return $balance / 100 * config('bank.withdraw.private.fee');
                break;

            default:
                return 0;
                break;
        }
    }


    /**
     * Collect user transactions for the week and return an array with a count and total amount
     */

    private function getWithdrawalsThisWeek(int $userId, string $date, float $amount, array $data): array
    {
        $count = 0;
        $total = 0;
        $weekStart = date('Y-m-d', strtotime('monday this week', strtotime($date)));
        $weekEnd = date('Y-m-d', strtotime('sunday this week', strtotime($date)));

        foreach ($data as $k=> $v) {
            $transaction = explode(',', $v);
            $transactionType = $transaction[3];
            $transactionUser = $transaction[1];
            $transactionAmount = $transaction[4];
            $transactionCurrency = $transaction[5];
            $transactionDate = date('Y-m-d', strtotime($transaction[0]));

            if ($transactionDate >= $weekStart && $transactionDate <= $weekEnd && $transactionType === 'withdraw' && $userId == $transactionUser && $count <= config('bank.withdraw.private.num_of_free_operations')) {
                //if this is the current transaction - stop and exlude it (ideally transaction id should be used)
                if ($transactionDate == date('Y-m-d', strtotime($date)) && $transactionAmount == $amount) {
                    return ['count' => $count, 'total' => $total];
                }

                $total += $transactionCurrency == config('bank.default_currency') ? floatval($transactionAmount) : $this->currency::convertToBase(floatval($transactionAmount), $transactionCurrency);
                $count++;
            }
        }

        return ['count' => $count, 'total' => $total];
    }

    public function getBusinessCustomerFee(array $transaction)
    {
        $amount = floatval($transaction[4]);
        return $amount / 100 * config('bank.withdraw.business.fee');
    }
}
