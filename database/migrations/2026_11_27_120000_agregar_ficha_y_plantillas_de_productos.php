<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('inv_productos')) {
            throw new RuntimeException('Falta inv_productos. Revisa el estado de 2026_11_27_040000_crear_inventarios antes de ejecutar esta migración.');
        }

        foreach (['inv_plantillas_contables', 'inv_categorias'] as $catalogo) {
            if (Schema::hasTable($catalogo)) {
                continue;
            }

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

        $this->completarFicha();

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

    private function completarFicha(): void
    {
        $columnas = [
            'id_categoria' => fn (Blueprint $tabla) => $tabla->unsignedBigInteger('id_categoria')->nullable(),
            'cuentas' => fn (Blueprint $tabla) => $tabla->json('cuentas')->nullable(),
            'tipo_producto' => fn (Blueprint $tabla) => $tabla->string('tipo_producto', 16)->default('producto'),
            'marca' => fn (Blueprint $tabla) => $tabla->string('marca', 100)->nullable(),
            'modelo' => fn (Blueprint $tabla) => $tabla->string('modelo', 100)->nullable(),
            'ficha_tecnica_url' => fn (Blueprint $tabla) => $tabla->string('ficha_tecnica_url', 500)->nullable(),
            'precio_venta' => fn (Blueprint $tabla) => $tabla->decimal('precio_venta', 12, 2)->nullable(),
            'costo_reposicion' => fn (Blueprint $tabla) => $tabla->decimal('costo_reposicion', 14, 4)->nullable(),
            'descuento_comercial' => fn (Blueprint $tabla) => $tabla->decimal('descuento_comercial', 5, 2)->nullable(),
            'descuento_financiero' => fn (Blueprint $tabla) => $tabla->decimal('descuento_financiero', 5, 2)->nullable(),
        ];
        $pendientes = array_diff_key($columnas, array_flip(Schema::getColumnListing('inv_productos')));
        if ($pendientes !== []) {
            Schema::table('inv_productos', function (Blueprint $tabla) use ($pendientes): void {
                foreach ($pendientes as $agregar) {
                    $agregar($tabla);
                }
            });
        }

        $categoria = ['id_empresa', 'id_categoria'];
        $tieneForanea = collect(Schema::getForeignKeys('inv_productos'))->contains(
            fn (array $clave): bool => $clave['columns'] === $categoria
                && $clave['foreign_table'] === 'inv_categorias' && $clave['foreign_columns'] === ['id_empresa', 'id'],
        );
        if (! $tieneForanea) {
            Schema::table('inv_productos', function (Blueprint $tabla) use ($categoria): void {
                $tabla->foreign($categoria, 'inv_producto_categoria_fk')
                    ->references(['id_empresa', 'id'])->on('inv_categorias')->restrictOnDelete();
            });
        }
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
