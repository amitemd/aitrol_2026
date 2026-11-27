<?php

namespace Aitrol\Rrhh\Application;

use Carbon\CarbonImmutable;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use stdClass;

final readonly class Nomina
{
    public function __construct(private DatosRrhh $datos) {}

    public function abrir(string $mes): int
    {
        return $this->datos->transaccion(function () use ($mes): int {
            $periodo = $mes.'-01';
            if ($this->datos->tabla('rh_nominas')->where('periodo', $periodo)->exists()) {
                throw ValidationException::withMessages(['periodo' => 'Ya existe una nómina para ese mes y sucursal.']);
            }

            return $this->datos->insertar('rh_nominas', ['periodo' => $periodo]);
        });
    }

    public function cambiarEstado(int $id, string $accion, ?string $referencia): void
    {
        $this->datos->transaccion(function () use ($id, $accion, $referencia): void {
            $nomina = $this->datos->registro($this->datos->tabla('rh_nominas'), $id);
            if ($accion === 'cerrar') {
                abort_unless($nomina->estado === 'borrador', 409, 'Esta nómina ya fue cerrada.');
                $this->cerrar($nomina);

                return;
            }

            abort_unless($nomina->estado === 'cerrada', 409, 'Solo se puede confirmar el pago de una nómina cerrada.');
            $this->datos->tabla('rh_nominas')->where('id', $id)->update([
                'estado' => 'pagada', 'pagada_por' => auth()->id(), 'pagada_at' => now(),
                'referencia_pago' => $referencia, 'updated_at' => now(),
            ]);
        });
    }

    private function cerrar(stdClass $nomina): void
    {
        $mes = CarbonImmutable::parse($nomina->periodo);
        $desde = $mes->toDateString();
        $hasta = $mes->endOfMonth()->toDateString();
        $incidencias = $this->datos->tabla('rh_procesos')->whereIn('seccion', ['novedades', 'decimos'])
            ->whereBetween('inicio', [$desde, $hasta])->whereNull('id_nomina');
        if ((clone $incidencias)->where('estado', 'pendiente')->exists()) {
            throw ValidationException::withMessages(['estado' => 'Resuelve las novedades y beneficios pendientes del mes antes de cerrar.']);
        }
        $aprobadas = $incidencias->where('estado', 'aprobado')->orderBy('id')->get();
        $this->validarCuotas($aprobadas);

        $contratos = $this->datos->tabla('rh_contratos')->join('rh_empleados as empleado', 'empleado.id', '=', 'rh_contratos.id_empleado')
            ->where('rh_contratos.inicio', '<=', $hasta)
            ->where(fn (Builder $q) => $q->whereNull('rh_contratos.fin')->orWhere('rh_contratos.fin', '>=', $desde))
            ->where('empleado.ingreso', '<=', $hasta)
            ->where(fn (Builder $q) => $q->whereNull('empleado.salida')->orWhere('empleado.salida', '>=', $desde))
            ->get(['rh_contratos.*', 'empleado.nombre', 'empleado.ingreso', 'empleado.salida'])->groupBy('id_empleado');
        if ($contratos->isEmpty() || $aprobadas->contains(fn (stdClass $item): bool => ! $contratos->has($item->id_empleado))) {
            throw ValidationException::withMessages(['estado' => 'Verifica que los empleados con novedades tengan contrato vigente durante el mes.']);
        }

        $porEmpleado = $aprobadas->groupBy('id_empleado');
        $recurrentes = $this->datos->procedimientos('conceptos')->where('estado', 'aprobado')
            ->where('inicio', '<=', $hasta)->where(fn (Builder $q) => $q->whereNull('fin')->orWhere('fin', '>=', $hasta))
            ->orderBy('id')->get()->groupBy('id_empleado');
        foreach ($contratos as $empleado => $tramos) {
            $base = $tramos->sum(function (stdClass $tramo) use ($desde, $hasta): int {
                $inicio = max($desde, $tramo->inicio, $tramo->ingreso);
                $fin = min($hasta, $tramo->fin ?? $hasta, $tramo->salida ?? $hasta);
                $dias = $fin < $inicio ? 0 : (int) CarbonImmutable::parse($inicio)->diffInDays(CarbonImmutable::parse($fin)) + 1;

                return $tramo->sueldo * $dias;
            });
            $sueldo = intdiv($base + intdiv($mes->daysInMonth, 2), $mes->daysInMonth);
            $lineas = [[
                'concepto' => 'Sueldo base proporcional a días calendario', 'tipo' => 'ingreso',
                'importe' => $sueldo,
            ]];
            foreach ($recurrentes->get($empleado, []) as $item) {
                $porcentual = str_ends_with($item->tipo, '-porcentual');
                $lineas[] = [
                    'concepto' => $item->titulo, 'tipo' => str_starts_with($item->tipo, 'descuento-') ? 'descuento' : 'ingreso',
                    'importe' => $porcentual ? intdiv($sueldo * DatosRrhh::centavos($item->cantidad) + 5000, 10000) : (int) $item->importe,
                    'id_concepto' => $item->id,
                ];
            }
            foreach ($porEmpleado->get($empleado, []) as $item) {
                $lineas[] = [
                    'concepto' => $item->titulo,
                    'tipo' => in_array($item->tipo, ['cuota', 'descuento'], true) ? 'descuento' : 'ingreso',
                    'importe' => (int) $item->importe,
                    'id_novedad' => $item->id,
                ];
            }
            $conceptos = collect($lineas)->groupBy('tipo');
            $ingresos = (int) $conceptos->get('ingreso', collect())->sum('importe');
            $descuentos = (int) $conceptos->get('descuento', collect())->sum('importe');
            if ($descuentos > $ingresos) {
                throw ValidationException::withMessages(['estado' => 'Hay un rol de pago con descuentos mayores a los ingresos.']);
            }
            $this->datos->insertar('rh_roles', [
                'id_nomina' => $nomina->id, 'id_empleado' => $empleado, 'empleado' => $tramos->first()->nombre,
                'ingresos' => $ingresos, 'descuentos' => $descuentos, 'neto' => $ingresos - $descuentos,
                'lineas' => json_encode($lineas, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
            ]);
        }
        $this->datos->tabla('rh_procesos')->whereIn('id', $aprobadas->pluck('id'))->update(['id_nomina' => $nomina->id, 'updated_at' => now()]);
        $this->datos->tabla('rh_nominas')->where('id', $nomina->id)->update([
            'estado' => 'cerrada', 'cerrada_por' => auth()->id(), 'cerrada_at' => now(), 'updated_at' => now(),
        ]);
    }

    private function validarCuotas(Collection $incidencias): void
    {
        foreach ($incidencias->where('tipo', 'cuota')->groupBy('id_prestamo') as $id => $cuotas) {
            $prestamo = $this->datos->registro($this->datos->procedimientos('prestamos')->where('estado', 'aprobado'), (int) $id);
            $cobrado = $this->datos->procedimientos('novedades')->where('id_prestamo', $id)->whereNotNull('id_nomina')->sum('importe');
            if ($cobrado + $cuotas->sum('importe') > $prestamo->importe) {
                throw ValidationException::withMessages(['estado' => 'Una cuota de préstamo o anticipo supera el saldo pendiente.']);
            }
        }
    }
}
