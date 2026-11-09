<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClinicalTransitionsAndTransferReconciliation extends Migration
{
    public function up()
    {
        if (Schema::hasTable('clinical_encounters') && ! Schema::hasColumn('clinical_encounters', 'clinical_state')) {
            Schema::table('clinical_encounters', function (Blueprint $table) {
                $table->string('clinical_state', 40)->nullable()->index();
            });
        }

        if (! Schema::hasTable('clinical_transfer_reconciliations')) {
            Schema::create('clinical_transfer_reconciliations', function (Blueprint $table) {
                $table->increments('id');
                $table->string('encounter_id', 36)->index();
                $table->string('source_type', 40)->default('ho_solicitud');
                $table->string('source_id', 80)->index();
                $table->string('from_state', 40);
                $table->string('to_state', 40);
                $table->string('decision', 20)->default('PRESERVE');
                $table->unsignedInteger('active_orders_count')->default(0);
                $table->text('reason')->nullable();
                $table->string('status', 20)->default('PENDING')->index();
                $table->string('actor_id', 45)->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->dateTime('completed_at')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('clinical_transfer_order_items')) {
            Schema::create('clinical_transfer_order_items', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('reconciliation_id')->index();
                $table->string('order_type', 40);
                $table->string('order_id', 80);
                $table->string('description', 180)->nullable();
                $table->string('previous_status', 40)->nullable();
                $table->string('decision', 20)->default('PRESERVE');
                $table->text('reason')->nullable();
                $table->string('responsible_id', 45)->nullable();
                $table->timestamps();
                $table->unique(['reconciliation_id', 'order_type', 'order_id'], 'clinical_transfer_order_unique');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('clinical_transfer_order_items');
        Schema::dropIfExists('clinical_transfer_reconciliations');
        if (Schema::hasTable('clinical_encounters') && Schema::hasColumn('clinical_encounters', 'clinical_state')) {
            Schema::table('clinical_encounters', function (Blueprint $table) {
                $table->dropColumn('clinical_state');
            });
        }
    }
}
