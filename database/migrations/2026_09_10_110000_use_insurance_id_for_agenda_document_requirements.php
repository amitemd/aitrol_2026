<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UseInsuranceIdForAgendaDocumentRequirements extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('agenda_documento_requisitos')) {
            return;
        }

        $hasLegacyCode = Schema::hasColumn('agenda_documento_requisitos', 'codigo_seguro');

        if (! Schema::hasColumn('agenda_documento_requisitos', 'id_seguro')) {
            Schema::table('agenda_documento_requisitos', function (Blueprint $table) {
                $table->unsignedInteger('id_seguro')->nullable()->after('id');
            });
        }

        if ($hasLegacyCode) {
            $this->migrateLegacyInsuranceCodes();

            $unmapped = DB::table('agenda_documento_requisitos')->whereNull('id_seguro')->count();
            if ($unmapped > 0) {
                throw new RuntimeException(
                    'No se pudieron asociar '.$unmapped.' requisito(s) a un seguro. '
                    .'Revise los seguros antes de volver a ejecutar la migración; no se eliminó información.'
                );
            }

            $this->dropIndexIfExists('agenda_doc_req_unique');
            $this->dropIndexIfExists('agenda_doc_req_lookup');
            DB::statement('ALTER TABLE `agenda_documento_requisitos` DROP COLUMN `codigo_seguro`');

            Schema::table('agenda_documento_requisitos', function (Blueprint $table) {
                $table->unique(['id_seguro', 'id_documento', 'proc_consul', 'id_empresa'], 'agenda_doc_req_unique');
                $table->index(['id_seguro', 'proc_consul', 'id_empresa', 'estado'], 'agenda_doc_req_lookup');
            });
        }

        // codigo_documental fue creado únicamente para la primera versión de
        // este mantenimiento. La gestión vigente se relaciona por id_seguro.
        if (Schema::hasTable('seguros') && Schema::hasColumn('seguros', 'codigo_documental')) {
            DB::statement('ALTER TABLE `seguros` DROP COLUMN `codigo_documental`');
        }
    }

    public function down()
    {
        // No se revierte para no reconstruir códigos documentales que ya no usa V3.
    }

    private function migrateLegacyInsuranceCodes()
    {
        if (! Schema::hasTable('seguros')) {
            return;
        }

        $hasDocumentCode = Schema::hasColumn('seguros', 'codigo_documental');
        foreach (DB::table('seguros')->get() as $insurance) {
            $codes = [
                strtoupper(trim((string) $insurance->nombre)),
                $this->legacyCodeFromName($insurance->nombre),
            ];
            if ($hasDocumentCode && ! empty($insurance->codigo_documental)) {
                $codes[] = trim((string) $insurance->codigo_documental);
            }
            $codes = array_values(array_unique(array_filter($codes)));

            if (! $codes) {
                continue;
            }

            DB::table('agenda_documento_requisitos')
                ->whereNull('id_seguro')
                ->whereIn('codigo_seguro', $codes)
                ->update(['id_seguro' => (int) $insurance->id]);
        }
    }

    private function legacyCodeFromName($name)
    {
        $name = strtoupper(trim((string) $name));
        $name = str_replace(['Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ'], ['A', 'E', 'I', 'O', 'U', 'N'], $name);
        $name = preg_replace('/[^A-Z0-9]+/', '_', $name);
        $name = trim($name, '_');

        return $name !== '' ? substr($name, 0, 70) : '';
    }

    private function dropIndexIfExists($index)
    {
        $found = DB::select('SHOW INDEX FROM `agenda_documento_requisitos` WHERE Key_name = ?', [$index]);
        if ($found) {
            DB::statement('ALTER TABLE `agenda_documento_requisitos` DROP INDEX `'.$index.'`');
        }
    }
}
