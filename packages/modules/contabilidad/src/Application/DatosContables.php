<?php

namespace Aitrol\Contabilidad\Application;

use App\Services\EmpresaActiva;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use stdClass;

final readonly class DatosContables
{
    public function __construct(private EmpresaActiva $empresa) {}

    public function tabla(string $tabla): Builder
    {
        return DB::table($tabla)->where($tabla.'.id_empresa', $this->empresa->id());
    }

    public function bloquearEmpresa(): void
    {
        DB::table('erp_empresas')->where('id', $this->empresa->id())->lockForUpdate()->first(['id']);
    }

    public function fila(string $tabla, int $id): stdClass
    {
        return $this->tabla($tabla)->where('id', $id)->first() ?? abort(404, 'Registro contable no encontrado.');
    }

    public function crear(string $tabla, array $datos, bool $auditar = true): int
    {
        return (int) DB::table($tabla)->insertGetId($datos + ['id_empresa' => $this->empresa->id()] + ($auditar ? [
            'id_usuario_creacion' => auth()->id(), 'created_at' => now(), 'updated_at' => now(),
        ] : []));
    }

    public function actualizar(string $tabla, int $id, array $datos): void
    {
        $this->tabla($tabla)->where('id', $id)->update($datos + ['id_usuario_modificacion' => auth()->id(), 'updated_at' => now()]);
    }

    public function cuentas(): Builder
    {
        return $this->tabla('co_cuentas')->leftJoin('co_cuentas as padre', 'padre.id', '=', 'co_cuentas.id_padre')
            ->select('co_cuentas.*', 'padre.codigo as codigo_padre', 'padre.nombre as padre')->orderBy('co_cuentas.codigo')->orderBy('co_cuentas.id');
    }

    public function asientos(): Builder
    {
        return $this->tabla('co_asientos')
            ->join('co_periodos as periodo', 'periodo.id', '=', 'co_asientos.id_periodo')
            ->leftJoin('co_asientos as reversa', 'reversa.id_reversa_de', '=', 'co_asientos.id')
            ->select('co_asientos.*', 'periodo.nombre as periodo', 'reversa.id as id_reversion', 'reversa.numero as numero_reversion');
    }

    public function asiento(int $id): stdClass
    {
        $fila = $this->asientos()->where('co_asientos.id', $id)->first() ?? abort(404, 'Asiento contable no encontrado.');
        $fila->lineas = $this->lineas()->where('co_lineas.id_asiento', $id)->orderBy('co_lineas.id')->get();

        return $fila;
    }

    public function lineas(): Builder
    {
        return $this->tabla('co_lineas')
            ->join('co_asientos as asiento', 'asiento.id', '=', 'co_lineas.id_asiento')
            ->join('co_cuentas as cuenta', 'cuenta.id', '=', 'co_lineas.id_cuenta')
            ->select('co_lineas.*', 'asiento.fecha', 'asiento.numero', 'asiento.concepto', 'asiento.referencia',
                'asiento.flujo', 'cuenta.codigo', 'cuenta.nombre as cuenta', 'cuenta.clase', 'cuenta.efectivo');
    }

    public function buscar(Builder $consulta, ?string $buscar, array $columnas): Builder
    {
        if ($buscar !== null && $buscar !== '') {
            $consulta->where(function (Builder $q) use ($columnas, $buscar): void {
                foreach ($columnas as $columna) {
                    $q->orWhereLike($columna, '%'.$buscar.'%');
                }
            });
        }

        return $consulta;
    }
}
