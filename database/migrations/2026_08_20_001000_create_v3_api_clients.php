<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateV3ApiClients extends Migration
{
    public function up()
    {
        if (Schema::hasTable('v3_api_clients')) {
            return;
        }
        Schema::create('v3_api_clients', function (Blueprint $table) {
            $table->increments('id');
            $table->string('id_empresa', 13)->index('v3_api_client_company_idx');
            $table->string('nombre', 120);
            $table->string('token_prefix', 20)->index('v3_api_client_prefix_idx');
            $table->string('token_hash', 64)->unique('v3_api_client_token_uq');
            $table->text('scopes');
            $table->tinyInteger('estado')->default(1)->index('v3_api_client_state_idx');
            $table->dateTime('expires_at')->nullable();
            $table->dateTime('last_used_at')->nullable();
            $table->string('last_used_ip', 45)->nullable();
            $table->string('id_usuariocrea', 13)->nullable();
            $table->string('ip_creacion', 45)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('v3_api_clients');
    }
}
