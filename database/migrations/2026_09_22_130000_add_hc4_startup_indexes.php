<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->index('agenda', ['estado', 'fechaini', 'proc_consul'], 'agenda_hc4_day_type_idx');
        $this->index('agenda', ['estado', 'fechaini', 'id_doctor1'], 'agenda_hc4_doctor1_day_idx');
        $this->index('agenda', ['estado', 'fechaini', 'id_doctor2'], 'agenda_hc4_doctor2_day_idx');
        $this->index('agenda', ['estado', 'fechaini', 'id_doctor3'], 'agenda_hc4_doctor3_day_idx');
    }

    private function index($table, array $columns, $name): void
    {
        if (! Schema::hasTable($table)) {
            return;
        }
        $exists = DB::table('information_schema.statistics')
            ->where('table_schema', DB::connection()->getDatabaseName())
            ->where('table_name', $table)->where('index_name', $name)->exists();
        if (! $exists) {
            Schema::table($table, function (Blueprint $blueprint) use ($columns, $name) {
                $blueprint->index($columns, $name);
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('agenda')) {
            return;
        }
        foreach (['agenda_hc4_day_type_idx', 'agenda_hc4_doctor1_day_idx',
            'agenda_hc4_doctor2_day_idx', 'agenda_hc4_doctor3_day_idx'] as $name) {
            Schema::table('agenda', function (Blueprint $blueprint) use ($name) {
                $blueprint->dropIndex($name);
            });
        }
    }
};
