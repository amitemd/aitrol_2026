<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;

final class EmpresaActiva
{
    public function id(): int
    {
        $id = auth()->user()?->id_empresa;
        abort_if($id === null, 403, 'La cuenta debe tener una empresa asignada.');

        return (int) $id;
    }

    public function verificar(User $usuario): void
    {
        abort_unless((int) $usuario->id_empresa === $this->id(), 404);
    }

    public function prepararPermisos(int $idEmpresa): void
    {
        $filas = [];
        foreach (array_keys(config('accesos')) as $opcion) {
            foreach (['leer', 'escribir', 'actualizar', 'eliminar'] as $accion) {
                $filas[] = ['id_empresa' => $idEmpresa, 'opcion' => $opcion, 'accion' => $accion];
            }
        }
        DB::table('permisos')->insertOrIgnore($filas);
    }
}
