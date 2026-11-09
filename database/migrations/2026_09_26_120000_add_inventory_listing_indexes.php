<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInventoryListingIndexes extends Migration
{
    private const INDEXES = [
        ['ct_inventario', ['id_empresa', 'producto_id'], 'aitrol_inv_empresa_producto_idx'],
        ['ct_inv_interno', ['id_empresa', 'estado', 'id_producto'], 'aitrol_interno_empresa_estado_producto_idx'],
    ];

    public function up()
    {
        foreach (self::INDEXES as [$table, $columns, $name]) {
            if (! Schema::hasTable($table) || ! Schema::hasColumns($table, $columns)) {
                continue;
            }
            $covered = false;
            foreach (Schema::getIndexes($table) as $index) {
                if (array_slice(array_map('strtolower', $index['columns']), 0, count($columns)) === $columns) {
                    $covered = true;
                    break;
                }
            }
            if (! $covered && ! Schema::hasIndex($table, $name)) {
                Schema::table($table, function (Blueprint $blueprint) use ($columns, $name) {
                    $blueprint->index($columns, $name);
                });
            }
        }
    }

    public function down()
    {
        foreach (self::INDEXES as [$table, $columns, $name]) {
            if (Schema::hasTable($table) && Schema::hasIndex($table, $name)) {
                Schema::table($table, function (Blueprint $blueprint) use ($name) {
                    $blueprint->dropIndex($name);
                });
            }
        }
    }
}
