<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePdfBrandingAdministration extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('pdf_branding_settings')) {
            Schema::create('pdf_branding_settings', function (Blueprint $table) {
                $table->increments('id');
                $table->string('id_empresa', 13);
                $table->string('view_name', 255);
                $table->string('view_key', 40);
                $table->string('display_name', 150);
                $table->string('module', 100)->nullable();
                $table->string('logo_path', 255);
                $table->string('original_filename', 255)->nullable();
                $table->string('position', 20)->default('top-right');
                $table->unsignedSmallInteger('width_mm')->default(38);
                $table->unsignedSmallInteger('height_mm')->default(20);
                $table->smallInteger('offset_x_mm')->default(0);
                $table->smallInteger('offset_y_mm')->default(0);
                $table->unsignedSmallInteger('opacity_percent')->default(100);
                $table->smallInteger('rotation_deg')->default(0);
                $table->boolean('keep_aspect_ratio')->default(true);
                $table->boolean('lock_content_position')->default(true);
                $table->boolean('repeat_on_each_page')->default(false);
                $table->boolean('enabled')->default(true);
                $table->string('id_usuariocrea', 40)->nullable();
                $table->string('id_usuariomod', 40)->nullable();
                $table->string('ip_creacion', 45)->nullable();
                $table->string('ip_modificacion', 45)->nullable();
                $table->timestamps();
                $table->unique(['id_empresa', 'view_key'], 'pdf_branding_company_view_unique');
                $table->index('id_empresa');
            });
        }

        $this->installMenu();
    }

    public function down()
    {
        if (Schema::hasTable('sidebar_items')) {
            DB::table('sidebar_items')->where('clave', 'system.pdf_branding')->delete();
        }
        if (Schema::hasTable('opcion')) {
            $option = DB::table('opcion')->where('clave', 'system.pdf_branding')->first();
            if ($option) {
                if (Schema::hasTable('opcion_usuario')) {
                    DB::table('opcion_usuario')->where('id_opcion', $option->id)->delete();
                }
                DB::table('opcion')->where('id', $option->id)->delete();
            }
        }
        Schema::dropIfExists('pdf_branding_settings');
    }

    private function installMenu()
    {
        if (! Schema::hasTable('users') || ! Schema::hasTable('opcion') || ! Schema::hasTable('sidebar_items')) {
            return;
        }
        $systemsUser = DB::table('users')->where('id_tipo_usuario', 1)->first();
        $parent = DB::table('sidebar_items')->where('clave', 'config.system_admin')->first();
        if (! $systemsUser || ! $parent) {
            return;
        }

        $now = date('Y-m-d H:i:s');
        $option = DB::table('opcion')->where('clave', 'system.pdf_branding')->first();
        $optionData = $this->existing('opcion', [
            'clave' => 'system.pdf_branding',
            'nombre' => 'Logos de documentos PDF',
            'descripcion' => 'Administra logos por empresa y formato PDF',
            'ruta' => '/sistema/pdf',
            'estado' => 1,
            'id_usuariocrea' => $systemsUser->id,
            'id_usuariomod' => $systemsUser->id,
            'ip_creacion' => '127.0.0.1',
            'ip_modificacion' => '127.0.0.1',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        if ($option) {
            unset($optionData['id_usuariocrea'], $optionData['ip_creacion'], $optionData['created_at']);
            DB::table('opcion')->where('id', $option->id)->update($optionData);
            $optionId = $option->id;
        } else {
            $optionId = DB::table('opcion')->insertGetId($optionData);
        }

        $sidebarData = $this->existing('sidebar_items', [
            'parent_id' => $parent->id,
            'id_opcion' => $optionId,
            'clave' => 'system.pdf_branding',
            'etiqueta' => 'Logos de documentos PDF',
            'traduccion' => 'tsidebar.menu_system_pdf_branding',
            'tipo' => 'enlace',
            'nombre_ruta' => 'pdf-branding.index',
            'url' => '/sistema/pdf',
            'icono' => 'fa fa-file-pdf-o',
            'orden' => 108,
            'patrones_activos' => 'sistema/pdf',
            'fijable' => 1,
            'estado' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $sidebar = DB::table('sidebar_items')->where('clave', 'system.pdf_branding')->first();
        if ($sidebar) {
            unset($sidebarData['created_at']);
            DB::table('sidebar_items')->where('id', $sidebar->id)->update($sidebarData);
        } else {
            DB::table('sidebar_items')->insert($sidebarData);
        }

        if (! Schema::hasTable('opcion_usuario')) {
            return;
        }
        $permission = DB::table('opcion_usuario')->where('id_opcion', $optionId)->where('id_tipo_usuario', 1)->first();
        if (! $permission) {
            DB::table('opcion_usuario')->insert($this->existing('opcion_usuario', [
                'id_opcion' => $optionId,
                'id_tipo_usuario' => 1,
                'estado' => 1,
                'id_usuariocrea' => $systemsUser->id,
                'id_usuariomod' => $systemsUser->id,
                'ip_creacion' => '127.0.0.1',
                'ip_modificacion' => '127.0.0.1',
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        } elseif (! (int) $permission->estado) {
            DB::table('opcion_usuario')->where('id', $permission->id)->update(['estado' => 1, 'updated_at' => $now]);
        }
    }

    private function existing($table, array $values)
    {
        return array_intersect_key($values, array_flip(Schema::getColumnListing($table)));
    }
}
