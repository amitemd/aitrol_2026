<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

final class UnidadesInventarioIniciales
{
    public function copiarA(int $empresa): void
    {
        $fecha = now();
        $filas = [];
        foreach (['UND' => 'Unidad', 'CJ' => 'Caja', 'GAL' => 'Galón', 'G' => 'Gramo', 'KG' => 'Kilogramo',
            'L' => 'Litro', 'ML' => 'Mililitro', 'M' => 'Metro'] as $codigo => $nombre) {
            $filas[] = ['id_empresa' => $empresa, 'codigo' => $codigo, 'nombre' => $nombre,
                'created_at' => $fecha, 'updated_at' => $fecha];
        }
        DB::table('inv_unidades')->insertOrIgnore($filas);
    }
}
