<?php

namespace Sis_medico\Support\Migrations;

use Illuminate\Support\Facades\Schema;

trait ChecksExistingIndexes
{
    private function hasIndex($table, $index)
    {
        $connection = Schema::getConnection();
        $table = $connection->getQueryGrammar()->wrapTable($table);

        return count($connection->select('SHOW INDEX FROM ' . $table . ' WHERE Key_name = ?', [$index])) > 0;
    }
}
