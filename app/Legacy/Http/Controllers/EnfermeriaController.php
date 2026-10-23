<?php

namespace Sis_medico\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Sis_medico\Agenda;
use Sis_medico\Bodega;
use Sis_medico\Ct_Configuraciones;
use Sis_medico\Equipo_Historia;
use Sis_medico\hc_procedimientos;
use Sis_medico\Historiaclinica;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Insumo_Plantilla;
use Sis_medico\Insumo_Plantilla_Control;
use Sis_medico\Insumo_Plantilla_Item_Control;
use Sis_medico\InvCabMovimientos;
use Sis_medico\InvCosto;
use Sis_medico\InvDetMovimientos;
use Sis_medico\InvDocumentosBodegas;
use Sis_medico\InvInventario;
use Sis_medico\InvInventarioSerie;
use Sis_medico\InvKardex;
use Sis_medico\InvTransaccionesBodegas;
use Sis_medico\Log_movimiento;
use Sis_medico\Movimiento;
use Sis_medico\Movimiento_Paciente;
use Sis_medico\Paciente;
use Sis_medico\Pedido;
use Sis_medico\Planilla;
use Sis_medico\Planilla_Detalle;
use Sis_medico\Planilla_Procedimiento;
use Sis_medico\Procedimiento;
use Sis_medico\Producto;
use Sis_medico\Tipo;
use Sis_medico\TransitoController;
use Sis_medico\User;
use Session;
use Sis_medico\Insumo_Plantilla_Tipo;
use Sis_medico\ModuloBodega;
use Sis_medico\Modulos;
use Sis_medico\Log_usuario;
use Sis_medico\hc_protocolo;
use Sis_medico\Procedimiento_Detalle_Honorario;
use Sis_medico\hc_imagenes_protocolo;
use Sis_medico\Paciente_Alergia;
use Sis_medico\hc_receta;
use Sis_medico\Hc_Epicrisis;
use Sis_medico\Http\Controllers\Insumos\PlantillaController;
use Sis_medico\Ct_Detalle_Venta_Omni;
use Sis_medico\grupo_procedimiento;
use Sis_medico\hc_evolucion;
use Sis_medico\Hc_Evolucion_Anestesiologo;
use Sis_medico\Hc_Procedimiento_Final;
use Sis_medico\Planilla_Cabecera;

class EnfermeriaController extends Controller
{
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->conf_fc = 0;
        if (Session::get('id_empresa') == '1391707460001') {
            $this->id_procedimiento_generico = 68; // poltoviejo
        } else {
            $this->id_procedimiento_generico = 8; //gye
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if (in_array($rolUsuario, array(1, 6, 7, 9, 11, 3)) == false) {
            return true;
        }
    }

