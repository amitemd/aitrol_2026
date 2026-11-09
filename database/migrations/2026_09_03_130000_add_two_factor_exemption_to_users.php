<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTwoFactorExemptionToUsers extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('users')) {
            return;
        }
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'two_factor_exempted_at')) {
                $table->timestamp('two_factor_exempted_at')->nullable();
            }
            if (! Schema::hasColumn('users', 'two_factor_exempted_by')) {
                $table->string('two_factor_exempted_by', 20)->nullable();
            }
        });
    }

    public function down()
    {
        if (! Schema::hasTable('users')) {
            return;
        }
        Schema::table('users', function (Blueprint $table) {
            foreach (['two_factor_exempted_at', 'two_factor_exempted_by'] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}
