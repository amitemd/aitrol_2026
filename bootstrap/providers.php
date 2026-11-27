<?php

use Aitrol\Clientes\Infrastructure\Providers\ClientesServiceProvider;
use Aitrol\Configuracion\Infrastructure\Providers\ConfiguracionServiceProvider;
use Aitrol\Proveedores\Infrastructure\Providers\ProveedoresServiceProvider;
use App\Providers\AppServiceProvider;

return [
    AppServiceProvider::class,
    ClientesServiceProvider::class,
    ConfiguracionServiceProvider::class,
    ProveedoresServiceProvider::class,
];
