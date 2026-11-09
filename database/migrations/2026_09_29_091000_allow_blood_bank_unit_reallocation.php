<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AllowBloodBankUnitReallocation extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('blood_bank_request_units')) {
            return;
        }

        Schema::table('blood_bank_request_units', function (Blueprint $table) {
            $table->dropUnique('bb_link_unit_uq');
            $table->index(['blood_bank_unit_id', 'status'], 'bb_link_unit_status_idx');
        });
    }

    public function down()
    {
        if (! Schema::hasTable('blood_bank_request_units')) {
            return;
        }

        Schema::table('blood_bank_request_units', function (Blueprint $table) {
            $table->dropIndex('bb_link_unit_status_idx');
        });
        $hasRepeatedUnits = DB::table('blood_bank_request_units')->select('blood_bank_unit_id')
            ->groupBy('blood_bank_unit_id')->havingRaw('COUNT(*) > 1')->exists();
        if (! $hasRepeatedUnits) {
            Schema::table('blood_bank_request_units', function (Blueprint $table) {
                $table->unique('blood_bank_unit_id', 'bb_link_unit_uq');
            });
        }
    }
}
