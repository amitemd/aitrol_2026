<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RepairClinicalProcedureCompany extends Migration
{
    public function up()
    {
        if (DB::connection()->getDriverName() !== 'mysql' || ! $this->available()) {
            return;
        }

        DB::statement("UPDATE hc_procedimientos AS procedure_row
            INNER JOIN historiaclinica AS history ON history.hcid = procedure_row.id_hc
            INNER JOIN agenda AS appointment ON appointment.id = history.id_agenda
            SET procedure_row.id_empresa = appointment.id_empresa
            WHERE (procedure_row.id_empresa IS NULL OR TRIM(procedure_row.id_empresa) = '')
              AND appointment.id_empresa IS NOT NULL AND TRIM(appointment.id_empresa) <> ''");

        foreach (['insert' => 'INSERT', 'update' => 'UPDATE'] as $suffix => $event) {
            $name = 'hc_procedimientos_company_before_'.$suffix;
            DB::unprepared('DROP TRIGGER IF EXISTS `'.$name.'`');
            DB::unprepared("CREATE TRIGGER `{$name}` BEFORE {$event} ON `hc_procedimientos` FOR EACH ROW
                BEGIN
                    DECLARE derived_company VARCHAR(32) DEFAULT NULL;
                    SELECT appointment.id_empresa INTO derived_company
                    FROM historiaclinica AS history
                    INNER JOIN agenda AS appointment ON appointment.id = history.id_agenda
                    WHERE history.hcid = NEW.id_hc
                    LIMIT 1;
                    IF derived_company IS NOT NULL AND TRIM(derived_company) <> '' THEN
                        SET NEW.id_empresa = derived_company;
                    END IF;
                END");
        }
    }

    public function down()
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return;
        }
        DB::unprepared('DROP TRIGGER IF EXISTS `hc_procedimientos_company_before_insert`');
        DB::unprepared('DROP TRIGGER IF EXISTS `hc_procedimientos_company_before_update`');
    }

    private function available()
    {
        return Schema::hasTable('hc_procedimientos') && Schema::hasTable('historiaclinica')
            && Schema::hasTable('agenda') && Schema::hasColumn('hc_procedimientos', 'id_empresa');
    }
}
