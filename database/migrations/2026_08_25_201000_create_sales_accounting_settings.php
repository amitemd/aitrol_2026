<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateSalesAccountingSettings extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('ct_modulos') || ! Schema::hasTable('ct_globales')
            || ! Schema::hasTable('empresa') || ! Schema::hasTable('plan_cuentas_empresa')) {
            return;
        }

        $modulo = DB::table('ct_modulos')->where('nombre', 'Ventas')->orderBy('id')->first();
        if (! $modulo) {
            $idModulo = DB::table('ct_modulos')->insertGetId([
                'nombre' => 'Ventas',
                'estado' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        } else {
            $idModulo = $modulo->id;
            DB::table('ct_modulos')->where('id', $idModulo)->update(['estado' => 1]);
        }

        $definiciones = [
            'Ventas - Cuentas por cobrar clientes' => ['lado' => 'debe', 'prefijo' => '1%', 'obligatorias' => [], 'alternativas' => ['cuentas por cobrar', 'clientes']],
            'Ventas - Ingresos tarifa 0%' => ['lado' => 'haber', 'prefijo' => '4%', 'obligatorias' => ['venta'], 'alternativas' => ['tarifa 0', '0%']],
            'Ventas - Ingresos gravados' => ['lado' => 'haber', 'prefijo' => '4%', 'obligatorias' => ['venta'], 'alternativas' => ['gravada', '12%', '15%']],
            'Ventas - IVA generado' => ['lado' => 'haber', 'prefijo' => '2%', 'obligatorias' => ['iva'], 'alternativas' => ['venta', 'generado', 'cobrado']],
            'Ventas - Descuentos' => ['lado' => 'debe', 'prefijo' => '4%', 'obligatorias' => ['descuento'], 'alternativas' => ['venta']],
            'Ventas - Costo de ventas' => ['lado' => 'debe', 'prefijo' => '5%', 'obligatorias' => ['costo'], 'alternativas' => ['venta', 'mercaderia']],
            'Ventas - Inventario' => ['lado' => 'haber', 'prefijo' => '1%', 'obligatorias' => ['inventario'], 'alternativas' => ['mercaderia', 'producto']],
            'Ventas - Propinas por pagar' => ['lado' => 'haber', 'prefijo' => '2%', 'obligatorias' => ['propina'], 'alternativas' => ['pagar', 'empleado']],
        ];

        $empresas = DB::table('empresa')->where('estado', 1)->get(['id']);
        foreach ($empresas as $empresa) {
            foreach ($definiciones as $nombre => $definicion) {
                $existente = DB::table('ct_globales')
                    ->where('id_empresa', $empresa->id)
                    ->where('nombre', $nombre)
                    ->first();

                $cuenta = $this->buscarCuenta($empresa->id, $definicion);
                $valores = [
                    'id_modulo' => $idModulo,
                    'estado' => 1,
                    'tipo' => 2,
                    'activo_debe' => $definicion['lado'] === 'debe' ? 1 : 0,
                    'activo_haber' => $definicion['lado'] === 'haber' ? 1 : 0,
                    'updated_at' => date('Y-m-d H:i:s'),
                ];

                if ($existente) {
                    if ($cuenta && trim((string) $existente->{$definicion['lado']}) === '') {
                        $valores[$definicion['lado']] = $cuenta;
                    }
                    DB::table('ct_globales')->where('id', $existente->id)->update($valores);

                    continue;
                }

                $valores['nombre'] = $nombre;
                $valores['id_empresa'] = $empresa->id;
                $valores['debe'] = $definicion['lado'] === 'debe' ? $cuenta : null;
                $valores['haber'] = $definicion['lado'] === 'haber' ? $cuenta : null;
                $valores['created_at'] = date('Y-m-d H:i:s');
                DB::table('ct_globales')->insert($valores);
            }
        }
    }

    private function buscarCuenta($idEmpresa, array $definicion)
    {
        $consulta = DB::table('plan_cuentas_empresa')
            ->where('id_empresa', $idEmpresa)
            ->where('plan', 'like', $definicion['prefijo']);

        foreach ($definicion['obligatorias'] as $texto) {
            $consulta->where('nombre', 'like', '%'.$texto.'%');
        }
        if (! empty($definicion['alternativas'])) {
            $alternativas = $definicion['alternativas'];
            $consulta->where(function ($query) use ($alternativas) {
                foreach ($alternativas as $indice => $texto) {
                    if ($indice === 0) {
                        $query->where('nombre', 'like', '%'.$texto.'%');
                    } else {
                        $query->orWhere('nombre', 'like', '%'.$texto.'%');
                    }
                }
            });
        }

        $cuenta = $consulta->orderBy('plan')->first(['id_plan']);

        return $cuenta ? $cuenta->id_plan : null;
    }

    public function down()
    {
        // No elimina configuraciones contables que ya pudieron ser utilizadas.
    }
}
