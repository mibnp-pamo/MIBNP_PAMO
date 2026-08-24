<?php

namespace App\Support;

class ProductionConfiguration
{
    /**
     * @return array<int, string>
     */
    public static function currentIssues(): array
    {
        $mailer = (string) config('mail.default');

        return self::issues([
            'app_key' => config('app.key'),
            'app_debug' => config('app.debug'),
            'app_url' => config('app.url'),
            'mail_default' => $mailer,
            'mail_scheme' => config("mail.mailers.{$mailer}.scheme"),
            'mail_host' => config("mail.mailers.{$mailer}.host"),
            'mail_from' => config('mail.from.address'),
            'session_secure' => config('session.secure'),
            'trusted_hosts' => config('http.trusted_hosts', []),
            'database_connection' => config('database.default'),
            'database_password' => config('database.connections.mysql.password'),
        ]);
    }

    /**
     * @param  array<string, mixed>  $settings
     * @return array<int, string>
     */
    public static function issues(array $settings): array
    {
        $issues = [];
        $appUrl = (string) ($settings['app_url'] ?? '');
        $appHost = parse_url($appUrl, PHP_URL_HOST);
        $appScheme = parse_url($appUrl, PHP_URL_SCHEME);
        $mailer = (string) ($settings['mail_default'] ?? '');
        $mailScheme = (string) ($settings['mail_scheme'] ?? '');
        $mailFrom = (string) ($settings['mail_from'] ?? '');
        $mailHost = (string) ($settings['mail_host'] ?? '');
        $databasePassword = (string) ($settings['database_password'] ?? '');

        if (trim((string) ($settings['app_key'] ?? '')) === '') {
            $issues[] = 'APP_KEY must be generated.';
        }

        if (($settings['app_debug'] ?? false) === true) {
            $issues[] = 'APP_DEBUG must be false.';
        }

        if ($appScheme !== 'https' || ! is_string($appHost) || $appHost === '') {
            $issues[] = 'APP_URL must be a complete HTTPS URL.';
        } elseif (
            in_array($appHost, ['localhost', '127.0.0.1'], true)
            || str_ends_with($appHost, '.example')
            || str_contains($appHost, '.example.')
        ) {
            $issues[] = 'APP_URL must use the real public hostname.';
        }

        if (in_array($mailer, ['', 'array', 'log'], true)) {
            $issues[] = 'MAIL_MAILER must deliver through an external mail transport.';
        }

        if ($mailer === 'smtp' && (
            $mailHost === ''
            || $mailHost === '127.0.0.1'
            || str_contains($mailHost, '.example.')
        )) {
            $issues[] = 'MAIL_HOST must identify the production SMTP service.';
        }

        if ($mailer === 'smtp' && ! in_array($mailScheme, ['', 'smtp', 'smtps'], true)) {
            $issues[] = 'MAIL_SCHEME must be smtp, smtps, or empty for automatic selection.';
        }

        if (
            $mailFrom === ''
            || str_ends_with($mailFrom, '@example.com')
            || str_contains($mailFrom, '@park.example.')
        ) {
            $issues[] = 'MAIL_FROM_ADDRESS must use an authorized production sender.';
        }

        if (($settings['session_secure'] ?? false) !== true) {
            $issues[] = 'SESSION_SECURE_COOKIE must be true.';
        }

        if (($settings['trusted_hosts'] ?? []) === []) {
            $issues[] = 'At least one exact trusted host must be configured.';
        }

        if (
            ($settings['database_connection'] ?? null) === 'mysql'
            && in_array($databasePassword, ['', 'CHANGE_ME'], true)
        ) {
            $issues[] = 'DB_PASSWORD must be replaced with a production secret.';
        }

        return $issues;
    }
}
