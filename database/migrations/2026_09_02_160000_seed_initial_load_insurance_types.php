<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Sis_medico\Services\InitialLoadCatalogService;

class SeedInitialLoadInsuranceTypes extends Migration
{
    public function up()
    {
        $actor = DB::table('users')->where('id_tipo_usuario', 1)->where('estado', 1)->orderBy('id')->value('id');
        if (! $actor) {
            $actor = DB::table('users')->orderBy('id')->value('id');
        }
        if ($actor) {
            app(InitialLoadCatalogService::class)->ensureInsuranceTypes($actor, '127.0.0.1');
        }
    }

    public function down()
    {
        // Los tipos pueden estar referenciados por seguros ya importados.
        // No se eliminan durante rollback para preservar integridad clínica y financiera.
    }
}
