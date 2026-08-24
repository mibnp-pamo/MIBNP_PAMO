<?php

use App\Support\TrustedNetworkConfiguration;

return [
    'trusted_hosts' => TrustedNetworkConfiguration::hostPatterns(
        env('TRUSTED_HOSTS'),
        env('APP_URL', 'http://localhost'),
    ),
];
