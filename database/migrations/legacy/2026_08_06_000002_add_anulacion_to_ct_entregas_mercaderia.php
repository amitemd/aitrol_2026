<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAnulacionToCtEntregasMercaderia extends Migration
{
    public function up()
    {
        Schema::table('ct_entregas_mercaderia', function (Blueprint $table) {
            if (!Schema::hasColumn('ct_entregas_mercaderia', 'id_asiento_reversion')) {
                $table->unsignedInteger('id_asiento_reversion')->nullable()->after('id_asiento_costo');
            }
            if (!Schema::hasColumn('ct_entregas_mercaderia', 'fecha_anulacion')) {
                $table->dateTime('fecha_anulacion')->nullable()->after('estado');
            }
            if (!Schema::hasColumn('ct_entregas_mercaderia', 'motivo_anulacion')) {
                $table->text('motivo_anulacion')->nullable()->after('fecha_anulacion');
            }
            if (!Schema::hasColumn('ct_entregas_mercaderia', 'id_usuario_anulacion')) {
                $table->string('id_usuario_anulacion', 50)->nullable()->after('motivo_anulacion');
            }
        });
    }

    public function down()
    {
        Schema::table('ct_entregas_mercaderia', function (Blueprint $table) {
            $table->dropColumn([
                'id_asiento_reversion',
                'fecha_anulacion',
                'motivo_anulacion',
                'id_usuario_anulacion',
            ]);
        });
    }
}
