<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table): void {
            $table->foreignId('id_usuario_creacion')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('id_usuario_modificacion')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table): void {
            $table->dropForeign(['id_usuario_creacion']);
            $table->dropForeign(['id_usuario_modificacion']);
            $table->dropColumn(['id_usuario_creacion', 'id_usuario_modificacion']);
            $table->dropTimestamps();
        });
    }
};
