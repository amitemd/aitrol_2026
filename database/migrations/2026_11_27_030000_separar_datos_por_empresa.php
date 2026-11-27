<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tablas = ['users', 'personal_access_tokens', 'erp_clientes', 'erp_proveedores', 'roles', 'permisos', 'rol_usuario', 'permiso_rol', 'permiso_usuario'];

    public function up(): void
    {
        $empresa = config('tenencia.empresa_legacy');
        if ($empresa !== null && ! DB::table('erp_empresas')->where('id', $empresa)->whereNull('deleted_at')->exists()) {
            throw new RuntimeException('AITROL_EMPRESA_INICIAL debe identificar una empresa activa.');
        }

        foreach (['erp_clientes', 'erp_proveedores', 'roles', 'rol_usuario', 'permiso_rol', 'permiso_usuario'] as $tabla) {
            if ($empresa === null && DB::table($tabla)->exists()) {
                throw new RuntimeException('Hay datos anteriores sin empresa. Define AITROL_EMPRESA_INICIAL antes de migrar.');
            }
        }

        foreach ($this->tablas as $tabla) {
            Schema::table($tabla, function (Blueprint $table): void {
                $table->foreignId('id_empresa')->nullable()->index()->constrained('erp_empresas')->restrictOnDelete();
            });
            if ($empresa !== null) DB::table($tabla)->update(['id_empresa' => $empresa]);
        }
        foreach (['users', 'erp_clientes'] as $tabla) {
            Schema::table($tabla, fn (Blueprint $table) => $table->index('id_persona'));
        }
        Schema::table('users', function (Blueprint $table): void {
            $table->string('foto')->nullable();
            $table->dropUnique(['id_persona']);
            $table->unique(['id_empresa', 'id_persona']);
        });
        Schema::table('erp_clientes', function (Blueprint $table): void {
            $table->dropUnique('erp_clientes_identificacion_unique');
            $table->dropUnique(['id_persona']);
            $table->unique(['id_empresa', 'tipo_identificacion', 'identificacion'], 'erp_clientes_identificacion_empresa');
            $table->unique(['id_empresa', 'id_persona']);
        });
        Schema::table('erp_proveedores', function (Blueprint $table): void {
            $table->dropUnique('erp_proveedores_identificacion_unique');
            $table->unique(['id_empresa', 'tipo_identificacion', 'identificacion'], 'erp_proveedores_identificacion_empresa');
        });
        Schema::table('roles', function (Blueprint $table): void {
            $table->dropUnique(['nombre']);
            $table->unique(['id_empresa', 'nombre']);
        });
        Schema::table('permisos', function (Blueprint $table): void {
            $table->dropUnique(['opcion', 'accion']);
            $table->unique(['id_empresa', 'opcion', 'accion']);
        });

        // Sin asignaciones previas, estos registros solo eran el catálogo inicial.
        DB::table('permisos')->whereNull('id_empresa')->delete();
    }

    public function down(): void
    {
        throw new RuntimeException('Volver a un esquema sin empresas requiere restaurar el respaldo anterior a la migración.');
    }
};
