<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LinkBreakGlassAccessEvents extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('clinical_access_events') || Schema::hasColumn('clinical_access_events', 'break_glass_access_id')) {
            return;
        }

        Schema::table('clinical_access_events', function (Blueprint $table) {
            $table->unsignedBigInteger('break_glass_access_id')->nullable()->index('cl_ae_bg_idx');
        });

        if (! Schema::hasTable('clinical_break_glass_accesses')) {
            return;
        }
        DB::table('clinical_break_glass_accesses')->orderBy('id')->chunk(100, function ($accesses) {
            foreach ($accesses as $access) {
                $event = DB::table('clinical_access_events')->whereNull('break_glass_access_id')
                    ->where('encounter_id', $access->encounter_id)->where('user_id', $access->user_id)
                    ->where('action', $access->action)->where('decision', 'ACTIVATED')
                    ->orderBy('id')->first();
                if ($event) {
                    DB::table('clinical_access_events')->where('id', $event->id)
                        ->update(['break_glass_access_id' => $access->id]);
                }
            }
        });
    }

    public function down()
    {
        if (Schema::hasTable('clinical_access_events') && Schema::hasColumn('clinical_access_events', 'break_glass_access_id')) {
            Schema::table('clinical_access_events', function (Blueprint $table) {
                $table->dropIndex('cl_ae_bg_idx');
                $table->dropColumn('break_glass_access_id');
            });
        }
    }
}
