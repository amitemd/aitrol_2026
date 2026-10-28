<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create the legacy product catalogue only when the restored database does not contain it.
     */
    public function up(): void
    {
        if (Schema::hasTable('ct_productos')) {
            return;
        }

        $this->dropInvalidLegacyProductForeignKey();

        Schema::create('ct_productos', function (Blueprint $table): void {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb3';
            $table->collation = 'utf8mb3_general_ci';

            $table->integer('id')->autoIncrement();
            $table->string('codigo', 255);
            $table->string('nombre', 255);
            $table->string('codigo_barra', 255)->nullable();
            $table->tinyInteger('tipo')->nullable();
            $table->string('descripcion', 255)->nullable();
            $table->string('clase', 25)->nullable();
            $table->string('grupo', 13)
                ->nullable()
                ->comment('1= Insumos, 2=Medicamentos, 3=Servicios, 4=Procedimientos, 5=Otros, 6=Honorario Medico, 7 =Equipo, 8=Honorario Anestesiologo, 9=Membresias');
            $table->string('proveedor', 13)->nullable();
            $table->string('cta_gastos', 255)->nullable();
            $table->string('cta_ventas', 255)->nullable();
            $table->string('cta_costos', 255)->nullable();
            $table->string('cta_devolucion', 255)->nullable();
            $table->tinyInteger('reg_serie')->default(0);
            $table->tinyInteger('mod_precio')->default(0);
            $table->tinyInteger('mod_desc')->default(0);
            $table->string('marca', 50)->nullable();
            $table->string('modelo', 25)->nullable();
            $table->tinyInteger('stock_minimo')->nullable();
            $table->date('fecha_expiracion')->nullable();
            $table->string('impuesto_iva_compras', 13)->nullable();
            $table->string('impuesto_iva_ventas', 13)->nullable();
            $table->string('impuesto_servicio', 50)->nullable();
            $table->string('impuesto_ice', 50)->nullable();
            $table->string('clasificacion_impuesto_ice', 50)->nullable();
            $table->double('promedio')->nullable();
            $table->double('reposicion')->nullable();
            $table->double('lista')->nullable();
            $table->double('ultima_compra')->nullable();
            $table->double('precio1')->nullable();
            $table->double('precio2')->nullable();
            $table->double('precio3')->nullable();
            $table->double('precio4')->nullable();
            $table->double('promocion')->nullable();
            $table->double('descuento')->nullable();
            $table->double('financiero')->nullable();
            $table->tinyInteger('ident_paquete')->default(0);
            $table->decimal('valor_total_paq', 18, 2)->nullable();
            $table->tinyInteger('estado_tabla')->default(1);
            $table->string('id_usuariocrea', 13)->nullable();
            $table->string('id_usuariomod', 13)->nullable();
            $table->string('ip_creacion', 40)->nullable();
            $table->string('ip_modificacion', 40)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->tinyInteger('iva')->default(0);
            $table->text('observacion_divisa')->nullable();
            $table->string('id_empresa', 17)->nullable();
            $table->string('gtin', 255)->nullable();
            $table->integer('unidades')->nullable();
            $table->string('tipo_objeto', 1)->default('P');
            $table->tinyInteger('tipo_producto')->default(1);

            $table->unique(['codigo', 'id_empresa'], 'UK_ct_productos');
            $table->index('impuesto_iva_ventas', 'FK_ct_productos_y_servicios_i3');
            $table->index('impuesto_iva_compras', 'FK_ct_productos_y_servicios_im');
            $table->foreign('id_empresa', 'FK_ct_productos_id_empresa')
                ->references('id')
                ->on('empresa')
                ->cascadeOnUpdate();
        });
    }

    /**
     * Keep the operational catalogue and its data during rollbacks.
     */
    public function down(): void {}

    /**
     * Remove the invalid historical constraint that references a non-unique product code.
     */
    private function dropInvalidLegacyProductForeignKey(): void
    {
        $connection = DB::connection();

        if ($connection->getDriverName() !== 'mysql' || ! Schema::hasTable('ct_comision_detalle')) {
            return;
        }

        $constraintExists = $connection
            ->table('information_schema.TABLE_CONSTRAINTS')
            ->where('CONSTRAINT_SCHEMA', $connection->getDatabaseName())
            ->where('TABLE_NAME', 'ct_comision_detalle')
            ->where('CONSTRAINT_NAME', 'FK_ct_comision_detalle_codigo_producto')
            ->where('CONSTRAINT_TYPE', 'FOREIGN KEY')
            ->exists();

        if (! $constraintExists) {
            return;
        }

        Schema::table('ct_comision_detalle', function (Blueprint $table): void {
            $table->dropForeign('FK_ct_comision_detalle_codigo_producto');
        });
    }
};
