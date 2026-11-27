<?php

return [
    'exigir_https' => env('AITROL_REQUIRE_HTTPS', false),
    'hosts' => array_values(array_filter(array_map('trim', explode(',', (string) env('AITROL_TRUSTED_HOSTS', ''))))),
    'proxies' => array_values(array_filter(array_map('trim', explode(',', (string) env('AITROL_TRUSTED_PROXIES', ''))), fn (string $ip) =>
        filter_var(explode('/', $ip, 2)[0], FILTER_VALIDATE_IP) !== false)),
];