    private function rol_anestesiologo()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if (in_array($rolUsuario, array(1, 9, 6, 11)) == false) {
            return true;
        }
    }

    public function index(Request $request)
    {
        //dd($request);
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $consultar = $request->all();
        $fecha     = $request['fecha'];
        $fechafin  = $request['fechafin'];

        //return $request;
        if ($fecha == 0) {
            $fecha_2 = date('Y-m-d');
            $fecha   = $fecha_2;
        } else {
            $fecha_2 = $fecha;
        }
        if ($fechafin == 0) {
            $fechafin1 = date('Y-m-d');
            $fechafin  = $fechafin1;
        } else {
            $fechafin1 = $fechafin;
        }

        //$agenda= Agenda::paginate(5);
        //dd($agenda);
        $nombres           = $request['nombres'];
        $cedula            = $request['cedula'];
        $consulta          = Agenda::where("agenda.estado_cita", "4")->where('agenda.proc_consul', 0)->whereBetween('agenda.fechaini', [$fecha_2 . ' 00:00:00', $fechafin1 . ' 23:59:59'])->join('paciente', 'agenda.id_paciente', '=', 'paciente.id');
        $consulta_hospital = Agenda::where("agenda.estado_cita", "4")->where('agenda.proc_consul', 4)->whereBetween('agenda.fechaini', [$fecha_2 . ' 00:00:00', $fechafin1 . ' 23:59:59'])->join('paciente', 'agenda.id_paciente', '=', 'paciente.id');
        $procedimientos    = Agenda::where("agenda.estado_cita", "4")->where('agenda.proc_consul', 1)->where('agenda.estado', 1)->whereBetween('agenda.fechaini', [$fecha_2 . ' 00:00:00', $fechafin1 . ' 23:59:59'])->join('paciente', 'agenda.id_paciente', '=', 'paciente.id');
        if (!is_null($cedula)) {
            $consulta->where('id_paciente', $cedula);
            $procedimientos->where('id_paciente', $cedula);
        }
        if ($nombres != null) {

            $nombres2 = explode(" ", $nombres);
            $cantidad = count($nombres2);

            $nombres_sql = '';

            foreach ($nombres2 as $n) {
                $nombres_sql = $nombres_sql . '%' . $n;
            }

            $nombres_sql = $nombres_sql . '%';

            if ($cantidad > 1) {
                $consulta = $consulta->where(function ($jq1) use ($nombres_sql) {
                    $jq1->orwhereraw('CONCAT(paciente.apellido1," ",paciente.apellido2," ",paciente.nombre1," ",paciente.nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(paciente.nombre1," ",paciente.nombre2," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(paciente.nombre1," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(paciente.apellido1," ",paciente.apellido2," ",paciente.nombre1) LIKE ?', [$nombres_sql]);
                });
                $consulta_hospital = $consulta_hospital->where(function ($jq1) use ($nombres_sql) {
                    $jq1->orwhereraw('CONCAT(paciente.apellido1," ",paciente.apellido2," ",paciente.nombre1," ",paciente.nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(paciente.nombre1," ",paciente.nombre2," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(paciente.nombre1," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(paciente.apellido1," ",paciente.apellido2," ",paciente.nombre1) LIKE ?', [$nombres_sql]);
                });
                $procedimientos = $procedimientos->where(function ($jq1) use ($nombres_sql) {
                    $jq1->orwhereraw('CONCAT(paciente.apellido1," ",paciente.apellido2," ",paciente.nombre1," ",paciente.nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(paciente.nombre1," ",paciente.nombre2," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(paciente.nombre1," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(paciente.apellido1," ",paciente.apellido2," ",paciente.nombre1) LIKE ?', [$nombres_sql]);
                });
            } else {

                $consulta = $consulta->where(function ($jq1) use ($nombres_sql) {
                    $jq1->orwhereraw('CONCAT(paciente.apellido1," ",paciente.apellido2," ",paciente.nombre1," ",paciente.nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(paciente.nombre1," ",paciente.nombre2," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', [$nombres_sql]);
                });

                $consulta_hospital = $consulta_hospital->where(function ($jq1) use ($nombres_sql) {
                    $jq1->orwhereraw('CONCAT(paciente.apellido1," ",paciente.apellido2," ",paciente.nombre1," ",paciente.nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(paciente.nombre1," ",paciente.nombre2," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', [$nombres_sql]);
                });
                $procedimientos = $procedimientos->where(function ($jq1) use ($nombres_sql) {
                    $jq1->orwhereraw('CONCAT(paciente.apellido1," ",paciente.apellido2," ",paciente.nombre1," ",paciente.nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(paciente.nombre1," ",paciente.nombre2," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', [$nombres_sql]);
                });
            }
        }

        $consulta          = $consulta->select('agenda.*')->paginate(30);
        $consulta_hospital = $consulta_hospital->select('agenda.*')->paginate(30);
        $procedimientos    = $procedimientos->select('agenda.*')->paginate(30);


        return view('enfermeria/index', ['consulta' => $consulta, 'fecha' => $fecha, 'fechafin' => $fechafin, 'procedimientos' => $procedimientos, 'request' => $request, 'consulta_hospital' => $consulta_hospital]);
    }

    public function procedimiento(Request $request, $id)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        //dd("hola");
        $nose   = Agenda::find($id);
        $agenda = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'paciente.id_seguro', '=', 'seguros.id')
            ->join('historiaclinica as h', 'h.id_agenda', 'agenda.id')
            ->join('seguros as s', 'h.id_seguro', '=', 's.id')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.id as id_pacente', 'paciente.apellido2 as papellido2', 'seguros.color as color', 'paciente.sexo', 'paciente.fecha_nacimiento', 'h.hcid', 's.nombre as hsnombre', 'paciente.gruposanguineo', 'h.presion', 'h.pulso', 'h.temperatura', 'h.altura', 'h.peso', 'h.hcid', 'h.o2')
            ->where('agenda.id', '=', $id)
            ->first();
        //dd($agenda);
        return view('enfermeria/procedimiento', ['agenda' => $agenda, 'nose' => $nose]);
    }

    public function guardar(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $input = [
            'gruposanguineo' => $request['gruposanguineo'],
            'presion'        => $request['presion'],
            'temperatura'    => $request['temperatura'],
            'altura'         => $request['altura'],
        ];
        $input2 = [
            'presion'     => $request['presion'],
            'pulso'       => $request['pulso'],
            'o2'          => $request['o2'],
            'temperatura' => $request['temperatura'],
            'altura'      => $request['estatura'],
            'peso'        => $request['peso'],
        ];

        paciente::find($request['id_paciente'])->update($input);
        Historiaclinica::find($request['hcid'])->update($input2);
        return redirect()->route('enfermeria.index');
    }

    public static function proveedorDetalle($serie)
    {
        $query = DB::select(DB::raw("
            select pr.*
            from inv_det_movimientos  idm
            INNER JOIN pedido p on idm.id_pedido = p.id
            INNER JOIN proveedor pr ON pr.id = p.id_proveedor 
            where idm.serie like '{$serie}' AND id_pedido is not NULL
            LIMIT 1 OFFSET 0
        "));
        return $query;
    }

    public function insumos($id_agenda)
    {
        // dd("EPA");
        // $exitencia = ceil((8 / 1)); dd($exitencia);
        $id_empresa = Session::get('id_empresa');

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $agenda  = Agenda::find($id_agenda);
        $bodegas = Bodega::all();

        // $bodega_empresa = Bodega::where('id_empresa', $id_empresa)->where('estado', 1)->where("mostrar", 1)->get();

        if (Auth::user()->id_tipo_usuario == 7 or Auth::user()->id_tipo_usuario == 1) {
            $bodega_empresa = Bodega::where('id_empresa', $id_empresa)->where('estado', 1)->get();
        } else {
            $bodega_empresa = Bodega::where('id_empresa', $id_empresa)->where('estado', 1)->where("mostrar", 1)->get();
        }

        if (Auth::user()->id == "0957258056") {
            // dd($bodega_empresa);
        }
        // dd($bodega_empresa);
        /*$procedimientos = hc_procedimientos::join('historiaclinica as hc', 'hc.hcid', '=', 'hc_procedimientos.id_hc')
            ->where('hc.id_agenda', $id_agenda)->where("hc_procedimientos.estado", 1)->get();*/
        $procedimientos = hc_procedimientos::join('historiaclinica as hc', 'hc.hcid', '=', 'hc_procedimientos.id_hc')
            ->where('hc.id_agenda', $id_agenda)->get(); /// DICE VICTOR QUE SE DEBEN MOSTRAR TODOS LOS AUNQUE NO ESTEN ACTIVOS YA QUE SON INACTIVADOS POR EL DOCTOR

        if ($procedimientos->count() > 0) {
            $hcid = $procedimientos->first()->hcid;
            ///return $hcid;
        } else {
            $hcid = "";
        }
        $equipos = Equipo_Historia::where('hcid', $hcid)->get();

        $cabecera = InvCabMovimientos::where('id_agenda', $id_agenda)->first();

        $planilla = Planilla::where('id_agenda', $agenda->id)->where('estado','1')->where('nota_entrega', 0)->first();

        $tipo_plantilla = Insumo_Plantilla_Tipo::get();
        // dd($tipo_plantilla);
        if (!is_null($planilla)) {
            if ($planilla->aprobado == 1) {
                return view('enfermeria/insumos_aprobados', ['procedimientos' => $procedimientos, 'agenda' => $agenda, 'equipos' => $equipos, 'hcid' => $hcid, 'bodegas' => $bodegas, 'cabecera' => $cabecera]);
            }
        }

        $id_usuario = Auth::user()->id;
        $modulo_bodega = ModuloBodega::where('estado', '1')->where('id_usuario', $id_usuario)->where('id_empresa', $id_empresa)->where('id_modulo', 1)->first();

        return view('enfermeria/insumos', ['procedimientos' => $procedimientos, 'agenda' => $agenda, 'equipos' => $equipos, 'hcid' => $hcid, 'bodegas' => $bodegas, 'cabecera' => $cabecera, 'bodega_empresa' => $bodega_empresa, 'tipo_plantilla' => $tipo_plantilla, 'modulo_bodega' => $modulo_bodega]);
    }
    public function obtener_plantilla(Request $request)
    {
        if ($request->opcion != null) {
            $insumo = Planilla_Procedimiento::where('id_procedimiento', $request->opcion)->get();
            $k      = [];
            foreach ($insumo as $p) {
                $p->id_planilla;
                $planilla = Insumo_Plantilla_Control::find($p);
                array_push($k, $planilla);
            }
            return response()->json($k);
        }
        return 'error';
    }
    public function guardar_observacion(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $hc = Historiaclinica::find($request->id_historia2);

        if (!is_null($hc)) {
            $hc->update(['observaciones_enfermeria' => $request->observaciones_enfermeria]);
        }

        return "ok";
    }

    public function __eliminar_insumo($id)
    {
        dd($id);
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        # ELIMINAR DETALLE DEL MOVIMIENTO
        $detalle = InvDetMovimientos::find($id);
        if (isset($detalle->id)) {
            $detalle->estado = 0;
            $detalle->save();
            # ELIMINAR KARDEX
            $kardex = InvKardex::where('id_inv_det_movimientos', $id)
                ->where('estado', 1)
                ->first();
            if (isset($kardex->id)) {
                $kardex->estado = 0;
                $kardex->save();
            }
            # INCREMENTAR INVENTARIO SERIE
            InvInventarioSerie::incrementarInventarioSerie($detalle->serie, $detalle->cabecera->id_bodega_origen, 1);

            return 'ok';
        } else {
            return 'no';
        }
    }

    public function eliminar_insumo($id)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $movimiento_paciente = Movimiento_Paciente::find($id);
        $ip_cliente          = $_SERVER["REMOTE_ADDR"];
        $idusuario           = Auth::user()->id;
        if (!is_null($movimiento_paciente)) {
            $producto     = Producto::find($movimiento_paciente->movimiento->id_producto);
            $usos_inicial = $producto->usos;
            $uso_final    = $movimiento_paciente->movimiento->usos + 1;
            $cantidad     = $movimiento_paciente->movimiento->cantidad;
            if ($uso_final > $usos_inicial) {
                $uso_final = 1;
                $cantidad  = $cantidad + 1;

                $producto          = Producto::find($movimiento_paciente->movimiento->id_producto);
                $cantidad_producto = $producto->cantidad + 1;
                $input2            = [
                    'cantidad'        => $cantidad_producto,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod'   => $idusuario,
                ];

                $producto->update($input2);
            } elseif ($usos_inicial == 1) {
                $uso_final = 1;
                $cantidad  = $cantidad + 1;

                $producto          = Producto::find($movimiento_paciente->movimiento->id_producto);
                $cantidad_producto = $producto->cantidad + 1;
                $input2            = [
                    'cantidad'        => $cantidad_producto,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod'   => $idusuario,
                ];

                $producto->update($input2);
            }

            $movimiento       = movimiento::find($movimiento_paciente->id_movimiento);
            $input_movimiento = [
                'cantidad'        => $cantidad,
                'usos'            => $uso_final,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $idusuario,
                'tipo'            => '2',
            ];
            $movimiento->update($input_movimiento);
            Log_movimiento::create([
                'id_producto'     => $movimiento_paciente->movimiento->id_producto,
                'id_encargado'    => $idusuario,
                'id_movimiento'   => $movimiento_paciente->id_movimiento,
                'observacion'     => "Producto devuelto del paciente por: " . Auth::user()->nombre1 . ' ' . Auth::user()->apellido1,
                'tipo'            => '2',
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
            ]);

            $detalle_planilla = Planilla_Detalle::where('id_movimiento_paciente', $movimiento_paciente->id)
                ->where('estado', 1)
                ->first();
            if (isset($detalle_planilla->id)) {
                $detalle_planilla->estado = 0;
                $detalle_planilla->save();
            }

            $inv_det_mov = InvDetMovimientos::where('id_movimiento_paciente', $movimiento_paciente->id)->first();
            if (isset($inv_det_mov->id)) {
                $inv_kardex = InvKardex::where('id_inv_det_movimientos', $inv_det_mov->id)->first();
                if (isset($inv_kardex->id)) {
                    $inv_kardex->id_inv_det_movimientos = null;
                    $inv_kardex->estado                 = 0;
                    $inv_kardex->deleted_at             = date('Y-m-d H:i:s');
                    $inv_kardex->save();
                }
                if (isset($inv_det_mov->id)) {
                    $inv_det_mov->delete();
                }
            }
            //     if (Auth::user()->id=='0924383631') {
            //     dd($inv_kardex);
            // }

            $movimiento_paciente->delete();
            return "ok";
        }

        return 'no';
    }

    public function mover_insumo($id, $id_hc_procedimiento)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $movimiento_paciente = Movimiento_Paciente::find($id);
        $ip_cliente          = $_SERVER["REMOTE_ADDR"];
        $idusuario           = Auth::user()->id;
        if (isset($movimiento_paciente->id)) {
            $movimiento_paciente->id_hc_procedimientos = $id_hc_procedimiento;
            $movimiento_paciente->ip_modificacion     = $ip_cliente;
            $movimiento_paciente->id_usuariomod       = $idusuario;
            $movimiento_paciente->save();

            // MOVIMIENTO EN LA PLANILLA
            $detalle = Planilla_Detalle::where('id_movimiento_paciente', $movimiento_paciente->id)->first();
            if (isset($detalle->id)) {
                $cabe_cambio = Planilla::where('id_hc_procedimiento', $id_hc_procedimiento)->where('nota_entrega', 0)->first();
                $detalle->id_planilla_cabecera = $cabe_cambio->id;
                $detalle->save();
            }
            return 'ok';
        } else {
            return 'error';
        }
    }

    public function index_insumos(Request $request)
    {
        // dd($request->all());
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $consultar = $request->all();
        $fecha     = $request['fecha'];
        $fechafin  = $request['fechafin'];

        //return $request;
        if ($fecha == 0) {
            $fecha_2 = date('Y-m-d');
            $fecha   = $fecha_2;
        } else {
            $fecha_2 = $fecha;
        }
        if ($fechafin == 0) {
            $fechafin1 = date('Y-m-d');
            $fechafin  = $fechafin1;
        } else {
            $fechafin1 = $fechafin;
        }

        //$agenda= Agenda::paginate(5);
        //dd($agenda);
        $nombres        = $request['nombres'];
        $cedula         = $request['cedula'];
        $consulta       = Agenda::where("agenda.estado_cita", "4")
            ->where('agenda.proc_consul', 0)
            ->whereBetween('agenda.fechaini', [$fecha_2 . ' 00:00:00', $fechafin1 . ' 23:59:59'])
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id');
        $procedimientos = Agenda::where("agenda.estado_cita", "4")->where('agenda.proc_consul', 1)->whereBetween('agenda.fechaini', [$fecha_2 . ' 00:00:00', $fechafin1 . ' 23:59:59'])->join('paciente', 'agenda.id_paciente', '=', 'paciente.id');
        if (!is_null($cedula)) {
            $consulta->where('id_paciente', $cedula);
            $procedimientos->where('id_paciente', $cedula);
        }

        if (!is_null($request->tipo)) {
            $procedimientos->where('id_procedimiento', $request->tipo);
        }


        if ($nombres != null) {

            $nombres2 = explode(" ", $nombres);
            $cantidad = count($nombres2);

            $nombres_sql = '';


            foreach ($nombres2 as $n) {
                $nombres_sql = $nombres_sql . '%' . $n;
            }

            $nombres_sql = $nombres_sql . '%';

            if ($cantidad > 1) {
                $consulta = $consulta->where(function ($jq1) use ($nombres_sql) {
                    $jq1->orwhereraw('CONCAT(paciente.apellido1," ",paciente.apellido2," ",paciente.nombre1," ",paciente.nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(paciente.nombre1," ",paciente.nombre2," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(paciente.nombre1," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(paciente.apellido1," ",paciente.apellido2," ",paciente.nombre1) LIKE ?', [$nombres_sql]);
                });
                $procedimientos = $procedimientos->where(function ($jq1) use ($nombres_sql) {
                    $jq1->orwhereraw('CONCAT(paciente.apellido1," ",paciente.apellido2," ",paciente.nombre1," ",paciente.nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(paciente.nombre1," ",paciente.nombre2," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(paciente.nombre1," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(paciente.apellido1," ",paciente.apellido2," ",paciente.nombre1) LIKE ?', [$nombres_sql]);
                });
            } else {

                $consulta = $consulta->where(function ($jq1) use ($nombres_sql) {
                    $jq1->orwhereraw('CONCAT(paciente.apellido1," ",paciente.apellido2," ",paciente.nombre1," ",paciente.nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(paciente.nombre1," ",paciente.nombre2," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', [$nombres_sql]);
                });
                $procedimientos = $procedimientos->where(function ($jq1) use ($nombres_sql) {
                    $jq1->orwhereraw('CONCAT(paciente.apellido1," ",paciente.apellido2," ",paciente.nombre1," ",paciente.nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(paciente.nombre1," ",paciente.nombre2," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', [$nombres_sql]);
                });
            }
        }



        $consulta       = $consulta->select('agenda.*')->paginate(30);


        $procedimientos = $procedimientos->select('agenda.*')->paginate(30);
        //dd($consulta);  


        $all_pro = agenda::join('procedimiento as p', 'agenda.id_procedimiento', 'p.id')
            ->join('grupo_procedimiento as gp', 'p.id_grupo_procedimiento', 'gp.id')
            ->whereIn('gp.tipo_procedimiento', ['0', '1'])->select('p.id as id_procedimiento', 'p.nombre', 'gp.id', 'gp.tipo_procedimiento')
            ->groupBy('p.id')->get();

        //dd($all_pro);

        return view('enfermeria/index_insumos', ['all_pro' => $all_pro, 'consulta' => $consulta, 'fecha' => $fecha, 'fechafin' => $fechafin, 'procedimientos' => $procedimientos, 'request' => $request]);
    }
    public function insumos_uso($id_agenda)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $tipos  = Tipo::all();
        $agenda = Agenda::find($id_agenda);

        $procedimientos = hc_procedimientos::join('historiaclinica as hc', 'hc.hcid', '=', 'hc_procedimientos.id_hc')
            ->where('hc.id_agenda', $id_agenda)->get();

        if ($procedimientos->count() > 0) {
            $hcid = $procedimientos->first()->hcid;
            ///return $hcid;
        } else {
            $hcid = "";
        }
        $equipos = Equipo_Historia::where('hcid', $hcid)->get();
        //dd($agenda);
        //return $equipos;

        return view('enfermeria/insumos_uso', ['procedimientos' => $procedimientos, 'agenda' => $agenda, 'equipos' => $equipos, 'hcid' => $hcid, 'tipos' => $tipos]);
    }
    public function selec_prod($id)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $tipos = Tipo::all();
        $id_empresa = Session::get('id_empresa');
        $procedimientos = hc_procedimientos::join('historiaclinica as hc', 'hc.hcid', '=', 'hc_procedimientos.id_hc')
            ->where('hc_procedimientos.id', $id)->get();

        //$procedimientos = hc_procedimientos::join('historiaclinica as hc', 'hc.hcid', '=', 'hc_procedimientos.id_hc')
        //   ->where('hc.id_agenda', '20773')->get();

        //20771
        //dd($procedimientos);

        if ($procedimientos->count() > 0) {
            $agenda = Agenda::find($procedimientos[0]->id_agenda);
            $hcid   = $procedimientos->first()->hcid;
            ///return $hcid;
        } else {
            $agenda = null;
            $hcid   = "";
        }
        $equipos = Equipo_Historia::where('hcid', $hcid)->get();
        if (!is_null($agenda)) {
            $planilla = Planilla::where("id_agenda", $agenda->id)->where('nota_entrega', 0)->first();
            if (!is_null($planilla)) {
                if ($planilla->aprobado == 1) {
                    return view('enfermeria/selec_prod2', ['procedimientos' => $procedimientos, 'agenda' => $agenda, 'equipos' => $equipos, 'hcid' => $hcid, 'tipos' => $tipos, 'id_proc' => $id]);
                }
            }
        }

        //dd($agenda);
        //return $equipos;

        // $bodegas = Bodega::where('id_empresa', $id_empresa)->where('estado', 1)->get();
        if (Auth::user()->id_tipo_usuario == 7 or Auth::user()->id_tipo_usuario == 1) {
            $bodegas = Bodega::where('id_empresa', $id_empresa)->where('estado', 1)->get();
        } else {
            $bodegas = Bodega::where('id_empresa', $id_empresa)->where('estado', 1)->where("mostrar", 1)->get();
        }

        $id_usuario = Auth::user()->id;
        $modulo_bodega = ModuloBodega::where('estado', '1')->where('id_usuario', $id_usuario)->where('id_empresa', $id_empresa)->where('id_modulo', 2)->first();
        //dd($modulo_bodega, $modulo);
        return view('enfermeria/selec_prod', ['procedimientos' => $procedimientos, 'agenda' => $agenda, 'equipos' => $equipos, 'hcid' => $hcid, 'tipos' => $tipos, 'id_proc' => $id, 'bodegas' => $bodegas, 'modulo_bodega' => $modulo_bodega, 'id_usuario' => $id_usuario]);
    }

    public function productos($id_producto, $id_procedimiento)
    {
        $productos     = Producto::where('codigo_siempre', '1')->where('tipo_producto', $id_producto)->get();
        $procedimiento = hc_procedimientos::find($id_procedimiento);
        return view('enfermeria/productos', ['productos' => $productos, 'procedimiento' => $id_procedimiento]);
    }

    public function __serie_enfermeroget($serie, $id_hc_procedimientos)
    {
        # 1. POR NUMERO DE SERIE
        // VERIFICO LA EXISTENCIA DEL INSUMO CONSIDERANDO LOS NUMEROS DE USOS TAMB
        $inv_serie = InvInventarioSerie::where('serie', $serie)->first();
        if (iseet($inv_serie->id) and $inv_serie->inventario->producto->usos == 0) {
            if ($inv_serie->fecha_vence >= date('Y-m-d') || $this->conf_fc == 0) {
                # GENERO EL DESCARGO
                $id = $this->documentoEgreso($inv_serie, $id_hc_procedimientos, 1, 1);
                return $id;
            } else {
                return "caducado";
            }
        } else {
            // VERIFICO EL SI TIENE EXISTENCIA EN USOS
            if (iseet($inv_serie->id) and $inv_serie->existencia_usos > 0) {
                if ($inv_serie->fecha_vence >= date('Y-m-d') || $this->conf_fc == 0) {
                    $id = $this->documentoEgreso($inv_serie, $id_hc_procedimientos, 1, 1);
                } else {
                    return "caducado";
                }
            } else {
                return "Existencia inconsistente";
            }
        }
        # 2. POR CODIGO DEL PRODUCTO
        // SI NO ENCUENTRA DATOS POR NUMERO DE SERIE VERIFICA POR CODIGO DEL PRODUCTO  Y BODEGA PARA OBTENER EL INVENTARIO
        $producto  = Producto::where('codigo', $serie)->fisrt();
        $id_bodega = env('BODEGA_EGR_PACI1', 2);
        if (isset($producto->id)) {
            #inventario
            $inventario = InvInventario::getInventario($producto->id, $id_bodega);
            if (isset($inventario->id)) {
                #inventario serie
                $inv_serie = InvInventarioSerie::where('id_inv_inventario', $inventario->id)
                    ->where('id_inv_inventario', '>', 0);
                if ($this->conf_fc != 0) {
                    $inv_serie = $inv_serie->where('fecha_vence', '>=', date('Y-m-d'));
                }
                $inv_serie = $inv_serie->orderBy('id', desc)
                    ->first();
                if (isset($inv_serie->id)) {
                    $id = $this->documentoEgreso($inv_serie, $id_hc_procedimientos, 1, 1);
                }
            }
        }
    }

    public function documentoEgreso($inv_serie, $id_hc_procedimientos, $cantidad, $cantidad_uso)
    {
        $id_bodega   = env('BODEGA_EGR_PACI1', 2); // bodega de descargo de medicina pentax
        $ip_cliente  = $_SERVER["REMOTE_ADDR"];
        $idusuario   = Auth::user()->id;
        $cab_mov_inv = InvCabMovimientos::where('id_hc_procedimiento', $id_hc_procedimiento)->first();
        if (!isset($cab_mov_inv->id)) {
            # creo la cabecera del traslado #
            $documento = invDocumentosBodegas::where('abreviatura_documento', 'EGP')->first();
            $secuencia = InvDocumentosBodegas::getSecueciaTipoDocum($value->id_bodega, 'EGP');
            if ($secuencia != 0) {
                $transaccion = InvTransaccionesBodegas::where('id_documento_bodega', $documento->id)
                    ->where('id_bodega', $value->id_bodega)
                    ->first();
                $cab_mov_inv                        = new InvCabMovimientos;
                $cab_mov_inv->id_documento_bodega   = $documento->id;
                $cab_mov_inv->id_transaccion_bodega = $transaccion->id;
                $cab_mov_inv->id_bodega_origen      = $id_bodega;
                $cab_mov_inv->numero_documento      = str_pad($secuencia, 9, "0", STR_PAD_LEFT);
                $cab_mov_inv->observacion           = $documento->abreviatura_documento . " " . strtoupper($documento->documento) . " PROCEDIMIENTO: " . $id_hc_procedimientos;
                $cab_mov_inv->fecha                 = date('Y-m-d');
                /* $cab_mov_inv->descuento             = $request['descuentx'];
                $cab_mov_inv->subtotal              = $request['subtotal_12'];
                $cab_mov_inv->subtotal_0            = $request['subtotal_0'];
                $cab_mov_inv->iva                   = $request['iva'];
                $cab_mov_inv->total                 = $request['total'];*/
                $cab_mov_inv->id_hc_procedimiento = $id_hc_procedimiento;
                $cab_mov_inv->ip_creacion         = $ip_cliente;
                $cab_mov_inv->ip_modificacion     = $ip_cliente;
                $cab_mov_inv->id_usuariocrea      = $idusuario;
                $cab_mov_inv->id_usuariomod       = $idusuario;
                $cab_mov_inv->save();
            }
        }
        # I V A
        $iva = 0;
        if (isset($inv_serie->inventario->producto->iva) && $inv_serie->inventario->producto->iva == 1) {
            $conf = Ct_Configuraciones::find(3);
            $iva  = ($inv_serie->inventario->costo_promedio) * $conf->iva;
        }
        # DETALLES
        $det_mov_inv                         = new InvDetMovimientos;
        $det_mov_inv->id_inv_cab_movimientos = $cab_mov_inv->id;
        $det_mov_inv->id_producto            = $inv_serie->inventario->producto->id;
        $det_mov_inv->id_inv_inventario      = $inv_serie->id_inv_inventario;
        $det_mov_inv->cantidad               = $cantidad;
        $det_mov_inv->cant_uso               = $cantidad_uso;
        $det_mov_inv->serie                  = $inv_serie->serie;
        $det_mov_inv->lote                   = $inv_serie->lote;
        $det_mov_inv->fecha_vence            = $inv_serie->fecha_vence;
        $det_mov_inv->valor_unitario         = $inv_serie->inventario->costo_promedio;
        $det_mov_inv->subtotal               = $inv_serie->inventario->costo_promedio;
        $det_mov_inv->descuento              = 0;
        $det_mov_inv->iva                    = $iva;
        $det_mov_inv->total                  = $inv_serie->inventario->costo_promedio + $iva;
        $det_mov_inv->motivo                 = $cab_mov_inv->observacion;
        $det_mov_inv->ip_creacion            = $ip_cliente;
        $det_mov_inv->ip_modificacion        = $ip_cliente;
        $det_mov_inv->id_usuariocrea         = $idusuario;
        $det_mov_inv->id_usuariomod          = $idusuario;
        $det_mov_inv->save();

        // CALCULAR TOTALES
        InvCabMovimientos::calcularTotalCabMovimiento($cab_mov_inv->id);
        // MOVIMIENTO EN KARDEX
        $kardex = InvKardex::setKardex($cab_mov_inv->id);
        return $cab_mov_inv->id;
    }


    public function serie_enfermeroget($nombre, $id_hc_procedimientos, Request $request)
    {
        //copia de TransitoController funcion serie_enfermero si se realizan cambios hacerlo en las 2 funciones
        $data = null;

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $id_empresa = Session::get('id_empresa');

        $producto = Producto::where('codigo', $nombre)->first();
        //dd($producto,env('BODEGA_EGR_PACI1',2));
        $id_bodega = $request->id_bodega;

        $inv_serie_ku = InvInventarioSerie::where('id_producto', $producto->id)
            ->where('id_bodega', $id_bodega)
            ->where('id_empresa', $id_empresa)
            ->where('estado', '!=', 0);

        $inv_serie_ku = $inv_serie_ku->orderBy('id', 'DESC')->first();

        if (!is_null($inv_serie_ku)) {
            $this->corrige_existencias($inv_serie_ku->serie, $id_bodega);
        }

        if (is_null($inv_serie_ku)) {

            return "NO EXISTE EN INVENTARIO SERIE";
        }

        //EL FAMOSO METODO FIFO
        $inv_serie = InvInventarioSerie::where('id_producto', $producto->id)
            // ->where('id_bodega', env('BODEGA_EGR_PACI1', 2))
            ->where('id_bodega', $id_bodega)
            ->where('existencia', '!=', 0)
            ->where('estado', '!=', 0)
            ->where('id_empresa', $id_empresa)
            ->first();

        // if($idusuario == "0924383631"){
        //     dd($inv_serie);
        // }
        //dd($inv_serie);
        if (is_null($inv_serie)) {

            return "NO EXISTE EN INVENTARIO SERIE";
        }

        //$producto = $inv_serie->producto;
        $codigo            = $producto->codigo;
        $lote              = $inv_serie->lote;
        $fecha_vencimiento = $inv_serie->fecha_vence;
        $observacion       = 'DESCARGO DE INSUMO ANESTESIOLOGO';
        $serie             = $inv_serie->serie;
        $precio            = 0;

        // $invcosto = InvCosto::where('id_producto', $producto->id)->first();

        $inv_costo_promedio = InvInventario::where('id_bodega', $id_bodega)->where('id_producto', $producto->id)->where("id_empresa", $id_empresa)->first();

        //dd($invcosto);
        // if (!is_null($invcosto)) {
        //     $precio = $invcosto->costo_promedio;
        // }

        if (!is_null($inv_costo_promedio)) {
            $precio = $inv_costo_promedio->costo_promedio;
        }
        //PLANILLA INGRESADO POR VH
        $hc_procedimiento = hc_procedimientos::find($id_hc_procedimientos);
        $historia         = $hc_procedimiento->historia;
        $id_agenda        = $historia->id_agenda;
        $paciente         = $historia->paciente;

        $vh_procedimiento = null;
        foreach ($hc_procedimiento->hc_procedimiento_f as $px) {
            if ($px->procedimiento->id_grupo_procedimiento != null) {
                $vh_procedimiento = $px->procedimiento->id; //dd($vh_procedimiento);
                break;
            }
        }
        //AQUI ANDO
        $cabecera  = null; //dd($vh_procedimiento);
        $idusuario = Auth::user()->id;

        if ($vh_procedimiento != null) {
            $planilla_procedimiento = Planilla_Procedimiento::where('id_procedimiento', $vh_procedimiento)->first();
            //dd($planilla_procedimiento);
            if (!is_null($planilla_procedimiento)) {
                $id_plantilla = $planilla_procedimiento->id_planilla;
                $la_planilla  = Planilla::where('id_hc_procedimiento', $id_hc_procedimientos)->where('estado', 1)->where('nota_entrega', 0)->first();
                if (is_null($la_planilla)) {
                    $a_proc = [
                        'fecha'               => date('Y-m-d H:i:s'),
                        'id_planilla'         => $id_plantilla,
                        'id_agenda'           => $id_agenda,
                        'id_movimiento'       => null,
                        'id_hc_procedimiento' => $id_hc_procedimientos,
                        'id_usuariocrea'      => $idusuario,
                        'id_usuariomod'       => $idusuario,
                        'ip_creacion'         => $ip_cliente,
                        'ip_modificacion'     => $ip_cliente,
                        //'codigo' => ,
                        'estado'              => '1',
                        'observacion'         => 'Paciente: ' . $paciente->apellido1 . ' ' . $paciente->apellido2 . ' ' . $paciente->nombre1 . ' ' . $paciente->nombre2,
                    ];
                    $cabecera = Planilla::insertGetId($a_proc);
                } else {
                    $a_proc = [
                        'id_usuariomod'   => $idusuario,
                        'ip_modificacion' => $ip_cliente,
                    ];
                    $la_planilla->update($a_proc);
                    $cabecera = $la_planilla->id;
                }
            } else {
                return "EL PROCEDIMIENTO $vh_procedimiento NO TIENE PLANTILLA ";
            }
        } else {
            return "NO TIENE PROCEDIMIENTO PRINCIPAL";
        }
        //
        if ($cabecera == null) {
            return "NO EXISTE LA PLANILLA";
        }
        //dd($cabecera);

        $tipo_plantilla = null;
        //dd($id_plantilla,$id_producto);
        $ins_plantilla_item_control = Insumo_Plantilla_Item_Control::where('id_plantilla', $id_plantilla)->where('id_producto', $producto->id)->first();

        if (!is_null($ins_plantilla_item_control)) {

            $tipo_plantilla = $ins_plantilla_item_control->tipo_plantilla;
        }

        //dd($tipo_plantilla);
        //dd($inv_serie);
        $var_request = [
            'codigo'               => $nombre,
            'id_hc_procedimientos' => $id_hc_procedimientos,
            'serie'                => $inv_serie->serie,
            'id_bodega'            => $id_bodega,
            'id_empresa' => $id_empresa,
        ];
        //dd($var_request);
        if (isset($inv_serie->id) and $inv_serie->inventario->producto->usos == 0) {
            //dd("1");
            if ($inv_serie->fecha_vence >= date('Y-m-d') || $this->conf_fc == 0) {
                # GENERO EL DESCARGO

                $id_movimiento_paciente = $this->_serie_enfermero($var_request);
                // INCREMENTA EL COMPROMETIDO EN INVENTARIO //
                if (isset($inv_serie->id)) {
                    InvInventarioSerie::comprometer($inv_serie, 1);
                }
                // $this->documentoTrasladoCompra($inv_serie,$id_agenda,$id_hc_procedimientos,1,1,$id_movimiento_paciente);
                // $id = $this->documentoEgreso($inv_serie,$id_agenda,$id_hc_procedimientos,1,1,$id_movimiento_paciente);
                $vh_movimiento_pac = Movimiento_Paciente::find($id_movimiento_paciente);
                $a_detalle         = [
                    'codigo'                 => $codigo,
                    'id_planilla_cabecera'   => $cabecera,
                    //'procedimiento' => ,
                    'precio'                 => $precio,
                    'check'                  => '1',
                    'estado'                 => '1',
                    'id_usuariocrea'         => $idusuario,
                    'id_usuariomod'          => $idusuario,
                    'ip_creacion'            => $ip_cliente,
                    'ip_modificacion'        => $ip_cliente,
                    'movimiento'             => $vh_movimiento_pac->id_movimiento,
                    'cantidad'               => 1,
                    'serie'                  => $serie,
                    'lote'                   => $lote,
                    'fecha_vencimiento'      => $fecha_vencimiento,
                    'observacion'            => $observacion,
                    'tipo_plantilla'         => $tipo_plantilla,
                    'id_movimiento_paciente' => $id_movimiento_paciente,
                    'id_bodega'              => $id_bodega

                ];

                $detalle = Planilla_Detalle::insertGetId($a_detalle);
                return "ok";
            } else {
                return "caducado";
            }
        } elseif (isset($inv_serie->id) and $inv_serie->existencia_uso > 0) {
            // VERIFICO EL SI TIENE EXISTENCIA EN USOS
            //dd("2");
            if ($inv_serie->fecha_vence >= date('Y-m-d') || $this->conf_fc == 0) {

                $id_movimiento_paciente = $this->_serie_enfermero($var_request); //dd($id_movimiento_paciente);
                // INCREMENTA EL COMPROMETIDO EN INVENTARIO //
                if (isset($inv_serie->id)) {
                    InvInventarioSerie::comprometer($inv_serie, 1);
                }
                // $this->documentoTrasladoCompra($inv_serie, $id_agenda,$id_hc_procedimientos,1,1,$id_movimiento_paciente);
                // $id = $this->documentoEgreso($inv_serie, $id_agenda,$id_hc_procedimientos,1,1,$id_movimiento_paciente);
                $vh_movimiento_pac = Movimiento_Paciente::find($id_movimiento_paciente);
                if (!is_null($vh_movimiento_pac)) {
                    $a_detalle = [
                        'codigo'                 => $codigo,
                        'id_planilla_cabecera'   => $cabecera,
                        //'procedimiento' => ,
                        'precio'                 => $precio,
                        'check'                  => '1',
                        'estado'                 => '1',
                        'id_usuariocrea'         => $idusuario,
                        'id_usuariomod'          => $idusuario,
                        'ip_creacion'            => $ip_cliente,
                        'ip_modificacion'        => $ip_cliente,
                        'movimiento'             => $vh_movimiento_pac->id_movimiento,
                        'cantidad'               => '1',
                        'serie'                  => $serie,
                        'lote'                   => $lote,
                        'fecha_vencimiento'      => $fecha_vencimiento,
                        'observacion'            => $observacion,
                        'tipo_plantilla'         => $tipo_plantilla,
                        'id_movimiento_paciente' => $id_movimiento_paciente,
                        'id_bodega'              => $id_bodega

                    ];

                    $detalle = Planilla_Detalle::insertGetId($a_detalle);
                }

                return "ok";
            } else {
                //dd("no entra",$inv_serie);
                return "caducado";
            }
        }
        //dd("3");
        # 2. POR CODIGO DEL PRODUCTO
        // SI NO ENCUENTRA DATOS POR NUMERO DE SERIE VERIFICA POR CODIGO DEL PRODUCTO  Y BODEGA PARA OBTENER EL INVENTARIO
        $producto  = Producto::where('codigo', $serie)->first();
        //$id_bodega = env('BODEGA_EGR_PACI1', 2);
        $id_bodega = $request->id_bodega;
        if (isset($producto->id)) {
            #inventario
            $inventario = InvInventario::getInventario($producto->id, $id_bodega);
            if (isset($inventario->id)) {
                #inventario serie
                $inv_serie = InvInventarioSerie::where('id_inv_inventario', $inventario->id)
                    ->where('existencia', '!=', 0)
                    ->where('id_empresa', $id_empresa)
                    ->where('estado', '!=', 0);
                if ($this->conf_fc != 0) {
                    $inv_serie = $inv_serie->where('fecha_vence', '>=', date('Y-m-d'));
                }
                $inv_serie = $inv_serie->orderBy('id', 'DESC')
                    ->first();
                if (isset($inv_serie->id)) {
                    $id_movimiento_paciente = $this->_serie_enfermero($var_request);
                    // INCREMENTA EL COMPROMETIDO EN INVENTARIO //
                    if (isset($inv_serie->id)) {
                        InvInventarioSerie::comprometer($inv_serie, 1);
                    }
                    // $this->documentoTrasladoCompra($inv_serie,$id_agenda,$id_hc_procedimientos,1,1,$id_movimiento_paciente);
                    // $id = $this->documentoEgreso($inv_serie,$id_agenda,$id_hc_procedimientos,1,1,$id_movimiento_paciente);
                    $vh_movimiento_pac = Movimiento_Paciente::find($id_movimiento_paciente);
                    $a_detalle         = [
                        'codigo'                 => $codigo,
                        'id_planilla_cabecera'   => $cabecera,
                        //'procedimiento' => ,
                        'precio'                 => $precio,
                        'check'                  => '1',
                        'estado'                 => '1',
                        'id_usuariocrea'         => $idusuario,
                        'id_usuariomod'          => $idusuario,
                        'ip_creacion'            => $ip_cliente,
                        'ip_modificacion'        => $ip_cliente,
                        'movimiento'             => $vh_movimiento_pac->id_movimiento,
                        'cantidad'               => $item_cant,
                        'serie'                  => $serie,
                        'lote'                   => $lote,
                        'fecha_vencimiento'      => $fecha_vencimiento,
                        'observacion'            => $observacion,
                        'tipo_plantilla'         => $tipo_plantilla,
                        'id_movimiento_paciente' => $id_movimiento_paciente,
                        'id_bodega'              => $id_bodega

                    ];

                    $detalle = Planilla_Detalle::insertGetId($a_detalle);
                    return "ok";
                }
            }
        } else {
            return "inconsistencia";
        }
    }

    public function _serie_enfermero($request)
    {
        $nombre               = $request['codigo'];
        $id_empresa = $request['id_empresa'];
        $id_bodega = $request['id_bodega'];
        $data                 = null;
        $id_hc_procedimientos = $request['id_hc_procedimientos'];
        $ip_cliente           = $_SERVER["REMOTE_ADDR"];
        $idusuario            = Auth::user()->id;
        $producto = Producto::where('codigo', $nombre)->first();

        $inv_serie = InvInventarioSerie::where('id_producto', $producto->id)
            // ->where('id_bodega', env('BODEGA_EGR_PACI1', 2))
            ->where('id_bodega', $id_bodega)
            ->where('id_empresa', $id_empresa)
            ->where('existencia', '!=', 0)
            ->where('estado', '!=', 0)
            ->first();
        if (isset($inv_serie->producto)) {
            $producto = $inv_serie->producto;
        }
        if (!isset($producto->id)) {
            $producto   = Producto::where('codigo', $nombre)->first();
            // $movimiento = Movimiento::where('id_producto', $producto->id)->first();
            $movimiento = Movimiento::where('serie', $inv_serie->serie)->first();
        } else {
            $movimiento = Movimiento::where('serie', $inv_serie->serie)->first();
        }

        if (isset($producto->id) and isset($movimiento->id)) {
            $mov_pac                       = new Movimiento_Paciente;
            $mov_pac->id_movimiento        = $movimiento->id;
            $mov_pac->id_hc_procedimientos = $id_hc_procedimientos;
            $mov_pac->id_usuariocrea       = $idusuario;
            $mov_pac->id_usuariomod        = $idusuario;
            $mov_pac->ip_modificacion      = $ip_cliente;
            $mov_pac->ip_creacion          = $ip_cliente;
            $mov_pac->id_bodega            = $id_bodega;
            $mov_pac->save();

            $log                  = new Log_movimiento;
            $log->id_producto     = $producto->id;
            $log->id_encargado    = $idusuario;
            $log->id_movimiento   = $movimiento->id;
            $log->observacion     = "Producto entregado a paciente";
            $log->tipo            = 0;
            $log->id_usuariocrea  = $idusuario;
            $log->id_usuariomod   = $idusuario;
            $log->ip_modificacion = $ip_cliente;
            $log->ip_creacion     = $ip_cliente;
            $log->save();
            return $mov_pac->id;
        } else {
            return 0;
        }
    }

    public function ____serie_enfermero($request)
    {
        //dd($request);

        # egreso insumos
        //copia en EnfermeriaController
        $nombre               = $request['codigo'];
        $data                 = null;
        $id_hc_procedimientos = $request['id_hc_procedimientos'];
        $ip_cliente           = $_SERVER["REMOTE_ADDR"];
        $idusuario            = Auth::user()->id;
        //return $query;
        $producto = Movimiento::where('serie', $nombre)->where('usos', '>=', 1)->where('cantidad', '>=', 0)->where('tipo', '=', 2)->first();
        //dd($producto);
        $producto_2 = Movimiento::where('serie', $nombre)->where('usos', '>=', 1)->where('cantidad', '>=', 0)->where('tipo', '=', 1)->first();
        $producto_3 = Movimiento::join('producto', 'movimiento.id_producto', '=', 'producto.id')
            ->where('producto.codigo_siempre', 1)
            ->where('producto.codigo', $nombre)
            //->where('movimiento.usos', '>=', 1)
            //->where('movimiento.fecha_vencimiento', '>=', date('Y-m-d'))
            //->where('movimiento.cantidad', '>=', 1)
            //->where('movimiento.tipo', '=', 1)
            ->select('movimiento.*')->first();

        //dd($producto, $producto_2, $producto_3);
        /*///////////////////////////////////////////////////////////////////
        // dd($request);
        $serie = InvInventarioSerie::where('serie',$request['codigo'])->first();
        dd($serie);
        $producto = $serie->producto;
        $input_movimiento_paciente = [
        'id_movimiento'        => $producto->id,
        'id_hc_procedimientos' => $id_hc_procedimientos,
        'id_usuariocrea'       => $idusuario,
        'id_usuariomod'        => $idusuario,
        'ip_modificacion'      => $ip_cliente,
        'ip_creacion'          => $ip_cliente,
        ];
        $id = Movimiento_Paciente::insertGetId($input_movimiento_paciente);
        return $id;
        ///////////////////////////////////////////////////////////////////*/
        DB::beginTransaction();
        try {
            if (!is_null($producto)) {
                //producto que esta en transito
                if ($producto->fecha_vencimiento >= date('Y-m-d')) {
                    $uso = $producto->usos - 1;
                    //return $producto->usos;
                    if ($uso > 0) {
                        $tipo       = '2';
                        $cantidad_2 = 1;
                    } else {
                        $tipo       = '0';
                        $cantidad_2 = 0;
                    }
                    $input_movimiento = [
                        // 'cantidad'        => $cantidad_2,
                        'usos'            => $uso,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariomod'   => $idusuario,
                        'tipo'            => $tipo,
                    ];
                    $producto_ingreso = $producto;
                    $producto_ingreso->update($input_movimiento);

                    $input_movimiento_paciente = [
                        'id_movimiento'        => $producto->id,
                        'id_hc_procedimientos' => $id_hc_procedimientos,
                        'id_usuariocrea'       => $idusuario,
                        'id_usuariomod'        => $idusuario,
                        'ip_modificacion'      => $ip_cliente,
                        'ip_creacion'          => $ip_cliente,
                    ];
                    $id = Movimiento_Paciente::insertGetId($input_movimiento_paciente);
                    Log_movimiento::create([
                        'id_producto'     => $producto->producto->id,
                        'id_encargado'    => $idusuario,
                        'id_movimiento'   => $producto->id,
                        'observacion'     => "Producto entregado a paciente",
                        'tipo'            => '0',
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea'  => $idusuario,
                        'id_usuariomod'   => $idusuario,
                    ]);
                    ## creo el documento de egreso
                    // $this->__serie_enfermeroget($nombre, $id_hc_procedimientos);
                    DB::commit();
                    return $id;
                } else {
                    return "caducado";
                }
            } elseif (!is_null($producto_2)) {
                //producto que esta en bodega
                if ($producto_2->fecha_vencimiento >= date('Y-m-d')) {
                    $uso = $producto_2->usos - 1;
                    if ($uso > 0) {
                        $cantidad_2 = 1;
                        $tipo       = '2';
                        $producto   = Producto::find($producto_2->id_producto);
                        $cantidad   = $producto->cantidad - 1;
                        $input2     = [
                            // 'cantidad'        => $cantidad,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariomod'   => $idusuario,
                        ];

                        $producto->update($input2);
                    } else {
                        $tipo       = '0';
                        $cantidad_2 = 0;
                        $producto   = Producto::find($producto_2->id_producto);
                        $cantidad   = $producto->cantidad - 1;
                        $input2     = [
                            // 'cantidad'        => $cantidad,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariomod'   => $idusuario,
                        ];

                        $producto->update($input2);
                    }
                    $input_movimiento = [
                        // 'cantidad'        => $cantidad_2,
                        'usos'            => $uso,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariomod'   => $idusuario,
                        'tipo'            => $tipo,
                    ];
                    $producto_ingreso = $producto_2;
                    $producto_ingreso->update($input_movimiento);

                    $input_movimiento_paciente = [
                        'id_movimiento'        => $producto_2->id,
                        'id_hc_procedimientos' => $id_hc_procedimientos,
                        'id_usuariocrea'       => $idusuario,
                        'id_usuariomod'        => $idusuario,
                        'ip_modificacion'      => $ip_cliente,
                        'ip_creacion'          => $ip_cliente,
                    ];
                    $id = Movimiento_Paciente::insertGetId($input_movimiento_paciente);

                    Log_movimiento::create([
                        'id_producto'     => $producto_2->producto->id,
                        'id_encargado'    => $idusuario,
                        'id_movimiento'   => $producto_2->id,
                        'observacion'     => "Producto entregado a paciente",
                        'tipo'            => '0',
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea'  => $idusuario,
                        'id_usuariomod'   => $idusuario,
                    ]);
                    ## creo el documento de egreso
                    // $this->__serie_enfermeroget($nombre, $id_hc_procedimientos);
                    DB::commit();
                    return $id;
                } else {
                    return "caducado";
                }
            } elseif (!is_null($producto_3)) {

                //return $producto_3;
                //producto que esta en bodega
                if ($producto_3->fecha_vencimiento >= date('Y-m-d')) {
                    $uso = $producto_3->usos - 1;
                    if ($uso > 0) {
                        $cantidad_2 = 1;
                        $tipo       = '2';
                        $producto   = Producto::find($producto_3->id_producto);
                        $cantidad   = $producto->cantidad - 1;
                        $input2     = [
                            // 'cantidad'        => $cantidad,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariomod'   => $idusuario,
                        ];

                        $producto->update($input2);
                    } else {
                        $tipo       = '0';
                        $cantidad_2 = 0;
                        $producto   = Producto::find($producto_3->id_producto);
                        $cantidad   = $producto->cantidad - 1;
                        $input2     = [
                            // 'cantidad'        => $cantidad,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariomod'   => $idusuario,
                        ];

                        $producto->update($input2);
                    }
                    $input_movimiento = [
                        // 'cantidad'        => $cantidad_2,
                        'usos'            => $uso,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariomod'   => $idusuario,
                        'tipo'            => $tipo,
                    ];
                    $producto_ingreso = $producto_3;
                    $producto_ingreso->update($input_movimiento);

                    $input_movimiento_paciente = [
                        'id_movimiento'        => $producto_3->id,
                        'id_hc_procedimientos' => $id_hc_procedimientos,
                        'id_usuariocrea'       => $idusuario,
                        'id_usuariomod'        => $idusuario,
                        'ip_modificacion'      => $ip_cliente,
                        'ip_creacion'          => $ip_cliente,
                    ];
                    $id = Movimiento_Paciente::insertGetId($input_movimiento_paciente);

                    Log_movimiento::create([
                        'id_producto'     => $producto_3->producto->id,
                        'id_encargado'    => $idusuario,
                        'id_movimiento'   => $producto_3->id,
                        'observacion'     => "Producto entregado a paciente",
                        'tipo'            => '0',
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea'  => $idusuario,
                        'id_usuariomod'   => $idusuario,
                    ]);
                    ## creo el documento de egreso
                    // $this->__serie_enfermeroget($nombre, $id_hc_procedimientos);
                    DB::commit();
                    return $id;
                } else {
                    return "caducado";
                }
            } else {
                return 'No se encontraron resultados';
            }
        } catch (\Exception $e) {
            //if there is an error/exception in the above code before commit, it'll rollback
            DB::rollBack();
            $data['msj']   = 'error';
            $data['error'] = 'error: ' . $e->getMessage();
            return response()->json($data);
            return $e->getMessage();
        }
    }

    public function listado_prod($id_procedimiento)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $procedimientos = hc_procedimientos::join('historiaclinica as hc', 'hc.hcid', '=', 'hc_procedimientos.id_hc')
            ->where('hc_procedimientos.id', $id_procedimiento)->get();

        if ($procedimientos->count() > 0) {
            $hcid = $procedimientos->first()->hcid;
            ///return $hcid;
        } else {
            $hcid = "";
        }
        $productos = Movimiento_Paciente::where('id_hc_procedimientos', $id_procedimiento)->get();

        $aprobado =0;
        $agenda = Agenda::find($procedimientos[0]->id_agenda);
        if (Auth::user()->id == "0951561075") {
            //dd($agenda, $procedimientos);
        }
        if (!is_null($agenda)) {
            $planilla = Planilla::where("id_agenda", $agenda->id)->where('nota_entrega', 0)->first();

            if (!is_null($planilla)) {
                if ($planilla->aprobado == 1) {
                    $aprobado = 1;
                }
            }
        }
        return view('enfermeria/listado_prod', ['productos' => $productos, 'hcid' => $hcid, 'id_proc' => $id_procedimiento, "aprobado" => $aprobado]);
    }

    public function nombre(Request $request)
    {
        $nombre = $request['term'];

        $data      = array();
        $productos = Producto::where('estado', '1')->where(function ($query) use ($nombre) {
            $query->where('nombre', 'like', '%' . $nombre . '%')
                ->orwhere('codigo', 'like', '%' . $nombre . '%');
        })->get();
        foreach ($productos as $product) {
            //$data[] = array('value' => $product->nombre, 'id' => $product->id, 'codigo' => $product->codigo);
            $data[] = array('value' => "{$product->codigo} | {$product->descripcion}", 'id' => $product->id, 'codigo' => $product->codigo);
        }
        if (count($data)) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }
    }
    public function nombre2(Request $request)
    {
        $nombre    = $request['nombre'];
        $data      = null;
        $productos = Producto::where('nombre', 'like', '%' . $nombre . '%')->where('estado', '1')->first();
        if (!is_null($productos)) {
            $data = $productos->codigo;
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }
    }

    public function nombre_plantilla(Request $request)
    {

        $nombre_plan = $request['term'];
        $data        = null;

        $seteo = '%' . $nombre_plan . '%';

        $query1 = "SELECT id, nombre
                  FROM insumo_plantilla
                  WHERE nombre like '" . $seteo . "' or id like '" . $seteo . "'  LIMIT 100
                  ";

        $plantilla = DB::select($query1);

        foreach ($plantilla as $value) {
            $data[] = array('value' => $value->nombre, 'id' => $value->id);
        }

        if (count($data)) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }
    }

    public function nombre_plantilla2(Request $request)
    {

        $nombre_plan = $request['nom_plantilla'];
        $data        = null;

        $seteo = '%' . $nombre_plan . '%';

        $query1 = "SELECT id, nombre
                  FROM insumo_plantilla
                  WHERE nombre like '" . $seteo . "' or id like '" . $seteo . "'  LIMIT 100
                  ";

        $plantilla = DB::select($query1);

        foreach ($plantilla as $value) {
            $data[] = array('value' => $value->nombre, 'id' => $value->id);
        }

        if (count($data) > 0) {
            return $data[0];
        } else {
            return '0';
        }
    }

    public function guardar_plantilla(Request $request)
    {
        $arreglo = json_decode($request['nombre']);
        //dd($arreglo);
        $id_plantilla_2       = $request['id_plantilla_2'];
        $ip_cliente           = $_SERVER["REMOTE_ADDR"];
        $id_hc_procedimientos = $request['id_hc_procedimientos'];
        $cantidad             = $request['cantidad'];
        $idusuario            = Auth::user()->id;
        $dato                 = 0;
        //dd($request->all());
        $users = array();
        for ($i = 0; $i < count($arreglo); $i++) {
            array_push($users, $arreglo[$i]);
        }
        //dd($users);
        $insumoPlantilla = Insumo_Plantilla::where('id', '=', $id_plantilla_2)->get();
        $producto_3      = Movimiento::join('producto', 'movimiento.id_producto', '=', 'producto.id')
            ->where('producto.codigo_siempre', 1)
            ->whereIn('producto.codigo', $users)
            ->where('movimiento.usos', '>=', 1)
            ->where('movimiento.fecha_vencimiento', '>=', date('Y-m-d'))
            ->where('movimiento.cantidad', '>=', 1)
            ->where('movimiento.tipo', '=', 1)
            ->select('movimiento.*')
            ->distinct()
            ->get();
        //dd($producto_3);
        $contador = 0;
        //dd($producto_3);
        foreach ($producto_3 as $value) {
            //dd($value);
            $contador++;
            //dd("asd");
            if ($value->fecha_vencimiento >= date('Y-m-d')) {
                $uso = $value->usos - 1;
                if ($uso > 0) {
                    //dd("aqui");
                    $cantidad_2     = 1;
                    $tipo           = '2';
                    $producto       = Producto::find($value->id_producto);
                    $cantidad_final = $value->cantidad - $cantidad;
                    $input2         = [
                        'cantidad'        => $cantidad,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariomod'   => $idusuario,
                    ];
                    $producto->update($input2);
                } else {
                    //dd("else");
                    $tipo           = '0';
                    $cantidad_2     = 0;
                    $producto       = Producto::find($value->id_producto);
                    $cantidad_final = $producto->cantidad - $cantidad;
                    $input2         = [
                        'cantidad'        => $cantidad,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariomod'   => $idusuario,
                    ];

                    $producto->update($input2);
                }
                //dd("hola");

                $input_movimiento = [
                    'cantidad'        => $cantidad,
                    'usos'            => $uso,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod'   => $idusuario,
                    'tipo'            => $tipo,
                ];
                $producto_ingreso = $value;
                $producto_ingreso->update($input_movimiento);
                //dd($input_movimiento);
                $input_movimiento_paciente = [
                    'id_movimiento'        => $value->id,
                    'id_hc_procedimientos' => $id_hc_procedimientos,
                    'id_usuariocrea'       => $idusuario,
                    'id_usuariomod'        => $idusuario,
                    'ip_modificacion'      => $ip_cliente,
                    'ip_creacion'          => $ip_cliente,
                ];
                $id   = Movimiento_Paciente::insertGetId($input_movimiento_paciente);
                $dato = $id;
                Log_movimiento::create([
                    'id_producto'     => $value->producto->id,
                    'id_encargado'    => $idusuario,
                    'id_movimiento'   => $value->id,
                    'observacion'     => "Producto entregado a paciente",
                    'tipo'            => '0',
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                ]);
            } else {
                return response()->json('caducado');
            }
        }
        return response()->json($dato);
    }

    /* public function creaPlanillaProcedimientoBasica($d_procedimiento)
    {
        $planilla_procedimiento_basica = Planilla_Procedimiento::where('id_procedimiento', $this->id_procedimiento_generico)->first();
        $planilla_procedimiento         = new Planilla_Procedimiento;
        // $planilla_procedimiento->
    } */

    public function vhguardar_plantilla_basica(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        // dd($request->all());
        // DB::beginTransaction();
        // try {

        $hcid        = $request->p_hcid;
        $a_codigo    = $request->codigo;
        $a_id_item   = $request->id_item;
        $a_item_cant = $request->item_cant;
        $id_bodega        = $request->id_bodega;


        $historia = Historiaclinica::find($hcid);

        $conf = 0;
        $secuencia = 0;
        $product_no_encontrado = "";

        $paciente      = null;
        $procedimiento = null;
        $errors = 0;
        $errors_ob = "";
        if (isset($historia)) {
            $paciente       = $historia->paciente;
            $procedimientos = $historia->hc_procedimientof;
        }

        if ($procedimientos == null) {
            return ['estado' => 'ERROR', 'msn' => 'SIN PROCEDIMIENTOS'];
        }

        if (isset($request['hc_procedimientos'])) {
            $procedimientos = hc_procedimientos::where('id', $request['hc_procedimientos'])->get();
        }

        foreach ($procedimientos as $procedimiento) {
            if ($procedimiento != null) {
                if ($request->p_hcid == $procedimiento->id_hc) {
                    $conf             = 1;
                    $vh_procedimiento = null;
                    if (isset($procedimiento->hc_procedimiento_f)) {
                        foreach ($procedimiento->hc_procedimiento_f as $px) {
                            if ($px->procedimiento->id_grupo_procedimiento != null) {
                                $vh_procedimiento = $px->procedimiento->id; //dd($vh_procedimiento);
                                break;
                            }
                        }
                    }
                    if ($vh_procedimiento != null) {
                        $planilla_procedimiento = Planilla_Procedimiento::where('id_procedimiento', $vh_procedimiento)->first();
                        if (is_null($planilla_procedimiento)) {
                            $planilla_procedimiento = Planilla_Procedimiento::where('id_procedimiento', $this->id_procedimiento_generico)->first();
                        }
                        if (!is_null($planilla_procedimiento)) {
                            $id_plantilla = $planilla_procedimiento->id_planilla;
                            /* LO DE TRANSITO PENTAX A COMPRAS */
                            // $id_bodega        = env('BODEGA_EGR_PACI1', 2);
                            // $id_bodega_compra = env('BODEGA_COMPRA', 3);
                            // if(Auth::user()->id == "0957258056"){
                            //     dd($request->all());
                            // }
                            $id_bodega_compra = env('BODEGA_COMPRA', 3);

                            $documento        = InvDocumentosBodegas::where('abreviatura_documento', 'TRA')->first();
                            $secuencia        = InvDocumentosBodegas::getSecueciaTipo($id_bodega, 'T');

                            if ($secuencia != 0) {
                                $la_planilla = Planilla::where('id_hc_procedimiento', $procedimiento->id)->where('estado', 1)->where('nota_entrega', 0)->first();

                                if (is_null($la_planilla)) {

                                    $transaccion = InvTransaccionesBodegas::where('id_documento_bodega', $documento->id)
                                        ->where('id_bodega', $id_bodega)
                                        ->first();

                                    $a_proc = [
                                        'fecha'               => date('Y-m-d H:i:s'),
                                        'id_planilla'         => $id_plantilla,
                                        'id_agenda'           => $historia->id_agenda,
                                        'id_movimiento'       => null,
                                        'id_hc_procedimiento' => $procedimiento->id,
                                        'id_usuariocrea'      => $idusuario,
                                        'id_usuariomod'       => $idusuario,
                                        'ip_creacion'         => $ip_cliente,
                                        'ip_modificacion'     => $ip_cliente,
                                        //'codigo' => ,
                                        'estado'              => '1',
                                        'observacion'         => 'Paciente: ' . $paciente->apellido1 . ' ' . $paciente->apellido2 . ' ' . $paciente->nombre1 . ' ' . $paciente->nombre2,
                                    ];
                                    $cabecera = Planilla::insertGetId($a_proc);
                                } else {

                                    //$cab_mov_inv = InvCabMovimientos::find($la_planilla->id_movimiento);

                                    $a_proc = [
                                        'id_usuariomod'   => $idusuario,
                                        'ip_modificacion' => $ip_cliente,
                                    ];

                                    $la_planilla->update($a_proc);
                                    $cabecera = $la_planilla->id;
                                }

                                $acum_subt  = 0;
                                $acum_desc  = 0;
                                $acum_iva   = 0;
                                $acum_total = 0;

                                for ($i = 0; $i < count($a_codigo); $i++) {

                                    $codigo      = $a_codigo[$i];
                                    $id_item     = $a_id_item[$i];
                                    $item_cant   = $a_item_cant[$i];
                                    $iva         = 0;
                                    $id_producto = $id_item;
                                    $inventario  = InvInventario::getInventario($id_producto, $id_bodega);
                                    // if (!isset($inventario->id)) {
                                    //     $inventario = InvInventario::setNeoInventario($request['id'][$i], $request['bodega_entrante'], 0, 0);
                                    // }
                                    $producto = Producto::find($id_producto);
                                    $precio   = 0;

                                    $conf_iva = Ct_Configuraciones::find(3);
                                    $invcosto = InvCosto::where('id_producto', $id_producto)->first();

                                    // if (!is_null($invcosto)) {
                                    //     $precio = $invcosto->costo_promedio;
                                    // }

                                    $inv_costo_promedio = InvInventario::where('id_bodega', $id_bodega)->where('id_producto', $id_producto)->where("id_empresa", Session::get("id_empresa"))->first();

                                    if (!is_null($inv_costo_promedio)) {
                                        $precio = $inv_costo_promedio->costo_promedio;
                                    }
                                    if (isset($producto->iva) && $producto->iva == 1) {
                                        $iva = ($item_cant * $precio) * $conf_iva->iva;
                                    }

                                    $cant_uso = 0;
                                    if ($producto->usos != null) {
                                        $cant_uso = $producto->usos;
                                    }
                                    if ($cant_uso == null or $cant_uso < 0) {
                                        $cant_uso = 0;
                                    }

                                    $inv_serie = InvInventarioSerie::where('id_producto', $id_producto)
                                        ->where('id_bodega', $id_bodega)
                                        ->where('estado', 1)
                                        ->where('existencia', '>=', $item_cant)
                                        ->orderBy('fecha_vence', 'desc')
                                        ->first();

                                    $transitocontroller = new Insumos\TransitoController();
                                    $id_movimiento      = null;

                                    $check                  = 0;
                                    $estado                 = 0;
                                    $observacion            = null;
                                    $fecha_vencimiento      = null;
                                    $lote                   = null;
                                    $serie                  = null;
                                    $id_movimiento_paciente = null;
                                    if (!is_null($inv_serie)) {

                                        $check             = 1;
                                        $estado            = 1;
                                        $fecha_vencimiento = $inv_serie->fecha_vence;
                                        $lote              = $inv_serie->lote;
                                        $serie             = $inv_serie->serie;

                                        //DETALLE DEL PEDIDO POR SERIE EL PRIMERO
                                        $detalle_movimiento = InvDetMovimientos::where('serie', $serie)->orderBy('id', 'ASC')->first();

                                        $mov_cabacera = $detalle_movimiento->cabecera;

                                        $pedido = Pedido::find($mov_cabacera->id_pedido);
                                        //if($id_producto=='110'){dd($pedido,$mov_cabacera,$detalle_movimiento,$id_producto,$inv_serie);}
                                        if (!is_null($pedido)) {

                                            //dd($detalle_movimiento);
                                            if (!is_null($detalle_movimiento)) {

                                                $arr_mov = [
                                                    'id_producto'       => $id_producto,
                                                    'cantidad'          => $item_cant,
                                                    'serie'             => $serie,
                                                    'id_bodega'         => $id_bodega_compra,
                                                    'id_pedido'         => $mov_cabacera->id_pedido,
                                                    'estado'            => '1',
                                                    'tipo'              => '0',
                                                    'fecha_vencimiento' => $fecha_vencimiento,
                                                    'id_encargado'      => $idusuario,
                                                    'usos'              => 0,
                                                    'lote'              => $lote,
                                                    'id_usuariocrea'    => $idusuario,
                                                    'id_usuariomod'     => $idusuario,
                                                    'ip_creacion'       => $ip_cliente,
                                                    'ip_modificacion'   => $ip_cliente,
                                                    'precio'            => $precio,
                                                    'descuento'         => 0,
                                                    'descuentop'        => 0,
                                                    'consecion_det'     => 0,
                                                    'id_bodega'         => $id_bodega
                                                ];

                                                $id_movimiento = Movimiento::insertGetId($arr_mov); //dd($id_movimiento);
                                                // if(Auth::user()->id == "0957258056"){
                                                //     dd($item_cant);
                                                // }
                                                // for ($i_victor = 0; $i_victor < $item_cant; $i_victor++) {

                                                $arr_mov_pac = [
                                                    'id_movimiento'        => $id_movimiento,
                                                    'cantidad'          => $item_cant,
                                                    'id_hc_procedimientos' => $procedimiento->id,
                                                    'id_usuariocrea'       => $idusuario,
                                                    'id_usuariomod'        => $idusuario,
                                                    'ip_creacion'          => $ip_cliente,
                                                    'ip_modificacion'      => $ip_cliente,
                                                    'id_bodega'            => $id_bodega,
                                                ];

                                                $id_movimiento_paciente = Movimiento_Paciente::insertGetId($arr_mov_pac);

                                                // }

                                                // INCREMENTA EL COMPROMETIDO EN INVENTARIO //
                                                $inv_serie = InvInventarioSerie::where('serie', $serie)
                                                    ->where('id_bodega', $id_bodega)
                                                    ->where('estado', 1)
                                                    ->first();
                                                if (isset($inv_serie->id)) {
                                                    InvInventarioSerie::comprometer($inv_serie, $item_cant);
                                                }

                                                //////////////////////////////////////
                                                $data                 = null;
                                                $id_hc_procedimientos = $procedimiento->id;
                                                $id_agenda            = $historia->id_agenda;
                                            } else {

                                                $observacion = 'NO SE ENCONTRO EL PEDIDO';
                                            }
                                        } else {
                                            $observacion = 'NO SE ENCONTRO EL PEDIDO REL';
                                        }
                                        $tipo_plantilla = null;
                                        //dd($id_plantilla,$id_producto);
                                        $ins_plantilla_item_control = Insumo_Plantilla_Item_Control::where('id_plantilla', $id_plantilla)->where('id_producto', $id_producto)->first();

                                        if (!is_null($ins_plantilla_item_control)) {

                                            $tipo_plantilla = $ins_plantilla_item_control->tipo_plantilla;
                                        }

                                        $a_detalle = [
                                            'codigo'                 => $codigo,
                                            'id_planilla_cabecera'   => $cabecera,
                                            //'procedimiento' => ,
                                            'precio'                 => $precio,
                                            'check'                  => $check,
                                            'estado'                 => $estado,
                                            'id_usuariocrea'         => $idusuario,
                                            'id_usuariomod'          => $idusuario,
                                            'ip_creacion'            => $ip_cliente,
                                            'ip_modificacion'        => $ip_cliente,
                                            'movimiento'             => $id_movimiento,
                                            'id_movimiento_paciente' => $id_movimiento_paciente,
                                            'cantidad'               => $item_cant,
                                            'serie'                  => $serie,
                                            'lote'                   => $lote,
                                            'fecha_vencimiento'      => $fecha_vencimiento,
                                            'observacion'            => $observacion,
                                            'tipo_plantilla'         => $tipo_plantilla,
                                            'id_bodega'              => $id_bodega
                                        ];

                                        $detalle = Planilla_Detalle::insertGetId($a_detalle);
                                    } else {
                                        $errors = 1;
                                        $errors_ob .= "<label class='label label-danger'>{$producto->nombre}</label><br><br>";
                                        // $observacion = 'NO SE ENCONTRO EL PRODUCTO';
                                    }
                                }
                                //DB::commit();
                            }
                        } else {
                            return ['estado' => 'ERROR', 'msn' => 'SIN PLANILLA PROCEDIMIENTO'];
                        }
                    } else {
                        return ['estado' => 'ERROR', 'msn' => 'SIN PROCEDIMIENTO PRINCIPAL'];
                    }
                    //DB::rollBack();
                }
            }
        }

        if ($conf == 0) {
            return ['estado' => 'ERROR', 'msn' => 'NO SE PUDO ASOCIAR LA PLANILLA'];
        }
        // }catch (\Exception $e) {
        //     DB::rollBack();
        //     return ['error' => $e->getMessage()];
        // }

        return ['estado' => 'OK', 'msn' => 'LISTO', "observacion" => $errors == 1 ? "<label class='label label-danger'>Insumos sin existencias: </label><br>" . $errors_ob : ''];
    }

    public function insumos_excel(Request $request)
    {
        $tiempo          = time();
        $nombre_original = $tiempo . '' . $request['file']->getClientOriginalName();
        $r1              = Storage::disk('public')->put($nombre_original, \File::get($request['file']));
        $arrFinal        = array();
        if ($r1) {
            try {
                Excel::load(('storage//app//avatars//' . $nombre_original), function ($reader) use (&$arrFinal) {

                    foreach ($reader->toArray() as $val) {
                        $serie = "";
                        if (substr($val['codigo'], -1, 1) == '-') {
                            $serie = substr($val['codigo'], 0, -1);
                        } else {
                            $serie = $val['codigo'];
                        }

                        $array = array(
                            'cantidad' => $val['cantidad'],
                            //'codigo' => $val['codigo'],
                            'codigo'   => $serie,
                        );


                        array_push($arrFinal, $array);
                    }
                });

                return ['array' => $arrFinal];
            } catch (\Exception $e) {

                return ['msj' => $e->getMessage()];
            }
        }
    }

    public function corrige_existencias($serie, $id_bodega)
    {

        $ip_cliente  = $_SERVER["REMOTE_ADDR"];
        $idusuario   = Auth::user()->id;

        $id_empresa = Session::get('id_empresa');

        $inv_serie = InvInventarioSerie::where('serie', $serie)->where('id_bodega', $id_bodega)->where('id_empresa', $id_empresa)->first();
        if ($inv_serie->cantidad_usos_kardex > 0) {
            if ($inv_serie->existencia == 0) {
                $inv_serie->update(['existencia' => 1]);
                $input_log = [
                    'id_usuario'  => $idusuario,
                    'ip_usuario'  => $ip_cliente,
                    'descripcion' => "CORRIGE EXISTENCIAS DESDE PISTOLEO ENF",
                    'dato_ant1'   => $serie,
                    'dato1'       => $id_bodega,
                    'dato_ant4'   => " ",
                    'dato2'       => ' ',
                ];

                Log_usuario::create($input_log);
            }
        }
    }

    public function insumos_uso2($id_agenda)
    {
        
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $tipos  = Tipo::all();
        $agenda = Agenda::find($id_agenda);

        $procedimientos = hc_procedimientos::join('historiaclinica as hc', 'hc.hcid', '=', 'hc_procedimientos.id_hc')
            ->where('hc.id_agenda', $id_agenda)->get();

            //dd($procedimientos);

        if ($procedimientos->count() > 0) {
            $hcid = $procedimientos->first()->hcid;
            ///return $hcid;
        } else {
            $hcid = "";
        }
        $equipos = Equipo_Historia::where('hcid', $hcid)->get();
        //dd($agenda);
        //return $equipos;
        //dd($procedimientos);

        return view('enfermeria/insumos_uso2', ['procedimientos' => $procedimientos, 'agenda' => $agenda, 'equipos' => $equipos, 'hcid' => $hcid, 'tipos' => $tipos]);
    }


    public function evolucion($hc_procedimiemto)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $adicionales = hc_procedimientos::find($hc_procedimiemto);
        //dd($adicionales->evolucion_consulta);

        $detalles = DB::table("hc_evolucion_anestesiologo")->where("id_hc",$adicionales->id_hc)->where('estado',1)->get();
        //dd($detalles);
  
        return view('enfermeria/evolucion', compact("adicionales","detalles"));
    }




    public function actualiza_evolucionar(Request $request)
    {
        //dd($request->all());
        //return "hola";
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $adicionales = hc_procedimientos::find($request["id_proc_final"]);
        

        Hc_Evolucion_Anestesiologo::create([

            'motivo'                  => $request["motivo"],
            'evolucion'               => $request["evolucion"],
            'hc_id_procedimiento'     => $request["id_proc_final"],
            'id_hc'                   => $adicionales["id_hc"],
            'estado'                  => 1,
            'id_usuariocrea'          => $idusuario,
            'ip_creacion'             => $ip_cliente,
            'id_usuariomod'           => $idusuario,

        ]);

        return "Guardado";
    }



    public function eliminar(Request $request)
    {
        $registro = Hc_Evolucion_Anestesiologo::find($request['id']);
        if ($registro) {
            // Actualiza el valor de 'estado' a 0
            $registro->update(['estado' => 0]);

            return "Estado actualizado a 0 para el registro con ID " . $request->id;
        } else {
            return "Registro no encontrado";
        }
    }

}

