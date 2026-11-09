<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateCompanyHospitalContexts extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('company_hospital_contexts')) {
            Schema::create('company_hospital_contexts', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('company_id', 20)->index('co_hosp_company_idx');
                $table->unsignedInteger('hospital_id')->index('co_hosp_hospital_idx');
                $table->boolean('is_default')->default(false)->index('co_hosp_default_idx');
                $table->boolean('active')->default(true);
                $table->timestamps();
                $table->unique(['company_id', 'hospital_id'], 'co_hosp_pair_uq');
            });
        }
        if (Schema::hasTable('agenda') && Schema::hasTable('sala')) {
            $rows = DB::table('agenda as a')->join('sala as s', 's.id', '=', 'a.id_sala')->whereNotNull('a.id_empresa')->whereNotNull('s.id_hospital')
                ->select('a.id_empresa as company_id', 's.id_hospital as hospital_id')->distinct()->get();
            $groups = [];
            foreach ($rows as $row) {
                $groups[(string) $row->company_id][] = (int) $row->hospital_id;
            }
            foreach ($groups as $company => $hospitals) {
                foreach (array_unique($hospitals) as $hospital) {
                    DB::table('company_hospital_contexts')->insert([
                        'company_id' => $company, 'hospital_id' => $hospital, 'is_default' => count(array_unique($hospitals)) === 1 ? 1 : 0, 'active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
                }
            }
        }
    }

    public function down()
    {
        Schema::dropIfExists('company_hospital_contexts');
    }
}
