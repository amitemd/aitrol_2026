<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        if (! class_exists('EnforcePersonSyncAtDatabaseLevel')) {
            require_once database_path(
                'migrations/2026_09_11_170000_enforce_person_sync_at_database_level.php'
            );
        }

        (new EnforcePersonSyncAtDatabaseLevel)->up();
    }

    public function down(): void
    {
        // La reversión pertenece a la migración que creó los disparadores.
    }
};
