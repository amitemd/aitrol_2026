<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipos_identificacion', function (Blueprint $table): void {
            $table->id();
            $table->string('codigo', 15)->unique();
            $table->string('nombre', 80);
        });
        $tipos = ['cedula' => 1, 'ruc' => 2, 'pasaporte' => 3];
        DB::table('tipos_identificacion')->insert([
            ['id' => 1, 'codigo' => 'cedula', 'nombre' => 'Cédula'],
            ['id' => 2, 'codigo' => 'ruc', 'nombre' => 'RUC'],
            ['id' => 3, 'codigo' => 'pasaporte', 'nombre' => 'Pasaporte'],
        ]);

        Schema::create('personas', function (Blueprint $table): void {
            $table->id();
            $table->string('identificacion', 30)->nullable();
            $table->foreignId('id_tipo_identificacion')->nullable()->constrained('tipos_identificacion')->restrictOnDelete();
            $table->string('nombre', 255);
            $table->string('apellido', 100)->nullable();
            $table->string('telefono', 100)->nullable();
            $table->string('celular', 100)->nullable();
            $table->string('direccion')->nullable();
            $table->unsignedBigInteger('id_ciudad')->nullable()->index();
            $table->string('email', 500)->nullable();
            $table->text('observacion')->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->string('urbanizacion', 100)->nullable();
            $table->string('etapa', 100)->nullable();
            $table->string('manzana', 100)->nullable();
            $table->string('villa', 100)->nullable();
            $table->text('referencia')->nullable();
            $table->string('foto')->nullable();
            $table->foreignId('id_usuario_creacion')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('id_usuario_modificacion')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['id_tipo_identificacion', 'identificacion']);
        });

        foreach (['users', 'erp_clientes'] as $tabla) {
            if (! Schema::hasTable($tabla)) {
                continue;
            }

            Schema::table($tabla, function (Blueprint $table): void {
                $table->foreignId('id_persona')->nullable()->unique()->constrained('personas')->restrictOnDelete();
            });
        }

        DB::transaction(function () use ($tipos): void {
            DB::table('users')->whereNull('id_persona')->chunkById(200, function ($usuarios): void {
                foreach ($usuarios as $usuario) {
                    $id = DB::table('personas')->insertGetId([
                        'nombre' => $usuario->name, 'email' => $usuario->email,
                        'created_at' => $usuario->created_at, 'updated_at' => $usuario->updated_at,
                    ]);
                    DB::table('users')->where('id', $usuario->id)->update(['id_persona' => $id]);
                }
            });

            if (! Schema::hasTable('erp_clientes')) {
                return;
            }

            DB::table('erp_clientes')->whereNull('id_persona')->chunkById(200, function ($clientes) use ($tipos): void {
                foreach ($clientes as $cliente) {
                    $id = DB::table('personas')->insertGetId([
                        'id_tipo_identificacion' => $tipos[$cliente->tipo_identificacion], 'identificacion' => $cliente->identificacion,
                        'nombre' => $cliente->nombre, 'email' => $cliente->email, 'telefono' => $cliente->telefono,
                        'direccion' => $cliente->direccion, 'created_at' => $cliente->created_at, 'updated_at' => $cliente->updated_at,
                    ]);
                    DB::table('erp_clientes')->where('id', $cliente->id)->update(['id_persona' => $id]);
                }
            });
        });
    }

    public function down(): void
    {
        foreach (['erp_clientes', 'users'] as $tabla) {
            if (Schema::hasColumn($tabla, 'id_persona')) {
                Schema::table($tabla, function (Blueprint $table): void {
                    $table->dropForeign(['id_persona']);
                    $table->dropUnique(['id_persona']);
                    $table->dropColumn('id_persona');
                });
            }
        }
        Schema::dropIfExists('personas');
        Schema::dropIfExists('tipos_identificacion');
    }
};
