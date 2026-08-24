<?php

namespace App\Providers;

use App\Support\ProductionConfiguration;
use Illuminate\Support\ServiceProvider;
use RuntimeException;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (! $this->app->isProduction() || $this->app->runningInConsole()) {
            return;
        }

        $issues = ProductionConfiguration::currentIssues();

        if ($issues !== []) {
            throw new RuntimeException(
                "Unsafe production configuration:\n- ".implode("\n- ", $issues)
            );
        }
    }
}
