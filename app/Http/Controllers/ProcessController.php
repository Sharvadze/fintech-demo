<?php

namespace App\Http\Controllers;

use App\Interfaces\DepositInterface;
use App\Interfaces\WithdrawalInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class ProcessController extends Controller
{
    public function __construct(
        protected Request $request,
        protected WithdrawalInterface $withdrawal,
        protected DepositInterface $deposit,
    ) {
    }

    /**
     * Check if the CSV contains strictly 6 columns
     */
    private static function validateCSVFormat($file): bool
    {
        foreach (File::lines($file) as $k=> $v) {
            $cnt = explode(',', $v);
            if (count($cnt) != 6) {
                return false;
            }
        }

        return true;
    }

    /**
     * Validate file type and run basic validation on file format.
     * Technicly this should be a seperate class with more complex validation logic, but since this is just a dummy demo, I'll skip that :)
     */

    private function validateCSV(): array|bool
    {
        $validator = Validator::make($this->request->all(), [
            'csv' => ['required', 'mimes:csv,txt'],
        ]);

        if ($validator->fails()) {
            return ['result' => false, 'msg' => 'Incorrect file type, please use the CSV'];
        }

        if (!self::validateCSVFormat($this->request->file('csv'))) {
            return ['result' => false, 'msg' => 'File format is not correct, it should have 6 columns'];
        }

        return true;
    }

    /**
     * Handle the file upload
     */
    public function uploadCSV()
    {
        $validated = $this->validateCSV();
        if ($validated !== true) {
            return $validated['msg'];
        }

        $results = $this->processData($this->request->file('csv'));

        foreach ($results as $k => $v) {
            echo $v.'<br>';
        }
    }


    /**
     * Process the file data and return results in array
     */
    public function processData($file)
    {
        $results = [];
        $data = File::lines($file);

        foreach ($data as $k=> $v) {
            $transaction = explode(',', $v);

            //split the process by operation
            switch($transaction[3]) {
                case 'withdraw':
                    $results[] = number_format($this->withdrawal->process($transaction, $data->toArray()), config('bank.commission_fee_round_up_dec'));
                    break;

                case 'deposit':
                    $results[] = number_format($this->deposit->process($transaction), config('bank.commission_fee_round_up_dec'));
                    break;
            }
        }

        return $results;
    }
}
