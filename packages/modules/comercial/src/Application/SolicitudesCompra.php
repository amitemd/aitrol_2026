<?php

namespace Aitrol\Comercial\Application;

use Aitrol\Inventarios\Domain\CantidadInventario as Cantidad;
use App\Services\EmpresaActiva;
use Illuminate\Database\Query\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use stdClass;

final readonly class SolicitudesCompra
{
    public const ESTADOS = [
        'pendiente' => 'Pendiente de revisión',
        'reservada' => 'Aprobada con reserva',
        'convertida' => 'Convertida en orden',
        'rechazada' => 'Rechazada',
        'cancelada' => 'Cancelada',
    ];

    public function __construct(
        private EmpresaActiva $empresa,
        private PresupuestosCompra $presupuestos,
        private DocumentosComerciales $documentos,
    ) {}

    public function listar(?string $estado): LengthAwarePaginator
    {
        return $this->presupuestos->solicitudes()
            ->when($estado !== null, fn (Builder $consulta) => $consulta->where('estado', $estado))
            ->join('com_presupuestos_compra as presupuesto', 'presupuesto.id', '=', 'com_solicitudes_compra.id_presupuesto')
            ->join('co_centros_costos as centro', 'centro.id', '=', 'presupuesto.id_centro_costo')
            ->select('com_solicitudes_compra.*', 'centro.nombre as centro')->orderByDesc('com_solicitudes_compra.id')
            ->paginate(25)->withQueryString()->through($this->presentar(...));
    }

    public function detalle(int $id): array
    {
        $registro = $this->solicitud($id);
        $presupuesto = $this->presupuestos->presentar($this->presupuestos->consultar($registro->id_presupuesto));
        $lineas = $this->lineas($id)->get()->map(fn (stdClass $fila): array => [
            'id_producto' => $fila->id_producto, 'descripcion' => $fila->descripcion,
            'cantidad' => Cantidad::decimal($fila->cantidad, 3), 'precio' => Cantidad::decimal($fila->precio, 4),
            'tasa' => Cantidad::decimal($fila->tasa, 2), 'subtotal' => Cantidad::decimal($fila->subtotal, 2),
            'impuesto' => Cantidad::decimal($fila->impuesto, 2), 'total' => Cantidad::decimal($fila->total, 2),
        ]);
        $historial = DB::table('com_eventos_solicitud_compra')->where(['id_empresa' => $this->empresa->id(), 'id_solicitud' => $id])
            ->orderBy('id')->get(['estado', 'motivo', 'id_usuario', 'responsable', 'created_at']);
        $orden = DB::table('com_documentos')->where(['id_empresa' => $this->empresa->id(), 'id_solicitud_compra' => $id])
            ->first(['id', 'numero', 'fecha', 'estado_aprobacion', 'anulado_at']);

        return ['solicitud' => $this->presentar($registro), 'presupuesto' => $presupuesto, 'lineas' => $lineas, 'historial' => $historial, 'orden' => $orden];
    }

    public function crear(array $datos): int
    {
        return $this->empresa->transaccion(function () use ($datos): int {
            $presupuesto = $this->presupuestos->consultar((int) $datos['id_presupuesto']);
            $this->verificarPresupuesto($presupuesto, $datos['fecha']);
            $lineas = $this->documentos->prepararLineas($datos['lineas']);
            $id = DB::table('com_solicitudes_compra')->insertGetId([
                'id_empresa' => $this->empresa->id(), 'id_sucursal' => $presupuesto->id_sucursal,
                'id_presupuesto' => $presupuesto->id, 'id_usuario_creacion' => auth()->id(), 'fecha' => $datos['fecha'],
                'motivo' => $datos['motivo'], 'estado' => 'pendiente',
                'subtotal' => array_sum(array_column($lineas, 'subtotal')), 'impuesto' => array_sum(array_column($lineas, 'impuesto')),
                'total' => array_sum(array_column($lineas, 'total')), 'created_at' => now(), 'updated_at' => now(),
            ]);
            DB::table('com_lineas_solicitud_compra')->insert(array_map(fn (array $fila): array => [
                'id_empresa' => $this->empresa->id(), 'id_solicitud' => $id,
            ] + array_diff_key($fila, ['inventariable' => true]), $lineas));
            $this->presupuestos->registrarEvento($id, 'pendiente');

            return $id;
        });
    }

    public function decidir(int $id, string $estado, ?string $motivo): void
    {
        $this->empresa->transaccion(function () use ($id, $estado, $motivo): void {
            $solicitud = $this->solicitud($id);
            abort_unless($solicitud->estado === 'pendiente', 409, 'La solicitud ya fue revisada.');
            abort_if((int) $solicitud->id_usuario_creacion === (int) auth()->id(), 403, 'Otro usuario debe revisar esta solicitud.');
            if ($estado === 'reservada') {
                $presupuesto = $this->presupuestos->consultar($solicitud->id_presupuesto);
                $this->verificarPresupuesto($presupuesto, $solicitud->fecha);
                $disponible = $this->presupuestos->disponible($presupuesto);
                if ($solicitud->total > $disponible) {
                    throw ValidationException::withMessages(['presupuesto' => 'El presupuesto disponible es USD '.Cantidad::decimal($disponible, 2).'. Ajusta el presupuesto o libera otra reserva.']);
                }
            }
            $this->presupuestos->transicionar($id, 'pendiente', $estado, $motivo);
        });
    }

    public function convertir(int $id, array $datos): int
    {
        return $this->empresa->transaccion(function () use ($id, $datos): int {
            $solicitud = $this->solicitud($id);
            abort_unless($solicitud->estado === 'reservada', 409, 'La solicitud debe tener una reserva vigente para generar la orden.');
            $presupuesto = $this->presupuestos->consultar($solicitud->id_presupuesto);
            $this->verificarPresupuesto($presupuesto, $datos['fecha']);
            if ($datos['fecha'] < $solicitud->fecha) {
                throw ValidationException::withMessages(['fecha' => 'La orden debe tener una fecha igual o posterior a la solicitud.']);
            }
            $lineas = $this->lineas($id)->get()->map(fn (stdClass $fila): array => [
                'id_producto' => $fila->id_producto, 'descripcion' => $fila->descripcion,
                'cantidad' => Cantidad::decimal($fila->cantidad, 3), 'precio' => Cantidad::decimal($fila->precio, 4),
                'tasa' => Cantidad::decimal($fila->tasa, 2),
            ])->all();
            $orden = $this->documentos->registrar('compras', 'ordenes-compra', $datos + [
                'lineas' => $lineas, 'observacion' => $solicitud->motivo,
            ], idSolicitudCompra: $id);
            abort_unless((int) $orden->total === (int) $solicitud->total, 409, 'El total de la orden debe coincidir con el presupuesto reservado.');
            $this->presupuestos->transicionar($id, 'reservada', 'convertida');

            return (int) $orden->id;
        });
    }

    public function cancelar(int $id, string $motivo): void
    {
        $this->empresa->transaccion(function () use ($id, $motivo): void {
            $solicitud = $this->solicitud($id);
            abort_if((int) $solicitud->id_usuario_creacion !== (int) auth()->id() && Gate::denies('permiso', ['compras.aprobaciones.index', 'actualizar']), 403);
            abort_unless(in_array($solicitud->estado, ['pendiente', 'reservada'], true), 409,
                'Solo se pueden cancelar solicitudes pendientes o reservadas. Para liberar una orden vinculada, anúlala desde Compras.');
            $this->presupuestos->transicionar($id, $solicitud->estado, 'cancelada', $motivo);
        });
    }

    private function verificarPresupuesto(stdClass $presupuesto, string $fecha): void
    {
        if (! $presupuesto->centro_activo) {
            throw ValidationException::withMessages(['presupuesto' => 'El centro de costo del presupuesto está inactivo.']);
        }
        if ((int) $presupuesto->anio !== (int) substr($fecha, 0, 4)) {
            throw ValidationException::withMessages(['fecha' => 'La fecha debe pertenecer al año del presupuesto.']);
        }
    }

    private function solicitud(int $id): stdClass
    {
        return $this->presupuestos->solicitudes()->find($id) ?? abort(404, 'Solicitud de compra no encontrada.');
    }

    private function lineas(int $id): Builder
    {
        return DB::table('com_lineas_solicitud_compra')->where(['id_empresa' => $this->empresa->id(), 'id_solicitud' => $id])->orderBy('id');
    }

    private function presentar(stdClass $solicitud): array
    {
        return [
            'id' => $solicitud->id, 'numero' => 'SOL-'.$solicitud->id, 'fecha' => $solicitud->fecha, 'estado' => $solicitud->estado,
            'id_presupuesto' => $solicitud->id_presupuesto, 'id_usuario_creacion' => $solicitud->id_usuario_creacion, 'motivo' => $solicitud->motivo,
            'subtotal' => Cantidad::decimal($solicitud->subtotal, 2), 'impuesto' => Cantidad::decimal($solicitud->impuesto, 2),
            'total' => Cantidad::decimal($solicitud->total, 2), 'centro' => $solicitud->centro ?? null,
        ];
    }
}
