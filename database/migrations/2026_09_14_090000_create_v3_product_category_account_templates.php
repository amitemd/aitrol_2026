<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateV3ProductCategoryAccountTemplates extends Migration
{
    public function up()
    {
        if (Schema::hasTable('v3_product_category_account_templates')) {
            return;
        }

        Schema::create('v3_product_category_account_templates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('id_empresa', 17);
            $table->string('categoria', 50);
            $table->string('nombre_plantilla', 120);
            $table->string('cuenta_ingreso_gravado', 50)->nullable();
            $table->string('cuenta_ingreso_cero', 50)->nullable();
            $table->string('cuenta_costo', 50)->nullable();
            $table->string('cuenta_gasto', 50)->nullable();
            $table->string('cuenta_inventario', 50)->nullable();
            $table->string('cuenta_devolucion', 50)->nullable();
            $table->boolean('estado')->default(true);
            $table->string('id_usuariocrea', 191)->nullable();
            $table->string('id_usuariomod', 191)->nullable();
            $table->string('ip_creacion', 45)->nullable();
            $table->string('ip_modificacion', 45)->nullable();
            $table->timestamps();
            $table->unique(['id_empresa', 'categoria'], 'v3_prod_cat_acc_company_category_uq');
            $table->index(['id_empresa', 'estado'], 'v3_prod_cat_acc_company_status_idx');
        });
    }

    public function down()
    {
        Schema::dropIfExists('v3_product_category_account_templates');
    }
}
