<?php

use App\Support\ProductionConfiguration;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('app:production-check', function (): int {
    $issues = ProductionConfiguration::currentIssues();

    if (! app()->isProduction()) {
        array_unshift($issues, 'APP_ENV must be production.');
    }

    if ($issues !== []) {
        $this->error('Production configuration is not ready.');

        foreach ($issues as $issue) {
            $this->line(" - {$issue}");
        }

        return 1;
    }

    $this->info('Production configuration is ready.');

    return 0;
})->purpose('Fail when internet-facing production settings are unsafe or incomplete');
