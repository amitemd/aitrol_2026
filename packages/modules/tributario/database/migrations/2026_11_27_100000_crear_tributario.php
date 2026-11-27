<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tr_declaraciones', function (Blueprint $table): void {
            $this->empresa($table);
            $table->string('formato', 16);
            $table->date('desde');
            $table->date('hasta');
            $table->string('estado', 14);
            $table->json('resumen');
            $table->json('detalle');
            $table->json('observaciones');
            $table->string('referencia_externa', 100)->nullable();
            $table->date('fecha_externa')->nullable();
            $table->unique(['id_empresa', 'formato', 'desde', 'hasta'], 'tr_periodo_unico');
            $this->auditar($table);
        });
        Schema::create('tr_nomina', function (Blueprint $table): void {
            $this->empresa($table);
            $table->unsignedSmallInteger('ejercicio');
            $table->string('identificacion', 30);
            $table->string('nombre', 180);
            $table->unsignedBigInteger('ingresos_gravados');
            $table->unsignedBigInteger('ingresos_exentos');
            $table->unsignedBigInteger('aporte_personal');
            $table->unsignedBigInteger('retencion');
            $table->unique(['id_empresa', 'ejercicio', 'identificacion']);
            $this->auditar($table);
        });
        Schema::create('tr_documentos', function (Blueprint $table): void {
            $this->empresa($table);
            $this->declaracion($table);
            $table->string('tipo', 14);
            $table->string('nombre', 180);
            $table->string('archivo', 255);
            $table->string('extension', 8);
            $table->string('sha256', 64);
            $table->unsignedBigInteger('bytes');
            $this->auditar($table);
        });
        Schema::create('tr_eventos', function (Blueprint $table): void {
            $this->empresa($table);
            $this->declaracion($table);
            $table->string('accion', 24);
            $table->string('detalle', 250)->nullable();
            $this->auditar($table);
        });
    }

    public function down(): void
    {
        foreach (['tr_eventos', 'tr_documentos', 'tr_nomina', 'tr_declaraciones'] as $tabla) Schema::dropIfExists($tabla);
    }

    private function empresa(Blueprint $table): void
    {
        $table->id();
        $table->foreignId('id_empresa')->constrained('erp_empresas')->restrictOnDelete();
        $table->unique(['id_empresa', 'id']);
    }

    private function declaracion(Blueprint $table): void
    {
        $table->unsignedBigInteger('id_declaracion');
        $table->foreign(['id_empresa', 'id_declaracion'])->references(['id_empresa', 'id'])->on('tr_declaraciones')->restrictOnDelete();
    }

    private function auditar(Blueprint $table): void
    {
        $table->foreignId('id_usuario_creacion')->nullable()->constrained('users')->nullOnDelete();
        $table->foreignId('id_usuario_modificacion')->nullable()->constrained('users')->nullOnDelete();
        $table->timestamps();
    }
};
