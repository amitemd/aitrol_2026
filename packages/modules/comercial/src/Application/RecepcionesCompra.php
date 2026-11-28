<?php

namespace Aitrol\Comercial\Application;

use Aitrol\Contabilidad\Infrastructure\Queries\ReferenciaAsiento;
use Aitrol\Inventarios\Application\Inventario;
use Aitrol\Inventarios\Domain\CantidadInventario as Cantidad;
use App\Events\OperacionContabilizable;
use App\Services\ContextoOperativo;
use App\Services\EmpresaActiva;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use stdClass;

final readonly class RecepcionesCompra
{
    public function __construct(
        private EmpresaActiva $empresa,
        private ContextoOperativo $contexto,
        private Inventario $inventario,
        private CotejoRecepcionesCompra $cotejo,
    ) {}

    public function seguimiento(int $idOrden): array
    {
        $orden = $this->orden($idOrden);
        $lineas = $this->lineasOrden($idOrden);
        $recepciones = ReferenciaAsiento::agregar(
            $this->recepciones($idOrden)->select('com_documentos.*'), 'comercial.recepcion', 'com_documentos.id',
        )->addSelect([
            'bodega' => DB::table('inv_bodegas')->select('nombre')->whereColumn('id_empresa', 'com_documentos.id_empresa')
                ->whereColumn('id', 'com_documentos.id_bodega')->limit(1),
        ])->orderByDesc('id')->paginate(25)->withQueryString();
        $ids = $recepciones->getCollection()->pluck('id');
        $partidas = $this->cotejo->saldos($this->lineas()->whereIn('id_documento', $ids)
            ->orderBy('id')->get(['id', 'id_documento', 'id_linea_orden', 'descripcion', 'cantidad', 'precio', 'subtotal', 'impuesto', 'id_operacion']))
            ->groupBy('id_documento');
        $facturas = $this->cotejo->facturasVigentes()->whereIn('vinculo.id_recepcion', $ids)->orderBy('factura.id')
            ->get(['vinculo.id_recepcion', 'factura.id', 'factura.numero'])->groupBy('id_recepcion');
        $recepciones->through(function (stdClass $recepcion) use ($partidas, $facturas): array {
            $lineasRecepcion = $partidas->get($recepcion->id, collect());
            $facturasRecepcion = $facturas->get($recepcion->id, collect());
            $vigente = $recepcion->anulado_at === null;

            return $this->resumen($recepcion) + [
                'id_bodega' => $recepcion->id_bodega, 'bodega' => $recepcion->bodega, 'numero_asiento' => $recepcion->numero_asiento,
                'id_factura' => $facturasRecepcion->first()?->id, 'anulado_at' => $recepcion->anulado_at, 'motivo_anulacion' => $recepcion->motivo_anulacion,
                'facturas' => $facturasRecepcion->map(fn (stdClass $factura): array => ['id' => $factura->id, 'numero' => $factura->numero])->values(),
                'facturable' => $vigente && $lineasRecepcion->contains(fn (stdClass $linea): bool => $linea->cantidad_facturable > 0),
                'base_por_facturar' => Cantidad::decimal($vigente ? (int) $lineasRecepcion->sum('base_facturable') : 0, 2),
                'lineas' => $lineasRecepcion->map(fn (stdClass $linea): array => [
                    'id_linea_orden' => $linea->id_linea_orden, 'descripcion' => $linea->descripcion,
                    'cantidad' => Cantidad::decimal($linea->cantidad, 3), 'precio' => Cantidad::decimal($linea->precio, 4),
                    'subtotal' => Cantidad::decimal($linea->subtotal, 2), 'impuesto' => Cantidad::decimal($linea->impuesto, 2),
                    'cantidad_por_facturar' => Cantidad::decimal($vigente ? $linea->cantidad_facturable : 0, 3),
                    'base_por_facturar' => Cantidad::decimal($vigente ? $linea->base_facturable : 0, 2),
                    'impuesto_por_facturar' => Cantidad::decimal($vigente ? $linea->impuesto_facturable : 0, 2),
                    'id_operacion' => $linea->id_operacion,
                ])->values(),
            ];
        });
        $cantidadesFacturadas = $this->cotejo->cantidadesFacturadas($idOrden);

        return [
            'orden' => $this->resumen($orden) + [
                'proveedor' => $orden->tercero, 'anulada' => $orden->anulado_at !== null,
                'estado_aprobacion' => $orden->estado_aprobacion,
            ],
            'lineas' => $lineas->map(fn (stdClass $linea): array => [
                'id' => $linea->id, 'descripcion' => $linea->descripcion, 'inventariable' => $linea->tipo_producto === 'producto' || $linea->recibida > 0,
                'cantidad' => Cantidad::decimal($linea->cantidad, 3), 'recibida' => Cantidad::decimal($linea->recibida, 3),
                'pendiente' => Cantidad::decimal($linea->cantidad - $linea->recibida, 3),
                'precio' => Cantidad::decimal($linea->precio, 4), 'tasa' => Cantidad::decimal($linea->tasa, 2),
                'servicio_por_facturar' => $linea->tipo_producto !== 'producto' && (int) $linea->recibida === 0
                    ? Cantidad::decimal((int) $linea->cantidad - (int) $cantidadesFacturadas->get($linea->id, 0), 3) : null,
            ])->values(),
            'recepciones' => $recepciones,
        ];
    }

    /** @return array{0: array, 1: bool} */
    public function recibir(int $idOrden, array $datos): array
    {
        return $this->empresa->transaccion(function () use ($idOrden, $datos): array {
            $orden = $this->orden($idOrden);
            $numero = 'REC-'.Str::upper($datos['clave_operacion']);
            $cantidades = collect($datos['lineas'])->mapWithKeys(fn (array $fila): array => [
                (int) $fila['id_linea_orden'] => Cantidad::unidades($fila['cantidad'], 3),
            ])->sortKeys();
            $anterior = DB::table('com_documentos')->where([
                'id_empresa' => $this->empresa->id(), 'modulo' => 'compras', 'tipo' => 'recepciones', 'emisor' => 0, 'numero' => $numero,
            ])->first();
            if ($anterior !== null) {
                $previas = $this->lineas()->where('id_documento', $anterior->id)->pluck('cantidad', 'id_linea_orden')->sortKeys();
                abort_unless((int) $anterior->id_documento_origen === $idOrden && (int) $anterior->id_bodega === (int) $datos['id_bodega']
                    && $anterior->observacion === ($datos['observacion'] ?? null)
                    && $previas->map(fn ($cantidad): int => (int) $cantidad)->all() === $cantidades->all(), 409,
                    'La clave ya se utilizó para una recepción con otros datos.');

                return [$this->resumen($anterior) + ['anulada' => $anterior->anulado_at !== null], true];
            }

            $hoy = now('America/Guayaquil')->toDateString();
            abort_if($orden->anulado_at !== null || $orden->fecha > $hoy, 409, 'La orden debe estar vigente y tener una fecha igual o anterior a la recepción.');
            abort_unless(AprobacionesOrdenCompra::permiteOperar($orden->estado_aprobacion), 409, 'La orden debe estar aprobada para recibir mercadería.');
            if (! DB::table('inv_bodegas')->where([
                'id' => $datos['id_bodega'], 'id_empresa' => $this->empresa->id(), 'id_sucursal' => $orden->id_sucursal, 'activo' => true,
            ])->exists()) {
                throw ValidationException::withMessages(['id_bodega' => 'Selecciona una bodega activa de la sucursal de la orden.']);
            }
            $lineasOrden = $this->lineasOrden($idOrden)->keyBy('id');
            $lineas = [];
            foreach ($cantidades as $idLinea => $cantidad) {
                $linea = $lineasOrden->get($idLinea);
                if ($linea === null || $linea->tipo_producto !== 'producto' || $cantidad <= 0
                    || $cantidad > (int) $linea->cantidad - (int) $linea->recibida) {
                    throw ValidationException::withMessages(['lineas' => 'Selecciona productos inventariables de la orden y cantidades que no superen lo pendiente.']);
                }
                $lineas[] = $this->partidaRecepcion($linea, $cantidad);
            }
            $recepcion = $this->crearDocumento($orden, 'recepciones', $numero, $hoy, $lineas, $datos['observacion'] ?? null, (int) $datos['id_bodega']);
            usort($lineas, fn (array $a, array $b): int => [$a['id_producto'], $a['id_linea_orden']] <=> [$b['id_producto'], $b['id_linea_orden']]);
            foreach ($lineas as $linea) {
                $costo = intdiv($linea['subtotal'] * 100000 + intdiv($linea['cantidad'], 2), $linea['cantidad']);
                $movimiento = $this->inventario->registrar([
                    'tipo' => 'entrada', 'id_producto' => $linea['id_producto'], 'id_bodega' => $datos['id_bodega'],
                    'cantidad' => Cantidad::decimal($linea['cantidad'], 3), 'costo_unitario' => Cantidad::decimal($costo, 4),
                    'motivo' => 'Recepción de orden '.$orden->numero, 'referencia' => 'recepciones-compra/'.$recepcion->id,
                ], contabilizar: false)['operacion'];
                $this->insertarLinea($recepcion->id, $linea + ['id_operacion' => $movimiento->id, 'id_bodega' => $datos['id_bodega']]);
            }
            event(new OperacionContabilizable('comercial.recepcion', $recepcion->id, true));

            return [$this->resumen($recepcion) + ['anulada' => false], false];
        });
    }

    public function facturar(int $idOrden, array $datos): array
    {
        try {
            return $this->empresa->transaccion(function () use ($idOrden, $datos): array {
                $orden = $this->orden($idOrden);
                abort_if($orden->anulado_at !== null, 409, 'No puedes facturar una orden anulada.');
                abort_unless(AprobacionesOrdenCompra::permiteOperar($orden->estado_aprobacion), 409, 'La orden debe estar aprobada para cotejar una factura.');
                $ids = array_map('intval', $datos['recepciones']);
                $recepciones = $this->recepciones($idOrden)->whereNull('anulado_at')->whereIn('id', $ids)->orderBy('fecha')->orderBy('id')->get(['id', 'fecha']);
                if ($recepciones->count() !== count($ids)) {
                    throw ValidationException::withMessages(['recepciones' => 'Selecciona recepciones vigentes de esta orden.']);
                }
                $periodo = $this->contexto->periodo();
                if ($datos['fecha'] < $recepciones->max('fecha') || ($periodo !== null && (int) substr($datos['fecha'], 0, 4) !== $periodo)) {
                    throw ValidationException::withMessages(['fecha' => 'La fecha debe ser igual o posterior a la última recepción seleccionada y pertenecer al período activo.']);
                }
                $recibidas = $this->lineas()->whereIn('id_documento', $ids)->orderBy('id')->get();
                $fiscal = ['forma_pago' => $datos['forma_pago'], 'plazo_dias' => (int) ($datos['plazo_dias'] ?? 0)];
                $aplicaciones = [];
                if (($datos['modo_cotejo'] ?? 'completo') === 'parcial') {
                    $posicion = $recepciones->pluck('id')->flip();
                    $preparacion = $this->cotejo->preparar($idOrden, $this->lineasOrden($idOrden)->keyBy('id'),
                        $this->cotejo->saldos($recibidas)->sortBy(fn (stdClass $linea): array => [$posicion[$linea->id_documento], $linea->id]), $datos);
                    $lineas = $preparacion['lineas'];
                    $aplicaciones = $preparacion['aplicaciones'];
                    $fiscal['cotejo'] = $preparacion['cotejo'];
                } else {
                    abort_if($this->cotejo->facturasVigentes()->whereIn('vinculo.id_recepcion', $ids)->exists(), 409, 'Una recepción ya está vinculada a una factura vigente.');
                    $this->cotejarCompleta($recibidas, $datos);
                    $lineas = $recibidas->map(fn (stdClass $linea): array => array_intersect_key((array) $linea,
                        array_flip(['id_producto', 'descripcion', 'cantidad', 'precio', 'tasa', 'subtotal', 'impuesto', 'total'])))->all();
                }
                $factura = $this->crearDocumento($orden, 'documentos', Str::upper($datos['numero']), $datos['fecha'], $lineas,
                    $datos['observacion'] ?? null, fiscal: $fiscal);
                foreach ($lineas as $linea) {
                    $this->insertarLinea($factura->id, $linea);
                }
                DB::table('com_facturas_recepciones')->insert(array_map(fn (int $id): array => [
                    'id_empresa' => $this->empresa->id(), 'id_factura' => $factura->id, 'id_recepcion' => $id,
                ], $ids));
                foreach (array_chunk($aplicaciones, 100) as $lote) {
                    DB::table('com_aplicaciones_recepcion')->insert(array_map(fn (array $aplicacion): array => [
                        'id_empresa' => $this->empresa->id(), 'id_factura' => $factura->id,
                    ] + array_diff_key($aplicacion, ['id_recepcion' => true]), $lote));
                }
                event(new OperacionContabilizable('comercial.documento', $factura->id, true));

                return $this->resumen($factura) + (isset($fiscal['cotejo']) ? ['cotejo' => $this->cotejo->detalleFactura($factura)] : []);
            });
        } catch (UniqueConstraintViolationException) {
            abort(409, 'El número de factura ya está registrado para este proveedor.');
        }
    }

    public function anular(int $idOrden, int $idRecepcion, string $motivo): void
    {
        $this->empresa->transaccion(function () use ($idOrden, $idRecepcion, $motivo): void {
            $orden = $this->orden($idOrden);
            $recepcion = $this->recepciones($idOrden)->where('id', $idRecepcion)->first() ?? abort(404);
            abort_if($recepcion->anulado_at !== null, 409, 'La recepción ya fue anulada.');
            abort_if($this->cotejo->facturasVigentes()->where('vinculo.id_recepcion', $idRecepcion)->exists(), 409,
                'Anula primero las facturas vigentes vinculadas antes de revertir la recepción.');
            foreach ($this->lineas()->where('id_documento', $idRecepcion)->orderBy('id_producto')->orderBy('id')->get() as $linea) {
                $movimiento = $this->inventario->registrar([
                    'tipo' => 'salida', 'id_producto' => $linea->id_producto, 'id_bodega' => $recepcion->id_bodega,
                    'cantidad' => Cantidad::decimal($linea->cantidad, 3), 'motivo' => 'Reversión de recepción de '.$orden->numero,
                    'referencia' => 'recepciones-compra/'.$idRecepcion,
                ], contabilizar: false)['operacion'];
                $this->lineas()->where('id', $linea->id)->update(['id_operacion_reversa' => $movimiento->id]);
            }
            $this->recepciones($idOrden)->where('id', $idRecepcion)->update([
                'anulado_at' => now(), 'id_usuario_anulacion' => auth()->id(), 'motivo_anulacion' => $motivo, 'updated_at' => now(),
            ]);
            event(new OperacionContabilizable('comercial.anulacion-recepcion', $idRecepcion, true));
        });
    }

    private function cotejarCompleta(Collection $recibidas, array $datos): void
    {
        $esperadas = $recibidas->groupBy('id_linea_orden')->map(fn (Collection $partidas): array => [
            'cantidad' => (int) $partidas->sum('cantidad'), 'precio' => (int) $partidas->first()->precio,
        ])->sortKeys()->all();
        $facturadas = collect($datos['lineas'])->mapWithKeys(fn (array $fila): array => [
            (int) $fila['id_linea_orden'] => ['cantidad' => Cantidad::unidades($fila['cantidad'], 3), 'precio' => Cantidad::unidades($fila['precio'], 4)],
        ])->sortKeys()->all();
        if ($esperadas !== $facturadas) {
            throw ValidationException::withMessages(['lineas' => 'Las líneas, cantidades y precios de la factura deben coincidir con las recepciones seleccionadas.']);
        }
        foreach (['subtotal', 'impuesto'] as $campo) {
            if (Cantidad::unidades($datos[$campo], 2) !== (int) $recibidas->sum($campo)) {
                throw ValidationException::withMessages([$campo => 'El importe de la factura no coincide con las recepciones seleccionadas.']);
            }
        }
    }

    private function partidaRecepcion(stdClass $linea, int $cantidad): array
    {
        $baseDisponible = (int) $linea->subtotal - (int) $linea->base_recibida;
        $impuestoDisponible = (int) $linea->impuesto - (int) $linea->impuesto_recibido;
        $completa = $cantidad === (int) $linea->cantidad - (int) $linea->recibida;
        $unidadesOrdenadas = (int) $linea->cantidad;
        $baseOrdenada = (int) $linea->subtotal;
        $baseProporcional = intdiv($baseOrdenada, $unidadesOrdenadas) * $cantidad
            + intdiv(($baseOrdenada % $unidadesOrdenadas) * $cantidad + intdiv($unidadesOrdenadas, 2), $unidadesOrdenadas);
        $subtotal = $completa ? $baseDisponible : min($baseDisponible, $baseProporcional);
        $impuesto = $completa ? $impuestoDisponible : min($impuestoDisponible, intdiv($subtotal * (int) $linea->tasa + 5000, 10000));
        if ($subtotal < 1 || (! $completa && $subtotal >= $baseDisponible)) {
            throw ValidationException::withMessages(['lineas' => 'La cantidad recibida o el saldo de la línea no alcanza un centavo. Agrupa una cantidad mayor.']);
        }

        return ['id_linea_orden' => $linea->id, 'id_producto' => $linea->id_producto, 'descripcion' => $linea->descripcion,
            'cantidad' => $cantidad, 'precio' => (int) $linea->precio, 'tasa' => $linea->tasa, 'subtotal' => $subtotal,
            'impuesto' => $impuesto, 'total' => $subtotal + $impuesto];
    }

    private function crearDocumento(stdClass $orden, string $tipo, string $numero, string $fecha, array $lineas, ?string $observacion, ?int $bodega = null, ?array $fiscal = null): stdClass
    {
        $subtotal = array_sum(array_column($lineas, 'subtotal'));
        $impuesto = array_sum(array_column($lineas, 'impuesto'));
        $documento = [
            'id_empresa' => $this->empresa->id(), 'id_sucursal' => $orden->id_sucursal, 'modulo' => 'compras', 'tipo' => $tipo,
            'id_documento_origen' => $orden->id, 'id_proveedor' => $orden->id_proveedor,
            'emisor' => $tipo === 'recepciones' ? 0 : $orden->id_proveedor,
            'tercero' => $orden->tercero, 'id_bodega' => $bodega, 'numero' => $numero, 'fecha' => $fecha,
            'subtotal' => $subtotal, 'impuesto' => $impuesto, 'total' => $subtotal + $impuesto, 'observacion' => $observacion,
            'datos_factura' => $fiscal === null ? null : json_encode($fiscal, JSON_THROW_ON_ERROR),
            'id_usuario_creacion' => auth()->id(), 'created_at' => now(), 'updated_at' => now(),
        ];

        return (object) (['id' => DB::table('com_documentos')->insertGetId($documento)] + $documento);
    }

    private function insertarLinea(int $documento, array $linea): void
    {
        $this->lineas()->insert($linea + ['id_empresa' => $this->empresa->id(), 'id_documento' => $documento]);
    }

    private function resumen(stdClass $documento): array
    {
        return ['id' => $documento->id, 'numero' => $documento->numero, 'fecha' => $documento->fecha,
            'subtotal' => Cantidad::decimal($documento->subtotal, 2), 'impuesto' => Cantidad::decimal($documento->impuesto, 2),
            'total' => Cantidad::decimal($documento->total, 2)];
    }

    private function lineasOrden(int $id): Collection
    {
        $recibido = $this->lineas()->whereIn('id_documento', $this->recepciones($id)->whereNull('anulado_at')->select('id'))
            ->select('id_linea_orden')->selectRaw('SUM(cantidad) as cantidad, SUM(subtotal) as base, SUM(impuesto) as impuesto')
            ->groupBy('id_linea_orden');

        return $this->lineas()->where('com_lineas.id_documento', $id)
            ->leftJoinSub($recibido, 'recibido', 'recibido.id_linea_orden', '=', 'com_lineas.id')
            ->leftJoin('inv_productos as producto', fn ($join) => $join->on('producto.id', '=', 'com_lineas.id_producto')
                ->on('producto.id_empresa', '=', 'com_lineas.id_empresa'))
            ->select('com_lineas.*', 'producto.tipo_producto')
            ->selectRaw('COALESCE(recibido.cantidad, 0) AS recibida, COALESCE(recibido.base, 0) AS base_recibida, COALESCE(recibido.impuesto, 0) AS impuesto_recibido')
            ->orderBy('com_lineas.id')->get();
    }

    private function lineas(): Builder
    {
        return DB::table('com_lineas')->where('com_lineas.id_empresa', $this->empresa->id());
    }

    private function orden(int $id): stdClass
    {
        return $this->documentos('ordenes-compra')->where('id', $id)->first() ?? abort(404, 'Orden de compra no encontrada.');
    }

    private function recepciones(int $idOrden): Builder
    {
        return $this->documentos('recepciones')->where('id_documento_origen', $idOrden);
    }

    private function documentos(string $tipo): Builder
    {
        return DB::table('com_documentos')->where([
            'id_empresa' => $this->empresa->id(), 'id_sucursal' => $this->contexto->sucursal()->id, 'modulo' => 'compras', 'tipo' => $tipo,
        ]);
    }
}
