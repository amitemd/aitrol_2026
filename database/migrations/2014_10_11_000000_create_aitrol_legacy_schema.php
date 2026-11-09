<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Sis_medico\Services\LegacySchemaSqlNormalizer;
use Sis_medico\Services\PurchaseSupportCatalogService;

class CreateAitrolLegacySchema extends Migration
{
    /**
     * Reproduce the complete legacy database structure without operational data.
     * Existing tables are preserved so this migration is also safe on upgrades.
     *
     * @return void
     */
    public function up()
    {
        $path = database_path('schema/aitrol_legacy_structure.sql');
        if (! is_file($path)) {
            throw new RuntimeException('No se encontró el esquema base: '.$path);
        }

        $sql = file_get_contents($path);
        if ($sql === false) {
            throw new RuntimeException('No se pudo leer el esquema base: '.$path);
        }

        preg_match_all('/CREATE TABLE `([^`]+)`\s*\([\s\S]*?\) ENGINE=.*?;/i', $sql, $matches, PREG_SET_ORDER);
        if (count($matches) < 800) {
            throw new RuntimeException('El esquema base está incompleto; se esperaban al menos 800 tablas.');
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('SET UNIQUE_CHECKS=0');

        try {
            foreach ($matches as $match) {
                $table = $match[1];
                if ($table === 'migrations' || Schema::hasTable($table)) {
                    continue;
                }

                try {
                    DB::unprepared(LegacySchemaSqlNormalizer::normalize($match[0]));
                } catch (Exception $exception) {
                    throw new RuntimeException(
                        'No se pudo crear la tabla '.$table.' en '.$this->databaseVersion().': '.$exception->getMessage(),
                        0,
                        $exception
                    );
                }
            }
        } finally {
            DB::statement('SET UNIQUE_CHECKS=1');
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }

        $this->seedMasterDocumentTypes();
        app(PurchaseSupportCatalogService::class)->ensure();
    }

    /**
     * The baseline is intentionally irreversible to protect legacy information.
     * Use a disposable empty database for migrate:fresh installations.
     *
     * @return void
     */
    public function down()
    {
        // No se eliminan cientos de tablas ni datos por medio de rollback.
    }

    /**
     * Include the database engine in failures without exposing credentials.
     *
     * @return string
     */
    private function databaseVersion()
    {
        try {
            $row = DB::selectOne('SELECT VERSION() AS version');

            return 'MySQL/MariaDB '.(isset($row->version) ? $row->version : 'desconocido');
        } catch (Exception $exception) {
            return 'MySQL/MariaDB de versión desconocida';
        }
    }

    /**
     * Carga el catalogo base usado por Compras V2 y V3 en instalaciones nuevas.
     *
     * @return void
     */
    private function seedMasterDocumentTypes()
    {
        if (! Schema::hasTable('ct_master_tipos')) {
            return;
        }

        $tipos = [
            '01' => 'Factura',
            '03' => 'Liquidación de compra de bienes o prestación de servicios',
            '04' => 'Nota de crédito',
            '05' => 'Nota de débito',
            '06' => 'Guía de remisión',
            '07' => 'Comprobante de retención',
        ];

        foreach ($tipos as $codigo => $nombre) {
            $existe = DB::table('ct_master_tipos')
                ->where('codigo', $codigo)
                ->where('tipo', 1)
                ->exists();

            if (! $existe) {
                DB::table('ct_master_tipos')->insert([
                    'codigo' => $codigo,
                    'nombre' => $nombre,
                    'tipo' => 1,
                    'estado' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }
    }
}
