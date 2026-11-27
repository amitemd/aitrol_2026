<?php

use Aitrol\ActivosFijos\Infrastructure\ActivosFijosServiceProvider;
use Aitrol\CajaBancos\Infrastructure\CajaBancosServiceProvider;

use Aitrol\Clientes\Infrastructure\Providers\ClientesServiceProvider;
use Aitrol\Comercial\Infrastructure\ComercialServiceProvider;
use Aitrol\Configuracion\Infrastructure\Providers\ConfiguracionServiceProvider;
use Aitrol\Contabilidad\Infrastructure\ContabilidadServiceProvider;
use Aitrol\Inventarios\Infrastructure\InventariosServiceProvider;
use Aitrol\Proveedores\Infrastructure\Providers\ProveedoresServiceProvider;
use Aitrol\Rrhh\Infrastructure\RrhhServiceProvider;
use Aitrol\Tributario\Infrastructure\TributarioServiceProvider;
use App\Providers\AppServiceProvider;

return [
    AppServiceProvider::class,
    ActivosFijosServiceProvider::class,
    CajaBancosServiceProvider::class,
    ClientesServiceProvider::class,
    ComercialServiceProvider::class,
    ConfiguracionServiceProvider::class,
    ContabilidadServiceProvider::class,
    InventariosServiceProvider::class,
    ProveedoresServiceProvider::class,
    RrhhServiceProvider::class,
    TributarioServiceProvider::class,
];
