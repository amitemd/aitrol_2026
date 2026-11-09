<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateAgendaDocumentRequirements extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('agenda_documento_requisitos')) {
            Schema::create('agenda_documento_requisitos', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedInteger('id_seguro');
                $table->unsignedInteger('id_documento');
                $table->string('id_empresa', 32)->nullable();
                $table->tinyInteger('proc_consul')->default(1);
                $table->boolean('obligatorio')->default(true);
                $table->unsignedInteger('secuencia')->default(0);
                $table->boolean('estado')->default(true);
                $table->string('id_usuariocrea', 32)->nullable();
                $table->string('id_usuariomod', 32)->nullable();
                $table->string('ip_creacion', 45)->nullable();
                $table->string('ip_modificacion', 45)->nullable();
                $table->timestamps();
                $table->unique(['id_seguro', 'id_documento', 'proc_consul', 'id_empresa'], 'agenda_doc_req_unique');
                $table->index(['id_seguro', 'proc_consul', 'id_empresa', 'estado'], 'agenda_doc_req_lookup');
            });
        }

        $this->seedLegacyRequirements();
        $this->publishMenu();
    }

    public function down()
    {
        Schema::dropIfExists('agenda_documento_requisitos');
        if (Schema::hasTable('new_db_menu')) {
            DB::table('new_db_menu')->where('id_menu', 741)->delete();
        }
    }

    private function seedLegacyRequirements()
    {
        if (! Schema::hasTable('agenda_documento_requisitos') || ! Schema::hasTable('documento') || ! Schema::hasTable('seguros')) {
            return;
        }

        $now = date('Y-m-d H:i:s');
        $actorId = $this->actorId();
        foreach (DB::table('seguros')->get() as $insurance) {
            $documents = DB::table('documento')->where('estado', 1)->where('est_doc_tarea', 0)
                ->where(function ($query) use ($insurance) {
                    $query->whereNull('tipo_seguro');
                    if (isset($insurance->tipo)) {
                        $query->orWhere('tipo_seguro', $insurance->tipo);
                    }
                })
                ->where(function ($query) use ($insurance) {
                    $query->whereNull('id_seguro')->orWhere('id_seguro', $insurance->id);
                })
                ->orderBy('secuencia')->orderBy('id')->get();

            foreach ($documents as $document) {
                $exists = DB::table('agenda_documento_requisitos')->where([
                    'id_seguro' => $insurance->id,
                    'id_documento' => $document->id,
                    'proc_consul' => $document->proc_consul,
                ])->where(function ($query) use ($document) {
                    if ($document->id_empresa === null) {
                        $query->whereNull('id_empresa');
                    } else {
                        $query->where('id_empresa', $document->id_empresa);
                    }
                })->exists();
                if ($exists) {
                    continue;
                }

                DB::table('agenda_documento_requisitos')->insert([
                    'id_seguro' => $insurance->id,
                    'id_documento' => $document->id,
                    'id_empresa' => $document->id_empresa,
                    'proc_consul' => $document->proc_consul,
                    'obligatorio' => (int) $document->obligatorio === 1,
                    'secuencia' => (int) $document->secuencia,
                    'estado' => 1,
                    'id_usuariocrea' => $actorId,
                    'id_usuariomod' => $actorId,
                    'ip_creacion' => '127.0.0.1',
                    'ip_modificacion' => '127.0.0.1',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    private function publishMenu()
    {
        if (! Schema::hasTable('new_db_menu')) {
            return;
        }
        $now = date('Y-m-d H:i:s');
        $actorId = $this->actorId();
        $data = [
            'nombre_menu' => 'Documentos requeridos por seguro',
            'icon_menu' => '<i class="fas fa-file-upload nav-icon"></i>',
            'controlador_menu' => 'documentos-requeridos',
            'funcion_menu' => 'index',
            'orden_menu' => 70,
            'estado_menu' => 1,
            'id_pertenece_menu' => 698,
            'id_usuario_modificacion_menu' => $actorId,
            'updated_at_menu' => $now,
            'ip_modificacion_menu' => '127.0.0.1',
            'color_menu' => null,
        ];
        if (DB::table('new_db_menu')->where('id_menu', 741)->exists()) {
            DB::table('new_db_menu')->where('id_menu', 741)->update($data);
        } else {
            DB::table('new_db_menu')->insert(array_merge($data, [
                'id_menu' => 741,
                'id_usuario_creacion_menu' => $actorId,
                'created_at_menu' => $now,
                'ip_creacion_menu' => '127.0.0.1',
            ]));
        }
        $this->grantMenuPermissions($now);
    }

    private function grantMenuPermissions($now)
    {
        if (! Schema::hasTable('new_db_permisos_usuario')) {
            return;
        }
        foreach (DB::table('new_db_permisos_usuario')->get() as $permission) {
            $ids = json_decode($permission->id_submenu_permiso, true);
            $ids = is_array($ids) ? array_map('intval', $ids) : [];
            if (! in_array(688, $ids, true) && ! in_array(683, $ids, true)) {
                continue;
            }
            $ids[] = 741;
            $ids[] = 10;
            $ids[] = 698;
            $ids = array_values(array_unique($ids));
            sort($ids);
            DB::table('new_db_permisos_usuario')->where('id_permiso', $permission->id_permiso)->update([
                'id_submenu_permiso' => json_encode($ids),
                'updated_at_permiso' => $now,
            ]);
        }
    }

    private function actorId()
    {
        if (Schema::hasTable('users')) {
            $id = DB::table('users')->where('id_tipo_usuario', 1)->value('id');
            if ($id) {
                return (string) $id;
            }
        }

        return env('BOOTSTRAP_SYSTEMS_ID', '0000000001');
    }
}
