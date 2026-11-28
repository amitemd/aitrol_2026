<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('co_plan_cuentas_plantilla', function (Blueprint $table): void {
            $table->string('codigo', 40)->primary();
            $table->string('nombre', 160);
            $table->string('clase', 15);
            $table->string('codigo_padre', 40)->nullable();
            $table->boolean('auxiliar');
            $table->boolean('efectivo');
        });

        $catalogo = [
            '1' => ['Activo', false],
            '1.01' => ['Activo corriente', false],
            '1.01.01' => ['Efectivo y equivalentes de efectivo', false],
            '1.01.01.01' => ['Caja general', true],
            '1.01.01.02' => ['Caja chica', true],
            '1.01.01.03' => ['Bancos nacionales', true],
            '1.01.01.04' => ['Bancos del exterior', true],
            '1.01.02' => ['Cuentas y documentos por cobrar', false],
            '1.01.02.01' => ['Clientes nacionales', false],
            '1.01.02.02' => ['Clientes del exterior', false],
            '1.01.02.03' => ['Documentos por cobrar', false],
            '1.01.02.04' => ['Anticipos a proveedores', false],
            '1.01.02.05' => ['Cuentas por cobrar al personal', false],
            '1.01.02.06' => ['Deterioro acumulado de cuentas por cobrar', false],
            '1.01.03' => ['Inventarios', false],
            '1.01.03.01' => ['Mercaderías para la venta', false],
            '1.01.03.02' => ['Materiales e insumos', false],
            '1.01.03.03' => ['Productos en proceso', false],
            '1.01.03.04' => ['Productos terminados', false],
            '1.01.03.05' => ['Inventario en tránsito', false],
            '1.01.03.06' => ['Deterioro acumulado de inventarios', false],
            '1.01.04' => ['Activos por impuestos corrientes', false],
            '1.01.04.01' => ['IVA crédito tributario', false],
            '1.01.04.02' => ['Retenciones de IVA recibidas', false],
            '1.01.04.03' => ['Retenciones de impuesto a la renta recibidas', false],
            '1.01.04.04' => ['Anticipos de impuesto a la renta', false],
            '1.01.05' => ['Pagos anticipados', false],
            '1.01.05.01' => ['Seguros pagados por anticipado', false],
            '1.01.05.02' => ['Arriendos pagados por anticipado', false],
            '1.02' => ['Activo no corriente', false],
            '1.02.01' => ['Propiedad, planta y equipo', false],
            '1.02.01.01' => ['Terrenos', false],
            '1.02.01.02' => ['Edificios e instalaciones', false],
            '1.02.01.03' => ['Maquinaria y equipo', false],
            '1.02.01.04' => ['Muebles y enseres', false],
            '1.02.01.05' => ['Equipos de computación', false],
            '1.02.01.06' => ['Vehículos', false],
            '1.02.01.07' => ['Obras en curso', false],
            '1.02.02' => ['Depreciación acumulada', false],
            '1.02.02.01' => ['Depreciación acumulada de edificios', false],
            '1.02.02.02' => ['Depreciación acumulada de maquinaria y equipo', false],
            '1.02.02.03' => ['Depreciación acumulada de muebles y enseres', false],
            '1.02.02.04' => ['Depreciación acumulada de equipos de computación', false],
            '1.02.02.05' => ['Depreciación acumulada de vehículos', false],
            '1.02.03' => ['Intangibles', false],
            '1.02.03.01' => ['Software y licencias', false],
            '1.02.03.02' => ['Amortización acumulada de intangibles', false],
            '2' => ['Pasivo', false],
            '2.01' => ['Pasivo corriente', false],
            '2.01.01' => ['Cuentas y documentos por pagar', false],
            '2.01.01.01' => ['Proveedores nacionales', false],
            '2.01.01.02' => ['Proveedores del exterior', false],
            '2.01.01.03' => ['Documentos por pagar', false],
            '2.01.01.04' => ['Anticipos de clientes', false],
            '2.01.01.05' => ['Propinas por pagar', false],
            '2.01.02' => ['Obligaciones con el personal', false],
            '2.01.02.01' => ['Sueldos por pagar', false],
            '2.01.02.02' => ['Aportes al IESS por pagar', false],
            '2.01.02.03' => ['Décimo tercer sueldo por pagar', false],
            '2.01.02.04' => ['Décimo cuarto sueldo por pagar', false],
            '2.01.02.05' => ['Vacaciones por pagar', false],
            '2.01.02.06' => ['Fondos de reserva por pagar', false],
            '2.01.02.07' => ['Participación a trabajadores por pagar', false],
            '2.01.03' => ['Obligaciones tributarias', false],
            '2.01.03.01' => ['IVA generado en ventas', false],
            '2.01.03.02' => ['IVA por pagar', false],
            '2.01.03.03' => ['Retenciones de IVA por pagar', false],
            '2.01.03.04' => ['Retenciones de impuesto a la renta por pagar', false],
            '2.01.03.05' => ['Impuesto a la renta por pagar', false],
            '2.01.04' => ['Obligaciones financieras corrientes', false],
            '2.01.04.01' => ['Préstamos bancarios a corto plazo', false],
            '2.01.04.02' => ['Porción corriente de deuda a largo plazo', false],
            '2.02' => ['Pasivo no corriente', false],
            '2.02.01' => ['Obligaciones financieras a largo plazo', false],
            '2.02.01.01' => ['Préstamos bancarios a largo plazo', false],
            '2.02.02' => ['Provisiones no corrientes', false],
            '2.02.02.01' => ['Provisión por jubilación patronal', false],
            '2.02.02.02' => ['Provisión por desahucio', false],
            '3' => ['Patrimonio', false],
            '3.01' => ['Capital y reservas', false],
            '3.01.01' => ['Capital social', false],
            '3.01.02' => ['Aportes para futura capitalización', false],
            '3.01.03' => ['Reserva legal', false],
            '3.01.04' => ['Otras reservas', false],
            '3.02' => ['Resultados', false],
            '3.02.01' => ['Utilidades acumuladas', false],
            '3.02.02' => ['Pérdidas acumuladas', false],
            '3.02.03' => ['Resultado del ejercicio', false],
            '4' => ['Ingresos', false],
            '4.01' => ['Ingresos operacionales', false],
            '4.01.01' => ['Ventas de bienes', false],
            '4.01.02' => ['Ingresos por servicios', false],
            '4.01.03' => ['Devoluciones sobre ventas', false],
            '4.01.04' => ['Descuentos sobre ventas', false],
            '4.02' => ['Otros ingresos', false],
            '4.02.01' => ['Intereses ganados', false],
            '4.02.02' => ['Ganancia por venta de activos', false],
            '4.02.03' => ['Otros ingresos no operacionales', false],
            '5' => ['Costos y gastos', false],
            '5.01' => ['Costos de ventas y servicios', false],
            '5.01.01' => ['Costo de mercaderías vendidas', false],
            '5.01.02' => ['Costo de servicios prestados', false],
            '5.01.03' => ['Compras sin inventario', false],
            '5.01.04' => ['Ajustes y pérdidas de inventario', false],
            '5.02' => ['Gastos administrativos', false],
            '5.02.01' => ['Sueldos y salarios', false],
            '5.02.02' => ['Aportes patronales al IESS', false],
            '5.02.03' => ['Beneficios sociales', false],
            '5.02.04' => ['Honorarios profesionales', false],
            '5.02.05' => ['Arriendos', false],
            '5.02.06' => ['Servicios básicos y telecomunicaciones', false],
            '5.02.07' => ['Suministros de oficina', false],
            '5.02.08' => ['Mantenimiento y reparaciones', false],
            '5.02.09' => ['Seguros', false],
            '5.02.10' => ['Depreciaciones', false],
            '5.02.11' => ['Amortizaciones', false],
            '5.03' => ['Gastos de venta', false],
            '5.03.01' => ['Publicidad y promoción', false],
            '5.03.02' => ['Comisiones de ventas', false],
            '5.03.03' => ['Transporte y distribución', false],
            '5.04' => ['Gastos financieros', false],
            '5.04.01' => ['Intereses pagados', false],
            '5.04.02' => ['Comisiones y gastos bancarios', false],
            '5.05' => ['Otros gastos', false],
            '5.05.01' => ['Pérdida por baja de activos', false],
            '5.05.02' => ['Gastos por deterioro de activos', false],
            '5.05.03' => ['Otros gastos no operacionales', false],
            '5.06' => ['Participación e impuesto a la renta', false],
            '5.06.01' => ['Participación a trabajadores', false],
            '5.06.02' => ['Gasto de impuesto a la renta', false],
        ];

        $clases = [1 => 'activo', 2 => 'pasivo', 3 => 'patrimonio', 4 => 'ingreso', 5 => 'gasto'];
        $padres = [];
        foreach (array_keys($catalogo) as $codigo) {
            $padre = str_contains((string) $codigo, '.') ? substr((string) $codigo, 0, strrpos((string) $codigo, '.')) : null;
            if ($padre !== null) {
                $padres[$padre] = true;
            }
        }

        $cuentas = [];
        foreach ($catalogo as $codigo => [$nombre, $efectivo]) {
            $codigo = (string) $codigo;
            $cuentas[] = [
                'codigo' => $codigo,
                'nombre' => $nombre,
                'clase' => $clases[(int) $codigo[0]],
                'codigo_padre' => str_contains($codigo, '.') ? substr($codigo, 0, strrpos($codigo, '.')) : null,
                'auxiliar' => ! isset($padres[$codigo]),
                'efectivo' => $efectivo,
            ];
        }
        DB::table('co_plan_cuentas_plantilla')->insert($cuentas);
    }

    public function down(): void
    {
        Schema::dropIfExists('co_plan_cuentas_plantilla');
    }
};
