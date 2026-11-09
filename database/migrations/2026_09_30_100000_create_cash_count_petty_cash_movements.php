<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateCashCountPettyCashMovements extends Migration
{
    public function up()
    {
        if (Schema::hasTable('ct_arqueo_caja_movimiento')) {
            return;
        }
        Schema::create('ct_arqueo_caja_movimiento', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_arqueo_caja');
            $table->string('id_empresa', 13);
            $table->date('fecha');
            $table->string('tipo', 30);
            $table->string('beneficiario')->nullable();
            $table->string('numero_comprobante', 80)->nullable();
            $table->string('concepto', 500);
            $table->decimal('valor', 12, 2);
            $table->tinyInteger('estado')->default(1);
            $table->string('id_usuariocrea', 13);
            $table->string('id_usuariomod', 13);
            $table->string('ip_creacion', 40);
            $table->string('ip_modificacion', 40);
            $table->timestamps();
            $table->index(['id_empresa', 'fecha']);
            $table->index(['id_arqueo_caja', 'estado']);
        });
        if (Schema::hasTable('ct_arqueo_caja')) {
            foreach (DB::table('ct_arqueo_caja')->where('vales', '>', 0)->get() as $arqueo) {
                DB::table('ct_arqueo_caja_movimiento')->insert([
                    'id_arqueo_caja' => $arqueo->id, 'id_empresa' => $arqueo->id_empresa,
                    'fecha' => $arqueo->fecha_proceso, 'tipo' => 'VALE_CAJA',
                    'beneficiario' => null, 'numero_comprobante' => null,
                    'concepto' => 'Saldo anterior de vales / comprobantes', 'valor' => $arqueo->vales,
                    'estado' => 1, 'id_usuariocrea' => $arqueo->id_usuariocrea,
                    'id_usuariomod' => $arqueo->id_usuariomod,
                    'ip_creacion' => $arqueo->ip_creacion ?: '127.0.0.1',
                    'ip_modificacion' => $arqueo->ip_modificacion ?: '127.0.0.1',
                    'created_at' => $arqueo->created_at, 'updated_at' => $arqueo->updated_at,
                ]);
            }
        }
    }

    public function down()
    {
        Schema::dropIfExists('ct_arqueo_caja_movimiento');
    }
}
