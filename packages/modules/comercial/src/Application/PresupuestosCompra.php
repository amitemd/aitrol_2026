<?php

namespace Aitrol\Comercial\Application;

use Aitrol\Inventarios\Domain\CantidadInventario as Cantidad;
use App\Services\ContextoOperativo;
use App\Services\EmpresaActiva;
use Illuminate\Database\Query\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use stdClass;

final readonly class PresupuestosCompra
{
    public function __construct(private EmpresaActiva $empresa, private ContextoOperativo $contexto) {}

    public function listar(int $anio): LengthAwarePaginator
    {
        return $this->conSaldos()->where('presupuesto.anio', $anio)->orderBy('centro.codigo')->orderBy('presupuesto.id')
            ->paginate(25)->withQueryString()->through($this->presentar(...));
    }

    public function centrosActivos(): Collection
    {
        return DB::table('co_centros_costos')->where(['id_empresa' => $this->empresa->id(), 'activo' => true])
            ->orderBy('codigo')->orderBy('id')->get(['id', 'codigo', 'nombre']);
    }

    public function guardar(int $centro, int $anio, string|int|float $monto): array
    {
        return $this->empresa->transaccion(function () use ($centro, $anio, $monto): array {
            if (! DB::table('co_centros_costos')->where(['id_empresa' => $this->empresa->id(), 'id' => $centro, 'activo' => true])->exists()) {
                throw ValidationException::withMessages(['id_centro_costo' => 'Selecciona un centro de costo activo de tu empresa.']);
            }
            $ambito = ['id_empresa' => $this->empresa->id(), 'id_sucursal' => $this->contexto->sucursal()->id, 'id_centro_costo' => $centro, 'anio' => $anio];
            $tabla = DB::table('com_presupuestos_compra');
            $actual = (clone $tabla)->where($ambito)->first();
            $importe = Cantidad::unidades($monto, 2);
            if ($actual !== null) {
                $ocupado = (int) $this->solicitudes()->where('id_presupuesto', $actual->id)->whereIn('estado', ['reservada', 'convertida'])->sum('total');
                if ($importe < $ocupado) {
                    throw ValidationException::withMessages(['monto' => 'El monto debe cubrir las reservas y los compromisos, USD '.Cantidad::decimal($ocupado, 2).'.']);
                }
            }
            $tabla->upsert([$ambito + [
                'monto' => $importe, 'id_usuario_modificacion' => auth()->id(), 'created_at' => now(), 'updated_at' => now(),
            ]], array_keys($ambito), ['monto', 'id_usuario_modificacion', 'updated_at']);
            $id = (clone $tabla)->where($ambito)->value('id');

            return $this->presentar($this->consultar((int) $id));
        });
    }

    public function consultar(int $presupuesto): stdClass
    {
        return $this->conSaldos()->where('presupuesto.id', $presupuesto)->first() ?? abort(404, 'Presupuesto de compras no encontrado.');
    }

    public function presentar(stdClass $registro): array
    {
        return [
            'id' => $registro->id, 'anio' => $registro->anio, 'id_centro_costo' => $registro->id_centro_costo,
            'codigo_centro' => $registro->codigo_centro, 'centro' => $registro->centro, 'centro_activo' => (bool) $registro->centro_activo,
            'monto' => Cantidad::decimal($registro->monto, 2), 'reservado' => Cantidad::decimal($registro->reservado, 2),
            'comprometido' => Cantidad::decimal($registro->comprometido, 2), 'disponible' => Cantidad::decimal($this->disponible($registro), 2),
        ];
    }

    public function disponible(stdClass $registro): int
    {
        return (int) $registro->monto - (int) $registro->reservado - (int) $registro->comprometido;
    }

    public function solicitudes(): Builder
    {
        return DB::table('com_solicitudes_compra')->where([
            'com_solicitudes_compra.id_empresa' => $this->empresa->id(), 'com_solicitudes_compra.id_sucursal' => $this->contexto->sucursal()->id,
        ]);
    }

    public function registrarEvento(int $solicitud, string $estado, ?string $motivo = null): void
    {
        DB::table('com_eventos_solicitud_compra')->insert([
            'id_empresa' => $this->empresa->id(), 'id_solicitud' => $solicitud, 'id_usuario' => auth()->id(),
            'responsable' => auth()->user()?->name ?? 'Sistema', 'estado' => $estado, 'motivo' => $motivo, 'created_at' => now(),
        ]);
    }

    public function transicionar(int $solicitud, string $desde, string $hasta, ?string $motivo = null): void
    {
        $actualizada = $this->solicitudes()->where(['id' => $solicitud, 'estado' => $desde])->update(['estado' => $hasta, 'updated_at' => now()]);
        abort_if($actualizada !== 1, 409, 'El estado de la solicitud cambió. Consulta su detalle.');
        $this->registrarEvento($solicitud, $hasta, $motivo);
    }

    public function liberarOrden(int $solicitud, ?string $motivo): void
    {
        $estado = $this->solicitudes()->where('id', $solicitud)->value('estado');
        if ($estado !== 'cancelada') {
            $this->transicionar($solicitud, 'convertida', 'cancelada', $motivo);
        }
    }

    private function conSaldos(): Builder
    {
        $saldos = $this->solicitudes()->whereIn('estado', ['reservada', 'convertida'])->groupBy('id_presupuesto')->select('id_presupuesto')
            ->selectRaw("SUM(CASE WHEN estado = 'reservada' THEN total ELSE 0 END) AS reservado")
            ->selectRaw("SUM(CASE WHEN estado = 'convertida' THEN total ELSE 0 END) AS comprometido");

        return DB::table('com_presupuestos_compra as presupuesto')->where([
            'presupuesto.id_empresa' => $this->empresa->id(), 'presupuesto.id_sucursal' => $this->contexto->sucursal()->id,
        ])->join('co_centros_costos as centro', fn ($join) => $join->on('centro.id', '=', 'presupuesto.id_centro_costo')
            ->on('centro.id_empresa', '=', 'presupuesto.id_empresa'))
            ->leftJoinSub($saldos, 'saldos', 'saldos.id_presupuesto', '=', 'presupuesto.id')
            ->select('presupuesto.id', 'presupuesto.id_sucursal', 'presupuesto.anio', 'presupuesto.id_centro_costo', 'presupuesto.monto',
                'centro.codigo as codigo_centro', 'centro.nombre as centro', 'centro.activo as centro_activo')
            ->selectRaw('COALESCE(saldos.reservado, 0) AS reservado, COALESCE(saldos.comprometido, 0) AS comprometido');
    }
}
