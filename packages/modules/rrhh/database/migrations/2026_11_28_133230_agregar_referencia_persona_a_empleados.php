<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rh_empleados', function (Blueprint $table): void {
            $table->foreignId('id_persona')->nullable()->constrained('personas')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('rh_empleados', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('id_persona');
        });
    }
};
