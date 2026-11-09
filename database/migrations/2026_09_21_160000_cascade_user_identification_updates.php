<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CascadeUserIdentificationUpdates extends Migration
{
    public function up()
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return;
        }
        $keys = DB::select("SELECT k.TABLE_NAME, k.CONSTRAINT_NAME, k.COLUMN_NAME, k.REFERENCED_COLUMN_NAME, r.DELETE_RULE
            FROM information_schema.KEY_COLUMN_USAGE k
            JOIN information_schema.REFERENTIAL_CONSTRAINTS r
              ON r.CONSTRAINT_SCHEMA=k.CONSTRAINT_SCHEMA AND r.TABLE_NAME=k.TABLE_NAME AND r.CONSTRAINT_NAME=k.CONSTRAINT_NAME
            WHERE k.CONSTRAINT_SCHEMA=DATABASE() AND k.REFERENCED_TABLE_SCHEMA=DATABASE()
              AND k.REFERENCED_TABLE_NAME='users' AND r.UPDATE_RULE <> 'CASCADE'
            ORDER BY k.TABLE_NAME, k.CONSTRAINT_NAME, k.ORDINAL_POSITION");
        $groups = $usedNames = [];
        foreach ($keys as $key) {
            $groups[$key->TABLE_NAME.'.'.$key->CONSTRAINT_NAME][] = $key;
        }
        foreach (DB::select('SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA=DATABASE()') as $constraint) {
            $usedNames[strtolower($constraint->CONSTRAINT_NAME)] = true;
        }
        $quote = function ($name) {
            return '`'.str_replace('`', '``', $name).'`';
        };
        foreach ($groups as $columns) {
            $key = $columns[0];
            $attempt = 0;
            // Nombre nuevo: evita el conflicto de DROP/ADD del mismo nombre en MariaDB.
            do {
                $name = 'fk_users_cascade_'.substr(hash('sha256', $key->TABLE_NAME.'.'.$key->CONSTRAINT_NAME.':'.$attempt++), 0, 32);
            } while (isset($usedNames[strtolower($name)]));
            $usedNames[strtolower($name)] = true;
            $local = $remote = [];
            foreach ($columns as $column) {
                $local[] = $quote($column->COLUMN_NAME);
                $remote[] = $quote($column->REFERENCED_COLUMN_NAME);
            }
            // Mantener ON DELETE y las verificaciones de FK; cambiar solo ON UPDATE.
            DB::statement('ALTER TABLE '.$quote($key->TABLE_NAME)
                .' DROP FOREIGN KEY '.$quote($key->CONSTRAINT_NAME)
                .', ADD CONSTRAINT '.$quote($name).' FOREIGN KEY ('.implode(',', $local).')'
                .' REFERENCES `users` ('.implode(',', $remote).') ON UPDATE CASCADE ON DELETE '.$key->DELETE_RULE);
        }
    }

    public function down()
    {
        throw new RuntimeException('Las reglas de actualización de usuarios requieren una reversión explícita; se conservan los datos y las claves foráneas.');
    }
}
