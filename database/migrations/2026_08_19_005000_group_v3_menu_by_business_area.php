<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class GroupV3MenuByBusinessArea extends Migration
{
    private $groupedItems = [
        2 => ['parent' => 563, 'order' => 3],
        6 => ['parent' => 563, 'order' => 4],
        5 => ['parent' => 563, 'order' => 5],
        4 => ['parent' => 563, 'order' => 6],
        3 => ['parent' => 563, 'order' => 7],
        61 => ['parent' => 563, 'order' => 8],
        51 => ['parent' => 563, 'order' => 9],
        66 => ['parent' => 563, 'order' => 10],
        177 => ['parent' => 563, 'order' => 11],
        204 => ['parent' => 563, 'order' => 12],
        9 => ['parent' => 10, 'order' => 11],
        561 => ['parent' => 3, 'order' => 2],
    ];

    private $rootOrder = [563 => 0, 36 => 1, 10 => 2, 530 => 3];

    private $originalItems = [
        2 => ['parent' => null, 'order' => 0],
        6 => ['parent' => null, 'order' => 1],
        5 => ['parent' => null, 'order' => 2],
        4 => ['parent' => null, 'order' => 4],
        3 => ['parent' => null, 'order' => 5],
        61 => ['parent' => null, 'order' => 0],
        51 => ['parent' => null, 'order' => 0],
        66 => ['parent' => null, 'order' => 0],
        177 => ['parent' => null, 'order' => 0],
        204 => ['parent' => null, 'order' => 0],
        9 => ['parent' => null, 'order' => 9],
        561 => ['parent' => 29, 'order' => 0],
    ];

    private $originalRootOrder = [563 => 0, 36 => 8, 10 => 10, 530 => 12];

    public function up()
    {
        $this->applyHierarchy($this->groupedItems, $this->rootOrder);
    }

    public function down()
    {
        $this->applyHierarchy($this->originalItems, $this->originalRootOrder);
    }

    private function applyHierarchy(array $items, array $roots)
    {
        if (! Schema::hasTable('new_db_menu')) {
            return;
        }

        DB::transaction(function () use ($items, $roots) {
            $now = date('Y-m-d H:i:s');

            foreach ($items as $id => $item) {
                DB::table('new_db_menu')
                    ->where('id_menu', $id)
                    ->update([
                        'id_pertenece_menu' => $item['parent'],
                        'orden_menu' => $item['order'],
                        'updated_at_menu' => $now,
                    ]);
            }

            foreach ($roots as $id => $order) {
                DB::table('new_db_menu')
                    ->where('id_menu', $id)
                    ->update([
                        'id_pertenece_menu' => null,
                        'orden_menu' => $order,
                        'updated_at_menu' => $now,
                    ]);
            }
        });
    }
}
