<?php

namespace App\Providers;

use App\Interfaces\CurrencyInterface;
use App\Interfaces\DepositInterface;
use App\Interfaces\WithdrawalInterface;
use App\Services\CurrencyProcessor;
use App\Services\DepositProcessor;
use App\Services\WithdrawalProcessor;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(WithdrawalInterface::class, WithdrawalProcessor::class);
        $this->app->bind(CurrencyInterface::class, CurrencyProcessor::class);
        $this->app->bind(DepositInterface::class, DepositProcessor::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
