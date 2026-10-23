<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Sis_medico\v3\contable\Clientes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Ct_Caja;
use Sis_medico\Http\Controllers\v3\contable\ClaveAcceso;
use Sis_medico\Venta_Kardex;

class Ventas extends Model
{
    private static $modelo = 'Ventas';
    protected $table = 'ct_ventas';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $keyType = 'string';

    private static function calcularTotalesConglomerada(array $data, $porcentajeIva)
    {
        $subtotal0 = 0.0;
        $subtotalIva = 0.0;
        $descuentoTotal = 0.0;
        $baseIva = 0.0;
        $base0 = 0.0;
        $copagoTotal = 0.0;
        $descuentos = isset($data['descuento']) && is_array($data['descuento'])
            ? $data['descuento']
            : (isset($data['desc']) && is_array($data['desc']) ? $data['desc'] : []);

        foreach ((array) ($data['codigo'] ?? []) as $i => $codigo) {
            $cantidad = (float) ($data['cantidad'][$i] ?? 0);
            $precio = (float) ($data['precio'][$i] ?? 0);
            $copago = round((float) ($data['copago'][$i] ?? 0), 2, PHP_ROUND_HALF_UP);
            $descuento = round((float) ($descuentos[$i] ?? 0), 2, PHP_ROUND_HALF_UP);
            $subtotalLinea = round(($cantidad * $precio) - $copago, 2, PHP_ROUND_HALF_UP);
            $netoLinea = round($subtotalLinea - $descuento, 2, PHP_ROUND_HALF_UP);
            $gravaIva = (float) ($data['iva'][$i] ?? 0) > 0;

            $data['copago'][$i] = $copago;
            $data['desc'][$i] = $descuento;
            $data['descuento'][$i] = $descuento;
            $data['precioneto'][$i] = $netoLinea;

            $copagoTotal += $copago;
            $descuentoTotal += $descuento;
            if ($gravaIva) {
                $subtotalIva += $subtotalLinea;
                $baseIva += $netoLinea;
            } else {
                $subtotal0 += $subtotalLinea;
                $base0 += $netoLinea;
            }
        }

        $data['subtotal_121'] = round($subtotalIva, 2, PHP_ROUND_HALF_UP);
        $data['subtotal_01'] = round($subtotal0, 2, PHP_ROUND_HALF_UP);
        $data['descuento1'] = round($descuentoTotal, 2, PHP_ROUND_HALF_UP);
        $data['totalc'] = round($copagoTotal, 2, PHP_ROUND_HALF_UP);
        $data['base1'] = round($baseIva + $base0, 2, PHP_ROUND_HALF_UP);
        $data['base_iva'] = round($baseIva, 2, PHP_ROUND_HALF_UP);
        $data['tarifa_iva1'] = round($baseIva * (float) $porcentajeIva, 2, PHP_ROUND_HALF_UP);
        $data['total1'] = round($data['base1'] + $data['tarifa_iva1'], 2, PHP_ROUND_HALF_UP);

        return $data;
    }


    public function detalles()
    {
        return $this->hasMany('Sis_medico\v3\contable\DetalleVenta', 'id_ct_ventas');
    }
    public function empresa()
    {
        return $this->belongsTo('Sis_medico\v3\contable\Empresas', 'id_empresa');
    }

    public function pasos()
    {
        return $this->belongsTo('Sis_medico\v3\contable\DePasos', 'doc_electronico');
    }
    public static function EditarFacturaCaja($r)
    {
        //dd($r->all());
        $valor = $r->valor;
    }

    public static function pdfFacturaCaja($r)
    {
        //dd($r->all());
        $fact_venta = OrdenVenta::findorfail($r->id);
        //dd($fact_venta->seguro);
        $ct_for_pag = OrdenVentaPago::where('id_orden', $r->id)->get();

        $agenda = $fact_venta->agenda;


        if ($agenda->proc_consul == '1') {
            //dd('entra');
            $vistaurl = "v3/contable/ventas/view_pdf_comprobante_tributario_proc";
        } else {
            $vistaurl = "v3/contable/ventas/view_pdf_comprobante_tributario";
        }

        $view = \View::make($vistaurl, compact('fact_venta', 'ct_for_pag'))->render();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->setPaper('A4', 'landscape');
        return $pdf->stream('Comprobante de Orden-' . $r->id . '.pdf');
    }

    public static function eliminarFacturaCaja($r)
    {
        $arrayRespuesta = [];
        try {
            $archivo = OrdenVenta::where('id', $r->id)->where("estado", 1)->first();
            $archivo->estado = 0;
            $archivo->save();
            $arrayRespuesta = [
                'status' => 'success',
                'code' => 200,
                'message' => 'Archivo eliminado correctamente',
                'data' => $r->input(),
            ];
        } catch (Exception $e) {
            $arrayRespuesta = [
                'status' => 'error',
                'code' => 500,
                'message' => 'Error al eliminar el archivo',
                'data' => $r->input(),
                'error' => $e->getMessage() . '|' . $e->getLine() . '|' . $e->getFile()
            ];
        }
        return json_encode($arrayRespuesta);
    }

    public static function facturadecajajs($r)
    {
        //dd($r->all());
        $fecha = $r['desde'];
        if (is_null($fecha)) {
            $fecha = date('Y-m-d');
        }
        $fecha_hasta = $r['hasta'];
        if (is_null($fecha_hasta)) {
            $fecha_hasta = date('Y-m-d');
        }
        $id_empresa = $r['id_empresa'];
        if (is_null($id_empresa)) {
            //$id_empresa = Empresa::where('prioridad', '1')->first()->id;
        }


        $caja = $r['caja'];
        $doctor = $r['doctor'];
        $id_paciente = $r['id_paciente'];
        $usuario_paciente = null;
        $tipo = $r['tipo'];
        $facturas_pendientes = Agenda::leftjoin('ct_orden_venta as orden', function ($join) {
            $join->on(function ($query) {
                $query->on('orden.id_agenda', 'agenda.id')
                    ->where('orden.estado', '=', '1');
            });
        })
            ->join('paciente as p', 'p.id', 'agenda.id_paciente')
            ->join('users as u', 'agenda.id_usuariomod', 'u.id')
            ->whereBetween('agenda.fechaini', [$fecha . ' 00:00:00', $fecha_hasta . ' 23:59:00'])
            ->join('seguros as s', 's.id', 'agenda.id_seguro')
            ->where('s.tipo', '<>', '0')->where('agenda.proc_consul', '<', '2')
            ->whereRaw('(agenda.omni = "%NO%" OR agenda.omni IS NULL)')
            ->where('agenda.estado', '<>', '0')
            ->whereNotNull('agenda.id_doctor1')
            ->whereNull('orden.id')
            ->where('agenda.estado_cita', '4')
            ->where('agenda.post_proc', '0')
            ->where('agenda.id_doctor1', '<>', '4444444444');

        /*$ordenes = OrdenVenta::where('ct_orden_venta.estado', 1)
            ->select([
                'ct_orden_venta.id as id',
                'ctp.tipo',
                'ct.nombre as nombre_tipo',
                'ctp.valor',
                DB::raw('SUBSTRING(a.fechaini, 1, 10) as fechaCita'),
                DB::raw('SUBSTRING(a.fechaini, 11, 6) as horaCita'),
                'a.proc_consul',
                'a.cortesia as cortesia',
                DB::raw('CONCAT(p.nombre1, " ", p.apellido1) as nombres'),
                DB::raw('CONCAT(u.apellido1, " ", u.nombre1) as admision'),
                DB::raw('CONCAT(u1.apellido1, " ", u1.nombre1) as doctor'),
                DB::raw("
            CASE
                WHEN ct_orden_venta.caja = 'LABORATORIO' THEN 'LABORATORIO'
                WHEN a.id_doctor1 IS NOT NULL AND a.id_doctor1 = '4444444444' THEN 'LABORATORIO'
                WHEN a.proc_consul = 0 THEN 'CONSULTA'
                WHEN a.proc_consul = 1 THEN 'PROCEDIMIENTO'
            END as procedimiento
        "),
                's.nombre as seguro',
                DB::raw('IF(ctp.tipo=1, ctp.valor, "0.00") as efectivo'),
                DB::raw('IF(ctp.tipo=2, ctp.valor, "0.00") as cheque'),
                DB::raw('IF(ctp.tipo=3 or ctp.tipo=5, ctp.valor, "0.00") as deposito'),
                DB::raw('IF(ctp.tipo=4 OR ctp.tipo=6, ctp.valor, "0.00") as tarjeta'),
                DB::raw('IF(ctp.tipo=9, ctp.valor, "0.00") as datalink'),
                DB::raw('IF(ctp.tipo=10, ctp.valor, "0.00") as pagoapp'),
                DB::raw('IF(ctp.tipo=7, ctp.valor, "0.00") as pendiente'),
                DB::raw('SUM(IF(ctp.tipo=1, ctp.valor, 0)) as total_efectivo'),
                DB::raw('SUM(IF(ctp.tipo=2, ctp.valor, 0)) as total_cheque'),
                DB::raw('SUM(IF(ctp.tipo=3 or ctp.tipo=5, ctp.valor, 0)) as total_deposito'),
                DB::raw('SUM(IF(ctp.tipo=4 OR ctp.tipo=6, ctp.valor, 0)) as total_tarjeta'),
                DB::raw('SUM(IF(ctp.tipo=9, ctp.valor, 0)) as total_datalink'),
                DB::raw('SUM(IF(ctp.tipo=10, ctp.valor, 0)) as total_pagoapp'),
                DB::raw('SUM(IF(ctp.tipo=7, ctp.valor, 0)) as total_pendiente')
            ])
            ->join('agenda as a', 'a.id', '=', 'ct_orden_venta.id_agenda')
            ->join('paciente as p', 'p.id', '=', 'a.id_paciente')
            ->join('users as u', 'a.id_usuariomod', '=', 'u.id')
            ->join('ct_orden_venta_pago as ctp', 'ct_orden_venta.id', '=', 'ctp.id_orden')
            ->join('ct_tipo_pago as ct', 'ctp.tipo', '=', 'ct.id')
            ->join('users as u1', function ($join) {
                $join->on('a.id_doctor1', '=', 'u1.id')
                    ->where('u1.id_tipo_usuario', 3);
            })
            ->join('seguros as s', 's.id', '=', 'ct_orden_venta.id_seguro')
            ->where('ct_orden_venta.estado', 1)
            ->whereBetween('ct_orden_venta.fecha_emision',  [$fecha . ' 00:00:00', $fecha_hasta . ' 23:59:00'])
            ->groupBy('ct_orden_venta.id');*/

        $subQueryEfectivo = DB::table('ct_orden_venta')
            ->join('ct_orden_venta_pago as ctp', 'ct_orden_venta.id', '=', 'ctp.id_orden')
            ->where('ct_orden_venta.estado', 1)
            ->whereBetween('ct_orden_venta.fecha_emision', [$fecha . ' 00:00:00', $fecha_hasta . ' 23:59:00'])
            ->selectRaw('SUM(IF(ctp.tipo = 1, ctp.valor, 0)) as total_global_efectivo')
            ->first()
            ->total_global_efectivo;

        $subQueryCheque = DB::table('ct_orden_venta')
            ->join('ct_orden_venta_pago as ctp', 'ct_orden_venta.id', '=', 'ctp.id_orden')
            ->where('ct_orden_venta.estado', 1)
            ->whereBetween('ct_orden_venta.fecha_emision', [$fecha . ' 00:00:00', $fecha_hasta . ' 23:59:00'])
            ->selectRaw('SUM(IF(ctp.tipo = 2, ctp.valor, 0)) as total_global_cheque')
            ->first()
            ->total_global_cheque;

        $subQueryDeposito = DB::table('ct_orden_venta')
            ->join('ct_orden_venta_pago as ctp', 'ct_orden_venta.id', '=', 'ctp.id_orden')
            ->where('ct_orden_venta.estado', 1)
            ->whereBetween('ct_orden_venta.fecha_emision', [$fecha . ' 00:00:00', $fecha_hasta . ' 23:59:00'])
            ->selectRaw('SUM(IF(ctp.tipo = 3 OR ctp.tipo = 5, ctp.valor, 0)) as total_global_deposito')
            ->first()
            ->total_global_deposito;

        $subQueryTarjeta = DB::table('ct_orden_venta')
            ->join('ct_orden_venta_pago as ctp', 'ct_orden_venta.id', '=', 'ctp.id_orden')
            ->where('ct_orden_venta.estado', 1)
            ->whereBetween('ct_orden_venta.fecha_emision', [$fecha . ' 00:00:00', $fecha_hasta . ' 23:59:00'])
            ->selectRaw('SUM(IF(ctp.tipo = 4 OR ctp.tipo = 6, ctp.valor, 0)) as total_global_tarjeta')
            ->first()
            ->total_global_tarjeta;


        $subQueryDatalink = DB::table('ct_orden_venta')
            ->join('ct_orden_venta_pago as ctp', 'ct_orden_venta.id', '=', 'ctp.id_orden')
            ->where('ct_orden_venta.estado', 1)
            ->whereBetween('ct_orden_venta.fecha_emision', [$fecha . ' 00:00:00', $fecha_hasta . ' 23:59:00'])
            ->selectRaw('SUM(IF(ctp.tipo = 9, ctp.valor, 0)) as total_global_datalink')
            ->first()
            ->total_global_datalink;

        $subQueryApp = DB::table('ct_orden_venta')
            ->join('ct_orden_venta_pago as ctp', 'ct_orden_venta.id', '=', 'ctp.id_orden')
            ->where('ct_orden_venta.estado', 1)
            ->whereBetween('ct_orden_venta.fecha_emision', [$fecha . ' 00:00:00', $fecha_hasta . ' 23:59:00'])
            ->selectRaw('SUM(IF(ctp.tipo = 10, ctp.valor, 0)) as total_global_app')
            ->first()
            ->total_global_app;



        $subQuerySeguro = DB::table('ct_orden_venta')
            ->join('ct_orden_venta_pago as ctp', 'ct_orden_venta.id', '=', 'ctp.id_orden')
            ->where('ct_orden_venta.estado', 1)
            ->whereBetween('ct_orden_venta.fecha_emision', [$fecha . ' 00:00:00', $fecha_hasta . ' 23:59:00'])
            ->selectRaw('FORMAT(SUM(ct_orden_venta.valor_oda), 2) as total_valor_oda')
            ->first()
            ->total_valor_oda;





        $subQueryTotal = DB::table('ct_orden_venta')
            ->join('ct_orden_venta_pago as ctp', 'ct_orden_venta.id', '=', 'ctp.id_orden')
            ->where('ct_orden_venta.estado', 1)
            ->whereBetween('ct_orden_venta.fecha_emision', [$fecha . ' 00:00:00', $fecha_hasta . ' 23:59:00'])
            ->selectRaw(
                'ROUND(
            SUM(
                IF(ctp.tipo = 1, ctp.valor, 0) +
                IF(ctp.tipo = 2, ctp.valor, 0) +
                IF(ctp.tipo = 3 OR ctp.tipo = 5, ctp.valor, 0) +
                IF(ctp.tipo = 4 OR ctp.tipo = 6, ctp.valor, 0) +
                IF(ctp.tipo = 9, ctp.valor, 0) +
                IF(ctp.tipo = 10, ctp.valor, 0) + 
                ct_orden_venta.valor_oda + IF(ctp.tipo = 7, ctp.valor, 0)
            ), 2
        ) as Total_Suma'
            )
            ->first()
            ->Total_Suma;

        //dd($subQueryTotal);

        $ordenes = DB::table('ct_orden_venta')
            ->join('agenda as a', 'a.id', '=', 'ct_orden_venta.id_agenda')
            ->join('paciente as p', 'p.id', '=', 'a.id_paciente')
            ->join('users as u', 'a.id_usuariomod', '=', 'u.id')
            ->join('ct_orden_venta_pago as ctp', 'ct_orden_venta.id', '=', 'ctp.id_orden')
            ->join('ct_tipo_pago as ct', 'ctp.tipo', '=', 'ct.id')
            ->join('users as u1', function ($join) {
                $join->on('a.id_doctor1', '=', 'u1.id')
                    ->where('u1.id_tipo_usuario', 3);
            })
            ->join('seguros as s', 's.id', '=', 'ct_orden_venta.id_seguro')
            ->where('ct_orden_venta.estado', 1)
            ->whereBetween('ct_orden_venta.fecha_emision', [$fecha . ' 00:00:00', $fecha_hasta . ' 23:59:00'])
            ->select(
                DB::raw('FORMAT(ct_orden_venta.valor_oda, 2) as valor_oda'),
                DB::raw('FORMAT(ct_orden_venta.total, 2) as total_suma'),

                'ct_orden_venta.id as id',
                'ctp.tipo',
                'ct.nombre as nombre_tipo',
                'ctp.valor',
                DB::raw('SUBSTRING(a.fechaini, 1, 10) as fechaCita'),
                DB::raw('SUBSTRING(a.fechaini, 11, 6) as horaCita'),
                'a.proc_consul',
                'a.cortesia as cortesia',
                DB::raw('CONCAT(p.nombre1, " ", p.apellido1) as nombres'),
                DB::raw('CONCAT(u.apellido1, " ", u.nombre1) as admision'),
                DB::raw('CONCAT(u1.apellido1, " ", u1.nombre1) as doctor'),
                DB::raw("
            CASE
                WHEN ct_orden_venta.caja = 'LABORATORIO' THEN 'LABORATORIO'
                WHEN a.id_doctor1 IS NOT NULL AND a.id_doctor1 = '4444444444' THEN 'LABORATORIO'
                WHEN a.proc_consul = 0 THEN 'CONSULTA'
                WHEN a.proc_consul = 1 THEN 'PROCEDIMIENTO'
            END as procedimiento
        "),
                's.nombre as seguro',
                DB::raw('IF(ctp.tipo = 1, ctp.valor, "0.00") as efectivo'),
                DB::raw('IF(ctp.tipo = 2, ctp.valor, "0.00") as cheque'),
                DB::raw('IF(ctp.tipo = 3 OR ctp.tipo = 5, ctp.valor, "0.00") as deposito'),
                DB::raw('IF(ctp.tipo = 4 OR ctp.tipo = 6, ctp.valor, "0.00") as tarjeta'),
                DB::raw('IF(ctp.tipo = 9, ctp.valor, "0.00") as datalink'),
                DB::raw('IF(ctp.tipo = 10, ctp.valor, "0.00") as pagoapp'),

                DB::raw('"' . $subQueryEfectivo . '" as total_global_efectivo'),
                DB::raw('"' . $subQueryCheque . '" as total_global_cheque'),
                DB::raw('"' . $subQueryDeposito . '" as total_global_deposito'),
                DB::raw('"' . $subQueryTarjeta . '" as total_global_tarjeta'),
                DB::raw('"' . $subQuerySeguro . '" as total_valor_oda'),
                DB::raw('"' . $subQueryDatalink . '" as total_global_datalink'),
                DB::raw('"' . $subQueryApp . '" as total_global_app'),
                DB::raw('"' . $subQueryTotal . '" as total_global_total')



            )->groupBy('ct_orden_venta.id');


        if (!is_null($id_empresa)) {
            $ordenes  = $ordenes->where('ct_orden_venta.id_empresa', $id_empresa);
        }
        if (!is_null($tipo)) {
            $ordenes             = $ordenes->where('a.proc_consul', $tipo);
            $facturas_pendientes = $facturas_pendientes->where('agenda.proc_consul', $tipo);
        }
        if (!is_null($caja)) {
            $ordenes = $ordenes->where('ct_orden_venta.caja', $caja);
            if ($caja == 'LABORATORIO') {
                $ordenes = $ordenes->where('ct_orden_venta.caja', $caja)->where('ct_orden_venta.id_Seguro', '<>', '1');
            }
        } else {
            $ordenes = $ordenes->where('ct_orden_venta.caja', '<>', 'LABORATORIO');
        }
        if (!is_null($doctor)) {
            $ordenes             = $ordenes->where('a.id_doctor1', $doctor);
            $facturas_pendientes = $facturas_pendientes->where('agenda.id_doctor1', $doctor);
        }
        if (!is_null($id_paciente)) {
            $ordenes  = $ordenes->where('a.id_paciente', $id_paciente);
            $usuario_paciente = Pacientes::find($id_paciente);
        }

        //dd($ordenes->get());

        return  $ordenes = $ordenes->get();
    }

