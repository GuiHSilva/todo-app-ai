<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Libraries\ApiContract;
use App\Libraries\GeminiAPI;
use App\Services\GenerateTodoSuggestions;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ApiContract::class, function ($app) {
            return new GeminiAPI(new \GeminiAPI\Client(config('services.gemini.api_key')));
        });

        $this->app->bind(GenerateTodoSuggestions::class, function ($app) {
            return new GenerateTodoSuggestions(
                $app->make(ApiContract::class)
            );
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
