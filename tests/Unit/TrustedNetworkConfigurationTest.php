<?php

namespace Tests\Unit;

use App\Support\TrustedNetworkConfiguration;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class TrustedNetworkConfigurationTest extends TestCase
{
    public function test_it_builds_exact_host_patterns(): void
    {
        $patterns = TrustedNetworkConfiguration::hostPatterns(
            'park.example.gov.ph,www.park.example.gov.ph',
            'https://ignored.example',
        );

        $this->assertSame([
            '^park\.example\.gov\.ph$',
            '^www\.park\.example\.gov\.ph$',
        ], $patterns);
        $this->assertSame(1, preg_match('/'.$patterns[0].'/', 'park.example.gov.ph'));
        $this->assertSame(0, preg_match('/'.$patterns[0].'/', 'evil.park.example.gov.ph'));
    }

    public function test_it_falls_back_to_the_application_url_host(): void
    {
        $this->assertSame(
            ['^park\.example\.gov\.ph$'],
            TrustedNetworkConfiguration::hostPatterns(null, 'https://park.example.gov.ph/visit'),
        );
    }

    public function test_it_parses_explicit_proxy_addresses(): void
    {
        $this->assertSame(
            ['192.0.2.10', '198.51.100.0/24'],
            TrustedNetworkConfiguration::proxyAddresses('192.0.2.10, 198.51.100.0/24'),
        );
    }

    public function test_it_rejects_wildcard_proxies(): void
    {
        $this->expectException(InvalidArgumentException::class);

        TrustedNetworkConfiguration::proxyAddresses('*');
    }
}
