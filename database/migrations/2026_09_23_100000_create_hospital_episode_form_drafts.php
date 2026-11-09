<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHospitalEpisodeFormDrafts extends Migration
{
    public function up()
    {
        if (Schema::hasTable('hospital_episode_form_drafts')) {
            return;
        }

        Schema::create('hospital_episode_form_drafts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('encounter_id', 36);
            $table->unsignedBigInteger('solicitud_id');
            $table->string('form_code', 8);
            $table->longText('payload');
            $table->string('created_by', 191);
            $table->string('updated_by', 191);
            $table->timestamps();
            $table->index(['encounter_id', 'form_code'], 'episode_form_drafts_enc_code_idx');
            $table->index(['solicitud_id', 'form_code'], 'episode_form_drafts_sol_code_idx');
        });
    }

    public function down()
    {
        Schema::dropIfExists('hospital_episode_form_drafts');
    }
}
