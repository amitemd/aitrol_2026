<?php

return [
    'habilitado' => env('AITROL_MANTENIMIENTO_WEB', env('APP_ENV') === 'local'),
    'git' => env('AITROL_MANTENIMIENTO_GIT', false),
    'remoto' => env('AITROL_DEPLOY_REMOTO', 'origin'),
    'rama' => env('AITROL_DEPLOY_RAMA', 'main'),
    'php' => env('AITROL_PHP_CLI', PHP_BINDIR.DIRECTORY_SEPARATOR.(PHP_OS_FAMILY === 'Windows' ? 'php.exe' : 'php')),
];
