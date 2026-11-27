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
        foreach (['erp_empresas', ...$this->tablas] as $tabla) {
            if (! Schema::hasTable($tabla)) {
                throw new RuntimeException("Falta la tabla {$tabla}. Revisa migrate:status y aplica su migración de creación antes de separar los datos por empresa.");
            }
        }

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
            $this->prepararEmpresa($tabla);
            if ($empresa !== null) {
                DB::table($tabla)->whereNull('id_empresa')->update(['id_empresa' => $empresa]);
            }
        }
        foreach (['users', 'erp_clientes'] as $tabla) {
            if (! Schema::hasIndex($tabla, $tabla.'_id_persona_index')) {
                Schema::table($tabla, fn (Blueprint $table) => $table->index('id_persona'));
            }
        }
        if (! Schema::hasColumn('users', 'foto')) {
            Schema::table('users', fn (Blueprint $table) => $table->string('foto')->nullable());
        }
        $this->reemplazarUnico('users', 'users_id_persona_unique', ['id_empresa', 'id_persona']);
        $this->reemplazarUnico('erp_clientes', 'erp_clientes_identificacion_unique', ['id_empresa', 'tipo_identificacion', 'identificacion'], 'erp_clientes_identificacion_empresa');
        $this->reemplazarUnico('erp_clientes', 'erp_clientes_id_persona_unique', ['id_empresa', 'id_persona']);
        $this->reemplazarUnico('erp_proveedores', 'erp_proveedores_identificacion_unique', ['id_empresa', 'tipo_identificacion', 'identificacion'], 'erp_proveedores_identificacion_empresa');
        $this->reemplazarUnico('roles', 'roles_nombre_unique', ['id_empresa', 'nombre']);
        $this->reemplazarUnico('permisos', 'permisos_opcion_accion_unique', ['id_empresa', 'opcion', 'accion']);

        // Sin asignaciones previas, estos registros solo eran el catálogo inicial.
        DB::table('permisos')->whereNull('id_empresa')->delete();
    }

    private function prepararEmpresa(string $tabla): void
    {
        if (! Schema::hasColumn($tabla, 'id_empresa')) {
            Schema::table($tabla, fn (Blueprint $table) => $table->foreignId('id_empresa')->nullable());
        }
        if (! Schema::hasIndex($tabla, $tabla.'_id_empresa_index')) {
            Schema::table($tabla, fn (Blueprint $table) => $table->index('id_empresa'));
        }
        $tieneForanea = collect(Schema::getForeignKeys($tabla))->contains(
            fn (array $foranea): bool => $foranea['columns'] === ['id_empresa']
                && $foranea['foreign_table'] === 'erp_empresas' && $foranea['foreign_columns'] === ['id'],
        );
        if (! $tieneForanea) {
            Schema::table($tabla, fn (Blueprint $table) => $table->foreign('id_empresa')->references('id')->on('erp_empresas')->restrictOnDelete());
        }
    }

    private function reemplazarUnico(string $tabla, string $anterior, array $columnas, ?string $nombre = null): void
    {
        if (! Schema::hasIndex($tabla, $columnas, 'unique')) {
            Schema::table($tabla, fn (Blueprint $table) => $table->unique($columnas, $nombre));
        }
        if (Schema::hasIndex($tabla, $anterior, 'unique')) {
            Schema::table($tabla, fn (Blueprint $table) => $table->dropUnique($anterior));
        }
    }

    public function down(): void
    {
        throw new RuntimeException('Volver a un esquema sin empresas requiere restaurar el respaldo anterior a la migración.');
    }
};
