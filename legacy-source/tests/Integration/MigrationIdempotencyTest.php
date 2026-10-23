<?php

namespace Tests\Integration;

use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Migrations\DatabaseMigrationRepository;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Schema;
use PDO;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

require_once __DIR__ . '/../../app/Support/Migrations/ChecksExistingIndexes.php';
require_once __DIR__ . '/../../app/Support/Migrations/UserForeignKey.php';
require_once __DIR__ . '/../../database/migrations/2015_10_25_000000_update_users_table.php';
require_once __DIR__ . '/../../database/migrations/2016_03_13_151018_crear_tabla_educacion.php';
require_once __DIR__ . '/../../database/migrations/2016_03_20_230021_crear_tabla_publicaciones.php';
require_once __DIR__ . '/../../database/migrations/2016_03_25_161404_crear_tabla_proyectos.php';
require_once __DIR__ . '/../../database/migrations/2016_03_26_211538_crear_tabla_pais.php';
require_once __DIR__ . '/../../database/migrations/2016_07_07_225615_update_table_users_V2.php';
require_once __DIR__ . '/../../database/migrations/2017_08_22_160719_update_table_users_V3.php';
require_once __DIR__ . '/../../database/migrations/2017_08_31_143819_create_pais_table.php';
foreach (glob(__DIR__ . '/../../database/migrations/2026_*.php') as $migrationFile) {
    require_once $migrationFile;
}

class MigrationIdempotencyTest extends TestCase
{
    private $admin;
    private $database;
    private $capsule;
    private $previousApplication;
    private $fixtureRoot;

