<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SeparateClinicalCatalogsByCompany extends Migration
{
    private $tables = ['especialidad', 'seguros', 'ct_tipo_pago'];

    public function up()
    {
        foreach ($this->tables as $table) {
            if (! Schema::hasColumn($table, 'id_empresa')) {
                Schema::table($table, function (Blueprint $definition) {
                    $definition->string('id_empresa', 13)->nullable()->index();
                });
            }
        }

        $defaultCompany = DB::table('empresa')->where('estado', 1)
            ->orderBy('prioridad')->orderBy('id')->value('id');
        if (! $defaultCompany) {
            $defaultCompany = DB::table('empresa')->orderBy('id')->value('id');
        }
        if (! $defaultCompany) {
            return;
        }

        foreach ($this->tables as $table) {
            $this->assignByCreator($table);
            DB::table($table)->whereNull('id_empresa')->orWhere('id_empresa', '')
                ->update(['id_empresa' => (string) $defaultCompany]);
            DB::statement('ALTER TABLE `'.$table.'` MODIFY `id_empresa` VARCHAR(13) NOT NULL');
        }
    }

    private function assignByCreator($table)
    {
        if (! Schema::hasTable('usuario_empresa') || ! Schema::hasColumn($table, 'id_usuariocrea')) {
            return;
        }
        $owners = DB::table('usuario_empresa')->select('id_usuario', DB::raw('MIN(id_empresa) as id_empresa'))
            ->groupBy('id_usuario')->havingRaw('COUNT(DISTINCT id_empresa) = 1')->get();
        foreach ($owners as $owner) {
            DB::table($table)->where('id_usuariocrea', $owner->id_usuario)
                ->whereNull('id_empresa')->update(['id_empresa' => (string) $owner->id_empresa]);
        }
    }

    public function down()
    {
        // No se revierte automaticamente: quitar id_empresa mezclaria de
        // nuevo catalogos que ya pertenecen a empresas distintas.
    }
}
