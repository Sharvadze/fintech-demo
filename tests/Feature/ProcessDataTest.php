<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\File;

class ProcessDataTest extends TestCase
{
    /** @test */
    public function it_can_process_transactions()
    {
        $testData = "2014-12-31,4,private,withdraw,1200.00,EUR
            2015-01-01,4,private,withdraw,1000.00,EUR
            2016-01-05,4,private,withdraw,1000.00,EUR
            2016-01-05,1,private,deposit,200.00,EUR
            2016-01-06,2,business,withdraw,300.00,EUR
            2016-01-06,1,private,withdraw,30000,JPY
            2016-01-07,1,private,withdraw,1000.00,EUR
            2016-01-07,1,private,withdraw,100.00,USD
            2016-01-10,1,private,withdraw,100.00,EUR
            2016-01-10,2,business,deposit,10000.00,EUR
            2016-01-10,3,private,withdraw,1000.00,EUR
            2016-02-15,1,private,withdraw,300.00,EUR
            2016-02-19,5,private,withdraw,3000000,JPY";

        $filename = 'test.txt';
        File::put($filename, $testData);

        $expectedResults = [
            '0.60', // first withdrawal
            '3.00', // second withdrawal
            '0.00', // third withdrawal (weekly limit reached)
            '0.06', // deposit
            '1.50', // business withdrawal
            '0.69', // JPY withdrawal (not supported)
            '0.69', // EUR withdrawal
            '0.27', // USD withdrawal converted to EUR
            '0.30', // fourth withdrawal (weekly limit reached)
            '3.00', // business deposit
            '0.00', // fifth withdrawal
            '0.00', // sixth withdrawal
            '68.77', // seventh withdrawal (converted from JPY to EUR)
        ];

        $result = $this->app->call('\App\Http\Controllers\ProcessController@processData', ['file' => $filename]);
        $this->assertEquals($expectedResults, $result);

        File::delete($filename);
    }
}
