<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class LocalAccountingPilotSeeder extends Seeder
{
    private $company = '0921605895001';

    private $user = '0000000001';

    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            throw new RuntimeException('LocalAccountingPilotSeeder solo puede ejecutarse en local o testing.');
        }

        DB::transaction(function () {
            $this->seedParties();
            $productId = $this->seedProductsAndMedicines();
            $saleJournal = $this->journal('QA-VENTA-001', 'Venta QA', 112, [
                ['1.01.02.05', 112, 0, 'Cliente QA'],
                ['4.01.01.01', 0, 100, 'Venta QA'],
                ['2.01.07.01', 0, 12, 'IVA venta QA'],
            ]);
            $saleId = $this->sale($saleJournal, $productId);

            $purchaseJournal = $this->journal('QA-COMPRA-001', 'Compra QA', 112, [
                ['1.01.03.01', 112, 0, 'Inventario QA'],
                ['2.01.03.01', 0, 112, 'Proveedor QA'],
            ]);
            $purchaseId = $this->purchase('QA-COMPRA-001', '001000000001', '01', 112, $purchaseJournal, $productId);

            $liquidationJournal = $this->journal('QA-LIQUIDACION-001', 'Liquidación de compra QA', 50, [
                ['5.02.05.02', 50, 0, 'Servicio QA'],
                ['2.01.03.01', 0, 50, 'Proveedor liquidación QA'],
            ]);
            $this->purchase('QA-LIQUIDACION-001', '001000000002', '03', 50, $liquidationJournal, $productId, '0999999998001');

            $retentionJournal = $this->journal('QA-RETENCION-001', 'Retención de compra QA', 10, [
                ['2.01.03.01', 10, 0, 'Disminución cuenta por pagar QA'],
                ['2.01.07.02', 0, 10, 'Retención por pagar QA'],
            ]);
            $this->retention($purchaseId, $retentionJournal);
            $this->guide($saleId, $productId);
        });

        $this->command?->info('Datos contables QA creados: catálogos, venta, compra, liquidación, retención, guía y asientos.');
    }

    private function audit(): array
    {
        return ['id_usuariocrea' => $this->user, 'id_usuariomod' => $this->user,
            'ip_creacion' => '127.0.0.1', 'ip_modificacion' => '127.0.0.1', 'updated_at' => now()];
    }

    private function seedParties(): void
    {
        DB::table('ct_clientes')->updateOrInsert(['identificacion' => '0999999999'], array_merge($this->audit(), [
            'nombre' => 'CLIENTE DEMOSTRACION QA', 'tipo' => '5', 'clase' => '2', 'pais' => 'Ecuador', 'estado' => 1,
            'direccion' => 'DIRECCION FICTICIA QA', 'email_representante' => 'cliente.qa@example.test', 'created_at' => now(),
        ]));
        foreach ([
            ['0999999999001', 'PROVEEDOR INSUMOS QA'],
            ['0999999998001', 'PROVEEDOR LIQUIDACION QA'],
        ] as $party) {
            DB::table('proveedor')->updateOrInsert(['id' => $party[0]], array_merge($this->audit(), [
                'razonsocial' => $party[1], 'nombrecomercial' => $party[1], 'ciudad' => 'GUAYAQUIL',
                'direccion' => 'DIRECCION FICTICIA QA', 'email' => 'proveedor.qa@example.test', 'telefono1' => '0990000000',
                'telefono2' => '0990000000', 'tipo' => 4, 'estado' => 1, 'id_tipoproveedor' => 1, 'visualizar' => 1,
                'id_cuentas' => '2.01.03.01', 'id_pais' => 1, 'deleted_at' => null, 'created_at' => now(),
            ]));
        }
        DB::table('transportistas')->updateOrInsert(['ci_ruc' => '0999999997001'], array_merge($this->audit(), [
            'razon_social' => 'TRANSPORTISTA QA', 'nombres' => 'TRANSPORTISTA', 'apellidos' => 'PRUEBA',
            'id_empresa' => $this->company, 'placa' => 'GQA-0001', 'estado' => 1, 'deleted_at' => null, 'created_at' => now(),
        ]));
    }

    private function seedProductsAndMedicines(): int
    {
        DB::table('ct_productos')->updateOrInsert(['codigo' => 'QA-PROD-001', 'id_empresa' => $this->company], array_merge($this->audit(), [
            'nombre' => 'INSUMO DEMOSTRACION QA', 'descripcion' => 'PRODUCTO FICTICIO PARA PRUEBAS', 'grupo' => '36',
            'cta_gastos' => '5.02.05.02', 'cta_ventas' => '4.01.01.01', 'cta_costos' => '5.01.01',
            'precio1' => 100, 'lista' => 100, 'ultima_compra' => 100, 'iva' => 1, 'estado_tabla' => 1, 'tipo_objeto' => 'P',
            'tipo_producto' => 1, 'created_at' => now(),
        ]));
        $productId = (int) DB::table('ct_productos')->where('codigo', 'QA-PROD-001')->where('id_empresa', $this->company)->value('id');
        foreach ([
            ['QA-MED-001', 'AMOXICILINA QA 500 MG', 'J01CA04'],
            ['QA-MED-002', 'IBUPROFENO QA 400 MG', 'M01AE01'],
        ] as $medicine) {
            DB::table('medicina')->updateOrInsert(['consep' => $medicine[0]], array_merge($this->audit(), [
                'nombre' => $medicine[1], 'codigo_atc' => $medicine[2], 'concentracion' => 'PRESENTACION QA',
                'presentacion' => 'TABLETA', 'cantidad' => '20', 'nombre_generico' => $medicine[1],
                'publico_privado' => 1, 'estado' => 1, 'laboratorio' => 'LABORATORIO QA', 'precio_unitario' => 0.25,
                'precio_total' => 5, 'indicaciones' => 'USO EXCLUSIVO DE PRUEBA LOCAL', 'contraindicaciones' => 'DATO FICTICIO QA',
                'iess' => 0, 'created_at' => now(),
            ]));
        }

        return $productId;
    }

    private function journal(string $reference, string $description, float $value, array $lines): int
    {
        $id = DB::table('ct_asientos_cabecera')->where('id_empresa', $this->company)->where('fact_numero', $reference)->value('id');
        if (! $id) {
            $id = DB::table('ct_asientos_cabecera')->insertGetId(array_merge($this->audit(), [
                'observacion' => $description, 'fecha_asiento' => now(), 'fact_numero' => $reference, 'valor' => $value,
                'estado' => 1, 'id_empresa' => $this->company, 'aparece_sri' => 0, 'especial' => 0, 'tipo' => 1,
                'modulo' => 'QA_LOCAL', 'sucursal' => '001', 'punto_emision' => '001', 'created_at' => now(),
            ]));
        }
        DB::table('ct_asientos_detalle')->where('id_asiento_cabecera', $id)->delete();
        foreach ($lines as $line) {
            DB::table('ct_asientos_detalle')->insert(array_merge($this->audit(), [
                'id_asiento_cabecera' => $id, 'id_plan_cuenta' => $line[0], 'descripcion' => $line[3],
                'fecha' => now(), 'debe' => $line[1], 'haber' => $line[2], 'estado' => 1, 'establecimiento' => '001', 'created_at' => now(),
            ]));
        }

        return (int) $id;
    }

    private function sale(int $journal, int $productId): int
    {
        $values = array_merge($this->audit(), ['sucursal' => '001', 'punto_emision' => '001', 'numero' => 'QA-VENTA-001',
            'nro_comprobante' => '001-001-000000001', 'id_asiento' => $journal, 'concepto' => 'VENTA DEMOSTRACION QA', 'tipo' => 'FACTURA',
            'fecha' => date('Y-m-d'), 'divisas' => 1, 'id_cliente' => '0999999999', 'direccion_cliente' => 'DIRECCION FICTICIA QA',
            'ruc_id_cliente' => '0999999999', 'nombre_cliente' => 'CLIENTE DEMOSTRACION QA', 'subtotal_12' => 100,
            'subtotal_total' => 100, 'base_imponible' => 100, 'impuesto' => 12, 'iva_total' => 12, 'total_final' => 112, 'saldo' => 112,
            'estado' => 1, 'id_empresa' => $this->company, 'valor_contable' => 112, 'electronica' => 0, 'rutapdf' => 'QA_LOCAL',
            'created_at' => now()]);
        $id = DB::table('ct_ventas')->where('numero', 'QA-VENTA-001')->value('id');
        $id ? DB::table('ct_ventas')->where('id', $id)->update($values) : $id = DB::table('ct_ventas')->insertGetId($values);
        DB::table('ct_detalle_venta')->updateOrInsert(['id_ct_ventas' => $id, 'codigo' => 'QA-PROD-001'], array_merge($this->audit(), [
            'id_ct_productos' => (string) $productId, 'nombre' => 'INSUMO DEMOSTRACION QA', 'cantidad' => 1, 'precio' => 100,
            'total' => '100', 'extendido' => 100, 'check_iva' => 1, 'estado' => 1, 'valor_contable' => 100, 'tipo_producto' => 1, 'created_at' => now()]));

        return (int) $id;
    }

    private function purchase(string $reference, string $sequence, string $type, float $total, int $journal, int $productId, string $provider = '0999999999001'): int
    {
        $values = array_merge($this->audit(), ['numero' => $reference, 'punto_emision' => '001', 'sucursal' => '001', 'id_asiento_cabecera' => $journal,
            'tipo' => 1, 'fecha' => date('Y-m-d'), 'estado' => 1, 'proveedor' => $provider, 'f_caducidad' => date('Y-m-d', strtotime('+1 year')),
            'serie' => '001001', 'secuencia_factura' => $sequence, 'tipo_comprobante' => $type, 'observacion' => $reference,
            'subtotal' => $total, 'subtotal_0' => $total, 'valor_contable' => $total, 'total_final' => $total, 'saldo_pendiente' => $total,
            'id_empresa' => $this->company, 'created_at' => now()]);
        $id = DB::table('ct_compras')->where('numero', $reference)->value('id');
        $id ? DB::table('ct_compras')->where('id', $id)->update($values) : $id = DB::table('ct_compras')->insertGetId($values);
        DB::table('ct_detalle_compra')->updateOrInsert(['id_ct_compras' => $id, 'codigo' => 'QA-PROD-001'], array_merge($this->audit(), [
            'nombre' => 'INSUMO DEMOSTRACION QA', 'cantidad' => '1', 'precio' => $total, 'total' => $total, 'extendido' => $total,
            'iva' => 0, 'estado' => 1, 'created_at' => now()]));

        return (int) $id;
    }

    private function retention(int $purchaseId, int $journal): void
    {
        DB::table('ct_retenciones')->updateOrInsert(['nro_comprobante' => 'QA-RETENCION-001'], array_merge($this->audit(), [
            'id_empresa' => $this->company, 'id_asiento_cabecera' => $journal, 'punto_emision' => '001', 'sucursal' => '001', 'tipo' => 1,
            'id_compra' => $purchaseId, 'valor_fuente' => 10, 'valor_iva' => 0, 'fecha' => date('Y-m-d'), 'secuencia' => '000000001',
            'descripcion' => 'RETENCION QA', 'total' => 10, 'id_proveedor' => '0999999999001', 'estado' => 1,
            'estado_electronica' => 0, 'electronica' => 0, 'created_at' => now(),
        ]));
    }

    private function guide(int $saleId, int $productId): void
    {
        $values = array_merge($this->audit(), ['establecimiento' => '001', 'punto_emision' => '001', 'num_secuencial' => 1,
            'datos_adicionales' => 'GUIA QA SIN EFECTO CONTABLE', 'ci_ruc_trasnportista' => '0999999997001',
            'direccion_partida' => 'BODEGA QA', 'placa' => 'GQA-0001', 'fecha_ini' => date('Y-m-d'), 'fecha_fin' => date('Y-m-d'),
            'ci_destinatario' => '0999999999001', 'direccion_destinatario' => 'DESTINO FICTICIO QA', 'tipo_documento_destinatario' => '04',
            'num_doc_destino' => 'QA-VENTA-001', 'motivo_traslado_destinatario' => 'TRASLADO DE PRUEBA LOCAL',
            'razon_social_destinatario' => 'PROVEEDOR INSUMOS QA', 'id_empresa' => $this->company, 'estado' => 0,
            'fecha_emision_documento' => now(), 'created_at' => now()]);
        $id = DB::table('ct_cabecera_remision')->where('id_empresa', $this->company)->where('num_doc_destino', 'QA-VENTA-001')->value('id');
        $id ? DB::table('ct_cabecera_remision')->where('id', $id)->update($values) : $id = DB::table('ct_cabecera_remision')->insertGetId($values);
        DB::table('ct_remision_detalle')->updateOrInsert(['id_cabecera_remision' => $id, 'id_producto' => $productId], array_merge($this->audit(), [
            'cod_principal' => 'QA-PROD-001', 'cantidad' => '1', 'precio' => 100, 'descripcion' => 'INSUMO DEMOSTRACION QA',
            'observacion' => 'SIN ASIENTO: DOCUMENTO LOGISTICO', 'tipo_objeto' => 'P', 'created_at' => now()]));
    }
}
