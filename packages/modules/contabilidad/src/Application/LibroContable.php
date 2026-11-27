<?php

namespace Aitrol\Contabilidad\Application;

use Aitrol\Contabilidad\Application\Puertos\RepositorioContable;
use Aitrol\Contabilidad\Domain\PartidaDoble;
use Aitrol\Inventarios\Domain\CantidadInventario as Decimal;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Validation\ValidationException;
use stdClass;

final readonly class LibroContable
{
    public function __construct(private RepositorioContable $datos) {}

    public function cuenta(array $entrada, ?int $id): stdClass
    {
        try {
            return $this->datos->transaccion(function () use ($entrada, $id): stdClass {
                if ($id !== null) {
                    $cuenta = $this->datos->cuenta($id);
                    $hijas = $this->datos->tieneSubcuentasActivas($id);
                    abort_if(! $entrada['activo'] && $hijas, 409, 'Desactiva primero las subcuentas activas.');
                    if ($entrada['activo'] && $cuenta->id_padre !== null) {
                        abort_unless($this->datos->cuenta($cuenta->id_padre)->activo, 409, 'Activa primero la cuenta superior.');
                    }
                } else {
                    if (isset($entrada['id_padre'])) {
                        $padre = $this->datos->cuenta((int) $entrada['id_padre']);
                        if (! $padre->activo || $padre->auxiliar || $padre->clase !== $entrada['clase']) {
                            $this->invalido('id_padre', 'Elige una cuenta de grupo activa de la misma clase y empresa.');
                        }
                    }
                    if (($entrada['efectivo'] ?? false) && ($entrada['clase'] !== 'activo' || ! $entrada['auxiliar'])) {
                        $this->invalido('efectivo', 'El efectivo debe ser una cuenta auxiliar de activo.');
                    }
                }

                return $this->datos->cuenta($this->datos->guardarCuenta($entrada, $id));
            });
        } catch (UniqueConstraintViolationException) {
            abort(409, 'El código ya existe en el plan de cuentas de tu empresa.');
        }
    }

    public function abrirPeriodo(array $entrada): stdClass
    {
        return $this->datos->transaccion(function () use ($entrada): stdClass {
            abort_if($this->datos->periodosSolapados($entrada['desde'], $entrada['hasta']), 409, 'Existe un período que se superpone a esas fechas.');

            return $this->datos->periodo($this->datos->guardarPeriodo($entrada));
        });
    }

    public function cerrarPeriodo(int $id, string $motivo): stdClass
    {
        return $this->datos->transaccion(function () use ($id, $motivo): stdClass {
            $periodo = $this->datos->periodo($id);
            abort_if($periodo->cerrado_at !== null, 409, 'El período ya está cerrado.');
            abort_if($periodo->hasta > now()->toDateString(), 409, 'El período aún no termina.');
            $this->datos->guardarPeriodo(['cerrado_at' => now(), 'motivo_cierre' => $motivo], $id);

            return $this->datos->periodo($id);
        });
    }

    public function registrar(array $entrada): array
    {
        return $this->datos->transaccion(function () use ($entrada): array {
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
            if ($clave !== null && ($previo = $this->datos->asientoConClave($clave))) {
                abort_unless($previo->huella === $huella, 409, 'La clave ya se utilizó con otro asiento.');

                return [$this->datos->asiento($previo->id), true];
            }
            $lineas = $this->validarLineas($lineas, $cabecera['flujo']);

            return [$this->insertar($cabecera + ['clave_operacion' => $clave, 'huella' => $huella], $lineas), false];
        });
    }

    public function revertir(int $id, array $entrada, array $origen = []): stdClass
    {
        return $this->datos->transaccion(function () use ($id, $entrada, $origen): stdClass {
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
        return $this->datos->transaccion(function () use ($origen, $id, $cabecera, $lineas): stdClass {
            $previo = $this->datos->asientoDeOrigen($origen, $id);
            if ($previo !== null) return $this->datos->asiento($previo);
            $lineas = $this->validarLineas($lineas, $cabecera['flujo'] ?? null);

            return $this->insertar($cabecera + ['origen' => $origen, 'id_origen' => $id], $lineas);
        });
    }

    public function guardarIntegracion(array $entrada): void
    {
        $this->datos->transaccion(function () use ($entrada): void {
            $reglas = $entrada['reglas'] ?? [];
            $tesoreria = $entrada['tesoreria'] ?? [];
            foreach ($reglas as $clave => $id) {
                if ($id === null) continue;
                [, $clase] = config('contabilidad.reglas.'.$clave);
                $this->validarMapeo((int) $id, $clase, false, 'reglas.'.$clave);
            }
            foreach ($tesoreria as $idCaja => $idCuenta) {
                if ($idCuenta === null) continue;
                if (! $this->datos->cajaEnDolares((int) $idCaja)) $this->invalido('tesoreria.'.$idCaja, 'Selecciona una caja o banco en USD de tu empresa.');
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
            $this->datos->guardarIntegracion($campos);
        });
    }

    private function validarMapeo(int $id, ?string $clase, bool $efectivo, string $campo): void
    {
        $cuenta = $this->datos->auxiliares([$id])[$id] ?? null;
        if ($cuenta === null || (bool) $cuenta->efectivo !== $efectivo || ($clase !== null && $cuenta->clase !== $clase)) {
            $this->invalido($campo, 'Selecciona una cuenta auxiliar activa de la clase indicada.');
        }
    }

    private function validarLineas(array $lineas, ?string $flujo): array
    {
        $cuentas = $this->datos->auxiliares(array_unique(array_column($lineas, 'id_cuenta')));
        $errores = PartidaDoble::errores($lineas, $cuentas, $flujo);
        if ($errores !== []) throw ValidationException::withMessages($errores);
        foreach ($lineas as &$fila) {
            if ($cuentas[$fila['id_cuenta']]->clase === 'patrimonio') {
                $fila['movimiento_patrimonio'] ??= $flujo === 'apertura' ? 'apertura' : 'otro';
            }
        }

        return $lineas;
    }

    private function insertar(array $cabecera, array $lineas): stdClass
    {
        $periodo = $this->datos->periodoEnFecha($cabecera['fecha']);
        abort_if($periodo === null || $periodo->cerrado_at !== null, 409, 'La fecha debe pertenecer a un período abierto.');
        $id = $this->datos->guardarAsiento($cabecera + [
            'numero' => $this->datos->siguienteNumero(), 'id_periodo' => $periodo->id, 'total' => array_sum(array_column($lineas, 'debe')),
        ], $lineas);

        return $this->datos->asiento($id);
    }

    private function invalido(string $campo, string $mensaje): never
    {
        throw ValidationException::withMessages([$campo => $mensaje]);
    }
}
