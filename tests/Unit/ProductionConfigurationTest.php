<?php

namespace Tests\Unit;

use App\Support\ProductionConfiguration;
use PHPUnit\Framework\TestCase;

class ProductionConfigurationTest extends TestCase
{
    public function test_it_accepts_a_hardened_production_configuration(): void
    {
        $this->assertSame([], ProductionConfiguration::issues([
            'app_key' => 'base64:valid-key-material',
            'app_debug' => false,
            'app_url' => 'https://park.gov.ph',
            'mail_default' => 'smtp',
            'mail_scheme' => 'smtp',
            'mail_host' => 'smtp.gov.ph',
            'mail_from' => 'noreply@park.gov.ph',
            'session_secure' => true,
            'trusted_hosts' => ['^park\.gov\.ph$'],
            'database_connection' => 'mysql',
            'database_password' => 'not-a-placeholder',
        ]));
    }

    public function test_it_reports_unsafe_production_placeholders(): void
    {
        $issues = ProductionConfiguration::issues([
            'app_key' => '',
            'app_debug' => true,
            'app_url' => 'http://localhost',
            'mail_default' => 'log',
            'mail_host' => '127.0.0.1',
            'mail_from' => 'hello@example.com',
            'session_secure' => false,
            'trusted_hosts' => [],
            'database_connection' => 'mysql',
            'database_password' => 'CHANGE_ME',
        ]);

        $this->assertCount(8, $issues);
        $this->assertContains('APP_DEBUG must be false.', $issues);
        $this->assertContains('MAIL_MAILER must deliver through an external mail transport.', $issues);
        $this->assertContains('SESSION_SECURE_COOKIE must be true.', $issues);
    }

    public function test_it_rejects_an_unsupported_smtp_scheme(): void
    {
        $issues = ProductionConfiguration::issues([
            'app_key' => 'base64:valid-key-material',
            'app_debug' => false,
            'app_url' => 'https://park.gov.ph',
            'mail_default' => 'smtp',
            'mail_scheme' => 'tls',
            'mail_host' => 'smtp.gov.ph',
            'mail_from' => 'noreply@park.gov.ph',
            'session_secure' => true,
            'trusted_hosts' => ['^park\.gov\.ph$'],
            'database_connection' => 'mysql',
            'database_password' => 'not-a-placeholder',
        ]);

        $this->assertSame(
            ['MAIL_SCHEME must be smtp, smtps, or empty for automatic selection.'],
            $issues,
        );
    }
}
