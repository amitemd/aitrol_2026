<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddCompanyScopeToHospitalCapacity extends Migration
{
    private $tables = ['piso', 'tipo_habitacion', 'habitacion', 'cama', 'cama_transaccion'];

    public function up()
    {
        foreach ($this->tables as $table) {
            if (! Schema::hasTable($table) || Schema::hasColumn($table, 'id_empresa')) {
                continue;
            }
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->string('id_empresa', 20)->nullable()->after('id');
                $blueprint->index('id_empresa');
            });
        }

        // Reasignación conservadora: solo usa la empresa asociada al usuario
        // creador. Si no hay una relación inequívoca, el registro queda NULL
        // para que Sistemas lo revise y no se muestre en otra empresa.
        foreach ($this->tables as $table) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'id_empresa') || ! Schema::hasColumn($table, 'id_usuariocrea')) {
                continue;
            }
            $rows = DB::table($table)->whereNull('id_empresa')->select('id', 'id_usuariocrea')->get();
            foreach ($rows as $row) {
                $companies = DB::table('usuario_empresa')
                    ->where('id_usuario', $row->id_usuariocrea)
                    ->distinct()->pluck('id_empresa');
                if ($companies->count() === 1) {
                    DB::table($table)->where('id', $row->id)->update(['id_empresa' => (string) $companies->first()]);
                }
            }
        }
    }

    public function down()
    {
        foreach (array_reverse($this->tables) as $table) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'id_empresa')) {
                continue;
            }
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->dropIndex(['id_empresa']);
                $blueprint->dropColumn('id_empresa');
            });
        }
    }
}
