<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupportTicketDeliveries extends Migration
{
    public function up()
    {
        Schema::create('support_ticket_deliveries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('ticket_id')->unique();
            $table->string('id_empresa', 20)->nullable()->index();
            $table->string('external_id', 120)->unique();
            $table->text('payload');
            $table->string('gateway_id', 36)->nullable();
            $table->text('snapshot')->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });
        Schema::create('support_ticket_attachments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('delivery_id')->index();
            $table->string('name', 150);
            $table->string('path', 100)->unique();
            $table->string('token_hash', 64)->unique();
            $table->text('download_url');
            $table->timestamps();
            $table->foreign('delivery_id')->references('id')->on('support_ticket_deliveries')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('support_ticket_attachments');
        Schema::dropIfExists('support_ticket_deliveries');
    }
}
