<?php

namespace Aitrol\Contabilidad\Infrastructure;

use Aitrol\Contabilidad\Application\LibroContable;
use Aitrol\Contabilidad\Infrastructure\Persistence\DatosContables;
use App\Events\OperacionContabilizable;
use Illuminate\Validation\ValidationException;
use stdClass;

final readonly class ContabilizacionOperativa
{
    public function __construct(private DatosContables $datos, private LibroContable $libro) {}

    public function handle(OperacionContabilizable $evento): void
    {
        if ($evento->origen === 'activos.costo') {
            $this->datos->bloquearEmpresa();
            abort_if($this->asientoOrigen('activos.alta', $evento->id) !== null, 409, 'El costo se registró en contabilidad. Corrige el valor mediante el proceso contable.');
            return;
        }
        // Sin despacho diferido. El asiento y la operación comparten la transacción.
        $config = $this->datos->tabla('co_integraciones')->first();
        $esReversa = $evento->origen === 'comercial.anulacion' ||
            ($evento->origen === 'tesoreria.operacion' && $this->datos->fila('cb_operaciones', $evento->id)->id_reversa_de !== null);
        if (! $config || (! $config->activa && ! $esReversa)) return;

        $this->datos->bloquearEmpresa();
        $config = $this->datos->tabla('co_integraciones')->lockForUpdate()->first();
        if (! $config || (! $config->activa && ! $esReversa)) return;
        $reglas = json_decode($config->reglas, true, flags: JSON_THROW_ON_ERROR);
        $tesoreria = json_decode($config->tesoreria, true, flags: JSON_THROW_ON_ERROR);
        if ($this->asientoOrigen($evento->origen, $evento->id) !== null) return;

        match ($evento->origen) {
            'comercial.documento' => $this->comercial($evento, $config->fecha_inicio, $reglas),
            'comercial.anulacion' => $this->anularComercial($evento, $reglas),
            'inventario.movimiento' => $this->inventario($evento, $config->fecha_inicio, $reglas),
            'tesoreria.operacion' => $this->tesoreria($evento, $config->fecha_inicio, $reglas, $tesoreria),
            'activos.alta', 'activos.baja', 'activos.depreciacion' => $this->activos($evento, $config->fecha_inicio, $reglas),
        };
    }

    private function comercial(OperacionContabilizable $evento, string $inicio, array $reglas): void
    {
        $doc = $this->datos->fila('com_documentos', $evento->id);
        if ($doc->fecha < $inicio || ! in_array($doc->tipo, ['documentos', 'notas-credito', 'notas-debito', 'retenciones'], true)) return;
        if ($doc->id_documento_origen !== null && $this->asientoOrigen('comercial.documento', $doc->id_documento_origen) === null) {
            // Aplica contra el saldo de apertura de clientes/proveedores cuando el original es histórico.
            $origen = $this->datos->fila('com_documentos', $doc->id_documento_origen);
            abort_if($origen->fecha >= $inicio, 409, 'El documento de origen aún no tiene un asiento contable.');
        }
        $venta = $doc->modulo === 'ventas';
        $lineas = [];
        if ($doc->tipo === 'retenciones') {
            $cabecera = $venta ? ['retencion_ventas', 'clientes'] : ['proveedores', 'retencion_compras'];
            $this->par($lineas, $reglas, $cabecera[0], $cabecera[1], (int) $doc->total);
        } elseif ($venta) {
            $this->linea($lineas, $reglas, 'clientes', (int) $doc->total);
            $this->linea($lineas, $reglas, 'ventas', -(int) $doc->subtotal);
            $this->linea($lineas, $reglas, 'impuesto_ventas', -(int) $doc->impuesto);
            $costo = 0;
            foreach ($this->lineasComerciales($doc->id)->get() as $fila) {
                if ($fila->id_operacion !== null) $costo += $this->valorInventario($this->datos->fila('inv_operaciones', $fila->id_operacion));
            }
            $this->par($lineas, $reglas, 'costo_ventas', 'inventario', $costo);
        } else {
            $inventario = $doc->tipo === 'documentos'
                ? (int) $this->lineasComerciales($doc->id)->whereNotNull('id_operacion')->sum('subtotal') : 0;
            $this->linea($lineas, $reglas, 'inventario', $inventario);
            $this->linea($lineas, $reglas, 'compras', $doc->subtotal - $inventario);
            $this->linea($lineas, $reglas, 'impuesto_compras', (int) $doc->impuesto);
            $this->linea($lineas, $reglas, 'proveedores', -(int) $doc->total);
        }
        if ($doc->tipo === 'notas-credito') {
            $lineas = array_map(fn ($fila) => array_replace($fila, ['debe' => $fila['haber'], 'haber' => $fila['debe']]), $lineas);
        }
        $this->publicar($evento, $doc->fecha, ucfirst($doc->modulo).' · '.$doc->numero, $lineas);
    }

    private function anularComercial(OperacionContabilizable $evento, array $reglas): void
    {
        $doc = $this->datos->fila('com_documentos', $evento->id);
        $original = $this->asientoOrigen('comercial.documento', $evento->id);
        if ($original === null) return;
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
        if ($fecha < $inicio || $op->tipo === 'transferencia') return;
        $signo = in_array($op->tipo, ['entrada', 'ajuste_entrada'], true) ? 1 : -1;
        $lineas = [];
        $this->par($lineas, $reglas, 'inventario', 'ajustes_inventario', $signo * $this->valorInventario($op));
        $this->publicar($evento, $fecha, $op->motivo, $lineas);
    }

    private function tesoreria(OperacionContabilizable $evento, string $inicio, array $reglas, array $tesoreria): void
    {
        $op = $this->datos->fila('cb_operaciones', $evento->id);
        if ($op->tipo === 'reversa') {
            $original = $this->asientoOrigen('tesoreria.operacion', $op->id_reversa_de);
            if ($original !== null) $this->revertir($evento, $original, $op->fecha, $op->concepto);
            return;
        }
        if ($op->fecha < $inicio) return;
        abort_unless($op->moneda === 'USD', 422, 'La contabilidad automática usa USD. Esta operación requiere conversión contable.');
        $lineas = [];
        $traslado = in_array($op->tipo, ['transferencia', 'deposito'], true);
        foreach ($this->datos->tabla('cb_asientos')->where('id_operacion', $op->id)->get() as $fila) {
            $idCuenta = $tesoreria[$fila->id_cuenta] ?? null;
            if ($idCuenta === null) $this->falta('Configura la cuenta contable de la caja o banco '.$fila->id_cuenta.'.');
            $lineas[] = $this->monto((int) $idCuenta, (int) $fila->variacion);
            if (! $traslado) {
                $contra = match ($op->tipo) {
                    'debito' => 'comisiones', 'credito' => 'abonos', default => 'puente_tesoreria',
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
            if ($fecha < $inicio || ! $activo->costo) return;
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
            $fecha = min(now()->toDateString(), \Illuminate\Support\Carbon::parse($registro->periodo)->endOfMonth()->toDateString());
            if ($fecha < $inicio) return;
            $activo = $this->datos->fila('af_activos', $registro->id_activo);
            $this->par($lineas, $reglas, 'depreciacion', 'depreciacion_acumulada', (int) $registro->importe);
        } else {
            $registro = $this->datos->fila('af_movimientos', $evento->id);
            $fecha = $registro->fecha;
            if ($fecha < $inicio) return;
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

    private function lineasComerciales(int $id): \Illuminate\Database\Query\Builder
    {
        return $this->datos->tabla('com_lineas')->where('id_documento', $id);
    }

    private function valorInventario(stdClass $op): int
    {
        return intdiv($op->cantidad * $op->costo_unitario + 50000, 100000);
    }

    private function publicar(OperacionContabilizable $evento, string $fecha, string $concepto, array $lineas, ?string $flujo = null): void
    {
        if ($lineas === []) return;
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
        if ($importe === 0) return;
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
