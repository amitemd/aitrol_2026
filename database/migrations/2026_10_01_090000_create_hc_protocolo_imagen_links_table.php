<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateHcProtocoloImagenLinksTable extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('hc_protocolo_imagen_links')) {
            Schema::create('hc_protocolo_imagen_links', function (Blueprint $table) {
                $table->increments('id');
                // hc_protocolo.id es INT con signo en la base histórica.
                $table->integer('id_hc_protocolo');
                $table->text('url');
                $table->string('id_usuariocrea', 20)->nullable();
                $table->string('id_usuariomod', 20)->nullable();
                $table->string('ip_creacion', 45)->nullable();
                $table->string('ip_modificacion', 45)->nullable();
                $table->timestamps();

                $table->index('id_hc_protocolo', 'hc_protocolo_imagen_links_protocolo_idx');
            });
        }

        $foreignExiste = DB::table('information_schema.TABLE_CONSTRAINTS')
            ->where('CONSTRAINT_SCHEMA', DB::connection()->getDatabaseName())
            ->where('TABLE_NAME', 'hc_protocolo_imagen_links')
            ->where('CONSTRAINT_NAME', 'hc_protocolo_imagen_links_protocolo_fk')
            ->exists();

        if (! $foreignExiste) {
            // También repara una ejecución interrumpida de la primera versión de la migración.
            DB::statement('ALTER TABLE hc_protocolo_imagen_links MODIFY id_hc_protocolo INT NOT NULL');
            Schema::table('hc_protocolo_imagen_links', function (Blueprint $table) {
                $table->foreign('id_hc_protocolo', 'hc_protocolo_imagen_links_protocolo_fk')
                    ->references('id')
                    ->on('hc_protocolo')
                    ->onDelete('cascade');
            });
        }

        // Conserva los enlaces que se registraron antes de admitir varios por protocolo.
        if (Schema::hasColumn('hc_protocolo', 'link_imagenes')) {
            DB::table('hc_protocolo')
                ->whereNotNull('link_imagenes')
                ->where('link_imagenes', '<>', '')
                ->orderBy('id')
                ->chunk(200, function ($protocolos) {
                    foreach ($protocolos as $protocolo) {
                        $url = trim((string) $protocolo->link_imagenes);
                        if ($url === '') {
                            continue;
                        }

                        $existe = DB::table('hc_protocolo_imagen_links')
                            ->where('id_hc_protocolo', $protocolo->id)
                            ->where('url', $url)
                            ->exists();

                        if (! $existe) {
                            DB::table('hc_protocolo_imagen_links')->insert([
                                'id_hc_protocolo' => $protocolo->id,
                                'url' => $url,
                                'id_usuariocrea' => $protocolo->id_usuariocrea ?? null,
                                'id_usuariomod' => $protocolo->id_usuariomod ?? null,
                                'ip_creacion' => $protocolo->ip_creacion ?? null,
                                'ip_modificacion' => $protocolo->ip_modificacion ?? null,
                                'created_at' => $protocolo->created_at ?? now(),
                                'updated_at' => $protocolo->updated_at ?? now(),
                            ]);
                        }
                    }
                });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('hc_protocolo_imagen_links');
    }
}
