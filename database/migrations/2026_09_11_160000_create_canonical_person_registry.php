<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateCanonicalPersonRegistry extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('personas')) {
            Schema::create('personas', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('identificacion', 32)->unique();
                $table->string('tipo_documento', 20)->nullable();
                $table->string('tipo_persona', 12)->default('NATURAL');
                $table->string('nombre1', 100)->nullable();
                $table->string('nombre2', 100)->nullable();
                $table->string('apellido1', 100)->nullable();
                $table->string('apellido2', 100)->nullable();
                $table->string('razon_social', 255)->nullable();
                $table->string('nombre_comercial', 255)->nullable();
                $table->date('fecha_nacimiento')->nullable();
                $table->string('sexo', 20)->nullable();
                $table->string('email', 191)->nullable();
                $table->string('telefono1', 30)->nullable();
                $table->string('telefono2', 30)->nullable();
                $table->text('direccion')->nullable();
                $table->string('id_usuariocrea', 32)->nullable();
                $table->string('id_usuariomod', 32)->nullable();
                $table->string('ip_creacion', 45)->nullable();
                $table->string('ip_modificacion', 45)->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('persona_empresa')) {
            Schema::create('persona_empresa', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('persona_id');
                $table->string('id_empresa', 32);
                $table->string('rol', 24);
                $table->boolean('estado')->default(true);
                $table->string('id_usuariocrea', 32)->nullable();
                $table->string('id_usuariomod', 32)->nullable();
                $table->string('ip_creacion', 45)->nullable();
                $table->string('ip_modificacion', 45)->nullable();
                $table->timestamps();
                $table->unique(['persona_id', 'id_empresa', 'rol'], 'persona_empresa_role_unique');
                $table->index(['id_empresa', 'rol', 'estado'], 'persona_empresa_company_role');
            });
        }

        foreach (['paciente', 'users', 'proveedor', 'ct_clientes'] as $table) {
            if (Schema::hasTable($table) && ! Schema::hasColumn($table, 'persona_id')) {
                Schema::table($table, function (Blueprint $blueprint) {
                    $blueprint->unsignedBigInteger('persona_id')->nullable()->index();
                });
            }
        }

        $this->backfillPeople();
        $this->backfillCompanyRoles();
    }

    public function down()
    {
        foreach (['paciente', 'users', 'proveedor', 'ct_clientes'] as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'persona_id')) {
                Schema::table($table, function (Blueprint $blueprint) {
                    $blueprint->dropColumn('persona_id');
                });
            }
        }
        Schema::dropIfExists('persona_empresa');
        Schema::dropIfExists('personas');
    }

    private function backfillPeople()
    {
        $sources = [
            ['table' => 'paciente', 'key' => 'id', 'kind' => 'NATURAL'],
            ['table' => 'users', 'key' => 'id', 'kind' => 'NATURAL'],
            ['table' => 'proveedor', 'key' => 'id', 'kind' => 'JURIDICA'],
            ['table' => 'ct_clientes', 'key' => 'identificacion', 'kind' => 'JURIDICA'],
        ];

        foreach ($sources as $source) {
            if (! Schema::hasTable($source['table'])) {
                continue;
            }
            DB::table($source['table'])->orderBy($source['key'])->chunk(500, function ($rows) use ($source) {
                foreach ($rows as $row) {
                    $identification = $this->normalize(isset($row->{$source['key']}) ? $row->{$source['key']} : null);
                    if ($identification === '') {
                        continue;
                    }
                    $person = DB::table('personas')->where('identificacion', $identification)->first();
                    if (! $person) {
                        $personId = DB::table('personas')->insertGetId($this->personValues($row, $identification, $source['kind']));
                    } else {
                        $personId = $person->id;
                        $candidate = $this->personValues($row, $identification, $source['kind']);
                        $missing = [];
                        foreach ($candidate as $column => $value) {
                            if (in_array($column, ['identificacion', 'tipo_persona', 'created_at'], true)) {
                                continue;
                            }
                            if (($person->{$column} === null || $person->{$column} === '') && $value !== null && $value !== '') {
                                $missing[$column] = $value;
                            }
                        }
                        if ($missing) {
                            DB::table('personas')->where('id', $personId)->update($missing);
                        }
                    }
                    DB::table($source['table'])->where($source['key'], $row->{$source['key']})->update(['persona_id' => $personId]);
                }
            });
        }
    }

    private function backfillCompanyRoles()
    {
        $relations = [
            ['pivot' => 'paciente_empresa', 'source' => 'paciente', 'pivot_key' => 'id_paciente', 'source_key' => 'id', 'role' => 'PACIENTE'],
            ['pivot' => 'usuario_empresa', 'source' => 'users', 'pivot_key' => 'id_usuario', 'source_key' => 'id', 'role' => 'USUARIO'],
            ['pivot' => 'ct_clientes_empresa', 'source' => 'ct_clientes', 'pivot_key' => 'id_cliente', 'source_key' => 'identificacion', 'role' => 'CLIENTE'],
            ['pivot' => 'ct_acreedores', 'source' => 'proveedor', 'pivot_key' => 'id_proveedor', 'source_key' => 'id', 'role' => 'PROVEEDOR'],
        ];
        $now = date('Y-m-d H:i:s');
        foreach ($relations as $relation) {
            if (! Schema::hasTable($relation['pivot']) || ! Schema::hasTable($relation['source'])) {
                continue;
            }
            $rows = DB::table($relation['pivot'].' as membership')
                ->join($relation['source'].' as source', 'source.'.$relation['source_key'], '=', 'membership.'.$relation['pivot_key'])
                ->whereNotNull('membership.id_empresa')->where('membership.id_empresa', '<>', '')
                ->whereNotNull('source.persona_id')
                ->select('source.persona_id', 'membership.id_empresa')->distinct()->get();
            foreach ($rows as $row) {
                DB::table('persona_empresa')->insertOrIgnore([
                    'persona_id' => $row->persona_id, 'id_empresa' => $row->id_empresa,
                    'rol' => $relation['role'], 'estado' => 1,
                    'ip_creacion' => '127.0.0.1', 'ip_modificacion' => '127.0.0.1',
                    'created_at' => $now, 'updated_at' => $now,
                ]);
            }
        }
    }

    private function personValues($row, $identification, $kind)
    {
        $now = date('Y-m-d H:i:s');

        return [
            'identificacion' => $identification,
            'tipo_documento' => isset($row->tipo_documento) ? $row->tipo_documento : null,
            'tipo_persona' => $kind,
            'nombre1' => isset($row->nombre1) ? $row->nombre1 : null,
            'nombre2' => isset($row->nombre2) ? $row->nombre2 : null,
            'apellido1' => isset($row->apellido1) ? $row->apellido1 : null,
            'apellido2' => isset($row->apellido2) ? $row->apellido2 : null,
            'razon_social' => isset($row->razonsocial) ? $row->razonsocial : (isset($row->nombre) ? $row->nombre : null),
            'nombre_comercial' => isset($row->nombrecomercial) ? $row->nombrecomercial : null,
            'fecha_nacimiento' => isset($row->fecha_nacimiento) && $row->fecha_nacimiento ? $row->fecha_nacimiento : null,
            'sexo' => isset($row->sexo) ? $row->sexo : null,
            'email' => isset($row->email) ? $row->email : null,
            'telefono1' => isset($row->telefono1) ? $row->telefono1 : null,
            'telefono2' => isset($row->telefono2) ? $row->telefono2 : null,
            'direccion' => isset($row->direccion) ? $row->direccion : null,
            'ip_creacion' => '127.0.0.1', 'ip_modificacion' => '127.0.0.1',
            'created_at' => $now, 'updated_at' => $now,
        ];
    }

    private function normalize($value)
    {
        return strtoupper((string) preg_replace('/[^A-Za-z0-9]/', '', trim((string) $value)));
    }
}
