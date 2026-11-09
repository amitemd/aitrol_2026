<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddPurchasePaymentLifecycle extends Migration
{
    public function up()
    {
        $initializeBalances = ! Schema::hasColumn('ct_compras', 'saldo_pendiente');
        Schema::table('ct_compras', function (Blueprint $table) {
            if (! Schema::hasColumn('ct_compras', 'estado_pago')) {
                $table->string('estado_pago', 15)->default('PENDIENTE')->after('forma_pago');
            }
            if (! Schema::hasColumn('ct_compras', 'saldo_pendiente')) {
                $table->decimal('saldo_pendiente', 18, 4)->default(0)->after('estado_pago');
            }
            if (! Schema::hasColumn('ct_compras', 'id_caja_banco_pago')) {
                $table->integer('id_caja_banco_pago')->nullable()->after('saldo_pendiente');
            }
            if (! Schema::hasColumn('ct_compras', 'id_asiento_pago')) {
                $table->integer('id_asiento_pago')->nullable()->after('id_caja_banco_pago');
            }
        });

        // Las compras históricas ya eran tratadas como deuda por los reportes. No se
        // infiere que estén pagadas solo por tener un código SRI de forma de pago.
        if ($initializeBalances) {
            DB::table('ct_compras')->update([
                'estado_pago' => 'PENDIENTE',
                'saldo_pendiente' => DB::raw('COALESCE(valor_contable, total_final, 0)'),
            ]);
        }
    }

    public function down()
    {
        Schema::table('ct_compras', function (Blueprint $table) {
            foreach (['id_asiento_pago', 'id_caja_banco_pago', 'saldo_pendiente', 'estado_pago'] as $column) {
                if (Schema::hasColumn('ct_compras', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}
