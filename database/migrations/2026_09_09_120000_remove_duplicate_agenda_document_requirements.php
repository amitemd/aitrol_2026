<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RemoveDuplicateAgendaDocumentRequirements extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('agenda_documento_requisitos')) {
            return;
        }

        $insuranceColumn = Schema::hasColumn('agenda_documento_requisitos', 'id_seguro') ? 'id_seguro' : 'codigo_seguro';
        $globalRows = DB::table('agenda_documento_requisitos')->whereNull('id_empresa')->get();
        foreach ($globalRows as $row) {
            $hasCompanyCopy = DB::table('agenda_documento_requisitos')
                ->where($insuranceColumn, $row->{$insuranceColumn})
                ->where('id_documento', $row->id_documento)
                ->where('proc_consul', $row->proc_consul)
                ->whereNotNull('id_empresa')
                ->exists();
            if ($hasCompanyCopy) {
                DB::table('agenda_documento_requisitos')->where('id', $row->id)->delete();
            }
        }
    }

    public function down()
    {
        // Los registros eliminados eran duplicados de configuraciones por empresa.
    }
}
