<?php

namespace Aitrol\Contabilidad\Application;

use Aitrol\Inventarios\Domain\CantidadInventario as Decimal;
use Closure;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use stdClass;

final readonly class LibroContable
{
    public function __construct(private DatosContables $datos) {}

    public function cuenta(array $entrada, ?int $id): stdClass
    {
        try {
            return $this->escribir(function () use ($entrada, $id): stdClass {
                if ($id !== null) {
                    $cuenta = $this->datos->fila('co_cuentas', $id);
                    $hijas = $this->datos->tabla('co_cuentas')->where('id_padre', $id)->where('activo', true)->exists();
                    abort_if(! $entrada['activo'] && $hijas, 409, 'Desactiva primero las subcuentas activas.');
                    if ($entrada['activo'] && $cuenta->id_padre !== null) {
                        abort_unless($this->datos->fila('co_cuentas', $cuenta->id_padre)->activo, 409, 'Activa primero la cuenta superior.');
                    }
                    $this->datos->actualizar('co_cuentas', $id, $entrada);
                } else {
                    if (isset($entrada['id_padre'])) {
                        $padre = $this->datos->tabla('co_cuentas')->where('id', $entrada['id_padre'])
                            ->where('clase', $entrada['clase'])->where('activo', true)->where('auxiliar', false)->first();
                        if ($padre === null) $this->invalido('id_padre', 'Elige una cuenta de grupo activa de la misma clase y empresa.');
                    }
                    if (($entrada['efectivo'] ?? false) && ($entrada['clase'] !== 'activo' || ! $entrada['auxiliar'])) {
                        $this->invalido('efectivo', 'El efectivo debe ser una cuenta auxiliar de activo.');
                    }
                    $id = $this->datos->crear('co_cuentas', $entrada);
                }

                return $this->datos->cuentas()->where('co_cuentas.id', $id)->first();
            });
        } catch (UniqueConstraintViolationException) {
            abort(409, 'El código ya existe en el plan de cuentas de tu empresa.');
        }
    }

    public function abrirPeriodo(array $entrada): stdClass
    {
        return $this->escribir(function () use ($entrada): stdClass {
            abort_if($this->datos->tabla('co_periodos')->where('desde', '<=', $entrada['hasta'])
                ->where('hasta', '>=', $entrada['desde'])->exists(), 409, 'Existe un período que se superpone a esas fechas.');
            $id = $this->datos->crear('co_periodos', $entrada);

            return $this->datos->fila('co_periodos', $id);
        });
    }

    public function cerrarPeriodo(int $id, string $motivo): stdClass
    {
        return $this->escribir(function () use ($id, $motivo): stdClass {
            $periodo = $this->datos->fila('co_periodos', $id);
            abort_if($periodo->cerrado_at !== null, 409, 'El período ya está cerrado.');
            abort_if($periodo->hasta > now()->toDateString(), 409, 'El período aún no termina.');
            $this->datos->actualizar('co_periodos', $id, ['cerrado_at' => now(), 'motivo_cierre' => $motivo]);

            return $this->datos->fila('co_periodos', $id);
        });
    }

    public function registrar(array $entrada): array
    {
        return $this->escribir(function () use ($entrada): array {
            $lineas = [];
            foreach ($entrada['lineas'] as $fila) {
                $lineas[] = [
                    'id_cuenta' => (int) $fila['id_cuenta'], 'debe' => Decimal::unidades($fila['debe'] ?? 0, 2),
                    'haber' => Decimal::unidades($fila['haber'] ?? 0, 2), 'detalle' => $fila['detalle'] ?? null,
                    'movimiento_patrimonio' => $fila['movimiento_patrimonio'] ?? null,
                ];
            }
            $clave = isset($entrada['clave_operacion']) ? strtolower($entrada['clave_operacion']) : null;
            $cabecera = ['fecha' => $entrada['fecha'], 'concepto' => $entrada['concepto'], 'referencia' => $entrada['referencia'] ?? null, 'flujo' => $entrada['flujo'] ?? null];
            $huella = hash('sha256', json_encode([$cabecera, $lineas], JSON_THROW_ON_ERROR));
            if ($clave !== null && ($previo = $this->datos->tabla('co_asientos')->where('clave_operacion', $clave)->first())) {
                abort_unless($previo->huella === $huella, 409, 'La clave ya se utilizó con otro asiento.');

                return [$this->datos->asiento($previo->id), true];
            }
            $lineas = $this->validarLineas($lineas, $cabecera['flujo']);

            return [$this->insertar($cabecera + ['clave_operacion' => $clave, 'huella' => $huella], $lineas), false];
        });
    }

    public function revertir(int $id, array $entrada, array $origen = []): stdClass
    {
        return $this->escribir(function () use ($id, $entrada, $origen): stdClass {
            $original = $this->datos->asiento($id);
            abort_if($original->origen !== null && $origen === [], 409, 'Revierte la operación desde su módulo de origen.');
            abort_if($original->id_reversa_de !== null || $original->id_reversion !== null, 409, 'El asiento ya fue revertido o es una reversión.');
            if ($entrada['fecha'] < $original->fecha) $this->invalido('fecha', 'La fecha de reversión debe ser igual o posterior al asiento original.');
            $lineas = $original->lineas->map(fn (stdClass $fila) => [
                'id_cuenta' => $fila->id_cuenta, 'debe' => $fila->haber, 'haber' => $fila->debe, 'detalle' => $fila->detalle,
                'movimiento_patrimonio' => $fila->movimiento_patrimonio,
            ])->all();

            return $this->insertar([
                'fecha' => $entrada['fecha'], 'concepto' => $entrada['motivo'], 'referencia' => 'Reversión del asiento '.$original->numero,
                'flujo' => $original->flujo, 'id_reversa_de' => $id,
            ] + $origen, $lineas);
        });
    }

    public function registrarAutomatico(string $origen, int $id, array $cabecera, array $lineas): stdClass
    {
        return $this->escribir(function () use ($origen, $id, $cabecera, $lineas): stdClass {
            $previo = $this->datos->tabla('co_asientos')->where('origen', $origen)->where('id_origen', $id)->value('id');
            if ($previo !== null) return $this->datos->asiento($previo);
            $lineas = $this->validarLineas($lineas, $cabecera['flujo'] ?? null);

            return $this->insertar($cabecera + ['origen' => $origen, 'id_origen' => $id], $lineas);
        });
    }

    public function guardarIntegracion(array $entrada): void
    {
        $this->escribir(function () use ($entrada): void {
            $reglas = $entrada['reglas'] ?? [];
            $tesoreria = $entrada['tesoreria'] ?? [];
            foreach ($reglas as $clave => $id) {
                if ($id === null) continue;
                [, $clase] = config('contabilidad.reglas.'.$clave);
                $this->validarMapeo((int) $id, $clase, false, 'reglas.'.$clave);
            }
            foreach ($tesoreria as $idCaja => $idCuenta) {
                if ($idCuenta === null) continue;
                $caja = $this->datos->tabla('cb_cuentas')->where('id', $idCaja)->where('moneda', 'USD')->first();
                if ($caja === null) $this->invalido('tesoreria.'.$idCaja, 'Selecciona una caja o banco en USD de tu empresa.');
                $this->validarMapeo((int) $idCuenta, 'activo', true, 'tesoreria.'.$idCaja);
            }
            $usadas = array_filter($tesoreria, fn ($id) => $id !== null);
            if (count($usadas) !== count(array_unique($usadas))) {
                $this->invalido('tesoreria', 'Cada caja o banco debe tener una cuenta de efectivo diferente.');
            }
            $campos = [
                'activa' => $entrada['activa'], 'fecha_inicio' => $entrada['fecha_inicio'] ?? null,
                'reglas' => json_encode(array_filter($reglas, fn ($id) => $id !== null), JSON_THROW_ON_ERROR),
                'tesoreria' => json_encode($usadas, JSON_THROW_ON_ERROR),
            ];
            $id = $this->datos->tabla('co_integraciones')->value('id');
            $id === null ? $this->datos->crear('co_integraciones', $campos) : $this->datos->actualizar('co_integraciones', $id, $campos);
        });
    }

    private function validarMapeo(int $id, ?string $clase, bool $efectivo, string $campo): void
    {
        $cuenta = $this->datos->tabla('co_cuentas')->where('id', $id)->where('activo', true)->where('auxiliar', true)
            ->where('efectivo', $efectivo)->when($clase, fn ($q) => $q->where('clase', $clase))->first();
        if ($cuenta === null) $this->invalido($campo, 'Selecciona una cuenta auxiliar activa de la clase indicada.');
    }

    private function validarLineas(array $lineas, ?string $flujo): array
    {
        $ids = array_unique(array_column($lineas, 'id_cuenta'));
        if (count($ids) < 2) $this->invalido('lineas', 'Un asiento debe afectar al menos dos cuentas distintas.');
        $cuentas = $this->datos->tabla('co_cuentas')->whereIn('id', $ids)->where('activo', true)->where('auxiliar', true)->get()->keyBy('id');
        $efectivo = 0;
        $lineasEfectivo = 0;
        foreach ($lineas as $indice => &$fila) {
            $cuenta = $cuentas->get($fila['id_cuenta']);
            if ($cuenta === null) $this->invalido('lineas.'.$indice.'.id_cuenta', 'Selecciona una cuenta auxiliar activa de tu empresa.');
            if (($fila['debe'] > 0) === ($fila['haber'] > 0)) $this->invalido('lineas.'.$indice.'.debe', 'Cada línea debe tener un importe positivo en Debe o en Haber.');
            if ($flujo === 'apertura' && in_array($cuenta->clase, ['ingreso', 'gasto'], true)) $this->invalido('flujo', 'Una apertura solo puede usar cuentas de activo, pasivo y patrimonio.');
            if ($cuenta->efectivo) {
                $efectivo += $fila['debe'] - $fila['haber'];
                $lineasEfectivo++;
            }
            if ($cuenta->clase === 'patrimonio') {
                $fila['movimiento_patrimonio'] ??= $flujo === 'apertura' ? 'apertura' : 'otro';
            } elseif ($fila['movimiento_patrimonio'] !== null) {
                $this->invalido('lineas.'.$indice.'.movimiento_patrimonio', 'La clasificación solo corresponde a cuentas de patrimonio.');
            }
        }
        unset($fila);
        if (array_sum(array_column($lineas, 'debe')) !== array_sum(array_column($lineas, 'haber'))) {
            $this->invalido('lineas', 'El total del Debe debe ser igual al total del Haber.');
        }
        if ($lineasEfectivo === 0 && $flujo !== null && $flujo !== 'apertura') $this->invalido('flujo', 'Solo clasifica el flujo cuando el asiento afecta efectivo.');
        if ($lineasEfectivo > 0 && $efectivo === 0 && ($flujo !== 'traslado' || $lineasEfectivo !== count($lineas))) {
            $this->invalido('flujo', 'Separa los movimientos compensados. Un traslado interno solo usa cuentas de efectivo y su flujo neto es cero.');
        }
        if ($efectivo !== 0 && ($flujo === null || $flujo === 'traslado')) {
            $this->invalido('flujo', 'Clasifica el movimiento de efectivo como operación, inversión, financiación o apertura.');
        }

        return $lineas;
    }

    private function insertar(array $cabecera, array $lineas): stdClass
    {
        $periodo = $this->datos->tabla('co_periodos')->where('desde', '<=', $cabecera['fecha'])->where('hasta', '>=', $cabecera['fecha'])->first();
        abort_if($periodo === null || $periodo->cerrado_at !== null, 409, 'La fecha debe pertenecer a un período abierto.');
        $numero = (int) $this->datos->tabla('co_asientos')->max('numero') + 1;
        $id = $this->datos->crear('co_asientos', $cabecera + [
            'numero' => $numero, 'id_periodo' => $periodo->id, 'total' => array_sum(array_column($lineas, 'debe')),
        ]);
        foreach ($lineas as $fila) {
            $this->datos->crear('co_lineas', $fila + ['id_asiento' => $id], false);
        }

        return $this->datos->asiento($id);
    }

    private function escribir(Closure $funcion): mixed
    {
        return DB::transaction(function () use ($funcion) {
            $this->datos->bloquearEmpresa();

            return $funcion();
        }, 3);
    }

    private function invalido(string $campo, string $mensaje): never
    {
        throw ValidationException::withMessages([$campo => $mensaje]);
    }
}
