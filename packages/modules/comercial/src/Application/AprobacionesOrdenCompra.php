<?php

namespace Aitrol\Comercial\Application;

use Aitrol\Inventarios\Domain\CantidadInventario as Cantidad;
use App\Services\ContextoOperativo;
use App\Services\EmpresaActiva;
use Illuminate\Database\Query\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use stdClass;

final readonly class AprobacionesOrdenCompra
{
    public const DECISIONES = ['aprobada', 'rechazada'];

    public const ESTADOS = [
        'pendiente' => 'Pendiente de aprobación',
        'aprobada' => 'Aprobada',
        'rechazada' => 'Rechazada',
        'anulada' => 'Anulada',
        'no_requerida' => 'Aprobación no requerida',
    ];

    public function __construct(private EmpresaActiva $empresa, private ContextoOperativo $contexto, private PresupuestosCompra $presupuestos) {}

    public function politica(): array
    {
        $actual = DB::table('com_politicas_aprobacion_compra')->where('id_empresa', $this->empresa->id())->first();

        return ['activa' => (bool) ($actual->activa ?? false), 'monto_minimo' => Cantidad::decimal((int) ($actual->monto_minimo ?? 0), 2)];
    }

    public function guardarPolitica(bool $activa, string|int|float $montoMinimo): array
    {
        return $this->empresa->transaccion(function () use ($activa, $montoMinimo): array {
            DB::table('com_politicas_aprobacion_compra')->upsert([[
                'id_empresa' => $this->empresa->id(), 'activa' => $activa, 'monto_minimo' => Cantidad::unidades($montoMinimo, 2),
                'id_usuario' => auth()->id(), 'created_at' => now(), 'updated_at' => now(),
            ]], ['id_empresa'], ['activa', 'monto_minimo', 'id_usuario', 'updated_at']);

            return $this->politica();
        });
    }

    public function umbralAplicable(int $total): ?int
    {
        $minimo = DB::table('com_politicas_aprobacion_compra')->where('id_empresa', $this->empresa->id())
            ->where('activa', true)->where('monto_minimo', '<=', $total)->value('monto_minimo');

        return $minimo === null ? null : (int) $minimo;
    }

    public function registrarEvento(int $orden, string $estado, ?string $motivo = null, ?int $montoMinimo = null): void
    {
        DB::table('com_decisiones_orden_compra')->insert([
            'id_empresa' => $this->empresa->id(), 'id_orden' => $orden, 'estado' => $estado, 'motivo' => $motivo,
            'id_usuario' => auth()->id(), 'responsable' => auth()->user()?->name ?? 'Sistema',
            'monto_minimo' => $montoMinimo, 'created_at' => now(),
        ]);
    }

    public function listar(string $estado, ?string $busqueda): LengthAwarePaginator
    {
        return $this->ordenes()->whereNotNull('estado_aprobacion')
            ->when($estado === 'anulada', fn (Builder $q) => $q->whereNotNull('anulado_at'),
                fn (Builder $q) => $q->whereNull('anulado_at')->where('estado_aprobacion', $estado))
            ->when(filled($busqueda), fn (Builder $q) => $q->where(fn (Builder $filtro) => $filtro
                ->whereLike('numero', '%'.$busqueda.'%')->orWhereLike('tercero', '%'.$busqueda.'%')))
            ->orderBy('id', $estado === 'pendiente' ? 'asc' : 'desc')->paginate(25)->withQueryString()
            ->through($this->presentarOrden(...));
    }

    public function detalle(int $id): array
    {
        $orden = $this->ordenes()->find($id) ?? abort(404, 'Orden de compra no encontrada.');
        $lineas = DB::table('com_lineas')->where(['id_empresa' => $this->empresa->id(), 'id_documento' => $id])->orderBy('id')
            ->get(['id', 'descripcion', 'cantidad', 'precio', 'subtotal', 'impuesto', 'total'])
            ->map(fn (stdClass $linea): array => [
                'id' => $linea->id, 'descripcion' => $linea->descripcion, 'cantidad' => Cantidad::decimal($linea->cantidad, 3),
                'precio' => Cantidad::decimal($linea->precio, 4), 'subtotal' => Cantidad::decimal($linea->subtotal, 2),
                'impuesto' => Cantidad::decimal($linea->impuesto, 2), 'total' => Cantidad::decimal($linea->total, 2),
            ]);
        $historial = DB::table('com_decisiones_orden_compra')->where(['id_empresa' => $this->empresa->id(), 'id_orden' => $id])->orderBy('id')
            ->get(['estado', 'motivo', 'id_usuario', 'responsable', 'monto_minimo', 'created_at'])
            ->map(fn (stdClass $evento): array => [
                'estado' => $evento->estado, 'motivo' => $evento->motivo, 'id_usuario' => $evento->id_usuario,
                'responsable' => $evento->responsable, 'monto_minimo' => $evento->monto_minimo === null ? null : Cantidad::decimal($evento->monto_minimo, 2),
                'fecha' => $evento->created_at,
            ]);

        return ['orden' => $this->presentarOrden($orden), 'lineas' => $lineas, 'historial' => $historial];
    }

    public function decidir(int $id, string $decision, ?string $motivo): array
    {
        return $this->empresa->transaccion(function () use ($id, $decision, $motivo): array {
            $consulta = $this->ordenes()->where('id', $id);
            $orden = (clone $consulta)->lockForUpdate()->first() ?? abort(404, 'Orden de compra no encontrada.');
            abort_if($orden->anulado_at !== null || $orden->estado_aprobacion !== 'pendiente', 409, 'La orden no está pendiente de aprobación.');
            abort_if((int) $orden->id_usuario_creacion === (int) auth()->id(), 403, 'La orden debe revisarla un usuario distinto de quien la registró.');
            $consulta->update(['estado_aprobacion' => $decision, 'updated_at' => now()]);
            $this->registrarEvento($id, $decision, $motivo);
            if ($decision === 'rechazada' && $orden->id_solicitud_compra !== null) {
                $this->presupuestos->liberarOrden($orden->id_solicitud_compra, $motivo);
            }

            return $this->detalle($id);
        });
    }

    public static function permiteOperar(?string $estado): bool
    {
        return $estado === null || $estado === 'aprobada';
    }

    private function ordenes(): Builder
    {
        return DB::table('com_documentos')->where([
            'id_empresa' => $this->empresa->id(), 'id_sucursal' => $this->contexto->sucursal()->id, 'modulo' => 'compras', 'tipo' => 'ordenes-compra',
        ])->select('id', 'numero', 'fecha', 'tercero', 'subtotal', 'impuesto', 'total', 'id_usuario_creacion', 'estado_aprobacion', 'anulado_at', 'id_solicitud_compra');
    }

    private function presentarOrden(stdClass $orden): array
    {
        return [
            'id' => $orden->id, 'numero' => $orden->numero, 'fecha' => $orden->fecha, 'proveedor' => $orden->tercero,
            'subtotal' => Cantidad::decimal($orden->subtotal, 2), 'impuesto' => Cantidad::decimal($orden->impuesto, 2),
            'total' => Cantidad::decimal($orden->total, 2), 'id_usuario_creacion' => $orden->id_usuario_creacion,
            'estado' => $orden->anulado_at !== null ? 'anulada' : ($orden->estado_aprobacion ?? 'no_requerida'),
        ];
    }
}
