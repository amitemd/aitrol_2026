<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use LogicException;

final class PlanCuentasInicial
{
    public function copiarA(int $empresa, int $usuario): void
    {
        $plantilla = DB::table('co_plan_cuentas_plantilla')->orderBy('codigo')->get();
        if ($plantilla->isEmpty()) {
            throw new LogicException('La plantilla del plan de cuentas está vacía.');
        }

        $cuentasPorCodigo = [];
        $fecha = now();
        foreach ($plantilla as $cuenta) {
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
