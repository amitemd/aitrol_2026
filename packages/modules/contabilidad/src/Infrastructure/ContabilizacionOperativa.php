<?php

namespace Aitrol\Contabilidad\Infrastructure;

use Aitrol\Contabilidad\Application\LibroContable;
use Aitrol\Contabilidad\Infrastructure\Persistence\DatosContables;
use Aitrol\Inventarios\Application\RepositorioInventario;
use App\Events\OperacionContabilizable;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use stdClass;

final readonly class ContabilizacionOperativa
{
    public function __construct(
        private DatosContables $datos,
        private LibroContable $libro,
        private RepositorioInventario $inventario,
    ) {}

    public function handle(OperacionContabilizable $evento): void
    {
        $this->procesar($evento, $evento->contabilizacionObligatoria);
    }

    public function contabilizarFactura(int $id): void
    {
        $this->procesar(new OperacionContabilizable('comercial.documento', $id), obligatorio: true);
    }

    private function procesar(OperacionContabilizable $evento, bool $obligatorio): void
    {
        if ($evento->origen === 'activos.costo') {
            $this->datos->bloquearEmpresa();
            abort_if($this->asientoOrigen('activos.alta', $evento->id) !== null, 409, 'El costo se registró en contabilidad. Corrige el valor mediante el proceso contable.');

            return;
        }
        // Sin despacho diferido. El asiento y la operación comparten la transacción.
        $config = $this->datos->tabla('co_integraciones')->first();
        $esReversa = $evento->origen === 'comercial.anulacion' ||
            $evento->origen === 'comercial.anulacion-recepcion' ||
            ($evento->origen === 'tesoreria.operacion' && $this->datos->fila('cb_operaciones', $evento->id)->id_reversa_de !== null);
        if (! $obligatorio && (! $config || (! $config->activa && ! $esReversa))) {
            return;
        }

        $this->datos->bloquearEmpresa();
        $config = $this->datos->tabla('co_integraciones')->lockForUpdate()->first();
        if (! $config || (! $config->activa && ! $esReversa)) {
            if ($obligatorio) {
                $this->falta('Activa la contabilización automática en Contabilidad antes de registrar esta operación.');
            }

            return;
        }
        $reglas = json_decode($config->reglas, true, flags: JSON_THROW_ON_ERROR);
        $tesoreria = json_decode($config->tesoreria, true, flags: JSON_THROW_ON_ERROR);
        if ($this->asientoOrigen($evento->origen, $evento->id) !== null) {
            return;
        }
        if ($obligatorio && $evento->origen === 'comercial.documento') {
            $factura = $this->datos->fila('com_documentos', $evento->id);
            if (blank($config->fecha_inicio) || $factura->fecha < $config->fecha_inicio) {
                $this->falta('La contabilización automática debe estar vigente desde la fecha de la factura.');
            }
            $periodo = $this->datos->periodoEnFecha($factura->fecha);
            if ($periodo === null || $periodo->cerrado_at !== null) {
                $this->falta('Abre un período contable que incluya la fecha del documento.');
            }
        }

        match ($evento->origen) {
            'comercial.documento' => $this->comercial($evento, $config->fecha_inicio, $reglas),
            'comercial.anulacion' => $this->anularComercial($evento, $reglas),
            'comercial.recepcion' => $this->recepcionCompra($evento, $config->fecha_inicio, $reglas),
            'comercial.anulacion-recepcion' => $this->anularRecepcionCompra($evento, $reglas),
            'inventario.movimiento' => $this->inventario($evento, $config->fecha_inicio, $reglas),
            'tesoreria.operacion' => $this->tesoreria($evento, $config->fecha_inicio, $reglas, $tesoreria),
            'activos.alta', 'activos.baja', 'activos.depreciacion' => $this->activos($evento, $config->fecha_inicio, $reglas),
        };
        if ($obligatorio && $this->asientoOrigen($evento->origen, $evento->id) === null) {
            $this->falta('La operación no tiene importes para generar un asiento contable.');
        }
    }

    private function comercial(OperacionContabilizable $evento, string $inicio, array $reglas): void
    {
        $doc = $this->datos->fila('com_documentos', $evento->id);
        if ($doc->fecha < $inicio || ! in_array($doc->tipo, ['documentos', 'notas-credito', 'notas-debito', 'retenciones'], true)) {
            return;
        }
        if ($doc->tipo === 'retenciones' && (int) $doc->total === 0) {
            return;
        }
        if ($doc->modulo === 'compras' && $doc->tipo === 'notas-credito'
            && $this->lineasComerciales($doc->id)->whereNotNull('id_linea_recepcion')->exists()) {
            $this->devolucionCompra($evento, $doc, $reglas);

            return;
        }
        if ($evento->contabilizacionObligatoria && $doc->tipo === 'notas-credito') {
            $idAsiento = $this->asientoOrigen('comercial.documento', $doc->id_documento_origen);
            if ($idAsiento === null) {
                $this->falta('Genera el asiento de la factura antes de registrar la nota de crédito.');
            }
            $original = $this->datos->asiento($idAsiento);
            $lineas = $original->lineas->map(fn (stdClass $fila): array => [
                'id_cuenta' => $fila->id_cuenta, 'debe' => $fila->haber, 'haber' => $fila->debe,
                'detalle' => $fila->detalle, 'id_centro_costo' => $fila->id_centro_costo,
                'movimiento_patrimonio' => $fila->movimiento_patrimonio,
            ])->all();
            $this->publicar($evento, $doc->fecha, 'Nota de crédito · '.$doc->numero, $lineas, $original->flujo);

            return;
        }
        $venta = $doc->modulo === 'ventas';
        if (! $venta && $doc->tipo === 'documentos' && $this->facturaCotejada($evento, $doc, $reglas)) {
            return;
        }
        if ($doc->id_documento_origen !== null && $this->asientoOrigen('comercial.documento', $doc->id_documento_origen) === null) {
            // Aplica contra el saldo de apertura de clientes/proveedores cuando el original es histórico.
            $origen = $this->datos->fila('com_documentos', $doc->id_documento_origen);
            abort_if($origen->fecha >= $inicio, 409, 'El documento de origen aún no tiene un asiento contable.');
        }
        $lineas = [];
        if ($doc->tipo === 'retenciones') {
            $cabecera = $venta ? ['retencion_ventas', 'clientes'] : ['proveedores', 'retencion_compras'];
            $this->par($lineas, $reglas, $cabecera[0], $cabecera[1], (int) $doc->total);
        } elseif ($venta) {
            $this->linea($lineas, $reglas, 'clientes', (int) $doc->total);
            $this->linea($lineas, $reglas, 'ventas', -(int) $doc->subtotal);
            $this->linea($lineas, $reglas, 'impuesto_ventas', -(int) $doc->impuesto);
            $this->linea($lineas, $reglas, 'propinas_por_pagar', -(int) $doc->propina);
            $porProducto = $this->lineasComerciales($doc->id)->whereNotNull('id_operacion')->get()->groupBy('id_producto');
            foreach ($porProducto as $idProducto => $movimientos) {
                $costo = $movimientos->sum(fn (stdClass $fila): int => $this->valorInventario($this->datos->fila('inv_operaciones', $fila->id_operacion)));
                $this->par($lineas, $this->cuentasInventario((int) $idProducto, $reglas), 'costo_ventas', 'inventario', $costo);
            }
        } else {
            $inventario = $doc->tipo === 'documentos'
                ? (int) $this->lineasComerciales($doc->id)->whereNotNull('id_operacion')->sum('subtotal') : 0;
            $this->linea($lineas, $reglas, 'inventario', $inventario);
            $gastoGeneral = (int) $doc->subtotal - $inventario;
            if ($doc->tipo === 'documentos') {
                $porProducto = $this->lineasComerciales($doc->id)
                    ->whereNull('id_operacion')->whereNotNull('id_producto')
                    ->select('id_producto')->selectRaw('SUM(subtotal) as base')
                    ->groupBy('id_producto')->orderBy('id_producto')->get();
                foreach ($porProducto as $fila) {
                    $producto = $this->inventario->catalogo('productos', (int) $fila->id_producto);
                    if ($producto->tipo_producto !== 'no_inventariable' || (int) $fila->base === 0) {
                        continue;
                    }
                    $cuentas = (array) $this->inventario->cuentasProducto($producto)['cuentas_efectivas'];
                    if (! empty($cuentas['compras'])) {
                        $lineas[] = $this->monto((int) $cuentas['compras'], (int) $fila->base);
                        $gastoGeneral -= (int) $fila->base;
                    }
                }
            }
            $this->linea($lineas, $reglas, 'compras', $gastoGeneral);
            $this->linea($lineas, $reglas, 'impuesto_compras', (int) $doc->impuesto);
            $this->linea($lineas, $reglas, 'proveedores', -(int) $doc->total);
        }
        if ($doc->tipo === 'notas-credito') {
            $lineas = array_map(fn ($fila) => array_replace($fila, ['debe' => $fila['haber'], 'haber' => $fila['debe']]), $lineas);
        }
        $this->publicar($evento, $doc->fecha, ucfirst($doc->modulo).' · '.$doc->numero, $lineas);
    }

    private function recepcionCompra(OperacionContabilizable $evento, ?string $inicio, array $reglas): void
    {
        $recepcion = $this->datos->fila('com_documentos', $evento->id);
        if ($inicio === null || $recepcion->fecha < $inicio) {
            $this->falta('La contabilización automática debe estar vigente desde la fecha de recepción.');
        }
        if (! empty($reglas['recepciones_por_facturar']) && (int) $reglas['recepciones_por_facturar'] === (int) ($reglas['proveedores'] ?? 0)) {
            $this->falta('Configura una cuenta de mercadería recibida por facturar distinta de cuentas por pagar a proveedores.');
        }
        $lineas = [];
        $this->par($lineas, $reglas, 'inventario', 'recepciones_por_facturar', (int) $recepcion->subtotal);
        $this->publicar($evento, $recepcion->fecha, 'Recepción de compra · '.$recepcion->numero, $lineas);
    }

    private function facturaCotejada(OperacionContabilizable $evento, stdClass $factura, array $reglas): bool
    {
        $recepciones = $this->datos->tabla('com_facturas_recepciones')->where('id_factura', $factura->id)
            ->orderBy('id_recepcion')->pluck('id_recepcion');
        if ($recepciones->isEmpty()) {
            return false;
        }

        $transitoria = $this->datos->tabla('co_asientos')
            ->join('co_lineas as partida', fn ($join) => $join->on('partida.id_asiento', '=', 'co_asientos.id')
                ->on('partida.id_empresa', '=', 'co_asientos.id_empresa'))
            ->where('co_asientos.origen', 'comercial.recepcion')->whereIn('co_asientos.id_origen', $recepciones)
            ->where('partida.haber', '>', 0)->get(['co_asientos.id_origen', 'partida.id_cuenta', 'partida.haber'])->keyBy('id_origen');
        $aplicadas = $this->datos->tabla('com_aplicaciones_recepcion')
            ->join('com_lineas as recibida', fn ($join) => $join->on('recibida.id', '=', 'com_aplicaciones_recepcion.id_linea_recepcion')
                ->on('recibida.id_empresa', '=', 'com_aplicaciones_recepcion.id_empresa'))
            ->where('com_aplicaciones_recepcion.id_factura', $factura->id)->groupBy('recibida.id_documento')
            ->select('recibida.id_documento')->selectRaw('SUM(com_aplicaciones_recepcion.subtotal) AS base')
            ->pluck('base', 'recibida.id_documento');
        $cotejo = json_decode($factura->datos_factura ?? '{}', true, flags: JSON_THROW_ON_ERROR)['cotejo'] ?? null;
        $lineas = [];
        foreach ($recepciones as $idRecepcion) {
            $partida = $transitoria->get($idRecepcion);
            if ($partida === null) {
                $this->falta('La recepción debe estar contabilizada antes de registrar su factura.');
            }
            if ($cotejo !== null && ! $aplicadas->has($idRecepcion)) {
                $this->falta('La factura no indica qué importe aplica a la recepción seleccionada.');
            }
            $importe = $cotejo === null ? (int) $partida->haber : (int) $aplicadas->get($idRecepcion);
            if ($importe > 0) {
                $lineas[] = $this->monto($partida->id_cuenta, $importe);
            }
        }
        $this->linea($lineas, $reglas, 'compras', (int) ($cotejo['subtotal_servicios'] ?? 0));
        $this->linea($lineas, $reglas, 'diferencias_precio_compras', (int) ($cotejo['diferencia_mercaderia'] ?? 0));
        $this->linea($lineas, $reglas, 'impuesto_compras', (int) $factura->impuesto);
        $this->linea($lineas, $reglas, 'proveedores', -(int) $factura->total);
        $this->publicar($evento, $factura->fecha, 'Compra cotejada · '.$factura->numero, $lineas);

        return true;
    }

    private function anularRecepcionCompra(OperacionContabilizable $evento, array $reglas): void
    {
        $recepcion = $this->datos->fila('com_documentos', $evento->id);
        $original = $this->asientoOrigen('comercial.recepcion', $recepcion->id);
        if ($original === null) {
            $this->falta('No se encontró el asiento de la recepción.');
        }
        $fecha = now('America/Guayaquil')->toDateString();
        $this->revertir($evento, $original, $fecha, $recepcion->motivo_anulacion);
        $ids = $this->lineasComerciales($recepcion->id)->whereNotNull('id_operacion_reversa')->pluck('id_operacion_reversa');
        $costoRetirado = $this->datos->tabla('inv_operaciones')->whereIn('id', $ids)->get(['cantidad', 'costo_unitario'])
            ->sum(fn (stdClass $movimiento): int => $this->valorInventario($movimiento));
        $diferencia = $costoRetirado - (int) $recepcion->subtotal;
        if ($diferencia !== 0) {
            $inventario = $this->datos->asiento($original)->lineas->firstWhere('debe', '>', 0);
            $lineas = [$this->monto($inventario->id_cuenta, -$diferencia)];
            $this->linea($lineas, $reglas, 'ajustes_inventario', $diferencia);
            $this->publicar(new OperacionContabilizable('comercial.ajuste-recepcion', $recepcion->id), $fecha,
                'Diferencia de costo en reversión de recepción', $lineas);
        }
    }

    private function devolucionCompra(OperacionContabilizable $evento, stdClass $nota, array $reglas): void
    {
        $factura = $this->asientoOrigen('comercial.documento', $nota->id_documento_origen);
        if ($factura === null) {
            $this->falta('Contabiliza la factura antes de registrar su devolución.');
        }
        $documento = $this->datos->fila('com_documentos', $nota->id_documento_origen);
        $partidasFactura = $this->datos->asiento($factura)->lineas;
        $proveedores = $partidasFactura->last(fn (stdClass $fila): bool => (int) $fila->haber === (int) $documento->total && $fila->clase === 'pasivo');
        if ($proveedores === null) {
            $this->falta('No se encontró la cuenta por pagar del asiento original.');
        }
        $movimientos = $this->lineasComerciales($nota->id)->whereNotNull('com_lineas.id_linea_recepcion')
            ->join('com_lineas as recibida', fn ($join) => $join->on('recibida.id', '=', 'com_lineas.id_linea_recepcion')->on('recibida.id_empresa', '=', 'com_lineas.id_empresa'))
            ->join('inv_operaciones as salida', fn ($join) => $join->on('salida.id', '=', 'com_lineas.id_operacion')->on('salida.id_empresa', '=', 'com_lineas.id_empresa'))
            ->get(['recibida.id_documento as id_recepcion', 'salida.cantidad', 'salida.costo_unitario'])->groupBy('id_recepcion');
        $lineas = [$this->monto($proveedores->id_cuenta, (int) $nota->total)];
        $costoTotal = 0;
        foreach ($movimientos as $idRecepcion => $partidas) {
            $origen = $this->asientoOrigen('comercial.recepcion', $idRecepcion);
            if ($origen === null) {
                $this->falta('No se encontró el asiento de la recepción.');
            }
            $inventario = $this->datos->asiento($origen)->lineas->firstWhere('debe', '>', 0);
            $costo = (int) $partidas->sum(fn (stdClass $fila): int => $this->valorInventario($fila));
            if ($costo !== 0) {
                $lineas[] = $this->monto($inventario->id_cuenta, -$costo);
            }
            $costoTotal += $costo;
        }
        if ((int) $nota->impuesto > 0) {
            $iva = $partidasFactura->last(fn (stdClass $fila): bool => (int) $fila->debe === (int) $documento->impuesto && $fila->clase === 'activo');
            if ($iva === null) {
                $this->falta('No se encontró el IVA de compras del asiento original.');
            }
            $lineas[] = $this->monto($iva->id_cuenta, -(int) $nota->impuesto);
        }
        $this->linea($lineas, $reglas, 'diferencias_precio_compras', $costoTotal - (int) $nota->subtotal);
        $this->publicar($evento, $nota->fecha, 'Devolución de compra · '.$nota->numero, $lineas);
    }

    private function anularComercial(OperacionContabilizable $evento, array $reglas): void
    {
        $doc = $this->datos->fila('com_documentos', $evento->id);
        $original = $this->asientoOrigen('comercial.documento', $evento->id);
        if ($original === null) {
            return;
        }
        if ($doc->modulo === 'compras' && $doc->tipo === 'documentos') {
            $vinculados = $this->datos->tabla('co_asientos')->where('origen', 'activos.alta')
                ->whereIn('id_origen', $this->datos->tabla('af_activos')->where('id_compra', $doc->id)->select('af_activos.id'))->exists();
            abort_if($vinculados, 409, 'La compra tiene activos capitalizados y requiere resolver la reclasificación antes de anularla.');
        }
        $fecha = substr($doc->anulado_at, 0, 10);
        $this->revertir($evento, $original, $fecha, $doc->motivo_anulacion);
        if ($doc->modulo === 'compras' && $doc->tipo === 'documentos') {
            $diferencia = 0;
            foreach ($this->lineasComerciales($doc->id)->whereNotNull('id_operacion_reversa')->get() as $fila) {
                $diferencia += $this->valorInventario($this->datos->fila('inv_operaciones', $fila->id_operacion_reversa)) - $fila->subtotal;
            }
            $lineas = [];
            $this->par($lineas, $reglas, 'ajustes_inventario', 'inventario', $diferencia);
            $this->publicar(new OperacionContabilizable('comercial.ajuste-anulacion', $doc->id), $fecha, 'Diferencia de costo en anulación '.$doc->numero, $lineas);
        }
    }

    private function inventario(OperacionContabilizable $evento, string $inicio, array $reglas): void
    {
        $op = $this->datos->fila('inv_operaciones', $evento->id);
        $fecha = substr($op->created_at, 0, 10);
        $valor = $this->valorInventario($op);
        if ($evento->contabilizacionObligatoria) {
            $fecha = Carbon::parse($op->created_at, config('app.timezone'))->setTimezone('America/Guayaquil')->toDateString();
            if ($fecha < $inicio) {
                $this->falta('La contabilización automática debe estar vigente en la fecha del ajuste.');
            }
            $periodo = $this->datos->periodoEnFecha($fecha);
            if ($periodo === null || $periodo->cerrado_at !== null) {
                $this->falta('Abre un período contable que incluya la fecha del ajuste.');
            }
            if ($valor === 0) {
                $this->falta('El ajuste debe tener un valor de al menos USD 0,01. Revisa el costo unitario o el costo promedio de la bodega.');
            }
            $reglas = $this->cuentasInventario((int) $op->id_producto, $reglas);
        }
        if ($fecha < $inicio || $op->tipo === 'transferencia') {
            return;
        }
        $signo = in_array($op->tipo, ['entrada', 'ajuste_entrada'], true) ? 1 : -1;
        $lineas = [];
        $this->par($lineas, $reglas, 'inventario', 'ajustes_inventario', $signo * $valor);
        $this->publicar($evento, $fecha, $op->motivo, $lineas);
    }

    private function cuentasInventario(int $idProducto, array $reglas): array
    {
        $producto = $this->inventario->catalogo('productos', $idProducto);
        $cuentas = (array) $this->inventario->cuentasProducto($producto)['cuentas_efectivas'];

        return array_filter(array_intersect_key($cuentas, array_flip(['inventario', 'costo_ventas', 'ajustes_inventario']))) + $reglas;
    }

    private function tesoreria(OperacionContabilizable $evento, string $inicio, array $reglas, array $tesoreria): void
    {
        $op = $this->datos->fila('cb_operaciones', $evento->id);
        if ($op->tipo === 'reversa') {
            $original = $this->asientoOrigen('tesoreria.operacion', $op->id_reversa_de);
            if ($original !== null) {
                $this->revertir($evento, $original, $op->fecha, $op->concepto);
            }

            return;
        }
        if ($op->fecha < $inicio) {
            return;
        }
        abort_unless($op->moneda === 'USD', 422, 'La contabilidad automática usa USD. Esta operación requiere conversión contable.');
        if ($op->id_factura_venta !== null) {
            $idAsiento = $this->asientoOrigen('comercial.documento', $op->id_factura_venta);
            if ($idAsiento === null || ! $this->datos->asiento($idAsiento)->lineas->contains(
                fn (stdClass $partida) => (int) $partida->id_cuenta === (int) ($reglas['clientes'] ?? 0) && $partida->debe > 0,
            )) {
                $this->falta('La regla de clientes debe coincidir con la cuenta por cobrar del asiento de la factura.');
            }
        }
        if ($op->id_factura_compra !== null) {
            $idAsiento = $this->asientoOrigen('comercial.documento', $op->id_factura_compra);
            if ($idAsiento === null || ! $this->datos->asiento($idAsiento)->lineas->contains(
                fn (stdClass $partida) => (int) $partida->id_cuenta === (int) ($reglas['proveedores'] ?? 0) && $partida->haber > 0,
            )) {
                $this->falta('La regla de proveedores debe coincidir con la cuenta por pagar del asiento de la factura.');
            }
        }
        $lineas = [];
        $traslado = in_array($op->tipo, ['transferencia', 'deposito'], true);
        foreach ($this->datos->tabla('cb_asientos')->where('id_operacion', $op->id)->get() as $fila) {
            $idCuenta = $tesoreria[$fila->id_cuenta] ?? null;
            if ($idCuenta === null) {
                $this->falta('Configura la cuenta contable de la caja o banco '.$fila->id_cuenta.'.');
            }
            $lineas[] = $this->monto((int) $idCuenta, (int) $fila->variacion);
            if (! $traslado) {
                $contra = match (true) {
                    $op->id_factura_venta !== null => 'clientes',
                    $op->id_factura_compra !== null => 'proveedores',
                    $op->tipo === 'debito' => 'comisiones',
                    $op->tipo === 'credito' => 'abonos',
                    default => 'puente_tesoreria',
                };
                $this->linea($lineas, $reglas, $contra, -(int) $fila->variacion);
            }
        }
        $this->publicar($evento, $op->fecha, $op->concepto, $lineas, $traslado ? 'traslado' : ($op->flujo_contable ?? 'operacion'));
    }

    private function activos(OperacionContabilizable $evento, string $inicio, array $reglas): void
    {
        $lineas = [];
        if ($evento->origen === 'activos.alta') {
            $activo = $this->datos->fila('af_activos', $evento->id);
            $fecha = $activo->fecha_adquisicion;
            if ($fecha < $inicio || ! $activo->costo) {
                return;
            }
            if ($activo->id_compra !== null) {
                $compra = $this->datos->fila('com_documentos', $activo->id_compra);
                abort_if($this->asientoOrigen('comercial.documento', $compra->id) === null, 409, 'Contabiliza primero la compra vinculada al activo.');
                abort_if($fecha < $compra->fecha, 422, 'La fecha de alta no puede ser anterior a la compra vinculada.');
                $base = $this->lineasComerciales($compra->id)->whereNull('id_operacion')->sum('subtotal');
                $asignado = $this->datos->tabla('af_activos')->where('id_compra', $compra->id)->sum('costo');
                abort_if($asignado > $base, 422, 'El costo capitalizado supera el subtotal de compra que no ingresó a inventario.');
            }
            $this->par($lineas, $reglas, 'activo_fijo', $activo->id_compra !== null ? 'compras' : 'pasivo_activo', (int) $activo->costo);
        } elseif ($evento->origen === 'activos.depreciacion') {
            $registro = $this->datos->fila('af_depreciaciones', $evento->id);
            $fecha = min(now()->toDateString(), Carbon::parse($registro->periodo)->endOfMonth()->toDateString());
            if ($fecha < $inicio) {
                return;
            }
            $activo = $this->datos->fila('af_activos', $registro->id_activo);
            $this->par($lineas, $reglas, 'depreciacion', 'depreciacion_acumulada', (int) $registro->importe);
        } else {
            $registro = $this->datos->fila('af_movimientos', $evento->id);
            $fecha = $registro->fecha;
            if ($fecha < $inicio) {
                return;
            }
            $activo = $this->datos->fila('af_activos', $registro->id_activo);
            $this->linea($lineas, $reglas, 'depreciacion_acumulada', (int) $activo->depreciado);
            $this->linea($lineas, $reglas, 'baja_activo', $activo->costo - $activo->depreciado);
            $this->linea($lineas, $reglas, 'activo_fijo', -(int) $activo->costo);
        }
        $this->publicar($evento, $fecha, ucfirst(str_replace('activos.', '', $evento->origen)).' · '.$activo->codigo, $lineas);
    }

    private function asientoOrigen(string $origen, int $id): ?int
    {
        $asiento = $this->datos->tabla('co_asientos')->where('origen', $origen)->where('id_origen', $id)->value('id');

        return $asiento === null ? null : (int) $asiento;
    }

    private function lineasComerciales(int $id): Builder
    {
        return $this->datos->tabla('com_lineas')->where('com_lineas.id_documento', $id);
    }

    private function valorInventario(stdClass $op): int
    {
        return intdiv($op->cantidad * $op->costo_unitario + 50000, 100000);
    }

    private function publicar(OperacionContabilizable $evento, string $fecha, string $concepto, array $lineas, ?string $flujo = null): void
    {
        if ($lineas === []) {
            return;
        }
        $this->libro->registrarAutomatico($evento->origen, $evento->id, [
            'fecha' => $fecha, 'concepto' => mb_substr($concepto, 0, 250), 'referencia' => $evento->origen.'/'.$evento->id, 'flujo' => $flujo,
        ], $lineas);
    }

    private function revertir(OperacionContabilizable $evento, int $original, string $fecha, string $motivo): void
    {
        $this->libro->revertir($original, ['fecha' => $fecha, 'motivo' => $motivo], ['origen' => $evento->origen, 'id_origen' => $evento->id]);
    }

    private function par(array &$lineas, array $reglas, string $debe, string $haber, int $importe): void
    {
        $this->linea($lineas, $reglas, $debe, $importe);
        $this->linea($lineas, $reglas, $haber, -$importe);
    }

    private function linea(array &$lineas, array $reglas, string $llave, int $importe): void
    {
        if ($importe === 0) {
            return;
        }
        if (! isset($reglas[$llave])) {
            $this->falta('Configura la regla contable «'.config('contabilidad.reglas.'.$llave.'.0').'».');
        }
        $lineas[] = $this->monto((int) $reglas[$llave], $importe);
    }

    private function monto(int $id, int $importe): array
    {
        return ['id_cuenta' => $id, 'debe' => max($importe, 0), 'haber' => max(-$importe, 0), 'detalle' => null, 'movimiento_patrimonio' => null];
    }

    private function falta(string $mensaje): never
    {
        throw ValidationException::withMessages(['contabilidad' => $mensaje]);
    }
}
