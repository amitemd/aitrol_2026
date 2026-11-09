<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LockPdfContentPositionByDefault extends Migration
{
    public function up()
    {
        if (Schema::hasTable('pdf_branding_settings') && ! Schema::hasColumn('pdf_branding_settings', 'lock_content_position')) {
            Schema::table('pdf_branding_settings', function (Blueprint $table) {
                $table->boolean('lock_content_position')->default(true)->after('keep_aspect_ratio');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('pdf_branding_settings') && Schema::hasColumn('pdf_branding_settings', 'lock_content_position')) {
            Schema::table('pdf_branding_settings', function (Blueprint $table) {
                $table->dropColumn('lock_content_position');
            });
        }
    }
}
