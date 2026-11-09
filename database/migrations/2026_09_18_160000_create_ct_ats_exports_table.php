<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ct_ats_exports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('ats_id')->unique();
            $table->string('id_empresa', 13)->index();
            $table->string('filename', 20);
            $table->longText('xml');
            $table->string('sha256', 64);
            $table->longText('manifest');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ct_ats_exports');
    }
};
