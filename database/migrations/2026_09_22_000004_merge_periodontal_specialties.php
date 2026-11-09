<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MergePeriodontalSpecialties extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('especialidad')) {
            return;
        }

        DB::transaction(function () {
            $especialidades = DB::table('especialidad')
                ->whereRaw('LOWER(TRIM(nombre)) IN (?, ?, ?)', ['periodoncia', 'periodontologia', 'periodontología'])
                ->orderBy('id')
                ->get();

            if ($especialidades->isEmpty()) {
                return;
            }

            $canonica = $especialidades->first(function ($especialidad) {
                return strpos(strtolower(trim((string) $especialidad->nombre)), 'periodontolog') === 0;
            }) ?: $especialidades->first();

            $duplicadas = $especialidades->where('id', '!=', $canonica->id);
            foreach ($duplicadas as $duplicada) {
                $this->moverReferencias((int) $duplicada->id, (int) $canonica->id);

                $valores = ['estado' => 0];
                if (Schema::hasColumn('especialidad', 'updated_at')) {
                    $valores['updated_at'] = now();
                }
                DB::table('especialidad')->where('id', $duplicada->id)->update($valores);
            }

            $valoresCanonicos = ['nombre' => 'PERIODONTOLOGIA', 'estado' => 1];
            if (Schema::hasColumn('especialidad', 'updated_at')) {
                $valoresCanonicos['updated_at'] = now();
            }
            DB::table('especialidad')->where('id', $canonica->id)->update($valoresCanonicos);
        });
    }

    public function down(): void
    {
        // La consolidacion de relaciones no se revierte: recrear el duplicado
        // separaria datos clinicos que ya pertenecen a una misma especialidad.
    }

    private function moverReferencias(int $origen, int $destino): void
    {
        if (Schema::hasTable('user_espe') && Schema::hasColumn('user_espe', 'espid')) {
            $usuariosCanonicos = DB::table('user_espe')->where('espid', $destino)->pluck('usuid')->all();
            if ($usuariosCanonicos) {
                DB::table('user_espe')->where('espid', $origen)->whereIn('usuid', $usuariosCanonicos)->delete();
            }
            DB::table('user_espe')->where('espid', $origen)->update(['espid' => $destino]);
        }

        foreach ([
            ['agenda', 'espid'],
            ['procedimiento', 'id_especialidad'],
            ['cie_10_3', 'id_especialidad'],
        ] as $referencia) {
            if (Schema::hasTable($referencia[0]) && Schema::hasColumn($referencia[0], $referencia[1])) {
                DB::table($referencia[0])->where($referencia[1], $origen)->update([$referencia[1] => $destino]);
            }
        }
    }
}
