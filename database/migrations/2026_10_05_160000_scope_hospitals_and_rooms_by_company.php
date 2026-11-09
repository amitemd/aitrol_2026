<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('hospital')) {
            return;
        }

        if (! Schema::hasColumn('hospital', 'id_empresa')) {
            Schema::table('hospital', function (Blueprint $table) {
                $table->string('id_empresa', 20)->nullable()->after('id');
                $table->index('id_empresa', 'hospital_empresa_idx');
            });
        }
        if (Schema::hasTable('sala') && ! Schema::hasColumn('sala', 'id_empresa')) {
            Schema::table('sala', function (Blueprint $table) {
                $table->string('id_empresa', 20)->nullable()->after('id');
                $table->index('id_empresa', 'sala_empresa_idx');
            });
        }

        foreach (DB::table('hospital')->orderBy('id')->get(['id', 'id_empresa']) as $hospital) {
            $owner = trim((string) $hospital->id_empresa);
            if ($owner === '') {
                $owner = $this->inferOwner((int) $hospital->id);
            }

            if ($owner !== '') {
                DB::table('hospital')->where('id', $hospital->id)->update(['id_empresa' => $owner]);
                if (Schema::hasTable('sala')) {
                    DB::table('sala')->where('id_hospital', $hospital->id)
                        ->where(function ($query) {
                            $query->whereNull('id_empresa')->orWhere('id_empresa', '');
                        })->update(['id_empresa' => $owner]);
                }
            }

            $this->repairContexts((int) $hospital->id, $owner);
        }
    }

    private function inferOwner($hospitalId)
    {
        $candidates = [];
        if (Schema::hasTable('sala') && Schema::hasColumn('sala', 'id_empresa')) {
            foreach (DB::table('sala')->where('id_hospital', $hospitalId)
                ->whereNotNull('id_empresa')->where('id_empresa', '<>', '')
                ->distinct()->pluck('id_empresa') as $companyId) {
                $candidates[(string) $companyId] = true;
            }
        }
        if (Schema::hasTable('agenda') && Schema::hasTable('sala') && Schema::hasColumn('agenda', 'id_empresa')) {
            foreach (DB::table('agenda as a')->join('sala as s', 's.id', '=', 'a.id_sala')
                ->where('s.id_hospital', $hospitalId)->whereNotNull('a.id_empresa')
                ->where('a.id_empresa', '<>', '')->distinct()->pluck('a.id_empresa') as $companyId) {
                $candidates[(string) $companyId] = true;
            }
        }
        if (count($candidates) === 1) {
            return (string) key($candidates);
        }
        if (count($candidates) > 1) {
            return '';
        }

        if (Schema::hasTable('company_hospital_contexts')) {
            $companies = DB::table('company_hospital_contexts')->where('hospital_id', $hospitalId)
                ->where('active', 1)->distinct()->pluck('company_id')->map(function ($value) {
                    return trim((string) $value);
                })->filter()->unique()->values();
            if ($companies->count() === 1) {
                return (string) $companies->first();
            }
        }

        return '';
    }

    private function repairContexts($hospitalId, $owner)
    {
        if (! Schema::hasTable('company_hospital_contexts')) {
            return;
        }
        $now = date('Y-m-d H:i:s');
        if ($owner === '') {
            // Una unidad con varios posibles propietarios no debe quedar visible
            // en ninguna empresa hasta que Sistemas defina su pertenencia.
            DB::table('company_hospital_contexts')->where('hospital_id', $hospitalId)
                ->update(['active' => 0, 'is_default' => 0, 'updated_at' => $now]);

            return;
        }

        DB::table('company_hospital_contexts')->where('hospital_id', $hospitalId)
            ->where('company_id', '<>', $owner)
            ->update(['active' => 0, 'is_default' => 0, 'updated_at' => $now]);

        $hasDefault = DB::table('company_hospital_contexts')->where('company_id', $owner)
            ->where('active', 1)->where('is_default', 1)->exists();
        DB::table('company_hospital_contexts')->updateOrInsert(
            ['company_id' => $owner, 'hospital_id' => $hospitalId],
            ['active' => 1, 'is_default' => $hasDefault ? 0 : 1,
                'created_at' => $now, 'updated_at' => $now]
        );
    }

    public function down(): void
    {
        if (Schema::hasTable('hospital') && Schema::hasColumn('hospital', 'id_empresa')) {
            Schema::table('hospital', function (Blueprint $table) {
                $table->dropIndex('hospital_empresa_idx');
                $table->dropColumn('id_empresa');
            });
        }
        // sala.id_empresa pertenece a una migración anterior y no se elimina.
    }
};
