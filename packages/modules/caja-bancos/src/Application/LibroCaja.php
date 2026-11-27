<?php

namespace Aitrol\CajaBancos\Application;

use App\Events\OperacionContabilizable;
use Aitrol\Inventarios\Domain\CantidadInventario as Decimal;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Validation\ValidationException;
use stdClass;

final readonly class LibroCaja
{
    public function __construct(private DatosTesoreria $datos) {}

    public function cuenta(array $campos, ?int $id): stdClass
    {
        try {
            return $this->datos->transaccion(function () use ($campos, $id): stdClass {
                if ($id === null) {
                    $id = $this->datos->crear('cb_cuentas', $campos);
                } else {
                    $cuenta = $this->datos->fila('cb_cuentas', $id, true);
                    abort_if(! $campos['activo'] && ($cuenta->saldo > 0 || $this->cajaAbierta($id)), 409, 'Para desactivar la cuenta, deja el saldo en cero y cierra la caja.');
                    $this->datos->actualizar('cb_cuentas', $id, $campos);
                }

                return $this->datos->fila('cb_cuentas', $id);
            });
        } catch (UniqueConstraintViolationException) {
            abort(409, 'El código de cuenta ya existe en tu empresa.');
        }
    }

    public function abrir(int $idCuenta): stdClass
    {
        return $this->datos->transaccion(function () use ($idCuenta): stdClass {
            $cuenta = $this->datos->fila('cb_cuentas', $idCuenta, true);
            abort_unless($cuenta->tipo === 'caja' && $cuenta->activo, 422, 'Selecciona una caja activa.');
            abort_if($this->cajaAbierta($idCuenta), 409, 'Esta caja ya tiene una apertura vigente.');
            $id = $this->datos->crear('cb_cierres', ['id_cuenta' => $idCuenta, 'saldo_apertura' => $cuenta->saldo]);

            return $this->datos->detalle('cierres', $id);
        });
    }

    public function cerrar(int $id, array $campos): stdClass
    {
        $idCuenta = $this->datos->fila('cb_cierres', $id)->id_cuenta;

        return $this->datos->transaccion(function () use ($id, $idCuenta, $campos): stdClass {
            $cuenta = $this->datos->fila('cb_cuentas', $idCuenta, true);
            $cierre = $this->datos->fila('cb_cierres', $id, true);
            abort_if($cierre->cerrado_at !== null, 409, 'La caja ya está cerrada.');
            $contado = Decimal::unidades($campos['efectivo_contado'], 2);
            $diferencia = $contado - $cuenta->saldo;
            $observacion = $campos['observacion'] ?? null;
            if ($diferencia !== 0 && $observacion === null) {
                throw ValidationException::withMessages(['observacion' => 'Explica la diferencia entre el efectivo contado y el saldo del sistema.']);
            }
            $this->datos->actualizar('cb_cierres', $id, [
                'saldo_cierre' => $cuenta->saldo, 'efectivo_contado' => $contado, 'diferencia' => $diferencia,
                'observacion' => $observacion, 'cerrado_at' => now(),
            ]);

            return $this->datos->detalle('cierres', $id);
        });
    }

    public function registrar(string $tipo, array $campos): array
    {
        $importe = Decimal::unidades($campos['importe'], 2);
        $origen = (int) $campos['id_cuenta'];
        $esEntrada = in_array($tipo, ['ingreso', 'credito'], true);
        $asientos = [$origen => $esEntrada ? $importe : -$importe];
        if (isset($campos['id_destino'])) {
            $destino = (int) $campos['id_destino'];
            if ($destino === $origen) {
                throw ValidationException::withMessages(['id_destino' => 'Selecciona una cuenta distinta del origen.']);
            }
            $asientos[$destino] = $importe;
        }
        $comprobante = [
            'tipo' => $tipo, 'fecha' => $campos['fecha'], 'importe' => $importe, 'concepto' => $campos['concepto'],
            'referencia' => $campos['referencia'] ?? null,
        ];
        if (isset($campos['flujo_contable'])) $comprobante['flujo_contable'] = $campos['flujo_contable'];
        $clave = isset($campos['clave_operacion']) ? strtolower($campos['clave_operacion']) : null;
        $huella = hash('sha256', json_encode([$comprobante, $asientos], JSON_THROW_ON_ERROR));

        try {
            return $this->datos->transaccion(function () use ($comprobante, $origen, $clave, $huella, $asientos): array {
                $cuentas = $this->bloquearCuentas($asientos);
                if ($clave !== null && ($repetida = $this->repetida($clave, $huella)) !== null) return [$repetida, true];
                if ($comprobante['tipo'] === 'deposito') {
                    $destino = array_key_first(array_diff_key($asientos, [$origen => true]));
                    abort_unless($cuentas[$origen]->tipo === 'caja' && $cuentas[$destino]->tipo === 'banco', 422,
                        'El depósito debe salir de una caja e ingresar a una cuenta bancaria.');
                }
                if (in_array($comprobante['tipo'], ['debito', 'credito'], true)) {
                    abort_unless($cuentas[$origen]->tipo === 'banco', 422, 'Las notas bancarias requieren una cuenta bancaria.');
                }
                $operacion = $this->aplicar($comprobante + ['clave_operacion' => $clave, 'huella' => $huella], $cuentas, $asientos);

                return [$operacion, false];
            });
        } catch (UniqueConstraintViolationException $error) {
            $repetida = $clave === null ? null : $this->repetida($clave, $huella);
            if ($repetida === null) throw $error;

            return [$repetida, true];
        }
    }

    public function revertir(string $seccion, int $id, string $motivo): stdClass
    {
        $original = $this->datos->detalle($seccion, $id);
        abort_if($original->tipo === 'reversa', 409, 'Las reversiones quedan en el historial.');
        $asientos = $original->asientos->mapWithKeys(fn (stdClass $fila) => [$fila->id_cuenta => -$fila->variacion])->all();

        return $this->datos->transaccion(function () use ($id, $motivo, $asientos, $original): stdClass {
            $cuentas = $this->bloquearCuentas($asientos);
            $op = $this->datos->operacion($id);
            abort_if($op->id_reversion !== null, 409, 'La operación ya fue revertida.');
            abort_if($op->asientos->contains(fn ($fila) => $fila->linea_conciliada !== null), 409, 'Se debe desconciliar la operación antes de revertirla.');

            return $this->aplicar([
                'tipo' => 'reversa', 'fecha' => now()->toDateString(), 'importe' => $original->importe, 'concepto' => $motivo,
                'referencia' => 'Operación '.$id, 'id_reversa_de' => $id,
            ], $cuentas, $asientos);
        });
    }

    private function aplicar(array $cabecera, array $cuentas, array $asientos): stdClass
    {
        $moneda = reset($cuentas)->moneda;
        $filas = [];
        foreach ($cuentas as $id => $cuenta) {
            abort_unless($cuenta->activo, 422, 'Todas las cuentas de la operación deben estar activas.');
            abort_unless($cuenta->moneda === $moneda, 422, 'El origen y el destino deben usar la misma moneda.');
            $saldo = $cuenta->saldo + $asientos[$id];
            if ($saldo < 0 || $saldo > 100_000_000_000) {
                $mensaje = $saldo < 0 ? ' tiene saldo insuficiente.' : ' superaría el saldo máximo admitido.';
                throw ValidationException::withMessages(['importe' => 'La cuenta '.$cuenta->codigo.$mensaje]);
            }
            $ultimo = $this->datos->libro()->where('cb_asientos.id_cuenta', $id)->orderByDesc('cb_asientos.id')->select('op.fecha')->value('op.fecha');
            if ($ultimo !== null && $cabecera['fecha'] < $ultimo) {
                throw ValidationException::withMessages(['fecha' => 'La fecha debe ser igual o posterior al último movimiento de '.$cuenta->codigo.'.']);
            }
            $cierre = $cuenta->tipo === 'caja' ? $this->cajaAbierta($id) : null;
            abort_if($cuenta->tipo === 'caja' && $cierre === null, 409, 'La caja '.$cuenta->codigo.' debe estar abierta.');
            abort_if($cierre !== null && $cabecera['fecha'] < substr($cierre->created_at, 0, 10), 422, 'La fecha no puede ser anterior a la apertura de caja.');
            $filas[] = ['id_cuenta' => $id, 'id_cierre' => $cierre?->id, 'variacion' => $asientos[$id], 'saldo_anterior' => $cuenta->saldo, 'saldo' => $saldo];
        }
        $op = $this->datos->crear('cb_operaciones', $cabecera + ['moneda' => $moneda]);
        foreach ($filas as $fila) {
            $this->datos->crear('cb_asientos', $fila + ['id_operacion' => $op], false);
            $this->datos->actualizar('cb_cuentas', $fila['id_cuenta'], ['saldo' => $fila['saldo']]);
        }
        event(new OperacionContabilizable('tesoreria.operacion', $op));

        return $this->datos->operacion($op);
    }

    private function bloquearCuentas(array $asientos): array
    {
        ksort($asientos, SORT_NUMERIC);
        $cuentas = [];
        foreach (array_keys($asientos) as $id) {
            $cuentas[$id] = $this->datos->fila('cb_cuentas', $id, true);
        }

        return $cuentas;
    }

    private function cajaAbierta(int $id): ?stdClass
    {
        return $this->datos->tabla('cb_cierres')->where('id_cuenta', $id)->whereNull('cerrado_at')->first();
    }

    private function repetida(string $clave, string $huella): ?stdClass
    {
        $op = $this->datos->tabla('cb_operaciones')->where('clave_operacion', $clave)->first();
        if ($op === null) return null;

        abort_unless($op->huella === $huella, 409, 'La clave ya se utilizó con otros datos.');

        return $this->datos->operacion($op->id);
    }
}
