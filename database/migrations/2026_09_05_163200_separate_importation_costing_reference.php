<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('log_importaciones')) {
            return;
        }
        // Validate before DDL: role 2 historically stored a costing ID in the invoice FK.
        $invalid = DB::table('log_importaciones as l')
            ->leftJoin('ct_importaciones_compras as c', 'c.id', '=', 'l.id_compra')
            ->where('l.principal', 2)->whereNotNull('l.id_compra')->whereNull('c.id')->exists();
        if ($invalid) {
            throw new RuntimeException('Importaciones: existen referencias de costeo sin documento; revisar antes de migrar.');
        }
        if (! Schema::hasColumn('log_importaciones', 'id_compra_costeo')) {
            Schema::table('log_importaciones', function (Blueprint $table) {
                $table->integer('id_compra_costeo')->nullable();
                $table->foreign('id_compra_costeo', 'importation_costing_document_fk')
                    ->references('id')->on('ct_importaciones_compras');
            });
        }
        DB::transaction(function () {
            DB::table('log_importaciones')->where('principal', 2)->whereNotNull('id_compra')
                ->whereNull('id_compra_costeo')->update(['id_compra_costeo' => DB::raw('id_compra'), 'id_compra' => null]);
        });
    }

    public function down(): void
    {
        // Preserve audit links: costing IDs must never be restored to the invoice FK.
    }
};
