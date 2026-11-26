<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHospitalEpisodeFormDraftItems extends Migration
{
    public function up()
    {
        if (Schema::hasTable('hospital_episode_form_draft_items')) {
            return;
        }
        Schema::create('hospital_episode_form_draft_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('draft_id');
            $table->string('item_type', 20);
            $table->string('phase', 20);
            $table->string('code', 30);
            $table->string('description', 500);
            $table->unsignedSmallInteger('position')->default(1);
            $table->string('created_by', 191);
            $table->string('updated_by', 191);
            $table->timestamps();
            $table->index(['draft_id', 'item_type', 'phase'], 'form_draft_items_lookup_idx');
            $table->unique(['draft_id', 'item_type', 'phase', 'code'], 'form_draft_items_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('hospital_episode_form_draft_items');
    }
}
