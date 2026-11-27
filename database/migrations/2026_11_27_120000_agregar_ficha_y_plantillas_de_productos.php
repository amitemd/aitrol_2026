<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['inv_plantillas_contables', 'inv_categorias'] as $catalogo) {
            Schema::create($catalogo, function (Blueprint $tabla) use ($catalogo): void {
                $tabla->id();
                $tabla->foreignId('id_empresa')->constrained('erp_empresas')->restrictOnDelete();
                $tabla->string('codigo', 60);
                $tabla->string('nombre', 160);
                $tabla->string('descripcion', 500)->nullable();
                $tabla->boolean('activo')->default(true);
                $tabla->foreignId('id_usuario_creacion')->nullable()->constrained('users')->nullOnDelete();
                $tabla->foreignId('id_usuario_modificacion')->nullable()->constrained('users')->nullOnDelete();
                $tabla->timestamps();
                $tabla->unique(['id_empresa', 'codigo']);
                $tabla->unique(['id_empresa', 'id']);
                if ($catalogo === 'inv_plantillas_contables') {
                    $tabla->json('cuentas');
                } else {
                    $tabla->unsignedBigInteger('id_plantilla_contable')->nullable();
                    $tabla->foreign(['id_empresa', 'id_plantilla_contable'], 'inv_categoria_plantilla_fk')
                        ->references(['id_empresa', 'id'])->on('inv_plantillas_contables')->restrictOnDelete();
                }
            });
        }

        Schema::table('inv_productos', function (Blueprint $tabla): void {
            $tabla->unsignedBigInteger('id_categoria')->nullable();
            $tabla->foreign(['id_empresa', 'id_categoria'], 'inv_producto_categoria_fk')
                ->references(['id_empresa', 'id'])->on('inv_categorias')->restrictOnDelete();
            $tabla->json('cuentas')->nullable();
            $tabla->string('tipo_producto', 16)->default('producto');
            $tabla->string('marca', 100)->nullable();
            $tabla->string('modelo', 100)->nullable();
            $tabla->string('ficha_tecnica_url', 500)->nullable();
            $tabla->decimal('precio_venta', 12, 2)->nullable();
            $tabla->decimal('costo_reposicion', 14, 4)->nullable();
            $tabla->decimal('descuento_comercial', 5, 2)->nullable();
            $tabla->decimal('descuento_financiero', 5, 2)->nullable();
        });

        DB::transaction(function (): void {
            $padre = DB::table('menu')->whereNull('padre_id')->where('nombre', 'Inventarios')->value('id');
            foreach (['categorias' => ['Categorías', 'bi-diagram-3', 80], 'plantillas-contables' => ['Plantillas contables', 'bi-journal-bookmark', 90]] as $seccion => [$nombre, $icono, $orden]) {
                $ruta = 'inventarios.'.$seccion.'.index';
                if ($padre && ! DB::table('menu')->where('ruta', $ruta)->exists()) {
                    DB::table('menu')->insert(['padre_id' => $padre, 'ruta' => $ruta, 'nombre' => $nombre,
                        'icono' => $icono, 'orden' => $orden, 'created_at' => now(), 'updated_at' => now()]);
                }
                DB::table('erp_empresas')->select('id')->chunkById(100, function ($empresas) use ($ruta): void {
                    $filas = [];
                    foreach ($empresas as $empresa) {
                        foreach (['leer', 'escribir', 'actualizar', 'eliminar'] as $accion) {
                            $filas[] = ['id_empresa' => $empresa->id, 'opcion' => $ruta, 'accion' => $accion];
                        }
                    }
                    DB::table('permisos')->insertOrIgnore($filas);
                });
            }
        });
    }

    public function down(): void
    {
        Schema::table('inv_productos', function (Blueprint $tabla): void {
            $tabla->dropForeign('inv_producto_categoria_fk');
            $tabla->dropColumn(['id_categoria', 'cuentas', 'tipo_producto', 'marca', 'modelo', 'ficha_tecnica_url',
                'precio_venta', 'costo_reposicion', 'descuento_comercial', 'descuento_financiero']);
        });
        Schema::dropIfExists('inv_categorias');
        Schema::dropIfExists('inv_plantillas_contables');
    }
};
