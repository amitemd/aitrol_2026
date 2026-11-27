<?php

use Aitrol\ActivosFijos\Infrastructure\ActivosFijosServiceProvider;

use Aitrol\Clientes\Infrastructure\Providers\ClientesServiceProvider;
use Aitrol\Comercial\Infrastructure\ComercialServiceProvider;
use Aitrol\Configuracion\Infrastructure\Providers\ConfiguracionServiceProvider;
use Aitrol\Inventarios\Infrastructure\InventariosServiceProvider;
use Aitrol\Proveedores\Infrastructure\Providers\ProveedoresServiceProvider;
use App\Providers\AppServiceProvider;

return [
    AppServiceProvider::class,
    ActivosFijosServiceProvider::class,
    ClientesServiceProvider::class,
    ComercialServiceProvider::class,
    ConfiguracionServiceProvider::class,
    InventariosServiceProvider::class,
    ProveedoresServiceProvider::class,
];
