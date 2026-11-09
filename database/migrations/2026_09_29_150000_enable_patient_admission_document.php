<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class EnablePatientAdmissionDocument extends Migration
{
    public function up()
    {
        DB::table('documento')->where(function ($query) {
            $query->where('tipo_documento', 'ING_PAC')
                ->orWhere('nombre_corto', 'INGRESO DE PACIENTES');
        })->update(['sistema' => 1, 'formato' => 'ingreso_paciente', 'link' => null]);
    }

    public function down()
    {
        DB::table('documento')->where(function ($query) {
            $query->where('tipo_documento', 'ING_PAC')
                ->orWhere('nombre_corto', 'INGRESO DE PACIENTES');
        })->where('formato', 'ingreso_paciente')->update(['sistema' => 0, 'formato' => null]);
    }
}
