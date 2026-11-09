<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateClinicalControlRollouts extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('clinical_control_rollouts')) {
            Schema::create('clinical_control_rollouts', function (Blueprint $table) {
                $table->increments('id');
                $table->string('control_key', 80)->unique('clinical_control_rollouts_control_key_unique');
                $table->dateTime('enforced_at');
                $table->timestamps();
            });
        }

        if (! DB::table('clinical_control_rollouts')->where('control_key', 'procedure_safety')->exists()) {
            $now = date('Y-m-d H:i:s');
            DB::table('clinical_control_rollouts')->insert([
                'control_key' => 'procedure_safety',
                'enforced_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down()
    {
        // El inicio de un control clínico es evidencia operativa y no se elimina.
    }
}
