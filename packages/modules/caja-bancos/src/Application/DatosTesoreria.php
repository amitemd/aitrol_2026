<?php

namespace Aitrol\CajaBancos\Application;

use Aitrol\Contabilidad\Infrastructure\Queries\ReferenciaAsiento;
use App\Services\EmpresaActiva;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use stdClass;

final readonly class DatosTesoreria
{
    public function __construct(private EmpresaActiva $empresa) {}

    public function tabla(string $tabla): Builder
    {
        return DB::table($tabla)->where($tabla.'.id_empresa', $this->empresa->id());
    }

    public function fila(string $tabla, int $id, bool $bloquear = false): stdClass
    {
        $consulta = $this->tabla($tabla)->where('id', $id);
        if ($bloquear) $consulta->lockForUpdate();

        return $consulta->first() ?? abort(404, 'Registro de Caja y bancos no encontrado.');
    }

    public function crear(string $tabla, array $datos, bool $auditar = true): int
    {
        $auditoria = $auditar ? ['id_usuario_creacion' => auth()->id(), 'created_at' => now(), 'updated_at' => now()] : [];

        return (int) DB::table($tabla)->insertGetId($datos + ['id_empresa' => $this->empresa->id()] + $auditoria);
    }

    public function actualizar(string $tabla, int $id, array $datos): void
    {
        $this->tabla($tabla)->where('id', $id)->update($datos + ['id_usuario_modificacion' => auth()->id(), 'updated_at' => now()]);
    }

    public function libro(): Builder
    {
        $consulta = $this->tabla('cb_asientos')
            ->join('cb_operaciones as op', 'op.id', '=', 'cb_asientos.id_operacion')
            ->join('cb_cuentas as cuenta', 'cuenta.id', '=', 'cb_asientos.id_cuenta')
            ->leftJoin('cb_lineas_extracto as extracto', 'extracto.id_asiento', '=', 'cb_asientos.id')
            ->leftJoin('cb_operaciones as reversa', 'reversa.id_reversa_de', '=', 'op.id')
            ->select('cb_asientos.*', 'op.tipo', 'op.fecha', 'op.moneda', 'op.concepto', 'op.referencia', 'op.id_reversa_de',
                'cuenta.nombre as cuenta', 'cuenta.codigo as codigo_cuenta', 'extracto.id as linea_conciliada', 'reversa.id as id_reversion');

        return ReferenciaAsiento::agregar($consulta, 'tesoreria.operacion', 'cb_asientos.id_operacion');
    }

    public function listado(string $seccion, array $filtros): Builder
    {
        [$consulta, $buscables, $cuenta, $fecha] = match ($seccion) {
            'cuentas' => [$this->tabla('cb_cuentas')->orderBy('nombre'), ['nombre', 'codigo', 'banco'], 'cb_cuentas.id', null],
            'cierres' => [$this->tabla('cb_cierres')->join('cb_cuentas as c', 'c.id', '=', 'cb_cierres.id_cuenta')
                ->select('cb_cierres.*', 'c.nombre as cuenta', 'c.moneda', 'c.saldo as saldo_actual')->orderByDesc('cb_cierres.id'), ['c.codigo', 'c.nombre'], 'cb_cierres.id_cuenta', 'cb_cierres.created_at'],
            'extractos' => [$this->tabla('cb_extractos')->join('cb_cuentas as c', 'c.id', '=', 'cb_extractos.id_cuenta')
                ->select('cb_extractos.*', 'c.nombre as cuenta', 'c.moneda')->orderByDesc('cb_extractos.id'), ['cb_extractos.nombre', 'c.nombre'], 'cb_extractos.id_cuenta', 'cb_extractos.created_at'],
            'conciliacion' => [$this->tabla('cb_lineas_extracto')->join('cb_cuentas as c', 'c.id', '=', 'cb_lineas_extracto.id_cuenta')
                ->select('cb_lineas_extracto.*', 'c.nombre as cuenta', 'c.moneda')->orderByDesc('cb_lineas_extracto.id'),
                ['cb_lineas_extracto.referencia', 'cb_lineas_extracto.descripcion', 'c.nombre'], 'cb_lineas_extracto.id_cuenta', 'cb_lineas_extracto.fecha'],
            'reportes' => [$this->libro()->orderByDesc('cb_asientos.id'), ['op.concepto', 'op.referencia', 'cuenta.nombre'], 'cb_asientos.id_cuenta', 'op.fecha'],
            default => [$this->operaciones()->whereIn('cb_operaciones.tipo', config('caja-bancos.operaciones.'.$seccion))->orderByDesc('cb_operaciones.id'),
                ['cb_operaciones.concepto', 'cb_operaciones.referencia'], null, 'cb_operaciones.fecha'],
        };
        if ($seccion === 'cuentas' && isset($filtros['activo'])) $consulta->where('activo', $filtros['activo']);
        if ($seccion === 'conciliacion' && isset($filtros['conciliado'])) {
            $filtros['conciliado'] ? $consulta->whereNotNull('cb_lineas_extracto.id_asiento') : $consulta->whereNull('cb_lineas_extracto.id_asiento');
        }
        if ($seccion === 'conciliacion' && isset($filtros['id_extracto'])) $consulta->where('cb_lineas_extracto.id_extracto', $filtros['id_extracto']);
        if ($seccion === 'conciliacion') {
            $consulta->leftJoin('cb_asientos as conciliado', 'conciliado.id', '=', 'cb_lineas_extracto.id_asiento');
            ReferenciaAsiento::agregar($consulta, 'tesoreria.operacion', 'conciliado.id_operacion');
        }
        if (isset($filtros['id_cuenta'])) {
            if ($cuenta === null) {
                $consulta->whereExists(fn (Builder $q) => $q->selectRaw('1')->from('cb_asientos')
                    ->whereColumn('cb_asientos.id_operacion', 'cb_operaciones.id')->where('cb_asientos.id_cuenta', $filtros['id_cuenta']));
            } else {
                $consulta->where($cuenta, $filtros['id_cuenta']);
            }
        }
        if (isset($filtros['q'])) {
            $consulta->where(function (Builder $q) use ($buscables, $filtros): void {
                foreach ($buscables as $columna) {
                    $q->orWhereLike($columna, '%'.$filtros['q'].'%');
                }
            });
        }
        if ($fecha !== null) {
            if (isset($filtros['desde'])) $consulta->whereDate($fecha, '>=', $filtros['desde']);
            if (isset($filtros['hasta'])) $consulta->whereDate($fecha, '<=', $filtros['hasta']);
        }

        return $consulta;
    }

    public function detalle(string $seccion, int $id): stdClass
    {
        $tabla = match ($seccion) {
            'cuentas' => 'cb_cuentas', 'cierres' => 'cb_cierres', 'extractos' => 'cb_extractos', 'conciliacion' => 'cb_lineas_extracto',
            default => 'cb_operaciones',
        };
        $registro = $this->listado($seccion, [])->where($tabla.'.id', $id)->first() ?? abort(404, 'Registro de Caja y bancos no encontrado.');
        if ($tabla === 'cb_operaciones') {
            $registro->asientos = $this->libro()->where('cb_asientos.id_operacion', $id)->orderBy('cb_asientos.id')->get();
        }
        if ($seccion === 'conciliacion') {
            $registro->historial = $this->tabla('cb_conciliaciones')->where('id_linea_extracto', $id)->orderBy('id')->get();
        }

        return $registro;
    }

    public function operacion(int $id): stdClass
    {
        $op = $this->operaciones()->where('cb_operaciones.id', $id)->first() ?? abort(404, 'Operación no encontrada.');
        $op->asientos = $this->libro()->where('cb_asientos.id_operacion', $id)->orderBy('cb_asientos.id')->get();

        return $op;
    }

    private function operaciones(): Builder
    {
        $consulta = $this->tabla('cb_operaciones')
            ->leftJoin('cb_operaciones as reversa', 'reversa.id_reversa_de', '=', 'cb_operaciones.id')
            ->select('cb_operaciones.*', 'reversa.id as id_reversion');
        foreach (['origen' => '<', 'destino' => '>'] as $alias => $signo) {
            $consulta->selectSub(DB::table('cb_asientos')
                ->join('cb_cuentas', 'cb_cuentas.id', '=', 'cb_asientos.id_cuenta')
                ->whereColumn('cb_asientos.id_operacion', 'cb_operaciones.id')->where('cb_asientos.variacion', $signo, 0)
                ->select('cb_cuentas.nombre')->orderBy('cb_asientos.id')->limit(1), $alias);
        }

        ReferenciaAsiento::agregar($consulta, 'tesoreria.operacion', 'cb_operaciones.id');

        return ReferenciaAsiento::agregar($consulta, 'tesoreria.operacion', 'reversa.id', 'asiento_reversion');
    }
}
