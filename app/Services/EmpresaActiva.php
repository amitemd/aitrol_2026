<?php

namespace App\Services;

use App\Models\User;
use Closure;
use Illuminate\Support\Facades\DB;

final class EmpresaActiva
{
    public function id(): int
    {
        $id = auth()->user()?->id_empresa;
        abort_if($id === null, 403, 'La cuenta debe tener una empresa asignada.');

        return (int) $id;
    }

    public function transaccion(Closure $trabajo, int $intentos = 3): mixed
    {
        $id = $this->id();

        return DB::transaction(function () use ($id, $trabajo): mixed {
            $empresa = DB::table('erp_empresas')->where('id', $id)->lockForUpdate()->first(['id']);
            abort_if($empresa === null, 403, 'La empresa asignada ya no existe.');

            return $trabajo();
        }, $intentos);
    }

    public function verificar(User $usuario): void
    {
        abort_unless((int) $usuario->id_empresa === $this->id(), 404);
    }

    public function prepararPermisos(int $idEmpresa): void
    {
        $filas = [];
        foreach (array_keys(config('accesos')) as $opcion) {
            $acciones = match ($opcion) {
                'auditoria.logs.index' => ['leer'],
                'compras.aprobaciones.index' => ['actualizar'],
                default => ['leer', 'escribir', 'actualizar', 'eliminar'],
            };
            foreach ($acciones as $accion) {
                $filas[] = ['id_empresa' => $idEmpresa, 'opcion' => $opcion, 'accion' => $accion];
            }
        }
        DB::table('permisos')->insertOrIgnore($filas);
    }
}