    public static function buscarVentas($r)
    {
        return self::join('ct_clientes as c', 'ct_ventas.id_cliente', 'c.identificacion')
            ->join('users as uc', 'ct_ventas.id_usuariocrea', 'uc.id')
            ->join('users as um', 'ct_ventas.id_usuariomod', 'um.id')
            ->leftjoin('seguros as s', 'ct_ventas.seguro_paciente', 's.id')
            ->where(function ($q) use ($r) {
                if ($r->c != '') {
                    $q->where('c.identificacion', $r->c);
                }
                if ($r->d != '' && $r->h != '') {
                    $q->whereBetween('ct_ventas.fecha', [$r->d . ' 00:00:00', $r->h . ' 23:59:59']);
                }
            })
            ->where('ct_ventas.tipo', '!=', 'N-D')
            ->where('id_empresa', session('id_empresa'))
            ->whereYear('fecha', session('periodo'))
            ->where('ct_ventas.tipo', '!=', 'N-C')
            ->orderBy('fecha', 'DESC')
            ->get([
                'ct_ventas.id',
                'ct_ventas.fecha',
                'ct_ventas.nro_comprobante',
                'ct_ventas.nro_autorizacion',
                'ct_ventas.nombre_cliente',
                'ct_ventas.id_asiento',
                'ct_ventas.doc_electronico',
                'ct_ventas.tipo',
                'ct_ventas.nombres_paciente',
                'ct_ventas.procedimientos',
                'ct_ventas.fecha_procedimiento',
                'ct_ventas.estado',
                DB::raw('CONCAT(SUBSTRING(uc.nombre1, 1, 1), ". ", uc.apellido1) AS usuariocrea'),
                DB::raw('IF(ct_ventas.estado=0, CONCAT(SUBSTRING(um.nombre1, 1, 1), ". ", um.apellido1), "") AS usuariomod'),
                's.nombre as seguro',
                'ct_ventas.id_cliente',
                'ct_ventas.concepto',
                DB::raw('ROUND(ct_ventas.subtotal_12, 4) AS subtotal_12'),
                DB::raw('ROUND(ct_ventas.subtotal_0, 4) AS subtotal_0'),
                DB::raw('ROUND(ct_ventas.subtotal_12 + ct_ventas.subtotal_0, 4) AS subtotal_total'),
                'ct_ventas.impuesto',
                'ct_ventas.total_final'
            ]);
    }
    public static function cambiarEstado($r)
    {
        echo '<pre>';
        print_r($r->input());
        exit;
    }
    public static function ventasAts($r)
    {
        if ($r->periodo != '') {
            $dato = explode('-', $r->periodo);
            return DB::table('ct_ventas as v')
                ->join('ct_clientes as c', 'v.id_cliente', '=', 'c.identificacion')
                ->leftJoin('ct_cliente_retencion as r', 'v.id', '=', 'r.id_factura')
                ->select(
                    'v.id',
                    'v.tipo as tipo_doc',
                    'v.nro_comprobante',
                    'c.nombre',
                    'c.tipo',
                    'c.identificacion',
                    DB::raw("'18' as tipo_comprobante"),
                    DB::raw('0 as baseex'),
                    'v.subtotal_0',
                    'v.subtotal_12',
                    'v.descuento',
                    'v.iva_total',
                    'v.total_final',
                    DB::raw('COALESCE(r.valor_fuente, 0) as valor_fuente'),
                    DB::raw('COALESCE(r.valor_iva, 0) as valor_iva')
                )
                ->whereYear('v.fecha', $dato[0])
                ->whereMonth('v.fecha', $dato[1])
                ->where('v.estado', '!=', 0)
                ->get();
        }
    }
    public function retenciones()
    {
        return $this->belongsTo('Sis_medico\v3\contable\ClientesRetenciones', 'id', 'id_factura');
    }
    public function comp_ingreso()
    {
        return $this->hasMany('Sis_medico\v3\contable\DetalleComprobanteIngreso', 'id_factura');
    }
    public function cruce()
    {
        return $this->hasMany('Sis_medico\v3\contable\DetalleCruceClientes', 'id_factura');
    }
    public function chequepost()
    {
        return $this->hasMany('Sis_medico\v3\contable\DetalleChequePost', 'id_factura');
    }
    public function cruce_cuentas()
    {
        return $this->hasMany('Sis_medico\v3\contable\CruceCuentasClientes', 'id_factura');
    }
    public function credito()
    {
        return $this->hasMany('Sis_medico\v3\contable\DetalleCreditoClientes', 'id_factura');
    }
    public static function updateRecibidoSri($idG)
    {
        $arrayDoc = [
            'doc_electronico' => 4,
        ];
        self::where('id', $idG)
            ->update($arrayDoc);
    }
    public static function updateXmlFirmado($idG)
    {
        $arrayDoc = [
            'doc_electronico' => 3,
        ];
        self::where('id', $idG)
            ->update($arrayDoc);
    }
    public function cliente()
    {
        return $this->belongsTo('Sis_medico\v3\contable\Clientes', 'id_cliente', 'identificacion');
    }
    public function usuario()
    {
        return $this->belongsTo('Sis_medico\v3\contable\Usuarios', 'id_usuariocrea');
    }
    public static function updateValidacionXSD($idG)
    {
        $arrayDoc = [
            'doc_electronico' => 2,
        ];
        self::where('id', $idG)
            ->update($arrayDoc);
    }
    public static function updateGenerarXML($idG, $clave)
    {
        $arrayDoc = [
            'doc_electronico' => 1,
            'nro_autorizacion' => $clave,
        ];
        self::where('id', $idG)
            ->update($arrayDoc);
    }
    public static function updateSinGenerarXML($idG)
    {
        $arrayDoc = [
            'doc_electronico' => 7,
        ];
        self::where('id', $idG)
            ->update($arrayDoc);
    }
    public static function valoresVentas()
    {
        $id_empresa = session('id_empresa');
        $periodo = session('periodo');
        /*$sql = "select * from ((SELECT 'Privadas'as tipo,
        IFNULL(SUM(CASE WHEN MONTH(cv.fecha)=1 THEN ((cv.subtotal_0)+(cv.subtotal_12)-(cv.descuento)) END),0) ene,
        IFNULL(SUM(CASE WHEN MONTH(cv.fecha)=2 THEN ((cv.subtotal_0)+(cv.subtotal_12)-(cv.descuento)) END),0) feb,
        IFNULL(SUM(CASE WHEN MONTH(cv.fecha)=3 THEN ((cv.subtotal_0)+(cv.subtotal_12)-(cv.descuento)) END),0) mar,
        IFNULL(SUM(CASE WHEN MONTH(cv.fecha)=4 THEN ((cv.subtotal_0)+(cv.subtotal_12)-(cv.descuento)) END),0) abr,
        IFNULL(SUM(CASE WHEN MONTH(cv.fecha)=5 THEN ((cv.subtotal_0)+(cv.subtotal_12)-(cv.descuento)) END),0) may,
        IFNULL(SUM(CASE WHEN MONTH(cv.fecha)=6 THEN ((cv.subtotal_0)+(cv.subtotal_12)-(cv.descuento)) END),0) jun,
        IFNULL(SUM(CASE WHEN MONTH(cv.fecha)=7 THEN ((cv.subtotal_0)+(cv.subtotal_12)-(cv.descuento)) END),0) jul,
        IFNULL(SUM(CASE WHEN MONTH(cv.fecha)=8 THEN ((cv.subtotal_0)+(cv.subtotal_12)-(cv.descuento)) END),0) ago,
        IFNULL(SUM(CASE WHEN MONTH(cv.fecha)=9 THEN ((cv.subtotal_0)+(cv.subtotal_12)-(cv.descuento)) END),0) sep,
        IFNULL(SUM(CASE WHEN MONTH(cv.fecha)=10 THEN ((cv.subtotal_0)+(cv.subtotal_12)-(cv.descuento)) END),0) oct,
        IFNULL(SUM(CASE WHEN MONTH(cv.fecha)=11 THEN ((cv.subtotal_0)+(cv.subtotal_12)-(cv.descuento)) END),0) nov,
        IFNULL(SUM(CASE WHEN MONTH(cv.fecha)=12 THEN ((cv.subtotal_0)+(cv.subtotal_12)-(cv.descuento)) END),0) dic
        FROM ct_ventas cv
        JOIN seguros s ON s.id = cv.seguro_paciente
        WHERE cv.estado = 1 AND cv.id_empresa = $id_empresa AND cv.tipo IN ('VEN-FA', 'VENFA-CO') AND YEAR(cv.fecha)=$periodo and s.tipo=1)
        UNION ALL
        (SELECT 'Particulares'as tipo,
        IFNULL(SUM(CASE WHEN MONTH(cv.fecha)=1 THEN ((cv.subtotal_0)+(cv.subtotal_12)-(cv.descuento)) END),0) ene,
        IFNULL(SUM(CASE WHEN MONTH(cv.fecha)=2 THEN ((cv.subtotal_0)+(cv.subtotal_12)-(cv.descuento)) END),0) feb,
        IFNULL(SUM(CASE WHEN MONTH(cv.fecha)=3 THEN ((cv.subtotal_0)+(cv.subtotal_12)-(cv.descuento)) END),0) mar,
        IFNULL(SUM(CASE WHEN MONTH(cv.fecha)=4 THEN ((cv.subtotal_0)+(cv.subtotal_12)-(cv.descuento)) END),0) abr,
        IFNULL(SUM(CASE WHEN MONTH(cv.fecha)=5 THEN ((cv.subtotal_0)+(cv.subtotal_12)-(cv.descuento)) END),0) may,
        IFNULL(SUM(CASE WHEN MONTH(cv.fecha)=6 THEN ((cv.subtotal_0)+(cv.subtotal_12)-(cv.descuento)) END),0) jun,
        IFNULL(SUM(CASE WHEN MONTH(cv.fecha)=7 THEN ((cv.subtotal_0)+(cv.subtotal_12)-(cv.descuento)) END),0) jul,
        IFNULL(SUM(CASE WHEN MONTH(cv.fecha)=8 THEN ((cv.subtotal_0)+(cv.subtotal_12)-(cv.descuento)) END),0) ago,
        IFNULL(SUM(CASE WHEN MONTH(cv.fecha)=9 THEN ((cv.subtotal_0)+(cv.subtotal_12)-(cv.descuento)) END),0) sep,
        IFNULL(SUM(CASE WHEN MONTH(cv.fecha)=10 THEN ((cv.subtotal_0)+(cv.subtotal_12)-(cv.descuento)) END),0) oct,
        IFNULL(SUM(CASE WHEN MONTH(cv.fecha)=11 THEN ((cv.subtotal_0)+(cv.subtotal_12)-(cv.descuento)) END),0) nov,
        IFNULL(SUM(CASE WHEN MONTH(cv.fecha)=12 THEN ((cv.subtotal_0)+(cv.subtotal_12)-(cv.descuento)) END),0) dic
        FROM ct_ventas cv
        JOIN seguros s ON s.id = cv.seguro_paciente
        WHERE cv.estado = 1 AND cv.id_empresa = $id_empresa AND cv.tipo IN ('VEN-FA', 'VENFA-CO') AND YEAR(cv.fecha)=$periodo and s.tipo=2)
        UNION ALL
        (
        SELECT 'Relacionadas'as tipo,
        IFNULL(SUM(CASE WHEN MONTH(cv.fecha)=1 THEN ((cv.subtotal_0)+(cv.subtotal_12)-(cv.descuento)) END),0) ene,
        IFNULL(SUM(CASE WHEN MONTH(cv.fecha)=2 THEN ((cv.subtotal_0)+(cv.subtotal_12)-(cv.descuento)) END),0) feb,
        IFNULL(SUM(CASE WHEN MONTH(cv.fecha)=3 THEN ((cv.subtotal_0)+(cv.subtotal_12)-(cv.descuento)) END),0) mar,
        IFNULL(SUM(CASE WHEN MONTH(cv.fecha)=4 THEN ((cv.subtotal_0)+(cv.subtotal_12)-(cv.descuento)) END),0) abr,
        IFNULL(SUM(CASE WHEN MONTH(cv.fecha)=5 THEN ((cv.subtotal_0)+(cv.subtotal_12)-(cv.descuento)) END),0) may,
        IFNULL(SUM(CASE WHEN MONTH(cv.fecha)=6 THEN ((cv.subtotal_0)+(cv.subtotal_12)-(cv.descuento)) END),0) jun,
        IFNULL(SUM(CASE WHEN MONTH(cv.fecha)=7 THEN ((cv.subtotal_0)+(cv.subtotal_12)-(cv.descuento)) END),0) jul,
        IFNULL(SUM(CASE WHEN MONTH(cv.fecha)=8 THEN ((cv.subtotal_0)+(cv.subtotal_12)-(cv.descuento)) END),0) ago,
        IFNULL(SUM(CASE WHEN MONTH(cv.fecha)=9 THEN ((cv.subtotal_0)+(cv.subtotal_12)-(cv.descuento)) END),0) sep,
        IFNULL(SUM(CASE WHEN MONTH(cv.fecha)=10 THEN ((cv.subtotal_0)+(cv.subtotal_12)-(cv.descuento)) END),0) oct,
        IFNULL(SUM(CASE WHEN MONTH(cv.fecha)=11 THEN ((cv.subtotal_0)+(cv.subtotal_12)-(cv.descuento)) END),0) nov,
        IFNULL(SUM(CASE WHEN MONTH(cv.fecha)=12 THEN ((cv.subtotal_0)+(cv.subtotal_12)-(cv.descuento)) END),0) dic
        FROM ct_ventas cv
        JOIN seguros s ON s.id = cv.seguro_paciente
        JOIN ct_empresa_relacionada cer ON cv.id_cliente = cer.id_cliente AND cer.estado = 1
        WHERE cv.estado = 1 AND cv.id_empresa = $id_empresa AND cv.tipo IN ('VEN-FA', 'VENFA-CO') AND YEAR(cv.fecha)=$periodo
        )
        ) as ventas";*/
        $sql = "select * from ((SELECT 'Ventas'as tipo,
        IFNULL(SUM(CASE WHEN MONTH(cv.fecha)=1 THEN ((cv.subtotal_0)+(cv.subtotal_12)-(cv.descuento)) END),0) ene,
        IFNULL(SUM(CASE WHEN MONTH(cv.fecha)=2 THEN ((cv.subtotal_0)+(cv.subtotal_12)-(cv.descuento)) END),0) feb,
        IFNULL(SUM(CASE WHEN MONTH(cv.fecha)=3 THEN ((cv.subtotal_0)+(cv.subtotal_12)-(cv.descuento)) END),0) mar,
        IFNULL(SUM(CASE WHEN MONTH(cv.fecha)=4 THEN ((cv.subtotal_0)+(cv.subtotal_12)-(cv.descuento)) END),0) abr,
        IFNULL(SUM(CASE WHEN MONTH(cv.fecha)=5 THEN ((cv.subtotal_0)+(cv.subtotal_12)-(cv.descuento)) END),0) may,
        IFNULL(SUM(CASE WHEN MONTH(cv.fecha)=6 THEN ((cv.subtotal_0)+(cv.subtotal_12)-(cv.descuento)) END),0) jun,
        IFNULL(SUM(CASE WHEN MONTH(cv.fecha)=7 THEN ((cv.subtotal_0)+(cv.subtotal_12)-(cv.descuento)) END),0) jul,
        IFNULL(SUM(CASE WHEN MONTH(cv.fecha)=8 THEN ((cv.subtotal_0)+(cv.subtotal_12)-(cv.descuento)) END),0) ago,
        IFNULL(SUM(CASE WHEN MONTH(cv.fecha)=9 THEN ((cv.subtotal_0)+(cv.subtotal_12)-(cv.descuento)) END),0) sep,
        IFNULL(SUM(CASE WHEN MONTH(cv.fecha)=10 THEN ((cv.subtotal_0)+(cv.subtotal_12)-(cv.descuento)) END),0) oct,
        IFNULL(SUM(CASE WHEN MONTH(cv.fecha)=11 THEN ((cv.subtotal_0)+(cv.subtotal_12)-(cv.descuento)) END),0) nov,
        IFNULL(SUM(CASE WHEN MONTH(cv.fecha)=12 THEN ((cv.subtotal_0)+(cv.subtotal_12)-(cv.descuento)) END),0) dic
        FROM ct_ventas cv
        JOIN seguros s ON s.id = cv.seguro_paciente
        WHERE cv.estado = 1 AND cv.id_empresa = $id_empresa AND cv.tipo IN ('VEN-FA', 'VENFA-CO') AND YEAR(cv.fecha)=$periodo and s.tipo=1)
        ) as ventas";
        $ventas = DB::select($sql);
        return $ventas;
    }
    public static function getVentasSinRetenciones()
    {
        /*return self::join('ct_clientes as c', 'ct_ventas.id_cliente', '=', 'c.identificacion')
            ->where('ct_ventas.tipo', '<>', 'VEN-FACT')
            ->where('ct_ventas.estado', '<>', 0)
            ->where('ct_ventas.id_empresa', '=', session('id_empresa'))
            ->where('ct_ventas.valor_contable', '>', 0)
            ->whereNotIn('ct_ventas.id_cliente', function ($query) {
                $query->select('id_cliente')
                    ->from('ct_ventas')
                    ->where('estado', 1);
            })
            ->get([
                'ct_ventas.id',
                'ct_ventas.id_cliente',
                'ct_ventas.numero',
                'c.nombre',
                'ct_ventas.concepto',
            ]);*/
        return self::join('ct_clientes as c', 'ct_ventas.id_cliente', '=', 'c.identificacion')
            ->leftJoin('ct_cliente_retencion as r', function ($join) {
                $join->on('ct_ventas.id', '=', 'r.id_factura')
                    ->where('r.estado', '=', 1);
            })
            ->where('ct_ventas.tipo', 'VEN-FA')
            ->where('ct_ventas.estado', '!=', 0)
            ->where('ct_ventas.id_empresa', session('id_empresa'))
            ->where('ct_ventas.valor_contable', '>', 0)
            ->whereNull('r.id_factura')
            ->get([
                'ct_ventas.id',
                'ct_ventas.id_cliente',
                'ct_ventas.numero',
                'c.nombre',
                'ct_ventas.concepto',
            ]);
    }
    public static function saveVenta($r)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        try {
            $origin = $r->input();
            $origin['d'] = '|';
            $electronica = 0;
            $arrayRespuesta = [];
            $empresa = Empresas::find(session('id_empresa'));
            $idusuario = Auth::user()->id;
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $cod_sucurs = PuntoEmision::where('estado', '1')->where('id', $r->establecimiento)->first();
            $c_sucursal = $cod_sucurs->codigo_sucursal;
            $cod_caj = DB::table('ct_caja')->where('estado', '1')->where('id', $r->emision)->first();
            $c_caja = $cod_caj->codigo_caja;
            $datos = [
                'sucursal' => $c_sucursal, //1
                'punto_emision' => $c_caja, //100
                'tipo' => 1
            ];
            $num_comprobante = self::generarNumeroDocumento($datos);
            if ($num_comprobante == '') {
                $arrayRespuesta = [
                    'status' => 'error',
                    'code' => 500,
                    'message' => 'La empresa no tiene configurada la información tributaria',
                    'footer' => '<a href="javascript:;" onclick="crearInfoTributaria();">¿Desea crear está información?</a>',
                    'reload' => ''
                ];
                DB::rollBack();
                return json_encode($arrayRespuesta);
            }
            $cliente = Clientes::where('identificacion', $r->identificacion_cliente)->first();
            $paciente = Pacientes::where('id', $r->identificacion_paciente)->first();
            $arrayAsientoCabecera = [
                'sucursal' => $c_sucursal,
                'punto_emision' => $c_caja,
                'fecha_asiento' => $r->fecha_emision,
                'fact_numero' => $num_comprobante,
                'id_empresa' => session('id_empresa'),
                'observacion' => $r->concepto,
                'valor' => $r->total_pagar,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ];
            $id_asiento_cabecera = Contabilidad::crearAsiento('ventas', 'ingreso', $arrayAsientoCabecera,  $r, "venta");
            if ($id_asiento_cabecera['status'] == 'error') {
                throw new Exception($id_asiento_cabecera['message']);
            }
            $deEmpresa = DeEmpresas::where('id_empresa', session('id_empresa'))->first();
            if ($deEmpresa != '') {
                $claveObj = new ClaveAcceso();
                $numComprobante = explode('-', $num_comprobante);
                $campos = [
                    'fecha_emision' => str_replace('/', '-', date('d/m/Y', strtotime($r->fecha_emision))),
                    'tipo_comprobante' => '01',
                    'ruc' => session('id_empresa'),
                    'tipo_ambiente' => $deEmpresa->ambiente,
                    'punto_establecimiento' => $numComprobante[0],
                    'punto_emision' => $numComprobante[1],
                    'numero_comprobante' => $numComprobante[2],
                    'codigo_numerico' => $numComprobante[2],
                    'tipo_emision' => 1
                ];
                $claveObj->llenarDatos($campos);
                $electronica = 1;
            }
            $msjk = ", Store: no tiene inventario";
            if ($empresa->inventario == 1) {
                $msjk = ", Store: tiene inventario";
            }
            $arrayCabeceraVenta = [
                'sucursal' => $c_sucursal,
                'punto_emision' => $c_caja,
                'numero' => $numComprobante[2],
                'nro_comprobante' => $num_comprobante,
                'id_asiento' => $id_asiento_cabecera,
                'id_empresa' => session('id_empresa'),
                'tipo' => $r->tipo_factura,
                'fecha' => $r->fecha_emision,
                'divisas' => $r->divisa,
                'nombre_cliente' => $cliente->nombre,
                'tipo_consulta' => $r->tipo,
                'id_cliente' => $r->identificacion_cliente,
                'direccion_cliente' => $r->direccion_cliente == $cliente->direccion_representante ? $cliente->direccion_representante : $r->direccion_cliente,
                'ruc_id_cliente' => $r->identificacion_cliente,
                'telefono_cliente' => $r['telefono_cliente'],
                'email_cliente' => $r->email_cliente == $cliente->email_representante ? $cliente->email_representante : $r->email_cliente,
                'orden_venta' => $r['orden_venta'],
                'nro_autorizacion' => $claveObj->clave_acceso,
                'id_paciente' => $r->identificacion_paciente,
                'nombres_paciente' => $paciente != '' ? $paciente->nombre1 . ' ' . $paciente->nombre2 . ' ' . $paciente->apellido1 . ' ' . $paciente->apellido2 : '',
                'id_hc_procedimiento' => $r['mov_paciente'],
                'seguro_paciente' => $r->id_seguro,
                'procedimientos' => $r->procedimiento,
                'fecha_procedimiento' => $r->fecha_procedimiento,
                'concepto' => $r->concepto,
                'copago' => $r['totalc'],
                'id_recaudador' => $r->recaudador,
                'ci_vendedor' => $r->vendedor,
                'vendedor' => $r['vendedor'],
                'electronica' => $r->electronica != '' ? $r->electronica : $electronica,
                'subtotal_0' => $r->subtotal0,
                'subtotal_12' => $r->subtotal12,
                'descuento' => $r->descuentototal,
                'base_imponible' => $r->total_pagar,
                'impuesto' => $r->iva,
                'total_final' => $r->total_pagar,
                'valor_contable' => $r->total_pagar,
                'dias_plazo' => $r->dias_plazo,
                'nota_electronica' => $r['nota_electronica'] != "" ?  json_encode($r['nota_electronica']) : json_encode([]),
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
                'mensaje_kardex' => $r["kardex_descripcion"] . "" . $msjk,
                'nota_array' => $r['nota_electronica'] != "" ?  json_encode($r['nota_electronica']) : json_encode([]),
                'doc_electronico' => 0,
                'emisor' => 'MD',
                'contraentrega' => $r->contraentrega,
            ];
            $idVenta = self::insertGetId($arrayCabeceraVenta);
            $r->c = 'FacturaVenta';
            $r->s = 'saveVenta';
            $r->d = $origin['d'];
            $r->m = self::$modelo;
            $r->o = 'Se agrego una nueva factura de venta con id.: ' . $idVenta;
            Auditorias::saveAuditoria($r, DB::getQueryLog());
            DB::disableQueryLog();
            $valorTotSin = 0;
            foreach ($r->id_pago as $key => $value) {
                if ($value != 7) {
                    $valorTotSin += $r["valor"][$key];
                }
            }
            $contComprobante = 0;
            foreach ($r->id_pago as $key => $row) {
                if ($row != 7) {
                    $arrayAsientoCabecera = [
                        'sucursal' => $c_sucursal,
                        'punto_emision' => $c_caja,
                        'fecha_asiento' => $r->fecha_emision,
                        'fact_numero' => $num_comprobante,
                        'id_empresa' => session('id_empresa'),
                        'observacion' => 'COMPROBANTE DE INGRESO FACT:' . $num_comprobante,
                        'valor' => $valorTotSin,
                        'id_usuariocrea' => $idusuario,
                        'id_usuariomod' => $idusuario,
                        'ip_creacion' => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                    ];
                    $id_asiento_cabecera = Contabilidad::crearAsiento('ventas', 'comprobante_ingreso', $arrayAsientoCabecera, $r);
                    if ($id_asiento_cabecera['status'] == 'error') {
                        throw new Exception($id_asiento_cabecera['message']);
                    }
                    $contComprobante++;
                }
                $fecha_pago = $r->fecha_pago[$key] != "" ? $r->fecha_pago[$key] : $r->fecha_emision;
                $val = 0;
                $val = $r->valor[$key];
                $arrayFormaPago = [
                    'id_ct_ventas'    => $idVenta,
                    'tipo'            => $r->id_pago[$key],
                    'fecha'           => $fecha_pago,
                    'tipo_tarjeta'    => $r->id_tarjetas[$key],
                    'numero'          => $r->numero[$key],
                    'banco'           => $r->id_bancos[$key],
                    'cuenta'          => $r->cuenta[$key],
                    'giradoa'         => $r->giradoA[$key],
                    'valor'           => $val,
                    'valor_base'      => $val,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                ];
                FormaPago::insert($arrayFormaPago);
            }
            $detalleArray = [];
            for ($j = 0; $j < count($r->codigo); $j++) {
                $detalleVentas = explode('|', $r->producto[$j]);
                $arrayAdicional = [
                    'nombre' => $r->nombreAdicional,
                    'detalle' => $r->valorAdicional
                ];
                if (config('data.empresa')->egreso_kardex == 1 && $r->bodega[$j] == '' && $r->kardex == 'on') {
                    $arrayRespuesta = [
                        'status' => 'error',
                        'code' => 500,
                        'message' => 'Debe seleccionar la bodega del producto en la fila: ' . $j,
                        'reload' => ''
                    ];
                    DB::rollBack();
                    return json_encode($arrayRespuesta);
                }
                $detalle = [
                    'id_ct_ventas' => $idVenta,
                    'id_ct_productos' => $r->codigo[$j],
                    'nombre' => $detalleVentas[2],
                    'descripcion_producto' => $detalleVentas[2],
                    'bodega' => $r->bodega[$j],
                    'cantidad' => $r->cantidad[$j],
                    'precio' => $r->precio[$j],
                    'descuento_porcentaje' => $r['descpor'],
                    'descuento' => $r->descuento[$j],
                    'extendido' => $r->precio[$j] * $r->cantidad[$j],
                    'detalle' => $r->descripcion[$j],
                    'check_iva' => $r->iva_fila[$j] != '' ? 1 : 0,
                    'porcentaje' => config('data.impuestoIva'),
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                    'otros' => json_encode($arrayAdicional)
                ];
                VentasDetalle::insert($detalle);
                $producto = Productos::where('id_empresa', session('id_empresa'))
                    ->where('id', $detalleVentas[0])
                    ->first();
                $detalleArray[] = [
                    "codigo" => $producto->cta_ventas,
                    "nombre" => $producto->nombre,
                    "cantidad" => $r->cantidad[$j],
                    "precio" =>  $r->precio[$j],
                    "descpor" =>  $r['descpor'],
                    "descuento" =>  $r->descuento[$j],
                    "precioneto" =>  $r->precio[$j] * $r->cantidad[$j], //extendido
                    "detalle" =>  $r->descripcion[$j],
                    "iva" =>  $r->iva_fila[$j] != '' ? 1 : 0,
                    "ivareal" => config('data.impuestoIva')
                ];
            }
            if ($contComprobante > 0) {
                $arrayComprobante = [
                    'observaciones' => 'COMPROBANTE DE INGRESO FACT: ' . $num_comprobante,
                    'estado' => '1',
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'fecha' => $r->fecha_emision,
                    'secuencia' =>  $num_comprobante,
                    'divisas' => '1',
                    'id_empresa' => session('id_empresa'),
                    'total_ingreso' => 0,
                    'id_cliente' => $r->identificacion_cliente,
                    'autollenar' => "Cancela FV : " .  $num_comprobante,
                    'id_vendedor' =>  $r->vendedor,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                ];
                $totalV = 0;
                $vent = Ventas::find($idVenta);
                $vent->valor_contable = $vent->valor_contable - $totalV;
                $vent->save();
                $id_comprobante = ComprobanteIngreso::insertGetId($arrayComprobante);
                $totalFactura = 0;
                for ($i = 0; $i < count($r->id_pago); $i++) {
                    $val = 0;
                    $val = $r->valor[$i];
                    $totalFactura += $val;
                    $fecha_pago = $r->fecha_pago[$i] != "" ? $r->fecha_pago[$i] : $r->fecha_emision;
                    if ($r->id_pago[$i] != 7) {
                        $totalV += $val;
                        $arrayDetalleComprobante = [
                            'id_comprobante'  => $id_comprobante,
                            'fecha' => $fecha_pago,
                            'numero' => $r->numero[$i],
                            'id_banco' => $r->id_bancos[$i],
                            'id_tipo_tarjeta' => $r->id_tarjetas[$i],
                            'id_tipo' => $r->id_pago[$i],
                            'total' => $val,
                            'cuenta' => $r->cuenta[$i],
                            'girador' => $r->giradoA[$i],
                            'estado' => '1',
                            'ip_creacion' => $ip_cliente,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariocrea' => $idusuario,
                            'id_usuariomod' => $idusuario,
                        ];
                        DetallePagoIngreso::insert($arrayDetalleComprobante);
                    }
                }
                $reG = ComprobanteIngreso::find($id_comprobante);
                $r->c = 'FacturaVentas';
                $r->s = 'saveVentas';
                $r->d = $origin['d'];
                $r->m = self::$modelo;
                $r->o = 'Se agrego un comprobante de ingreso id.: ' . $reG;
                Auditorias::saveAuditoria($r, DB::getQueryLog());
                DB::disableQueryLog();
                $reG->total_ingreso = $totalV;
                $reG->save();
                $arrayDetalleComprobanteIngreso = [
                    'id_comprobante'    => $id_comprobante,
                    'fecha'             => $fecha_pago,
                    'observaciones'     => "Cancela FV : " . $num_comprobante,
                    'id_factura'        => $idVenta,
                    'secuencia_factura' => $num_comprobante,
                    'total_factura'     => $totalFactura,
                    'total'             => $totalV,
                    'estado'            => '1',
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                ];
                DetalleComprobanteIngreso::insert($arrayDetalleComprobanteIngreso);
            }
            $arrayInfoTributaria = [
                'secuencial_nro' => (int)$numComprobante[2],
                'numero_factura' => $numComprobante[0] . '-' . $numComprobante[1] . '-' . $numComprobante[2],
            ];
            InfoTributaria::where('id_empresa', session('id_empresa'))
                ->where('id_maestro_documentos', 1)
                ->where('cod_sucursal', $c_sucursal)
                ->where('cod_caja', $c_caja)
                ->update($arrayInfoTributaria);
            if (isset($r->kardex)) {
                $kardex = Kardex::newgenerarKardex($idVenta);
                if (!$kardex["status"]) {
                    throw new Exception($kardex["msj"]);
                }
                if (isset($kardex["generate"]) and $kardex["generate"]) {
                    $kardex["asiento_costo"] = $kardex["asiento_costo"];
                    $logCosto = LogAsiento::guardarAsientoCostoVenta($idVenta, $kardex["asiento_costo"]);
                    if (!$logCosto["status"]) {
                        throw new Exception($kardex["logCosto"]);
                    }
                }
            }
            if ($r->identificacion_cliente= '0993296015001') {
                $arrayDatos = [
                    "token" => '8c0a00ec19933215dc29225e645ea714',
                    "id_grupo_empresa" => "17",
                    "tipo" => "2",
                    "fecha" => date('Y-m-d'),
                    "archivosri" => "1",
                    "proveedor" => session('id_empresa'),
                    "termino" => "1", //ct_termino
                    "observacion" => $r->concepto,
                    //"punto_emision" => $cod_caj->id, //id ct_caja
                    //"sucursal" => $cod_sucurs->id, //id ct_sucursles
                    "punto_emision" => 45, //id ct_caja> $cod_caj->id, //id ct_caja
                    "sucursal" => 16, //id ct_sucursles
                    "f_caducidad" => date('Y-m-d'),
                    "tipo_gasto" => "1", //??
                    "autorizacion" => $claveObj->clave_acceso,
                    "f_autorizacion" => "2025-05-14",
                    "serie" => $c_sucursal . '-' . $c_caja,
                    "secuencia_factura" => $numComprobante[2],
                    "credito_tributario" => "02",
                    "tipo_comprobante" => "01",
                    "subtotal_01" => $r->subtotal0,
                    "subtotal_121" => $r->subtotal12,
                    "descuento1" => $r->descuentototal,
                    "tarifa_iva1" => $r->iva,
                    "ice_final1" => "0.0000",
                    "total1" => $r->baseImponibleTotal,
                    "total_final1" => $r->baseImponibleTotal,
                    "base1" => $r->baseImponibleTotal,
                    "tipo_electronica" => "1", //??
                    "forma_pago" => "20",
                    "detalle" => $detalleArray,
                    "prov" => [
                        "id_proveedor" => $empresa->id,
                        "razonsocial" => $empresa->razonsocial,
                        "nombrecomercial" => $empresa->nombrecomercial,
                        "ciudad" => $empresa->ciudad,
                        "direccion" => $empresa->direccion,
                        "email" => $empresa->email,
                        "telefono1" => $empresa->telefono1,
                        "telefono2" => $empresa->telefono2
                    ]
                ];

                $ch = curl_init('https://bd.aitrol.com/app/registro/fc/compra');

                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/json',
                    'Accept: application/json'
                ]);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($arrayDatos));

                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                // Procesar la respuesta
                if ($httpCode == 200 || $httpCode == 201) {
                    $respuesta = json_decode($response, true);
                    // éxito
                } else {
                    // error
                    $error = json_decode($response, true);
                }
            }
            DB::commit();
            //DB::rollBack();
            $arrayRespuesta = [
                'status' => 'success',
                'code' => 200,
                'message' => 'Registro guardado correctamente',
                'data' => $r->input(),
                'reload' => 'v3/ventas/listadeventas'
            ];
            return json_encode($arrayRespuesta);
        } catch (Exception $e) {
            DB::rollBack();
            $arrayRespuesta = [
                'status' => 'error',
                'code' => 500,
                'message' => $e->getMessage(),
                'linea' => $e->getLine(),
                'file' => $e->getFile(),
                'data' => $r->input(),
                'reload' => ''
            ];
            $r->c = 'FacturaVenta';
            $r->s = 'saveVenta';
            $r->d =  $origin['d'];
            $r->m = self::$modelo;
            $r->o = 'Ocurrio un error: ' . $e->getMessage();
            Auditorias::saveAuditoria($r, DB::getQueryLog());
            DB::disableQueryLog();
            return json_encode($arrayRespuesta);
        }
    }
    private static function generarNumeroDocumento($datos)
    {
        $numero = '';
        $num = InfoTributaria::where('id_empresa', session('id_empresa'))
            ->where('id_maestro_documentos', $datos['tipo'])
            ->where('cod_sucursal', $datos['sucursal'])
            ->where('cod_caja', $datos['punto_emision'])
            ->first([
                'cod_sucursal',
                'cod_caja',
                'secuencial_nro'
            ]);
        if ($num != '') {
            $numero = $num->secuencial_nro + 1;
            $numero = $num->cod_sucursal . '-' . $num->cod_caja . '-' . str_pad($numero, 9, 0, STR_PAD_LEFT);
        }
        return $numero;
    }
    public static function callPrivateStisGenerarNumero($datos)
    {
        return self::generarNumeroDocumento($datos);
    }
    public static function getVentasNetas($r)
    {
        $dClinte = '';
        if ($r->cliente != '') {
            $dClinte = explode('|', $r->cliente);
            $dClinte = ' AND ct_ventas.id_cliente=' . $dClinte[0];
        }
        $estadoGeneral = '';
        if ($r->estado != '') {
            $estadoGeneral = ' AND ct_ventas.estado=' . $r->estado;
        }
        $ventas = self::select(
            'ct_ventas.id',
            'ct_ventas.fecha',
            'ct_ventas.nro_comprobante',
            'ct_ventas.id_cliente',
            'ct_clientes.nombre as nombre_cliente',
            'ct_ventas.vendedor',
            DB::raw('(ct_ventas.subtotal_0 + ct_ventas.subtotal_12) as subtotal'),
            'ct_ventas.descuento',
            DB::raw('(ct_ventas.subtotal_0 + ct_ventas.subtotal_12) - ct_ventas.descuento as vn'),
            DB::raw('IFNULL(ct_nota_credito_clientes.total_abonos, 0) as nc'),
            DB::raw('((ct_ventas.subtotal_0 + ct_ventas.subtotal_12) - ct_ventas.descuento) - IFNULL(ct_nota_credito_clientes.total_abonos, 0) as vnf'),
            'ct_ventas.estado',
            DB::raw("CONCAT(SUBSTRING(users.nombre1, 1, 1), '. ', users.apellido1) as usuariocrea"),
            DB::raw("IF(ct_ventas.estado = 0, CONCAT(SUBSTRING(users_mod.nombre1, 1, 1), '. ', users_mod.apellido1), '') as usuariomod"),
            DB::raw("(SELECT SUM(subtotal_0 + subtotal_12) FROM ct_ventas WHERE fecha BETWEEN '$r->d 00:00:00' AND '$r->h 23:59:59' $dClinte $estadoGeneral) as total_subtotal"),
            DB::raw("(SELECT SUM(descuento) FROM ct_ventas WHERE fecha BETWEEN '$r->d 00:00:00' AND '$r->h 23:59:59' $dClinte $estadoGeneral) as total_descuento"),
            DB::raw("(SELECT SUM((subtotal_0 + subtotal_12) - descuento) FROM ct_ventas WHERE fecha BETWEEN '$r->d 00:00:00' AND '$r->h 23:59:59' $dClinte $estadoGeneral) as total_vn"),
            DB::raw("IFNULL((SELECT SUM(IFNULL(total_abonos, 0)) FROM ct_nota_credito_clientes WHERE id_factura = ct_ventas.id AND estado != 0 $dClinte $estadoGeneral), 0) as total_nc")
        )
            ->join('ct_clientes', 'ct_ventas.id_cliente', '=', 'ct_clientes.identificacion')
            ->join('users', 'ct_ventas.id_usuariocrea', '=', 'users.id')
            ->leftJoin('users as users_mod', 'ct_ventas.id_usuariomod', '=', 'users_mod.id')
            ->leftJoin('ct_nota_credito_clientes', function ($join) {
                $join->on('ct_ventas.id', '=', 'ct_nota_credito_clientes.id_factura')
                    ->where('ct_nota_credito_clientes.estado', '!=', 0);
            })
            ->where(function ($q) use ($r) {
                $q->whereBetween('ct_ventas.fecha', [$r->d, $r->h]);
                if ($r->cliente != '') {
                    $dClinte = explode('|', $r->cliente);
                    $q->where('ct_ventas.id_cliente', $dClinte[0]);
                }
                if ($r->vendedor != '') {
                    $q->where('ct_ventas.ci_vendedor', $r->vendedor);
                }
                if ($r->estado != '') {
                    $q->where('ct_ventas.estado', $r->estado);
                }
            })
            ->get();
        return $ventas;

        return self::leftjoin('ct_clientes as c', 'ct_ventas.id_cliente', 'c.identificacion')
            //->where('id_empresa', session('id_empresa'))
            ->where(function ($q) use ($r) {
                $q->whereBetween('ct_ventas.fecha', [$r->d, $r->h]);
                if ($r->cliente != '') {
                    $q->where('ct_ventas.id_cliente', $r->cliente);
                }
                if ($r->vendedor != '') {
                    $q->where('ct_ventas.ci_vendedor', $r->vendedor);
                }
                if ($r->estado != '') {
                    $q->where('ct_ventas.estado', $r->estado);
                }
            })->get([
                'ct_ventas.id',
                'ct_ventas.fecha',
                'ct_ventas.nro_comprobante',
                'ct_ventas.id_cliente',
                'c.nombre as cliente',
                'ct_ventas.ci_vendedor',
                'ct_ventas.subtotal_0',
                'ct_ventas.subtotal_12',
                'ct_ventas.descuento',
                'ct_ventas.total_final',
            ]);
    }
    public static function getVentaPrint($id)
    {
        $contenidoHtml = '';
        $venta = self::selectRaw(
            '(select  
            GROUP_CONCAT(t.nombre,":",f.valor SEPARATOR "|") 
            from ct_forma_pago f 
            join ct_tipo_pago t on f.tipo=t.id 
            where f.id_ct_ventas=ct_ventas.id) as formaPago,
            ct_ventas.id,
            ct_ventas.fecha,
            ct_ventas.nro_comprobante,
            ct_ventas.id_cliente,
            p.nombre,
            p.codigo,
            d.precio,
            d.cantidad,
            d.descuento,
            d.porcentaje,
            d.check_iva,
            c.nombre as cliente,
            c.identificacion,
            c.direccion_representante,
            ct_ventas.telefono_cliente as telefono1_representante,
            c.telefono2_representante,
            ct_ventas.email_cliente,
            ct_ventas.dias_plazo,
            ct_ventas.concepto,
            ct_ventas.nro_autorizacion,
            s.nombre as seguro'
        )
            ->join('ct_detalle_venta as d', 'ct_ventas.id', 'd.id_ct_ventas')
            ->leftjoin('ct_productos as p', function ($q) {
                $q->on('d.id_ct_productos', 'p.codigo');
                //->where("p.id_empresa", session("id_empresa"));
            })
            ->join('ct_clientes as c', 'ct_ventas.id_cliente', 'c.identificacion')
            ->leftjoin('seguros as s', 'ct_ventas.seguro_paciente', 's.id')
            ->where('ct_ventas.id', $id)
            ->get();
        if (count($venta) > 0) {
            $fechaMax = date("d/m/Y", strtotime($venta[0]->fecha . "+ " . $venta[0]->dias_plazo . " days"));
            $seguro = $venta[0]->seguro;
            $formPago = $venta[0]->formaPago;
            $contenidoHtml .= '<div class="row">
                    <div class="col-12">
                        <h4>
                            <img src="' . url('/') . '/logo/' . config('data.empresa')->logo . '" style="width:120px;height: 60px; margin-left: 11px;" />
                            <small class="float-right">Fecha: ' . date('d/m/Y', strtotime($venta[0]->fecha)) . '</small>
                        </h4>
                    </div>
                </div>
                <div class="row invoice-info">
                    <div class="col-sm-4 invoice-col">
                        <address>
                            <strong>' . config('data.empresa')->razonsocial . '</strong><br>
                            ' . config('data.empresa')->id . '<br>
                            ' . config('data.empresa')->direccion . '<br>
                            Teléfono: ' . config('data.empresa')->telefono1 . ' ' . config('data.empresa')->telefono2 . '<br>
                            E-mail: <a href="mailto:' . config('data.empresa')->email . '">' . config('data.empresa')->email . '</a>
                        </address>
                    </div>
                    <div class="col-sm-4 invoice-col">
                        <address>
                            <strong>' . $venta[0]->cliente . '</strong><br>
                            ' . $venta[0]->identificacion . '<br>
                            ' . $venta[0]->direccion . '<br>
                            Teléfono: ' . $venta[0]->telefono1_representante . ' ' . $venta[0]->telefono2_representante . '<br>
                            E-mail: <a href="mailto:' . $venta[0]->email_cliente . '">' . $venta[0]->email_cliente . '</a>
                        </address>
                    </div>
                    <div class="col-sm-4 invoice-col">
                        <b>Factura #' . $venta[0]->nro_comprobante . '</b><br>
                        <br>
                        <b>Order ID:</b> <br>
                        <b>Fecha máxima de pago:</b> ' . $fechaMax . '<br>
                        <b># autorización:</b> <span style="font-size:13px;">' . $venta[0]->nro_autorizacion . '</span>
                    </div>
                </div>
                <div class="row"><div class="col-12 table-responsive"><table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Product</th>
                            <th>Cantidad</th>
                            <th>Valor</th>
                            <th>Descuento</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>';
            $subtotal = 0;
            $descuento = 0;
            $iva = 0;
            $subtotal_ = 0;
            $aPagar = 0;
            $detalle = DetalleVenta::where('id_ct_ventas', $venta[0]->id)->groupBy('porcentaje')->get([
                DB::raw('GROUP_CONCAT(porcentaje limit 1) as porcentaje'),
                DB::raw('GROUP_CONCAT(check_iva limit 1) as check_iva'),
                DB::raw('sum(extendido) as extendido')
            ]);
            /*if (Auth::user()->id == '0921605895') {
                echo '<pre>';
                print_r($detalle);
                exit;
            }*/
            //$tipoImpuestos = $detalle->groupBy('porcentaje');
            $impuestos = '';
            /*if (count($tipoImpuestos) > 0) {
                foreach ($tipoImpuestos as $reo) {
                    if ($reo[0]->check_iva == 1) {
                        $impuestos .= ' <tr><th>Iva (' . (($reo[0]->porcentaje) * 100) . '%)</th><td align="right">$ ' . number_format($reo[0]->precio * $reo[0]->porcentaje, 2) . '</td></tr>';
                    }
                }
            }*/
            if (count($detalle) > 0) {
                foreach ($detalle as $reo) {
                    if ($reo->check_iva == 1) {
                        $impuestos .= ' <tr><th>Iva (' . (($reo->porcentaje) * 100) . '%)</th><td align="right">$ ' . number_format($reo->extendido * (($reo->porcentaje)), 2) . '</td></tr>';
                    }
                }
            }
            $porcentajeIva = 0;
            if (count($venta) > 0) {
                foreach ($venta as $row) {
                    $total = ($row->cantidad * $row->precio) - $row->descuento;
                    $subtotal += $row->cantidad * $row->precio;
                    $descuento += $row->descuento;
                    $porcentajeIva = $row->check_iva != null ? $row->check_iva : 0;
                    if ($row->check_iva == 1) {
                        $iva += ($row->cantidad * $row->precio) * $row->porcentaje;
                    }
                    $subtotal_ += $total;
                    $contenidoHtml .= '<tr>
                <td>' . $row->codigo . '</td>
                <td>' . $row->nombre . '</td>
                <td align="right">' . $row->cantidad . '</td>
                <td align="right">' . $row->precio . '</td>
                <td align="right">' . $row->descuento . '</td>
                <td align="right">' . number_format($total, 4) . '</td>
                </tr>';
                }
            }
            $aPagar = $subtotal_ + $iva;
            $contenidoHtml .= '</tbody>
                </table></div></div>
                    <div class="row">
                        <div class="col-6">
                            <p class="lead">Métodos de pago:</p>';
            if ($seguro != '') {
                $contenidoHtml .= '<table style="width:100%;"><tr><td colspan="2">SEGURO: ' . $seguro . '</td></tr></table>';
            }
            $formPago = explode("|", $formPago);
            if (count($formPago) > 0)
                $contenidoHtml .= '<table style="width:70%;">';
            for ($i = 0; $i < count($formPago); $i++) {
                $datosPago = explode(":", $formPago[$i]);
                $contenidoHtml .= '<tr><td>' . $datosPago[0] . ': </td><td align="right">$' . $datosPago[1]  . '</td></tr>';
            }
            $contenidoHtml .= '</table>';
            $contenidoHtml .= '<table style="width:100%;"><tr><td colspan="2">' . nl2br($venta[0]->concepto) . '</td></tr></table>';
            $contenidoHtml .= '<!--<img src="' . url('v3/') . '/dist/img/credit/visa.png" alt="Visa">
                            <img src="' . url('v3/') . '/dist/img/credit/mastercard.png" alt="Mastercard">
                            <img src="' . url('v3/') . '/dist/img/credit/american-express.png" alt="American Express">
                            <img src="' . url('v3/') . '/dist/img/credit/paypal2.png" alt="Paypal">-->
                        </div>
                        <div class="col-6">
                            <p class="lead">Monto adeudado ' . $fechaMax . '</p>
                            <div class="table-responsive">
                                <table class="table">
                                    <tr>
                                        <th style="width:50%">Total:</th>
                                        <td align="right">$ ' . number_format($subtotal, 4) . '</td>
                                    </tr>
                                    <tr>
                                        <th>Descuento</th>
                                        <td align="right">$ ' . number_format($descuento, 4) . '</td>
                                    </tr>
                                    <tr>
                                    <th>Subtotal:</th>
                                    <td align="right">$ ' . number_format($subtotal_, 4) . '</td>
                                </tr>' . $impuestos . '<tr>
                                        <th>Total:</th>
                                        <td align="right">$ ' . number_format($aPagar, 4) . '</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row no-print">
                        <div class="col-12">
                            <a href="' . url('v3/') . '/ventas/printFactura?id=' . $venta[0]->id . '" rel="noopener" target="_blank" class="btn btn-default"><i class="fas fa-print"></i> <span class="botones">Imprimir</span></a>
                            <a class="btn btn-success float-right" href="' . url('/') . '/api_doc_electronico?opcion=descargarXML&clave=' . $venta[0]->nro_autorizacion . '" ><i class="far fa-file-code"></i>  <span class="botones">Generar XML</span></a>
                            <a class="btn btn-primary float-right" href="' . url('/') . '/api_doc_electronico?opcion=generarPdf&clave=' . $venta[0]->nro_autorizacion . '" style="margin-right: 5px;"><i class="fas fa-download"></i>  <span class="botones">Generar PDF</span></a>
                        </div>
                    </div>';
        } else {
            $contenidoHtml = 'Busqueda sin resultados';
        }
        return $contenidoHtml;
    }
    public static function getVentas($id = '')
    {
        if ($id == '') {
            return self::join('users as uc', 'ct_ventas.id_usuariocrea', 'uc.id')
                ->join('users as um', 'ct_ventas.id_usuariomod', 'um.id')
                ->leftjoin('seguros as s', 'ct_ventas.seguro_paciente', 's.id')
                ->where('ct_ventas.tipo', '!=', 'N-D')
                ->where('id_empresa', session('id_empresa'))
                ->whereYear('fecha', session('periodo'))
                ->where('ct_ventas.tipo', '!=', 'N-C')
                ->orderBy('fecha', 'DESC')
                ->get([
                    'ct_ventas.id',
                    'ct_ventas.fecha',
                    'ct_ventas.nro_comprobante',
                    'ct_ventas.nro_autorizacion',
                    'ct_ventas.nombre_cliente',
                    'ct_ventas.id_asiento',
                    'ct_ventas.doc_electronico',
                    'ct_ventas.tipo',
                    'ct_ventas.nombres_paciente',
                    'ct_ventas.procedimientos',
                    'ct_ventas.fecha_procedimiento',
                    'ct_ventas.estado',
                    DB::raw('CONCAT(SUBSTRING(uc.nombre1, 1, 1), ". ", uc.apellido1) AS usuariocrea'),
                    DB::raw('IF(ct_ventas.estado=0, CONCAT(SUBSTRING(um.nombre1, 1, 1), ". ", um.apellido1), "") AS usuariomod'),
                    's.nombre as seguro',
                    'ct_ventas.id_cliente',
                    'ct_ventas.concepto',
                    DB::raw('ROUND(ct_ventas.subtotal_12, 4) AS subtotal_12'),
                    DB::raw('ROUND(ct_ventas.subtotal_0, 4) AS subtotal_0'),
                    DB::raw('ROUND(ct_ventas.subtotal_12 + ct_ventas.subtotal_0, 4) AS subtotal_total'),
                    'ct_ventas.impuesto',
                    'ct_ventas.total_final'
                ]);
        } else {
            return self::where("id", $id)
                ->first([
                    DB::raw('IF(ct_ventas.iva_total IS NULL OR ct_ventas.iva_total = 0, ct_ventas.impuesto, ct_ventas.iva_total) AS impuesto'),
                    'ct_ventas.*'
                ]);
        }
    }
    public static function metodosDePagos()
    {
        return FormaDePago::where("estado", 1)->get();
    }
    public static function cuentaDePago()
    {
        return Plan_Cuentas::where('id', 'like', '1%')
            ->whereRaw("LENGTH(id) - LENGTH(REPLACE(id, '.', '')) = 4")
            ->get();
    }
    public static function buscarCliente()
    {
        return Clientes::where('estado', 1)->get();
    }
    public static function buscarClienteF($term)
    {
        $clientes = Clientes::where('estado', 1)->where("identificacion", "like", "%" . $term . "%")->orWhere(function ($query) use ($term) {
            $query->where('nombre', 'like', '%' . $term . '%');
        })->get();
        $client = [];
        foreach ($clientes as $value) {
            $client[] = ['id' => $value->identificacion, 'nombre' => $value->identificacion . ' | ' . $value->nombre];
        }

        return response()->json($client);
    }
    public static function comprobanteCombo($id)
    {
        $combo = '';
        $cuenta = self::where('id', $id)
            ->join('ct_clientes', 'ct_ventas.id_cliente', '=', 'ct_clientes.identificacion')
            ->where('id_empresa', session('id_empresa'))->first();
        if ($cuenta != '') {
            $combo = '<option value="' . $cuenta->id . '" selected> Fact#: ' . $cuenta->nro_comprobante . ' | ' . $cuenta->nombre . ' | Id: ' . $cuenta->id . '</option>';
        }
        return $combo;
    }
    public static function buscarFacturas($datos)
    {
        $fecha = $datos["fecha_desde"];
        $fecha_hasta  = $datos["fecha_hasta"];
        $id_empresa = session("id_empresa");
        $ordenes = "";
        $secure = $datos["seguro_f"];
        if ($datos["tiposervicio_f"] == 2) {
            $ordenes = VenOrden::where('estado', '1')
                ->where('id_empresa', $id_empresa)
                ->where('estado_pago', '<>', '1')
                ->where('tipo_consulta', '2');
            if ($secure != 0) {
                $ordenes = $ordenes->where('seguro_paciente', $secure);
            }
            $ordenes = $ordenes
                ->whereBetween('fecha_procedimiento', [str_replace('/', '-', $fecha) . ' 00:00:00', str_replace('/', '-', $fecha_hasta) . ' 23:59:59'])
                ->orderBy('fecha_procedimiento', 'desc')->get();
        } else if ($datos["tiposervicio_f"] == 1) {
            $ordenes = VenOrden::where('estado', '1')->where('id_empresa', $id_empresa)
                ->where('estado_pago', '<>', '1')
                ->where('tipo_consulta', '1');
            if ($secure != 0) {
                $ordenes = $ordenes->where('seguro_paciente', $secure);
            }
            $ordenes = $ordenes->whereBetween('fecha_procedimiento', [str_replace('/', '-', $fecha) . ' 00:00:00', str_replace('/', '-', $fecha_hasta) . ' 23:59:59'])
                ->orderBy('fecha_procedimiento', 'desc')->get();
        } else if ($datos["tiposervicio_f"] == 3) {
            $ordenes = VenOrden::where('estado', '1')->where('id_empresa', $id_empresa)->where('estado_pago', '<>', '1');

            if (!is_null($secure)) {
                $ordenes = $ordenes->where('seguro_paciente', $secure);
            }


            $ordenes = $ordenes->whereBetween('fecha_procedimiento', [str_replace('/', '-', $fecha) . ' 00:00:00', str_replace('/', '-', $fecha_hasta) . ' 23:59:59'])->orderBy('fecha_procedimiento', 'desc')->get();
        }
        $html = "<div>";
        if (!empty($ordenes)) {
            $contador = 0;
            foreach ($ordenes as $x) {
                if (!is_null($x)) {
                    $html .= '<div class="panel panel-default">
                        <div class="panel-heading">
                            <input type="checkbox" name="activo[]" value="1" class="relactivo" />
                            <input type="hidden" name="veractivo[]" class="veractivo" value="0">
                            <input type="hidden" class="pt" value="' . $x->id_paciente . '">
                            <input type="hidden" class="patients" value="' . $x->id . '">
                            ' . '<b>' . $x->nombres_paciente . '</b>' . ' - ' . '<b>' . substr($x->fecha_procedimiento, 0, 10) . '</b>' . '<b> #recibo : ' . (isset($x->orden_venta) ? $x->orden_venta : '') . '</b>' . '
                            <div class="panel-body" style="padding:0;">
                                <div class="col-md-12 table-responsive " style="padding:0 !important;">
                                    <table class="table table-bordered table-hover dataTable noacti" role="grid" aria-describedby="example2_info" style="margin-top:0 !important;">
                                        <thead>
                                            <tr class="well-dark">
                                                <th width="35%" tabindex="0">Descripción del Producto</th>
                                                <th width="10%" tabindex="0">Cantidad</th>
                                                <th width="10%" tabindex="0">Precio</th>
                                                <th width="10%" tabindex="0">Cobrar Seguro</th>
                                                <th width="10%" tabindex="0">% Desc</th>
                                                <th width="10%" tabindex="0">Descuento</th>
                                                <th width="10%" tabindex="0">Precio Neto</th>
                                                <th width="5%" tabindex="0">IVA</th>
                                                <th width="10%" tabindex="0">
                                                    <button type="button" class="btn btn-warning btn-gray anadir" onclick="nuevo(\'' . $contador . '\',\'' . $x->id . '\',\'' . $x->id_paciente . '\')"> <i class="fa fa-plus"></i> </button>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody id="entrega' . $contador . '">';
                    foreach ($x->detalles as $z) {
                        $precio = $z->copago;
                        if ($precio == 0) {
                            if ($z->precio > 0)
                                $precio = $z->precio;
                            else
                                $precio = 0.00;
                        }
                        $precioFinal = $precio * $z["cantidad"];
                        if ($z->extendido > 0) {
                            $validate = \Sis_medico\ParametersConglomerada::dontSee($z->id_ct_productos);
                            $productofind = \Sis_medico\Ct_productos::where('codigo', $z->id_ct_productos)->first();
                            if (!$validate) {
                                $html .= '<tr>
                                    <td style="max-width:100px;">
                                        <input type="hidden" class="codigo_producto" />
                                        <select class="form-control select2 productos" required onchange="verificar(this); verificar_stock(this);">
                                            <option> </option>';
                                if ($productofind != null) {
                                    $html .= '<option selected="selected" value="' . $z->id_ct_productos . '">' . $productofind->nombre . '</option>';
                                } else {
                                    $html .= '<option value="' . $z->id_ct_productos . '"> ' . $z->id_ct_productos . '</option>';
                                }
                                $html .= '</select>
                                <input type="hidden" class="id_patient" value="' . $x->id_paciente . '">
                                <input type="hidden" name="verid[]" class="verid" value="1">
                                <textarea wrap="hard" rows="3" class="form-control px-1 desc_producto mt-1" placeholder="Detalle del producto">' . $z->detalle . '</textarea>
                                <input type="hidden" name="iva[]" class="iva" />
                                </td>
                                <td style="text-align:center;">
                                    <input class="form-control text-right cneto" type="text" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="' . (!is_null($z->cantidad) ? '1' : '') . '" required onchange="verificar_stock(this);">
                                    <br/>
                                </td>
                                <td  class="d-flex mb-4">
                                        <input type="text" class="form-control pneto form-outline me-3" style="width: 8rem;text-align:right" onkeypress="return isNumberKey(event)" value=' . $precio . '>  
                                </td>
                                <td>
                                    <input class="form-control text-right copago" type="text" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="0.00" readonly>
                                </td>
                                <td>
                                    <input class="form-control text-right pdesc" type="text"  onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" required>
                                    <input class="form-control text-right maxdesc" type="hidden"  onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" required>
                                </td>
                                <td>
                                    <input class="form-control text-right desc" type="text"  onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="0" required>
                                </td>
                                <td>
                                    <input class="form-control px-1 text-right"  type="text"  onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value=' . $precioFinal . ' required>
                                </td>
                                <td>
                                    <input class="form luffy" type="checkbox"';
                                if ($z->check_iva > 0) {
                                    $html .= 'selected="selected"';
                                }
                                $html .= 'name="valoriva[]"';
                                $html .= 'value="';
                                if ($z->check_iva != null) {
                                    $z->check_iva;
                                }
                                $html .= '">';
                                $html .= '</td>';
                                $html .= '<td>
                                    <button type="button" class="btn btn-danger btn-gray delete">
                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                    </button>
                                </td>';
                                $html .= '</tr>';
                            }
                        }
                    }
                    $html .= '</tbody>
                    </table>';
                    $html .= '</div>
                        </div>
                    </div>';
                    $contador++;
                }
            }
        } else {
            $html .= 'No hay datos.';
        }
        $html .= '</div>';
        return $html;
    }
    public static function obtener_numero_factura($idempresa, $sucursal, $punto_emision)
    {
        $contador_ctv = self::where('id_empresa', $idempresa)->where('sucursal', $sucursal)->where('punto_emision', $punto_emision)->get()->count();
        $numero_factura = 0;
        if ($contador_ctv == 0) {
            $num = '1';
            $numero_factura = str_pad($num, 9, "0", STR_PAD_LEFT);
        } else {
            $max_id = self::where('id_empresa', $idempresa)
                ->where('sucursal', $sucursal)
                ->where('punto_emision', $punto_emision)
                ->where('estado', '!=', 0)
                ->latest()
                ->first();
            $max_id = intval($max_id->numero);
            if (strlen($max_id) < 9) {
                $nu = $max_id + 1;
                $numero_factura = str_pad($nu, 9, "0", STR_PAD_LEFT);
            }
        }
        return $numero_factura;
    }
    public static function crearComprobante($nfactura, $data, $array_pagos, $factura_id, $id_asiento_cabecera, $c_sucursal, $c_caja)
    {
        try {
            $id_empresa     = session('id_empresa');
            $ip_cliente     = $_SERVER["REMOTE_ADDR"];
            $idusuario      = Auth::user()->id;
            $arrayAsientoCabecera = [];
            $acumulador = 0;
            foreach ($data['id_pago'] as $key => $row) {
                if ($row != 7) {
                    $acumulador +=  $data["valor"][$key];
                }
            }

            $arrayAsientoCabecera = [
                'sucursal' => $c_sucursal,
                'punto_emision' => $c_caja,
                'fecha_asiento' => $data["fecha"],
                'fact_numero' => $nfactura,
                'id_empresa' => session('id_empresa'),
                'observacion' => 'COMPROBANTE DE INGRESO FACT:' . $nfactura,
                'valor' => $acumulador,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ];

            Contabilidad::crearAsiento('ventas', 'comprobante_ingreso', $arrayAsientoCabecera,  $data);
            $datosCliente = explode("|", $data["identi"]);
            $input_comprobante = [
                'observaciones'       => 'COMPROBANTE DE INGRESO FACT: ' . $nfactura,
                'estado'              => '1',
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'fecha'               => $data['fecha'],
                'secuencia'           => $nfactura,
                'divisas'             => '1',
                'id_empresa'          => $id_empresa,
                'total_ingreso'       => $data["total1"],
                'id_cliente'          => $datosCliente[0],
                'autollenar'          => "Cancela FV : " . $nfactura,
                'id_vendedor'         => isset($data['cedula_vendedor']) ? $data['cedula_vendedor'] : null,
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
            ];

            $id_comprobante = ComprobanteIngreso::insertGetId($input_comprobante);
            //formas de pago detalle

            foreach ($array_pagos as $valor) {


                $val = is_null($valor['valor']) ? 0 : $valor["valor"];
                $fecha_pago = $valor['fecha_pago'] != "" ? $valor['fecha_pago'] : $data['fecha_asiento'];

                $arraydetallepago = [
                    'id_comprobante'  => $id_comprobante,
                    'fecha'           => $fecha_pago,
                    'numero'          => $valor['numero_pago'],
                    'id_banco'        => $valor['id_banco_pago'],
                    'id_tipo_tarjeta' => $valor['tipo_tarjeta'],
                    'id_tipo'         => $valor['id_tip_pago'],
                    'total'           => $val,
                    'cuenta'          => $valor['id_cuenta_pago'],
                    'girador'         => $valor['giradoa'],
                    'estado'          => '1',
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                ];

                DetallePagoIngreso::insert($arraydetallepago);
            }
            $input_comprobante = null;

            if (floatval($data["total1"]) > 0) {
                DetalleComprobanteIngreso::insert([
                    'id_comprobante'    => $id_comprobante,
                    'fecha'             => $data['fecha'],
                    'observaciones'     => "Cancela FV : " . $nfactura,
                    'id_factura'        => $factura_id,
                    'secuencia_factura' => $nfactura,
                    'total_factura'     => $data['total1'],
                    'total'             => $data["total1"],
                    'estado'            => '1',
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                ]);
            }
            $valor_actual = self::where('id', $factura_id)->first();
            $id__ventas   = [
                'valor_contable' => floatVal($valor_actual->valor_contable) - $data["total1"],
                'estado_pago'    => 2,
            ];
            self::where('id', $factura_id)->update($id__ventas);
            return [
                'status' => true,
            ];
        } catch (Exception $r) {

            return [
                'status' => false,
                'msj' => $r->getMessage(),
                'line' => $r->getLine()
            ];
        }
    }
    public static function saveConglomerada($r)
    {
        DB::enableQueryLog();
        DB::beginTransaction();
        $cont = 0;
        try {
            $data = $r->input();
            $origin = $r->input();
            $camposMonetarios = [
                'total1', 'totalc', 'subtotal_01', 'subtotal_121',
                'descuento1', 'base1', 'tarifa_iva1', 'valor_totalPagos'
            ];
            foreach ($camposMonetarios as $campo) {
                if (isset($data[$campo]) && is_numeric($data[$campo])) {
                    $data[$campo] = round((float) $data[$campo], 2, PHP_ROUND_HALF_UP);
                }
            }
            foreach (['copago', 'desc', 'precioneto', 'valor', 'valor_base'] as $campo) {
                if (isset($data[$campo]) && is_array($data[$campo])) {
                    $data[$campo] = array_map(function ($valor) {
                        return is_numeric($valor)
                            ? round((float) $valor, 2, PHP_ROUND_HALF_UP)
                            : $valor;
                    }, $data[$campo]);
                }
            }
            $ivaConfigurado = \Sis_medico\Utilidades::porcentajeIva();
            $data = self::calcularTotalesConglomerada($data, $ivaConfigurado ? $ivaConfigurado->iva : 0);
            $r->merge($data);
            unset($data['d']);
            unset($data['_token']);
            $idusuario =  Auth::user()->id;
            $ip = Utilidades::getRealIP();
            if ($data['id'] == '') {
                unset($data['id']);
                DB::enableQueryLog();
                $empresa = Empresas::find(session('id_empresa'));
                $cod_sucurs = PuntoEmision::where('estado', '1')->where('id', $data["sucursal_empre"])->first();
                $cod_caj = Ct_Caja::where('estado', '1')->where('id', $data["punto_emision"])->first();
                $array = [
                    'sucursal' => $cod_sucurs->codigo_sucursal,
                    'punto_emision' => $cod_caj->codigo_caja,
                    'tipo' => 1
                ];
                $num_comprobante = self::generarNumeroDocumento($array);
                $arrayAsientoCabecera = [
                    'sucursal' => $cod_sucurs->codigo_sucursal,
                    'punto_emision' => $cod_caj->codigo_caja,
                    'fecha_asiento' => $data["fecha"],
                    'fact_numero' => $num_comprobante,
                    'id_empresa' => session('id_empresa'),
                    'observacion' => $data["concepto"],
                    'valor' => $data["total1"],
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                    'ip_creacion' => $ip,
                    'ip_modificacion' => $ip,
                ];
                $numComprobante = explode('-', $num_comprobante);
                $claveObj = new ClaveAcceso();
                $deEmpresa = DeEmpresas::where('id_empresa', session('id_empresa'))->first();
                $campos = [
                    'fecha_emision' => str_replace('/', '-', date('d/m/Y', strtotime($r->fecha_emision))),
                    'tipo_comprobante' => '01',
                    'ruc' => session('id_empresa'),
                    'tipo_ambiente' => $deEmpresa->ambiente,
                    'punto_establecimiento' => $numComprobante[0],
                    'punto_emision' => $numComprobante[1],
                    'numero_comprobante' => $numComprobante[2],
                    'codigo_numerico' => $numComprobante[2],
                    'tipo_emision' => 1
                ];
                $claveObj->llenarDatos($campos);
                if ($empresa->inventario == 1) {
                    $msjk = "Store: tiene inventario";
                    throw new Exception($msjk);
                }
                if (isset($r['descuento1'])) {

                    $r['descuentototal'] = $r['descuento1'];
                }
                $id_asiento_cabecera = Contabilidad::crearAsiento('ventas', 'ingreso', $arrayAsientoCabecera,  $r, "conglomerada");
                $datosCliente = explode("|", $data["identi"]);
                $clienteD = Clientes::where("identificacion", $datosCliente[0])->first();
                $factura_venta = [
                    'sucursal' => $cod_sucurs->codigo_sucursal,
                    'punto_emision' => $cod_caj->codigo_caja,
                    'numero'              => $numComprobante[2],
                    'nro_comprobante'     => $num_comprobante,
                    'id_asiento'          => $id_asiento_cabecera,
                    'id_empresa'          => session("id_empresa"),
                    'tipo'                => 'VENFA-CO',
                    'ambulatorio'         => isset($data['amBu']) ? $data["amBu"] : '',
                    'fecha'               => $data['fecha'],
                    'divisas'             => $data['divisas'],
                    'nombre_cliente'      => $data['nombre_cliente'],
                    'tipo_consulta'       => isset($data['tipo_consulta']) ? $data["tipo_consulta"] : null,
                    'id_cliente'          => $clienteD->identificacion,
                    'direccion_cliente'   => $clienteD->direccion_representante,
                    'ruc_id_cliente'      => $clienteD->identificacion,
                    'telefono_cliente'    => $clienteD->telefono1_representante,
                    'email_cliente'       => $clienteD->email_representante,
                    'orden_venta'         => $data['orden_venta'],
                    'nro_autorizacion'    => $claveObj->clave_acceso,
                    'id_paciente'         => isset($data['identificacion_paciente']) ? $data['identificacion_paciente'] : null,
                    'nombres_paciente'    => isset($data['nombre_paciente']) ? $data["nombre_paciente"] : null,
                    'id_hc_procedimiento' => isset($data['mov_paciente']) ? $data["mov_paciente"] : null,
                    'concepto'            => $data['concepto'],
                    'seguro_paciente'     => $data['seguro_f'],
                    'electronica'         =>  $cod_caj->codigo_caja == 100 ? 1 : $data['electronica'],
                    'procedimientos'      => isset($data['procedimiento']) ? $data["procedimiento"] : null,
                    'fecha_procedimiento' => isset($data['fecha_proced']) ? $data["fecha_proced"] : '',
                    'copago'              => $data['totalc'],
                    'id_recaudador'       => isset($data['cedula_recaudador']) ? $data["cedula_recaudador"] : null,
                    'subtotal_0'          => $data['subtotal_01'],
                    'subtotal_12'         => $data['subtotal_121'],
                    'descuento'           => $data['descuento1'],
                    'base_imponible'      => $data['base_iva'],
                    'impuesto'            => $data['tarifa_iva1'],
                    'total_final'         => $data['total1'],
                    'valor_contable'      => $data['total1'],
                    'ip_creacion'         => "conglomerada",
                    'ip_modificacion'     => $ip,
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'nota_electronica' => $data['concepto'],
                    'doc_electronico' => 0,
                    'emisor' => 'MD',
                ];
                $id_venta = self::insertGetId($factura_venta);
                $arr_total2  = [];
                for ($i = 0; $i < count($data["codigo"]); $i++) {
                    $upd = [
                        'estado_pago'     => '1',
                        'id_referencia'   => $id_venta,
                        'ip_modificacion' => $ip,
                        'id_usuariomod'   => $idusuario,
                    ];
                    $nombre = "";
                    if (isset($data["nombre_producto"][$i])) {
                        $nombre = $data["nombre_producto"][$i];
                    }
                    $codigo = "";
                    if (isset($data["codigo"][$i])) {
                        $codigo = $data["codigo"][$i];
                    }
                    $cantidad = 0;
                    if (isset($data["cantidad"][$i])) {
                        $cantidad = $data["cantidad"][$i];
                    }
                    $paciente = "";
                    if (isset($data["paciente"][$i])) {
                        $paciente = $data["paciente"][$i];
                    }
                    $precio = 0;
                    if (isset($data["precio"][$i])) {
                        $precio = $data["precio"][$i];
                    }
                    $descuentopor = 0;
                    if (isset($data["descpor"][$i])) {
                        $descuentopor = $data["descpor"][$i];
                    }
                    $descuento = 0;
                    if (isset($data["descuento"][$i])) {
                        $descuento = $data["descuento"][$i];
                    } elseif (isset($data["desc"][$i])) {
                        $descuento = $data["desc"][$i];
                    }
                    $copago = 0;
                    if (isset($data["copago"][$i])) {
                        $copago = $data["copago"][$i];
                    }
                    $precio_neto = 0;
                    if (isset($data["precioneto"][$i])) {
                        $precio_neto = $data["precioneto"][$i];
                    }
                    $detalle = "";
                    $detalle = $data["descrip_prod"][$i];

                    $iva = 0;
                    if (isset($data["iva"][$i])) {
                        $iva = $data["iva"][$i];
                    }
                    $id_orden = 1;
                    if (isset($data["id_orden"][$i])) {
                        $id_orden = $data["id_orden"][$i];
                        $ventax = VenOrden::where('id', $data["id_orden"][$i])->first();
                        if ($ventax != '') {
                            VenOrden::where('id', $data["id_orden"][$i])->update($upd);
                        }
                    }
                    $bodega_in = "";
                    if (isset($data["bodega"][$i])) {
                        $bodega_in = $data["bodega"][$i];
                    }
                    $arr = [
                        'nombre'      => $nombre,
                        'cantidad'    => $cantidad,
                        'codigo'      => $codigo,
                        'id_paciente' => $paciente,
                        'precio'      => $precio,
                        'id_orden'    => $id_orden,
                        'descpor'     => $descuentopor,
                        'copago'      => $copago,
                        'descuento'   => $descuento,
                        'precioneto'  => $precio_neto,
                        'detalle'     => $detalle,
                        'iva'         => $iva,
                        'id_hc_proc'  => isset($data['id_hc_proc'][$i]) ? $data['id_hc_proc'][$i] : null,
                        'obs_pac'     => $data["concepto"],
                        'bodega'      => $bodega_in,
                    ];
                    array_push($arr_total2, $arr);
                }
                $detalleConglomerada = [];
                foreach ($arr_total2 as $valor) {
                    $paciente = "";
                    if (isset($valor['paciente'])) {
                        $paciente = $valor['paciente'];
                    }
                    $precio_neto = "";
                    if (isset($valor['precioneto'])) {
                        $precio_neto = $valor['precioneto'];
                    }
                    $nombre = "";
                    if (isset($valor['nombre'])) {
                        $nombre = $valor['nombre'];
                    }
                    $cantidad = 0;
                    if (isset($valor['cantidad'])) {
                        $cantidad = $valor['cantidad'];
                    }
                    $codigo = "";
                    if (isset($valor['codigo'])) {
                        $codigo = $valor['codigo'];
                    }
                    $precio = 0;
                    if (isset($valor['precio'])) {
                        $precio = $valor['precio'];
                    }
                    $paciente = "";
                    if (isset($valor['paciente'])) {
                        $paciente = $valor['paciente'];
                    }
                    $iva = 0;
                    if (isset($valor['iva'])) {
                        $iva = $valor['iva'];
                    }
                    $descuento = 0;
                    if (isset($valor['descuento'])) {
                        $descuento = $valor['descuento'];
                    }
                    $descrip_prod = "";
                    if (isset($valor['detalle'])) {
                        $descrip_prod = $valor['detalle'];
                    }
                    $id_orden = 1;
                    if (isset($valor['id_orden'])) {
                        $id_orden = $valor['id_orden'];
                    }
                    $bodega_in = "";
                    if (isset($valor['bodega'])) {
                        $bodega_in = $valor['bodega'];
                    }
                    $copago = round((float) ($valor['copago'] ?? 0), 2, PHP_ROUND_HALF_UP);
                    $precio_neto = round((floatval($cantidad) * floatval($precio)) - $descuento - $copago, 2, PHP_ROUND_HALF_UP);
                    $desPro = Productos::where('codigo', $codigo)->first();
                    $detalle     = [
                        'id_ct_ventas'         => $id_venta,
                        'id_ct_productos'      => $codigo,
                        'nombre'               => $codigo,
                        'descripcion_producto' => $desPro != '' ? $desPro->nombre : $codigo,
                        'cantidad'             => $cantidad,
                        'precio'               => $precio,
                        'descuento_porcentaje' => $descuento,
                        'descuento'            => $descuento,
                        'extendido'            => $precio_neto,
                        'detalle'              => $descrip_prod,
                        'copago'               => $copago,
                        'check_iva'            => $iva,
                        'codigo'               => $codigo,
                        'ip_creacion'          => $ip,
                        'ip_modificacion'      => $ip,
                        'id_usuariocrea'       => $idusuario,
                        'id_usuariomod'        => $idusuario,
                        'bodega'               => $bodega_in,
                    ];
                    DetalleVenta::insert($detalle);
                    $detalles = [
                        'id_ct_ventas'         => $id_venta,
                        'id_ct_productos'      => $codigo,
                        'nombre'               => $codigo,
                        'cantidad'             => $cantidad,
                        'precio'               => $precio,
                        'descuento_porcentaje' => $descuento,
                        'descuento'            => $descuento,
                        'extendido'            => $precio_neto,
                        'id_paciente'          => $valor['id_paciente'],
                        'detalle'              => $descrip_prod,
                        'copago'               => $copago,
                        'check_iva'            => $iva,
                        'id_orden'             => $valor['id_orden'],
                        'codigo'               => $codigo,
                        'ip_creacion'          => $ip,
                        'ip_modificacion'      => $ip,
                        'id_usuariocrea'       => $idusuario,
                        'id_usuariomod'        => $idusuario,
                    ];
                    array_push($detalleConglomerada, $detalles);
                    DetalleVentaConglomerada::insert($detalles);
                }
                $data['id']   = $id_venta;
                $data['tipo'] = 'VENFA-CO';
                $arr_p = [];
                for ($i = 0; $i < $data['contador_pago']; $i++) {
                    $arr_pagos = [
                        'id_tipo'    => isset($data['id_pago'][$i]) ? $data['id_pago'][$i] : null,
                        'fecha'     => isset($data['fecha_pago'][$i]) ? $data['fecha_pago'][$i] : null,
                        'tipo_tarjeta'   => isset($data['id_tarjetas'][$i]) ? $data['id_tarjetas'][$i] : '',
                        'numero_transaccion'    => isset($data['numero'][$i]) ? $data['numero'][$i] : '',
                        'id_banco'  => isset($data['id_banco_pago'][$i]) ? $data['id_banco_pago'][$i] : '',
                        'cuenta' => isset($data['id_cuenta_pago'][$i]) ? $data['id_cuenta_pago'][$i] : '',
                        'giradoa'        => isset($data['giradoa'][$i]) ? $data['giradoa'][$i] : '',
                        'valor'          => isset($data['valor'][$i]) ? $data['valor'][$i] : '',
                        'valor_base'     => isset($data['valor_base'][$i]) ? $data['valor_base'][$i] : '',
                        'info_banco' => Bancos::find($data['id_bancos'][$i])
                    ];
                    array_push($arr_p, $arr_pagos);
                }
                // Generar Kardex
                $msj = Kardex::generar_kardex($data);
                if ($msj !== 'Mensaje: registro ingresado al kardex') {
                    throw new Exception($msj);
                }
                $arr_p = [];
                for ($i = 0; $i < $data['contador_pago']; $i++) {
                    $arr_pagos = [
                        'id_tip_pago'    => $data['id_pago'][$i],
                        'fecha_pago'     => $data['fecha_pago'][$i],
                        'tipo_tarjeta'   => $data['id_tarjetas'][$i],
                        'numero_pago'    => $data['numero'][$i],
                        'id_banco_pago'  => $data['id_bancos'][$i],
                        'id_cuenta_pago' => $data['cuenta'][$i],
                        'giradoa'        => $data['giradoA'][$i],
                        'valor'          => $data['valor'][$i],
                    ];
                    array_push($arr_p, $arr_pagos);
                }
                foreach ($arr_p as $valor) {
                    FormaPago::insert([
                        'id_ct_ventas'    => $id_venta,
                        'tipo'            => $valor['id_tip_pago'],
                        'fecha'           => $valor['fecha_pago'],
                        'numero'          => $valor['numero_pago'],
                        'banco'           => $valor['id_banco_pago'],
                        'cuenta'          => $valor['id_cuenta_pago'],
                        'giradoa'         => $valor['giradoa'],
                        'valor'           => $valor['valor'],
                        'id_usuariocrea'  => $idusuario,
                        'id_usuariomod'   => $idusuario,
                        'ip_creacion'     => $ip,
                        'ip_modificacion' => $ip,
                    ]);
                }
                //agregar comprobantes de ingreso  
                $resultado = self::crearComprobante($num_comprobante, $r, $arr_p, $id_venta, $id_asiento_cabecera, $cod_sucurs->codigo_sucursal, $cod_caj->codigo_caja);
                if (!$resultado["status"]) {
                    throw new Exception($resultado["msj"]);
                }
                $r->c = 'FacturaConglomerada';
                $r->s = 'saveConglomerada';
                $r->d = $origin['d'];
                $r->m = self::$modelo;
                $r->o = 'Se agrego una nueva factura conglomerda con id.: ';
                $cont++;
            }
            Auditorias::saveAuditoria($r, DB::getQueryLog());
            DB::disableQueryLog();
        } catch (Exception $e) {
            DB::rollBack();
            $arrayRespuesta = [
                'status' => 'error',
                'code' => 500,
                'message' => $e->getMessage(),
                'linea' => $e->getLine(),
                'file' => $e->getFile(),
                'data' => $r->input(),
            ];
            $r->c = 'FacturaConglomerada';
            $r->s = 'saveConglomerada';
            $r->d =  $origin['d'];
            $r->m = self::$modelo;
            $r->o = 'Ocurrio un error: ' . $e->getMessage();
            Auditorias::saveAuditoria($r, DB::getQueryLog());
            DB::disableQueryLog();
            return json_encode($arrayRespuesta);
        }
        if ($cont > 0) {
            DB::commit();
            $arrayRespuesta = [
                'status' => 'success',
                'code' => 200,
                'message' => 'Registro guardado correctamente',
                'data' => $r->input(),
            ];
            return json_encode($arrayRespuesta);
        }
    }
    public static function getOnlyRow($id)
    {
        $ventas = DB::table('ct_ventas as ct_v')
            ->leftjoin('ct_clientes as ct_c', 'ct_c.identificacion', 'ct_v.id_cliente')
            ->where('ct_v.id', $id)
            ->select('ct_v.*')->first();
        $detalle_venta = DB::table('ct_detalle_venta as ct_det_vent')
            ->where('ct_det_vent.id_ct_ventas', $id)
            ->where('ct_det_vent.estado', '1')
            ->select('ct_det_vent.*')
            ->get();
        $detalle_venfaco = DB::table('ct_detalle_venta as ct_detalle')
            ->where('ct_detalle.id_ct_ventas', $id)
            ->where('ct_detalle.estado', '1')
            ->groupBy('ct_detalle.id_ct_productos')
            ->select('id_ct_productos', 'nombre')
            ->select(DB::raw('id_ct_productos, nombre, SUM(precio) as precio, SUM(cantidad) as cantidad', 'SUM(descuento) as descuento'))
            ->get();

        $forma_pago = FormaPago::where('id_ct_ventas', $id)->where('estado', '1')->get();

        $array = [
            'ventas' => $ventas,
            'detalle_venta' => $detalle_venta,
            'detalle_venfaco' => $detalle_venfaco,
            'forma_pago' => $forma_pago
        ];
        return $array;
    }
    public static function getFacturasConglomerada($r)
    {
        $desde = $r["desde"] == null ? date("Y-m-d") : $r["desde"];
        $hasta = $r["hasta"] == null ? date("Y-m-d") : $r["hasta"];

        return  self::where('ct_ventas.estado', '<', 2)
            ->leftjoin("ct_clientes", "ct_ventas.id_cliente", "ct_clientes.identificacion")
            ->leftjoin("seguros", "ct_ventas.seguro_paciente", "seguros.id")
            ->leftjoin("users", "ct_ventas.id_usuariocrea", "users.id")
            ->leftjoin("users as uss", "ct_ventas.id_usuariomod", "uss.id")
            ->where('ct_ventas.id_empresa', session("id_empresa"))
            ->where('ct_ventas.tipo', "VENFA-CO")
            ->whereBetween("ct_ventas.created_at", [$desde . ' 00:00:00', $hasta . ' 23:59:59'])
            ->orderby('ct_ventas.id', 'desc')
            ->get([
                "ct_clientes.nombre as nombreCliente",
                "seguros.nombre as nombreSeguro",
                "ct_ventas.*",
                DB::raw("CONCAT(users.nombre1, ' ', users.nombre2) as nombreCrea"),
                DB::raw("CONCAT(uss.nombre1, ' ', uss.nombre2) as nombreAnula")
            ]);
    }
    public static function recovery_by_model($id)
    {

        $ventas = self::find($id);
        $arraymodules = [];
        $arraymodules['id'] = $ventas->id;
        $arraymodules['comprobante'] = $ventas->nro_comprobante;
        $arraymodules['cliente'] = $ventas->cliente->nombre;
        $arraymodules['asiento'] = $ventas->id_asiento;
        $arraymodules['total_final'] = $ventas->total_final;
        $arraymodules['valor_contable'] = $ventas->valor_contable;
        $arraymodules['usuariocrea'] = $ventas->usuario->nombre1 . ' ' . $ventas->usuario->apellido1;
        $arraymodules['creado'] = $ventas->created_at;
        $arraymodules['actualizado'] = $ventas->updated_at;
        if ($ventas->omni == 1) {
            $arraymodules['informacion'] = "OMNI";
        }
        if (isset($ventas->retenciones)) {
            $rete = ClientesRetenciones::where('id_factura', $id)->get();
            $contador = 0;
            foreach ($rete as $value) {
                if (($value->estado == 1)) {
                    $arraymodules['retencion' . $contador] = $value->id;
                } else {
                    $arraymodules['retencionAnulado' . $contador] = $value->id;
                }
                $contador++;
            }
        }
        $ing = [];
        if (isset($ventas->comp_ingreso)) {
            if (count($ventas->comp_ingreso) > 0) {
                foreach ($ventas->comp_ingreso as $ig) {
                    if (isset($ig->ingreso)) {
                        if ($ig->ingreso->estado == 1) {
                            array_push($ing, $ig->ingreso->id);
                        }
                    }
                }
                $arraymodules['ingreso'] = $ing;
            }
        }
        $cruce = [];
        if (isset($ventas->cruce)) {
            if (count($ventas->cruce) > 0) {
                foreach ($ventas->cruce as $x) {
                    if (isset($x->cabecera)) {
                        if ($x->cabecera->estado == 1) {
                            array_push($cruce, $x->cabecera->id);
                        }
                    }
                }
                $arraymodules['cruce'] = $cruce;
            }
        }
        $cheque = [];
        if (isset($ventas->chequepost)) {
            if (count($ventas->chequepost) > 0) {
                foreach ($ventas->chequepost as $ch) {
                    if (isset($ch->cabecera)) {
                        if ($ch->cabecera->estado == 1) {
                            array_push($cheque, $ch->cabecera->id);
                        }
                    }
                }
                $arraymodules['chequepost'] = $cheque;
            }
        }
        $xp = [];
        if (isset($ventas->cruce_cuentas)) {
            if (count($ventas->cruce_cuentas) > 0) {
                foreach ($ventas->cruce_cuentas as $x) {
                    if ($x->estado != 0)
                        array_push($xp, $x->id);
                }
                $arraymodules['cruce_cuentas'] = $xp;
            }
        }
        $credi = [];
        if (isset($ventas->credito)) {
            if (count($ventas->credito) > 0) {
                foreach ($ventas->credito as $c) {
                    if (isset($c->cabecera)) {
                        if ($c->cabecera->estado == 1) {
                            array_push($cheque, $c->cabecera->id);
                        }
                    }
                }
                $arraymodules['credito'] = $credi;
            }
        }
        return response()->json($arraymodules);
    }
    public static function verificar($r)
    {
        try {
            $information = self::recovery_by_model($r['id']);
            return $information;
        } catch (Exception $e) {
            return [
                'status' => false,
                'msj' => 'Ocurrio un error',
                'consola' => $e->getMessage()
            ];
        }
    }
    public static function anularVentas($r)
    {
        DB::beginTransaction();
        try {
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $idusuario = Auth::user()->id;
            $concepto = $r['concepto'];
            $estado_ventas = self::where('id', $r['id'])->where('estado', '<>', 0)->first();
            if (!empty($estado_ventas)) {
                $act_estado = [
                    'estado'        => '0',
                    'id_usuariomod' => $idusuario,
                ];
                $registro_venta = self::find($r["id"]);
                $data['id']     = $r["id"];
                $data['tipo']   = 'VEN-FA';
                Kardex::anular_kardex($data);
                self::where('id', $r["id"])->update($act_estado);
                $ordenes = VenOrden::where('id_referencia', $r["id"])->get();
                if (!is_null($ordenes)) {
                    foreach ($ordenes as $v) {
                        $vs = VenOrden::find($v->id);
                        $vs->estado_pago = 0;
                        $vs->id_usuariomod = $idusuario;
                        $vs->save();
                    }
                }
                $consulta_cabecera_ctventas = AsientosCabecera::where('estado', '1')->where('id', $registro_venta->id_asiento)->first();
                $text = 'Fact #' . ':' . $registro_venta->nro_comprobante . '-' . $registro_venta->procedimientos . '-' . 'ANULACION FACTURA';
                $input_cabecera = [
                    'fecha_asiento'   => $consulta_cabecera_ctventas->fecha_asiento,
                    'fact_numero'     => $consulta_cabecera_ctventas->fact_numero,
                    'id_empresa'      => $registro_venta->id_empresa,
                    'observacion'     => $concepto . " " . $text,
                    'valor'           => $consulta_cabecera_ctventas->valor,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                ];
                $id_asiento_cab = AsientosCabecera::insertGetId($input_cabecera);
                $consulta_detalle = AsientosDetalles::where('id_asiento_cabecera', $consulta_cabecera_ctventas->id)->get();
                if (!empty($consulta_detalle)) {
                    foreach ($consulta_detalle as $value) {
                        AsientosDetalles::insert([
                            'id_asiento_cabecera' => $id_asiento_cab,
                            'id_plan_cuenta'      => $value->id_plan_cuenta,
                            'descripcion'         => $value->descripcion,
                            'fecha'               => $consulta_cabecera_ctventas->fecha_asiento,
                            'haber'               => $value->debe,
                            'debe'                => $value->haber,
                            'id_usuariocrea'      => $idusuario,
                            'id_usuariomod'       => $idusuario,
                            'ip_creacion'         => $ip_cliente,
                            'ip_modificacion'     => $ip_cliente,
                        ]);
                    }
                }
                LogContable::insert([
                    'tipo'           => "VEN-FA",
                    'valor_ant'      => $consulta_cabecera_ctventas["valor"],
                    'valor'          => $consulta_cabecera_ctventas["valor"],
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod'  => $idusuario,
                    'observacion'    => "Anulación -- {$consulta_cabecera_ctventas["observacion"]}",
                    'id_ant'         => $consulta_cabecera_ctventas["id"],
                    'id_referencia'  => $id_asiento_cab,
                ]);
                $kardex = Venta_Kardex::where('id_venta', $r["id"])->where('estado', 1)->groupBy('id_cab_moviento')->select(DB::raw('id_cab_moviento'))->get();
                if (count($kardex) > 0) {
                    foreach ($kardex as $item) {
                        InvCabMovimientos::anularTransaccionBodega($item->id_cab_moviento);
                        Venta_Kardex::where('estado', 1)->where('id_cab_moviento', $item->id_cab_moviento)->update(['estado' => 0, 'id_usuariomod' => $idusuario, 'ip_modificacion' => $ip_cliente]);
                    }
                    $asientoCosto =  LogVentaCosto::where("id_venta", $r["id"])->where("estado", 1)->first();
                    if (!is_null($asientoCosto)) {
                        $anuladoAsCosto = LogAsiento::anularAsiento($asientoCosto->id_asiento, "", "");
                        if ($anuladoAsCosto["status"] == "error") {
                            DB::rollback();
                            throw new Exception($anuladoAsCosto["msj"]);
                        }
                        $asientoCosto->estado = 0;
                        $asientoCosto->id_usuariomod = $idusuario;
                        $asientoCosto->ip_modificacion = $ip_cliente;
                        $asientoCosto->save();
                    }
                }
                DB::commit();
                return ["status" => "success", "msj" => "Registro Anulado", 'reload' => ''];
            }
        } catch (Exception $e) {
            DB::rollback();
            return ["status" => "error", "msj" => $e->getmessage(), 'linea' => $e->getLine()];
        }
    }
    public static function anularVentaVerificar($r)
    {
        DB::beginTransaction();
        try {
            $ventas = Ventas::find($r->id_venta);
            $arraymodules = [];
            $arraymodules['id'] = $ventas->id;
            $arraymodules['comprobante'] = $ventas->nro_comprobante;
            $arraymodules['cliente'] = $ventas->cliente->nombre;
            $arraymodules['asiento'] = $ventas->id_asiento;
            $arraymodules['total_final'] = $ventas->total_final;
            $arraymodules['valor_contable'] = $ventas->valor_contable;
            $arraymodules['usuariocrea'] = $ventas->usuario->nombre1 . ' ' . $ventas->usuario->apellido1;
            $arraymodules['creado'] = $ventas->created_at;
            $arraymodules['actualizado'] = $ventas->updated_at;
            if ($ventas->omni == 1) {
                $arraymodules['informacion'] = "OMNI";
            }
            if (isset($ventas->retenciones)) {
                $rete = ClientesRetenciones::where('id_factura', $r->id_venta)->get();
                $contador = 0;
                foreach ($rete as $value) {
                    if (($value->estado == 1)) {
                        $arraymodules['retencion' . $contador] = $value->id;
                    } else {
                        $arraymodules['retencionAnulado' . $contador] = $value->id;
                    }
                    $contador++;
                }
            }
            $ing = [];
            if (isset($ventas->comp_ingreso)) {
                if (count($ventas->comp_ingreso) > 0) {
                    foreach ($ventas->comp_ingreso as $ig) {
                        if (isset($ig->ingreso)) {
                            if ($ig->ingreso->estado == 1) {
                                array_push($ing, $ig->ingreso->id);
                            }
                        }
                    }
                    $arraymodules['ingreso'] = $ing;
                }
            }
            $cruce = [];
            if (isset($ventas->cruce)) {
                if (count($ventas->cruce) > 0) {
                    foreach ($ventas->cruce as $x) {
                        if (isset($x->cabecera)) {
                            if ($x->cabecera->estado == 1) {
                                array_push($cruce, $x->cabecera->id);
                            }
                        }
                    }
                    $arraymodules['cruce'] = $cruce;
                }
            }
            $cheque = [];
            if (isset($ventas->chequepost)) {
                if (count($ventas->chequepost) > 0) {
                    foreach ($ventas->chequepost as $ch) {
                        if (isset($ch->cabecera)) {
                            if ($ch->cabecera->estado == 1) {
                                array_push($cheque, $ch->cabecera->id);
                            }
                        }
                    }
                    $arraymodules['chequepost'] = $cheque;
                }
            }
            $xp = [];
            if (isset($ventas->cruce_cuentas)) {
                if (count($ventas->cruce_cuentas) > 0) {
                    foreach ($ventas->cruce_cuentas as $x) {
                        if ($x->estado != 0)
                            array_push($xp, $x->id);
                    }
                    $arraymodules['cruce_cuentas'] = $xp;
                }
            }
            $credi = [];
            if (isset($ventas->credito)) {
                if (count($ventas->credito) > 0) {
                    foreach ($ventas->credito as $c) {
                        if (isset($c->cabecera)) {
                            if ($c->cabecera->estado == 1) {
                                array_push($cheque, $c->cabecera->id);
                            }
                        }
                    }
                    $arraymodules['credito'] = $credi;
                }
            }
            DB::commit();
            return ["status" => "success", 'content' => $arraymodules, 'reload' => ''];
        } catch (Exception $e) {
            DB::rollback();
            return ["status" => "error", "msj" => $e->getmessage(), 'linea' => $e->getLine()];
        }
    }
    public static function anularVentaD($r)
    {
        DB::beginTransaction();
        try {
            $ip_cliente    = $_SERVER["REMOTE_ADDR"];
            $idusuario     = Auth::user()->id;
            $concepto      = $r['text'];
            $estado_ventas = Ventas::where('id', $r->id)->where('estado', '<>', 0)->first();

            if (!empty($estado_ventas)) {
                $registro_venta = Ventas::find($r->id);
                $msj            = Kardex::anular_kardex([
                    'id' => $r->id,
                    'tipo' => "VEN-FA"
                ]);
                if (!$msj["status"]) {
                    throw new Exception($msj['message']);
                }

                $ventasUpdate = Ventas::where('id', $r->id)->first();
                $ventasUpdate->estado =  0;
                $ventasUpdate->id_usuariomod  = $idusuario;
                $ventasUpdate->save();
                $ordenes = VenOrden::where('id_referencia', $r->id)->get();
                if (!is_null($ordenes)) {
                    foreach ($ordenes as $v) {
                        $vs          = VenOrden::find($v->id);
                        $vs->estado_pago = 0;
                        $vs->id_usuariomod = $idusuario;
                        $vs->save();
                    }
                }
                $consulta_cabecera_ctventas = AsientosCabecera::where('estado', '1')
                    ->where('id', $registro_venta->id_asiento)->first();

                $text = 'Fact #' . ':' . $registro_venta->nro_comprobante . '-' . $registro_venta->procedimientos . '-' . 'ANULACION FACTURA';

                $arrayAsientoCabecera = [
                    'sucursal' => $consulta_cabecera_ctventas->codigo_sucursal,
                    'punto_emision' => $consulta_cabecera_ctventas->codigo_caja,
                    'fecha_asiento' => $consulta_cabecera_ctventas->fecha_asiento,
                    'fact_numero' => $consulta_cabecera_ctventas->fact_numero,
                    'id_empresa' => session('id_empresa'),
                    'observacion' => $concepto . " " . $text,
                    'valor' => $consulta_cabecera_ctventas->valor,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                ];
                $id_asiento_cabecera = Contabilidad::crearAsiento('ventas', 'ingreso', $arrayAsientoCabecera,  $r, "anularVenta");

                if ($id_asiento_cabecera['status'] == 'error') {
                    throw new Exception($id_asiento_cabecera['message']);
                }

                $logAsientoV = LogAsiento::anulacion("VEN-FA", $id_asiento_cabecera, $consulta_cabecera_ctventas->id);
                if (!$logAsientoV["status"]) {
                    throw new Exception($msj['message']);
                }
                $kardex = Venta_Kardex::where('id_venta', $r->id)->where('estado', 1)->groupBy('id_cab_moviento')->select(DB::raw('id_cab_moviento'))->get();
                if (count($kardex) > 0) {
                    foreach ($kardex as $item) {
                        $invR =  InvCabMovimientos::anularTransaccionBodega($item->id_cab_moviento);

                        if (!$invR["status"]) {
                            throw new Exception($msj['message']);
                        }
                    }
                    $asientoCosto =  LogVentaCosto::where("id_venta", $r->id)->where("estado", 1)->first();
                    if (!is_null($asientoCosto)) {
                        $anuladoAsCosto = LogAsiento::anularAsiento($asientoCosto->id_asiento, "", "");
                        if (!$anuladoAsCosto["status"]) {
                            DB::rollback();
                            return $anuladoAsCosto;
                        }
                        $asientoCosto->estado = 0;
                        $asientoCosto->id_usuariomod = $idusuario;
                        $asientoCosto->ip_modificacion = $ip_cliente;
                        $asientoCosto->save();
                    }
                }
                DB::commit();
                return ["status" => "success", "msj" => "Registro Anulado", 'reload' => ''];
            }
        } catch (\Exception $e) {
            DB::rollback();
            return ["status" => "error", "msj" => $e->getmessage(), 'linea' => $e->getLine(), 'reload' => ''];
        }
    }
    public static function obtenerFacturaxCliente($id)
    {
        return self::join('ct_clientes as c', 'ct_ventas.id_cliente', '=', 'c.identificacion')
            ->leftJoin('ct_cliente_retencion as r', function ($join) {
                $join->on('ct_ventas.id', '=', 'r.id_factura')
                    ->where('r.estado', '=', 1);
            })
            ->where('ct_ventas.tipo', 'VEN-FA')
            ->where('ct_ventas.estado', '!=', 0)
            ->where('ct_ventas.id_empresa', session('id_empresa'))
            ->where('ct_ventas.valor_contable', '>', 0)
            ->where('ct_ventas.id_cliente', $id)
            ->whereNull('r.id_factura')
            ->get([
                'ct_ventas.id',
                'ct_ventas.id_cliente',
                'ct_ventas.numero',
                'c.nombre',
                'ct_ventas.concepto',
            ]);;
    }
    public static function verificarStock($r)
    {
        $id_bodega = $r->bodega;
        $cantidad = $r->cantidad;
        $cod_producto_conta = explode("|", $r->id_producto)[1];
        $empresa = Empresas::find(session("id_empresa"));
        if ($empresa->inventario === 0) {
            return ['status' => "success", "msj" => "No tiene habilitado el inventario"];
        }
        if ($empresa->egreso_kardex === 0) {
            return ['status' => "success", "msj" => "No tiene habilitado el egreso de kardex"];
        }
        $union_bodega = UnionBodega::where('id_bodega_cont', $id_bodega)->where('id_empresa', $empresa->id)->first();
        if (is_null($union_bodega)) {
            return ['status' => "error", "msj" => "No se encontro ligue de bodega", "exp" => ""];
        }
        $producto_contab = Productos::where('codigo', $cod_producto_conta)->where('id_empresa', $empresa->id)->first();
        if (is_null($producto_contab)) {
            return ['status' => "error", "msj" => "No se encontro el producto: {$cod_producto_conta}", "exp" => ""];
        }
        $union_producto = ProductosInsumos::where('id_producto', $producto_contab->id)->first();
        if (is_null($union_producto)) {
            return ['status' => "error", "msj" => "El producto no se encuentra ligado con los productos de compras: <br>Cod: {$producto_contab->nombre}", "exp" => ""];
        }
        $inv_inventario = [];
        $inv_inventario = InvInventarioSerie::select(DB::raw('SUM(existencia) as existencia'), 'id_bodega', 'id_producto', 'id_empresa')
            ->where('id_producto', $union_producto->id_insumo)->where('id_bodega', $union_bodega->id_bodega_compra)
            ->where('estado', 1)
            ->where('existencia', '>', 0)
            ->groupBy('id_producto', 'id_bodega')
            ->where('id_empresa', session("id_empresa"))
            ->get();
        $existencia = 0;
        foreach ($inv_inventario as $inventario) {
            if ($inventario->id_empresa != $empresa->id) {
                return ["status" => "error", "msj" => "Mal registrada el inventario por la empresa"];
            }
            if ($inventario->existencia >= $cantidad) {
                return ["status" => "success", "msj" => "Si tiene existencia: {$inventario->existencia}"];
            } else {
                $existencia += $inventario->existencia;
            }
        }
        return ["status" => "error", "msj" => "Cantidades insuficientes en la bodega, solicite traslado de bodegas<br><b>Cantidad Solicitada: <b>{$cantidad} <br> <b>Cantidad en bodega: <b>{$existencia} "];
    }
    public static function guardarReciboCobro($r)
    {
        DB::beginTransaction();
        DB::enableQueryLog();
        try {
            $origin = $r->input();
            $origin['d'] = '|';
            $electronica = 0;
            $nfactura        = 0;
            $arrayRespuesta = [];
            $empresa = Empresas::find(session('id_empresa'));
            $idusuario = Auth::user()->id;
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $cod_sucurs = PuntoEmision::where('estado', '1')->where('id', $r->sucursal_empre)->first();
            $c_sucursal = $cod_sucurs->codigo_sucursal;
            $cod_caj = DB::table('ct_caja')->where('estado', '1')->where('id', $r->punto_emision)->first();
            $c_caja = $cod_caj->codigo_caja;
            $datos = [
                'sucursal' => $c_sucursal, //1
                'punto_emision' => $c_caja, //100
                'tipo' => 1
            ];
            $num_comprobante = self::generarNumeroDocumento($datos);
            if ($num_comprobante == '') {
                $arrayRespuesta = [
                    'status' => 'error',
                    'code' => 500,
                    'message' => 'La empresa no tiene configurada la información tributaria',
                    'footer' => '<a href="javascript:;" onclick="crearInfoTributaria();">¿Desea crear está información?</a>',
                    'reload' => ''
                ];
                DB::rollBack();
                return json_encode($arrayRespuesta);
            }
            $cliente = Clientes::where('identificacion', $r->identificacion_cliente)->first();
            $paciente = Pacientes::where('id', $r->identificacion_paciente)->first();
            $pac = "";
            if ($r['nombre_paciente'] != "") {
                $pac = " | " . $r['nombre_paciente'];
            }
            $text        = 'Fact #' . ':' . $num_comprobante . '-' . $r->procedimiento . $pac;
            $arrayAsientoCabecera = [
                'sucursal' => $c_sucursal,
                'punto_emision' => $c_caja,
                'fecha_asiento' => $r->fecha,
                'fact_numero' => $num_comprobante,
                'id_empresa' => session('id_empresa'),
                'observacion' => $text . " " . $r['concepto'],
                'valor' => $r['total1'],
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ];
            $id_asiento_cabecera = Contabilidad::crearAsiento('ventas', 'facturadeventa', $arrayAsientoCabecera,  $r, "venta");
            if ($id_asiento_cabecera['status'] == 'error') {
                throw new Exception($id_asiento_cabecera['message']);
            }
            $deEmpresa = DeEmpresas::where('id_empresa', session('id_empresa'))->first();
            if ($deEmpresa != '') {
                $claveObj = new ClaveAcceso();
                $numComprobante = explode('-', $num_comprobante);
                $campos = [
                    'fecha_emision' => str_replace('/', '-', date('d/m/Y', strtotime($r->fecha_emision))),
                    'tipo_comprobante' => '01',
                    'ruc' => session('id_empresa'),
                    'tipo_ambiente' => $deEmpresa->ambiente,
                    'punto_establecimiento' => $numComprobante[0],
                    'punto_emision' => $numComprobante[1],
                    'numero_comprobante' => $numComprobante[2],
                    'codigo_numerico' => $numComprobante[2],
                    'tipo_emision' => 1
                ];
                $claveObj->llenarDatos($campos);
                $electronica = 1;
            }
            $msjk = ", Store: no tiene inventario";
            if ($empresa->inventario == 1) {
                $msjk = ", Store: tiene inventario";
            }
            // validar el cliente
            $cliente = Clientes::where('identificacion', '=', $r['identi'])->count();

            $id_proforma = $r->proforma;
            if ($cliente == 0) {
                Clientes::insert([
                    'nombre' => strtoupper($r['nombre_cliente']),
                    'tipo' => '5',
                    'identificacion' => $r['identi'],
                    'clase' => '1',
                    'nombre_representante' => $r['nombre_cliente'],
                    'cedula_representante' => $r['identi'],
                    'ciudad_representante' => $r['ciudad_cliente'],
                    'direccion_representante' => $r['direccion_cliente'],
                    'telefono1_representante' => $r['telefono_cliente'],
                    'email_representante' => $r['mail_cliente'],
                    'estado' => '1',
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                ]);
            }

            $cliente = Clientes::where('identificacion', '=', $r['identi'])->first();

            $secuencialserie = explode('-', $num_comprobante);
            $diasPlazo = "0";
            $nota_electronica = "";
            if ($r->electronica == 1) {
                $diasPlazo = $r->diasPlazo;
                $nota_electronica = isset($r['nota_electronica'][0]) ? $r['nota_electronica'][0] : '';
            }
            $factura_venta = [
                'sucursal' => $c_sucursal,
                'punto_emision' => $c_caja,
                'nro_comprobante' => $num_comprobante,
                'id_asiento' => $id_asiento_cabecera,
                'id_empresa' => $empresa->id,
                'tipo' => $r['tipo'],
                'fecha' => $r['fecha'],
                'divisas' => $r['divisas'],
                'nombre_cliente' => $cliente->nombre,
                'tipo_consulta' => $r['tipo_consulta'],
                'id_cliente' => $r['identi'], //nombre_cliente
                'direccion_cliente' => $r['direccion_cliente'],
                'ruc_id_cliente' => $r['identi'],
                'telefono_cliente' => $r['telefono_cliente'],
                'email_cliente' => $r['mail_cliente'],
                'orden_venta' => $r['orden_venta'],
                'nro_autorizacion' => $r['numero_autorizacion'],
                'id_paciente' => $r['razon_social_edit'],
                'nombres_paciente' => $r['nombre_paciente'],
                'id_hc_procedimiento' => $r['mov_paciente'],
                'seguro_paciente' => $r['id_seguro'],
                'procedimientos' => $r['procedimiento'],
                'fecha_procedimiento' => $r['fecha_procedimiento'],
                'concepto' => $r['concepto'],
                'copago' => $r['totalc'],
                'id_recaudador' => $r['cedula_recaudador'],
                'ci_vendedor' => $r['cedula_vendedor'],
                'vendedor' => $r['vendedor'],
                'electronica' => $r['electronica'],
                //'nota' => $r['nota'],
                'subtotal_0' => $r['subtotal_01'],
                'subtotal_12' => $r['subtotal_121'],
                //'subtotal' => $r['subtotal1'],
                'descuento' => $r['descuento1'],
                'base_imponible' => $r['subtotal_121'],
                'impuesto' => $r['tarifa_iva1'],
                // 'transporte' => $r['transporte'],
                'total_final' => $r['total1'],
                'valor_contable' => $r['total1'],
                'dias_plazo' => $diasPlazo,
                'nota_electronica' => $nota_electronica,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
                'nota_array' => $r['nota_electronica'] != "" ?  json_encode($r['nota_electronica']) : json_encode([]),
                'doc_electronico' => $c_caja == 100 ? 0 : null,
                'emisor' => 'MD'
            ];



            if (is_null($r->identi_paciente)) {
                $factura_venta = [
                    'sucursal'            => $c_sucursal,
                    'punto_emision'       => $c_caja,
                    'nro_comprobante'     => $num_comprobante,
                    'id_asiento'          => $id_asiento_cabecera,
                    'id_empresa'          => session("id_empresa"),
                    'tipo'                => $r['tipo'],
                    'fecha'               => $r['fecha'],
                    'divisas'             => $r['divisas'],
                    'nombre_cliente'      => $r['nombre_cliente'],
                    'tipo_consulta'       => $r['tipo_consulta'],
                    'id_cliente'          => $r['identi'],
                    'direccion_cliente'   => $r['direccion_cliente'],
                    'ruc_id_cliente'      => $r['identi'],
                    'telefono_cliente'    => $r['telefono_cliente'],
                    'email_cliente'       => $r['mail_cliente'],
                    'orden_venta'         => $r['orden_venta'],
                    'nro_autorizacion'    => $r['numero_autorizacion'],
                    'nombres_paciente'    => $r['nombre_paciente'],
                    'id_hc_procedimiento' => $r['mov_paciente'],
                    'seguro_paciente'     => $r['id_seguro'],
                    'procedimientos'      => $r['procedimiento'],
                    'fecha_procedimiento' => $r['fecha_proced'],
                    'concepto'            => $r['concepto'],
                    'copago'              => $r['totalc'],
                    'id_recaudador'       => $r['cedula_recaudador'],
                    'ci_vendedor'         => $r['cedula_vendedor'],
                    'vendedor'            => $r['vendedor'],
                    'electronica'         => $r['electronica'],
                    'subtotal_0'          => $r['subtotal_01'],
                    'subtotal_12'         => $r['subtotal_121'],
                    'descuento'           => $r['descuento1'],
                    'base_imponible'      => $r['subtotal_121'],
                    'impuesto'            => $r['tarifa_iva1'],
                    'total_final'         => $r['total1'],
                    'valor_contable'      => $r['total1'],
                    'dias_plazo'          => $diasPlazo,
                    'nota_electronica'    => $nota_electronica,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'nota_array'          => $r['nota_electronica'] != "" ?  json_encode($r['nota_electronica']) : json_encode([]),
                    'doc_electronico' => $c_caja == 100 ? 0 : null,
                    'emisor' => 'MD',
                    'contraentrega' => $r['contraentrega'] != '' ? $r['contraentrega'] : 0,
                ];
            }
            $idVenta = self::insertGetId($factura_venta);
            $arr_total      = [];
            $total_iva      = 0;
            $total_impuesto = 0;
            $total_0        = 0;

            for ($i = 0; $i < count($r->input("codigo")); $i++) {
                if ($r->input("bodega")[$i] !== null) {
                    $checkA = explode("|", $r->input("nombre")[$i]);
                    $arr = [
                        'nombre'        => count($checkA) == 1 ? $checkA[0] : $checkA[2],
                        'cantidad'      => $r->input("cantidad")[$i],
                        'bodega'        => isset($r["bodega"][$i]) ? $r->input("bodega")[$i] : null,
                        'codigo'        => count($checkA) == 1 ? $checkA[0] : $checkA[1],
                        'precio'        => $r->input("precio")[$i],
                        'descpor'       => $r->input("descpor")[$i],
                        'copago'        => $r->input("copago")[$i],
                        'descuento'     => $r->input("desc")[$i],
                        'precioneto'    => $r->input("precioneto")[$i],
                        'detalle'       => $r->input("descrip_prod")[$i],
                        'iva'           => $r->input("iva")[$i],
                        'id_inv_serie'  => isset($r["id_inv_serie"][$i]) ? $r->input("id_inv_serie")[$i] : null,
                    ];
                    array_push($arr_total, $arr);
                }
            }

            dd($arr_total);
            $cont = 0;
            $r->c = 'FacturaVenta';
            $r->s = 'saveVenta';
            $r->d = $origin['d'];
            $r->m = self::$modelo;
            $r->o = 'Se agrego una nueva factura de venta con id.: ' . $idVenta;
            Auditorias::saveAuditoria($r, DB::getQueryLog());
            DB::disableQueryLog();
            $valorTotSin = 0;
            foreach ($r->id_pago as $key => $value) {
                if ($value != 7) {
                    $valorTotSin += $r["valor"][$key];
                }
            }
            $contComprobante = 0;
            foreach ($r->id_pago as $key => $row) {
                if ($row != 7) {
                    $arrayAsientoCabecera = [
                        'sucursal' => $c_sucursal,
                        'punto_emision' => $c_caja,
                        'fecha_asiento' => $r->fecha_emision,
                        'fact_numero' => $num_comprobante,
                        'id_empresa' => session('id_empresa'),
                        'observacion' => 'COMPROBANTE DE INGRESO FACT:' . $num_comprobante,
                        'valor' => $valorTotSin,
                        'id_usuariocrea' => $idusuario,
                        'id_usuariomod' => $idusuario,
                        'ip_creacion' => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                    ];
                    $id_asiento_cabecera = Contabilidad::crearAsiento('ventas', 'comprobante_ingreso', $arrayAsientoCabecera, $r);
                    if ($id_asiento_cabecera['status'] == 'error') {
                        throw new Exception($id_asiento_cabecera['message']);
                    }
                    $contComprobante++;
                }
                $fecha_pago = $r->fecha_pago[$key] != "" ? $r->fecha_pago[$key] : $r->fecha_emision;
                $val = 0;
                $val = $r->valor[$key];
                $arrayFormaPago = [
                    'id_ct_ventas'    => $idVenta,
                    'tipo'            => $r->id_pago[$key],
                    'fecha'           => $fecha_pago,
                    'tipo_tarjeta'    => $r->id_tarjetas[$key],
                    'numero'          => $r->numero[$key],
                    'banco'           => $r->id_bancos[$key],
                    'cuenta'          => $r->cuenta[$key],
                    'giradoa'         => $r->giradoA[$key],
                    'valor'           => $val,
                    'valor_base'      => $val,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                ];
                FormaPago::insert($arrayFormaPago);
            }
            for ($j = 0; $j < count($r->codigo); $j++) {
                $detalleVentas = explode('|', $r->producto[$j]);
                $arrayAdicional = [
                    'nombre' => $r->nombreAdicional,
                    'detalle' => $r->valorAdicional
                ];
                if (config('data.empresa')->egreso_kardex == 1 && $r->bodega[$j] == '' && $r->kardex == 'on') {
                    $arrayRespuesta = [
                        'status' => 'error',
                        'code' => 500,
                        'message' => 'Debe seleccionar la bodega del producto en la fila: ' . $j,
                        'reload' => ''
                    ];
                    DB::rollBack();
                    return json_encode($arrayRespuesta);
                }
                $detalle = [
                    'id_ct_ventas' => $idVenta,
                    'id_ct_productos' => $r->codigo[$j],
                    'nombre' => $detalleVentas[2],
                    'descripcion_producto' => $detalleVentas[2],
                    'bodega' => $r->bodega[$j],
                    'cantidad' => $r->cantidad[$j],
                    'precio' => $r->precio[$j],
                    'descuento_porcentaje' => $r['descpor'],
                    'descuento' => $r->descuento[$j],
                    'extendido' => $r->precio[$j] * $r->cantidad[$j],
                    'detalle' => $r->descripcion[$j],
                    'check_iva' => $r->iva_fila[$j] != '' ? 1 : 0,
                    'porcentaje' => config('data.impuestoIva'),
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                    'otros' => json_encode($arrayAdicional)
                ];
                VentasDetalle::insert($detalle);
            }
            if ($contComprobante > 0) {
                $arrayComprobante = [
                    'observaciones' => 'COMPROBANTE DE INGRESO FACT: ' . $num_comprobante,
                    'estado' => '1',
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'fecha' => $r->fecha_emision,
                    'secuencia' =>  $num_comprobante,
                    'divisas' => '1',
                    'id_empresa' => session('id_empresa'),
                    'total_ingreso' => 0,
                    'id_cliente' => $r->identificacion_cliente,
                    'autollenar' => "Cancela FV : " .  $num_comprobante,
                    'id_vendedor' =>  $r->vendedor,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                ];
                $totalV = 0;
                $id_comprobante = ComprobanteIngreso::insertGetId($arrayComprobante);
                $totalFactura = 0;
                for ($i = 0; $i < count($r->id_pago); $i++) {
                    $val = 0;
                    $val = $r->valor[$i];
                    $totalFactura += $val;
                    $fecha_pago = $r->fecha_pago[$i] != "" ? $r->fecha_pago[$i] : $r->fecha_emision;
                    if ($r->id_pago[$i] != 7) {
                        $totalV += $val;
                        $arrayDetalleComprobante = [
                            'id_comprobante'  => $id_comprobante,
                            'fecha' => $fecha_pago,
                            'numero' => $r->numero[$i],
                            'id_banco' => $r->id_bancos[$i],
                            'id_tipo_tarjeta' => $r->id_tarjetas[$i],
                            'id_tipo' => $r->id_pago[$i],
                            'total' => $val,
                            'cuenta' => $r->cuenta[$i],
                            'girador' => $r->giradoA[$i],
                            'estado' => '1',
                            'ip_creacion' => $ip_cliente,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariocrea' => $idusuario,
                            'id_usuariomod' => $idusuario,
                        ];
                        DetallePagoIngreso::insert($arrayDetalleComprobante);
                    }
                }
                $reG = ComprobanteIngreso::find($id_comprobante);
                $r->c = 'FacturaVentas';
                $r->s = 'saveVentas';
                $r->d = $origin['d'];
                $r->m = self::$modelo;
                $r->o = 'Se agrego un comprobante de ingreso id.: ' . $reG;
                Auditorias::saveAuditoria($r, DB::getQueryLog());
                DB::disableQueryLog();
                $reG->total_ingreso = $totalV;
                $reG->save();
                $arrayDetalleComprobanteIngreso = [
                    'id_comprobante'    => $id_comprobante,
                    'fecha'             => $fecha_pago,
                    'observaciones'     => "Cancela FV : " . $num_comprobante,
                    'id_factura'        => $idVenta,
                    'secuencia_factura' => $num_comprobante,
                    'total_factura'     => $totalFactura,
                    'total'             => $totalV,
                    'estado'            => '1',
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                ];
                DetalleComprobanteIngreso::insert($arrayDetalleComprobanteIngreso);
            }
            $arrayInfoTributaria = [
                'secuencial_nro' => (int)$numComprobante[2],
                'numero_factura' => $numComprobante[0] . '-' . $numComprobante[1] . '-' . $numComprobante[2],
            ];
            InfoTributaria::where('id_empresa', session('id_empresa'))
                ->where('id_maestro_documentos', 1)
                ->where('cod_sucursal', $c_sucursal)
                ->where('cod_caja', $c_caja)
                ->update($arrayInfoTributaria);
            if (isset($r->kardex)) {
                $kardex = Kardex::newgenerarKardex($idVenta);
                if (!$kardex["status"]) {
                    throw new Exception($kardex["msj"]);
                }
                if (isset($kardex["generate"]) and $kardex["generate"]) {
                    $kardex["asiento_costo"] = $kardex["asiento_costo"];
                    $logCosto = LogAsiento::guardarAsientoCostoVenta($idVenta, $kardex["asiento_costo"]);
                    if (!$logCosto["status"]) {
                        throw new Exception($kardex["logCosto"]);
                    }
                }
            }
            DB::commit();
            $arrayRespuesta = [
                'status' => 'success',
                'code' => 200,
                'message' => 'Registro guardado correctamente',
                'data' => $r->input(),
                'reload' => 'v3/ventas/listadeventas'
            ];
            return json_encode($arrayRespuesta);
        } catch (Exception $e) {
            DB::rollBack();
            $arrayRespuesta = [
                'status' => 'error',
                'code' => 500,
                'message' => $e->getMessage(),
                'linea' => $e->getLine(),
                'file' => $e->getFile(),
                'data' => $r->input(),
                'reload' => ''
            ];
            $r->c = 'FacturaVenta';
            $r->s = 'saveVenta';
            $r->d =  $origin['d'];
            $r->m = self::$modelo;
            $r->o = 'Ocurrio un error: ' . $e->getMessage();
            Auditorias::saveAuditoria($r, DB::getQueryLog());
            DB::disableQueryLog();
            return json_encode($arrayRespuesta);
        }
    }

    public static function descargarComprobanteVenta($r)
    {
        $venta = self::find($r->id);
        $retencionAsiento = null;
        $retencion = null;
        $asientoRetencion = $venta->retenciones ? $venta->retenciones->id_asiento_cabecera : null;
        if ($asientoRetencion !== null) {
            $retencionAsiento = AsientosDetalles::join('plan_cuentas as p', 'ct_asientos_detalle.id_plan_cuenta', 'p.id')
                ->join('plan_cuentas_empresa as pe', function ($q) {
                    $q->on('p.id', 'pe.id_plan')->where('pe.id_empresa', session('id_empresa'));
                })
                ->where('id_asiento_cabecera', $asientoRetencion)
                ->get([
                    'pe.plan',
                    'p.nombre',
                    'ct_asientos_detalle.debe',
                    'ct_asientos_detalle.haber'
                ]);
        }
        $retencion = $venta->retenciones;
        $sqlMovimiento = "
        select pe.plan,p.nombre,
            d.debe,
            d.haber
            from ct_ventas v 
        left join log_venta_costo k on v.id=k.id_venta
        join ct_asientos_cabecera c on k.id_asiento=c.id
        join ct_asientos_detalle d on c.id=d.id_asiento_cabecera
        JOIN plan_cuentas p ON d.id_plan_cuenta = p.id
        JOIN plan_cuentas_empresa pe ON p.id = pe.id_plan AND pe.id_empresa = '$venta->id_empresa'
        where v.id=$venta->id
        ";
        $queryAsientos = AsientosDetalles::join('plan_cuentas as p', 'ct_asientos_detalle.id_plan_cuenta', 'p.id')
            ->join('plan_cuentas_empresa as pe', function ($q) {
                $q->on('p.id', 'pe.id_plan')->where('pe.id_empresa', session('id_empresa'));
            })
            ->where('id_asiento_cabecera', $venta->id_asiento)
            ->get([
                'pe.plan',
                'p.nombre',
                'ct_asientos_detalle.debe',
                'ct_asientos_detalle.haber'
            ]);
        $queryMovimiento = DB::select(DB::raw($sqlMovimiento));
        $usuarioSession = Auth::user()->id;
        $formasPago = FormaPago::where("id_ct_ventas", $r->id)->get();
        $subtotal5 = 0;
        $subtotal15 = 0;
        $iva15 = 0;
        $iva5 = 0;
        $subtotal = 0;
        $descuento = 0;
        $resTotal = 0;
        $vat = DetalleVenta::where("id_ct_ventas", $r->id)
            ->groupBy("porcentaje")
            ->get(["porcentaje"]);
        $taxRates = $vat->pluck('porcentaje', 'porcentaje')->toArray();
        foreach ($venta->detalles as $val) {
            $porcentaje = $val->porcentaje * 100;
            $veri = (string) $val->porcentaje;
            if (isset($taxRates[$veri])) {
                if ($val->check_iva == 1) {
                    if ($porcentaje == 5) {
                        $iva5 += $val->extendido * ($val->porcentaje);
                    } elseif ($porcentaje == 15) {
                        $iva15 += $val->extendido * ($val->porcentaje);
                    }
                }

                if ($porcentaje == 5) {
                    $subtotal5 += $val->precio * $val->cantidad;
                } elseif ($porcentaje == 15) {
                    $subtotal15 += $val->precio * $val->cantidad;
                }
            } else {
                $subtotal += $val->extendido;
            }

            $resTotal += ($val->precio * $val->cantidad) - $val->descuento;
            $descuento += $val->descuento;
        }
        $user = Usuarios::find($usuarioSession);
        $data['resTotal'] = $resTotal;
        $data['descuento'] = $descuento;
        $data['subtotal'] = $subtotal;
        $data['subtotal5'] = $subtotal5;
        $data['subtotal15'] = $subtotal15;
        $data['iva15'] = $iva15;
        $data['iva5'] = $iva5;
        $data['venta'] = $venta;
        $data['user'] = $user;
        $data['queryAsientos'] = $queryAsientos;
        $data['formasPago'] = $formasPago;
        $data['queryMovimiento'] = $queryMovimiento;
        $data['retencion'] = $retencion;
        $data['retencionAsiento'] = $retencionAsiento;
        $view = \View::make('v3.contable.ventas.pdf_comprobante', $data)->render();
        $pdf  = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view)->setPaper('a4', 'portrait');
        $pdf->setOptions(['chroot'  => base_path('/')]);
        return $pdf->stream('Comprobante de Venta No.: ' . $venta->nro_comprobante . '.pdf');
    }
}
