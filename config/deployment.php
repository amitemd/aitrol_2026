<?php

return [
    'asset_manifest' => env('DEPLOY_ASSET_MANIFEST', public_path('build/manifest.json')),
    'git' => [
        'enabled' => env('DEPLOY_GIT_ENABLED', true),
        'remote' => env('DEPLOY_GIT_REMOTE', 'origin'),
        'branch' => env('DEPLOY_GIT_BRANCH', 'main'),
        'timeout' => (int) env('DEPLOY_GIT_TIMEOUT', 300),
        'helper' => env('DEPLOY_GIT_HELPER'),
        'user' => env('DEPLOY_GIT_USER'),
    ],
];
