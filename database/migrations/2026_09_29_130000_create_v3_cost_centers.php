<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateV3CostCenters extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('v3_cost_centers')) {
            Schema::create('v3_cost_centers', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('id_empresa', 17);
                $table->string('codigo', 30);
                $table->string('nombre', 120);
                $table->string('departamento', 30);
                $table->unsignedBigInteger('id_centro_padre')->nullable();
                $table->string('origen_tipo', 20)->nullable();
                $table->string('origen_id', 191)->nullable();
                $table->string('id_plan_cuenta', 191)->nullable();
                $table->decimal('presupuesto_mensual', 15, 2)->default(0);
                $table->string('descripcion', 500)->nullable();
                $table->boolean('estado')->default(true);
                $table->string('id_usuariocrea', 191)->nullable();
                $table->string('id_usuariomod', 191)->nullable();
                $table->string('ip_creacion', 45)->nullable();
                $table->string('ip_modificacion', 45)->nullable();
                $table->timestamps();
                $table->unique(['id_empresa', 'codigo'], 'v3_cost_center_company_code_uq');
                $table->index(['id_empresa', 'departamento', 'estado'], 'v3_cost_center_company_dept_idx');
                $table->index('id_centro_padre', 'v3_cost_center_parent_idx');
                $table->index(['origen_tipo', 'origen_id'], 'v3_cost_center_source_idx');
            });
        }

        if (Schema::hasTable('ct_asientos_detalle') && ! Schema::hasColumn('ct_asientos_detalle', 'id_centro_costo')) {
            Schema::table('ct_asientos_detalle', function (Blueprint $table) {
                $table->unsignedBigInteger('id_centro_costo')->nullable()->after('id_plan_cuenta');
                $table->index('id_centro_costo', 'ct_entry_detail_cost_center_idx');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('ct_asientos_detalle') && Schema::hasColumn('ct_asientos_detalle', 'id_centro_costo')) {
            Schema::table('ct_asientos_detalle', function (Blueprint $table) {
                $table->dropIndex('ct_entry_detail_cost_center_idx');
                $table->dropColumn('id_centro_costo');
            });
        }
        Schema::dropIfExists('v3_cost_centers');
    }
}
