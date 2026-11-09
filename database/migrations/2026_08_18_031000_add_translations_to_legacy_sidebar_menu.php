<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddTranslationsToLegacySidebarMenu extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('sidebar_items') || ! Schema::hasColumn('sidebar_items', 'traduccion')) {
            return;
        }

        $translations = [
            'config.system_admin' => 'tsidebar.administracion_sistemas',
            'system.specialties' => 'tsidebar.menu_system_specialties',
            'system.geography' => 'tsidebar.menu_system_geography',
            'system.locations' => 'tsidebar.menu_system_locations',
            'system.procedures' => 'tsidebar.menu_system_procedures',
            'system.insurances' => 'tsidebar.menu_system_insurances',
            'system.companies' => 'tsidebar.menu_system_companies',
            'system.complete_procedures' => 'tsidebar.menu_system_complete_procedures',
            'system.user_types' => 'tsidebar.menu_system_user_types',
            'system.users' => 'tsidebar.menu_system_users',
            'system.error_references' => 'tsidebar.menu_system_error_references',
            'system.sidebar' => 'tsidebar.menu_system_sidebar',
            'system.cie10_3' => 'tsidebar.menu_system_cie10_3',
            'system.cie10_4' => 'tsidebar.menu_system_cie10_4',
        ];

        foreach ($translations as $key => $translation) {
            DB::table('sidebar_items')->where('clave', $key)->update([
                'traduccion' => $translation,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    public function down()
    {
        if (! Schema::hasTable('sidebar_items') || ! Schema::hasColumn('sidebar_items', 'traduccion')) {
            return;
        }

        DB::table('sidebar_items')->whereIn('clave', [
            'system.specialties', 'system.geography', 'system.locations',
            'system.procedures', 'system.insurances', 'system.companies',
            'system.complete_procedures', 'system.user_types', 'system.users',
            'system.error_references', 'system.sidebar', 'system.cie10_3',
            'system.cie10_4',
        ])->update(['traduccion' => null]);
    }
}
