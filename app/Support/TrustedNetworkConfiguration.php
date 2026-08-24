<?php

namespace App\Support;

use InvalidArgumentException;

class TrustedNetworkConfiguration
{
    /**
     * @return array<int, string>
     */
    public static function hostPatterns(?string $configuredHosts, ?string $appUrl): array
    {
        $hosts = self::commaSeparatedValues($configuredHosts);

        if ($hosts === []) {
            $appHost = parse_url((string) $appUrl, PHP_URL_HOST);

            if (is_string($appHost) && $appHost !== '') {
                $hosts[] = $appHost;
            }
        }

        return array_map(
            static fn (string $host): string => '^'.preg_quote($host, '/').'$',
            $hosts,
        );
    }

    /**
     * @return array<int, string>
     */
    public static function proxyAddresses(?string $configuredProxies): array
    {
        $proxies = self::commaSeparatedValues($configuredProxies);

        if (array_intersect($proxies, ['*', '**']) !== []) {
            throw new InvalidArgumentException(
                'TRUSTED_PROXIES must contain explicit IP addresses or CIDR ranges; wildcards are not allowed.'
            );
        }

        return $proxies;
    }

    /**
     * @return array<int, string>
     */
    private static function commaSeparatedValues(?string $value): array
    {
        if (! is_string($value) || trim($value) === '') {
            return [];
        }

        return array_values(array_filter(
            array_map(trim(...), explode(',', $value)),
            static fn (string $item): bool => $item !== '',
        ));
    }
}
