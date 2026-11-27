<?php

namespace Aitrol\Inventarios\Infrastructure\Persistence;

use Aitrol\Contabilidad\Infrastructure\Queries\ReferenciaAsiento;
use Aitrol\Inventarios\Application\RepositorioInventario;
use App\Services\EmpresaActiva;
use Closure;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use stdClass;

final readonly class InventarioSql implements RepositorioInventario
{
    public function __construct(private EmpresaActiva $empresa) {}

    public function listar(string $seccion, array $filtros): LengthAwarePaginator
    {
        $consulta = match ($seccion) {
            'productos', 'bodegas' => $this->catalogos($seccion, $filtros),
            'existencias', 'reportes' => $this->existencias($filtros),
            'movimientos', 'ajustes' => $this->operaciones($seccion, $filtros),
            'kardex' => $this->kardex($filtros),
        };

        return $consulta->paginate((int) ($filtros['per_page'] ?? 20), page: (int) ($filtros['page'] ?? 1))
            ->appends(array_diff_key($filtros, ['page' => true]));
    }

    public function reporte(array $filtros): iterable
    {
        return $this->existencias($filtros)->orderBy('inv_existencias.id')->cursor();
    }

    public function catalogo(string $seccion, int $id, bool $bloquear = false): stdClass
    {
        $consulta = $this->tabla('inv_'.$seccion)->where('id', $id);
        if ($bloquear) $consulta->lockForUpdate();

        return $consulta->first() ?? abort(404, 'Registro de inventario no encontrado.');
    }

    public function guardarCatalogo(string $seccion, ?int $id, array $datos): stdClass
    {
        try {
            return $this->transaccion(function () use ($seccion, $id, $datos): stdClass {
                $tabla = 'inv_'.$seccion;
                if ($id === null) {
                    $id = DB::table($tabla)->insertGetId($datos + [
                        'id_empresa' => $this->empresa->id(), 'id_usuario_creacion' => auth()->id(),
                        'created_at' => now(), 'updated_at' => now(),
                    ]);
                } else {
                    $this->catalogo($seccion, $id, bloquear: true);
                    $columna = $seccion === 'productos' ? 'id_producto' : 'id_bodega';
                    if (! $datos['activo'] && $this->tabla('inv_existencias')->where($columna, $id)->where('cantidad', '>', 0)->exists()) {
                        throw ValidationException::withMessages(['activo' => 'Lleva la existencia a cero antes de desactivar este registro.']);
                    }
                    $this->tabla($tabla)->where('id', $id)->update($datos + ['id_usuario_modificacion' => auth()->id(), 'updated_at' => now()]);
                }

                return $this->catalogo($seccion, $id);
            });
        } catch (UniqueConstraintViolationException $error) {
            abort(409, 'El código o código de barras ya existe en esta empresa.');
        }
    }

    public function operacion(int $id): stdClass
    {
        $consulta = $this->tabla('inv_operaciones')->select('inv_operaciones.*')->where('id', $id);
        $operacion = ReferenciaAsiento::inventario($consulta, 'inv_operaciones.id')->first() ?? abort(404, 'Movimiento no encontrado.');
        $operacion->lineas = $this->tabla('inv_kardex')->where('id_operacion', $id)->orderBy('id')->get();

        return $operacion;
    }

    public function porClave(string $clave): ?stdClass
    {
        $id = $this->tabla('inv_operaciones')->where('clave_operacion', $clave)->value('id');

        return $id === null ? null : $this->operacion((int) $id);
    }

    public function saldo(int $producto, int $bodega): stdClass
    {
        return $this->tabla('inv_existencias')->where('id_producto', $producto)->where('id_bodega', $bodega)->lockForUpdate()->first()
            ?? (object) ['id_bodega' => $bodega, 'cantidad' => 0, 'costo_promedio' => 0];
    }

    public function registrar(array $operacion, array $lineas): stdClass
    {
        $id = DB::table('inv_operaciones')->insertGetId($operacion + [
            'id_empresa' => $this->empresa->id(), 'id_usuario' => auth()->id(), 'created_at' => now(),
        ]);
        foreach ($lineas as $linea) {
            $clave = ['id_empresa' => $this->empresa->id(), 'id_producto' => $operacion['id_producto'], 'id_bodega' => $linea['id_bodega']];
            DB::table('inv_existencias')->updateOrInsert($clave, [
                'cantidad' => $linea['saldo'], 'costo_promedio' => $linea['costo_promedio'],
            ]);
            DB::table('inv_kardex')->insert($clave + $linea + ['id_operacion' => $id]);
        }

        return $this->operacion((int) $id);
    }

    public function transaccion(Closure $proceso): mixed
    {
        return $this->empresa->transaccion($proceso);
    }

    private function tabla(string $tabla): Builder
    {
        return DB::table($tabla)->where($tabla.'.id_empresa', $this->empresa->id());
    }

    private function catalogos(string $seccion, array $filtros): Builder
    {
        $consulta = $this->tabla('inv_'.$seccion)->orderBy('nombre')->orderBy('id');
        if (! empty($filtros['q'])) {
            $consulta->where(function (Builder $q) use ($filtros, $seccion): void {
                $patron = '%'.$filtros['q'].'%';
                $q->whereLike('nombre', $patron)->orWhereLike('codigo', $patron);
                if ($seccion === 'productos') $q->orWhereLike('codigo_barras', $patron)->orWhereLike('categoria', $patron);
            });
        }
        if (isset($filtros['activo'])) $consulta->where('activo', $filtros['activo']);

        return $consulta;
    }

    private function existencias(array $filtros): Builder
    {
        $consulta = $this->conCatalogos('inv_existencias')
            ->addSelect('p.unidad', 'p.stock_minimo')->orderBy('p.nombre')->orderBy('b.nombre');
        if (! empty($filtros['bajo_minimo'])) $consulta->whereColumn('inv_existencias.cantidad', '<=', 'p.stock_minimo');

        return $this->filtrar($consulta, 'inv_existencias', $filtros);
    }

    private function operaciones(string $seccion, array $filtros): Builder
    {
        $tabla = 'inv_operaciones';
        $consulta = $this->conCatalogos($tabla)->leftJoin('inv_bodegas as destino', function ($join): void {
            $join->on('destino.id', '=', 'inv_operaciones.id_bodega_destino')->on('destino.id_empresa', '=', 'inv_operaciones.id_empresa');
        })->addSelect('destino.nombre as bodega_destino')->orderByDesc($tabla.'.id');
        $tipos = ['ajuste_entrada', 'ajuste_salida'];
        $seccion === 'ajustes' ? $consulta->whereIn($tabla.'.tipo', $tipos) : $consulta->whereNotIn($tabla.'.tipo', $tipos);
        ReferenciaAsiento::inventario($consulta, $tabla.'.id');

        return $this->filtrar($consulta, $tabla, $filtros, $tabla.'.created_at');
    }

    private function kardex(array $filtros): Builder
    {
        $consulta = $this->conCatalogos('inv_kardex')->join('inv_operaciones as o', function ($join): void {
            $join->on('o.id', '=', 'inv_kardex.id_operacion')->on('o.id_empresa', '=', 'inv_kardex.id_empresa');
        })->addSelect('o.tipo', 'o.motivo', 'o.referencia', 'o.created_at')->orderByDesc('inv_kardex.id');
        ReferenciaAsiento::inventario($consulta, 'inv_kardex.id_operacion');

        return $this->filtrar($consulta, 'inv_kardex', $filtros, 'o.created_at');
    }

    private function conCatalogos(string $tabla): Builder
    {
        return $this->tabla($tabla)
            ->join('inv_productos as p', fn ($join) => $join->on('p.id', '=', $tabla.'.id_producto')->on('p.id_empresa', '=', $tabla.'.id_empresa'))
            ->join('inv_bodegas as b', fn ($join) => $join->on('b.id', '=', $tabla.'.id_bodega')->on('b.id_empresa', '=', $tabla.'.id_empresa'))
            ->select($tabla.'.*', 'p.nombre as producto', 'p.codigo as codigo_producto', 'b.nombre as bodega');
    }

    private function filtrar(Builder $consulta, string $tabla, array $filtros, ?string $fecha = null): Builder
    {
        if ($fecha !== null && isset($filtros['desde'])) {
            $consulta->where($fecha, '>=', $filtros['desde'].' 00:00:00');
        }
        if ($fecha !== null && isset($filtros['hasta'])) {
            $consulta->where($fecha, '<', \Illuminate\Support\Carbon::parse($filtros['hasta'])->addDay()->toDateString().' 00:00:00');
        }
        if (isset($filtros['id_producto'])) $consulta->where($tabla.'.id_producto', $filtros['id_producto']);
        if (isset($filtros['id_bodega'])) {
            $consulta->where(function (Builder $q) use ($tabla, $filtros): void {
                $q->where($tabla.'.id_bodega', $filtros['id_bodega']);
                if ($tabla === 'inv_operaciones') $q->orWhere($tabla.'.id_bodega_destino', $filtros['id_bodega']);
            });
        }
        if (! empty($filtros['q'])) {
            $patron = '%'.$filtros['q'].'%';
            $consulta->where(fn (Builder $q): Builder => $q->whereLike('p.nombre', $patron)->orWhereLike('p.codigo', $patron)->orWhereLike('b.nombre', $patron));
        }

        return $consulta;
    }
}
