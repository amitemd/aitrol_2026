<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('procedimiento') || Schema::hasColumn('procedimiento', 'quirofano')) {
            return;
        }

        Schema::table('procedimiento', function (Blueprint $table): void {
            $table->tinyInteger('quirofano')->default(0);
        });
    }

    /**
     * Keep the column because procedure records may already use its classification.
     */
    public function down(): void {}
};
