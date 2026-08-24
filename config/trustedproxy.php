<?php

use App\Support\TrustedNetworkConfiguration;

return [
    'proxies' => TrustedNetworkConfiguration::proxyAddresses(
        env('TRUSTED_PROXIES'),
    ),
];