    protected function setUp()
    {
        if (!getenv('MIGRATION_TEST_MYSQL_PORT')) {
            $this->markTestSkipped('Requiere un servidor MySQL de pruebas con MIGRATION_TEST_MYSQL_PORT.');
        }

        $config = [
            'driver' => 'mysql',
            'host' => getenv('MIGRATION_TEST_MYSQL_HOST') ?: '127.0.0.1',
            'port' => getenv('MIGRATION_TEST_MYSQL_PORT'),
            'username' => getenv('MIGRATION_TEST_MYSQL_USER') ?: 'root',
            'password' => getenv('MIGRATION_TEST_MYSQL_PASSWORD') ?: '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
        ];
        $this->admin = new PDO(
            'mysql:host=' . $config['host'] . ';port=' . $config['port'],
            $config['username'],
            $config['password'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        $this->database = 'migration_test_' . bin2hex(random_bytes(8));
        $this->admin->exec('CREATE DATABASE `' . $this->database . '`');
        $config['database'] = $this->database;

        $this->previousApplication = Facade::getFacadeApplication();
        $this->capsule = new Manager();
        $this->capsule->addConnection($config);
        $container = $this->capsule->getContainer();
        $container->instance('db', $this->capsule->getDatabaseManager());
        $container->bind('db.schema', function () {
            return $this->capsule->getConnection()->getSchemaBuilder();
        });
        Facade::clearResolvedInstances();
        Facade::setFacadeApplication($container);
    }

    protected function tearDown()
    {
        if ($this->fixtureRoot) {
            foreach (glob($this->fixtureRoot . '/database/migrations/*.php') as $file) {
                unlink($file);
            }
            rmdir($this->fixtureRoot . '/database/migrations');
            rmdir($this->fixtureRoot . '/database');
            rmdir($this->fixtureRoot);
        }
        if ($this->capsule) {
            $this->capsule->getConnection()->disconnect();
            Facade::clearResolvedInstances();
            Facade::setFacadeApplication($this->previousApplication);
        }
        if ($this->admin && $this->database) {
            $this->admin->exec('DROP DATABASE `' . $this->database . '`');
        }
        $this->admin = null;
        $this->capsule = null;
    }

    public function testFreshInstallAndRetryPreserveSchemaAndData()
    {
        $this->runMigrations();
        DB::table('gateway_tickets')->insert([
            'external_id' => 'ticket-existing', 'user_id' => 'user-existing',
            'origin_url' => 'https://example.test', 'payload' => '{"preservar":true}',
        ]);
        DB::table('amortizacion_prestamos')->insert([
            'id_empresa' => '0991234567001', 'institucion' => 'Institución existente',
            'fecha_desembolso' => '2026-09-01', 'fecha_primer_pago' => '2026-10-01',
            'monto' => 1000, 'tasa_anual' => 10, 'plazo_meses' => 12, 'metodo' => 'frances',
            'cuenta_banco' => '1.01.01.02.01', 'cuenta_deuda' => '2.01.01',
            'cuenta_interes' => '5.01.01', 'cuenta_seguro' => null,
            'id_asiento_anulacion' => 345,
        ]);
        $schema = $this->schemaSnapshot();
        $ticket = DB::table('gateway_tickets')->first();
        $loan = DB::table('amortizacion_prestamos')->first();

        $this->runMigrations();
        $this->runMigrations();

        $this->assertSame($schema, $this->schemaSnapshot());
        $this->assertEquals($ticket, DB::table('gateway_tickets')->first());
        $this->assertEquals($loan, DB::table('amortizacion_prestamos')->first());
    }

    public function testIncompleteTableCreationStillCreatesMissingTables()
    {
        (new \CreateEntregasMercaderiaTables())->up();
        Schema::drop('ct_entregas_mercaderia_trazabilidad');
        Schema::drop('ct_entregas_mercaderia_detalle');
        Schema::table('ct_entregas_mercaderia', function (Blueprint $table) {
            $table->string('custom_existing_column')->nullable();
        });

        (new \CreateEntregasMercaderiaTables())->up();

        $this->assertTrue(Schema::hasTable('ct_entregas_mercaderia_detalle'));
        $this->assertTrue(Schema::hasTable('ct_entregas_mercaderia_trazabilidad'));
        $this->assertTrue(Schema::hasColumn('ct_entregas_mercaderia', 'custom_existing_column'));
    }

    public function testPartialColumnsAndMissingIndexesAreCompleted()
    {
        (new \CreateEntregasMercaderiaTables())->up();
        Schema::table('ct_entregas_mercaderia', function (Blueprint $table) {
            $table->text('motivo_anulacion')->nullable();
            $table->string('idempotency_key', 64)->nullable();
            $table->unsignedInteger('id_bodega_inventario')->nullable();
        });
        Schema::table('ct_entregas_mercaderia_detalle', function (Blueprint $table) {
            $table->string('serie', 300)->nullable();
            $table->integer('id_bodega_contable')->nullable();
        });
        $this->runMigrations();

        $this->assertTrue(Schema::hasColumn('ct_entregas_mercaderia', 'fecha_anulacion'));
        $this->assertTrue(Schema::hasColumn('ct_entregas_mercaderia_detalle', 'id_inv_inventario_serie'));
        $this->assertTrue(Schema::hasColumn('ct_entregas_mercaderia_detalle', 'lote'));
        $column = DB::select("SHOW COLUMNS FROM ct_entregas_mercaderia_detalle WHERE Field = 'serie'");
        $this->assertSame('varchar(300)', $column[0]->Type);
        foreach ([
            ['ct_entregas_mercaderia', 'uq_entrega_idempotency_key'],
            ['ct_entregas_mercaderia', 'idx_entrega_bodega_inv'],
            ['ct_entregas_mercaderia_detalle', 'idx_entrega_det_inv_serie'],
            ['ct_entregas_mercaderia_detalle', 'idx_entrega_det_bodega'],
        ] as $index) {
            $this->assertCount(1, DB::select('SHOW INDEX FROM `' . $index[0] . '` WHERE Key_name = ?', [$index[1]]));
        }
        $this->runMigrations();
    }

    public function testExistingReversalColumnAndLegacyMissingColumnAreBothSupported()
    {
        (new \CreateAmortizacionPrestamosTables())->up();
        (new \AddReversalEntryToAmortizacion())->up();
        Schema::table('amortizacion_prestamos', function (Blueprint $table) {
            $table->dropColumn('id_asiento_anulacion');
        });

        (new \AddReversalEntryToAmortizacion())->up();
        (new \AddReversalEntryToAmortizacion())->up();

        $this->assertTrue(Schema::hasColumn('amortizacion_prestamos', 'id_asiento_anulacion'));
    }

    public function testIndexChecksRespectTablePrefixes()
    {
        $this->capsule->getConnection()->setTablePrefix('test_');
        $this->runMigrations('2026_08_*.php');
        $this->runMigrations('2026_08_*.php');

        $this->assertTrue(Schema::hasColumn('ct_entregas_mercaderia', 'id_bodega_inventario'));
        $this->assertCount(1, DB::select("SHOW INDEX FROM test_ct_entregas_mercaderia WHERE Key_name = 'idx_entrega_bodega_inv'"));
    }

    /**
     * @dataProvider historicalUserColumns
     */
    public function testHistoricalUserMigrationsOnlyAddMissingFields(array $existing)
    {
        Schema::create('users', function (Blueprint $table) use ($existing) {
            $table->increments('id');
            foreach ($existing as $column => $value) {
                $table->string($column, 150)->nullable();
            }
        });
        DB::table('users')->insert([
            array_merge(['id' => 1], $existing),
            array_merge(['id' => 2], $existing),
        ]);
        $definitions = DB::select('SHOW COLUMNS FROM users');
        $migrations = [new \UpdateUsersTable(), new \UpdateTableUsersV2(), new \UpdateTableUsersV3()];

        foreach ($migrations as $migration) {
            $migration->up();
        }

        foreach (['nombres', 'apellidos', 'pais', 'ciudad', 'institucion', 'ocupacion', 'tipoUsuario', 'cedula', 'telefono3', 'telefono2', 'fechanacimiento', 'estado'] as $column) {
            $this->assertTrue(Schema::hasColumn('users', $column), $column);
        }
        foreach ($definitions as $definition) {
            $actual = DB::select('SHOW COLUMNS FROM users WHERE Field = ?', [$definition->Field]);
            $this->assertEquals($definition, $actual[0]);
        }
        $rows = DB::table('users')->orderBy('id')->get()->toArray();
        foreach ($rows as $row) {
            foreach ($existing as $column => $value) {
                $this->assertSame($value, $row->$column, $column);
            }
            if (!array_key_exists('cedula', $existing)) {
                $this->assertNull($row->cedula);
            }
        }
        $schema = $this->schemaSnapshot();
        $connection = DB::connection();
        $connection->enableQueryLog();
        $connection->flushQueryLog();
        foreach ($migrations as $migration) {
            $migration->up();
            $migration->down();
        }
        foreach ($connection->getQueryLog() as $query) {
            $this->assertSame(0, preg_match('/^\s*(alter|create|drop|update|delete|insert)\b/i', $query['query']));
        }
        $connection->disableQueryLog();
        $this->assertSame($schema, $this->schemaSnapshot());
        $this->assertEquals($rows, DB::table('users')->orderBy('id')->get()->toArray());
    }

    public function historicalUserColumns()
    {
        return [
            'ciudad existente, error reportado' => [['ciudad' => 'Guayaquil']],
            'varios campos existentes' => [['ciudad' => 'Quito', 'ocupacion' => 'Doctora', 'estado' => 'ACTIVO', 'telefono2' => '0999999999']],
            'todos existentes, incluyendo identificaciones repetidas' => [[
                'nombres' => 'Ana', 'apellidos' => 'Pérez', 'pais' => 'EC', 'ciudad' => 'Cuenca',
                'institucion' => 'Institución existente', 'ocupacion' => 'Doctora', 'tipoUsuario' => 'Médico',
                'cedula' => '1234567890', 'telefono3' => '0999999999', 'telefono2' => '0888888888',
                'fechanacimiento' => '1980-01-01', 'estado' => 'ACTIVO',
            ]],
            'faltan todos los campos con usuarios existentes' => [[]],
        ];
    }

    public function testStandardMigrateRunsHistoricalAndCurrentMigrationsTogether()
    {
        $this->createCommandFixture();
        Schema::create('users', function (Blueprint $table) {
            $table->string('id', 13)->primary();
            $table->string('ciudad', 150)->nullable();
            $table->string('ocupacion', 150)->nullable();
        });
        DB::table('users')->insert(['id' => '0123456789', 'ciudad' => 'Guayaquil', 'ocupacion' => 'Doctora']);
        Schema::create('pais', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre', 20);
            $table->timestamps();
        });
        DB::table('pais')->insert(['id' => 593, 'nombre' => 'Ecuador']);
        $countryDefinitions = DB::select('SHOW FULL COLUMNS FROM pais');
        $repository = new DatabaseMigrationRepository($this->capsule->getDatabaseManager(), 'migrations');
        $repository->createRepository();
        $repository->log('2014_10_12_000000_create_users_table', 7);
        $application = new Application($this->fixtureRoot);
        $application->instance('env', 'production');
        $migrator = new Migrator($repository, $this->capsule->getDatabaseManager(), new Filesystem());
        $command = new \Illuminate\Database\Console\Migrations\MigrateCommand($migrator);
        $command->setLaravel($application);
        $tester = new CommandTester($command);

        $this->assertSame(0, $tester->execute(['--force' => true]));
        $this->assertSame(21, DB::table('migrations')->count());
        $this->assertSame(1, DB::table('migrations')->where('migration', '2015_10_25_000000_update_users_table')->count());
        $user = DB::table('users')->first();
        $this->assertSame('0123456789', $user->id);
        $this->assertSame('Guayaquil', $user->ciudad);
        $this->assertSame('Doctora', $user->ocupacion);
        $country = DB::table('pais')->first();
        $this->assertSame(593, $country->id);
        $this->assertSame('Ecuador', $country->nombre);
        foreach ($countryDefinitions as $definition) {
            $actual = DB::select('SHOW FULL COLUMNS FROM pais WHERE Field = ?', [$definition->Field]);
            $this->assertEquals($definition, $actual[0]);
        }
        foreach (['ip_crea', 'ip_modifica', 'deleted_at'] as $column) {
            $this->assertTrue(Schema::hasColumn('pais', $column), $column);
            $this->assertNull($country->$column);
        }

        $this->assertSame(0, $tester->execute(['--force' => true]));
        $this->assertSame(21, DB::table('migrations')->count());
        $this->assertEquals($user, DB::table('users')->first());
        $this->assertEquals($country, DB::table('pais')->first());
    }

    /**
     * @dataProvider countryMigrations
     */
    public function testCountryMigrationCreatesMissingTableAndPreservesItOnRetry($class, $nameLength)
    {
        $migration = new $class();
        $migration->up();
        DB::table('pais')->insert(['id' => 593, 'nombre' => 'Ecuador']);
        $name = DB::select("SHOW COLUMNS FROM pais WHERE Field = 'nombre'")[0];
        $this->assertSame('varchar(' . $nameLength . ')', $name->Type);
        $this->assertSame('NO', $name->Null);
        $schema = $this->schemaSnapshot();
        $row = DB::table('pais')->first();

        $migration->up();
        $migration->down();

        $this->assertSame($schema, $this->schemaSnapshot());
        $this->assertEquals($row, DB::table('pais')->first());
    }

    public function countryMigrations()
    {
        return [
            '2016' => ['CrearTablaPais', 20],
            '2017' => ['CreatePaisTable', 60],
        ];
    }

    /**
     * @dataProvider existingCountryColumns
     */
    public function testCountryMigrationsOnlyAddMissingFields(array $existing)
    {
        Schema::create('pais', function (Blueprint $table) use ($existing) {
            // Una base existente puede tener tipos distintos a los originales.
            $table->string('id', 13)->primary();
            foreach ($existing as $column => $value) {
                $table->string($column, 150)->nullable();
            }
            $table->string('codigo_externo', 10);
        });
        $originalRows = [
            array_merge(['id' => '000593', 'codigo_externo' => 'EC'], $existing),
            array_merge(['id' => '000057', 'codigo_externo' => 'CO'], $existing),
        ];
        DB::table('pais')->insert($originalRows);
        $definitions = DB::select('SHOW FULL COLUMNS FROM pais');
        $migrations = [new \CrearTablaPais(), new \CreatePaisTable()];

        foreach ($migrations as $migration) {
            $migration->up();
        }

        foreach (['id', 'nombre', 'created_at', 'updated_at', 'ip_crea', 'ip_modifica', 'deleted_at'] as $column) {
            $this->assertTrue(Schema::hasColumn('pais', $column), $column);
        }
        foreach ($definitions as $definition) {
            $actual = DB::select('SHOW FULL COLUMNS FROM pais WHERE Field = ?', [$definition->Field]);
            $this->assertEquals($definition, $actual[0]);
        }
        $this->assertSame(2, DB::table('pais')->count());
        foreach ($originalRows as $original) {
            $row = DB::table('pais')->where('id', $original['id'])->first();
            foreach ($original as $column => $value) {
                $this->assertSame($value, $row->$column, $column);
            }
            foreach (['nombre', 'created_at', 'updated_at', 'ip_crea', 'ip_modifica', 'deleted_at'] as $column) {
                if (!array_key_exists($column, $original)) {
                    $this->assertNull($row->$column, $column);
                }
            }
        }
        $schema = $this->schemaSnapshot();
        $rows = DB::table('pais')->orderBy('id')->get()->toArray();
        $connection = DB::connection();
        $connection->enableQueryLog();
        $connection->flushQueryLog();
        foreach ($migrations as $migration) {
            $migration->up();
            $migration->down();
        }
        foreach ($connection->getQueryLog() as $query) {
            $this->assertSame(0, preg_match('/^\s*(alter|create|drop|update|delete|insert)\b/i', $query['query']));
        }
        $connection->disableQueryLog();
        $this->assertSame($schema, $this->schemaSnapshot());
        $this->assertEquals($rows, DB::table('pais')->orderBy('id')->get()->toArray());
    }

    public function existingCountryColumns()
    {
        return [
            'nombre existente más largo que el permitido por las migraciones originales' => [['nombre' => str_repeat('País existente ', 5)]],
            'falta updated_at' => [['nombre' => 'Ecuador', 'created_at' => '2016-03-26 12:00:00']],
            'faltan nombre y created_at' => [['updated_at' => '2017-08-31 12:00:00']],
            'todos los campos existen' => [[
                'nombre' => 'Ecuador', 'created_at' => '2016-03-26 12:00:00', 'updated_at' => '2017-08-31 12:00:00',
                'ip_crea' => '2001:db8::1', 'ip_modifica' => '192.0.2.1', 'deleted_at' => '2020-01-01 12:00:00',
            ]],
        ];
    }

    public function testEducationRecoversEmptyTableLeftByForeignKeyError()
    {
        $this->createReferenceUsers('text');
        $this->createEducationWithoutForeignKey('integer');
        $usersSchema = DB::select('SHOW CREATE TABLE users');
        try {
            Schema::table('educacion', function (Blueprint $table) {
                $table->foreign('idUsuario')->references('id')->on('users')->onDelete('cascade');
            });
            $this->fail('La definición original debe reproducir la clave foránea incompatible.');
        } catch (\Illuminate\Database\QueryException $exception) {
            $this->assertSame(1005, $exception->errorInfo[1]);
        }
        $this->assertTrue(Schema::hasTable('educacion'));

        (new \CrearTablaEducacion())->up();

        $parent = DB::select("SHOW FULL COLUMNS FROM users WHERE Field = 'id'")[0];
        $child = DB::select("SHOW FULL COLUMNS FROM educacion WHERE Field = 'idUsuario'")[0];
        $this->assertSame($parent->Type, $child->Type);
        $this->assertSame($parent->Collation, $child->Collation);
        $this->assertEquals($usersSchema, DB::select('SHOW CREATE TABLE users'));
        $this->assertUserReference('educacion');
        DB::table('educacion')->insert($this->educationRow('00123'));
        $this->assertSame('00123', DB::table('educacion')->first()->idUsuario);
        $schema = $this->schemaSnapshot();
        $rows = DB::table('educacion')->get()->toArray();

        (new \CrearTablaEducacion())->up();
        (new \CrearTablaEducacion())->down();

        $this->assertSame($schema, $this->schemaSnapshot());
        $this->assertEquals($rows, DB::table('educacion')->get()->toArray());
    }

    /**
     * @dataProvider referenceIdTypes
     */
    public function testNewLegacyTablesUseActualUserIdType($type)
    {
        $this->createReferenceUsers($type);
        $parent = DB::select("SHOW FULL COLUMNS FROM users WHERE Field = 'id'")[0];
        foreach ([
            'educacion' => new \CrearTablaEducacion(),
            'publicaciones' => new \CrearTablaPublicaciones(),
            'proyectos' => new \CrearTablaProyectos(),
        ] as $table => $migration) {
            $migration->up();
            $migration->up();
            $child = DB::select('SHOW FULL COLUMNS FROM `' . $table . '` WHERE Field = ?', ['idUsuario'])[0];
            $this->assertSame($parent->Type, $child->Type);
            $this->assertSame($parent->Collation, $child->Collation);
            $this->assertUserReference($table);
        }
    }

    public function referenceIdTypes()
    {
        return [['text'], ['char'], ['integer'], ['bigInteger']];
    }

    public function testCompatiblePopulatedEducationKeepsItsDefinitionAndRows()
    {
        $this->createReferenceUsers('text');
        $this->createEducationWithoutForeignKey('text');
        DB::table('educacion')->insert($this->educationRow('00123'));
        $columns = DB::select('SHOW FULL COLUMNS FROM educacion');
        $rows = DB::table('educacion')->get()->toArray();

        (new \CrearTablaEducacion())->up();

        $this->assertEquals($columns, DB::select('SHOW FULL COLUMNS FROM educacion'));
        $this->assertEquals($rows, DB::table('educacion')->get()->toArray());
        $this->assertUserReference('educacion');
        $schema = $this->schemaSnapshot();
        (new \CrearTablaEducacion())->up();
        $this->assertSame($schema, $this->schemaSnapshot());
    }

    public function testIncompatiblePopulatedEducationIsNotConvertedOrErased()
    {
        $this->createReferenceUsers('text');
        $this->createEducationWithoutForeignKey('integer');
        DB::table('educacion')->insert($this->educationRow(123));
        $schema = $this->schemaSnapshot();
        $row = DB::table('educacion')->first();

        try {
            (new \CrearTablaEducacion())->up();
            $this->fail('No debe convertir una columna incompatible que tiene datos.');
        } catch (\RuntimeException $exception) {
            $this->assertContains('contiene datos', $exception->getMessage());
        }
        $this->assertSame($schema, $this->schemaSnapshot());
        $this->assertEquals($row, DB::table('educacion')->first());
    }

    public function testEducationWithOrphanedReferencesIsNotDeletedOrReassigned()
    {
        $this->createReferenceUsers('text');
        $this->createEducationWithoutForeignKey('text');
        DB::table('educacion')->insert($this->educationRow('99999'));
        $schema = $this->schemaSnapshot();

        try {
            (new \CrearTablaEducacion())->up();
            $this->fail('Debe detectar referencias a usuarios inexistentes.');
        } catch (\RuntimeException $exception) {
            $this->assertContains('usuarios inexistentes', $exception->getMessage());
        }
        $this->assertSame($schema, $this->schemaSnapshot());
        $this->assertSame('99999', DB::table('educacion')->first()->idUsuario);
    }

    public function testEducationForeignKeyRespectsTablePrefixes()
    {
        $this->capsule->getConnection()->setTablePrefix('test_');
        $this->createReferenceUsers('text');
        (new \CrearTablaEducacion())->up();
        DB::table('educacion')->insert($this->educationRow('00123'));
        (new \CrearTablaEducacion())->up();

        $this->assertUserReference('educacion');
        $this->assertSame('00123', DB::table('educacion')->first()->idUsuario);
    }

    private function createReferenceUsers($type)
    {
        Schema::create('users', function (Blueprint $table) use ($type) {
            $table->engine = 'InnoDB';
            if ($type === 'text' || $type === 'char') {
                $method = $type === 'char' ? 'char' : 'string';
                $table->$method('id', 13)->charset('latin1')->collation('latin1_swedish_ci')->primary();
            } elseif ($type === 'integer') {
                $table->unsignedInteger('id')->primary();
            } else {
                $table->bigInteger('id')->primary();
            }
        });
        DB::table('users')->insert(['id' => $type === 'text' || $type === 'char' ? '00123' : 123]);
    }

    private function createEducationWithoutForeignKey($type)
    {
        Schema::create('educacion', function (Blueprint $table) use ($type) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            if ($type === 'text') {
                $table->string('idUsuario', 60)->charset('latin1')->collation('latin1_swedish_ci');
            } else {
                $table->unsignedInteger('idUsuario');
            }
            $table->index('idUsuario');
            $table->integer('idTipoeducacion');
            $table->string('titulo', 100);
            $table->string('institucion', 60);
            $table->string('anio', 5);
            $table->timestamps();
        });
    }

