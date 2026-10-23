<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPrecioVentaToEntregaDetalle extends Migration
{
    public function up()
    {
        Schema::table('ct_entregas_mercaderia_detalle', function (Blueprint $table) {
            if (!Schema::hasColumn('ct_entregas_mercaderia_detalle', 'precio_venta')) {
                $table->decimal('precio_venta', 14, 6)->default(0)->after('cantidad');
            }
        });
    }

    public function down()
    {
        Schema::table('ct_entregas_mercaderia_detalle', function (Blueprint $table) {
            $table->dropColumn('precio_venta');
        });
    }
}
