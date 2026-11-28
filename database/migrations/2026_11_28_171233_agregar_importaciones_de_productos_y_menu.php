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
        Schema::create('inv_importaciones', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignId('id_empresa')->constrained('erp_empresas')->restrictOnDelete();
            $table->foreignId('id_usuario')->nullable()->constrained('users')->nullOnDelete();
            $table->string('archivo');
            $table->string('estado', 20)->default('pendiente');
            foreach (['total', 'procesadas', 'importadas', 'existentes', 'omitidas', 'procesadas_inicio'] as $contador) {
                $table->unsignedInteger($contador)->default(0);
            }
            $table->string('error')->nullable();
            $table->timestamp('inicio_progreso')->nullable();
            $table->timestamps();
            $table->index(['id_empresa', 'estado', 'created_at']);
        });

        $datos = [
            'ruta' => 'configuracion.importaciones.index', 'nombre' => 'Importaciones',
            'icono' => 'bi-cloud-arrow-up', 'activo' => true, 'updated_at' => now(),
        ];
        $menuPersonas = DB::table('menu')->where('ruta', 'configuracion.personas.importaciones.index');
        if ($menuPersonas->exists()) {
            $menuPersonas->update($datos);
        } elseif (! DB::table('menu')->where('ruta', $datos['ruta'])->exists()) {
            DB::table('menu')->insert($datos + [
                'padre_id' => DB::table('menu')->where('ruta', 'configuracion.menu.index')->where('protegido', true)->value('padre_id'),
                'orden' => 25, 'solo_administrador' => true, 'created_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('menu')->where('ruta', 'configuracion.importaciones.index')->update([
            'ruta' => 'configuracion.personas.importaciones.index', 'nombre' => 'Importar personas',
            'icono' => 'bi-person-vcard', 'updated_at' => now(),
        ]);
        Schema::dropIfExists('inv_importaciones');
    }
};
