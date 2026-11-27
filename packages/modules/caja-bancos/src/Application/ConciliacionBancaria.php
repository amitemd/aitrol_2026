<?php

namespace Aitrol\CajaBancos\Application;

use Aitrol\Inventarios\Domain\CantidadInventario as Decimal;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use stdClass;

final readonly class ConciliacionBancaria
{
    public function __construct(private DatosTesoreria $datos) {}

    public function importar(array $datos): stdClass
    {
        return $this->datos->transaccion(function () use ($datos): stdClass {
            $cuenta = $this->datos->fila('cb_cuentas', (int) $datos['id_cuenta'], true);
            abort_unless($cuenta->tipo === 'banco' && $cuenta->activo, 422, 'Selecciona una cuenta bancaria activa.');
            $extracto = $this->datos->crear('cb_extractos', [
                'id_cuenta' => $cuenta->id, 'nombre' => $datos['nombre'], 'importadas' => 0, 'omitidas' => 0,
            ]);
            $importadas = 0;
            $omitidas = 0;
            foreach ($datos['lineas'] as $i => $linea) {
                $referencia = Str::upper($linea['referencia']);
                $importe = Decimal::unidades($linea['importe'], 2);
                if ($importe === 0 || Str::length($referencia) > 120) {
                    throw ValidationException::withMessages(['lineas.'.$i => 'Indica un importe distinto de cero y una referencia de hasta 120 caracteres.']);
                }
                $existente = $this->datos->tabla('cb_lineas_extracto')->where('id_cuenta', $cuenta->id)->where('referencia', $referencia)->first();
                if ($existente !== null) {
                    abort_if($existente->fecha !== $linea['fecha'] || (int) $existente->importe !== $importe ||
                        $existente->descripcion !== $linea['descripcion'], 409, 'La referencia '.$referencia.' ya existe con otros datos.');
                    $omitidas++;
                    continue;
                }
                $this->datos->crear('cb_lineas_extracto', [
                    'id_extracto' => $extracto, 'id_cuenta' => $cuenta->id, 'fecha' => $linea['fecha'],
                    'referencia' => $referencia, 'descripcion' => $linea['descripcion'], 'importe' => $importe,
                ]);
                $importadas++;
            }
            $this->datos->actualizar('cb_extractos', $extracto, compact('importadas', 'omitidas'));

            return $this->datos->detalle('extractos', $extracto);
        });
    }

    public function candidatos(int $id, ?string $q): Builder
    {
        $linea = $this->datos->fila('cb_lineas_extracto', $id);
        $consulta = $this->datos->libro()->where('cb_asientos.id_cuenta', $linea->id_cuenta)
            ->where('cb_asientos.variacion', $linea->importe)->whereNull('extracto.id');
        if ($q !== null && $q !== '') {
            $consulta->where(fn (Builder $b) => $b->whereLike('op.concepto', '%'.$q.'%')->orWhereLike('op.referencia', '%'.$q.'%'));
        }

        return $consulta->orderByRaw('case when upper(op.referencia) = ? then 0 when op.fecha = ? then 1 else 2 end', [$linea->referencia, $linea->fecha])
            ->orderByDesc('op.fecha')->orderByDesc('cb_asientos.id');
    }

    public function conciliar(int $id, int $asiento): stdClass
    {
        $idCuenta = $this->datos->fila('cb_lineas_extracto', $id)->id_cuenta;

        return $this->datos->transaccion(function () use ($id, $idCuenta, $asiento): stdClass {
            $this->datos->fila('cb_cuentas', $idCuenta, true);
            $linea = $this->datos->fila('cb_lineas_extracto', $id, true);
            abort_if($linea->id_asiento !== null, 409, 'La línea del extracto ya está conciliada.');
            if (! $this->candidatos($id, null)->where('cb_asientos.id', $asiento)->exists()) {
                throw ValidationException::withMessages(['id_asiento' => 'Elige un movimiento pendiente de la misma cuenta y con el mismo importe y signo.']);
            }
            $this->datos->actualizar('cb_lineas_extracto', $id, ['id_asiento' => $asiento]);
            $this->datos->crear('cb_conciliaciones', ['id_linea_extracto' => $id, 'id_asiento' => $asiento, 'accion' => 'conciliar']);

            return $this->datos->detalle('conciliacion', $id);
        });
    }

    public function desconciliar(int $id, string $motivo): stdClass
    {
        $idCuenta = $this->datos->fila('cb_lineas_extracto', $id)->id_cuenta;

        return $this->datos->transaccion(function () use ($id, $idCuenta, $motivo): stdClass {
            $this->datos->fila('cb_cuentas', $idCuenta, true);
            $linea = $this->datos->fila('cb_lineas_extracto', $id, true);
            abort_if($linea->id_asiento === null, 409, 'La línea del extracto está pendiente.');
            $this->datos->actualizar('cb_lineas_extracto', $id, ['id_asiento' => null]);
            $this->datos->crear('cb_conciliaciones', [
                'id_linea_extracto' => $id, 'id_asiento' => $linea->id_asiento, 'accion' => 'desconciliar', 'motivo' => $motivo,
            ]);

            return $this->datos->detalle('conciliacion', $id);
        });
    }
}
