<?php

namespace App\Providers;

use App\Contracts\NumeradorFactura;
use App\Services\NumeradorFacturaSecuencial;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(NumeradorFactura::class, NumeradorFacturaSecuencial::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
