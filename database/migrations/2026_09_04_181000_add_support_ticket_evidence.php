<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSupportTicketEvidence extends Migration
{
    public function up()
    {
        if (! Schema::hasColumn('support_ticket_deliveries', 'id_empresa')) {
            Schema::table('support_ticket_deliveries', function (Blueprint $table) {
                $table->string('id_empresa', 20)->nullable()->index();
                $table->text('snapshot')->nullable();
                $table->timestamp('synced_at')->nullable();
            });
        }
        if (! Schema::hasTable('support_ticket_attachments')) {
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
    }

    public function down()
    {
        // No automatic rollback: these columns may belong to the base migration.
    }
}
