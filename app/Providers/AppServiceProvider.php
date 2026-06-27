<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (! $this->app['config']->has('academix') && file_exists(config_path('academix.php'))) {
            $this->app['config']->set('academix', require config_path('academix.php'));
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('ai', function (Request $request) {
            $key = optional($request->user())->id ?: $request->ip();

            return [
                Limit::perHour((int) config('services.gemini.rate_limit_per_hour', 10))->by($key),
            ];
        });
    }
}
