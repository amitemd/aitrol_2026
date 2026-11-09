<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateV3CrashCartModule extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('v3_crash_cart_controls')) {
            Schema::create('v3_crash_cart_controls', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('id_empresa', 13);
                $table->unsignedInteger('id_bodega');
                $table->string('bodega_nombre', 255);
                $table->date('fecha');
                $table->unsignedTinyInteger('tipo');
                $table->string('servicio', 100);
                $table->string('responsable_id', 13);
                $table->string('responsable_nombre', 255);
                $table->string('reference_basis', 20);
                $table->text('observacion')->nullable();
                $table->unsignedTinyInteger('estado')->default(1);
                // NULL releases the unique daily slot when a control is cancelled.
                $table->unsignedTinyInteger('active_slot')->nullable()->default(1);
                $table->unsignedInteger('version')->default(1);
                $table->string('id_usuariocrea', 13);
                $table->string('id_usuariomod', 13);
                $table->string('ip_creacion', 45)->nullable();
                $table->string('ip_modificacion', 45)->nullable();
                $table->text('motivo_anulacion')->nullable();
                $table->timestamps();
                $table->unique(['id_empresa', 'id_bodega', 'fecha', 'tipo', 'servicio', 'active_slot'], 'v3_cart_daily_unique');
                $table->index(['id_empresa', 'fecha', 'estado'], 'v3_cart_company_date');
            });
        }
        if (! Schema::hasTable('v3_crash_cart_details')) {
            Schema::create('v3_crash_cart_details', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('control_id');
                $table->unsignedBigInteger('inventory_id');
                $table->integer('producto_id');
                $table->string('producto_nombre', 255);
                $table->string('producto_codigo', 50)->nullable();
                $table->string('lote', 255)->nullable();
                $table->string('serie', 90)->nullable();
                $table->date('fecha_caducidad')->nullable();
                $table->string('presentacion', 100)->nullable();
                $table->string('concentracion', 100)->nullable();
                $table->unsignedInteger('referencia_cantidad');
                $table->unsignedInteger('stock');
                $table->string('observacion', 500)->nullable();
                $table->timestamps();
                $table->unique(['control_id', 'inventory_id'], 'v3_cart_detail_unique');
                $table->foreign('control_id')->references('id')->on('v3_crash_cart_controls');
            });
        }
        if (! Schema::hasTable('v3_crash_cart_events')) {
            Schema::create('v3_crash_cart_events', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('control_id');
                $table->string('id_empresa', 13);
                $table->string('accion', 20);
                $table->unsignedInteger('version');
                $table->string('actor_id', 13);
                $table->string('ip', 45)->nullable();
                $table->longText('snapshot');
                $table->timestamp('created_at')->nullable();
                $table->foreign('control_id')->references('id')->on('v3_crash_cart_controls');
                $table->index(['id_empresa', 'control_id'], 'v3_cart_event_company');
            });
        }
        $this->publishMenu();
    }

    private function publishMenu()
    {
        if (! Schema::hasTable('new_db_menu') || ! Schema::hasTable('users')) {
            return;
        }
        if (DB::table('new_db_menu')->where('controlador_menu', 'carro-paro')->exists()) {
            return;
        }
        $actor = DB::table('users')->where('id_tipo_usuario', 1)->value('id');
        if (! $actor || ! DB::table('new_db_menu')->where('id_menu', 144)->exists()) {
            return;
        }
        // Allocate a free identifier; never replace an installation's menu item.
        $id = max(9010, (int) DB::table('new_db_menu')->max('id_menu') + 1);
        $now = date('Y-m-d H:i:s');
        DB::table('new_db_menu')->insert([
            'id_menu' => $id, 'nombre_menu' => 'Carro de paro',
            'icon_menu' => '<i class="fas fa-medkit nav-icon"></i>',
            'controlador_menu' => 'carro-paro', 'funcion_menu' => 'index',
            'id_pertenece_menu' => 144, 'orden_menu' => 30, 'estado_menu' => 1,
            'id_usuario_creacion_menu' => $actor, 'id_usuario_modificacion_menu' => $actor,
            'created_at_menu' => $now, 'updated_at_menu' => $now,
            'ip_creacion_menu' => '127.0.0.1', 'ip_modificacion_menu' => '127.0.0.1', 'color_menu' => null,
        ]);
        // Existing grants/denials remain unchanged; systems has intrinsic V3 access.
        if (Schema::hasTable('new_db_permisos_usuario')) {
            $permissions = DB::table('new_db_permisos_usuario as p')->join('users as u', 'u.id', '=', 'p.id_usuario_permiso')
                ->where('u.id_tipo_usuario', 1)->select('p.id_permiso', 'p.id_submenu_permiso')->get();
            foreach ($permissions as $permission) {
                $ids = json_decode($permission->id_submenu_permiso, true);
                if (! is_array($ids)) {
                    continue;
                }
                $ids = array_values(array_unique(array_merge(array_map('intval', $ids), [576, 144, $id])));
                DB::table('new_db_permisos_usuario')->where('id_permiso', $permission->id_permiso)
                    ->update(['id_submenu_permiso' => json_encode($ids), 'updated_at_permiso' => $now]);
            }
        }
    }

    public function down()
    {
        // Intentionally preserve recorded controls and audit history on rollback.
        // Disable the new menu instead of deleting clinical operational records.
        if (Schema::hasTable('new_db_menu')) {
            DB::table('new_db_menu')->where('controlador_menu', 'carro-paro')->update(['estado_menu' => 0]);
        }
    }
}
