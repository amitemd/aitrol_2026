<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaitingRoomVideosTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('waiting_room_videos')) {
            return;
        }

        Schema::create('waiting_room_videos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 160);
            $table->string('stored_name', 191)->unique();
            $table->string('original_name', 255);
            $table->string('mime_type', 60);
            $table->unsignedInteger('sort_order')->default(10);
            $table->boolean('is_active')->default(true);
            $table->string('uploaded_by', 13)->nullable();
            $table->timestamps();
            $table->index(['is_active', 'sort_order']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('waiting_room_videos');
    }
}
