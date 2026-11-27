<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rh_empleados', function (Blueprint $tabla): void {
            $this->sucursal($tabla);
            $tabla->string('codigo', 30);
            $tabla->string('nombre', 160);
            $tabla->string('identificacion', 40)->nullable();
            $tabla->string('correo', 150)->nullable();
            $tabla->date('ingreso');
            $tabla->date('salida')->nullable();
            $tabla->unsignedBigInteger('id_departamento')->nullable();
            $tabla->unsignedBigInteger('id_cargo')->nullable();
            $tabla->unique(['id_empresa', 'codigo']);
        });

        Schema::create('rh_contratos', function (Blueprint $tabla): void {
            $this->sucursal($tabla);
            $this->relacion($tabla, 'id_empleado', 'rh_empleados', 'contrato_empleado');
            $tabla->string('tipo', 40);
            $tabla->date('inicio');
            $tabla->date('fin')->nullable();
            $tabla->unsignedBigInteger('sueldo');
            $tabla->decimal('horas_semanales', 5, 2);
            $tabla->index(['id_empresa', 'id_sucursal', 'id_empleado', 'inicio'], 'rh_contrato_vigencia');
        });

        Schema::create('rh_nominas', function (Blueprint $tabla): void {
            $this->sucursal($tabla);
            $tabla->date('periodo');
            $tabla->string('estado', 18)->default('borrador');
            $tabla->timestamp('cerrada_at')->nullable();
            $tabla->unsignedBigInteger('cerrada_por')->nullable();
            $tabla->timestamp('pagada_at')->nullable();
            $tabla->unsignedBigInteger('pagada_por')->nullable();
            $tabla->string('referencia_pago', 80)->nullable();
            $tabla->unique(['id_empresa', 'id_sucursal', 'periodo']);
        });

        Schema::create('rh_procesos', function (Blueprint $tabla): void {
            $this->sucursal($tabla);
            $this->relacion($tabla, 'id_empleado', 'rh_empleados', 'proceso_empleado', true);
            $this->relacion($tabla, 'id_nomina', 'rh_nominas', 'proceso_nomina', true);
            $this->relacion($tabla, 'id_prestamo', 'rh_procesos', 'proceso_prestamo', true);
            $tabla->string('seccion', 20);
            $tabla->string('tipo', 30);
            $tabla->string('titulo', 160);
            $tabla->string('estado', 15)->default('pendiente');
            $tabla->date('inicio')->nullable();
            $tabla->date('fin')->nullable();
            $tabla->unsignedBigInteger('importe')->nullable();
            $tabla->decimal('cantidad', 7, 2)->nullable();
            $tabla->text('detalle')->nullable();
            $tabla->unsignedBigInteger('resuelto_por')->nullable();
            $tabla->timestamp('resuelto_at')->nullable();
            $tabla->index(['id_empresa', 'id_sucursal', 'seccion', 'estado', 'inicio'], 'rh_proceso_busqueda');
        });

        Schema::table('rh_empleados', function (Blueprint $tabla): void {
            $this->relacionExistente($tabla, 'id_departamento', 'rh_procesos', 'empleado_departamento');
            $this->relacionExistente($tabla, 'id_cargo', 'rh_procesos', 'empleado_cargo');
        });

        Schema::create('rh_roles', function (Blueprint $tabla): void {
            $this->sucursal($tabla);
            $this->relacion($tabla, 'id_empleado', 'rh_empleados', 'rol_empleado');
            $this->relacion($tabla, 'id_nomina', 'rh_nominas', 'rol_nomina');
            $tabla->string('empleado', 160);
            $tabla->unsignedBigInteger('ingresos');
            $tabla->unsignedBigInteger('descuentos');
            $tabla->unsignedBigInteger('neto');
            $tabla->json('lineas');
            $tabla->unique(['id_nomina', 'id_empleado']);
        });

        $padre = DB::table('menu')->insertGetId([
            'nombre' => 'Recursos humanos', 'icono' => 'bi-person-workspace', 'orden' => 62,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $opciones = ['rrhh'];
        foreach ((require config_path('rrhh.php'))['secciones'] as $seccion => [$nombre, $icono]) {
            $ruta = 'rrhh.'.$seccion.'.index';
            DB::table('menu')->insert([
                'padre_id' => $padre, 'nombre' => $nombre, 'icono' => $icono, 'ruta' => $ruta,
                'orden' => count($opciones) * 10, 'created_at' => now(), 'updated_at' => now(),
            ]);
            $opciones[] = $ruta;
        }
        DB::table('erp_empresas')->select('id')->chunkById(100, function ($empresas) use ($opciones): void {
            foreach ($empresas as $empresa) {
                $permisos = [];
                foreach ($opciones as $opcion) {
                    foreach (['leer', 'escribir', 'actualizar'] as $accion) {
                        $permisos[] = ['id_empresa' => $empresa->id, 'opcion' => $opcion, 'accion' => $accion];
                    }
                }
                DB::table('permisos')->insertOrIgnore($permisos);
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rh_roles');
        $sqlite = DB::getDriverName() === 'sqlite';
        Schema::table('rh_empleados', function (Blueprint $tabla) use ($sqlite): void {
            $tabla->dropForeign($sqlite ? ['id_empresa', 'id_sucursal', 'id_departamento'] : 'rh_fk_empleado_departamento');
            $tabla->dropForeign($sqlite ? ['id_empresa', 'id_sucursal', 'id_cargo'] : 'rh_fk_empleado_cargo');
        });
        Schema::dropIfExists('rh_procesos');
        Schema::dropIfExists('rh_nominas');
        Schema::dropIfExists('rh_contratos');
        Schema::dropIfExists('rh_empleados');
        DB::table('menu')->where('ruta', 'like', 'rrhh.%')->delete();
        DB::table('menu')->whereNull('ruta')->where('nombre', 'Recursos humanos')->where('icono', 'bi-person-workspace')->delete();
    }

    private function sucursal(Blueprint $tabla): void
    {
        $tabla->id();
        $tabla->unsignedBigInteger('id_empresa');
        $tabla->unsignedBigInteger('id_sucursal');
        $tabla->unsignedBigInteger('creado_por')->nullable();
        $tabla->timestamps();
        $tabla->unique(['id_empresa', 'id_sucursal', 'id']);
        $tabla->foreign(['id_empresa', 'id_sucursal'])->references(['id_empresa', 'id'])->on('erp_sucursales')->restrictOnDelete();
    }

    private function relacion(Blueprint $tabla, string $campo, string $destino, string $clave, bool $nullable = false): void
    {
        $tabla->unsignedBigInteger($campo)->nullable($nullable);
        $this->relacionExistente($tabla, $campo, $destino, $clave);
    }

    private function relacionExistente(Blueprint $tabla, string $campo, string $destino, string $clave): void
    {
        $tabla->foreign(['id_empresa', 'id_sucursal', $campo], 'rh_fk_'.$clave)
            ->references(['id_empresa', 'id_sucursal', 'id'])->on($destino)->restrictOnDelete();
    }
};
