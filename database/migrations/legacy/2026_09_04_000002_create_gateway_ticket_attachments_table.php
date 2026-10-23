<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGatewayTicketAttachmentsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('gateway_ticket_attachments')) {
            Schema::create('gateway_ticket_attachments', function (Blueprint $table) {
                $table->increments('id');
                $table->string('external_id', 120)->index();
                $table->string('user_id', 100);
                $table->string('name');
                $table->string('path');
                $table->string('token_hash', 64)->unique();
                $table->text('download_url');
                $table->timestamps();
            });
        }
    }
    public function down()
    {
        Schema::dropIfExists('gateway_ticket_attachments');
    }
}
