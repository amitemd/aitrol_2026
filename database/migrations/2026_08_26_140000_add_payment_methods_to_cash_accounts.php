<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddPaymentMethodsToCashAccounts extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('ct_caja_banco_forma_pago')) {
            Schema::create('ct_caja_banco_forma_pago', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('id_caja_banco');
                $table->unsignedInteger('id_forma_pago');
                $table->unique(['id_caja_banco', 'id_forma_pago'], 'uq_caja_banco_forma_pago');
                $table->index('id_forma_pago', 'idx_caja_banco_forma_pago');
            });
        }

        // Conserva como primera selección la forma ya configurada en cada registro.
        DB::statement(
            'INSERT IGNORE INTO ct_caja_banco_forma_pago (id_caja_banco, id_forma_pago) '
            .'SELECT id, formas_pago FROM ct_caja_banco WHERE formas_pago IS NOT NULL AND formas_pago > 0'
        );
    }

    public function down()
    {
        Schema::dropIfExists('ct_caja_banco_forma_pago');
    }
}
