<?php

use App\Models\User;
use App\Support\ProductionConfiguration;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;

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

Artisan::command('staff:create {username} {--name=} {--email=}', function (string $username): int {
    if (preg_match('/\A[a-zA-Z0-9._-]{3,40}\z/', $username) !== 1) {
        $this->error('Use 3 to 40 letters, numbers, periods, underscores, or hyphens for the username.');

        return 1;
    }

    $email = strtolower((string) ($this->option('email') ?: $username.'@local.invalid'));

    if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        $this->error('Enter a valid email address when using --email.');

        return 1;
    }

    $name = trim((string) ($this->option('name') ?: $this->ask('Staff name')));

    if ($name === '') {
        $this->error('The staff name is required.');

        return 1;
    }

    $minimumPasswordLength = app()->isLocal() ? 8 : 12;
    $password = (string) $this->secret("Password (at least {$minimumPasswordLength} characters)");
    $confirmation = (string) $this->secret('Confirm password');

    if (strlen($password) < $minimumPasswordLength) {
        $this->error("The password must contain at least {$minimumPasswordLength} characters.");

        return 1;
    }

    if (! hash_equals($password, $confirmation)) {
        $this->error('The passwords do not match.');

        return 1;
    }

    $user = User::query()->updateOrCreate(
        ['username' => $username],
        [
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'is_admin' => true,
        ],
    );

    $this->info("Administrator ready: {$user->username}");

    return 0;
})->purpose('Create or update a PAMO staff administrator without exposing a password');
