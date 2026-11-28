<?php

namespace Aitrol\Comercial\Application;

use Aitrol\Contabilidad\Infrastructure\Queries\ReferenciaAsiento;
use Aitrol\Inventarios\Application\Inventario;
use Aitrol\Inventarios\Application\RepositorioInventario;
use Aitrol\Inventarios\Domain\CantidadInventario as Cantidad;
use App\Events\OperacionContabilizable;
use App\Services\ContextoOperativo;
use App\Services\EmpresaActiva;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use stdClass;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class DocumentosComerciales
{
    public const VINCULADOS = ['notas-credito', 'notas-debito', 'retenciones'];

    public function __construct(
        private EmpresaActiva $empresa,
        private Inventario $inventario,
        private RepositorioInventario $existencias,
        private ContextoOperativo $contexto,
    ) {}

    public function listar(string $modulo, string $seccion, array $filtros, int $porPagina = 20, bool $incluirSri = false): LengthAwarePaginator
    {
        $consulta = $this->filtrar($this->conAsientos($this->documentos($modulo)), $filtros);
        if ($incluirSri && $modulo === 'ventas' && $seccion === 'documentos') {
            $consulta->addSelect(['estado_sri' => DB::table('com_sri_consultas')->select('estado')
                ->whereColumn('id_empresa', 'com_documentos.id_empresa')->whereColumn('id_documento', 'com_documentos.id')
                ->latest('id')->limit(1)]);
        }
        $this->contexto->aplicarPeriodo($consulta, 'fecha');
        if ($seccion === 'anulaciones') {
            $consulta->whereNotNull('anulado_at')->orderByDesc('anulado_at');
        } else {
            $consulta->where('tipo', $seccion)->orderByDesc('fecha');
        }

        return $consulta->orderByDesc('id')->paginate($filtros['per_page'] ?? $porPagina)
            ->appends($filtros);
    }

    public function consultar(string $modulo, string $seccion, int $id): stdClass
    {
        $consulta = $this->conAsientos($this->documentos($modulo))->where('id', $id);
        $this->contexto->aplicarPeriodo($consulta, 'fecha');
        $seccion === 'anulaciones' ? $consulta->whereNotNull('anulado_at') : $consulta->where('tipo', $seccion);
        $documento = $consulta->first() ?? abort(404, 'Documento no encontrado.');
        $documento->lineas = $this->lineasConBodega()->where('com_lineas.id_documento', $id)->get();
        $documento->numero_origen = $documento->id_documento_origen === null ? null
            : $this->documentos($modulo)->where('id', $documento->id_documento_origen)->value('numero');

        return $documento;
    }

    public function listarConDetalle(string $modulo, string $seccion, array $filtros): LengthAwarePaginator
    {
        unset($filtros['per_page']);
        $documentos = $this->listar($modulo, $seccion, $filtros, porPagina: 12, incluirSri: true);
        $registros = $documentos->getCollection();
        if ($registros->isEmpty()) {
            return $documentos;
        }

        $lineas = $this->lineasConBodega()->whereIn('com_lineas.id_documento', $registros->pluck('id'))
            ->get()->groupBy('id_documento');
        $idsOrigen = $registros->pluck('id_documento_origen')->filter()->unique();
        $origenes = $idsOrigen->isEmpty() ? collect()
            : $this->documentos($modulo)->whereIn('id', $idsOrigen)->pluck('numero', 'id');

        return $documentos->through(function (stdClass $documento) use ($lineas, $origenes): stdClass {
            $documento->lineas = $lineas->get($documento->id, collect());
            $documento->numero_origen = $origenes->get($documento->id_documento_origen);

            return $documento;
        });
    }

    public function registrar(string $modulo, string $seccion, array $datos, ?array $factura = null, bool $creditoTotal = false): stdClass
    {
        abort_if($creditoTotal && ($modulo !== 'ventas' || $seccion !== 'notas-credito'), 422);
        $periodo = $this->contexto->periodo();
        if ($periodo !== null && (int) substr($datos['fecha'], 0, 4) !== $periodo) {
            throw ValidationException::withMessages(['fecha' => "La fecha debe pertenecer al período {$periodo}."]);
        }
        $vinculado = in_array($seccion, self::VINCULADOS, true);
        try {
            return $this->empresa->transaccion(function () use ($modulo, $seccion, $datos, $vinculado, $factura, $creditoTotal): stdClass {
                $origen = $vinculado ? $this->origen($modulo, (int) $datos['id_documento_origen']) : null;
                $campo = $modulo === 'ventas' ? 'id_cliente' : 'id_proveedor';
                $tercero = $origen?->$campo ?? (int) $datos['id_tercero'];
                $nombre = $origen?->tercero ?? $this->validarTercero($modulo, $tercero);
                $lineasOrigen = $creditoTotal ? $this->lineas($origen->id)->orderBy('id')->get() : collect();
                if ($creditoTotal && $this->documentos($modulo)->where('id_documento_origen', $origen->id)->whereNull('anulado_at')->exists()) {
                    throw ValidationException::withMessages(['id_documento_origen' => 'La factura tiene notas o retenciones vigentes. Revisa la trazabilidad antes de registrar una nota por el total.']);
                }
                $lineas = match (true) {
                    $creditoTotal => $lineasOrigen->map(fn (stdClass $fila): array => array_intersect_key((array) $fila,
                        array_flip(['id_producto', 'id_bodega', 'descripcion', 'cantidad', 'precio', 'tasa', 'subtotal', 'impuesto', 'total'])))->all(),
                    $seccion === 'retenciones' => $this->prepararRetenciones($datos['lineas']),
                    default => $this->prepararLineas($datos['lineas']),
                };
                $subtotal = array_sum(array_column($lineas, 'subtotal'));
                $impuesto = array_sum(array_column($lineas, 'impuesto'));
                $propina = $creditoTotal ? (int) $origen->propina
                    : ($factura !== null && ($datos['aplicar_propina'] ?? false) ? intdiv($subtotal + 5, 10) : 0);
                $total = $subtotal + $impuesto + $propina;
                if ($origen !== null) {
                    if ($datos['fecha'] < $origen->fecha) {
                        throw ValidationException::withMessages(['fecha' => 'La fecha debe ser igual o posterior a la del documento de origen.']);
                    }
                    $this->verificarImportes($modulo, $origen, $seccion, $total);
                }
                $bodega = $seccion === 'documentos' && isset($datos['id_bodega']) ? (int) $datos['id_bodega'] : null;
                $tieneProducto = collect($lineas)->contains(fn (array $linea) => $linea['id_producto'] !== null);
                if ($bodega !== null && ! $tieneProducto) {
                    throw ValidationException::withMessages(['id_bodega' => 'Agrega al menos un producto de inventario para usar una bodega.']);
                }
                if ($modulo === 'compras' && $seccion === 'documentos' && $tieneProducto) {
                    $principal = $this->existencias->bodegaPrincipal();
                    if ($principal === null) {
                        throw ValidationException::withMessages(['id_bodega' => 'Registra una bodega principal activa para recibir la mercadería de esta compra.']);
                    }
                    if ($bodega !== null && $bodega !== (int) $principal->id) {
                        throw ValidationException::withMessages(['id_bodega' => 'Selecciona la bodega principal de la empresa para recibir la compra.']);
                    }
                    $bodega = (int) $principal->id;
                }
                if ($factura === null && $seccion === 'documentos' && $bodega === null && $tieneProducto) {
                    throw ValidationException::withMessages(['id_bodega' => 'Selecciona una bodega para los productos de inventario.']);
                }
                $numero = Str::upper($datos['numero']);
                if (Str::length($numero) > 60) {
                    throw ValidationException::withMessages(['numero' => 'El número debe tener hasta 60 caracteres en mayúsculas.']);
                }
                $id = DB::table('com_documentos')->insertGetId([
                    'id_empresa' => $this->empresa->id(), 'modulo' => $modulo, 'tipo' => $seccion,
                    'id_sucursal' => $this->contexto->sucursal()->id,
                    'numero' => $numero, 'emisor' => $modulo === 'compras' ? $tercero : 0,
                    $campo => $tercero, 'tercero' => $nombre, 'fecha' => $datos['fecha'],
                    'id_documento_origen' => $origen?->id, 'id_bodega' => $bodega, 'subtotal' => $subtotal,
                    'impuesto' => $impuesto, 'total' => $total, 'observacion' => $datos['observacion'] ?? null,
                    'id_usuario_creacion' => auth()->id(), 'created_at' => now(), 'updated_at' => now(),
                ] + ($creditoTotal ? ['propina' => $propina, 'datos_factura' => json_encode(['credito_total' => true], JSON_THROW_ON_ERROR)] : []) + ($factura === null ? [] : [
                    'propina' => $propina,
                    'datos_factura' => json_encode($factura + ['propina_centavos' => $propina, 'total_pago_centavos' => $total], JSON_THROW_ON_ERROR),
                ]));
                // El orden por producto mantiene consistente la adquisición de bloqueos de inventario.
                $orden = array_keys($lineas);
                usort($orden, fn (int $a, int $b) => [$lineas[$a]['id_producto'], $lineas[$a]['id_bodega'] ?? $bodega]
                    <=> [$lineas[$b]['id_producto'], $lineas[$b]['id_bodega'] ?? $bodega]);
                foreach ($orden as $indice) {
                    $bodegaLinea = $lineas[$indice]['id_bodega'] ?? $bodega;
                    if ($creditoTotal && $lineasOrigen[$indice]->id_operacion !== null) {
                        $linea = $lineasOrigen[$indice];
                        $movimiento = $this->existencias->operacion((int) $linea->id_operacion);
                        $lineas[$indice]['id_operacion'] = $this->inventario->registrar([
                            'tipo' => 'entrada', 'id_producto' => $linea->id_producto, 'id_bodega' => $movimiento->id_bodega,
                            'cantidad' => Cantidad::decimal((int) $linea->cantidad, 3),
                            'costo_unitario' => Cantidad::decimal((int) $movimiento->costo_unitario, 4),
                            'motivo' => 'Nota de crédito total', 'referencia' => $modulo.'/'.$id,
                        ], contabilizar: false)['operacion']->id;
                    } elseif (! $creditoTotal && $bodegaLinea !== null && $lineas[$indice]['id_producto'] !== null) {
                        $lineas[$indice]['id_operacion'] = $this->afectarInventario($modulo, $bodegaLinea, $id, $lineas[$indice]);
                    }
                }
                foreach ($lineas as $linea) {
                    DB::table('com_lineas')->insert($linea + ['id_empresa' => $this->empresa->id(), 'id_documento' => $id]);
                }
                event(new OperacionContabilizable('comercial.documento', $id, $factura !== null || $creditoTotal));

                return $this->consultar($modulo, $seccion, $id);
            });
        } catch (UniqueConstraintViolationException) {
            if ($factura !== null) {
                throw ValidationException::withMessages(['numero' => 'Este número de factura ya está registrado.']);
            }
            abort(409, 'El número ya está registrado para este tipo de documento y emisor.');
        }
    }

    public function anular(string $modulo, string $seccion, int $id, string $motivo): stdClass
    {
        $idOrigen = null;
        if (in_array($seccion, self::VINCULADOS, true)) {
            $idOrigen = $this->documentos($modulo)->where('tipo', $seccion)->where('id', $id)->value('id_documento_origen')
                ?? abort(404, 'Documento no encontrado.');
        }

        return $this->empresa->transaccion(function () use ($modulo, $seccion, $id, $motivo, $idOrigen): stdClass {
            // Las notas y retenciones toman siempre el bloqueo del documento base antes del propio.
            $origen = $idOrigen === null ? null
                : $this->documentos($modulo)->where('tipo', 'documentos')->where('id', $idOrigen)->lockForUpdate()->first();
            $documento = $this->contexto->aplicarPeriodo($this->documentos($modulo), 'fecha')
                ->where('tipo', $seccion)->where('id', $id)->lockForUpdate()->first()
                ?? abort(404, 'Documento no encontrado.');
            abort_if($documento->anulado_at !== null, 409, 'El documento ya está anulado.');
            if ($origen !== null && $seccion === 'notas-debito') {
                $this->verificarImportes($modulo, $origen, $seccion, -(int) $documento->total);
            }
            abort_if($this->documentos($modulo)->where('id_documento_origen', $id)->whereNull('anulado_at')->lockForUpdate()->first(['id']) !== null, 409,
                'Anula primero las notas o retenciones vigentes vinculadas al documento.');

            foreach ($this->lineas($id)->whereNotNull('id_operacion')->orderBy('id_producto')->orderBy('id_bodega')->orderBy('id')->get() as $linea) {
                $movimiento = $this->existencias->operacion((int) $linea->id_operacion, todaEmpresa: $modulo === 'compras');
                $esEntrada = $movimiento->tipo === 'salida';
                $reversa = $this->inventario->registrar([
                    'tipo' => $esEntrada ? 'entrada' : 'salida', 'id_producto' => $linea->id_producto, 'id_bodega' => $movimiento->id_bodega,
                    'cantidad' => Cantidad::decimal((int) $linea->cantidad, 3),
                    'costo_unitario' => $esEntrada ? Cantidad::decimal((int) $movimiento->costo_unitario, 4) : null,
                    'motivo' => 'Anulación de '.($modulo === 'ventas' ? 'venta' : 'compra'), 'referencia' => $modulo.'/'.$id,
                ], contabilizar: false, bodegaOrigenExterna: $modulo === 'compras');
                $this->lineas($id)->where('id', $linea->id)->update(['id_operacion_reversa' => $reversa['operacion']->id]);
            }
            $this->documentos($modulo)->where('id', $id)->update([
                'anulado_at' => now(), 'motivo_anulacion' => $motivo, 'id_usuario_anulacion' => auth()->id(), 'updated_at' => now(),
            ]);
            event(new OperacionContabilizable('comercial.anulacion', $id));

            return $this->consultar($modulo, 'anulaciones', $id);
        });
    }

    public function vinculadosVenta(int $id): \Illuminate\Support\Collection
    {
        return $this->conAsientos($this->documentos('ventas')->where('id_documento_origen', $id))
            ->orderBy('fecha')->orderBy('id')->get();
    }

    public function contabilizarVenta(int $id, \Aitrol\Contabilidad\Infrastructure\ContabilizacionOperativa $contabilidad): void
    {
        $this->empresa->transaccion(function () use ($id, $contabilidad): void {
            $factura = $this->contexto->aplicarPeriodo($this->documentos('ventas'), 'fecha')
                ->where('tipo', 'documentos')->where('id', $id)->lockForUpdate()->first()
                ?? abort(404, 'Documento no encontrado.');
            abort_if($factura->anulado_at !== null, 409, 'La factura está anulada.');
            $contabilidad->contabilizarFactura($id);
        });
    }

    public function reporte(string $modulo, array $filtros, ?ConnectionInterface $conexion = null): array
    {
        $filas = $this->filtrar($this->documentos($modulo, $conexion), $filtros)
            ->select('tipo')->selectRaw('case when anulado_at is null then 0 else 1 end as anulado')
            ->selectRaw('count(*) as cantidad, sum(subtotal) as subtotal, sum(impuesto) as impuesto, sum(total) as total')
            ->groupBy('tipo')->groupByRaw('case when anulado_at is null then 0 else 1 end')->orderBy('tipo')->orderBy('anulado')->get();
        $neto = 0;
        $retenciones = 0;
        foreach ($filas as $fila) {
            if ($fila->anulado) {
                continue;
            }
            $neto += match ($fila->tipo) {
                'documentos', 'notas-debito' => (int) $fila->total,
                'notas-credito' => -(int) $fila->total,
                default => 0,
            };
            if ($fila->tipo === 'retenciones') {
                $retenciones += (int) $fila->total;
            }
        }

        return ['filas' => $filas, 'neto' => $neto, 'retenciones' => $retenciones, 'neto_menos_retenciones' => $neto - $retenciones];
    }

    public function opciones(string $modulo, string $catalogo, ?string $busqueda, bool $factura = false): LengthAwarePaginator
    {
        $consulta = match ($catalogo) {
            'terceros' => $this->terceros($modulo)->select('id', 'nombre as texto', 'identificacion as codigo'),
            'origenes' => $this->documentos($modulo)->where('tipo', 'documentos')->whereNull('anulado_at')->select('id', 'tercero as texto', 'numero as codigo'),
            'bodegas', 'productos' => DB::table('inv_'.$catalogo)->where('id_empresa', $this->empresa->id())
                ->when($catalogo === 'bodegas', fn (Builder $q) => $q->where('id_sucursal', $this->contexto->sucursal()->id))
                ->where('activo', true)->select('id', 'nombre as texto', 'codigo'),
        };
        if ($factura && $catalogo === 'productos') {
            $consulta->addSelect('precio_venta', 'iva_codigo', 'tipo_producto');
        }
        $campos = match ($catalogo) {
            'terceros' => ['nombre', 'identificacion'],
            'origenes' => ['tercero', 'numero'],
            default => ['nombre', 'codigo'],
        };
        if ($busqueda !== null && $busqueda !== '') {
            $consulta->where(fn (Builder $q) => $q->whereLike($campos[0], '%'.$busqueda.'%')->orWhereLike($campos[1], '%'.$busqueda.'%'));
        }

        return $consulta->orderBy('texto')->orderBy('id')->paginate(20);
    }

    private function origen(string $modulo, int $id): stdClass
    {
        return $this->documentos($modulo)->where('tipo', 'documentos')->whereNull('anulado_at')->where('id', $id)->lockForUpdate()->first()
            ?? throw ValidationException::withMessages(['id_documento_origen' => 'Selecciona un documento vigente de tu empresa.']);
    }

    private function verificarImportes(string $modulo, stdClass $origen, string $tipo, int $monto): void
    {
        // Una lectura con bloqueo ve los importes confirmados aun bajo REPEATABLE READ.
        $saldos = $this->documentos($modulo)->where('id_documento_origen', $origen->id)->whereNull('anulado_at')
            ->lockForUpdate()->get(['tipo', 'total'])->groupBy('tipo')->map(fn ($filas) => $filas->sum('total'));
        $saldos[$tipo] = ($saldos[$tipo] ?? 0) + $monto;
        $valor = $origen->total + ($saldos['notas-debito'] ?? 0) - ($saldos['notas-credito'] ?? 0);
        if ($valor < 0 || $valor < ($saldos['retenciones'] ?? 0)) {
            throw ValidationException::withMessages(['lineas' => 'El importe dejaría el documento por debajo del total retenido o de cero.']);
        }
    }

    private function prepararLineas(array $lineas): array
    {
        $preparadas = [];
        foreach ($lineas as $indice => $linea) {
            $producto = isset($linea['id_producto']) ? (int) $linea['id_producto'] : null;
            $descripcion = $linea['descripcion'] ?? null;
            if ($producto !== null) {
                try {
                    $registro = $this->existencias->catalogo('productos', $producto);
                } catch (NotFoundHttpException) {
                    $registro = null;
                }
                if (! $registro?->activo) {
                    throw ValidationException::withMessages(['lineas.'.$indice.'.id_producto' => 'Selecciona un producto activo de tu empresa.']);
                }
                $descripcion ??= $registro->nombre;
            }
            $cantidad = Cantidad::unidades($linea['cantidad'], 3);
            $precio = Cantidad::unidades($linea['precio'], 4);
            $tasa = Cantidad::unidades($linea['tasa'] ?? 0, 2);
            $base = intdiv($cantidad * $precio + 50000, 100000);
            $descuento = Cantidad::unidades($linea['descuento'] ?? 0, 2);
            if ($descuento > $base) {
                throw ValidationException::withMessages(["lineas.$indice.descuento" => 'El descuento no puede superar el importe de la línea.']);
            }
            $subtotal = $base - $descuento;
            if ($base === 0) {
                throw ValidationException::withMessages(['lineas.'.$indice.'.precio' => 'El subtotal de cada línea debe ser al menos 0.01.']);
            }
            $impuesto = intdiv($subtotal * $tasa + 5000, 10000);
            $preparadas[] = [
                'id_producto' => $producto, 'descripcion' => $descripcion, 'cantidad' => $cantidad, 'precio' => $precio,
                'tasa' => $tasa, 'subtotal' => $subtotal, 'impuesto' => $impuesto, 'total' => $subtotal + $impuesto,
            ] + (isset($linea['id_bodega']) ? ['id_bodega' => (int) $linea['id_bodega']] : []);
        }

        return $preparadas;
    }

    private function prepararRetenciones(array $lineas): array
    {
        $preparadas = [];
        foreach ($lineas as $indice => $linea) {
            $base = Cantidad::unidades($linea['base_retencion'], 2);
            $porcentaje = Cantidad::unidades($linea['porcentaje_retencion'], 2);
            $importe = intdiv($base * $porcentaje + 5000, 10000);
            if ($importe === 0) {
                throw ValidationException::withMessages(['lineas.'.$indice.'.porcentaje_retencion' => 'El importe retenido debe ser al menos 0.01.']);
            }
            $preparadas[] = [
                'id_producto' => null, 'descripcion' => $linea['descripcion'],
                'tipo_retencion' => $linea['tipo_retencion'], 'codigo_retencion' => $linea['codigo_retencion'],
                'base_retencion' => $base, 'porcentaje_retencion' => $porcentaje,
                'cantidad' => 1000, 'precio' => $importe * 100, 'tasa' => 0,
                'subtotal' => $importe, 'impuesto' => 0, 'total' => $importe,
            ];
        }

        return $preparadas;
    }

    private function afectarInventario(string $modulo, int $bodega, int $documento, array $linea): int
    {
        $compra = $modulo === 'compras';
        $movimiento = $this->inventario->registrar([
            'tipo' => $compra ? 'entrada' : 'salida', 'id_producto' => $linea['id_producto'], 'id_bodega' => $bodega,
            'cantidad' => Cantidad::decimal($linea['cantidad'], 3),
            'costo_unitario' => $compra ? Cantidad::decimal($linea['precio'], 4) : null,
            'motivo' => $compra ? 'Registro de compra' : 'Registro de venta', 'referencia' => $modulo.'/'.$documento,
        ], contabilizar: false, bodegaOrigenExterna: $compra);

        return (int) $movimiento['operacion']->id;
    }

    private function validarTercero(string $modulo, int $id): string
    {
        return $this->terceros($modulo)->where('id', $id)->value('nombre')
            ?? throw ValidationException::withMessages(['id_tercero' => 'Selecciona un cliente o proveedor activo de tu empresa.']);
    }

    private function terceros(string $modulo): Builder
    {
        return DB::table($modulo === 'ventas' ? 'erp_clientes' : 'erp_proveedores')
            ->where('id_empresa', $this->empresa->id())->whereNull('deleted_at')->where('activo', true);
    }

    private function documentos(string $modulo, ?ConnectionInterface $conexion = null): Builder
    {
        return ($conexion ?? DB::connection())->table('com_documentos')
            ->where('id_empresa', $this->empresa->id())->where('modulo', $modulo)
            ->when($conexion === null, fn (Builder $q) => $q->where('id_sucursal', $this->contexto->sucursal()->id));
    }

    private function conAsientos(Builder $consulta): Builder
    {
        $consulta->select('com_documentos.*');
        ReferenciaAsiento::agregar($consulta, 'comercial.documento', 'com_documentos.id');

        return ReferenciaAsiento::agregar($consulta, 'comercial.anulacion', 'com_documentos.id', 'asiento_reversion');
    }

    private function lineas(int $id): Builder
    {
        return DB::table('com_lineas')->where('com_lineas.id_empresa', $this->empresa->id())->where('com_lineas.id_documento', $id);
    }

    private function lineasConBodega(): Builder
    {
        return DB::table('com_lineas')->where('com_lineas.id_empresa', $this->empresa->id())
            ->leftJoin('inv_bodegas as bodega', function (JoinClause $join): void {
                $join->on('bodega.id_empresa', '=', 'com_lineas.id_empresa')
                    ->on('bodega.id', '=', 'com_lineas.id_bodega');
            })->select('com_lineas.*', 'bodega.nombre as nombre_bodega')->orderBy('com_lineas.id');
    }

    private function filtrar(Builder $consulta, array $filtros): Builder
    {
        if (isset($filtros['desde'])) $consulta->where('fecha', '>=', $filtros['desde']);
        if (isset($filtros['hasta'])) $consulta->where('fecha', '<=', $filtros['hasta']);
        if (isset($filtros['q'])) {
            $patron = '%'.$filtros['q'].'%';
            $consulta->where(fn (Builder $q) => $q->whereLike('numero', $patron)->orWhereLike('tercero', $patron));
        }

        return $consulta;
    }
}
