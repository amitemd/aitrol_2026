<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVisualControlsToPdfBrandingSettings extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('pdf_branding_settings')) {
            return;
        }

        Schema::table('pdf_branding_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('pdf_branding_settings', 'height_mm')) {
                $table->unsignedSmallInteger('height_mm')->default(20)->after('width_mm');
            }
            if (! Schema::hasColumn('pdf_branding_settings', 'offset_x_mm')) {
                $table->smallInteger('offset_x_mm')->default(0)->after('height_mm');
            }
            if (! Schema::hasColumn('pdf_branding_settings', 'offset_y_mm')) {
                $table->smallInteger('offset_y_mm')->default(0)->after('offset_x_mm');
            }
            if (! Schema::hasColumn('pdf_branding_settings', 'opacity_percent')) {
                $table->unsignedSmallInteger('opacity_percent')->default(100)->after('offset_y_mm');
            }
            if (! Schema::hasColumn('pdf_branding_settings', 'rotation_deg')) {
                $table->smallInteger('rotation_deg')->default(0)->after('opacity_percent');
            }
            if (! Schema::hasColumn('pdf_branding_settings', 'keep_aspect_ratio')) {
                $table->boolean('keep_aspect_ratio')->default(true)->after('rotation_deg');
            }
            if (! Schema::hasColumn('pdf_branding_settings', 'lock_content_position')) {
                $table->boolean('lock_content_position')->default(true)->after('keep_aspect_ratio');
            }
        });
    }

    public function down()
    {
        if (! Schema::hasTable('pdf_branding_settings')) {
            return;
        }
        Schema::table('pdf_branding_settings', function (Blueprint $table) {
            $columns = ['height_mm', 'offset_x_mm', 'offset_y_mm', 'opacity_percent', 'rotation_deg', 'keep_aspect_ratio', 'lock_content_position'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('pdf_branding_settings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}
