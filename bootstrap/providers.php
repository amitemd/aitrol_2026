<?php

use Aitrol\Clientes\Infrastructure\Providers\ClientesServiceProvider;
use Aitrol\Configuracion\Infrastructure\Providers\ConfiguracionServiceProvider;
use App\Providers\AppServiceProvider;

return [
    AppServiceProvider::class,
    ClientesServiceProvider::class,
    ConfiguracionServiceProvider::class,
];
