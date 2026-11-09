<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddCompanyInternalIdentity extends Migration
{
    public function up()
    {
        if (! Schema::hasColumn('empresa', 'id_interno')) {
            DB::statement('ALTER TABLE `empresa` ADD `id_interno` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT, ADD UNIQUE KEY `empresa_id_interno_unique` (`id_interno`)');
        }

        // Keep the existing RUC key and delete rules; corrections must follow
        // the same company in every declared relationship. Never disable FKs.
        $keys = DB::select("SELECT k.TABLE_NAME, k.CONSTRAINT_NAME, k.COLUMN_NAME, k.REFERENCED_COLUMN_NAME, r.DELETE_RULE
            FROM information_schema.KEY_COLUMN_USAGE k
            JOIN information_schema.REFERENTIAL_CONSTRAINTS r
              ON r.CONSTRAINT_SCHEMA = k.CONSTRAINT_SCHEMA AND r.TABLE_NAME = k.TABLE_NAME AND r.CONSTRAINT_NAME = k.CONSTRAINT_NAME
            WHERE k.CONSTRAINT_SCHEMA = DATABASE() AND k.REFERENCED_TABLE_SCHEMA = DATABASE()
              AND k.REFERENCED_TABLE_NAME = 'empresa' AND r.UPDATE_RULE <> 'CASCADE'
            ORDER BY k.TABLE_NAME, k.CONSTRAINT_NAME, k.ORDINAL_POSITION");
        $groups = [];
        foreach ($keys as $key) {
            $groups[$key->TABLE_NAME.'.'.$key->CONSTRAINT_NAME][] = $key;
        }
        $usedNames = [];
        foreach (DB::select('SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = DATABASE()') as $constraint) {
            $usedNames[strtolower($constraint->CONSTRAINT_NAME)] = true;
        }
        $quote = function ($name) {
            return '`'.str_replace('`', '``', $name).'`';
        };
        foreach ($groups as $columns) {
            $key = $columns[0];
            // MariaDB can report errno 121 when DROP and ADD reuse the same
            // FK name in one ALTER (MDEV-30416). Use a free schema-wide name,
            // keeping replacement in one statement and FK checks enabled.
            $attempt = 0;
            do {
                $replacementName = 'fk_empresa_cascade_'.substr(hash('sha256',
                    $key->TABLE_NAME.'.'.$key->CONSTRAINT_NAME.':'.$attempt++), 0, 32);
            } while (isset($usedNames[strtolower($replacementName)]));
            $usedNames[strtolower($replacementName)] = true;
            $local = $remote = [];
            foreach ($columns as $column) {
                $local[] = $quote($column->COLUMN_NAME);
                $remote[] = $quote($column->REFERENCED_COLUMN_NAME);
            }
            DB::statement('ALTER TABLE '.$quote($key->TABLE_NAME)
                .' DROP FOREIGN KEY '.$quote($key->CONSTRAINT_NAME)
                .', ADD CONSTRAINT '.$quote($replacementName)
                .' FOREIGN KEY ('.implode(',', $local).') REFERENCES `empresa` ('.implode(',', $remote).')'
                .' ON UPDATE CASCADE ON DELETE '.$key->DELETE_RULE);
        }
    }

    public function down()
    {
        // Forward-only: stable identifiers must not be reassigned on rollback.
        throw new RuntimeException('La identidad permanente de empresas no se revierte automaticamente.');
    }
}
