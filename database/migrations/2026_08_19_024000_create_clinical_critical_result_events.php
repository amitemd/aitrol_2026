<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateClinicalCriticalResultEvents extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('clinical_critical_result_events')) {
            Schema::create('clinical_critical_result_events', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('notification_id')->index('cl_crit_evt_not_idx');
                $table->string('event_type', 40)->index('cl_crit_evt_type_idx');
                $table->string('recipient_id', 191)->nullable();
                $table->string('recipient_name', 191)->nullable();
                $table->string('recipient_role', 100)->nullable();
                $table->string('actor_id', 191)->nullable();
                $table->dateTime('occurred_at');
                $table->text('notes')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->timestamps();
            });
        }

        if (Schema::hasTable('clinical_critical_result_notifications')) {
            DB::table('clinical_critical_result_notifications')->orderBy('id')->chunk(200, function ($rows) {
                foreach ($rows as $row) {
                    $this->insertEvent($row, 'ALERT_CREATED', $row->created_at, null);
                    if ($row->notified_at) {
                        $this->insertEvent($row, 'NOTIFIED', $row->notified_at, $row->notified_by);
                    }
                    if ($row->escalated_at) {
                        $this->insertEvent($row, 'ESCALATED', $row->escalated_at, 'SYSTEM_ESCALATION');
                    }
                    if ($row->acknowledged_at) {
                        $this->insertEvent($row, 'ACKNOWLEDGED', $row->acknowledged_at, $row->acknowledged_by);
                    }
                }
            });
        }
    }

    private function insertEvent($row, $type, $at, $actor)
    {
        DB::table('clinical_critical_result_events')->insert([
            'notification_id' => $row->id, 'event_type' => $type,
            'recipient_id' => $row->recipient_id, 'recipient_name' => $row->recipient_name,
            'recipient_role' => $row->recipient_role, 'actor_id' => $actor,
            'occurred_at' => $at ?: date('Y-m-d H:i:s'), 'notes' => $row->notes,
            'ip_address' => $row->ip_address, 'created_at' => $at ?: date('Y-m-d H:i:s'),
            'updated_at' => $at ?: date('Y-m-d H:i:s'),
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('clinical_critical_result_events');
    }
}
