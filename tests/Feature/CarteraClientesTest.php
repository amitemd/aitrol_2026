<?php

namespace Tests\Feature;

use Aitrol\Configuracion\Infrastructure\Persistence\EmpresaModel;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

final class CarteraClientesTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_los_comprobantes_se_entregan_en_bloques_y_conservan_el_estado_revertido(): void
    {
        [$usuario, $sucursal, $cliente] = $this->empresa('0991234567001');
        $this->actingAs($usuario)->travelTo(Carbon::parse('2026-11-28 12:00', 'America/Guayaquil'));
        $factura = $this->documento($usuario, $sucursal, $cliente, 'FACT-01', '2026-11-28', 10000);
        $cuenta = DB::table('cb_cuentas')->insertGetId(['id_empresa' => $usuario->id_empresa, 'id_sucursal' => $sucursal,
            'codigo' => 'BAN-1', 'nombre' => 'Banco de prueba', 'tipo' => 'banco', 'moneda' => 'USD']);
        for ($numero = 1; $numero <= 26; $numero++) {
            $cobro = $this->cobro($usuario, $sucursal, $factura, '2026-11-28', 100, sprintf('REF-%02d', $numero));
            DB::table('cb_asientos')->insert(['id_empresa' => $usuario->id_empresa, 'id_operacion' => $cobro,
                'id_cuenta' => $cuenta, 'variacion' => 100, 'saldo_anterior' => ($numero - 1) * 100, 'saldo' => $numero * 100]);
        }
        DB::table('cb_operaciones')->insert(['id_empresa' => $usuario->id_empresa, 'id_sucursal' => $sucursal, 'fecha' => '2026-11-28',
            'tipo' => 'reversa', 'id_reversa_de' => $cobro, 'moneda' => 'USD', 'importe' => 100, 'concepto' => 'Reversión']);

        $primera = $this->get(route('clientes.documentos.comprobantes-de-pago'))->assertOk()
            ->assertSee('Cargar más comprobantes')->assertSee('REF-26')->assertSee('Revertido')->assertDontSee('REF-01');
        $this->assertCount(25, $primera->viewData('filas'));
        $this->get($primera->viewData('filas')->nextPageUrl(), ['X-Aitrol-Fragmento' => 'contenido'])
            ->assertOk()->assertSee('REF-01')->assertDontSee('REF-26')->assertDontSee('Cargar más comprobantes');
    }

    public function test_reporta_cartera_de_la_sucursal_al_corte_con_ajustes_y_reversiones(): void
    {
        [$usuario, $sucursal, $cliente] = $this->empresa('0991234567001');
        $this->actingAs($usuario)->withSession([
            'contexto_operativo.'.$usuario->id.'.'.$usuario->id_empresa => ['id_sucursal' => $sucursal, 'periodo' => 2026],
        ])->travelTo(Carbon::parse('2026-11-28 12:00', 'America/Guayaquil'));
        $factura = $this->documento($usuario, $sucursal, $cliente, 'FACT-2025', '2025-12-10', 10000);
        $this->documento($usuario, $sucursal, $cliente, 'ND-1', '2026-01-15', 1000, 'notas-debito', $factura);
        $this->documento($usuario, $sucursal, $cliente, 'NC-1', '2026-02-15', 1500, 'notas-credito', $factura);
        $this->documento($usuario, $sucursal, $cliente, 'RET-1', '2026-03-15', 500, 'retenciones', $factura);
        $this->cobro($usuario, $sucursal, $factura, '2026-04-15', 2000, 'REC-1');
        $revertido = $this->cobro($usuario, $sucursal, $factura, '2026-10-10', 3000, 'REC-REV');
        DB::table('cb_operaciones')->insert([
            'id_empresa' => $usuario->id_empresa, 'id_sucursal' => $sucursal, 'fecha' => '2026-11-10',
            'tipo' => 'reversa', 'id_reversa_de' => $revertido, 'moneda' => 'USD', 'importe' => 3000, 'concepto' => 'Reversión de cobro',
        ]);
        $this->documento($usuario, $sucursal, $cliente, 'FACT-CREDITO', '2026-11-20', 8000, plazo: 30);
        $anulada = $this->documento($usuario, $sucursal, $cliente, 'FACT-ANULADA', '2026-10-15', 4000);
        DB::table('com_documentos')->where('id', $anulada)->update(['anulado_at' => '2026-11-11 02:30:00']);
        $alDia = $this->documento($usuario, $sucursal, $cliente, 'FACT-PAGADA', '2026-09-01', 1000);
        $this->cobro($usuario, $sucursal, $alDia, '2026-09-02', 1000, 'REC-2');
        $this->documento($usuario, $sucursal, $cliente, 'RET-A-FAVOR', '2026-09-03', 200, 'retenciones', $alDia);
        [$externo, $sucursalExterna, $clienteExterno] = $this->empresa('0991234568001');
        $this->documento($externo, $sucursalExterna, $clienteExterno, 'FACT-EXTERNA', '2026-10-01', 10000);
        $otraSucursal = DB::table('erp_sucursales')->insertGetId(['id_empresa' => $usuario->id_empresa, 'codigo' => 'OTRA', 'nombre' => 'Otra sucursal']);
        $this->documento($usuario, $otraSucursal, $cliente, 'FACT-OTRA-SUCURSAL', '2026-10-01', 10000);

        $ruta = route('clientes.reportes.cuentas-por-cobrar');
        $reporte = $this->get($ruta)->assertOk()->assertSee('FACT-2025')->assertSee('FACT-CREDITO')->assertSee('20/12/2026')
            ->assertDontSee('FACT-ANULADA')->assertDontSee('FACT-PAGADA')->assertDontSee('FACT-EXTERNA')->assertDontSee('FACT-OTRA-SUCURSAL');
        $this->assertSame(15000, (int) $reporte->viewData('totales')->pendiente);
        $this->assertSame(7000, (int) $reporte->viewData('totales')->vencido);
        $this->assertSame(200, (int) $reporte->viewData('totales')->a_favor);
        $this->get(route('ventas.documentos.cobro.create', $factura))->assertOk()
            ->assertSee('Genera primero el asiento contable de la factura.');

        $historico = $this->get(route('clientes.reportes.cuentas-por-cobrar', ['corte' => '2026-11-01']))
            ->assertOk()->assertSee('FACT-ANULADA')->assertDontSee('FACT-CREDITO');
        $this->assertSame(8000, (int) $historico->viewData('totales')->pendiente);
        $this->get(route('clientes.reportes.cuentas-por-cobrar', ['corte' => '2026-11-10']))->assertOk()->assertDontSee('FACT-ANULADA');
        $this->get(route('clientes.reportes.cuentas-por-cobrar', ['q' => 'FACT-CREDITO']))
            ->assertOk()->assertSee('FACT-CREDITO')->assertDontSee('FACT-2025');

        $this->get(route('clientes.reportes.cartera-vencida'))->assertOk()->assertSee('FACT-2025')->assertDontSee('FACT-CREDITO');
        $edad = $this->get(route('clientes.reportes.edad-de-cartera'))->assertOk()->viewData('filas')->first();
        $this->assertSame(7000, (int) $edad->dias_91);
        $this->assertSame(8000, (int) $edad->vigente);
        $saldos = $this->get(route('clientes.reportes.saldos-de-cartera'))->assertOk()->viewData('filas')->first();
        $this->assertSame(14800, (int) $saldos->saldo);

        $this->get(route('clientes.index'))->assertOk()->assertSee(route('clientes.estado-cuenta', $cliente));
        $this->get(route('clientes.show', $cliente))->assertOk()->assertSee(route('clientes.estado-cuenta', $cliente));
        $this->get(route('clientes.estado-cuenta', $cliente))->assertOk()->assertSee('FACT-PAGADA')->assertSee('RET-A-FAVOR')
            ->assertSee('REC-1')->assertSee('NC-1')->assertDontSee('REC-REV');
        $this->get(route('clientes.estado-cuenta', $clienteExterno))->assertNotFound();
        $this->getJson(route('clientes.reportes.cuentas-por-cobrar', ['corte' => '2026-11-29']))
            ->assertUnprocessable()->assertJsonValidationErrors('corte');
    }

    private function empresa(string $ruc): array
    {
        $empresa = EmpresaModel::create(['ruc' => $ruc, 'razon_social' => 'Empresa de cartera']);
        $sucursal = DB::table('erp_sucursales')->insertGetId(['id_empresa' => $empresa->id, 'codigo' => 'MATRIZ', 'nombre' => 'Matriz']);
        $usuario = User::factory()->create(['id_empresa' => $empresa->id, 'is_admin' => true]);
        $cliente = DB::table('erp_clientes')->insertGetId([
            'id_empresa' => $empresa->id, 'nombre' => 'Cliente de cartera', 'identificacion' => '0912345678', 'tipo_identificacion' => 'cedula',
        ]);

        return [$usuario, $sucursal, $cliente];
    }

    private function documento(User $usuario, int $sucursal, int $cliente, string $numero, string $fecha, int $total, string $tipo = 'documentos', ?int $origen = null, int $plazo = 0): int
    {
        return DB::table('com_documentos')->insertGetId([
            'id_empresa' => $usuario->id_empresa, 'id_sucursal' => $sucursal, 'id_cliente' => $cliente,
            'modulo' => 'ventas', 'tipo' => $tipo, 'numero' => $numero, 'fecha' => $fecha, 'tercero' => 'Cliente de cartera',
            'subtotal' => $total, 'impuesto' => 0, 'total' => $total, 'id_documento_origen' => $origen,
            'datos_factura' => $plazo ? json_encode(['plazo_dias' => $plazo], JSON_THROW_ON_ERROR) : null,
        ]);
    }

    private function cobro(User $usuario, int $sucursal, int $factura, string $fecha, int $importe, string $referencia): int
    {
        return DB::table('cb_operaciones')->insertGetId([
            'id_empresa' => $usuario->id_empresa, 'id_sucursal' => $sucursal, 'id_factura_venta' => $factura, 'fecha' => $fecha,
            'tipo' => 'ingreso', 'moneda' => 'USD', 'importe' => $importe, 'referencia' => $referencia, 'concepto' => 'Cobro de factura',
        ]);
    }
}
