<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'is_admin_global')) {
            Schema::table('users', fn (Blueprint $table) => $table->boolean('is_admin_global')->default(false));
        }
        $propietario = DB::table('users')->where('is_admin', true)->orderBy('id')->value('id');
        if ($propietario !== null) {
            DB::table('users')->where('id', $propietario)->update(['is_admin_global' => true]);
        }
    }

    public function down(): void
    {
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn('is_admin_global'));
    }
};
