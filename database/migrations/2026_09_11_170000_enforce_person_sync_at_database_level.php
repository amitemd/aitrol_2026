<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EnforcePersonSyncAtDatabaseLevel extends Migration
{
    private $tables = ['paciente', 'users', 'proveedor', 'ct_clientes'];

    public function up()
    {
        if (DB::connection()->getDriverName() !== 'mysql' || ! Schema::hasTable('personas')) {
            return;
        }

        foreach ($this->tables as $table) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'persona_id')) {
                continue;
            }
            foreach (['insert' => 'INSERT', 'update' => 'UPDATE'] as $suffix => $event) {
                $name = 'sync_person_'.$table.'_before_'.$suffix;
                DB::unprepared('DROP TRIGGER IF EXISTS `'.$name.'`');
                DB::unprepared($this->triggerSql($name, $table, $event));
            }
        }
    }

    public function down()
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return;
        }
        foreach ($this->tables as $table) {
            foreach (['insert', 'update'] as $suffix) {
                DB::unprepared('DROP TRIGGER IF EXISTS `sync_person_'.$table.'_before_'.$suffix.'`');
            }
        }
    }

    private function triggerSql($name, $table, $event)
    {
        $definition = $this->definition($table);
        $columns = array_keys($definition);
        $values = array_values($definition);
        $updates = [];
        foreach ($columns as $column) {
            if (in_array($column, ['identificacion', 'tipo_persona', 'created_at'], true)) {
                continue;
            }
            $updates[] = '`'.$column.'` = COALESCE(NULLIF(VALUES(`'.$column.'`), \'\'), `'.$column.'`)';
        }
        $updates[] = '`id` = LAST_INSERT_ID(`id`)';
        $identifier = $values[array_search('identificacion', $columns)];

        return 'CREATE TRIGGER `'.$name.'` BEFORE '.$event.' ON `'.$table.'` FOR EACH ROW
            BEGIN
                IF '.$identifier.' IS NOT NULL AND '.$identifier.' <> \'\' THEN
                    INSERT INTO `personas` (`'.implode('`, `', $columns).'`)
                    VALUES ('.implode(', ', $values).')
                    ON DUPLICATE KEY UPDATE '.implode(', ', $updates).';
                    SET NEW.`persona_id` = LAST_INSERT_ID();
                END IF;
            END';
    }

    private function definition($table)
    {
        $idColumn = $table === 'ct_clientes' ? 'identificacion' : 'id';
        $identification = "UPPER(REGEXP_REPLACE(TRIM(NEW.`{$idColumn}`), '[^A-Za-z0-9]', ''))";
        $common = [
            'identificacion' => $identification,
            'tipo_documento' => in_array($table, ['paciente', 'users'], true) ? 'NEW.`tipo_documento`' : 'NULL',
            'tipo_persona' => in_array($table, ['proveedor', 'ct_clientes'], true) ? "'JURIDICA'" : "'NATURAL'",
            'nombre1' => in_array($table, ['paciente', 'users'], true) ? 'NEW.`nombre1`' : 'NULL',
            'nombre2' => in_array($table, ['paciente', 'users'], true) ? 'NEW.`nombre2`' : 'NULL',
            'apellido1' => in_array($table, ['paciente', 'users'], true) ? 'NEW.`apellido1`' : 'NULL',
            'apellido2' => in_array($table, ['paciente', 'users'], true) ? 'NEW.`apellido2`' : 'NULL',
            'razon_social' => $table === 'proveedor' ? 'NEW.`razonsocial`' : ($table === 'ct_clientes' ? 'NEW.`nombre`' : 'NULL'),
            'nombre_comercial' => $table === 'proveedor' ? 'NEW.`nombrecomercial`' : 'NULL',
            'fecha_nacimiento' => in_array($table, ['paciente', 'users'], true) ? 'NEW.`fecha_nacimiento`' : 'NULL',
            'sexo' => $table === 'paciente' ? 'NEW.`sexo`' : 'NULL',
            'email' => $table === 'paciente' ? 'NEW.`mail_opcional`' : (in_array($table, ['users', 'proveedor'], true) ? 'NEW.`email`' : 'NULL'),
            'telefono1' => $table === 'ct_clientes' ? 'NEW.`telefono1_representante`' : 'NEW.`telefono1`',
            'telefono2' => $table === 'ct_clientes' ? 'NEW.`telefono2_representante`' : 'NEW.`telefono2`',
            'direccion' => 'NEW.`direccion`',
            'ip_modificacion' => "'DATABASE_TRIGGER'",
            'created_at' => 'CURRENT_TIMESTAMP',
            'updated_at' => 'CURRENT_TIMESTAMP',
        ];

        return $common;
    }
}
