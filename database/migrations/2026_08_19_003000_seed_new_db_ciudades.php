<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SeedNewDbCiudades extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('new_db_ciudades')
            || ! Schema::hasTable('new_db_provincias')
            || ! Schema::hasTable('pais')) {
            return;
        }

        $catalogPath = database_path('data/ecuador_division_territorial_2026.json');
        if (! is_file($catalogPath)) {
            throw new RuntimeException('No se encontro el catalogo territorial de Ecuador: '.$catalogPath);
        }

        $catalog = json_decode(file_get_contents($catalogPath), true);
        if (! is_array($catalog) || empty($catalog['provincias']) || empty($catalog['cantones'])) {
            throw new RuntimeException('El catalogo territorial de Ecuador no tiene provincias o cantones validos.');
        }

        $country = DB::table('pais')->whereRaw('UPPER(nombre) = ?', ['ECUADOR'])->first();
        if (! $country) {
            throw new RuntimeException('No existe Ecuador en el catalogo de paises.');
        }

        $provinceNames = [];
        foreach ($catalog['provincias'] as $province) {
            $provinceNames[$province['codigo']] = $province['nombre'];
        }

        $provinceIds = [];
        $provinces = DB::table('new_db_provincias')
            ->where('id_pais_provincia', $country->id)
            ->get(['id_provincia', 'cod_provincia']);

        foreach ($provinces as $province) {
            $provinceIds[$province->cod_provincia] = (int) $province->id_provincia;
        }

        $now = date('Y-m-d H:i:s');
        foreach ($catalog['cantones'] as $canton) {
            $provinceCode = $canton['provincia_codigo'];
            if (! isset($provinceIds[$provinceCode])) {
                continue;
            }

            $provinceId = $provinceIds[$provinceCode];
            $existing = DB::table('new_db_ciudades')
                ->where('id_provincia_ciudad', $provinceId)
                ->where(function ($query) use ($canton) {
                    $query->where('cod_area_provincia_ciudad', $canton['codigo'])
                        ->orWhereRaw('UPPER(nombre_ciudad) = ?', [$canton['nombre']]);
                })->first();

            $values = [
                'id_pais_ciudad' => (int) $country->id,
                'id_provincia_ciudad' => $provinceId,
                'provincia_ciudad' => isset($provinceNames[$provinceCode]) ? $provinceNames[$provinceCode] : null,
                'cod_area_provincia_ciudad' => $canton['codigo'],
                'nombre_ciudad' => $canton['nombre'],
                'estado' => 1,
                'updated_at_ciudad' => $now,
            ];

            if ($existing) {
                DB::table('new_db_ciudades')->where('id_ciudad', $existing->id_ciudad)->update($values);
            } else {
                $values['fecha_ciudad'] = $now;
                $values['created_at_ciudad'] = $now;
                DB::table('new_db_ciudades')->insert($values);
            }
        }
    }

    public function down()
    {
        // El catalogo se conserva para no eliminar ciudades utilizadas por usuarios y empresas.
    }
}
