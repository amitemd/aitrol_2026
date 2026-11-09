<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CompleteCustomerDebitNoteElectronicFields extends Migration
{
    public function up()
    {
        Schema::table('ct_debito_clientes', function (Blueprint $table) {
            if (! Schema::hasColumn('ct_debito_clientes', 'sucursal')) {
                $table->string('sucursal', 3)->nullable();
            }
            if (! Schema::hasColumn('ct_debito_clientes', 'punto_emision')) {
                $table->string('punto_emision', 3)->nullable();
            }
            if (! Schema::hasColumn('ct_debito_clientes', 'electronica')) {
                $table->boolean('electronica')->default(0);
            }
            if (! Schema::hasColumn('ct_debito_clientes', 'doc_electronico')) {
                $table->unsignedTinyInteger('doc_electronico')->default(0);
            }
            if (! Schema::hasColumn('ct_debito_clientes', 'nro_autorizacion')) {
                $table->string('nro_autorizacion', 100)->nullable();
            }
        });
        Schema::table('ct_detalle_debito_clientes', function (Blueprint $table) {
            if (! Schema::hasColumn('ct_detalle_debito_clientes', 'iva')) {
                $table->boolean('iva')->default(0);
            }
        });
    }

    public function down()
    {
        // No se eliminan campos tributarios para conservar la trazabilidad fiscal.
    }
}
