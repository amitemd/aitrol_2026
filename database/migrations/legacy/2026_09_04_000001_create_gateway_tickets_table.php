<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGatewayTicketsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('gateway_tickets')) {
            Schema::create('gateway_tickets', function (Blueprint $table) {
                $table->increments('id');
                $table->string('external_id', 120)->unique();
                $table->string('gateway_id', 36)->nullable()->unique();
                $table->string('user_id', 100)->index();
                $table->text('origin_url');
                $table->text('payload');
                $table->text('snapshot')->nullable();
                $table->timestamp('synced_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('gateway_tickets');
    }
}
