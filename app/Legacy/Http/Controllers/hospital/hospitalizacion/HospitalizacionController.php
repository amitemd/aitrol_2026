<?php

namespace Sis_medico\Http\Controllers\hospital\hospitalizacion;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Bodega;
use Sis_medico\Ct_Ven_Orden;
use Sis_medico\Ct_Ven_Orden_Detalle;
use Sis_medico\Ho_Solicitud;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Opcion_Usuario;
use Sis_medico\Sala;
use Sis_medico\Ho_Traspaso_Sala008;
use Sis_medico\hc_receta;
use Sis_medico\Movimiento;
use Sis_medico\Hc_Evolucion;
use Sis_medico\hc_receta_detalle;
use Sis_medico\Producto_Medicina;
use Sis_medico\Hospitalizacion;
use Sis_medico\Empresa;
use Sis_medico\ModuloBodega;
use Illuminate\Support\Facades\Session;
use Sis_medico\agenda;
use Sis_medico\hc_procedimientos;
use Sis_medico\Movimiento_Paciente;
use Sis_medico\Planilla;

class HospitalizacionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    private function rol_new($opcion)
    {
        $rolUsuario     = Auth::user()->id_tipo_usuario;
        $opcion_usuario = Opcion_Usuario::where('id_opcion', $opcion)->where('id_tipo_usuario', $rolUsuario)->first();
        if (is_null($opcion_usuario)) {
            return true;
        }
    }
    public function master()
    {
        $fecha_desde = date('Y-m-d');
        $fecha_hasta = date('Y-m-d');

        $salas = Sala::where('estado', '1')->where('id_hospital', '5')->get();
        $en_habitacion = Ho_Traspaso_Sala008::where('ho_traspaso_sala008.estado', '2')
            ->whereBetween('fecha', [$fecha_desde . ' 00:00', $fecha_hasta . ' 23:59'])
            ->join('sala as s', 's.id', 'ho_traspaso_sala008.id_sala')
            ->select('ho_traspaso_sala008.*', 's.nombre_sala')
            ->get();
        //dd($en_habitacion);
        //dd($en_habitacion);

        return view('hospital/habitacion/master', ['salas' => $salas, 'en_habitacion' => $en_habitacion, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta, 'cedula' => '', 'nombres' => '', 'sala' => '']);
    }

    public function buscar_hospitalizado(Request $request)
    {
        //$fecha_hoy = date('Y-m-d');
        $fecha_desde = $request['fecha_desde'];
        $fecha_hasta = $request['fecha_hasta'];
        $sala = $request['sala'];
        $nombres = $request['paciente'];
        $cedula = $request['cedula'];
        //dd($request->all());
        $en_habitacion = Ho_Traspaso_Sala008::where('ho_traspaso_sala008.estado', '2')
            ->join('paciente as p', 'p.id', 'ho_traspaso_sala008.id_paciente')
            ->join('sala as s', 's.id', 'ho_traspaso_sala008.id_sala')
            ->select('ho_traspaso_sala008.*', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 's.nombre_sala');

        if ($fecha_desde != null && $fecha_hasta != null) {
            $en_habitacion = $en_habitacion->whereBetween('fecha', [$fecha_desde . ' 00:00', $fecha_hasta . ' 23:59']);
        }

        if ($sala != null) {
            $en_habitacion = $en_habitacion->where('id_sala', $sala);
        }


        if ($cedula != null) {
            $en_habitacion = $en_habitacion->where('id_paciente', $cedula);
        }

        if ($nombres != null) {
            $nombres2 = explode(" ", $nombres);
            $cantidad = count($nombres2);

            if ($cantidad == '2' || $cantidad == '3') {
                $en_habitacion = $en_habitacion->where(function ($jq1) use ($nombres) {
                    $jq1->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', ['%' . $nombres . '%'])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', ['%' . $nombres . '%']);
                });
            } else {

                $en_habitacion = $en_habitacion->whereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', ['%' . $nombres . '%']);
            }
        }

        $en_habitacion = $en_habitacion->get();
        //dd($en_habitacion);

        $salas = Sala::where('estado', '1')->where('id_hospital', '5')->get();

        return view('hospital/habitacion/master', ['salas' => $salas, 'en_habitacion' => $en_habitacion, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta, 'nombres' => $nombres, 'cedula' => $cedula, 'sala' => $sala]);
    }

    public function descargo_medicina($id_solic)
    {

        $solicitud = Ho_Solicitud::find($id_solic);

        $logs = $solicitud->log;

        foreach ($logs as $log) {
            if ($log->id_agenda != null) {
                $agenda[$log->id_agenda] = $log->agenda;
            }
        }

        $procedimientos = hc_procedimientos::join('historiaclinica as hc', 'hc.hcid', '=', 'hc_procedimientos.id_hc')
            ->where('hc.id_agenda', $solicitud->id_agenda)->get();

        return view('hospital/hospitalizacion/index', ['solicitud' => $solicitud, 'agenda' => $agenda, 'procedimientos' => $procedimientos]);
    }


    public function descargo_enfermeria_detalle($id_receta, $id_solicitud)
    {

        $receta = hc_receta::find($id_receta);
        $solicitud = Ho_Solicitud::find($id_solicitud);
        $detalles = $receta->detalles;
        $id_empresa = Session::get('id_empresa');
        $bodegas = Bodega::where('id_empresa', $id_empresa)->where('estado', 1)->get();
        $id_usuario = Auth::user()->id;
        $modulo_bodega = ModuloBodega::where('estado', '1')->where('id_usuario', $id_usuario)->where('id_empresa', $id_empresa)->where('id_modulo', 2)->first();
        $procedimientos = hc_procedimientos::join('historiaclinica as hc', 'hc.hcid', '=', 'hc_procedimientos.id_hc')
            ->where('hc.id_agenda', $solicitud->id_agenda)->get();
        if ($procedimientos->count() > 0) {
            $hcid = $procedimientos->first()->hcid;
        
        } else {
            $hcid = "";
        }

        return view('hospital/hospitalizacion/detalle', ['detalles' => $detalles, 'bodegas' => $bodegas, 'modulo_bodega' => $modulo_bodega, 'procedimientos' => $procedimientos, 'hcid' => $hcid]);
    }

    public function descargo_enfermeria_detalle_store(Request $request)
    {

        $id_detalle = $request->id;
        $serie = $request->serie;

        $receta_detalle = hc_receta_detalle::find($id_detalle); //aqui esta la cantidad

        $producto_medicina  = Producto_Medicina::where('id_medicina', $receta_detalle->id_medicina)->first();
        $movimiento         = Movimiento::where('serie', $serie)->first();
        if (is_null($movimiento)) {
            return ['estado' => 'E', 'msn' => 'No se encuentra Número de Serie'];
        } else {
            if ($movimiento->id_producto != $producto_medicina->id_producto) {
                return ['estado' => 'E', 'msn' => 'Numero de Serie No corresponde a la medicina'];
            }
        }

        $receta_detalle->update([
            'id_producto'   => $movimiento->id_producto,
            'descargo'      => '1',
            'serie'         => $serie,
        ]);
        $receta_detalle = hc_receta_detalle::where('descargo', '1')->get();


        return ['estado' => 'O', 'msn' => 'Medicamento descargado con exito'];
    }
    public function formulario005_pdf($id, Request $request)
    {

        $empresa        = Empresa::where('prioridad', 2)->get()->first();
        $solicitud005 = Ho_Solicitud::where('ho_solicitud.id', $id)
            ->join('agenda as ag', 'ag.id', 'ho_solicitud.id_agenda')
            ->join('historiaclinica as h', 'h.id_agenda', 'ag.id')
            ->join('hc_procedimientos as hc_proc', 'hc_proc.id_hc', 'h.hcid')
            ->select('ag.id as id_agenda', 'h.hcid', 'hc_proc.id as id_hcproc', 'ho_solicitud.id_paciente', 'ho_solicitud.id')
            ->first();
        //dd($solicitud005);
        $evoluciones = Hc_Evolucion::where('hc_id_procedimiento', $solicitud005->id_hcproc)->get();
        $historia = $solicitud005->agenda->historia_clinica;
        $receta = hc_receta::where('id_hc', $historia->hcid)->first();
        //dd($recetas);
        $view = \View::make('hospital.habitacion.formulario005_pdf', ['empresa', 'solicitud005' => $solicitud005, 'evoluciones' => $evoluciones,  'historia' => $historia,  'detalles' => $receta->detalles, 'receta' => $receta, 'empresa' => $empresa])->render();
        $pdf  = \App::make('dompdf.wrapper');

        $pdf->loadHTML($view);
        return $pdf->stream('formulario005_pdf.pdf');
    }
    public function store_ordenes(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $fecha_as   = $request['fecha_asiento'];
        $id_empresa = $request->session()->get('id_empresa');
        DB::beginTransaction();
        try {
            $c_sucursal      = 0;
            $c_caja          = 0;
            $num_comprobante = 0;
            $nfactura        = 0;
            $proced          = $request['procedimiento'];
            $pac             = "";
            if ($request['nombre_paciente'] != "") {
                $pac = " | " . $request['nombre_paciente'];
            }
            $id_asiento_cabecera = 0;
            $factura_venta = [
                'sucursal'            => $c_sucursal,
                'punto_emision'       => $c_caja,
                'numero'              => $nfactura,
                'nro_comprobante'     => $num_comprobante,
                'id_asiento'          => $id_asiento_cabecera,
                'id_empresa'          => $id_empresa,
                'tipo'                => $request['tipo'],
                'fecha'               => $request['fecha_asiento'],
                'divisas'             => $request['divisas'],
                'nombre_cliente'      => $request['nombre_cliente'],
                'tipo_consulta'       => $request['tipo_consulta'],
                'id_cliente'          => $request['identificacion_cliente'],
                'direccion_cliente'   => $request['direccion_cliente'],
                'ruc_id_cliente'      => $request['identificacion_cliente'],
                'telefono_cliente'    => $request['telefono_cliente'],
                'email_cliente'       => $request['mail_cliente'],
                'orden_venta'         => $request['orden_venta'],
                'estado_pago'         => '0',
                'id_paciente'         => $request['identificacion_paciente'],
                'nombres_paciente'    => $request['nombre_paciente'],
                'id_hc_procedimiento' => $request['mov_paciente'],
                'seguro_paciente'     => $request['id_seguro'],
                'procedimientos'      => $request['procedimiento'],
                'fecha_procedimiento' => $request['fecha_proced'],
                'copago'              => $request['total1'],
                'id_recaudador'       => $request['cedula_recaudador'],
                'ci_vendedor'         => $request['cedula_vendedor'],
                'vendedor'            => $request['vendedor'],
                'subtotal_0'          => $request['subtotal_01'],
                'subtotal_12'         => $request['subtotal_121'],
                'descuento'           => $request['descuento1'],
                'base_imponible'      => $request['subtotal_121'],
                'impuesto'            => $request['tarifa_iva1'],
                'total_final'         => $request['totalc'],
                'valor_contable'      => $request['total1'],
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
            ];
            $id_venta = Ct_Ven_Orden::insertGetId($factura_venta);
            //$id_venta = 0;
            $arr_total      = [];

            for ($i = 0; $i < count($request->input("nombre")); $i++) {
                if ($request->input("nombre")[$i] != "" || $request->input("nombre")[$i] != null) {
                    $arr = [
                        'nombre'     => $request->input("nombre")[$i],
                        'cantidad'   => $request->input("cantidad")[$i],
                        'codigo'     => $request->input("codigo")[$i],
                        'precio'     => $request->input("precio")[$i],
                        'descpor'    => $request->input("descpor")[$i],
                        'copago'     => $request->input("copago")[$i],
                        'descuento'  => $request->input("desc")[$i],
                        'precioneto' => $request->input("precioneto")[$i],
                        'detalle'    => $request->input("descrip_prod")[$i],
                        'iva'        => $request->input("iva")[$i],

                    ];
                    array_push($arr_total, $arr);
                }
            }
            foreach ($arr_total as $valor) {
                $detalle = [
                    'id_ct_ven_orden'      => $id_venta,
                    'id_ct_productos'      => $valor['codigo'],
                    'nombre'               => $valor['nombre'],
                    'cantidad'             => $valor['cantidad'],
                    'precio'               => $valor['precio'],
                    'descuento_porcentaje' => $valor['descpor'],
                    'descuento'            => $valor['descuento'],
                    'extendido'            => $valor['copago'],
                    'detalle'              => $valor['detalle'],
                    'copago'               => $valor['precioneto'],
                    'check_iva'            => $valor['iva'],
                    'ip_creacion'          => $ip_cliente,
                    'ip_modificacion'      => $ip_cliente,
                    'id_usuariocrea'       => $idusuario,
                    'id_usuariomod'        => $idusuario,
                ];

                Ct_Ven_Orden_Detalle::create($detalle);
            }

            return ['id' => $id_venta];
        } catch (\Exception $e) {
            //if there is an error/exception in the above code before commit, it'll rollback
            DB::rollBack();
            return $e->getMessage();
        }
    }

    public function detalle_planilla($id_proc)
    {
    }
}