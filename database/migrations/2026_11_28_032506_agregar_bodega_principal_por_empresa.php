<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('inv_bodegas', function (Blueprint $table): void {
            $table->boolean('es_principal')->nullable();
            $table->unique(['id_empresa', 'es_principal'], 'inv_bodegas_principal_empresa');
        });

        DB::table('inv_bodegas')->where('activo', true)
            ->select('id_empresa')->selectRaw('min(id) as id')->groupBy('id_empresa')
            ->chunkById(200, function ($bodegas): void {
                DB::table('inv_bodegas')->whereIn('id', $bodegas->pluck('id'))->update(['es_principal' => true]);
            }, 'id_empresa');
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inv_bodegas', function (Blueprint $table): void {
            $table->dropUnique('inv_bodegas_principal_empresa');
            $table->dropColumn('es_principal');
        });
    }
};
