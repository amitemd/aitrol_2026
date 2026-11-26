<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $guayas = DB::table('provincias')->join('paises', 'paises.id', '=', 'provincias.id_pais')
            ->where('paises.codigo', 'EC')->where('provincias.nombre', 'Guayas')->value('provincias.id');

        if ($guayas === null) {
            return;
        }

        DB::table('ciudades')->insertOrIgnore(array_map(
            fn (string $nombre): array => ['id_provincia' => $guayas, 'nombre' => $nombre],
            [
                'Balao', 'Balzar', 'Bucay', 'Colimes', 'Coronel Marcelino Maridueña', 'Daule', 'Durán',
                'El Empalme', 'El Triunfo', 'Guayaquil', 'Isidro Ayora', 'Jujan', 'Lomas de Sargentillo',
                'Milagro', 'Naranjal', 'Naranjito', 'Nobol', 'Palestina', 'Pedro Carbo', 'Playas',
                'Salitre', 'Samborondón', 'Santa Lucía', 'Simón Bolívar', 'Yaguachi',
            ],
        ));
    }

    public function down(): void
    {
        // Las ciudades pueden estar vinculadas a personas después de la carga.
    }
};