    private function educationRow($userId)
    {
        return ['idUsuario' => $userId, 'idTipoeducacion' => 1, 'titulo' => 'Título existente', 'institucion' => 'Institución', 'anio' => '2020'];
    }

    private function assertUserReference($table)
    {
        $prefix = $this->capsule->getConnection()->getTablePrefix();
        $keys = DB::select(
            'SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ? AND REFERENCED_TABLE_NAME = ? AND REFERENCED_COLUMN_NAME = ?',
            [$this->database, $prefix . $table, 'idUsuario', $prefix . 'users', 'id']
        );
        $this->assertCount(1, $keys);
    }

    private function createCommandFixture()
    {
        $this->fixtureRoot = sys_get_temp_dir() . '/migration_command_test_' . bin2hex(random_bytes(8));
        mkdir($this->fixtureRoot . '/database/migrations', 0777, true);
        $files = array_merge(
            glob(__DIR__ . '/../../database/migrations/201*_*.php'),
            glob(__DIR__ . '/../../database/migrations/2026_*.php')
        );
        foreach ($files as $file) {
            // Carga los archivos reales sin redeclarar las clases ya usadas por otras pruebas.
            file_put_contents(
                $this->fixtureRoot . '/database/migrations/' . basename($file),
                '<?php require_once ' . var_export(realpath($file), true) . ';'
            );
        }
    }

    private function runMigrations($pattern = '2026_*.php')
    {
        foreach (glob(__DIR__ . '/../../database/migrations/' . $pattern) as $file) {
            preg_match('/class\s+(\w+)\s+extends\s+Migration/', file_get_contents($file), $match);
            $class = $match[1];
            (new $class())->up();
        }
    }

    private function schemaSnapshot()
    {
        $snapshot = [];
        foreach (DB::select('SHOW TABLES') as $table) {
            $name = current((array) $table);
            $snapshot[$name] = DB::select('SHOW CREATE TABLE `' . $name . '`');
        }

        return json_encode($snapshot);
    }
}
