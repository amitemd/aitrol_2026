<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRequirementsToCtTipoPago extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('ct_tipo_pago')) {
            return;
        }
        Schema::table('ct_tipo_pago', function (Blueprint $table) {
            if (! Schema::hasColumn('ct_tipo_pago', 'requiere_banco')) {
                $table->boolean('requiere_banco')->default(false)->after('codigo_sri');
            }
            if (! Schema::hasColumn('ct_tipo_pago', 'requiere_referencia')) {
                $table->boolean('requiere_referencia')->default(false)->after('requiere_banco');
            }
        });
    }

    public function down()
    {
        if (! Schema::hasTable('ct_tipo_pago')) {
            return;
        }
        Schema::table('ct_tipo_pago', function (Blueprint $table) {
            $columns = [];
            if (Schema::hasColumn('ct_tipo_pago', 'requiere_banco')) {
                $columns[] = 'requiere_banco';
            }
            if (Schema::hasColumn('ct_tipo_pago', 'requiere_referencia')) {
                $columns[] = 'requiere_referencia';
            }
            if ($columns) {
                $table->dropColumn($columns);
            }
        });
    }
}
