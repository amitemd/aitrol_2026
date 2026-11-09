<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateV3ActionPermissions extends Migration
{
    public function up()
    {
        if (Schema::hasTable('v3_action_permissions')) {
            return;
        }
        Schema::create('v3_action_permissions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('id_usuario', 20);
            $table->string('id_empresa', 20);
            $table->string('modulo', 60);
            $table->string('accion', 20);
            $table->boolean('permitido')->default(false);
            $table->string('otorgado_por', 20)->nullable();
            $table->timestamps();
            $table->unique(['id_usuario', 'id_empresa', 'modulo', 'accion'], 'v3_act_perm_scope_uq');
            $table->index(['id_empresa', 'modulo'], 'v3_act_perm_company_mod_idx');
        });
    }

    public function down()
    {
        Schema::dropIfExists('v3_action_permissions');
    }
}
