<?php

namespace Aitrol\Contabilidad\Infrastructure\Persistence;

use Aitrol\Contabilidad\Application\Puertos\RepositorioContable;
use App\Services\EmpresaActiva;
use Closure;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use stdClass;

final readonly class DatosContables implements RepositorioContable
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

    public function transaccion(Closure $trabajo): mixed
    {
        return $this->empresa->transaccion($trabajo);
    }

    public function cuenta(int $id): stdClass
    {
        return $this->cuentas()->where('co_cuentas.id', $id)->first() ?? abort(404, 'Cuenta contable no encontrada.');
    }

    public function codigosConPrefijo(string $prefijo): iterable
    {
        return $this->tabla('co_cuentas')->whereLike('codigo', $prefijo.'%')->pluck('codigo');
    }

    public function nombresDeCuentas(?int $excepto): iterable
    {
        return $this->tabla('co_cuentas')
            ->when($excepto !== null, fn (Builder $consulta) => $consulta->where('id', '<>', $excepto))
            ->select('nombre')->cursor();
    }

    public function auxiliares(array $ids): array
    {
        return $this->tabla('co_cuentas')->whereIn('id', $ids)->where('activo', true)->where('auxiliar', true)->get()->keyBy('id')->all();
    }

    public function tieneSubcuentasActivas(int $id): bool
    {
        return $this->tabla('co_cuentas')->where('id_padre', $id)->where('activo', true)->exists();
    }

    public function periodoEnFecha(string $fecha): ?stdClass
    {
        return $this->tabla('co_periodos')->where('desde', '<=', $fecha)->where('hasta', '>=', $fecha)->first();
    }

    public function periodosSolapados(string $desde, string $hasta): bool
    {
        return $this->tabla('co_periodos')->where('desde', '<=', $hasta)->where('hasta', '>=', $desde)->exists();
    }

    public function periodo(int $id): stdClass
    {
        return $this->fila('co_periodos', $id);
    }

    public function asientoConClave(string $clave): ?stdClass
    {
        return $this->tabla('co_asientos')->where('clave_operacion', $clave)->first();
    }

    public function asientoDeOrigen(string $origen, int $id): ?int
    {
        $asiento = $this->tabla('co_asientos')->where('origen', $origen)->where('id_origen', $id)->value('id');

        return $asiento === null ? null : (int) $asiento;
    }

    public function siguienteNumero(): int
    {
        return (int) $this->tabla('co_asientos')->max('numero') + 1;
    }

    public function guardarCuenta(array $datos, ?int $id): int
    {
        return $this->guardar('co_cuentas', $datos, $id);
    }

    public function guardarPeriodo(array $datos, ?int $id = null): int
    {
        return $this->guardar('co_periodos', $datos, $id);
    }

    public function guardarAsiento(array $cabecera, array $lineas): int
    {
        $id = $this->crear('co_asientos', $cabecera);
        foreach ($lineas as $fila) {
            $this->crear('co_lineas', $fila + ['id_asiento' => $id], false);
        }

        return $id;
    }

    public function cajaEnDolares(int $id): bool
    {
        return $this->tabla('cb_cuentas')->where('id', $id)->where('moneda', 'USD')->exists();
    }

    public function guardarIntegracion(array $datos): void
    {
        $this->guardar('co_integraciones', $datos, $this->tabla('co_integraciones')->value('id'));
    }

    private function guardar(string $tabla, array $datos, ?int $id): int
    {
        if ($id === null) return $this->crear($tabla, $datos);
        $this->actualizar($tabla, $id, $datos);

        return $id;
    }
}
