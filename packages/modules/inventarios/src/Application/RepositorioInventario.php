<?php

namespace Aitrol\Inventarios\Application;

use Closure;
use Illuminate\Pagination\LengthAwarePaginator;
use stdClass;

interface RepositorioInventario
{
    public function listar(string $seccion, array $filtros): LengthAwarePaginator;

    public function reporte(array $filtros): iterable;

    public function catalogo(string $seccion, int $id, bool $bloquear = false): stdClass;

    public function guardarCatalogo(string $seccion, ?int $id, array $datos): stdClass;

    public function operacion(int $id): stdClass;

    public function porClave(string $clave): ?stdClass;

    public function saldo(int $producto, int $bodega): stdClass;

    public function registrar(array $operacion, array $lineas): stdClass;

    public function transaccion(Closure $proceso): mixed;
}
