<?php

namespace Aitrol\Rrhh\Application;

use Carbon\CarbonImmutable;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final readonly class GestionPersonal
{
    public function __construct(private DatosRrhh $datos) {}

    public function guardar(string $seccion, ?int $registro, array $campos): void
    {
        $this->datos->transaccion(function () use ($seccion, $registro, $campos): void {
            $consulta = $this->consulta($seccion);
            $actual = $registro === null ? null : $this->datos->registro(clone $consulta, $registro);
            if (isset(config('rrhh.procesos')[$seccion]) && $seccion !== 'organizacion') {
                abort_if($actual !== null && $actual->estado !== 'pendiente', 409, 'El registro ya fue resuelto.');
            }

            if (isset($campos['id_empleado'])) {
                $this->datos->registro($this->datos->tabla('rh_empleados'), (int) $campos['id_empleado']);
            }
            $campos = match ($seccion) {
                'empleados' => $this->empleado($campos, $registro),
                'contratos' => $this->contrato($campos, $registro, $actual?->id_empleado),
                default => $this->proceso($seccion, $campos),
            };
            if ($registro === null) {
                $this->datos->insertar($consulta->from, $campos);

                return;
            }
            $consulta->where('id', $registro)->update($campos + ['updated_at' => now()]);
        });
    }

    public function consulta(string $seccion): Builder
    {
        return match ($seccion) {
            'empleados' => $this->datos->tabla('rh_empleados'),
            'contratos' => $this->datos->tabla('rh_contratos'),
            default => $this->datos->procedimientos($seccion),
        };
    }

    public function resolver(string $seccion, int $registro, string $estado): void
    {
        $this->datos->transaccion(function () use ($seccion, $registro, $estado): void {
            $consulta = $this->datos->procedimientos($seccion);
            $actual = $this->datos->registro(clone $consulta, $registro);
            abort_unless($actual->estado === 'pendiente', 409, 'El registro ya fue resuelto.');
            if ($estado === 'aprobado') {
                $this->validarMesAbierto($seccion, $actual->inicio);
            }
            if ($estado === 'aprobado' && $seccion === 'permisos') {
                $aprobados = $this->datos->procedimientos('permisos')->where('id_empleado', $actual->id_empleado)->where('estado', 'aprobado');
                $cruce = (clone $aprobados)->where('inicio', '<=', $actual->fin)->where('fin', '>=', $actual->inicio)->exists();
                if ($cruce) {
                    throw ValidationException::withMessages(['estado' => 'Ya existe un permiso aprobado que coincide con estas fechas.']);
                }
                if ($actual->tipo === 'vacaciones') {
                    $dias = fn (string $desde, string $hasta): int => (int) CarbonImmutable::parse($desde)->diffInDays(CarbonImmutable::parse($hasta)) + 1;
                    $disponibles = (int) $this->datos->procedimientos('saldos-vacaciones')
                        ->where('id_empleado', $actual->id_empleado)->where('estado', 'aprobado')->where('inicio', '<=', $actual->inicio)->sum('cantidad');
                    $reservados = $aprobados->where('tipo', 'vacaciones')->get(['inicio', 'fin'])
                        ->sum(fn ($item): int => $dias($item->inicio, $item->fin));
                    if ($dias($actual->inicio, $actual->fin) > $disponibles - $reservados) {
                        throw ValidationException::withMessages(['estado' => 'Los días solicitados superan el saldo de vacaciones disponible.']);
                    }
                }
            }
            $consulta->where('id', $registro)->update([
                'estado' => $estado, 'resuelto_por' => auth()->id(), 'resuelto_at' => now(), 'updated_at' => now(),
            ]);
        });
    }

    private function empleado(array $campos, ?int $registro): array
    {
        $campos['codigo'] = Str::upper($campos['codigo']);
        if ($this->datos->codigoEmpleadoOcupado($campos['codigo'], $registro)) {
            throw ValidationException::withMessages(['codigo' => 'El código ya está asignado en esta empresa.']);
        }
        foreach (['id_departamento' => 'departamento', 'id_cargo' => 'cargo'] as $campo => $tipo) {
            if (isset($campos[$campo])) {
                $this->datos->registro($this->datos->procedimientos('organizacion')->where('tipo', $tipo), (int) $campos[$campo]);
            }
        }

        return $campos;
    }

    private function contrato(array $campos, ?int $registro, ?int $empleadoAnterior): array
    {
        $empleado = $this->datos->registro($this->datos->tabla('rh_empleados'), (int) $campos['id_empleado']);
        abort_if($empleadoAnterior !== null && $empleadoAnterior !== $empleado->id, 409, 'El titular del contrato no puede cambiar.');
        if ($campos['inicio'] < $empleado->ingreso || ($empleado->salida && $campos['inicio'] > $empleado->salida)) {
            throw ValidationException::withMessages(['inicio' => 'La fecha inicial debe estar dentro de la relación laboral.']);
        }
        $cruce = $this->datos->tabla('rh_contratos')->where('id_empleado', $empleado->id)
            ->when($registro !== null, fn (Builder $q) => $q->where('id', '<>', $registro))
            ->when($campos['fin'] ?? null, fn (Builder $q, string $fin) => $q->where('inicio', '<=', $fin))
            ->where(fn (Builder $q) => $q->whereNull('fin')->orWhere('fin', '>=', $campos['inicio']))->exists();
        if ($cruce) {
            throw ValidationException::withMessages(['inicio' => 'Las fechas se superponen con otro contrato del empleado.']);
        }
        $campos['sueldo'] = DatosRrhh::centavos($campos['sueldo']);

        return $campos;
    }

    private function proceso(string $seccion, array $campos): array
    {
        $this->validarMesAbierto($seccion, $campos['inicio'] ?? null);
        $campos['seccion'] = $seccion;
        $campos['id_prestamo'] = $seccion === 'novedades' && $campos['tipo'] === 'cuota' ? ($campos['id_prestamo'] ?? null) : null;
        if ($campos['id_prestamo'] !== null) {
            $prestamo = $this->datos->procedimientos('prestamos')->where('estado', 'aprobado')
                ->where('id_empleado', $campos['id_empleado'])->where('inicio', '<=', $campos['inicio']);
            $this->datos->registro($prestamo, (int) $campos['id_prestamo']);
        }
        if (isset($campos['importe'])) {
            $campos['importe'] = DatosRrhh::centavos($campos['importe']);
        }

        return $campos;
    }

    private function validarMesAbierto(string $seccion, ?string $fecha): void
    {
        if (in_array($seccion, ['novedades', 'decimos'], true)
            && $this->datos->tabla('rh_nominas')->where('periodo', substr((string) $fecha, 0, 7).'-01')
                ->whereIn('estado', ['cerrada', 'pagada'])->exists()) {
            throw ValidationException::withMessages(['inicio' => 'La nómina de este mes ya fue cerrada.']);
        }
    }
}
