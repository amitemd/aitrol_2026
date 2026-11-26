<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSystemicPhysicalExamToHoForm008 extends Migration
{
    private $columns = [
        'organo_sentidos',
        'ex_respiratorio',
        'ex_cardio_vascular',
        'ex_digestivo',
        'genital',
        'urinario',
        'musculo_esqueletico',
        'ex_endocrino',
        'ex_hemo_linfatico',
        'neurologico',
    ];

    public function up(): void
    {
        if (! Schema::hasTable('ho_form008')) {
            return;
        }

        $missing = array_values(array_filter($this->columns, function ($column) {
            return ! Schema::hasColumn('ho_form008', $column);
        }));
        if (! $missing) {
            return;
        }

        Schema::table('ho_form008', function (Blueprint $table) use ($missing) {
            foreach ($missing as $column) {
                $table->text($column)->nullable();
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('ho_form008')) {
            return;
        }

        $existing = array_values(array_filter($this->columns, function ($column) {
            return Schema::hasColumn('ho_form008', $column);
        }));
        if (! $existing) {
            return;
        }

        Schema::table('ho_form008', function (Blueprint $table) use ($existing) {
            $table->dropColumn($existing);
        });
    }
}
