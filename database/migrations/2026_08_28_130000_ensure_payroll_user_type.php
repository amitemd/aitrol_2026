<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EnsurePayrollUserType extends Migration
{
    /**
     * Add the technical role used by the payroll employee registry when a
     * legacy database does not already provide it.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('tipousuario') || DB::table('tipousuario')->where('nombre', 'NOMINA')->exists()) {
            return;
        }

        DB::table('tipousuario')->insert([
            'nombre' => 'NOMINA',
            'descripcion' => 'USUARIO TECNICO PARA NOMINA',
            'estado' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * The role can already be in use by employees, so it is not deleted on rollback.
     *
     * @return void
     */
    public function down()
    {
        // Intentionally left empty to protect existing employee accounts.
    }
}
