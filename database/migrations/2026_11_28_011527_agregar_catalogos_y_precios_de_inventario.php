<?php

use App\Services\UnidadesInventarioIniciales;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $catalogos = ['marcas' => ['marca', 100, 'Marcas', 'bi-bookmark', 100],
            'unidades' => ['unidad', 30, 'Unidades de medida', 'bi-rulers', 110]];
        foreach ($catalogos as $seccion => [$campo, $longitud, $nombre, $icono, $orden]) {
            $tabla = 'inv_'.$seccion;
            Schema::create($tabla, function (Blueprint $esquema) use ($longitud): void {
                $esquema->id();
                $esquema->foreignId('id_empresa')->constrained('erp_empresas')->restrictOnDelete();
                $esquema->string('codigo', 60);
                $esquema->string('nombre', $longitud);
                $esquema->string('descripcion', 500)->nullable();
                $esquema->boolean('activo')->default(true);
                $esquema->foreignId('id_usuario_creacion')->nullable()->constrained('users')->nullOnDelete();
                $esquema->foreignId('id_usuario_modificacion')->nullable()->constrained('users')->nullOnDelete();
                $esquema->timestamps();
                $esquema->unique(['id_empresa', 'codigo']);
                $esquema->unique(['id_empresa', 'id']);
            });
            Schema::table('inv_productos', function (Blueprint $esquema) use ($campo, $tabla): void {
                $esquema->unsignedBigInteger('id_'.$campo)->nullable();
                $esquema->foreign(['id_empresa', 'id_'.$campo])->references(['id_empresa', 'id'])->on($tabla)->restrictOnDelete();
            });
            $ruta = 'inventarios.'.$seccion.'.index';
            $padre = DB::table('menu')->whereNull('padre_id')->where('nombre', 'Inventarios')->value('id');
            if ($padre !== null && ! DB::table('menu')->where('ruta', $ruta)->exists()) {
                DB::table('menu')->insert(['padre_id' => $padre, 'ruta' => $ruta, 'nombre' => $nombre,
                    'icono' => $icono, 'orden' => $orden, 'created_at' => now(), 'updated_at' => now()]);
            }
            DB::table('erp_empresas')->select('id')->chunkById(100, function ($empresas) use ($ruta, $tabla, $campo): void {
                foreach ($empresas as $empresa) {
                    if ($campo === 'unidad') {
                        app(UnidadesInventarioIniciales::class)->copiarA($empresa->id);
                    }
                    DB::table('permisos')->insertOrIgnore(array_map(
                        fn (string $accion): array => ['id_empresa' => $empresa->id, 'opcion' => $ruta, 'accion' => $accion],
                        ['leer', 'escribir', 'actualizar', 'eliminar'],
                    ));
                    $this->vincularProductos($empresa->id, $tabla, $campo);
                }
            });
        }
        Schema::table('inv_productos', function (Blueprint $tabla): void {
            foreach (['precio_venta_2', 'precio_venta_3', 'precio_venta_4'] as $campo) {
                $tabla->decimal($campo, 12, 2)->nullable();
            }
            $tabla->decimal('costo_unitario_referencia', 14, 4)->nullable();
        });
    }

    private function vincularProductos(int $empresa, string $tabla, string $campo): void
    {
        $productos = DB::table('inv_productos')->where('id_empresa', $empresa);
        $catalogo = DB::table($tabla)->where('id_empresa', $empresa);
        $ids = (clone $catalogo)->pluck('id', 'nombre')->mapWithKeys(
            fn (int $id, string $nombre): array => [Str::lower($nombre) => $id],
        )->all();
        foreach ((clone $productos)->whereNotNull($campo)->distinct()->pluck($campo) as $valor) {
            $nombre = Str::squish($valor);
            if ($nombre === '') {
                continue;
            }
            $clave = Str::lower($nombre);
            $ids[$clave] ??= DB::table($tabla)->insertGetId([
                'id_empresa' => $empresa, 'nombre' => $nombre, 'codigo' => 'LEG-'.substr(hash('sha256', $clave), 0, 24),
                'created_at' => now(), 'updated_at' => now(),
            ]);
            (clone $productos)->where($campo, $valor)->update(['id_'.$campo => $ids[$clave]]);
        }
    }

    public function down(): void
    {
        Schema::table('inv_productos', function (Blueprint $tabla): void {
            $tabla->dropForeign(['id_empresa', 'id_marca']);
            $tabla->dropForeign(['id_empresa', 'id_unidad']);
            $tabla->dropColumn(['id_marca', 'id_unidad', 'precio_venta_2', 'precio_venta_3', 'precio_venta_4', 'costo_unitario_referencia']);
        });
        foreach (['marcas', 'unidades'] as $seccion) {
            DB::table('menu')->where('ruta', 'inventarios.'.$seccion.'.index')->delete();
            DB::table('permisos')->where('opcion', 'inventarios.'.$seccion.'.index')->delete();
            Schema::dropIfExists('inv_'.$seccion);
        }
    }
};
