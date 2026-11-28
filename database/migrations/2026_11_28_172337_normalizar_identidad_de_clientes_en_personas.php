<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const COLUMNAS_DUPLICADAS = ['tipo_identificacion', 'identificacion', 'nombre', 'email', 'telefono', 'direccion'];

    private const CAMPOS_PERFIL_PERSONAL = [
        'primer_nombre', 'segundo_nombre', 'primer_apellido', 'segundo_apellido', 'celular', 'id_ciudad',
        'fecha_nacimiento', 'observacion', 'urbanizacion', 'etapa', 'manzana', 'villa', 'referencia',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasColumn('erp_clientes', 'tipo_identificacion')) {
            $this->vincularPersonas();
        }

        Schema::table('erp_clientes', function (Blueprint $table): void {
            $table->foreignId('id_persona')->nullable(false)->change();
        });

        foreach (['erp_clientes_identificacion_empresa', 'erp_clientes_nombre_index'] as $indice) {
            if (Schema::hasIndex('erp_clientes', $indice)) {
                Schema::table('erp_clientes', fn (Blueprint $table) => $table->dropIndex($indice));
            }
        }
        $columnas = array_values(array_intersect(self::COLUMNAS_DUPLICADAS, Schema::getColumnListing('erp_clientes')));
        if ($columnas !== []) {
            Schema::table('erp_clientes', fn (Blueprint $table) => $table->dropColumn($columnas));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('erp_clientes', function (Blueprint $table): void {
            $table->string('tipo_identificacion', 15)->nullable();
            $table->string('identificacion', 30)->nullable();
            $table->string('nombre', 360)->nullable();
            $table->string('email', 500)->nullable();
            $table->string('telefono', 100)->nullable();
            $table->string('direccion', 255)->nullable();
        });

        DB::table('erp_clientes as cliente')->join('personas as persona', 'persona.id', '=', 'cliente.id_persona')
            ->join('tipos_identificacion as tipo', 'tipo.id', '=', 'persona.id_tipo_identificacion')
            ->select('cliente.id', 'tipo.codigo as tipo_identificacion', 'persona.identificacion', 'persona.email', 'persona.telefono', 'persona.direccion')
            ->selectRaw("TRIM(CONCAT(persona.nombre, ' ', COALESCE(persona.apellido, ''))) AS nombre")
            ->chunkById(250, function ($clientes): void {
                foreach ($clientes as $cliente) {
                    DB::table('erp_clientes')->where('id', $cliente->id)
                        ->update(array_intersect_key((array) $cliente, array_flip(self::COLUMNAS_DUPLICADAS)));
                }
            }, 'cliente.id', 'id');

        Schema::table('erp_clientes', function (Blueprint $table): void {
            $table->string('tipo_identificacion', 15)->nullable(false)->change();
            $table->string('identificacion', 30)->nullable(false)->change();
            $table->string('nombre', 360)->nullable(false)->change();
            $table->foreignId('id_persona')->nullable()->change();
            $table->unique(['id_empresa', 'tipo_identificacion', 'identificacion'], 'erp_clientes_identificacion_empresa');
            $table->index('nombre');
        });
    }

    private function vincularPersonas(): void
    {
        $tipos = DB::table('tipos_identificacion')->pluck('id', 'codigo');
        $camposPersonales = array_flip([...self::CAMPOS_PERFIL_PERSONAL, ...self::COLUMNAS_DUPLICADAS,
            'id_tipo_identificacion', 'apellido', 'nombres', 'apellidos']);

        DB::transaction(function () use ($tipos, $camposPersonales): void {
            DB::table('erp_clientes')->chunkById(250, function ($clientes) use ($tipos, $camposPersonales): void {
                foreach ($clientes as $cliente) {
                    $perfil = json_decode($cliente->perfil ?? 'null', true, flags: JSON_THROW_ON_ERROR);
                    $perfil = is_array($perfil) ? $perfil : [];
                    $datos = array_intersect_key($perfil, array_flip(self::CAMPOS_PERFIL_PERSONAL)) + [
                        'nombre' => $perfil['nombres'] ?? $perfil['nombre'] ?? $cliente->nombre,
                        'apellido' => $perfil['apellidos'] ?? $perfil['apellido'] ?? null,
                        'email' => $perfil['email'] ?? $cliente->email,
                        'telefono' => $perfil['telefono'] ?? $cliente->telefono,
                        'direccion' => $perfil['direccion'] ?? $cliente->direccion,
                    ];
                    $documento = ['id_tipo_identificacion' => $tipos->get($cliente->tipo_identificacion), 'identificacion' => $cliente->identificacion];
                    $personas = DB::table('personas');
                    $persona = $cliente->id_persona === null
                        ? (clone $personas)->where($documento)->lockForUpdate()->first()
                        : (clone $personas)->where('id', $cliente->id_persona)->lockForUpdate()->first();
                    if ($persona === null || $persona->id_tipo_identificacion === null || blank($persona->identificacion)) {
                        if (! $documento['id_tipo_identificacion'] || blank($documento['identificacion'])
                            || ($persona?->id_tipo_identificacion !== null && $persona->id_tipo_identificacion != $documento['id_tipo_identificacion'])
                            || (filled($persona?->identificacion) && $persona->identificacion !== $documento['identificacion'])) {
                            throw new RuntimeException("El cliente {$cliente->id} tiene una identificación incompleta o incompatible.");
                        }
                        $datos += $documento;
                    }

                    if ($persona === null) {
                        $idPersona = $personas->insertGetId($datos + ['created_at' => $cliente->created_at, 'updated_at' => $cliente->updated_at]);
                    } else {
                        $idPersona = $persona->id;
                        $faltantes = array_filter($datos, fn ($valor, string $campo): bool => filled($valor) && blank($persona->{$campo}), ARRAY_FILTER_USE_BOTH);
                        if ($faltantes !== []) {
                            $personas->where('id', $idPersona)->update($faltantes);
                        }
                    }

                    $actualizar = [];
                    if ($cliente->id_persona !== $idPersona) {
                        $actualizar['id_persona'] = $idPersona;
                    }
                    if (array_intersect_key($perfil, $camposPersonales) !== []) {
                        $comercial = array_diff_key($perfil, $camposPersonales);
                        $actualizar['perfil'] = $comercial === [] ? null : json_encode($comercial, JSON_THROW_ON_ERROR);
                    }
                    if ($actualizar !== []) {
                        DB::table('erp_clientes')->where('id', $cliente->id)->update($actualizar);
                    }
                }
            });
        });
    }
};
