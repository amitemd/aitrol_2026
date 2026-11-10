<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EnsureFlatFilePublicInsurances extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('seguros')) {
            return;
        }

        $actor = DB::table('users')->where('id_tipo_usuario', 1)->where('estado', 1)
            ->orderBy('id')->value('id');
        if (! $actor) {
            $actor = DB::table('users')->orderBy('id')->value('id');
        }
        if (! $actor) {
            throw new RuntimeException('No se pueden registrar los seguros públicos porque no existe un usuario para la auditoría.');
        }

        $company = null;
        if (Schema::hasColumn('seguros', 'id_empresa')) {
            $company = DB::table('empresa')->where('estado', 1)
                ->orderBy('prioridad')->orderBy('id')->value('id');
            if (! $company) {
                $company = DB::table('empresa')->orderBy('id')->value('id');
            }
            if (! $company) {
                return;
            }
        }

        $definitions = [
            ['nombre' => 'IESS', 'descripcion' => 'INSTITUTO ECUATORIANO DE SEGURIDAD SOCIAL', 'color' => '#1687C9'],
            ['nombre' => 'ISSFA', 'descripcion' => 'INSTITUTO DE SEGURIDAD SOCIAL DE LAS FUERZAS ARMADAS', 'color' => '#3569A8'],
            ['nombre' => 'MSP', 'descripcion' => 'MINISTERIO DE SALUD PUBLICA', 'color' => '#E5A11A'],
            ['nombre' => 'ISSPOL', 'descripcion' => 'INSTITUTO DE SEGURIDAD SOCIAL DE LA POLICIA NACIONAL', 'color' => '#2E8B57'],
        ];

        foreach ($definitions as $definition) {
            $existing = DB::table('seguros')
                ->whereRaw('UPPER(TRIM(nombre)) = ?', [$definition['nombre']]);
            if (Schema::hasColumn('seguros', 'id_empresa')) {
                $existing->where('id_empresa', (string) $company);
            }

            $existingId = $existing->value('id');
            if ($existingId) {
                $updates = ['tipo' => 0, 'inactivo' => 1, 'updated_at' => now()];
                if (Schema::hasColumn('seguros', 'codigo_documental')) {
                    $updates['codigo_documental'] = $definition['nombre'];
                }
                if (Schema::hasColumn('seguros', 'id_seguro_tipos') && Schema::hasTable('seguro_tipos')) {
                    $updates['id_seguro_tipos'] = DB::table('seguro_tipos')
                        ->whereRaw('UPPER(nombre) = ?', ['PUBLICO'])->value('id');
                }
                DB::table('seguros')->where('id', $existingId)->update($updates);

                continue;
            }

            $row = [
                'nombre' => $definition['nombre'],
                'descripcion' => $definition['descripcion'],
                'tipo' => 0,
                'color' => $definition['color'],
                'id_usuariocrea' => (string) $actor,
                'id_usuariomod' => (string) $actor,
                'ip_creacion' => '127.0.0.1',
                'ip_modificacion' => '127.0.0.1',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (Schema::hasColumn('seguros', 'codigo_documental')) {
                $row['codigo_documental'] = $definition['nombre'];
            }
            if (Schema::hasColumn('seguros', 'id_seguro_tipos') && Schema::hasTable('seguro_tipos')) {
                $row['id_seguro_tipos'] = DB::table('seguro_tipos')
                    ->whereRaw('UPPER(nombre) = ?', ['PUBLICO'])->value('id');
            }
            if (Schema::hasColumn('seguros', 'id_empresa')) {
                $row['id_empresa'] = (string) $company;
            }

            DB::table('seguros')->insert($row);
        }
    }

    public function down()
    {
        // No se eliminan seguros porque pueden quedar referenciados por información clínica.
    }
}
