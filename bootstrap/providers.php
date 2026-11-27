<?php

use Aitrol\Clientes\Infrastructure\Providers\ClientesServiceProvider;
use Aitrol\Configuracion\Infrastructure\Providers\ConfiguracionServiceProvider;
use Aitrol\Inventarios\Infrastructure\InventariosServiceProvider;
use Aitrol\Proveedores\Infrastructure\Providers\ProveedoresServiceProvider;
use App\Providers\AppServiceProvider;

return [
    AppServiceProvider::class,
    ClientesServiceProvider::class,
    ConfiguracionServiceProvider::class,
    InventariosServiceProvider::class,
    ProveedoresServiceProvider::class,
];
