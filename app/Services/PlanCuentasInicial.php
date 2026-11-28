<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use LogicException;

final class PlanCuentasInicial
{
    public function copiarA(int $empresa, ?int $usuario = null): void
    {
        $plantilla = DB::table('co_plan_cuentas_plantilla')->orderBy('codigo')->get();
        if ($plantilla->isEmpty()) {
            throw new LogicException('La plantilla del plan de cuentas está vacía.');
        }

        $existentes = DB::table('co_cuentas')->where('id_empresa', $empresa)
            ->whereIn('codigo', $plantilla->pluck('codigo'))->get(['id', 'codigo', 'clase', 'auxiliar'])->keyBy('codigo');
        $cuentasPorCodigo = [];
        $fecha = now();
        foreach ($plantilla as $cuenta) {
            $existente = $existentes->get($cuenta->codigo);
            if ($existente !== null) {
                $cuentasPorCodigo[$cuenta->codigo] = $existente->id;

                continue;
            }

            $padreExistente = $existentes->get($cuenta->codigo_padre);
            if ($padreExistente !== null && ($padreExistente->auxiliar || $padreExistente->clase !== $cuenta->clase)) {
                throw new LogicException("La cuenta {$cuenta->codigo_padre} de la empresa {$empresa} debe ser un grupo de clase {$cuenta->clase} para cargar sus subcuentas.");
            }

            $cuentasPorCodigo[$cuenta->codigo] = DB::table('co_cuentas')->insertGetId([
                'id_empresa' => $empresa,
                'codigo' => $cuenta->codigo,
                'nombre' => $cuenta->nombre,
                'clase' => $cuenta->clase,
                'id_padre' => $cuenta->codigo_padre === null ? null : $cuentasPorCodigo[$cuenta->codigo_padre],
                'auxiliar' => $cuenta->auxiliar,
                'efectivo' => $cuenta->efectivo,
                'activo' => true,
                'id_usuario_creacion' => $usuario,
                'created_at' => $fecha,
                'updated_at' => $fecha,
            ]);
        }
    }
}
