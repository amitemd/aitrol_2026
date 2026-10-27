<?php

use App\Modules\ModulesServiceProvider;
use App\Providers\AppServiceProvider;
use App\Providers\LegacyCompatibilityServiceProvider;
use Sis_medico\Providers\AppServiceProvider as LegacyAppServiceProvider;
use Sis_medico\Providers\AuthServiceProvider as LegacyAuthServiceProvider;
use Sis_medico\Providers\EventServiceProvider as LegacyEventServiceProvider;
use Sis_medico\Providers\RouteServiceProvider as LegacyRouteServiceProvider;

return [
    AppServiceProvider::class,
    ModulesServiceProvider::class,
    LegacyCompatibilityServiceProvider::class,
    LegacyAppServiceProvider::class,
    LegacyAuthServiceProvider::class,
    LegacyEventServiceProvider::class,
    LegacyRouteServiceProvider::class,
];
