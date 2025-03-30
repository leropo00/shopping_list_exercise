<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\JsonDataService;


require_once __DIR__.'/../../app/defines.php';

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(JsonDataService::class, function ($app) {
            return new JsonDataService();
        });    
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
