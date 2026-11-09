<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddPrincipalToWarehouses extends Migration
{
    public function up()
    {
        if (! Schema::hasColumn('ct_bodegas', 'principal')) {
            Schema::table('ct_bodegas', function (Blueprint $table) {
                $table->boolean('principal')->default(0)->after('estado');
                $table->index(['id_empresa', 'principal'], 'idx_ct_bodegas_empresa_principal');
            });
        }

        DB::transaction(function () {
            DB::table('ct_bodegas')->update(['principal' => 0]);
            $empresas = DB::table('ct_bodegas')->whereNotNull('id_empresa')
                ->where('id_empresa', '<>', '')->distinct()->pluck('id_empresa');
            foreach ($empresas as $idEmpresa) {
                $idPrincipal = null;
                if (Schema::hasTable('de_parametros')) {
                    $idConfigurado = (int) DB::table('de_parametros')->where('id_empresa', $idEmpresa)
                        ->where('nombre', 'V3_BODEGA_PRINCIPAL')->where('estado', 1)->value('valor');
                    if ($idConfigurado > 0) {
                        $idPrincipal = DB::table('ct_bodegas')->where('id', $idConfigurado)
                            ->where('id_empresa', $idEmpresa)->where('estado', 1)->value('id');
                    }
                }
                if (! $idPrincipal) {
                    $idPrincipal = DB::table('ct_bodegas')->where('id_empresa', $idEmpresa)
                        ->where('estado', 1)->orderBy('id')->value('id');
                }
                if ($idPrincipal) {
                    DB::table('ct_bodegas')->where('id', $idPrincipal)->update(['principal' => 1]);
                }
            }
        });
    }

    public function down()
    {
        if (Schema::hasColumn('ct_bodegas', 'principal')) {
            Schema::table('ct_bodegas', function (Blueprint $table) {
                $table->dropIndex('idx_ct_bodegas_empresa_principal');
                $table->dropColumn('principal');
            });
        }
    }
}
