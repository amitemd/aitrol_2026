<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserNetworkAccessRules extends Migration
{
    public function up()
    {
        if (Schema::hasTable('user_network_access_rules')) {
            return;
        }

        Schema::create('user_network_access_rules', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user_id', 13);
            $table->string('name', 120);
            $table->string('ip_rule', 100);
            $table->string('weekdays', 20)->default('1,2,3,4,5');
            $table->time('starts_at');
            $table->time('ends_at');
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();
            $table->boolean('active')->default(true);
            $table->text('notes')->nullable();
            $table->string('created_by', 13);
            $table->string('updated_by', 13)->nullable();
            $table->timestamps();

            $table->index(['user_id', 'active'], 'network_access_user_active_idx');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_network_access_rules');
    }
}
