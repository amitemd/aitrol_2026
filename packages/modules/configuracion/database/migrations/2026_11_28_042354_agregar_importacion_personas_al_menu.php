<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const RUTA = 'configuracion.personas.importaciones.index';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::table('menu')->where('ruta', self::RUTA)->exists()) {
            return;
        }

        $grupo = DB::table('menu')->where('ruta', 'configuracion.menu.index')
            ->where('protegido', true)->value('padre_id');

        DB::table('menu')->insert([
            'padre_id' => $grupo,
            'nombre' => 'Importar personas',
            'ruta' => self::RUTA,
            'icono' => 'bi-person-vcard',
            'orden' => 25,
            'solo_administrador' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('menu')->where('ruta', self::RUTA)->delete();
    }
};
