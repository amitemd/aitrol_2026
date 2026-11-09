<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateEcuadorTerritorialCatalogs extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('ecuador_cantones')) {
            Schema::create('ecuador_cantones', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('id_provincia');
                $table->string('codigo', 4)->unique();
                $table->string('nombre', 160);
                $table->tinyInteger('estado')->default(1);
                $table->timestamps();
                $table->index(['id_provincia', 'estado']);
                $table->index('nombre');
            });
        }

        if (! Schema::hasTable('ecuador_parroquias')) {
            Schema::create('ecuador_parroquias', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('id_canton');
                $table->string('codigo', 6)->unique();
                $table->string('nombre', 200);
                $table->string('tipo', 20)->default('RURAL');
                $table->tinyInteger('estado')->default(1);
                $table->timestamps();
                $table->index(['id_canton', 'estado']);
                $table->index('nombre');
            });
        }

        $catalogPath = database_path('data/ecuador_division_territorial_2026.json');
        if (! is_file($catalogPath)) {
            throw new RuntimeException('No se encontro el catalogo territorial de Ecuador: '.$catalogPath);
        }

        $catalog = json_decode(file_get_contents($catalogPath), true);
        if (! is_array($catalog)
            || empty($catalog['provincias'])
            || empty($catalog['cantones'])
            || empty($catalog['parroquias'])) {
            throw new RuntimeException('El catalogo territorial de Ecuador no tiene un formato valido.');
        }

        $now = date('Y-m-d H:i:s');
        $ecuadorId = $this->ensureEcuadorCountry($now);
        $provinceIds = $this->loadProvinces($catalog['provincias'], $ecuadorId, $now);
        $cantonIds = $this->loadCantons($catalog['cantones'], $provinceIds, $now);
        $this->loadLegacyCities($catalog['cantones'], $catalog['provincias'], $provinceIds, $ecuadorId, $now);
        $this->loadParishes($catalog['parroquias'], $cantonIds, $now);
    }

    public function down()
    {
        Schema::dropIfExists('ecuador_parroquias');
        Schema::dropIfExists('ecuador_cantones');
        // Las provincias se conservan porque el proyecto ya las comparte con otros perfiles.
    }

    private function ensureEcuadorCountry($now)
    {
        $country = DB::table('pais')->whereRaw('UPPER(nombre) = ?', ['ECUADOR'])->first();
        if ($country) {
            if (Schema::hasColumn('pais', 'estado')) {
                DB::table('pais')->where('id', $country->id)->update(['estado' => 1]);
            }

            return (int) $country->id;
        }

        $values = ['nombre' => 'ECUADOR'];
        if (Schema::hasColumn('pais', 'estado')) {
            $values['estado'] = 1;
        }
        if (Schema::hasColumn('pais', 'created_at')) {
            $values['created_at'] = $now;
        }
        if (Schema::hasColumn('pais', 'updated_at')) {
            $values['updated_at'] = $now;
        }

        return (int) DB::table('pais')->insertGetId($values);
    }

    private function loadProvinces(array $provinces, $ecuadorId, $now)
    {
        $ids = [];

        foreach ($provinces as $province) {
            $existing = DB::table('new_db_provincias')
                ->where('id_pais_provincia', $ecuadorId)
                ->where(function ($query) use ($province) {
                    $query->where('cod_provincia', $province['codigo'])
                        ->orWhereRaw('UPPER(nombre_provincia) = ?', [$province['nombre']]);
                })->first();

            $values = [
                'id_pais_provincia' => $ecuadorId,
                'nombre_provincia' => $province['nombre'],
                'cod_provincia' => $province['codigo'],
                'estado' => 1,
                'updated_at_provincia' => $now,
            ];

            if ($existing) {
                DB::table('new_db_provincias')->where('id_provincia', $existing->id_provincia)->update($values);
                $ids[$province['codigo']] = (int) $existing->id_provincia;
            } else {
                $values['created_at_provincia'] = $now;
                $ids[$province['codigo']] = (int) DB::table('new_db_provincias')->insertGetId($values);
            }
        }

        return $ids;
    }

    private function loadCantons(array $cantons, array $provinceIds, $now)
    {
        $ids = [];
        $codes = array_column($cantons, 'codigo');
        $existingByCode = DB::table('ecuador_cantones')
            ->whereIn('codigo', $codes)
            ->get()
            ->keyBy('codigo');
        $pending = [];

        foreach ($cantons as $canton) {
            if (! isset($provinceIds[$canton['provincia_codigo']])) {
                continue;
            }

            $existing = $existingByCode->get($canton['codigo']);
            $values = [
                'id_provincia' => $provinceIds[$canton['provincia_codigo']],
                'nombre' => $canton['nombre'],
                'estado' => 1,
                'updated_at' => $now,
            ];

            if ($existing) {
                if ((int) $existing->id_provincia !== (int) $values['id_provincia']
                    || $existing->nombre !== $values['nombre']
                    || (int) $existing->estado !== 1) {
                    DB::table('ecuador_cantones')->where('id', $existing->id)->update($values);
                }
                $ids[$canton['codigo']] = (int) $existing->id;
            } else {
                $values['codigo'] = $canton['codigo'];
                $values['created_at'] = $now;
                $pending[] = $values;
            }
        }

        foreach (array_chunk($pending, 250) as $chunk) {
            DB::table('ecuador_cantones')->insert($chunk);
        }
        if (count($pending)) {
            foreach (DB::table('ecuador_cantones')->whereIn('codigo', $codes)->get(['id', 'codigo']) as $row) {
                $ids[$row->codigo] = (int) $row->id;
            }
        }

        return $ids;
    }

    private function loadParishes(array $parishes, array $cantonIds, $now)
    {
        $existingByCode = collect();
        foreach (array_chunk(array_column($parishes, 'codigo'), 500) as $codes) {
            foreach (DB::table('ecuador_parroquias')->whereIn('codigo', $codes)->get() as $row) {
                $existingByCode->put($row->codigo, $row);
            }
        }
        $pending = [];

        foreach ($parishes as $parish) {
            if (! isset($cantonIds[$parish['canton_codigo']])) {
                continue;
            }

            $existing = $existingByCode->get($parish['codigo']);
            $values = [
                'id_canton' => $cantonIds[$parish['canton_codigo']],
                'nombre' => $parish['nombre'],
                'tipo' => $parish['tipo'],
                'estado' => 1,
                'updated_at' => $now,
            ];

            if ($existing) {
                if ((int) $existing->id_canton !== (int) $values['id_canton']
                    || $existing->nombre !== $values['nombre']
                    || $existing->tipo !== $values['tipo']
                    || (int) $existing->estado !== 1) {
                    DB::table('ecuador_parroquias')->where('id', $existing->id)->update($values);
                }
            } else {
                $values['codigo'] = $parish['codigo'];
                $values['created_at'] = $now;
                $pending[] = $values;
            }
        }

        foreach (array_chunk($pending, 300) as $chunk) {
            DB::table('ecuador_parroquias')->insert($chunk);
        }
    }

    /**
     * V3 y los formularios heredados consultan new_db_ciudades. En Ecuador,
     * el catalogo que el sistema presenta como "ciudad" corresponde al canton.
     */
    private function loadLegacyCities(array $cantons, array $provinces, array $provinceIds, $ecuadorId, $now)
    {
        if (! Schema::hasTable('new_db_ciudades')) {
            return;
        }

        $provinceNames = [];
        foreach ($provinces as $province) {
            $provinceNames[$province['codigo']] = $province['nombre'];
        }

        foreach ($cantons as $canton) {
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
                'id_pais_ciudad' => $ecuadorId,
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
}
