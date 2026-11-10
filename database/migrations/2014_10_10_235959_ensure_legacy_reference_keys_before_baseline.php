<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Prepare keys required by foreign keys in the legacy baseline.
     */
    public function up(): void
    {
        if (! Schema::hasTable('forma_de_pago')
            || ! Schema::hasColumn('forma_de_pago', 'id')
            || Schema::hasIndex('forma_de_pago', ['id'], 'unique')) {
            return;
        }

        $hasDuplicateIds = DB::table('forma_de_pago')
            ->select('id')
            ->groupBy('id')
            ->havingRaw('COUNT(*) > 1')
            ->exists();

        if ($hasDuplicateIds) {
            throw new RuntimeException(
                'No se puede preparar forma_de_pago.id porque contiene valores duplicados. '
                .'Corrija los duplicados antes de ejecutar las migraciones.'
            );
        }

        Schema::table('forma_de_pago', function (Blueprint $table): void {
            $table->unique('id', 'forma_de_pago_id_unique');
        });
    }

    /**
     * The unique key is required by factura_forma_pago and is not removed.
     */
    public function down(): void
    {
        // Quitarla volveria a invalidar la clave foranea del esquema legado.
    }
};
