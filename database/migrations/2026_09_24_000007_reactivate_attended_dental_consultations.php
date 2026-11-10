<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ReactivateAttendedDentalConsultations extends Migration
{
    public function up(): void
    {
        foreach (['agenda', 'especialidad'] as $table) {
            if (! Schema::hasTable($table)) {
                throw new RuntimeException('No se pueden reparar las consultas odontologicas: falta '.$table.'.');
            }
        }

        foreach (['estado', 'estado_cita', 'proc_consul', 'espid'] as $column) {
            if (! Schema::hasColumn('agenda', $column)) {
                throw new RuntimeException('No se pueden reparar las consultas odontologicas: falta agenda.'.$column.'.');
            }
        }

        DB::transaction(function () {
            $especialidades = DB::table('especialidad')
                ->where(function ($query) {
                    $query->whereRaw('LOWER(TRIM(nombre)) LIKE ?', ['%odontolog%'])
                        ->orWhereRaw('LOWER(TRIM(nombre)) LIKE ?', ['%periodont%']);
                })
                ->pluck('id')
                ->map(function ($id) {
                    return (int) $id;
                })
                ->all();

            if (! $especialidades) {
                return;
            }

            $valores = ['estado' => 1];
            if (Schema::hasColumn('agenda', 'updated_at')) {
                $valores['updated_at'] = now();
            }

            DB::table('agenda')
                ->where('estado', 0)
                ->where('estado_cita', 4)
                ->where('proc_consul', 0)
                ->whereIn('espid', $especialidades)
                ->update($valores);
        });
    }

    public function down(): void
    {
        // No se vuelven a cancelar consultas clinicas que ya constan como atendidas.
    }
}
