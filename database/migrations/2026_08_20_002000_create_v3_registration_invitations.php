<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateV3RegistrationInvitations extends Migration
{
    public function up()
    {
        if (Schema::hasTable('v3_registration_invitations')) {
            return;
        }

        Schema::create('v3_registration_invitations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('token_hash', 64)->unique('v3_reg_inv_token_uq');
            $table->unsignedInteger('id_plan');
            $table->string('email', 191)->nullable();
            $table->timestamp('expires_at');
            $table->timestamp('used_at')->nullable();
            $table->string('used_by', 20)->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->string('created_by', 20)->nullable();
            $table->string('created_ip', 45)->nullable();
            $table->timestamps();
            $table->index(['id_plan', 'expires_at'], 'v3_reg_inv_plan_exp_idx');
        });
    }

    public function down()
    {
        Schema::dropIfExists('v3_registration_invitations');
    }
}
