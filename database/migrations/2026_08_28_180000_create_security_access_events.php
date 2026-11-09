<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSecurityAccessEvents extends Migration
{
    public function up()
    {
        if (Schema::hasTable('security_access_events')) {
            return;
        }

        Schema::create('security_access_events', function (Blueprint $table) {
            $table->bigIncrements('id');
            // 64 evita indices demasiado largos en las versiones antiguas de MySQL
            // que todavía utilizan varias instalaciones de este sistema.
            $table->string('user_id', 64)->nullable()->index();
            $table->string('identifier_hash', 64)->nullable()->index();
            $table->string('event_type', 40)->index();
            $table->string('result', 20)->index();
            $table->string('risk_level', 20)->default('low')->index();
            $table->text('risk_reasons')->nullable();
            $table->string('ip_address', 45)->nullable()->index();
            $table->boolean('is_new_ip')->default(false)->index();
            $table->boolean('is_new_device')->default(false)->index();
            $table->string('country_code', 8)->nullable()->index();
            $table->string('region', 120)->nullable();
            $table->string('city', 120)->nullable();
            $table->string('location_source', 40)->nullable();
            $table->string('device_type', 40)->nullable();
            $table->string('operating_system', 80)->nullable();
            $table->string('browser', 80)->nullable();
            $table->string('device_fingerprint', 64)->nullable()->index();
            $table->text('user_agent')->nullable();
            $table->string('http_method', 10)->nullable();
            $table->string('path', 500)->nullable();
            $table->string('session_hash', 64)->nullable()->index();
            $table->timestamp('occurred_at')->nullable()->index();
            $table->timestamps();
            $table->index(['user_id', 'event_type', 'occurred_at'], 'security_user_event_time_idx');
            $table->index(['ip_address', 'result', 'occurred_at'], 'security_ip_result_time_idx');
        });
    }

    public function down()
    {
        Schema::dropIfExists('security_access_events');
    }
}
