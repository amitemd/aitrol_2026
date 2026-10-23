<?php

namespace Sis_medico\Support\Migrations;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class UserForeignKey
{
    private $connection;
    private $parent;

    public function __construct()
    {
        $this->connection = Schema::getConnection();
        $this->parent = $this->column('users', 'id');
        if (!$this->parent || !preg_match('/^(tinyint|smallint|mediumint|int|bigint|char|varchar)$/', $this->parent->data_type)) {
            throw new RuntimeException('No se puede crear la referencia: users.id no existe o tiene un tipo no soportado.');
        }
        if (strtolower($this->engine('users')) !== 'innodb') {
            throw new RuntimeException('users debe usar InnoDB para crear la clave foránea. No se modificó su motor.');
        }
        $indexes = $this->connection->select(
            'SELECT INDEX_NAME FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ? AND SEQ_IN_INDEX = 1 AND SUB_PART IS NULL',
            [$this->connection->getDatabaseName(), $this->physicalTable('users'), 'id']
        );
        if (!$indexes) {
            throw new RuntimeException('users.id necesita un índice completo para crear la referencia. No se modificó users.');
        }
    }

    public function addColumn(Blueprint $table, $nullable = false)
    {
        $types = [
            'tinyint' => 'tinyInteger', 'smallint' => 'smallInteger',
            'mediumint' => 'mediumInteger', 'int' => 'integer', 'bigint' => 'bigInteger',
        ];
        if (isset($types[$this->parent->data_type])) {
            $method = $types[$this->parent->data_type];
            $column = $table->$method('idUsuario')->unsigned($this->isUnsigned($this->parent));
        } else {
            $method = $this->parent->data_type === 'char' ? 'char' : 'string';
            $column = $table->$method('idUsuario', (int) $this->parent->character_length)
                ->charset($this->parent->character_set)->collation($this->parent->collation);
        }

        return $column->nullable($nullable);
    }

    public function ensure($tableName)
    {
        $foreignKeys = $this->connection->select(
            'SELECT CONSTRAINT_NAME, COLUMN_NAME, REFERENCED_TABLE_SCHEMA, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND REFERENCED_TABLE_NAME IS NOT NULL',
            [$this->connection->getDatabaseName(), $this->physicalTable($tableName)]
        );
        $constraints = [];
        foreach ($foreignKeys as $key) {
            $constraints[$key->CONSTRAINT_NAME][] = $key;
        }
        foreach ($constraints as $keys) {
            foreach ($keys as $key) {
                if (strtolower($key->COLUMN_NAME) !== 'idusuario') {
                    continue;
                }
                if (count($keys) === 1 && $key->REFERENCED_TABLE_SCHEMA === $this->connection->getDatabaseName()
                    && $key->REFERENCED_TABLE_NAME === $this->physicalTable('users')
                    && strtolower($key->REFERENCED_COLUMN_NAME) === 'id') {
                    return;
                }
                throw new RuntimeException($tableName . '.idUsuario ya tiene otra clave foránea; se conservó sin cambios.');
            }
        }

        $child = $this->column($tableName, 'idUsuario');
        $incompatible = $child && !$this->compatible($child);
        $changeEngine = strtolower($this->engine($tableName)) !== 'innodb';
        if (($incompatible || $changeEngine) && $this->connection->table($tableName)->exists()) {
            throw new RuntimeException($tableName . ' contiene datos y su tipo de idUsuario o motor es incompatible con users.id. No se convirtió ni eliminó ningún registro; requiere revisar la relación.');
        }
        if ($incompatible && ($child->column_default !== null || $child->extra !== '')) {
            throw new RuntimeException($tableName . '.idUsuario tiene una definición personalizada; se conservó sin cambios.');
        }

        $wrappedTable = $this->connection->getQueryGrammar()->wrapTable($tableName);
        if ($incompatible) {
            // Solo se adapta una tabla vacía que pudo quedar tras el fallo del ALTER.
            $definition = $this->parent->column_type;
            if (!preg_match('/^(?:(?:tinyint|smallint|mediumint|int|bigint)(?:\(\d+\))?(?: unsigned)?(?: zerofill)?|(?:char|varchar)\(\d+\))$/', $definition)) {
                throw new RuntimeException('El tipo de users.id necesita revisión antes de convertir la columna vacía.');
            }
            if ($this->parent->character_set !== null) {
                $definition .= ' CHARACTER SET ' . $this->identifier($this->parent->character_set)
                    . ' COLLATE ' . $this->identifier($this->parent->collation);
            }
            $definition .= $child->is_nullable === 'YES' ? ' NULL' : ' NOT NULL';
            $definition .= ' COMMENT ' . $this->connection->getPdo()->quote($child->comment);
            $this->connection->statement('ALTER TABLE ' . $wrappedTable . ' MODIFY `idUsuario` ' . $definition);
        } elseif (!$child) {
            $nullable = $this->connection->table($tableName)->exists();
            Schema::table($tableName, function (Blueprint $table) use ($nullable) {
                $this->addColumn($table, $nullable);
            });
        }
        if ($changeEngine) {
            $this->connection->statement('ALTER TABLE ' . $wrappedTable . ' ENGINE = InnoDB');
        }

        $wrappedUsers = $this->connection->getQueryGrammar()->wrapTable('users');
        $orphans = $this->connection->select(
            'SELECT 1 FROM ' . $wrappedTable . ' AS child LEFT JOIN ' . $wrappedUsers
            . ' AS parent ON child.`idUsuario` = parent.`id` WHERE child.`idUsuario` IS NOT NULL AND parent.`id` IS NULL LIMIT 1'
        );
        if ($orphans) {
            throw new RuntimeException($tableName . ' contiene referencias a usuarios inexistentes. No se borraron ni reasignaron registros; la clave foránea queda pendiente.');
        }
        Schema::table($tableName, function (Blueprint $table) {
            $table->foreign('idUsuario')->references('id')->on('users')->onDelete('cascade');
        });
    }

    private function column($table, $column)
    {
        $columns = $this->connection->select(
            'SELECT DATA_TYPE AS data_type, COLUMN_TYPE AS column_type, CHARACTER_MAXIMUM_LENGTH AS character_length, CHARACTER_SET_NAME AS character_set, COLLATION_NAME AS collation, IS_NULLABLE AS is_nullable, COLUMN_DEFAULT AS column_default, EXTRA AS extra, COLUMN_COMMENT AS comment FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ?',
            [$this->connection->getDatabaseName(), $this->physicalTable($table), $column]
        );

        return $columns ? $columns[0] : null;
    }

    private function engine($table)
    {
        $tables = $this->connection->select(
            'SELECT ENGINE AS engine FROM information_schema.TABLES WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?',
            [$this->connection->getDatabaseName(), $this->physicalTable($table)]
        );

        return $tables ? $tables[0]->engine : '';
    }

    private function compatible($child)
    {
        return $child->data_type === $this->parent->data_type
            && $child->character_set === $this->parent->character_set
            && $child->collation === $this->parent->collation
            && $this->isUnsigned($child) === $this->isUnsigned($this->parent);
    }

    private function isUnsigned($column)
    {
        return strpos($column->column_type, 'unsigned') !== false;
    }

    private function physicalTable($table)
    {
        return $this->connection->getTablePrefix() . $table;
    }

    private function identifier($name)
    {
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $name)) {
            throw new RuntimeException('Nombre de juego de caracteres o collation no válido.');
        }

        return $name;
    }
}
