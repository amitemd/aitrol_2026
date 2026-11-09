<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSriEmailDeliveries extends Migration
{
    public function up()
    {
        if (Schema::hasTable('sri_email_deliveries')) {
            return;
        }
        Schema::create('sri_email_deliveries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('workflow_id')->index();
            $table->string('company_id', 13)->index();
            $table->unsignedBigInteger('sale_id')->nullable()->index();
            $table->string('recipient', 190);
            $table->string('status', 20)->default('queued')->index();
            $table->unsignedInteger('attempts')->default(0);
            $table->text('last_error')->nullable();
            $table->dateTime('queued_at')->nullable();
            $table->dateTime('last_attempt_at')->nullable();
            $table->dateTime('sent_at')->nullable();
            $table->timestamps();
            $table->foreign('workflow_id')->references('id')->on('sri_electronic_workflows')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('sri_email_deliveries');
    }
}
