<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table): void {
            $table->id();
            $table->string('nombre', 100)->unique();
        });
        Schema::create('permisos', function (Blueprint $table): void {
            $table->id();
            $table->string('opcion', 120);
            $table->string('accion', 20);
            $table->unique(['opcion', 'accion']);
        });
        Schema::create('permiso_rol', function (Blueprint $table): void {
            $table->foreignId('id_rol')->constrained('roles')->cascadeOnDelete();
            $table->foreignId('id_permiso')->constrained('permisos')->cascadeOnDelete();
            $table->primary(['id_rol', 'id_permiso']);
        });
        Schema::create('rol_usuario', function (Blueprint $table): void {
            $table->foreignId('id_usuario')->constrained('users')->cascadeOnDelete();
            $table->foreignId('id_rol')->constrained('roles')->cascadeOnDelete();
            $table->primary(['id_usuario', 'id_rol']);
        });
        Schema::create('permiso_usuario', function (Blueprint $table): void {
            $table->foreignId('id_usuario')->constrained('users')->cascadeOnDelete();
            $table->foreignId('id_permiso')->constrained('permisos')->cascadeOnDelete();
            $table->boolean('permitido');
            $table->primary(['id_usuario', 'id_permiso']);
        });

        foreach (array_keys(config('accesos')) as $opcion) {
            DB::table('permisos')->insert(array_map(
                fn (string $accion): array => ['opcion' => $opcion, 'accion' => $accion],
                ['leer', 'escribir', 'actualizar', 'eliminar'],
            ));
        }

        $padre = DB::table('menu')->where('ruta', 'configuracion.menu.index')->value('padre_id');
        foreach ([
            ['Roles y permisos', 'configuracion.roles.index', 'bi-shield-lock', 40, true],
            ['Ubicaciones', 'configuracion.ubicaciones.index', 'bi-geo-alt', 50, true],
        ] as [$nombre, $ruta, $icono, $orden, $soloAdmin]) {
            DB::table('menu')->insert([
                'nombre' => $nombre, 'ruta' => $ruta, 'icono' => $icono, 'orden' => $orden,
                'padre_id' => $padre, 'solo_administrador' => $soloAdmin, 'created_at' => now(), 'updated_at' => now(),
            ]);
        }
        DB::table('menu')->where('ruta', 'configuracion.empresas.index')->update(['solo_administrador' => false]);
        if ($padre !== null) {
            DB::table('menu')->where('id', $padre)->update(['solo_administrador' => false]);
        }
    }

    public function down(): void
    {
        DB::table('menu')->whereIn('ruta', ['configuracion.roles.index', 'configuracion.ubicaciones.index'])->delete();
        $padre = DB::table('menu')->where('ruta', 'configuracion.menu.index')->value('padre_id');
        DB::table('menu')->where('ruta', 'configuracion.empresas.index')->update(['solo_administrador' => true]);
        if ($padre !== null) {
            DB::table('menu')->where('id', $padre)->update(['solo_administrador' => true]);
        }
        foreach (['permiso_usuario', 'rol_usuario', 'permiso_rol', 'permisos', 'roles'] as $tabla) {
            Schema::dropIfExists($tabla);
        }
    }
};