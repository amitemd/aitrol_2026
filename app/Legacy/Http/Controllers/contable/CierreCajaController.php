<?php

namespace Sis_medico\Http\Controllers\contable;

use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Agenda;
use Sis_medico\Ct_Arqueo_Caja;
use Sis_medico\Agenda_Permiso;
use Sis_medico\CierreCaja;
use Sis_medico\Ct_Bancos;
use Sis_medico\Ct_Clientes;
use Sis_medico\Ct_Orden_Venta;
use Sis_medico\Ct_Tipo_Pago;
use Sis_medico\Ct_Tipo_Tarjeta;
use Sis_medico\Ct_Ven_Orden;
use Sis_medico\Ct_Ven_Orden_Detalle;
use Sis_medico\Ct_Orden_Venta_Pago;
use Sis_medico\CtCajaCobro;
use Sis_medico\Empresa;
use Sis_medico\Examen;
use Sis_medico\Examen_Agrupador;
use Sis_medico\Examen_Detalle_Forma_Pago;
use Sis_medico\Examen_Orden;
use Sis_medico\Http\Controllers\ApiFacturacionController;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Labs_doc_externos;
use Sis_medico\Log_usuario;
use Sis_medico\Paciente;
use Sis_medico\Protocolo;
use Sis_medico\Seguro;
use Sis_medico\User;
use Sis_medico\Orden_documento;
class CierreCajaController extends Controller
{
    private $controlador = 'cierrecaja';
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if (in_array($rolUsuario, array(1, 4, 5, 20, 21, 22)) == false) {
            return true;
        }
    }

    public function index_cierre(Request $request)
    {
       //dd($request->all());
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $fecha = $request['fecha'];
        if (is_null($fecha)) {
            $fecha = date('Y-m-d');
        }
        $fecha_hasta = $request['fecha_hasta'];
        if (is_null($fecha_hasta)) {
            $fecha_hasta = date('Y-m-d');
        }
        $id_empresa = $request['id_empresa'];
        $id_usuario = $request['id_usuario'];
        if (is_null($id_empresa)) {
            //$id_empresa = Empresa::where('prioridad', '1')->first()->id;
        }
        $caja = $request['caja'];
        $usuario = null;
        $doctor = $request['doctor'];

        $tipo = $request['tipo'];
        /*if (is_null($tipo)) {
        $tipo = '0';
        }*/
        $cedula = $request['cedula'];
        //agenda.estado = 1 activa agenda.proc_consul 0 o 1
        $numero              = $request['numero'];
        $seguro              = $request['seguro'];
        $tipo_seguro         = $request['tipo_seguro'];
        
        //$facturas_pendientes = Agenda::leftjoin('ct_orden_venta as orden', 'orden.id_agenda', 'agenda.id')
        $facturas_pendientes = Agenda::leftjoin('ct_orden_venta as orden', function ($join) {
                            $join->on(function($query){
                                    $query->on('orden.id_agenda','agenda.id')
                                    ->where('orden.estado', '=', '1');
                                });
                            })
                            ->join('paciente as p', 'p.id', 'agenda.id_paciente')
                            ->join('users as u', 'agenda.id_usuariomod', 'u.id')
                            ->whereBetween('agenda.fechaini', [$fecha . ' 00:00:00', $fecha_hasta . ' 23:59:00'])
                            ->join('seguros as s', 's.id', 'agenda.id_seguro')
                            ->where('s.tipo', '<>', '0')
                            ->where('agenda.proc_consul', '<', '2')
                            ->whereRaw('(agenda.omni like "%NO%" OR agenda.omni IS NULL)')
                            ->where('agenda.estado', '<>', '0')
                            ->whereNotNull('agenda.id_doctor1')
                            ->whereNull('orden.id')
                            ->where('agenda.estado_cita', '4')
                            ->where('agenda.post_proc', '0')
                            ->where('agenda.id_doctor1', '<>', '4444444444');
        //dd($facturas_pendientes->select('agenda.*','orden.id as idorden','orden.estado as oestado')->get());

        if (is_null($numero)) {
            $ordenes = Ct_Orden_Venta::where('ct_orden_venta.estado', 1)
                ->join('agenda as a', 'a.id', 'ct_orden_venta.id_agenda')
                ->leftjoin('seguros as seg', 'seg.id', 'ct_orden_venta.id_seguro');
                //->where('ct_orden_venta.id_empresa', $id_empresa);
            if(!is_null($id_empresa)){
                $ordenes  = $ordenes->where('ct_orden_venta.id_empresa', $id_empresa);
            }
            if(!is_null($id_usuario)){
                $ordenes  = $ordenes->where('ct_orden_venta.id_usuariomod', $id_usuario);
                $usuario = User::find($id_usuario);
            }
           
            if(!is_null($seguro)){
                $ordenes  = $ordenes->where('ct_orden_venta.id_seguro', $seguro);
            }
            if (!is_null($tipo_seguro)) {
                $ordenes = $ordenes->where('seg.tipo', $tipo_seguro);
            }
            if (!is_null($caja)) {

                $ordenes = $ordenes->where('ct_orden_venta.caja', $caja);
                if ($caja == 'LABORATORIO') {
                    $ordenes = $ordenes->where('ct_orden_venta.caja', $caja)->where('ct_orden_venta.id_Seguro', '<>', '1');
                }
            } else {
                //$ordenes = $ordenes->where('ct_orden_venta.caja', '<>', 'LABORATORIO');
            }
            if (!is_null($tipo)) {
                $ordenes             = $ordenes->where('a.proc_consul', $tipo);
                $facturas_pendientes = $facturas_pendientes->where('agenda.proc_consul', $tipo);
            }
            if (!is_null($doctor)) {
                $ordenes             = $ordenes->where('a.id_doctor1', $doctor);
                $facturas_pendientes = $facturas_pendientes->where('agenda.id_doctor1', $doctor);
            }
            if (!is_null($cedula)) {
                $ordenes             = $ordenes->where('a.id_paciente', $cedula);
                $facturas_pendientes = $facturas_pendientes->where('agenda.id_paciente', $cedula);
            }
            if (!is_null($seguro)) {
                $ordenes = $ordenes->where('ct_orden_venta.id_seguro', $seguro);
                $facturas_pendientes = $facturas_pendientes->where('agenda.id_seguro', $seguro);
            }
            if (!is_null($tipo_seguro)) {
                $ordenes = $ordenes->where('seg.tipo', $tipo_seguro);
                $facturas_pendientes = $facturas_pendientes->where('agenda.id_seguro', $tipo_seguro);
            }

            $ordenes = $ordenes->whereBetween('ct_orden_venta.fecha_emision', [$fecha . ' 00:00:00', $fecha_hasta . ' 23:59:00'])
                ->select('ct_orden_venta.*', 'a.id_doctor1', 'a.proc_consul')->get();
        } else {
            $ordenes = Ct_Orden_Venta::where('ct_orden_venta.id', $numero)
                ->join('agenda as a', 'a.id', 'ct_orden_venta.id_agenda')
                ->leftjoin('seguros as seg', 'seg.id', 'ct_orden_venta.id_seguro')
                ->select('ct_orden_venta.*', 'a.id_doctor1', 'a.proc_consul')
                ->get();
        }
        $cajas      = CtCajaCobro::where('estado',1)->get();
        $doctores            = User::where('id_tipo_usuario', '3')->where('training', '0')->where('uso_sistema', '0')->orderby('apellido1')->get();
        $empresas            = Empresa::where('admision', '1')->where('estado',1)->get();
        $facturas_pendientes = $facturas_pendientes->select('agenda.*', 'p.nombre1 as nombre1', 'p.apellido1 as apellido1', 'p.apellido2 as apellido2', 'u.nombre1 as unombre1', 'u.apellido1 as uapellido1', 'u.apellido2 as uapellido2', 'orden.id as orden')->get();
        //$idusuario   = Auth::user()->id;
        $idempresa   = session()->get('id_empresa');
        $user_caja    = Auth::user()->id;
        
        $arqueos = Ct_Arqueo_Caja::where('id_empresa', $idempresa)->where('estado', 1)->whereBetween('fecha_proceso', [$fecha, $fecha_hasta]);
        $seguros         = Seguro::where('inactivo', '<>', '0')->get();

        if(!is_null($id_usuario)){
            $arqueos = $arqueos->where('id_usuario', $id_usuario);
        }
 
        $arqueos = $arqueos->get();
        

        //dd($arqueos);
        return view('contable/reporte_cierre_caja/index_cierre', ['arqueos' => $arqueos, 'empresas' => $empresas, 'facturas_pendientes' => $facturas_pendientes, 'fecha' => $fecha, 'fecha_hasta' => $fecha_hasta, 'ordenes' => $ordenes, 'request' => $request, 'doctores' => $doctores, 'numero' => $numero, 'cedula' => $cedula, 'usuario' => $usuario, 'cajas' => $cajas, 'id_usuario'=>$id_usuario, 'seguros'=>$seguros]);
    }

    public function reporte(Request $request)
    {
        ini_set('max_execution_time', 900);
        $fecha = $request['fecha'];
        if (is_null($fecha)) {
            $fecha = date('Y-m-d');
        }
        $fecha_hasta = $request['fecha_hasta'];
        if (is_null($fecha_hasta)) {
            $fecha_hasta = date('Y-m-d');
        }
        $id_empresa = $request['id_empresa'];
        $id_usuario = $request['id_usuario'];
        //dd($id_empresa);
        if (is_null($id_empresa)) {
           // $id_empresa = Empresa::where('prioridad', '1')->first()->id;
        }
        $caja = $request['caja'];

        $doctor = $request['doctor'];

        $tipo = $request['tipo'];
        /*if (is_null($tipo)) {
        $tipo = '0';
        }*/
        $seguro              = $request['seguro'];
        $tipo_seguro         = $request['tipo_seguro'];

        $ordenes = Ct_Orden_Venta::where('ct_orden_venta.estado', 1)
            ->join('agenda as a', 'a.id', 'ct_orden_venta.id_agenda')
            ->join('seguros as seg', 'seg.id', 'ct_orden_venta.id_seguro')
            
            //->where('ct_orden_venta.id_empresa', $id_empresa)
            //->where('ct_orden_venta.caja', '<>', 'LABORATORIO')
            ->whereBetween('ct_orden_venta.fecha_emision', [$fecha . ' 00:00:00', $fecha_hasta . ' 23:59:00'])
            ->select('ct_orden_venta.*', 'a.id_doctor1', 'a.proc_consul');

        if (!is_null($tipo)) {
            $ordenes = $ordenes->where('a.proc_consul', $tipo);
        }

        if (!is_null($caja)) {
            $ordenes = $ordenes->where('ct_orden_venta.caja', $caja);
            if ($caja == 'LABORATORIO') {
                $ordenes = $ordenes->where('ct_orden_venta.caja', $caja)->where('ct_orden_venta.id_Seguro', '<>', '1');
            }
        }else {
                //$ordenes = $ordenes->where('ct_orden_venta.caja', '<>', 'LABORATORIO');
            }

        if (!is_null($doctor)) {
            $ordenes = $ordenes->where('a.id_doctor1', $doctor);
        }

        if(!is_null($id_empresa)){
            $ordenes  = $ordenes->where('ct_orden_venta.id_empresa', $id_empresa);
        }  

        if(!is_null($id_usuario)){
            $ordenes  = $ordenes->where('ct_orden_venta.id_usuariomod', $id_usuario);
        }
        if (!is_null($seguro)) {
            $ordenes = $ordenes->where('ct_orden_venta.id_seguro', $seguro);
            
        }
        if (!is_null($tipo_seguro)) {
            $ordenes = $ordenes->where('seg.tipo', $tipo_seguro);
            
        }

        $ordenes = $ordenes->get();
        if (is_null($id_empresa)) {
            $id_empresa = Empresa::where('prioridad', '1')->first()->id;
        }
        $empresa = Empresa::findorfail($id_empresa);
        
        
        $user_caja    = Auth::user()->id;

        //$arqueo_caja = Ct_Arqueo_Caja::where('id_empresa', $empresa->id)->where('id_usuario', $user_caja)->where('fecha_proceso', $fecha)->first();

        $arqueo_caja = Ct_Arqueo_Caja::where('id_empresa', $empresa->id)->whereBetween('fecha_proceso', [$fecha, $fecha_hasta]);

        if(!is_null($id_usuario)){
            $arqueo_caja = $arqueo_caja->where('id_usuario', $id_usuario);
        }
        else{
            $arqueo_caja = $arqueo_caja->whereNull('id_usuario');
        }
        if(!is_null($caja)){
            $arqueo_caja = $arqueo_caja->where('caja',$caja);
        } else{
            $arqueo_caja = $arqueo_caja->whereNull('caja');
        }
        $arqueo_caja = $arqueo_caja->first();
        //dd($arqueo_caja);
        $vistaurl = "contable.reporte_cierre_caja.pdf";
        $view     = \View::make($vistaurl, compact('ordenes', 'empresa', 'fecha', 'fecha_hasta', 'caja', 'arqueo_caja'))->render();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->setPaper('A4', 'landscape');
        return $pdf->stream('Cierre de Caja-' . $fecha . '.pdf');
    }
    public function imprimir_excel(Request $request)
    {
        $fecha = $request['fecha'];
        if (is_null($fecha)) {
            $fecha = date('Y-m-d');
        }
        $fecha_hasta = $request['fecha_hasta'];
        if (is_null($fecha_hasta)) {
            $fecha_hasta = date('Y-m-d');
        }
        $id_empresa = $request['id_empresa'];
        $id_usuario = $request['id_usuario'];
        if (is_null($id_empresa)) {
            $id_empresa = Empresa::where('prioridad', '1')->first()->id;
        }
        $caja = $request['caja'];

        $doctor = $request['doctor'];

        $tipo = $request['tipo'];
        $seguro   = $request['seguro'];
        $tipo_seguro   = $request['tipo_seguro'];
        /*if (is_null($tipo)) {
        $tipo = '0';
        }*/
        $ordenes = Ct_Orden_Venta::where('ct_orden_venta.estado', 1)
            ->join('agenda as a', 'a.id', 'ct_orden_venta.id_agenda')
            ->leftjoin('seguros as seg', 'seg.id', 'ct_orden_venta.id_seguro')
            ->where('ct_orden_venta.id_empresa', $id_empresa)
            ->whereBetween('ct_orden_venta.fecha_emision', [$fecha . ' 00:00:00', $fecha_hasta . ' 23:59:00'])
            ->select('ct_orden_venta.*', 'a.id_doctor1', 'a.proc_consul');

        /*$ordenes  = Agenda::where('agenda.estado', 1)
        ->leftjoin('ct_orden_venta as orden_venta', 'orden_venta.id_agenda', 'agenda.id')
        ->where('orden_venta.id_empresa', $id_empresa)
        ->whereBetween('agenda.fechaini', [$fecha.' 00:00:00', $fecha_hasta.' 23:59:00'])
        ->where('agenda.proc_consul',$tipo)
        ->where('agenda.estado_cita','4')
        ->select('agenda.*', 'agenda.id_doctor1', 'agenda.proc_consul','agenda.estado_cita');*/

        if (!is_null($tipo)) {
            $ordenes = $ordenes->where('a.proc_consul', $tipo);
        }

        if(!is_null($id_usuario)){
            $ordenes  = $ordenes->where('ct_orden_venta.id_usuariomod', $id_usuario);
        }
        if(!is_null($seguro)){
            $ordenes  = $ordenes->where('ct_orden_venta.id_seguro', $seguro);
        }
        if (!is_null($tipo_seguro)) {
            $ordenes = $ordenes->where('seg.tipo', $tipo_seguro);
        }

        if (!is_null($caja)) {
            $ordenes = $ordenes->where('ct_orden_venta.caja', $caja);
            if ($caja == 'LABORATORIO') {
                $ordenes = $ordenes->where('ct_orden_venta.caja', $caja)->where('ct_orden_venta.id_Seguro', '<>', '1');
            }
        }else {
                //$ordenes = $ordenes->where('ct_orden_venta.caja', '<>', 'LABORATORIO');
            }

        if (!is_null($doctor)) {
            $ordenes = $ordenes->where('a.id_doctor1', $doctor);
        }
        $ordenes = $ordenes->get();
        //dd($request->all(),$ordenes);

        $empresa = Empresa::findorfail($id_empresa);

        Excel::create('Cierre de Caja-' . $fecha, function ($excel) use ($ordenes, $empresa) {

            $excel->sheet('Cierre de Caja', function ($sheet) use ($ordenes, $empresa) {
                $sheet->mergeCells('B2:AA2');
                $sheet->cell('B2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CONSULTAS MEDICAS ESPECIALIZADAS DR CARLOS ROBLES');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#33E9FF');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA CITA');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('HORA CITA');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('D3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PACIENTE');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('E3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CTS');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('F3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ADMISION');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('G3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DOCTOR');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('H3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PROCEDIMIENTO');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('I3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SEGURO/CONVENIO');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('J3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TIPO CITA');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('K3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('REFERENCIA');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('L3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('EFECTIVO');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('M3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('T.CREDITO');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                // $sheet->cell('N3', function ($cell) {
                //     // manipulate the cel
                //     $cell->setValue('7% T/C');
                //     $cell->setFontWeight('bold');
                //     $cell->setAlignment('center');
                //     $cell->setBorder('thin', 'thin', 'thin', 'thin');
                // });
                // $sheet->cell('O3', function ($cell) {
                //     // manipulate the cel
                //     $cell->setValue('2% T/D');
                //     $cell->setFontWeight('bold');
                //     $cell->setAlignment('center');
                //     $cell->setBorder('thin', 'thin', 'thin', 'thin');
                // });
                $sheet->cell('N3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TRANSF/DEP');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('O3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CHEQUE');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('P3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DATALINK');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('Q3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PAGO APP');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('R3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PEND FC SEG');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('S3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TOTAL VTA');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('T3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('HONOR. MEDICOS');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('U3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TIPO TARJETA');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('V3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('BANCO');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('W3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('COMPROBANTE');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('X3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('OBSERVACION');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('Y3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ORIGEN');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('Z3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('REFERIDO');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AA3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DETALLE');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $tefectivo      = 0;
                $ttarjeta       = 0;
                $acum_efectivo  = 0;
                $acum_datalink  = 0;
                $acum_tcredito  = 0;
                $acum_p7        = 0;
                $acum_p2        = 0;
                $acum_tran      = 0;
                $acum_cheque    = 0;
                $acum_pago_app  = 0;
                $acum_oda       = 0;
                $acum_total     = 0;
                $acum_honorario = 0;
                $xcont          = 0;
                $i              = 4;
                foreach ($ordenes as $value) {
                    $pagos      = $value->pagos;
                    $efectivo   = 0;
                    $datalink   = 0;
                    $tcredito   = 0;
                    $p7         = 0;
                    $p2         = 0;
                    $tran       = 0;
                    $cheque     = 0;
                    $pago_app   = 0;
                    $total      = 0;
                    $referencia = "";
                    foreach ($pagos as $pago) {
                        $total += $pago->valor;
                        if ($pago->tipo == '1') {
                            $efectivo += $pago->valor;
                        }
                        if($pago->tipo == '9'){
                          $datalink += $pago->valor;
                        }
                        if($pago->tipo == '10'){
                          $pago_app += $pago->valor;
                        }

                        if ($pago->tipo == 4) {
                            $va = $pago->valor / (1 + $pago->p_fi);
                            $po = $va * $pago->p_fi;
                            $tcredito += $va;
                            $p7 += $po;
                        }

                        if ($pago->tipo == 6) {
                            $va = $pago->valor / (1 + $pago->p_fi);
                            $po = $va * $pago->p_fi;
                            $tcredito += $va;
                            $p2 += $po;
                        }

                        if ($pago->tipo == 3 || $pago->tipo == 5) {
                            $tran += $pago->valor;
                        }

                        if ($pago->tipo == 2) {
                            $cheque += $pago->valor;
                        }
                    }
                    if ($efectivo > 0) {
                        $referencia = "CASH";
                    }
                    if ($tcredito > 0) {
                        if (!is_null($referencia)) {
                            $referencia = $referencia . "+Tarjeta ";
                        } else {
                            $referencia = "+Tarjeta ";
                        }
                    }
                    if ($tran > 0) {
                        if (!is_null($referencia)) {
                            $referencia = $referencia . "+TRAN/DEP";
                        } else {
                            $referencia = "+TRAN/DEP";
                        }
                    }
                    if ($cheque > 0) {
                        if (!is_null($referencia)) {
                            $referencia = $referencia . "+CH";
                        } else {
                            $referencia = "+CH";
                        }
                    }
                    if ($pago_app > 0) {
                        if (!is_null($referencia)) {
                            $referencia = $referencia . "+Pago_APP";
                        } else {
                            $referencia = "+Pago_APP";
                        }
                    }
                    $total += $value->valor_oda;
                    $honorario      = $total - $p2 - $p7;
                    $acum_efectivo  = $acum_efectivo + $efectivo;
                    $acum_datalink  = $acum_datalink + $datalink;
                    $acum_tcredito  = $acum_tcredito + $tcredito;
                    $acum_p7        = $acum_p7 + $p7;
                    $acum_p2        = $acum_p2 + $p2;
                    $acum_tran      = $acum_tran + $tran;
                    $acum_cheque    = $acum_cheque + $cheque;
                    $acum_pago_app  = $acum_pago_app + $pago_app;
                    $acum_oda       = $acum_oda + $value->valor_oda;
                    $acum_total     = $acum_total + $total;
                    $acum_honorario = $acum_honorario + $honorario;
                    $xcont++;

                    $sheet->cell('B' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue(substr($value->agenda->fechaini, 0, 10));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('C' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue(substr($value->agenda->fechaini, 11, 5));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('D' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->agenda->paciente->apellido1 . ' ' . $value->agenda->paciente->apellido2 . ' ' . $value->agenda->paciente->nombre1 . ' ' . $value->agenda->paciente->nombre2);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('E' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->agenda->cortesia);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('F' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->usercrea->apellido1 . " " . $value->usercrea->nombre1);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('G' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        if ($value->agenda->doctor1 != null) {
                            $cell->setValue($value->agenda->doctor1->apellido1 . " " . $value->agenda->doctor1->nombre1);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        }
                    });
                    $sheet->cell('H' . $i, function ($cell) use ($value) {

                        // manipulate the cel
                        
                        if(!is_null($value->agenda->doctor1)){
                            if($value->agenda->doctor1->id == '4444444444'){
                                $cell->setValue("LABORATORIO");
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            }elseif($value->agenda->proc_consul == 0) {
                                $cell->setValue("CONSULTA");
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            }elseif ($value->agenda->proc_consul == 1) {
                                $procedimiento = "";
                                if ($value->agenda->proc_consul == 1) {
                                    $pro_c = \Sis_medico\Procedimiento::find($value->agenda->id_procedimiento);
                                    if (!is_null($pro_c)) {
                                        $procedimiento = $pro_c->observacion . '+';
                                    }
                                    $proced_total = \Sis_medico\AgendaProcedimiento::where('id_agenda', $value->id_agenda)->get();
                                    foreach ($proced_total as $value_pro) {
                                        $procedimiento = $procedimiento . $value_pro->procedimiento->observacion . '+';
                                    }
                                    $procedimiento = substr($procedimiento, 0, -1);
                                }
                                $cell->setValue($procedimiento);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            }
                        }

                        // if ($value->agenda->proc_consul == 0) {
                        //     $cell->setValue("CONSULTA");
                        //     $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        // } elseif ($value->agenda->proc_consul == 1) {
                        //     $procedimiento = "";
                        //     if ($value->agenda->proc_consul == 1) {
                        //         $pro_c = \Sis_medico\Procedimiento::find($value->agenda->id_procedimiento);
                        //         if (!is_null($pro_c)) {
                        //             $procedimiento = $pro_c->observacion . '+';
                        //         }
                        //         $proced_total = \Sis_medico\AgendaProcedimiento::where('id_agenda', $value->id_agenda)->get();
                        //         foreach ($proced_total as $value_pro) {
                        //             $procedimiento = $procedimiento . $value_pro->procedimiento->observacion . '+';
                        //         }
                        //         $procedimiento = substr($procedimiento, 0, -1);
                        //     }
                        //     $cell->setValue($procedimiento);
                        //     $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        // }
                    });
                    //dd($value->agenda);

                    $sheet->cell('I' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        if (!is_null($value->id_seguro)) {
                            $seguro_recibo = \Sis_medico\Seguro::where('id', $value->id_seguro)->first();
                            $cell->setValue($seguro_recibo->nombre);
                        } else {
                            $cell->setValue($value->agenda->seguro->nombre);
                        }
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('J' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        if ($value->agenda->tipo_cita == 0) {
                            $cell->setValue("PRIMERA VEZ");
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        } else {
                            $cell->setValue("CONSECUTIVO");
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        }
                    });
                    $sheet->cell('K' . $i, function ($cell) use ($value, $referencia) {
                        // manipulate the cel
                        $cell->setValue($referencia);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('L' . $i, function ($cell) use ($value, $efectivo) {
                        // manipulate the cel
                        $cell->setValue(sprintf("%.2f", $efectivo));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('M' . $i, function ($cell) use ($value, $tcredito) {
                        // manipulate the cel
                        $cell->setValue(sprintf("%.2f", $tcredito));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    // $sheet->cell('N' . $i, function ($cell) use ($value, $p7) {
                    //     // manipulate the cel
                    //     $cell->setValue(sprintf("%.2f", $p7));
                    //     $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    // });
                    // $sheet->cell('O' . $i, function ($cell) use ($value, $p2) {
                    //     // manipulate the cel
                    //     $cell->setValue(sprintf("%.2f", $p2));
                    //     $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    // });
                    $sheet->cell('N' . $i, function ($cell) use ($value, $tran) {
                        // manipulate the cel
                        $cell->setValue(sprintf("%.2f", $tran));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('O' . $i, function ($cell) use ($value, $cheque) {
                        // manipulate the cel
                        $cell->setValue(sprintf("%.2f", $cheque));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('P' . $i, function ($cell) use ($value, $datalink) {
                        // manipulate the cel
                        $cell->setValue(sprintf("%.2f", $datalink));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('Q' . $i, function ($cell) use ($value, $pago_app) {
                        // manipulate the cel
                        $cell->setValue(sprintf("%.2f", $pago_app));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('R' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue(sprintf("%.2f", $value->valor_oda));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('S' . $i, function ($cell) use ($value, $total) {
                        // manipulate the cel
                        $cell->setValue(sprintf("%.2f", $total));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('T' . $i, function ($cell) use ($value, $honorario) {
                        // manipulate the cel
                        $cell->setValue(sprintf("%.2f", $honorario));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('U' . $i, function ($cell) use ($value) {
                        // manipulate the cel                                         
                        if (!is_null($value->pagos->first())) {
                            $tipo_tarjeta = \Sis_medico\Ct_Tipo_Tarjeta::find($value->pagos->first()->tipo_tarjeta);
                            if (!is_null($tipo_tarjeta)) {
                                $cell->setValue($tipo_tarjeta->nombre);
                            } else {
                                $cell->setValue("");
                            }
                        }
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('V' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        if (!is_null($value->pagos->first())) {
                            $banco = \Sis_medico\Ct_Bancos::find($value->pagos->first()->banco);
                            if (!is_null($banco)) {
                                $cell->setValue($banco->nombre);
                            } else {
                                $cell->setValue("");
                            }
                        }
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('W' . $i, function ($cell) use ($value, $honorario) {
                        // manipulate the cel
                        $cell->setValue(sprintf("%.2f", $value->id));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('X' . $i, function ($cell) use ($value, $honorario) {
                        // manipulate the cel
                        $cell->setValue($value->observacion);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('Y' . $i, function ($cell) use ($value, $honorario) {
                        // manipulate the cel
                        $cell->setValue($value->agenda->paciente->origen);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('Z' . $i, function ($cell) use ($value, $honorario) {
                        // manipulate the cel
                        $cell->setValue($value->agenda->paciente->referido);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('AA' . $i, function ($cell) use ($value, $honorario) {
                        // manipulate the cel
                        $cell->setValue($value->agenda->paciente->origen2);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $i++;
                }
                $x = $i;
                $sheet->cell('L' . $x, function ($cell) use ($acum_efectivo) {
                    // manipulate the cel
                    $cell->setValue(sprintf("%.2f", $acum_efectivo));
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('M' . $x, function ($cell) use ($acum_tcredito) {
                    // manipulate the cel
                    $cell->setValue(sprintf("%.2f", $acum_tcredito));
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                // $sheet->cell('N' . $x, function ($cell) use ($acum_p7) {
                //     // manipulate the cel
                //     $cell->setValue(sprintf("%.2f", $acum_p7));
                //     $cell->setBorder('thin', 'thin', 'thin', 'thin');
                // });
                // $sheet->cell('O' . $x, function ($cell) use ($acum_p2) {
                //     // manipulate the cel
                //     $cell->setValue(sprintf("%.2f", $acum_p2));
                //     $cell->setBorder('thin', 'thin', 'thin', 'thin');
                // });
                $sheet->cell('N' . $x, function ($cell) use ($acum_tran) {
                    // manipulate the cel
                    $cell->setValue(sprintf("%.2f", $acum_tran));
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('O' . $x, function ($cell) use ($acum_cheque) {
                    // manipulate the cel
                    $cell->setValue(sprintf("%.2f", $acum_cheque));
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('P' . $x, function ($cell) use ($acum_datalink) {
                    // manipulate the cel
                    $cell->setValue(sprintf("%.2f", $acum_datalink));
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('Q' . $x, function ($cell) use ($acum_pago_app) {
                    // manipulate the cel
                    $cell->setValue(sprintf("%.2f", $acum_pago_app));
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('R' . $x, function ($cell) use ($acum_oda) {
                    // manipulate the cel
                    $cell->setValue(sprintf("%.2f", $acum_oda));
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('S' . $x, function ($cell) use ($acum_total) {
                    // manipulate the cel
                    $cell->setValue(sprintf("%.2f", $acum_total));
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('T' . $x, function ($cell) use ($acum_honorario) {
                    // manipulate the cel
                    $cell->setValue(sprintf("%.2f", $acum_honorario));
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $x++;

                $sheet->cell('B' . $x, function ($cell) {
                    // manipulate the cel
                    $fecha = date("Y-m-d H:i:s");
                    $cell->setValue("FECHA IMPRESION: " . $fecha);
                });
            });
        })->export('xlsx');
    }

    //Completa Proceso
    /*public function completa_proceso_cierrecaja($id_product,$id_orden,$id_orden){

    //dd($id_product,$id_orden);

    $orden_venta_det =  Ct_Orden_Venta::where('ct_orden_venta.id',$id_orden)
    ->select('ct_orden_venta.id_seguro as seguro', 'ct_orden_venta.id_nivel as nivel')
    ->first();

    $existe_prod_paquete = Ct_productos_paquete::where('ct_productos_paquete.id_producto',$id_product)
    ->join('ct_producto_tarifario_paquete as ptq', 'ptq.id_producto_paquete', 'ct_productos_paquete.id')
    ->where('ct_productos_paquete.estado','1')
    ->where('ptq.id_seguro',$request['id_seguro'])
    ->where('ptq.id_nivel',$request['id_nivel'])
    ->where('ptq.estado','1')
    ->select('ct_productos_paquete.id as id_prod_paquete','ptq.id as id_prod_tar_paquete','ptq.precio as precio','ptq.id_seguro as id_seguro','ptq.id_nivel as id_nivel','ct_productos_paquete.id_producto as id_producto','ct_productos_paquete.nombre as nomb_paquete')
    ->get();

    }*/
    public function cierre_caja(Request $request)
    {
        config(['data'=>[]]);
        $data['controlador'] = $this->controlador;
        config(['data' => $data]);
        $id_empresa = $request->session()->get('id_empresa');
        $idusuario  = Auth::user()->id;
        $rol_usuario = Auth::user()->id_tipo_usuario;
        $empresa    = Empresa::where('prioridad_labs', '1')->first();
        $dateToday  = date('Y-m-d');
        if ($request['fecha'] != null) {
            $dateToday = date('Y-m-d', strtotime($request['fecha']));
        }
        //$cierre      = CierreCaja::where('cierre_caja.estado', '1')->join('examen_orden as eo','cierre_caja.id_orden','eo.id')->whereNotIn('eo.id_seguro',['2','3','5','6','4'])->whereDate('cierre_caja.fecha', $dateToday)->where('cierre_caja.id_usuariocrea', $idusuario)->orderBy('fecha', 'ASC')->get();//dd($cierre);
        $cierre = [];
        $ingresoCaja = CierreCaja::whereDate('cierre_caja.fecha', $dateToday)->where('cierre_caja.tipo', '0')->first(); //dd($ingresoCaja);
        $cierreFinal = CierreCaja::whereDate('fecha', $dateToday)->where('tipo', '4')->where('id_usuariocrea', $idusuario)->first(); //dd($cierreFinal);
        $datosFinal  = $this->getLast($idusuario, $dateToday, $request); //dd($datosFinal);
        return view('contable.cierre_caja.index', ['empresa' => $empresa, 'cierre' => $cierre, 'ingresoCaja' => $ingresoCaja, 'cierreFinal' => $cierreFinal, 'fecha' => $dateToday, 'datosFinal' => $datosFinal, 'rol_usuario' => $rol_usuario]);
    }
    public function store_cierre(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        if ($request['inicial'] == '0') {
            CierreCaja::create([
                'fecha'           => $request['fechaTime'],
                'tipo'            => '0',
                'descripcion'     => $request['observacionTime'],
                'valor'           => $request['valorTime'],
                'saldo'           => $request['valorTime'],
                'estado'          => '1',
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
            ]);
        } else {
            $saldo = CierreCaja::whereDate('fecha', date('Y-m-d'))->latest()->first();
            $total = $saldo->saldo + $request['valorTime'];
            CierreCaja::create([
                'fecha'           => $request['fechaTime'],
                'tipo'            => '1',
                'descripcion'     => $request['observacionTime'],
                'valor'           => $request['valorTime'],
                'saldo'           => $total,
                'estado'          => '1',
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
            ]);
        }

        return redirect()->route('c_caja.index');
    }
    public function store_salida(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $saldo = CierreCaja::whereDate('fecha', date('Y-m-d'))->latest()->first();
        CierreCaja::create([
            'fecha'           => $request['fechacierre'],
            'tipo'            => '4',
            'descripcion'     => $request['observacionTime2'],
            'valor'           => $request['valorcierre'],
            'saldo'           => $request['valorcierre'],
            'estado'          => '1',
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
        ]);
        return redirect()->route('c_caja.index');
    }
    public function modalrecibo($id, Request $request)
    {
        $orden         = Examen_Orden::find($id);
        $cliente       = Ct_Clientes::where('identificacion', $id)->where('estado', '1')->first();
        $recargo_valor = $orden->detalle_forma_pago->sum('p_fi');
        $valor_forma   = $orden->detalle_forma_pago->sum('valor');
        $total_forma   = $valor_forma + $recargo_valor;
        return view('contable.cierre_caja.modal', ['id_orden' => $id, 'orden' => $orden, 'cliente' => $cliente, 'recargo_valor' => $recargo_valor, 'valor_forma' => $valor_forma, 'total_forma' => $total_forma]);
    }
    public function storeLabs(Request $request)
    {

        $saldo      = CierreCaja::whereDate('fecha', date('Y-m-d'))->latest()->first();
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        if (!is_null($saldo)) {
            $total = $saldo->saldo + $request['total'];
        } else {
            $total = 0;
        }
        $orden = Examen_Orden::find($request['id_orden']);
        if (is_null($orden)) {
            return response()->json("error");
        }
        $paciente = Paciente::find($request['id_paciente']);
        if (is_null($request['id_paciente'])) {
            $request['id_paciente'] = $idusuario;
        }
        $nseguro = "";
        if (!is_null($paciente)) {
            $nseguro = $paciente->seguro->id;
        }

        $valid      = CierreCaja::whereDate('fecha', date('Y-m-d'))->where('tipo', '4')->where('id_usuariocrea', $idusuario)->first();
        $othervalid = CierreCaja::where('id_orden', $request['id_orden'])->first();
        if (is_null($othervalid) && is_null($valid)) {
            $idcierre = CierreCaja::insertGetid([
                'fecha'           => date('Y-m-d H:m:s'),
                'tipo'            => '1',
                'id_paciente'     => $request['id_paciente'],
                'id_seguro'       => $nseguro,
                'descripcion'     => 'El examen orden : ' . $request['id_orden'] . ' paciente: ' . $paciente->apellido1 . ' ' . $paciente->nombre1,
                'valor'           => $request['total'],
                'saldo'           => $total,
                'id_orden'        => $request['id_orden'],
                'estado'          => '1',
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
            ]);
        }

        return response()->json("ok");
    }
    public function getData(Request $request)
    {

        $rolUsuario = Auth::user()->id_tipo_usuario;
        $idusuario       = Auth::user()->id;
        $today           = date('Y-m-d', strtotime($request['fecha']));
        $ct_fecha_desde = $request['ct_fecha_desde'];
        $ct_fecha_hasta = $request['ct_fecha_hasta'];
        $draw            = $request->get('draw');
        $start           = $request->get("start");
        $rowperpage      = $request->get("length"); // Rows display per page
        $columnIndex_arr = $request->get('order');
        $columnName_arr  = $request->get('columns');
        $order_arr       = $request->get('order');
        $search_arr      = $request->get('search');

        $columnIndex     = $columnIndex_arr[0]['column']; // Column index
        $columnName      = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue     = $search_arr['value']; // Search value
        $records         = [];
        $id_user         = $request['idUser'];
        $id_seguro       = $request['idSeguro'];

        if ($ct_fecha_desde == null) {
            $ct_fecha_desde = date('Y-m-d');
        }
        if ($ct_fecha_hasta == null) {
            $ct_fecha_hasta = date('Y-m-d');
        }
        // Total records


        //$totalRecords           = CierreCaja::where('estado', '1')->where('tipo', '<>', '3')->whereDate('fecha', $today)->select('count(*) as allcount')->count();
        //$totalRecordswithFilter = CierreCaja::where('estado', '1')->where('tipo', '<>', '3')->whereDate('fecha', $today)->select('count(*) as allcount')->where('descripcion', 'like', '%' . $searchValue . '%')->count();
        //dd($totalRecords,$totalRecordswithFilter,$searchValue);

        $records  = CierreCaja::where('cierre_caja.estado', '1')
            ->join('examen_orden as eo', 'eo.id', 'cierre_caja.id_orden')
            ->leftjoin('examen_detalle_forma_pago as ep', 'ep.id_examen_orden', 'cierre_caja.id_orden')
            ->where('eo.estado', 1)
            ->whereDate('cierre_caja.fecha', $today)
            ->where('cierre_caja.tipo', '<>', '4')
            ->whereNotIn('eo.id_seguro', ['2', '3', '5', '6', '4']);

        $rolUsuario = Auth::user()->id_tipo_usuario;
        $usuario_permiso = Agenda_Permiso::where('id_usuario', $idusuario)->where('estado', '1')->where('cierre_caja_labs', '1')->first();
        if (in_array($rolUsuario, array(1,20)) == true || !is_null($usuario_permiso)) {
           
            $records  = CierreCaja::where('cierre_caja.estado', '1')
                ->join('examen_orden as eo', 'eo.id', 'cierre_caja.id_orden')
                ->leftjoin('examen_detalle_forma_pago as ep', 'ep.id_examen_orden', 'cierre_caja.id_orden')
                ->where('eo.estado', 1)
                ->whereBetween('cierre_caja.fecha', [$ct_fecha_desde . ' 00:00:00', $ct_fecha_hasta . ' 23:59:59'])
                ->where('cierre_caja.tipo', '<>', '4')
                ->whereNotIn('eo.id_seguro', ['2', '3', '5', '6', '4']);
        }


        $totalRecords = $records->count();
        $totalRecordswithFilter = $totalRecords;
        if ($searchValue != null) {
            $totalRecordswithFilter = $records->where('cierre_caja.descripcion', 'like', '%' . $searchValue . '%')->count();
        }
        //dd($totalRecords,$totalRecordswithFilter);    
        //dd($rowperpage);
        if ($rowperpage == '-1') {

            if (isset($id_user) && $id_user != "null") {
                $records = $records
                    ->where('cierre_caja.id_usuariocrea', $id_user);
            }
            if (isset($id_seguro) && $id_seguro != "null") {
                $records = $records
                    ->where('eo.id_seguro', $id_seguro);
            }
            if (isset($request['ordenid']) && $request['ordenid'] != null) {
                $records->where('cierre_caja.id_orden', $request['ordenid']);
            }
        }

        $records = $records->orderBy($columnName, $columnSortOrder)
            ->select('cierre_caja.*')
            ->distinct('cierre_caja.id_orden')
            ->get();

        // Fetch records burguer king para chilan 
        /*if ($searchValue != null) {
            if ($rowperpage == '-1') {
                $records = CierreCaja::where('cierre_caja.estado', '1')
                    ->join('examen_orden as eo','eo.id','cierre_caja.id_orden')        
                    ->leftjoin('examen_detalle_forma_pago as ep', 'ep.id_examen_orden', 'cierre_caja.id_orden')
                    ->whereDate('cierre_caja.fecha', $today)
                    ->orderBy($columnName, $columnSortOrder)
                    ->select('cierre_caja.*')
                    ->distinct();
                    //->get();
                if (isset($id_user) && $id_user != "null") {
                    $records = CierreCaja::where('cierre_caja.estado', '1')
                        ->join('examen_orden as eo','eo.id','cierre_caja.id_orden')
                        ->leftjoin('examen_detalle_forma_pago as ep', 'ep.id_examen_orden', 'cierre_caja.id_orden')
                        ->whereDate('cierre_caja.fecha', $today)
                        ->where('cierre_caja.id_usuariocrea', $id_user)
                        ->orderBy($columnName, $columnSortOrder)
                        ->select('cierre_caja.*')
                        ->distinct();
                        //->get();
                }
                if (isset($id_seguro) && $id_seguro != "null") {
                    $records = $records->where('eo.id_seguro', $id_seguro);                   
                }    
                $records = $records->get();
            } else {
                $records = CierreCaja::where('cierre_caja.estado', '1')
                    ->join('examen_orden as eo','eo.id','cierre_caja.id_orden')
                    ->leftjoin('examen_detalle_forma_pago as ep', 'ep.id_examen_orden', 'cierre_caja.id_orden')
                    ->whereDate('cierre_caja.fecha', $today)
                    ->select('cierre_caja.*')
                    ->distinct()
                    ->orderBy($columnName, $columnSortOrder)
                    ->skip($start)
                    ->take($rowperpage);
                    //->get();

                if (isset($id_seguro) && $id_seguro != "null") {
                    $records = $records->where('eo.id_seguro', $id_seguro);                   
                }    

                $records = $records->get();    
            }
        }*/

        $data_arr = array();
        $sno      = $start + 1;
        $contador = 0;
        foreach ($records as $record) {
            $estadoDocs =      Orden_documento::where('id_examen_orden',$record->id_orden)->first();
            //dd($record);
            $id                 = date('d/m/Y H:i:s', strtotime($record->fecha));
            $orden              = Examen_Orden::find($record->id_orden);
            $nivelResultado     =  $orden->nivel;
            //$forma_pago         = Examen_Detalle_Forma_Pago::where('id_examen_orden', $record->id_orden)->get();
            $forma_pago         = $orden->detalle_forma_pago; //dd($forma_pago, $forma_pago2);
            $xdata              = "";

            //$dataEfectivo       = Examen_Detalle_Forma_Pago::where('id_examen_orden', $record->id_orden)->where('id_tipo_pago', '1')->sum('valor');
            $dataEfectivo       = $orden->detalle_forma_pago->where('id_tipo_pago', '1')->sum('valor');

            //$dataCheque         = Examen_Detalle_Forma_Pago::where('id_examen_orden', $record->id_orden)->where('id_tipo_pago', '2')->sum('valor');
            $dataCheque         = $orden->detalle_forma_pago->where('id_tipo_pago', '2')->sum('valor');

            //$dataDeposito       = Examen_Detalle_Forma_Pago::where('id_examen_orden', $record->id_orden)->where('id_tipo_pago', '3')->sum('valor');
            $dataDeposito       = $orden->detalle_forma_pago->where('id_tipo_pago', '3')->sum('valor');

            //$dataTarjetaCredito_1 = Examen_Detalle_Forma_Pago::where('id_examen_orden', $record->id_orden)->where('id_tipo_pago', '4')->sum('valor');
            $dataTarjetaCredito_1 = $orden->detalle_forma_pago->where('id_tipo_pago', '4')->sum('valor');

            //$dataTarjetaCredito_2 = Examen_Detalle_Forma_Pago::where('id_examen_orden', $record->id_orden)->where('id_tipo_pago', '4')->sum('p_fi');
            $dataTarjetaCredito_2 = $orden->detalle_forma_pago->where('id_tipo_pago', '4')->sum('p_fi');

            $dataTarjetaCredito   = $dataTarjetaCredito_1 + $dataTarjetaCredito_2;

            //$dataTransferencia  = Examen_Detalle_Forma_Pago::where('id_examen_orden', $record->id_orden)->where('id_tipo_pago', '5')->sum('valor');
            $dataTransferencia  = $orden->detalle_forma_pago->where('id_tipo_pago', '5')->sum('valor');

            //$dataTarjetaDebito_1  = Examen_Detalle_Forma_Pago::where('id_examen_orden', $record->id_orden)->where('id_tipo_pago', '6')->sum('valor');
            $dataTarjetaDebito_1  = $orden->detalle_forma_pago->where('id_tipo_pago', '6')->sum('valor');

            //$dataTarjetaDebito_2  = Examen_Detalle_Forma_Pago::where('id_examen_orden', $record->id_orden)->where('id_tipo_pago', '6')->sum('p_fi');
            $dataTarjetaDebito_2  = $orden->detalle_forma_pago->where('id_tipo_pago', '6')->sum('p_fi');

            $dataTarjetaDebito    = $dataTarjetaDebito_1 + $dataTarjetaDebito_2;

            //$dataPendientePago  = Examen_Detalle_Forma_Pago::where('id_examen_orden', $record->id_orden)->where('id_tipo_pago', '7')->sum('valor');
            $dataPendientePago  = $orden->detalle_forma_pago->where('id_tipo_pago','7')->sum('valor');
            $dataDatalink       = $orden->detalle_forma_pago->where('id_tipo_pago','9')->sum('valor');

            if($orden->pago_online == 1){
                $ponline = $orden->total_valor;
            }else{
                $ponline = 0;
            }
            if($orden->cobrar_pac_pct < 100){
                $valor_oda = $orden->valor - $orden->valor_con_oda;
            }else{
                $valor_oda = 0;
            }

            $paciente           = Paciente::find($record->id_paciente);
            $usuario            = User::find($record->id_usuariocrea);
            $seguro             = null;
            $id_orden           = null;
            if (!is_null($orden)) {
                $seguro   = Seguro::find($orden->id_seguro);
                $paciente = Paciente::find($orden->id_paciente);
                $id_orden = $orden->id;
                if ($dataTarjetaCredito > 0) {
                    //tuve que setear los valores de esta manera por el valor
                    //$dataTarjetaCredito = $orden->total_valor;
                }
                if ($dataTarjetaDebito > 0) {
                    //tuve que setear los valores de esta manera por equivocacion martes 20 de julio del 2021
                    //$dataTarjetaDebito = $orden->total_valor;
                }
            }
            //$pl = "1"; //acuerdate de esto siempre
            //$datesFinally = "Martes 20 de Julio del 2021";
            $nseguro = "";
            if (!is_null($seguro)) {
                $nseguro = $seguro->nombre;
            }

            $facturado = "No Facturado";
            if ($orden != null) {
                if ($orden->comprobante != null && $orden->fecha_envio != null) {
                    $facturado = "Facturado";
                }
                if ($orden->seguro->tipo == 0) {
                    if ($dataPendientePago == 0) {
                        $facturado         = "Orden Publica";
                        $dataPendientePago = $orden->total_valor;
                    }
                }
            } else {
                if ($record->tipo == '4') {
                    $facturado = "Cierre Caja";
                } else if ($record->tipo == '0') {
                    $facturado = "Inicio de Caja";
                }
            }

            $nombre = "";
            if (!is_null($paciente)) {
                $nombre = $paciente->apellido1 . ' ' . $paciente->apellido2 . ' ' . $paciente->nombre1;
            } else {
            }
            if ($record->tipo == '0') {
                $xdata = "Inicio de caja";
            } elseif ($record->tipo == '1') {
                $xdata = "Ingreso de caja";
            } elseif ($record->tipo == '2') {
                $xdata = "Salida de caja";
            } elseif ($record->tipo == '4') {
                $xdata = "Cierre de caja";
            }

            if ($request['facturado'] == '1') {
                //facturado
                if ($facturado == "Facturado") {
                    if ($orden != null) {

                        if ($orden->estado == 0) {
                        } else {
                            $data_arr[] = array(
                                'nivelDocs'          => $nivelResultado,
                                "fecha"              => $id,
                                "descripcion"        => $record->descripcion,
                                "observacion"        => $record->observacion,
                                "tipo"               => $xdata,
                                "valor"              => $record->valor,
                                "saldo"              => $record->saldo,
                                "orden"              => $orden,
                                "id_orden"           => $id_orden,
                                "dataEfectivo"       => $dataEfectivo,
                                "dataCheque"         => $dataCheque,
                                "dataDeposito"       => $dataDeposito,
                                "dataTransferencia"  => $dataTransferencia,
                                "dataTarjetaCredito" => $dataTarjetaCredito,
                                "dataTarjetaDebito"  => $dataTarjetaDebito,
                                "dataPendientePago"  => $dataPendientePago,
                                "forma_pago"         => $forma_pago,
                                "paciente"           => $nombre,
                                "seguro"             => $nseguro,
                                "numero"             => $orden->comprobante,
                                "facturado"          => $facturado,
                                "usuario"            => substr($usuario->nombre1, 0, 1) . '. ' . $usuario->apellido1,
                                "oda"                => $valor_oda,
                                "online"             => $ponline,
                                'estadoDocs'         => $estadoDocs,
                                "dataDatalink"       => $dataDatalink,
                            );
                        }
                    } else {
                        $orden      = [];
                        $forma_pago = [];
                        if ($xdata == "Inicio de caja") {
                            $dataEfectivo = $record->valor;
                        }
                        if ($xdata == "Cierre de caja") {
                            $dataEfectivo = $record->valor;
                        }

                        $dataPendientePago = 0.00;
                        $dataTarjetaDebito = 0.00;
                        $dataTransferencia = 0.00;
                        $dataDeposito      = 0.00;
                        $dataCheque        = 0.00;
                        $data_arr[]        = array(
                            'nivelDocs'          => $nivelResultado,
                            "fecha"              => $id,
                            "descripcion"        => $record->descripcion,
                            "observacion"        => $record->observacion,
                            "tipo"               => $xdata,
                            "id_orden"           => $id_orden,
                            "valor"              => $record->valor,
                            "saldo"              => $record->saldo,
                            "dataEfectivo"       => $dataEfectivo,
                            "dataCheque"         => $dataCheque,
                            "dataDeposito"       => $dataDeposito,
                            "dataTransferencia"  => $dataTransferencia,
                            "dataTarjetaCredito" => $dataTarjetaCredito,
                            "dataTarjetaDebito"  => $dataTarjetaDebito,
                            "dataPendientePago"  => $dataPendientePago,
                            "orden"              => $orden,
                            "forma_pago"         => $forma_pago,
                            "paciente"           => $nombre,
                            "seguro"             => $nseguro,
                            "facturado"          => $facturado,
                            "numero"             => "",
                            "oda"                => $valor_oda,
                            "usuario"            => substr($usuario->nombre1,0,1) . '. ' . $usuario->apellido1,
                            "online"             => $ponline,
                            'estadoDocs'         => $estadoDocs,
                            "dataDatalink"       => $dataDatalink,
                        );
                    }
                }
            } elseif ($request['facturado'] == '2') {
                // no facturado
                if ($facturado == "No Facturado") {
                    if ($orden != null) {
                        if ($orden->estado == 0) {
                        } else {
                            $data_arr[] = array(
                                'nivelDocs'          => $nivelResultado,
                                "fecha"              => $id,
                                "descripcion"        => $record->descripcion,
                                "observacion"        => $record->observacion,
                                "tipo"               => $xdata,
                                "valor"              => $record->valor,
                                "saldo"              => $record->saldo,
                                "orden"              => $orden,
                                "id_orden"           => $id_orden,
                                "dataEfectivo"       => $dataEfectivo,
                                "dataCheque"         => $dataCheque,
                                "dataDeposito"       => $dataDeposito,
                                "dataTransferencia"  => $dataTransferencia,
                                "dataTarjetaCredito" => $dataTarjetaCredito,
                                "dataTarjetaDebito"  => $dataTarjetaDebito,
                                "dataPendientePago"  => $dataPendientePago,
                                "forma_pago"         => $forma_pago,
                                "paciente"           => $nombre,
                                "seguro"             => $nseguro,
                                "numero"             => $orden->comprobante,
                                "facturado"          => $facturado,
                                "usuario"            => substr($usuario->nombre1, 0, 1) . '. ' . $usuario->apellido1,
                                "oda"                => $valor_oda,
                                "online"             => $ponline,
                                'estadoDocs'         => $estadoDocs,
                                "dataDatalink"       => $dataDatalink,
                            );
                        }
                    } else {
                        $orden      = [];
                        $forma_pago = [];
                        if ($xdata == "Inicio de caja") {
                            $dataEfectivo = $record->valor;
                        }
                        if ($xdata == "Cierre de caja") {
                            $dataEfectivo = $record->valor;
                        }

                        $dataPendientePago = 0.00;
                        $dataTarjetaDebito = 0.00;
                        $dataTransferencia = 0.00;
                        $dataDeposito      = 0.00;
                        $dataCheque        = 0.00;
                        $data_arr[]        = array(
                            'nivelDocs'          => $nivelResultado,
                            "fecha"              => $id,
                            "descripcion"        => $record->descripcion,
                            "observacion"        => $record->observacion,
                            "tipo"               => $xdata,
                            "id_orden"           => $id_orden,
                            "valor"              => $record->valor,
                            "saldo"              => $record->saldo,
                            "dataEfectivo"       => $dataEfectivo,
                            "dataCheque"         => $dataCheque,
                            "dataDeposito"       => $dataDeposito,
                            "dataTransferencia"  => $dataTransferencia,
                            "dataTarjetaCredito" => $dataTarjetaCredito,
                            "dataTarjetaDebito"  => $dataTarjetaDebito,
                            "dataPendientePago"  => $dataPendientePago,
                            "orden"              => $orden,
                            "forma_pago"         => $forma_pago,
                            "paciente"           => $nombre,
                            "seguro"             => $nseguro,
                            "facturado"          => $facturado,
                            "numero"             => "",
                            "oda"                => $valor_oda,
                            "usuario"            => substr($usuario->nombre1,0,1) . '. ' . $usuario->apellido1,
                            "online"             => $ponline,
                            'estadoDocs'         => $estadoDocs,
                            "dataDatalink"       => $dataDatalink,
                        );
                    }
                }
            } elseif ($request['facturado'] == null) {
                if ($orden != null) {

                    if ($orden->estado == 0) {
                    } else {
                        if ($idusuario == '1316262193') {
                            if ($contador == 32) {
                                //dd($records);
                            }
                        }
                        $data_arr[] = array(
                            'nivelDocs'          => $nivelResultado,
                            "fecha"              => $id,
                            "descripcion"        => $record->descripcion,
                            "observacion"        => $record->observacion,
                            "tipo"               => $xdata,
                            "valor"              => $record->valor,
                            "saldo"              => $record->saldo,
                            "orden"              => $orden,
                            "id_orden"           => $id_orden,
                            "dataEfectivo"       => $dataEfectivo,
                            "dataCheque"         => $dataCheque,
                            "dataDeposito"       => $dataDeposito,
                            "dataTransferencia"  => $dataTransferencia,
                            "dataTarjetaCredito" => $dataTarjetaCredito,
                            "dataTarjetaDebito"  => $dataTarjetaDebito,
                            "dataPendientePago"  => $dataPendientePago,
                            "forma_pago"         => $forma_pago,
                            "paciente"           => $nombre,
                            "seguro"             => $nseguro,
                            "numero"             => $orden->comprobante,
                            "facturado"          => $facturado,
                            "usuario"            => substr($usuario->nombre1,0,1) . '. ' . $usuario->apellido1,
                                "oda"                => $valor_oda,
                            "online"             => $ponline,
                            'estadoDocs'         => $estadoDocs,
                            "dataDatalink"       => $dataDatalink,
                        );
                    }
                } else {
                    $orden = [];

                    $forma_pago = [];
                    if ($xdata == "Inicio de caja") {
                        $dataEfectivo = $record->valor;
                    }
                    if ($xdata == "Cierre de caja") {
                        $dataEfectivo = $record->valor;
                    }

                    $dataPendientePago = 0.00;
                    $dataTarjetaDebito = 0.00;
                    $dataTransferencia = 0.00;
                    $dataDeposito      = 0.00;
                    $dataCheque        = 0.00;
                    $data_arr[]        = array(
                        'nivelDocs'          => $nivelResultado,
                        "fecha"              => $id,
                        "descripcion"        => $record->descripcion,
                        "observacion"        => $record->observacion,
                        "tipo"               => $xdata,
                        "id_orden"           => $id_orden,
                        "valor"              => $record->valor,
                        "saldo"              => $record->saldo,
                        "dataEfectivo"       => $dataEfectivo,
                        "dataCheque"         => $dataCheque,
                        "dataDeposito"       => $dataDeposito,
                        "dataTransferencia"  => $dataTransferencia,
                        "dataTarjetaCredito" => $dataTarjetaCredito,
                        "dataTarjetaDebito"  => $dataTarjetaDebito,
                        "dataPendientePago"  => $dataPendientePago,
                        "orden"              => $orden,
                        "forma_pago"         => $forma_pago,
                        "paciente"           => $nombre,
                        "seguro"             => $nseguro,
                        "facturado"          => $facturado,
                        "numero"             => "",
                            "oda"                => $valor_oda,
                        "usuario"            => substr($usuario->nombre1,0,1) . '. ' . $usuario->apellido1,
                        "online"             => $ponline,
                        'estadoDocs'         => $estadoDocs,
                            "dataDatalink"       => $dataDatalink,
                    );
                }
            }
            $contador++;
        }

        //dd($d
        $response = array(
            "draw"                 => intval($draw),
            "contador"             => $contador,
            "iTotalRecords"        => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData"               => $data_arr,
        );

        echo json_encode($response);
        exit;
    }
    public function modalforma($id, Request $request)
    {
        if (!is_null($id)) {
            $forma_pago   = Examen_Detalle_Forma_Pago::where('id_examen_orden', $id)->get();
            $tipo_pago    = Ct_Tipo_Pago::all();
            $bancos       = Ct_Bancos::all();
            $tipo_tarjeta = Ct_Tipo_Tarjeta::all();
            return view('contable.cierre_caja.modalforma', ['formapago' => $forma_pago, 'tipo_pago' => $tipo_pago, 'lista_banco' => $bancos, 'tipo_tarjeta' => $tipo_tarjeta]);
        } else {
            return response()->json("error");
        }
    }
    public function redirecciona($id, $valida)
    {
        $orden = Examen_Orden::find($id);

        //$usuarios = User::where('id_tipo_usuario', '3')->where('estado', '1')->where('training', '<>', '1')->orderBy('nombre1')->get();
        //file:///Users/macbook/Downloads/vuexy-admin-6.5/vuexy-admin-v6.5/html-version/vuexy-html-bootstrap-admin-template/html/ltr/horizontal-menu-template/index.html
        $usuarios = User::where('id_tipo_usuario', '3')->where('estado', '1')->orderBy('apellido1')->where('uso_laboratorio', '1')->get();

        $formas = DB::table('forma_de_pago')->where('estado', '1')->get();
        //dd($formas);
        $examenes = Examen::orderBy('id_agrupador')->get();
        //dd($examenes);
        $agrupadores = Examen_Agrupador::where('estado', '1')->get();
        $seguros1    = DB::table('seguros as s')->where('s.inactivo', '1')->where('s.tipo', '>', '1')->orderBy('s.nombre');
        $seguros2    = DB::table('seguros as s')->where('s.inactivo', '1')->where('s.tipo', '>', '0')->join('convenio as c', 'c.id_seguro', 's.id')->select('s.*')->orderBy('s.nombre');
        $seguros     = $seguros1->union($seguros2)->get();
        $protocolos  = Protocolo::where('estado', '1')->get();
        $protocolos2 = Protocolo::where('estado', '3')->get();
        $codigo      = Labs_doc_externos::where('estado', '1')->get();

        $empresas = DB::table('empresa as e')->where('e.estado', '1')->join('convenio as c', 'c.id_empresa', 'e.id')->select('e.id', 'e.nombrecomercial')->groupBy('e.id', 'e.nombrecomercial')->get();
        if ($valida == 0) {
            /*return view('laboratorio/orden/create_particular',['usuarios' => $usuarios, 'examenes' => $examenes, 'agrupadores' => $agrupadores, 'seguros' => $seguros, 'protocolos' => $protocolos, 'empresas' => $empresas]);*/
            return view('laboratorio/orden/editar_cotizacion', ['usuarios' => $usuarios, 'examenes' => $examenes, 'agrupadores' => $agrupadores, 'seguros' => $seguros, 'empresas' => $empresas, 'orden' => $orden, 'formas' => $formas, 'protocolos' => $protocolos, 'codigo' => $codigo, 'protocolos2' => $protocolos2]);
        } else {
            return view('contable/cierre_caja/editforma', ['usuarios' => $usuarios, 'examenes' => $examenes, 'agrupadores' => $agrupadores, 'seguros' => $seguros, 'empresas' => $empresas, 'orden' => $orden, 'formas' => $formas, 'protocolos' => $protocolos, 'codigo' => $codigo, 'protocolos2' => $protocolos2]);
        }
    }
    public function storenew($id, Request $request)
    {
        //funcion para guardar
        $mes     = $request['mes'];
        $anio    = $request['anio'];
        $validar = Examen_Orden::whereMonth('mes', $mes)->whereYear('anio', $anio)->where('estado', '1')->first();
        if (is_null($validar)) {
            $ip_cliente          = $_SERVER["REMOTE_ADDR"];
            $idusuario           = Auth::user()->id;
            $fecha_as            = $request['fecha_asiento'];
            $id_empresa          = $request->session()->get('id_empresa');
            $c_sucursal          = 0;
            $c_caja              = 0;
            $num_comprobante     = 0;
            $nfactura            = 0;
            $proced              = $request['procedimiento'];
            $pac                 = "";
            $id_asiento_cabecera = 0;
            $ver                 = Ct_Ven_Orden::where('orden_venta', $request['orden_venta'])->where('tipo', 'VEN-LABS')->first();
            if (is_null($ver)) {
                $factura_venta = [
                    'sucursal'          => $c_sucursal,
                    'punto_emision'     => $c_caja,
                    'numero'            => $nfactura,
                    'nro_comprobante'   => $request['numero_comprobante'], //numero de comprobante
                    'id_empresa'        => $request['empresa'], // id_empresa
                    'tipo'              => $request['tipo'], // es un campo varchar puede ser VEN-LABS
                    'fecha'             => $request['fecha_asiento'], //fecha de envio
                    'divisas'           => $request['divisas'], // clavale uno
                    'nombre_cliente'    => $request['nombre_cliente'], // nombre del cliente en varchar
                    'tipo_consulta'     => $request['tipo_consulta'], // este puede ser 1 o 0 consulta o procedimiento
                    'id_cliente'        => $request['identificacion_cliente'], //identificacion del cliente
                    'direccion_cliente' => $request['direccion_cliente'], //direccion del cliente
                    'telefono_cliente'  => $request['telefono_cliente'], // telefono del cliente
                    'email_cliente'     => $request['mail_cliente'], //mail del cliene
                    'orden_venta'       => $request['orden_venta'], //el numero de orden, el id de la orden de laboratorio
                    'estado_pago'       => '0', // default 0
                    'id_paciente'       => $request['identificacion_paciente'], // datos del paciente
                    'nombres_paciente'  => $request['nombre_paciente'], //nombre del paciente
                    'seguro_paciente'   => $request['id_seguro'], //seguro del paciente
                    'copago'            => $request['copago'], // valor copago del paciente
                    'id_recaudador'     => $request['cedula_recaudador'], //recaudador pero no es requerido
                    'ci_vendedor'       => $request['cedula_vendedor'], // id del recuadador
                    'vendedor'          => $request['vendedor'], // vendedor pero no es requerido
                    'subtotal_0'        => $request['subtotal_0'], //subtotal 0
                    'subtotal_12'       => $request['subtotal_12'], //subtotal 12
                    'descuento'         => $request['descuento'], // descuento
                    'base_imponible'    => $request['subtotal'], //subtotal
                    'impuesto'          => $request['tarifa_iva'], // el valor del iva
                    'total_final'       => $request['total'], //total de la factura
                    'ip_creacion'       => "vic",
                    'ip_modificacion'   => $ip_cliente,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                ];

                // return $factura_venta;

                $id_venta = Ct_Ven_Orden::insertGetId($factura_venta);
                //$id_venta = 0;
                $arr_total      = [];
                $total_iva      = 0;
                $total_impuesto = 0;
                $total_0        = 0;

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
                return response()->json(['success' => '1', 'id_orden' => $id_venta]);
            }
            return response()->json(['success' => '1', 'id_orden' => '0']);
        } else {
            return response()->json(['success' => '1', 'id_orden' => '0']);
        }
    }

     public function pago_en_linea_contab_02022023($id)
    {
        $empresa = Empresa::where('prioridad_labs',1)->first();
        
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        //PAGO 1 - ESTADO PAGO 1 - PAGO ONLINE 1 - COMPROBANTE NULL - FECHA ENVIO NULL
        $orden = Examen_Orden::find($id);

        if(is_null($empresa)){
            $msn_error = 'NO ENCUENTRA EMPRESA';
            Log_usuario::create([
                'id_usuario'  => $idusuario,
                'ip_usuario'  => $ip_cliente,
                'descripcion' => "LABORATORIO",
                'dato_ant1'   => $orden->id,
                'dato1'       => $orden->id_paciente,
                'dato_ant2'   => "ERROR FACTURA PAGO EN LINEA",
                'dato_ant4'   => $msn_error,
            ]);
            return "error";
        }

        if($empresa->externo == '1'){ //dd("entra");
            $productos = [];
            $cant = 0;
            foreach ($orden->detalles as $value) {
                //se envian los productos
                $producto['sku']       = "LABS-" . $value->examen->id; //ID EXAMEN
                $producto['nombre']    = $value->examen->nombre; // NOMBRE DEL EXAMEN
                $producto['cantidad']  = "1";
                $producto['precio']    = $value->valor; //DETALLE
                $producto['descuento'] = $value->valor_descuento;
                $producto['subtotal']  = $value->valor - $value->valor_descuento; //precio-descuento
                $producto['tax']       = "0";
                $producto['total']     = $value->valor - $value->valor_descuento; //SUBTOTAL
                $producto['copago']    = "0";
                $productos[$cant]      = $producto;
                $cant++;
            }//dd($productos);
            //dd($productos,$orden->detalles);

            /*$orden->update([
                'fecha_envio' => date('Y-m-d H:i:s'),
            ]);*/

            
            $idcierre = CierreCaja::insertGetid([
                'fecha'           => date('Y-m-d H:m:s'),
                'tipo'            => '1',
                'id_paciente'     => $orden->paciente->id,
                'id_seguro'       => $orden->seguro->id,
                'descripcion'     => 'El examen orden : ' . $orden->id . ' paciente: ' . $orden->paciente->apellido1 . ' ' . $orden->paciente->nombre1,
                'valor'           => $orden->total_valor,
                'saldo'           => $orden->total_valor,
                'id_orden'        => $orden->id,
                'estado'          => '1',
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
            ]);

            $appId = "wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=";
            $elemento = json_encode([ 
                "id_orden"   => $id,
                "empresa"    => $empresa->id,
                "sucursal"   => $empresa->establecimiento,
                "caja"       => $empresa->punto_emision,
                //DATOS DEL CLIENTE/////
                "cedula"     => $orden->cedula_factura,
                "tipo"       => '6',
                "nombre"     => $orden->nombre_factura,
                "email"      => $orden->email_factura,
                "telefono"   => $orden->telefono_factura,
                "direccion"  => $orden->direccion_factura,
                "ciudad"     => $orden->ciudad_factura,
                "id_paciente"=> $orden->id_paciente,
                "nombre_pac" => $orden->paciente->apellido1.' '.$orden->paciente->apellido2.' '.$orden->paciente->nombre1.' '.$orden->paciente->nombre2,
                "seguro"     => $orden->seguro->nombre,
                "total_valor"=> $orden->total_valor,  
                "fecha_orden"=> substr($orden->fecha_orden, 0, 10), 
                "fecha_envio"=> substr($orden->fecha_envio, 0, 10), 
                //////////////////////// 
                'productos'  => $productos,
                "token"      => "8c0a00ec19933215dc29225e645ea714",
            ]);//dd($elemento);
            $options = array(
                'http' => array(
                    'header'  => "Content-type: application/json\r\n" . "AppId: " . $appId . "\r\n",
                    'method'  => 'POST',
                    'content' => $elemento,
                ),
                "ssl" => array(
                    "verify_peer" => false,
                    "verify_peer_name" => false,
                ),
            );


            //$url     = "https://ieced.siaam.ec/sis_medico/public/labs_gestion_servicios";
            $url     = "http://ieced.siaam.ec/sis_medico/public/labs_gestion_servicios";
            $context = stream_context_create($options);
            $response = file_get_contents($url, false, $context);//dd($response);
            $respuesta = json_decode($response, true);
            
            if($respuesta['estado'] == 'ok'){
                //dd("fin",$respuesta);
                Log_usuario::create([
                    'id_usuario'  => $idusuario,
                    'ip_usuario'  => $ip_cliente,
                    'descripcion' => "GESTION DE PAGOS EN LINEA ENVIO",
                    'dato_ant1'   => "EMP:".$empresa->id."-SUC:".$empresa->establecimiento."-CAJ:".$empresa->punto_emision,
                    'dato1'       => "ORDEN:".$id,
                    'dato_ant2'   => "RESPUESTA:",
                    'dato2'       => $response,
                    'dato_ant4'   => "-",
                ]);

                $orden->update([
                    'comprobante' => $respuesta['mensaje'],
                    'fecha_envio' => date('Y-m-d H:i:s'),
                ]);
            }
                
            /*Log_usuario::create([
                'id_usuario'  => $idusuario,
                'ip_usuario'  => $ip_cliente,
                'descripcion' => "LABORATORIO",
                'dato_ant1'   => $orden->id,
                'dato1'       => $orden->id_paciente,
                'dato_ant2'   => "FACTURA PAGO EN LINEA",
                'dato_ant4'   => $pagoonline->nro_comprobante,
            ]);*/

        }else{

        $data           = array();
        $cliente        = array();
        $direccion      = array();
        $producto       = array();
        $info_adicional = array();
        $pago           = array();
        $info           = array();
        $tipos_pago     = array();
        $productos      = array();

            //$pagoonline = DB::table('pagosenlinea')->where('clave', $orden->id)->first();
            $pagoonline = DB::table('pagosenlinea')->where('clave', $id)->where('empresa_id',$empresa->id)->where('sucursal',$empresa->establecimiento)->where('caja',$empresa->punto_emision)->first();
            if (!is_null($pagoonline)) {
                if ($pagoonline->nro_comprobante != null) {

                $datavh['empresa']     = '0993075000001';
                $datavh['tipo']        = 'comprobante';
                $datavh['comprobante'] = $pagoonline->nro_comprobante;

                $comprascontroller = new ComprasController();
                $finaly            = json_decode($comprascontroller->estado_comprobante($datavh), true);
                if ($finaly['status']['status'] == 'error') {
                    Log_usuario::create([
                        'id_usuario'  => $idusuario,
                        'ip_usuario'  => $ip_cliente,
                        'descripcion' => "LABORATORIO",
                        'dato_ant1'   => $id,
                        'dato1'       => $orden->id_paciente,
                        'dato_ant2'   => "ERROR FACTURA PAGO EN LINEA",
                        'dato_ant4'   => $finaly['status']['message'],
                    ]);
                    return "error";
                }
                $f_emision         = $finaly['details']['fecha'];
                $f_emision         = date('Y-m-d H:i:s', strtotime($f_emision));

                $data['empresa']     = '0993075000001';
                $data['fecha']       = date('Y-m-d ', strtotime($f_emision)); //ESTA FECHA ES LA DE EMISION
                $data['electronica'] = '1';

                $cliente['cedula'] = $orden->cedula_factura;

                $cliente['tipo'] = '6'; //eduardo dice q el lo calcula y luego se
                if (strlen($orden->cedula_factura) == 13 && substr($orden->cedula_factura, -3) == '001') {
                    $cliente['tipo'] = '4';
                } elseif (strlen($orden->cedula_factura) == 10) {
                    $cliente['tipo'] = '5';
                }

                $cliente['nombre']   = $orden->nombre_factura;
                $cliente['apellido'] = '';

                $explode = explode(" ", $orden->nombre_factura);
                if (count($explode) >= 4) {
                    $cliente['nombre'] = $explode[0] . ' ' . $explode[1];
                    for ($i = 2; $i < count($explode); $i++) {
                        $cliente['apellido'] = $cliente['apellido'] . ' ' . $explode[$i];
                    }
                }
                if (count($explode) == 3) {
                    $cliente['nombre']   = $explode[0];
                    $cliente['apellido'] = $explode[1] . ' ' . $explode[2];
                }
                if (count($explode) == 2) {
                    $cliente['nombre']   = $explode[0];
                    $cliente['apellido'] = $explode[1];
                }
           
                $cliente['email']            = $orden->email_factura;
                $cliente['telefono']         = $orden->telefono_factura;
                $direccion['calle']          = $orden->direccion_factura;
                $direccion['ciudad']         = $orden->ciudad_factura;
                $cliente['direccion']        = $direccion;
                $cliente['nro_autorizacion'] = null;
                $data['cliente']             = $cliente;

                $msn_error  = '';
                $flag_error = false;
                if ($cliente['cedula'] == null) {
                    $flag_error = true;
                    $msn_error  = 'Error en cedula';
                }

                if ($cliente['nombre'] == null) {
                    $flag_error = true;
                    $msn_error  = 'Error en Nombre';
                }
                if ($cliente['email'] == null) {
                    $flag_error = true;
                    $msn_error  = 'Error en email';
                }
                if ($cliente['telefono'] == null) {
                    $flag_error = true;
                    $msn_error  = 'Error en telefono';
                }
                if ($direccion['calle'] == null) {
                    $flag_error = true;
                    $msn_error  = 'Error en calle';
                }
                if ($direccion['ciudad'] == null) {
                   
                    $direccion['ciudad'] = 'GUAYAQUIL';
                }

                $cant = 0;
                foreach ($orden->detalles as $value) {
                    //se envian los productos
                    $producto['sku']       = "LABS-" . $value->examen->id; //ID EXAMEN
                    $producto['nombre']    = $value->examen->nombre; // NOMBRE DEL EXAMEN
                    $producto['cantidad']  = "1";
                    $producto['precio']    = $value->valor; //DETALLE
                    $producto['descuento'] = $value->valor_descuento;
                    $producto['subtotal']  = $value->valor - $value->valor_descuento; //precio-descuento
                    $producto['tax']       = "0";
                    $producto['total']     = $value->valor - $value->valor_descuento; //SUBTOTAL
                    $producto['copago']    = "0";
                    $productos[$cant]      = $producto;
                    $cant++;
                }

                $data['productos'] = $productos;
                /*01  SIN UTILIZACION DEL SISTEMA FINANCIERO
                15  COMPENSACIÓN DE DEUDAS
                16  TARJETA DE DÉBITO
                17  DINERO ELECTRÓNICO
                18  TARJETA PREPAGO
                19  TARJETA DE CRÉDITO
                20  OTROS CON UTILIZACION DEL SISTEMA FINANCIERO
                21  ENDOSO DE TÍTULOS
                 */
                $info_adicional['nombre'] = "AGENTES_RETENCION";
                $info_adicional['valor']  = "Resolucion 1";
                $info[0]                  = $info_adicional;

                $info_adicional['nombre'] = "PACIENTE";
                $info_adicional['valor']  = $orden->id_paciente . ' ' . $orden->paciente->apellido1 . ' ' . $orden->paciente->nombre1;
                $info[1]                  = $info_adicional;

                $info_adicional['nombre'] = "MAIL";
                $info_adicional['valor']  = $orden->email_factura; //EMAIL
                $info[2]                  = $info_adicional;

                $info_adicional['nombre'] = "CIUDAD";
                $info_adicional['valor']  = $orden->ciudad_factura; //EMAIL
                $info[3]                  = $info_adicional;

                $info_adicional['nombre'] = "DIRECCION";
                $info_adicional['valor']  = $orden->direccion_factura; //EMAIL
                $info[4]                  = $info_adicional;

                $info_adicional['nombre'] = "ORDEN";
                $info_adicional['valor']  = '' . $orden->id . ''; //EMAIL
                $info[5]                  = $info_adicional;

                $info_adicional['nombre'] = "SEGURO";
                $info_adicional['valor']  = $orden->seguro->nombre; //SEGURO
                $info[6]                  = $info_adicional;

                $info_adicional['nombre'] = "FORMA_PAGO";
                $info_adicional['valor']  = '';
                $info[7]                  = $info_adicional;

                $pago['forma_pago']            = '01';
                $pago['informacion_adicional'] = $info;
                $pago['dias_plazo']            = '10';
                $data['pago']                  = $pago;
                $data['contable']              = 1; //si se marca 1 crea factura, 0 no crea factura
                $data['laboratorio']           = 1;
                $data['paciente']              = $orden->id_paciente;
                $data['concepto']              = 'Ingreso de Factura Electronica por Pago en Linea';
                $data['copago']                = 0;
                $data['id_seguro']             = '1'; //si se marca 1 se crea un solo producto por id de laboratorioo, 0 graba detalle de datos
                $data['total_factura']         = $orden->total_valor;

                $tipos_pago['id_tipo']            = 5; //metodo de pago efectivo, tarjeta, etc
                $tipos_pago['fecha']              = substr($orden->fecha_orden, 0, 10);
                $tipos_pago['tipo_tarjeta']       = null; //si es efectivo no se envia
                $tipos_pago['numero_transaccion'] = $pagoonline->pago_auth; //si es efectivo no se envia
                $banco                            = Ct_Bancos::where('nombre', $pagoonline->issuer)->first();
                $id_banco                         = '1';
                if (!is_null($banco)) {
                    $id_banco = $banco->id;
                }
                $tipos_pago['id_banco']   = $id_banco; //si es efectivo no se envia
                $tipos_pago['cuenta']     = $pagoonline->credittype . '-' . $pagoonline->paymentmethod; //si es efectivo no se envia
                $tipos_pago['giradoa']    = null; //si es efectivo no se envia
                $tipos_pago['valor']      = $orden->total_valor; //valor a pagar de total
                $tipos_pago['valor_base'] = $orden->total_valor; //valor a pagar de base
                $pagos['tipos_pago']      = $tipos_pago;
                $data['formas_pago']      = $pagos;

                if ($orden->fecha_envio != null) {
                    $flag_error = true;
                    $msn_error  = 'Ya enviado al SRI';
                }

                if ($flag_error) {
                    Log_usuario::create([
                        'id_usuario'  => $idusuario,
                        'ip_usuario'  => $ip_cliente,
                        'descripcion' => "LABORATORIO",
                        'dato_ant1'   => $orden->id,
                        'dato1'       => $orden->id_paciente,
                        'dato_ant2'   => "ERROR FACTURA PAGO EN LINEA",
                        'dato_ant4'   => $msn_error,
                    ]);
                    return "error";
                }

                $orden->update([
                    'fecha_envio' => date('Y-m-d H:i:s'),
                ]);

                $valid = CierreCaja::whereDate('fecha', $orden->fecha_orden)->where('tipo', '4')->first();
                if (is_null($valid)) {
                    $idcierre = CierreCaja::insertGetid([
                        'fecha'           => date('Y-m-d H:m:s'),
                        'tipo'            => '1',
                        'id_paciente'     => $orden->paciente->id,
                        'id_seguro'       => $orden->seguro->id,
                        'descripcion'     => 'El examen orden : ' . $orden->id . ' paciente: ' . $orden->paciente->apellido1 . ' ' . $orden->paciente->nombre1,
                        'valor'           => $orden->total_valor,
                        'saldo'           => $orden->total_valor,
                        'id_orden'        => $orden->id,
                        'estado'          => '1',
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea'  => $idusuario,
                        'id_usuariomod'   => $idusuario,
                    ]);
                }

                //dd($data);
                $envio = ApiFacturacionController::crea_factura_noelec($data, $pagoonline->nro_comprobante);

                $orden->update([
                    'comprobante' => $pagoonline->nro_comprobante,
                    'fecha_envio' => date('Y-m-d H:i:s'),
                ]);

                Log_usuario::create([
                    'id_usuario'  => $idusuario,
                    'ip_usuario'  => $ip_cliente,
                    'descripcion' => "LABORATORIO",
                    'dato_ant1'   => $orden->id,
                    'dato1'       => $orden->id_paciente,
                    'dato_ant2'   => "FACTURA PAGO EN LINEA",
                    'dato_ant4'   => $pagoonline->nro_comprobante,
                ]);
            }
            }
        }

            
        

        return response()->json("ok");
    

    }

    public function pago_en_linea_contab($id)
    {
        $empresa = Empresa::where('prioridad_labs',1)->first();

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        //PAGO 1 - ESTADO PAGO 1 - PAGO ONLINE 1 - COMPROBANTE NULL - FECHA ENVIO NULL
        $orden = Examen_Orden::find($id);
        $vt_bandera = false; 

        if(is_null($empresa)){
            $msn_error = 'NO ENCUENTRA EMPRESA';
            Log_usuario::create([
                'id_usuario'  => $idusuario,
                'ip_usuario'  => $ip_cliente,
                'descripcion' => "LABORATORIO",
                'dato_ant1'   => $orden->id,
                'dato1'       => $orden->id_paciente,
                'dato_ant2'   => "ERROR FACTURA PAGO EN LINEA",
                'dato_ant4'   => $msn_error,
            ]);
            return "error";
        }

        $productos = [];
        $cant = 0;
        foreach ($orden->detalles as $value) {
            //se envian los productos
            $producto['sku']       = "LABS-" . $value->examen->id; //ID EXAMEN
            $producto['nombre']    = $value->examen->nombre; // NOMBRE DEL EXAMEN
            $producto['cantidad']  = "1";
            $producto['precio']    = $value->valor; //DETALLE
            $producto['descuento'] = $value->valor_descuento;
            $producto['subtotal']  = $value->valor - $value->valor_descuento; //precio-descuento
            $producto['tax']       = "0";
            $producto['total']     = $value->valor - $value->valor_descuento; //SUBTOTAL
            $producto['copago']    = "0";
            $productos[$cant]      = $producto;
            $cant++;
        }

        $vt_cierre = CierreCaja::where('id_orden',$orden->id)->where('estado',1)->first();

        if(is_null($vt_cierre)){

        $idcierre = CierreCaja::insertGetid([
            'fecha'           => date('Y-m-d H:m:s'),
            'tipo'            => '1',
            'id_paciente'     => $orden->paciente->id,
            'id_seguro'       => $orden->seguro->id,
            'descripcion'     => 'El examen orden : ' . $orden->id . ' paciente: ' . $orden->paciente->apellido1 . ' ' . $orden->paciente->nombre1,
            'valor'           => $orden->total_valor,
            'saldo'           => $orden->total_valor,
            'id_orden'        => $orden->id,
            'estado'          => '1',
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
        ]);
        }
        $id_orden = $orden->id; $id_empresa = $empresa->id; $sucursal = $empresa->establecimiento; $caja = $empresa->punto_emision;
        //$pagoonline = DB::table('pagosenlinea')->where('clave', $id_orden)->where('empresa_id',$id_empresa)->where('sucursal',$sucursal)->where('caja',$caja)->first(); //return json_encode($pagoonline);
        //dd($pagoonline);
        if($orden->comprobante != null && $orden->fecha_envio){
            $f_emision = date('Y-m-d H:i:s', strtotime($orden->fecha_envio));
            $nro_comprobante     = $orden->comprobante;
            $vt_bandera = true;

        }else{

            Log_usuario::create([
                'id_usuario'  => $idusuario,
                'ip_usuario'  => $ip_cliente,
                'descripcion' => "GESTION DE PAGOS EN LINEA",
                'dato_ant1'   => "EMP:".$id_empresa."-SUC:".$sucursal."-CAJ:".$caja,
                'dato1'       => "ORDEN:".$id_orden,
                'dato_ant2'   => "ERROR:",
                'dato2'       => "SIN NUMERO DE FACTURA",
                'dato_ant4'   => "-",
            ]);
            return response()->json([ 'estado' => 'error', 'mensaje' => "SIN NUMERO DE FACTURA" ]);

        }
        /*if (!is_null($pagoonline)) {
            if ($pagoonline->nro_comprobante != null) {

                  
                    $f_emision = date('Y-m-d H:i:s', strtotime($orden->created_at));
                $nro_comprobante     = $pagoonline->nro_comprobante;
                $vt_bandera = true;
            }else{
                Log_usuario::create([
                    'id_usuario'  => $idusuario,
                    'ip_usuario'  => $ip_cliente,
                    'descripcion' => "GESTION DE PAGOS EN LINEA",
                    'dato_ant1'   => "EMP:".$id_empresa."-SUC:".$sucursal."-CAJ:".$caja,
                    'dato1'       => "ORDEN:".$id_orden,
                    'dato_ant2'   => "ERROR:",
                    'dato2'       => "SIN NUMERO DE FACTURA",
                    'dato_ant4'   => "-",
                ]);
                return response()->json([ 'estado' => 'error', 'mensaje' => "SIN NUMERO DE FACTURA" ]);      
            }
        }else{
            Log_usuario::create([
                'id_usuario'  => $idusuario,
                'ip_usuario'  => $ip_cliente,
                'descripcion' => "GESTION DE PAGOS EN LINEA",
                'dato_ant1'   => "EMP:".$id_empresa."-SUC:".$sucursal."-CAJ:".$caja,
                'dato1'       => "ORDEN:".$id_orden,
                'dato_ant2'   => "ERROR:",
                'dato2'       => "PAGO EN LINEA NO ENCONTRADO",
                'dato_ant4'   => "-",
            ]);
            return response()->json([ 'estado' => 'error', 'mensaje' => "PAGO EN LINEA NO ENCONTRADO"]);    
        }*/
        if($vt_bandera){

        $appId = "wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=";
        $elemento = json_encode([
            "id_orden"   => $id,
            "empresa"    => $empresa->id,
            "sucursal"   => $empresa->establecimiento,
            "caja"       => $empresa->punto_emision,
            //DATOS DEL CLIENTE/////
            "cedula"     => $orden->cedula_factura,
            "tipo"       => '6',
            "nombre"     => $orden->nombre_factura,
            "email"      => $orden->email_factura,
            "telefono"   => $orden->telefono_factura,
            "direccion"  => $orden->direccion_factura,
            "ciudad"     => $orden->ciudad_factura,
            "id_paciente"=> $orden->id_paciente,
            "nombre_pac" => $orden->paciente->apellido1.' '.$orden->paciente->apellido2.' '.$orden->paciente->nombre1.' '.$orden->paciente->nombre2,
            "seguro"     => $orden->seguro->nombre,
            "total_valor"=> $orden->total_valor,
            "fecha_orden"=> substr($orden->fecha_orden, 0, 10),
            "fecha_envio"=> substr($orden->fecha_envio, 0, 10),
            ////////////////////////
            'productos'  => $productos,
            "token"      => "8c0a00ec19933215dc29225e645ea714",
                "nro_comprobante" => $nro_comprobante,
                "f_emision" => $f_emision,
                "pago_auth"     =>  '0',
                "issuer"        =>  '0',
                "credittype"    =>  '0',
                "paymentmethod" =>  '0',
        ]);//dd($elemento);
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/json\r\n" . "AppId: " . $appId . "\r\n",
                'method'  => 'POST',
                'content' => $elemento,
            ),
            "ssl" => array(
                "verify_peer" => false,
                "verify_peer_name" => false,
            ),
        );

    
        //$url     = "https://humanlabs.aitrol.com/labs_gestion_servicios";
        $url     = "http://humanlabs.aitrol.com/labs_gestion_servicios";
        $context = stream_context_create($options);
            $response = file_get_contents($url, false, $context);//dd($response);
        $respuesta = json_decode($response, true);

        if($respuesta['estado'] == 'ok'){
            //dd("fin",$respuesta);
            Log_usuario::create([
                'id_usuario'  => $idusuario,
                'ip_usuario'  => $ip_cliente,
                'descripcion' => "GESTION DE PAGOS EN LINEA ENVIO",
                'dato_ant1'   => "EMP:".$empresa->id."-SUC:".$empresa->establecimiento."-CAJ:".$empresa->punto_emision,
                'dato1'       => "ORDEN:".$id,
                'dato_ant2'   => "RESPUESTA:",
                'dato2'       => $response,
                'dato_ant4'   => "-",
            ]);

            $orden->update([
                'comprobante' => $respuesta['mensaje'],
                'fecha_envio' => date('Y-m-d H:i:s'),
            ]);

            return response()->json("ok");
        
        }else{

            return $response;  

        }

        }

        return response()->json("ok");
    }

    public function getUserByCierre(Request $request)
    {
        $productos = [];
        if ($request['search'] != null) {
            $types     = ['1', '10', '12', '5'];
            $productos = User::whereIn('id_tipo_usuario', $types)->where('estado', '1')->whereRaw("CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) like '%" . $request['search'] . "%' ")->select(DB::raw('CONCAT_WS(" ", nombre1, " " , nombre2, " ", apellido1," ",apellido2) as text'), 'id as id')->get();
        }

        return response()->json($productos);
    }
    public function buscar_usuarios(Request $request)
    {
        $productos = [];
        if ($request['search'] != null) {
            //$types     = ['5','1'];
            $productos = User::where('id_tipo_usuario', '<>', '2')->where('estado', '1')->whereRaw("CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) like '%" . $request['search'] . "%' ")->select(DB::raw('CONCAT_WS(" ", nombre1, " " , nombre2, " ", apellido1," ",apellido2) as text'), 'id as id')->get();
        }

        return response()->json($productos);
    }
    public function cierre_caja_modal($id, $today, Request $request)
    {
        $records = CierreCaja::where('cierre_caja.estado', '1')
            ->leftjoin('examen_detalle_forma_pago as ep', 'ep.id_examen_orden', 'cierre_caja.id_orden')
            ->whereDate('cierre_caja.fecha', $today)
            ->where('cierre_caja.id_usuariocrea', $id)
            ->select('cierre_caja.*')
            ->distinct('cierre_caja.id_orden')
            ->get();
        $total = 0;
        foreach ($records as $record) {
            $ids                = date('d/m/Y H:i:s', strtotime($record->fecha));
            $orden              = Examen_Orden::find($record->id_orden);
            $forma_pago         = Examen_Detalle_Forma_Pago::where('id_examen_orden', $record->id_orden)->get();
            $xdata              = "";
            $dataEfectivo       = Examen_Detalle_Forma_Pago::where('id_examen_orden', $record->id_orden)->where('id_tipo_pago', '1')->sum('valor');
            $dataCheque         = Examen_Detalle_Forma_Pago::where('id_examen_orden', $record->id_orden)->where('id_tipo_pago', '2')->sum('valor');
            $dataDeposito       = Examen_Detalle_Forma_Pago::where('id_examen_orden', $record->id_orden)->where('id_tipo_pago', '3')->sum('valor');
            $dataTarjetaCredito = Examen_Detalle_Forma_Pago::where('id_examen_orden', $record->id_orden)->where('id_tipo_pago', '4')->sum('p_fi');
            $dataTransferencia  = Examen_Detalle_Forma_Pago::where('id_examen_orden', $record->id_orden)->where('id_tipo_pago', '5')->sum('valor');
            //$dataTarjetaDebito = Examen_Detalle_Forma_Pago::where('id_examen_orden', $record->id_orden)->where('id_tipo_pago', '6')->sum('p_fi');
            $dataPendientePago = Examen_Detalle_Forma_Pago::where('id_examen_orden', $record->id_orden)->where('id_tipo_pago', '7')->sum('valor');
            $paciente          = Paciente::find($record->id_paciente);
            $usuario           = User::find($record->id_usuariocrea);
            $seguro            = null;
            if (!is_null($orden)) {
                $seguro   = Seguro::find($orden->id_seguro);
                $paciente = Paciente::find($orden->id_paciente);
                $id_orden = $orden->id;
                $dataTarjetaCredito = $orden->total_valor;
                $dataTarjetaDebito = $orden->total_valor;
            }
            $nseguro = "";
            if (!is_null($seguro)) {
                $nseguro = $seguro->nombre;
            }

            $facturado = "No Facturado";
            if ($orden != null) {
                if ($orden->comprobante != null && $orden->fecha_envio != null) {
                    $facturado = "Facturado";
                }
                if ($orden->seguro->tipo == 0) {
                    if ($dataPendientePago == 0) {
                        $facturado         = "Orden Publica";
                        $dataPendientePago = $orden->total_valor;
                    }
                }
            } else {
                if ($record->tipo == '4') {
                    $facturado = "Cierre Caja";
                } else if ($record->tipo == '0') {
                    $facturado = "Inicio de Caja";
                }
            }

            $nombre = "";
            if (!is_null($paciente)) {
                $nombre = $paciente->apellido1 . ' ' . $paciente->apellido2 . ' ' . $paciente->nombre1;
            } else {
            }
            if ($record->tipo == '0') {
                $xdata = "Inicio de caja";
            } elseif ($record->tipo == '1') {
                $xdata = "Ingreso de caja";
            } elseif ($record->tipo == '2') {
                $xdata = "Salida de caja";
            } elseif ($record->tipo == '4') {
                $xdata = "Cierre de caja";
            }
            if ($xdata == "Inicio de caja") {
                $dataEfectivo = $record->saldo;
            }
            if ($xdata == "Cierre de caja") {
                $dataEfectivo = $record->saldo;
            }
            $total += $dataEfectivo + $dataCheque + $dataDeposito + $dataTarjetaCredito + $dataTransferencia + $dataTarjetaDebito + $dataPendientePago;
        }
        return view('contable/cierre_caja/modal_cierre', ['valor' => $total]);
    }
    public function getLast($id, $today, Request $request)
    {
        //dd($request->all());
        $records = CierreCaja::where('cierre_caja.estado', '1')
            ->join('examen_orden as eo', 'eo.id', 'cierre_caja.id_orden')
            ->leftjoin('examen_detalle_forma_pago as ep', 'ep.id_examen_orden', 'cierre_caja.id_orden')
            ->whereDate('cierre_caja.fecha', $today)
            ->where('cierre_caja.id_usuariocrea', $id)
            ->select('cierre_caja.*')
            ->where('eo.estado', 1)
            ->whereNotIn('eo.id_seguro', ['2', '3', '5', '6', '4'])
            ->distinct('cierre_caja.id_orden')
            ->get(); //dd($records);



        $total              = 0;
        $dataEfectivo       = 0;
        $dataCheque         = 0;
        $dataDeposito       = 0;
        $dataTarjetaCredito = 0;
        $dataTarjetaDebito  = 0;
        $dataPendientePago  = 0;
        $dataTransferencia  = 0;
        $no_facturados_cant = 0;
        $dataTarjetaCredito2 = 0;
        $dataTarjetaDebito2 = 0;
        $facturados_cant = 0;
        foreach ($records as $record) {
            $ids        = date('d/m/Y H:i:s', strtotime($record->fecha));
            $orden      = Examen_Orden::find($record->id_orden);
            if ($orden->comprobante == null || $orden->fecha_envio == null) {
                $no_facturados_cant++;
            } else {
                $facturados_cant++;
            }
            $forma_pago = Examen_Detalle_Forma_Pago::where('id_examen_orden', $record->id_orden)->get();
            $xdata      = "";
            $dataEfectivo += Examen_Detalle_Forma_Pago::where('id_examen_orden', $record->id_orden)->where('id_tipo_pago', '1')->sum('valor');
            $dataCheque += Examen_Detalle_Forma_Pago::where('id_examen_orden', $record->id_orden)->where('id_tipo_pago', '2')->sum('valor');
            $dataDeposito += Examen_Detalle_Forma_Pago::where('id_examen_orden', $record->id_orden)->where('id_tipo_pago', '3')->sum('valor');

            $dataTarjetaCredito_1 = Examen_Detalle_Forma_Pago::where('id_examen_orden', $record->id_orden)->where('id_tipo_pago', '4')->sum('p_fi');
            $dataTarjetaCredito_2 = Examen_Detalle_Forma_Pago::where('id_examen_orden', $record->id_orden)->where('id_tipo_pago', '4')->sum('valor');
            $dataTarjetaCredito += $dataTarjetaCredito_1 + $dataTarjetaCredito_2;

            $dataTransferencia += Examen_Detalle_Forma_Pago::where('id_examen_orden', $record->id_orden)->where('id_tipo_pago', '5')->sum('valor');

            $dataTarjetaDebito_1 = Examen_Detalle_Forma_Pago::where('id_examen_orden', $record->id_orden)->where('id_tipo_pago', '6')->sum('p_fi');
            $dataTarjetaDebito_2 = Examen_Detalle_Forma_Pago::where('id_examen_orden', $record->id_orden)->where('id_tipo_pago', '6')->sum('valor');
            $dataTarjetaDebito += $dataTarjetaDebito_1 + $dataTarjetaDebito_2;
            $dataPendientePago += Examen_Detalle_Forma_Pago::where('id_examen_orden', $record->id_orden)->where('id_tipo_pago', '7')->sum('valor');
            if (!is_null($orden)) {
                $seguro   = Seguro::find($orden->id_seguro);
                $paciente = Paciente::find($orden->id_paciente);
                $id_orden = $orden->id;
                /*if ($dataTarjetaCredito2 > 0) {
                    //tuve que setear los valores de esta manera por el valor
                    //$dataTarjetaCredito += $orden->total_valor;
                }
                if ($dataTarjetaDebito2 > 0) {
                    //tuve que setear los valores de esta manera por equivocacion martes 20 de julio del 2021
                    //$dataTarjetaDebito += $orden->total_valor;
                }*/
            }
            if ($orden != null) {
                if ($orden->comprobante != null && $orden->fecha_envio != null) {
                    $facturado = "Facturado";
                }
                if ($orden->seguro->tipo == 0) {

                    $facturado = "Orden Publica";
                    $dataPendientePago += $orden->total_valor;
                }
            } else {
                if ($record->tipo == '4') {
                    $facturado = "Cierre Caja";
                } else if ($record->tipo == '0') {
                    $facturado = "Inicio de Caja";
                }
            }

            $total += $dataEfectivo + $dataCheque + $dataDeposito + $dataTarjetaCredito + $dataTransferencia + $dataTarjetaDebito + $dataPendientePago;
        }
        $arraySend['efectivo']      = $dataEfectivo;
        $arraySend['cheque']        = $dataCheque;
        $arraySend['deposito']      = $dataDeposito;
        $arraySend['credito']       = $dataTarjetaCredito;
        $arraySend['debito']        = $dataTarjetaDebito;
        $arraySend['pendiente']     = $dataPendientePago;
        $arraySend['transferencia'] = $dataTransferencia;
        $arraySend['total']         = $total;
        $arraySend['no_facturados_cant'] = $no_facturados_cant;
        $arraySend['facturados_cant']    = $facturados_cant;
        return $arraySend;
    }


    public function nr_cierre_laboratorio(Request $request)
    {
        $fecha_desde    = $request->fecha;
        $ordenid        = $request->ordenid;
        $id_usuario     = $request->id_usuario;
        $facturado      = $request->facturado;
        $id_seguro      = $request->id_seguro;

        //PANTALLA DEL CIERRE DE CAJA
        $records = CierreCaja::where('cierre_caja.estado', '1')
            ->join('examen_orden as eo', 'eo.id', 'cierre_caja.id_orden')
            ->join('seguros as s', 's.id', 'eo.id_seguro')
            //->leftjoin('examen_detalle_forma_pago as ep', 'ep.id_examen_orden', 'cierre_caja.id_orden')
            ->whereBetween('cierre_caja.fecha', [$fecha_desde . ' 00:00:00', $fecha_desde . ' 23:59:59'])
            ->where('eo.estado', 1)
            ->where('cierre_caja.tipo', '<>', '4')
            ->where('s.tipo', '>', '0');
        // ->orderBy('eo.id_nivel','eo.comprobante')
        // ->select('eo.*','cierre_caja.id_usuariocrea','cierre_caja.id as id_caja')
        // ->get();
        //dd($records->get());
        $cabecera_excel = CierreCaja::where('cierre_caja.estado', '1')
            ->join('examen_orden as eo', 'eo.id', 'cierre_caja.id_orden')
            ->join('seguros as s', 's.id', 'eo.id_seguro')
            //->leftjoin('examen_detalle_forma_pago as ep', 'ep.id_examen_orden', 'cierre_caja.id_orden')
            ->whereBetween('cierre_caja.fecha', [$fecha_desde . ' 00:00:00', $fecha_desde . ' 23:59:59'])
            ->where('eo.estado', 1)
            ->where('cierre_caja.tipo', '<>', '4')
            ->where('s.tipo', '>', '0');
        // ->groupBy('cierre_caja.id_usuariocrea')
        // ->select('cierre_caja.*')
        // ->get();

        if (isset($id_usuario) && $id_usuario != "null") {
            $records = $records->where('cierre_caja.id_usuariocrea', $id_usuario);
            $cabecera_excel = $cabecera_excel->where('cierre_caja.id_usuariocrea', $id_usuario);
        }
        if (isset($id_seguro) && $id_seguro != "null") {
            $records = $records->where('eo.id_seguro', $id_seguro);
            $cabecera_excel = $cabecera_excel->where('eo.id_seguro', $id_seguro);
        }
        if (isset($ordenid) && $ordenid != "null") {
            $records->where('cierre_caja.id_orden', $ordenid);
            $cabecera_excel->where('cierre_caja.id_orden', $ordenid);
        }

        $records = $records->orderBy('cierre_caja.fecha')->select('eo.*','cierre_caja.id_usuariocrea','cierre_caja.id as id_caja')->get();
        $cabecera_excel = $cabecera_excel->groupBy('cierre_caja.id_usuariocrea')->select('cierre_caja.*')->get();

        $fechaBusqueda = $this->obtenerFechaEnLetra($fecha_desde);
        //->get();

        $seguros_cierre_caja = Seguro::orderBy('tipo', 'desc')->where('tipo', '<>', '0')->whereNotIn('id', ['4', '33'])->get(); //dd($seguros_cierre_caja);   
        $seguros_resto = Seguro::orderBy('tipo')->where('tipo', '<>', '0')->whereIn('id', ['4', '33'])->get();

        $agrupados = [];
        //modificar por niveles
        /*$agrupados = CierreCaja::where('cierre_caja.estado', '1')
        ->join('examen_orden as eo', 'eo.id', 'cierre_caja.id_orden')
        ->leftjoin('examen_detalle_forma_pago as ep', 'ep.id_examen_orden', 'cierre_caja.id_orden')
        ->whereBetween('cierre_caja.fecha', [$request['fecha_cierre'] . ' 00:00:00', $request['fecha_cierre'] . ' 23:59:59'])
        ->where('cierre_caja.tipo', '<>', '4')
        ->whereIn('eo.id_seguro', ['2', '3', '5', '6', '4','33','4'])->groupBy('eo.id_seguro')->get();*/
        $vistaurl = "contable.cierre_caja.pdfcierredecaja";
        $view     = \View::make($vistaurl, compact('fechaBusqueda', 'records', 'agrupados', 'seguros_cierre_caja', 'seguros_resto', 'cabecera_excel'))->render();
        $pdf      = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->setPaper('A4', 'landscape');

        return $pdf->stream('Cierre de Caja ' . '.pdf');
    }

    public function ct_reporte_cierre_pdf(Request $request)
    {

        $fecha_desde = date('Y-m-d');
        $ct_fecha_desde = $request->ct_fecha_desde;
        $ct_fecha_hasta = $request->ct_fecha_hasta;
        $ordenid        = $request->ordenid;
        $id_usuario     = $request->id_usuario;
        $facturado      = $request->facturado;
        $id_seguro      = $request->id_seguro;
        //dd($request->all());
        //PANTALLA DEL CIERRE DE CAJA
        if (is_null($ct_fecha_desde)) {
            $ct_fecha_desde = date('Y-m-d');
        }

        if (is_null($ct_fecha_hasta)) {
            $ct_fecha_hasta = date('Y-m-d');
        }

        $records = CierreCaja::where('cierre_caja.estado', '1')
            ->join('examen_orden as eo', 'eo.id', 'cierre_caja.id_orden')
            ->join('seguros as s', 's.id', 'eo.id_seguro')
            //->leftjoin('examen_detalle_forma_pago as ep', 'ep.id_examen_orden', 'cierre_caja.id_orden')
            ->whereBetween('cierre_caja.fecha', [$ct_fecha_desde . ' 00:00:00', $ct_fecha_hasta . ' 23:59:59'])
            ->where('eo.estado', 1)
            ->where('cierre_caja.tipo', '<>', '4')
            ->where('s.tipo', '>', '0');

        $cabecera_excel = CierreCaja::where('cierre_caja.estado', '1')
            ->join('examen_orden as eo', 'eo.id', 'cierre_caja.id_orden')
            ->join('seguros as s', 's.id', 'eo.id_seguro')
            //->leftjoin('examen_detalle_forma_pago as ep', 'ep.id_examen_orden', 'cierre_caja.id_orden')
            ->whereBetween('cierre_caja.fecha', [$ct_fecha_desde . ' 00:00:00', $ct_fecha_hasta . ' 23:59:59'])
            ->where('eo.estado', 1)
            ->where('cierre_caja.tipo', '<>', '4')
            ->where('s.tipo', '>', '0');

        if (isset($id_usuario) && $id_usuario != "null") {
            $records = $records->where('cierre_caja.id_usuariocrea', $id_usuario);
            $cabecera_excel = $cabecera_excel->where('cierre_caja.id_usuariocrea', $id_usuario);
        }
        if (isset($id_seguro) && $id_seguro != "null") {
            $records = $records->where('eo.id_seguro', $id_seguro);
            $cabecera_excel = $cabecera_excel->where('eo.id_seguro', $id_seguro);
        }
        if (isset($ordenid) && $ordenid != "null") {
            $records->where('cierre_caja.id_orden', $ordenid);
            $cabecera_excel->where('cierre_caja.id_orden', $ordenid);
        }

        $records = $records->orderBy('cierre_caja.fecha')->select('eo.*','cierre_caja.id_usuariocrea','cierre_caja.id as id_caja')->get();
        $cabecera_excel = $cabecera_excel->groupBy('cierre_caja.id_usuariocrea')->select('cierre_caja.*')->get();

        $fechaBusqueda = $this->obtenerFechaEnLetra($fecha_desde);
        //->get();

        $seguros_cierre_caja = Seguro::orderBy('tipo', 'desc')->where('tipo', '<>', '0')->whereNotIn('id', ['4', '33'])->get(); //dd($seguros_cierre_caja);   
        $seguros_resto = Seguro::orderBy('tipo')->where('tipo', '<>', '0')->whereIn('id', ['4', '33'])->get();

        $agrupados = [];
        //modificar por niveles
        /*$agrupados = CierreCaja::where('cierre_caja.estado', '1')
        ->join('examen_orden as eo', 'eo.id', 'cierre_caja.id_orden')
        ->leftjoin('examen_detalle_forma_pago as ep', 'ep.id_examen_orden', 'cierre_caja.id_orden')
        ->whereBetween('cierre_caja.fecha', [$request['fecha_cierre'] . ' 00:00:00', $request['fecha_cierre'] . ' 23:59:59'])
        ->where('cierre_caja.tipo', '<>', '4')
        ->whereIn('eo.id_seguro', ['2', '3', '5', '6', '4','33','4'])->groupBy('eo.id_seguro')->get();*/
        $vistaurl = "contable.cierre_caja.pdfcierredecaja_contabilidad";
        $view     = \View::make($vistaurl, compact('fechaBusqueda', 'records', 'agrupados', 'seguros_cierre_caja', 'seguros_resto', 'cabecera_excel', 'ct_fecha_desde', 'ct_fecha_hasta'))->render();
        $pdf      = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->setPaper('A4', 'landscape');

        return $pdf->stream('Cierre de Caja Contabilidad' . '.pdf');
    }


    function obtenerFechaEnLetra($fecha)
    {
        $num = date("j", strtotime($fecha));
        $anno = date("Y", strtotime($fecha));
        $mes = array('ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE');
        $mes = $mes[(date('m', strtotime($fecha)) * 1) - 1];
        return $num . ' de ' . $mes . ' del ' . $anno;
    }


    public function conglomerada_cierre_laboratorio(Request $request)
    {
        //dd($request->all());
        $fecha_desde = $request->fecha;
        $fecha_hasta = $request->fecha_hasta;

        //PANTALLA DEL CIERRE DE CAJA
        $records = CierreCaja::where('cierre_caja.estado', '1')
            ->join('examen_orden as eo', 'eo.id', 'cierre_caja.id_orden')
            ->join('seguros as s', 's.id', 'eo.id_seguro')
            //->leftjoin('examen_detalle_forma_pago as ep', 'ep.id_examen_orden', 'cierre_caja.id_orden')
            ->whereBetween('cierre_caja.fecha', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59'])
            ->where('eo.estado', 1)
            ->where('cierre_caja.tipo', '<>', '4')
            ->where('s.tipo','>','0')
            ->orderBy('cierre_caja.fecha')
            ->select('eo.*','cierre_caja.id_usuariocrea','cierre_caja.id as id_caja')
            ->get();
        //dd($fecha_desde,$request->all(),$records->get());

        $fechaBusqueda = $this->obtenerFechaEnLetra($fecha_desde);
        //->get();

        $seguros_cierre_caja = Seguro::orderBy('tipo', 'desc')->where('tipo', '<>', '0')->whereNotIn('id', ['4', '33'])->get(); //dd($seguros_cierre_caja);   
        $seguros_resto = Seguro::orderBy('tipo')->where('tipo', '<>', '0')->whereIn('id', ['4', '33'])->get();



        $agrupados = [];
        //modificar por niveles
        /*$agrupados = CierreCaja::where('cierre_caja.estado', '1')
        ->join('examen_orden as eo', 'eo.id', 'cierre_caja.id_orden')
        ->leftjoin('examen_detalle_forma_pago as ep', 'ep.id_examen_orden', 'cierre_caja.id_orden')
        ->whereBetween('cierre_caja.fecha', [$request['fecha_cierre'] . ' 00:00:00', $request['fecha_cierre'] . ' 23:59:59'])
        ->where('cierre_caja.tipo', '<>', '4')
        ->whereIn('eo.id_seguro', ['2', '3', '5', '6', '4','33','4'])->groupBy('eo.id_seguro')->get();*/
        $vistaurl = "contable.cierre_caja.pdfcierredecaja";
        $view     = \View::make($vistaurl, compact('fechaBusqueda', 'records', 'agrupados', 'seguros_cierre_caja', 'seguros_resto'))->render();
        $pdf      = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->setPaper('A4', 'landscape');

        return $pdf->stream('Cierre de Caja ' . '.pdf');
    }


    public function observacion($id, Request $request)
    {

        DB::beginTransaction();
        try {
            if (!is_null($id)) {
                $cierre = CierreCaja::where('estado', '1')->where('id_orden', $id)->first();
                $ip_cliente   = $_SERVER["REMOTE_ADDR"];
                $idusuario    = Auth::user()->id;
                if (!is_null($cierre)) {
                    $input = [
                        'observacion'     => $request->observacion,
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariomod'   => $idusuario,
                    ];
                    $cierre->update($input);


                    DB::commit();
                    return 'ok';
                }
            } else {
                return 'error';
            }
        } catch (\Exception $e) {
            //if there is an error/exception in the above code before commit, it'll rollback
            DB::rollBack();
            return $e->getMessage();
        }
    }



    public function ct_reporte_cierre_excel(Request $request)
    {
        $fecha_desde = date('Y-m-d');
        $ct_fecha_desde = $request->ct_fecha_desde;
        $ct_fecha_hasta = $request->ct_fecha_hasta;
        $ordenid        = $request->ordenid;
        $id_usuario     = $request->id_usuario;
        $facturado      = $request->facturado;
        $id_seguro      = $request->id_seguro;

        if (is_null($ct_fecha_desde)) {
            $ct_fecha_desde = date('Y-m-d');
        }

        if (is_null($ct_fecha_hasta)) {
            $ct_fecha_hasta = date('Y-m-d');
        }

        $records = CierreCaja::where('cierre_caja.estado', '1')
            ->join('examen_orden as eo', 'eo.id', 'cierre_caja.id_orden')
            ->join('seguros as s', 's.id', 'eo.id_seguro')
            //->leftjoin('examen_detalle_forma_pago as ep', 'ep.id_examen_orden', 'cierre_caja.id_orden')
            ->whereBetween('cierre_caja.fecha', [$ct_fecha_desde . ' 00:00:00', $ct_fecha_hasta . ' 23:59:59'])
            ->where('eo.estado', 1)
            ->where('cierre_caja.tipo', '<>', '4')
            ->where('s.tipo', '>', '0');

        $cabecera_excel = CierreCaja::where('cierre_caja.estado', '1')
            ->join('examen_orden as eo', 'eo.id', 'cierre_caja.id_orden')
            ->join('seguros as s', 's.id', 'eo.id_seguro')
            //->leftjoin('examen_detalle_forma_pago as ep', 'ep.id_examen_orden', 'cierre_caja.id_orden')
            ->whereBetween('cierre_caja.fecha', [$ct_fecha_desde . ' 00:00:00', $ct_fecha_hasta . ' 23:59:59'])
            ->where('eo.estado', 1)
            ->where('cierre_caja.tipo', '<>', '4')
            ->where('s.tipo', '>', '0');

        if (isset($id_usuario) && $id_usuario != "null") {
            $records = $records->where('cierre_caja.id_usuariocrea', $id_usuario);
            $cabecera_excel = $cabecera_excel->where('cierre_caja.id_usuariocrea', $id_usuario);
        }
        if (isset($id_seguro) && $id_seguro != "null") {
            $records = $records->where('eo.id_seguro', $id_seguro);
            $cabecera_excel = $cabecera_excel->where('eo.id_seguro', $id_seguro);
        }
        if (isset($ordenid) && $ordenid != "null") {
            $records->where('cierre_caja.id_orden', $ordenid);
            $cabecera_excel->where('cierre_caja.id_orden', $ordenid);
        }

        $records = $records->orderBy('cierre_caja.fecha')->select('eo.*','cierre_caja.id_usuariocrea','cierre_caja.id as id_caja')->get();
        $cabecera_excel = $cabecera_excel->groupBy('cierre_caja.id_usuariocrea')->select('cierre_caja.*')->get();
        $fechaBusqueda = $this->obtenerFechaEnLetra($fecha_desde);
        //->get();

        $seguros_cierre_caja = Seguro::orderBy('tipo', 'desc')->where('tipo', '<>', '0')->whereNotIn('id', ['4', '33'])->get();
        //dd($seguros_cierre_caja);   
        $seguros_resto = Seguro::orderBy('tipo')->where('tipo', '<>', '0')->whereIn('id', ['4', '33'])->get();



        Excel::create('Cierre de Caja Labs -' . $fecha_desde, function ($excel) use ($records, $cabecera_excel, $seguros_cierre_caja, $ct_fecha_desde, $ct_fecha_hasta, $fecha_desde) {

            $excel->sheet('Cierre de Caja Labs - Usuarios', function ($sheet) use ($records, $cabecera_excel, $seguros_cierre_caja, $ct_fecha_desde, $ct_fecha_hasta, $fecha_desde) {
                $i = 1;
                foreach ($cabecera_excel as $cabecera) {
                    $usuario = user::find($cabecera->id_usuariocrea);
                    $sheet->mergeCells('A' . $i . ':' . 'Q' . $i);

                    $sheet->cell('A' . $i, function ($cell) {
                        // manipulate the cel
                        $cell->setValue('REPORTE CAJA LABS');
                        $cell->setFontSize(20);
                        $cell->setFontWeight('bold');
                        $cell->setBackground('#D1F2EB');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $i++;
                    $sheet->cell('E' . $i, function ($cell) {
                        // manipulate the cel
                        $cell->setValue('USUARIO:');
                        $cell->setFontWeight('bold');
                        $cell->setBackground('#D1F2EB');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('F' . $i, function ($cell) use ($usuario) {
                        // manipulate the cel
                        $cell->setValue($usuario->nombre1 . ' ' . $usuario->apellido1);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('I' . $i, function ($cell) {
                        // manipulate the cel
                        $cell->setValue('DESDE:');
                        $cell->setFontWeight('bold');
                        $cell->setBackground('#D1F2EB');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('J' . $i, function ($cell) use ($ct_fecha_desde) {
                        // manipulate the cel
                        $cell->setValue(substr($ct_fecha_desde, 0, 10));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $i++;
                    $sheet->cell('E' . $i, function ($cell) {
                        // manipulate the cel
                        $cell->setValue('FECHA DE REPORTE:');
                        $cell->setFontWeight('bold');
                        $cell->setBackground('#D1F2EB');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('F' . $i, function ($cell) use ($fecha_desde) {
                        // manipulate the cel
                        $cell->setValue(substr($fecha_desde, 0, 10));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                        $sheet->cell('I'.$i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('HASTA');
                            $cell->setFontWeight('bold');
                            $cell->setBackground('#D1F2EB');
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('J' . $i, function ($cell) use ($ct_fecha_hasta) {
                            // manipulate the cel
                            $cell->setValue(substr($ct_fecha_hasta,0,10));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $i++;
                        $sheet->cell('A'.$i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('#');
                            $cell->setFontWeight('bold');
                            $cell->setBackground('#D1F2EB');
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                    $sheet->cell('B' . $i, function ($cell) {
                        // manipulate the cel
                        $cell->setValue('ORDEN');
                        $cell->setFontWeight('bold');
                        $cell->setBackground('#D1F2EB');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('C' . $i, function ($cell) {
                        // manipulate the cel
                        $cell->setValue('FECHA');
                        $cell->setFontWeight('bold');
                        $cell->setBackground('#D1F2EB');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                        $sheet->cell('D'.$i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('SEGURO - NIVEL');
                            $cell->setFontWeight('bold');
                            $cell->setBackground('#D1F2EB');
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('E'.$i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('PACIENTE');
                            $cell->setFontWeight('bold');
                            $cell->setBackground('#D1F2EB');
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('F'.$i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('EFECTIVO');
                            $cell->setFontWeight('bold');
                            $cell->setBackground('#D1F2EB');
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('G'.$i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('CHEQUE');
                            $cell->setFontWeight('bold');
                            $cell->setBackground('#D1F2EB');
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('H'.$i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('DEPOSITO');
                            $cell->setFontWeight('bold');
                            $cell->setBackground('#D1F2EB');
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('I'.$i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('TRANS');
                            $cell->setFontWeight('bold');
                            $cell->setBackground('#D1F2EB');
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('J'.$i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('T/C');
                            $cell->setFontWeight('bold');
                            $cell->setBackground('#D1F2EB');
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('K'.$i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('T/D');
                            $cell->setFontWeight('bold');
                            $cell->setBackground('#D1F2EB');
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('L'.$i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('P.PAGO');
                            $cell->setFontWeight('bold');
                            $cell->setBackground('#D1F2EB');
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('M'.$i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('ONLINE');
                            $cell->setFontWeight('bold');
                            $cell->setBackground('#D1F2EB');
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('N'.$i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('DATALINK');
                            $cell->setFontWeight('bold');
                            $cell->setBackground('#D1F2EB');
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('O'.$i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('ODA');
                            $cell->setFontWeight('bold');
                            $cell->setBackground('#D1F2EB');
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('P'.$i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('# FACTURA');
                            $cell->setFontWeight('bold');
                            $cell->setBackground('#D1F2EB');
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('Q'.$i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('OBSERVACION');
                            $cell->setFontWeight('bold');
                            $cell->setBackground('#D1F2EB');
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $cant = 1; $ac_efectivo = 0; $ac_cheque = 0; $ac_deposito = 0; $ac_transf = 0; $ac_tar_cre = 0; $ac_tar_deb = 0; $ac_ppago = 0; $total_final = 0; $ac_online = 0; $ac_datalink = 0; $ac_oda = 0;
                        $i++;
                        //foreach($seguros_cierre_caja as $seguro ){
                            foreach($records as $val){
                                $seguro = Seguro::find($val->id_seguro);
                                if($seguro->tipo != 0){
                                    if (in_array($seguro->id, array(4, 33)) == false){
                                        if($val->id_usuariocrea == $cabecera->id_usuariocrea){
                                            $eorden = Examen_Orden::find($val->id); 
                                            $cierre_caja = CierreCaja::find($val->id_caja);
                                            $efectivo = $eorden->detalle_forma_pago->where('id_tipo_pago','1')->sum('valor');
                                            $cheque = $eorden->detalle_forma_pago->where('id_tipo_pago','2')->sum('valor');
                                            $deposito = $eorden->detalle_forma_pago->where('id_tipo_pago','3')->sum('valor');
                                            $transferencia = $eorden->detalle_forma_pago->where('id_tipo_pago','5')->sum('valor');
                                            $tarjeta_credito1 = $eorden->detalle_forma_pago->where('id_tipo_pago','4')->sum('p_fi');
                                            $tarjeta_credito2 = $eorden->detalle_forma_pago->where('id_tipo_pago','4')->sum('valor');
                                            $tarjeta_credito = $tarjeta_credito1 + $tarjeta_credito2;

                                    $tarjeta_debito1 = $eorden->detalle_forma_pago->where('id_tipo_pago', '6')->sum('p_fi');
                                    $tarjeta_debito2 = $eorden->detalle_forma_pago->where('id_tipo_pago', '6')->sum('valor');
                                    $tarjeta_debito = $tarjeta_debito1 + $tarjeta_debito2;

                                    $ppago = $eorden->detalle_forma_pago->where('id_tipo_pago', '7')->sum('valor');
                                            $datalink = $eorden->detalle_forma_pago->where('id_tipo_pago','9')->sum('valor');

                                            if($eorden->pago_online == 1){
                                                $ponline = $eorden->total_valor;
                                            }else{
                                                $ponline = 0;
                                            }

                                            if($eorden->cobrar_pac_pct < 100){
                                                $valor_oda = $eorden->valor - $eorden->valor_con_oda;
                                            }else{
                                                $valor_oda = 0;
                                            }
                                    $ac_efectivo += $efectivo;
                                    $ac_cheque += $cheque;
                                    $ac_deposito += $deposito;
                                    $ac_transf += $transferencia;
                                    $ac_tar_cre += $tarjeta_credito;
                                    $ac_tar_deb += $tarjeta_debito;
                                    $ac_ppago += $ppago;
                                    $ac_online += $ponline;
                                    $ac_datalink += $datalink;
                                    $ac_oda += $valor_oda;

                                    $sheet->cell('A' . $i, function ($cell) use ($cant) {
                                        // manipulate the cel
                                        $cell->setValue($cant);
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    });

                                    $sheet->cell('B' . $i, function ($cell) use ($eorden) {
                                        // manipulate the cel
                                        $cell->setValue($eorden->id);
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    });

                                    $sheet->cell('C' . $i, function ($cell) use ($eorden) {
                                        // manipulate the cel
                                        $cell->setValue(substr($eorden->fecha_orden, 0, 10));
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    });

                                            $sheet->cell('D'.$i, function ($cell) use ($eorden) {
                                                $nivel_nombre = "";
                                                    if($eorden->id_nivel != null){
                                                        $nivel_nombre = $eorden->nivel->nombre;
                                                    }
                                                $cell->setValue($eorden->seguro->nombre ." ". $nivel_nombre);
                                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                            });

                                    $sheet->cell('E' . $i, function ($cell) use ($eorden) {
                                        // manipulate the cel
                                        $cell->setValue($eorden->paciente->apellido1 . " " . $eorden->paciente->apellido2 . " " . $eorden->paciente->nombre1 . " " . $eorden->paciente->nombre2);
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    });

                                    $sheet->cell('F' . $i, function ($cell) use ($efectivo) {
                                        // manipulate the cel
                                        $cell->setValue($efectivo);
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    });
                                    $sheet->getStyle('F' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                                    $sheet->cell('G' . $i, function ($cell) use ($cheque) {
                                        // manipulate the cel
                                        $cell->setValue($cheque);
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    });
                                    $sheet->getStyle('G' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                                    $sheet->cell('H' . $i, function ($cell) use ($deposito) {
                                        // manipulate the cel
                                        $cell->setValue($deposito);
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    });
                                    $sheet->getStyle('H' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                                    $sheet->cell('I' . $i, function ($cell) use ($transferencia) {
                                        // manipulate the cel
                                        $cell->setValue($transferencia);
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    });
                                    $sheet->getStyle('I' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                                    $sheet->cell('J' . $i, function ($cell) use ($tarjeta_credito) {
                                        // manipulate the cel
                                        $cell->setValue($tarjeta_credito);
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    });
                                    $sheet->getStyle('J' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                                    $sheet->cell('K' . $i, function ($cell) use ($tarjeta_debito) {
                                        // manipulate the cel
                                        $cell->setValue($tarjeta_debito);
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    });
                                    $sheet->getStyle('K' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                                    $sheet->cell('L' . $i, function ($cell) use ($ppago) {
                                        // manipulate the cel
                                        $cell->setValue($ppago);
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    });
                                    $sheet->getStyle('L' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                                    $sheet->cell('M' . $i, function ($cell) use ($ponline) {
                                        // manipulate the cel
                                        $cell->setValue($ponline);
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    });
                                    $sheet->getStyle('M' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                                    $sheet->cell('N' . $i, function ($cell) use ($datalink) {
                                        // manipulate the cel
                                        $cell->setValue($datalink);
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    });
                                    $sheet->getStyle('N' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                                    $sheet->cell('O' . $i, function ($cell) use ($valor_oda) {
                                        // manipulate the cel
                                        $cell->setValue($valor_oda);
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    });
                                    $sheet->getStyle('O' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                                    $sheet->cell('P' . $i, function ($cell) use ($eorden) {
                                        // manipulate the cel
                                        if ($eorden->comprobante != null) {
                                            $cell->setValue($eorden->comprobante);
                                        } else {
                                            $cell->setValue("Sin Facturar");
                                        }
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    });

                                    $sheet->cell('Q'.$i, function ($cell) use ($cierre_caja) {
                                        // manipulate the cel
                                        $cell->setValue($cierre_caja->observacion);
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    });

                                    $cant++;
                                    $i++;  
                                        }
                                    }
                                }
                            }
                        //}
                        $sheet->cell('A' . $i, function ($cell) {
                        // manipulate the cel
                        $cell->setValue("");
                        $cell->setBackground('#D1F2EB');
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('B' . $i, function ($cell) {
                        // manipulate the cel
                        $cell->setValue("");
                        $cell->setBackground('#D1F2EB');
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('C' . $i, function ($cell) {
                        // manipulate the cel
                        $cell->setValue("");
                        $cell->setBackground('#D1F2EB');
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('D' . $i, function ($cell) {
                        // manipulate the cel
                        $cell->setValue("");
                        $cell->setBackground('#D1F2EB');
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('E' . $i, function ($cell) {
                        // manipulate the cel
                        $cell->setValue("TOTAL");
                        $cell->setBackground('#D1F2EB');
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('F' . $i, function ($cell) use ($ac_efectivo) {
                        // manipulate the cel
                        $cell->setValue($ac_efectivo);
                        $cell->setBackground('#D1F2EB');
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->getStyle('F' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                    $sheet->cell('G' . $i, function ($cell) use ($ac_cheque) {
                        // manipulate the cel
                        $cell->setValue($ac_cheque);
                        $cell->setBackground('#D1F2EB');
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->getStyle('G' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                    $sheet->cell('H' . $i, function ($cell) use ($ac_deposito) {
                        // manipulate the cel
                        $cell->setValue($ac_deposito);
                        $cell->setBackground('#D1F2EB');
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->getStyle('H' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                    $sheet->cell('I' . $i, function ($cell) use ($ac_transf) {
                        // manipulate the cel
                        $cell->setValue($ac_transf);
                        $cell->setBackground('#D1F2EB');
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->getStyle('I' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                    $sheet->cell('J' . $i, function ($cell) use ($ac_tar_cre) {
                        // manipulate the cel
                        $cell->setValue($ac_tar_cre);
                        $cell->setBackground('#D1F2EB');
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->getStyle('J' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                    $sheet->cell('K' . $i, function ($cell) use ($ac_tar_deb) {
                        // manipulate the cel
                        $cell->setValue($ac_tar_deb);
                        $cell->setBackground('#D1F2EB');
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->getStyle('K' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                    $sheet->cell('L' . $i, function ($cell) use ($ac_ppago) {
                        // manipulate the cel
                        $cell->setValue($ac_ppago);
                        $cell->setBackground('#D1F2EB');
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->getStyle('L' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                        $sheet->cell('M' . $i, function ($cell) use ($ac_online) {
                        // manipulate the cel
                        $cell->setValue($ac_online);
                        $cell->setBackground('#D1F2EB');
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->getStyle('M' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                        $sheet->cell('N' . $i, function ($cell) use ($ac_datalink) {
                        // manipulate the cel
                        $cell->setValue($ac_datalink);
                        $cell->setBackground('#D1F2EB');
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    $sheet->getStyle('N' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                    $sheet->cell('O' . $i, function ($cell) use ($ac_oda) {
                        // manipulate the cel
                        $cell->setValue($ac_oda);
                        $cell->setBackground('#D1F2EB');
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->getStyle('O' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                        $sheet->cell('P' . $i, function ($cell) {
                        // manipulate the cel
                        $cell->setValue("");
                        $cell->setBackground('#D1F2EB');
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                        $sheet->cell('Q' . $i, function ($cell) {
                        // manipulate the cel
                        $cell->setValue("");
                        $cell->setBackground('#D1F2EB');
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        
                    $i=$i+4; 
                }    
            });

            $excel->sheet('Cierre de Caja Labs - General', function ($sheet) use ($records, $cabecera_excel, $seguros_cierre_caja, $ct_fecha_desde, $ct_fecha_hasta, $fecha_desde) {
                $sheet->mergeCells('A1:R1');
                $sheet->cell('A1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('REPORTE CAJA GENERAL LABS');
                    $cell->setFontSize(20);
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('A2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ORDEN');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('B2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('C2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SEGURO - NIVEL');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('D2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PACIENTE');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('E2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('EFECTIVO');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('F2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CHEQUE');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('G2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DEPOSITO');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('H2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TRANS');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('I2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CREDITO');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('J2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DEBITO');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('K2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('P.PAGO');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('L2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ONLINE');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('M2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DATALINK');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('N2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ODA');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('O2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('BANCO');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('P2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('COMPROBANTE');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('Q2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('USUARIO');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('R2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('OBSERVACION');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $cant = 1;
                $i = 3;
                $ac_efectivo = 0;
                $ac_cheque = 0;
                $ac_deposito = 0;
                $ac_transf = 0;
                $ac_tar_cre = 0;
                $ac_tar_deb = 0;
                $ac_ppago = 0;
                $ac_online = 0;
                $ac_oda = 0;
                $ac_datalink = 0;
                $total_final = 0;

                //foreach($seguros_cierre_caja as $seguro ){
                foreach ($records as $val) {
                    $seguro = Seguro::find($val->id_seguro);
                    if ($seguro->tipo != 0) {
                        if (in_array($seguro->id, array(4, 33)) == false) {
                            $eorden = Examen_Orden::find($val->id);
                            $cierre_caja = CierreCaja::find($val->id_caja);
                            $efectivo = $eorden->detalle_forma_pago->where('id_tipo_pago', '1')->sum('valor');
                            $cheque = $eorden->detalle_forma_pago->where('id_tipo_pago', '2')->sum('valor');
                            $deposito = $eorden->detalle_forma_pago->where('id_tipo_pago', '3')->sum('valor');
                            $transferencia = $eorden->detalle_forma_pago->where('id_tipo_pago', '5')->sum('valor');
                            $tarjeta_credito1 = $eorden->detalle_forma_pago->where('id_tipo_pago', '4')->sum('p_fi');
                            $tarjeta_credito2 = $eorden->detalle_forma_pago->where('id_tipo_pago', '4')->sum('valor');
                            $tarjeta_credito = $tarjeta_credito1 + $tarjeta_credito2;

                            $tarjeta_debito1 = $eorden->detalle_forma_pago->where('id_tipo_pago', '6')->sum('p_fi');
                            $tarjeta_debito2 = $eorden->detalle_forma_pago->where('id_tipo_pago', '6')->sum('valor');
                            $tarjeta_debito = $tarjeta_debito1 + $tarjeta_debito2;

                                $ppago = $eorden->detalle_forma_pago->where('id_tipo_pago','7')->sum('valor');
                                $datalink = $eorden->detalle_forma_pago->where('id_tipo_pago','9')->sum('valor');
                                if($eorden->pago_online == 1){
                                    $ponline = $eorden->total_valor;
                                }else{
                                    $ponline = 0;
                                }

                                if($eorden->cobrar_pac_pct < 100){
                                    $valor_oda = $eorden->valor - $eorden->valor_con_oda;
                                }else{
                                    $valor_oda = 0;
                                }
                            $ac_efectivo += $efectivo;
                            $ac_cheque += $cheque;
                            $ac_deposito += $deposito;
                            $ac_transf += $transferencia;
                            $ac_tar_cre += $tarjeta_credito;
                            $ac_tar_deb += $tarjeta_debito;
                            $ac_ppago += $ppago;
                            $ac_online += $ponline;
                            $ac_datalink += $datalink;
                            $ac_oda += $valor_oda;

                            $total_final = $ac_efectivo + $ac_cheque + $ac_deposito + $ac_transf + $ac_tar_cre + $ac_tar_deb + $ac_ppago + $ac_datalink + $ac_oda;

                            $sheet->cell('A' . $i, function ($cell) use ($eorden) {
                                // manipulate the cel
                                $cell->setValue($eorden->id);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->cell('B' . $i, function ($cell) use ($eorden) {
                                // manipulate the cel
                                $cell->setValue(substr($eorden->fecha_orden, 0, 10));
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $nivel_nombre = "";
                            if ($eorden->id_nivel != null) {
                                $nivel_nombre = $eorden->nivel->nombre;
                            }
                            $sheet->cell('C' . $i, function ($cell) use ($eorden, $nivel_nombre) {
                                // manipulate the cel
                                $cell->setValue($eorden->seguro->nombre . " " . $nivel_nombre);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->cell('D' . $i, function ($cell) use ($eorden) {
                                // manipulate the cel
                                $cell->setValue($eorden->paciente->apellido1 . " " . $eorden->paciente->apellido2 . " " . $eorden->paciente->nombre1 . " " . $eorden->paciente->nombre2);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->cell('E' . $i, function ($cell) use ($efectivo) {
                                // manipulate the cel
                                $cell->setValue($efectivo);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->getStyle('E' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                            $sheet->cell('F' . $i, function ($cell) use ($cheque) {
                                // manipulate the cel
                                $cell->setValue($cheque);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->getStyle('F' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                            $sheet->cell('G' . $i, function ($cell) use ($deposito) {
                                // manipulate the cel
                                $cell->setValue($deposito);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->getStyle('G' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                            $sheet->cell('H' . $i, function ($cell) use ($transferencia) {
                                // manipulate the cel
                                $cell->setValue($transferencia);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->getStyle('H' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                            $sheet->cell('I' . $i, function ($cell) use ($tarjeta_credito) {
                                // manipulate the cel
                                $cell->setValue($tarjeta_credito);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->getStyle('I' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                            $sheet->cell('J' . $i, function ($cell) use ($tarjeta_debito) {
                                // manipulate the cel
                                $cell->setValue($tarjeta_debito);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->getStyle('J' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                            $sheet->cell('K' . $i, function ($cell) use ($ppago) {
                                // manipulate the cel
                                $cell->setValue($ppago);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->getStyle('K' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                            $sheet->cell('L' . $i, function ($cell) use ($ponline) {
                                // manipulate the cel
                                $cell->setValue($ponline);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->getStyle('L' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                            $sheet->cell('M' . $i, function ($cell) use ($datalink) {
                                // manipulate the cel
                                $cell->setValue($datalink);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->getStyle('M' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                            $sheet->cell('N' . $i, function ($cell) use ($valor_oda) {
                                // manipulate the cel
                                $cell->setValue($valor_oda);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->getStyle('N' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                            $sheet->cell('O' . $i, function ($cell) use ($eorden) {
                                // $nombre_banco = "";
                                // if($banco != null){
                                //     $nombre_banco = $banco->nombre;
                                // }
                                // $cell->setValue($nombre_banco);
                                if (!is_null($eorden->detalle_forma_pago->first())) {
                                    $banco = \Sis_medico\Ct_Bancos::find($eorden->detalle_forma_pago->first()->banco);
                                    if (!is_null($banco)) {
                                        $cell->setValue($banco->nombre);
                                    } else {
                                        $cell->setValue("");
                                    }
                                }
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->cell('P' . $i, function ($cell) use ($eorden) {
                                if ($eorden->comprobante != null) {
                                    $cell->setValue($eorden->comprobante);
                                } else {
                                    $cell->setValue("Sin Facturar");
                                }
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->cell('Q' . $i, function ($cell) use ($cierre_caja) {
                                // manipulate the cel
                                $cell->setValue(substr($cierre_caja->crea->nombre1, 0, 1) . " " . $cierre_caja->crea->apellido1);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->cell('R' . $i, function ($cell) use ($cierre_caja) {
                                // manipulate the cel
                                $cell->setValue($cierre_caja->observacion);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $cant++;
                            $i++;
                        }
                    }
                }
                //}
                // $sheet->cell('A' . $i, function ($cell) {
                //     // manipulate the cel
                //     $cell->setValue("");
                //     $cell->setBackground('#D1F2EB');
                //     $cell->setBorder('thin', 'thin', 'thin', 'thin');
                // });
                $sheet->cell('A' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue("");
                    $cell->setBackground('#D1F2EB');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue("");
                    $cell->setBackground('#D1F2EB');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue("");
                    $cell->setBackground('#D1F2EB');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue("TOTAL");
                    $cell->setBackground('#D1F2EB');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E' . $i, function ($cell) use ($ac_efectivo) {
                    // manipulate the cel
                    $cell->setValue($ac_efectivo);
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->getStyle('E' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                $sheet->cell('F' . $i, function ($cell) use ($ac_cheque) {
                    // manipulate the cel
                    $cell->setValue($ac_cheque);
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->getStyle('F' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                $sheet->cell('G' . $i, function ($cell) use ($ac_deposito) {
                    // manipulate the cel
                    $cell->setValue($ac_deposito);
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->getStyle('G' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                $sheet->cell('H' . $i, function ($cell) use ($ac_transf) {
                    // manipulate the cel
                    $cell->setValue($ac_transf);
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->getStyle('H' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                $sheet->cell('I' . $i, function ($cell) use ($ac_tar_cre) {
                    // manipulate the cel
                    $cell->setValue($ac_tar_cre);
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->getStyle('I' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                $sheet->cell('J' . $i, function ($cell) use ($ac_tar_deb) {
                    // manipulate the cel
                    $cell->setValue($ac_tar_deb);
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->getStyle('J' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                $sheet->cell('K' . $i, function ($cell) use ($ac_ppago) {
                    // manipulate the cel
                    $cell->setValue($ac_ppago);
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->getStyle('K' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                $sheet->cell('L' . $i, function ($cell) use ($ac_online) {
                    // manipulate the cel
                    $cell->setValue($ac_online);
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->getStyle('L' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                $sheet->cell('M' . $i, function ($cell) use ($ac_datalink) {
                    // manipulate the cel
                    $cell->setValue($ac_datalink);
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->getStyle('M' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                $sheet->cell('N' . $i, function ($cell) use ($ac_oda) {
                    // manipulate the cel
                    $cell->setValue($ac_oda);
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->getStyle('N' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                $sheet->cell('O' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue("");
                    $cell->setBackground('#D1F2EB');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('P' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue("");
                    $cell->setBackground('#D1F2EB');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('Q' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue("");
                    $cell->setBackground('#D1F2EB');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('R' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue("");
                    $cell->setBackground('#D1F2EB');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
            });

            $excel->sheet('EFECTIVO', function ($sheet) use ($records, $cabecera_excel, $seguros_cierre_caja, $ct_fecha_desde, $ct_fecha_hasta, $fecha_desde) {
                $sheet->mergeCells('A1:K1');
                $sheet->cell('A1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('EFECTIVO');
                    $cell->setFontSize(20);
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('A2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ORDEN');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('B2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('C2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PACIENTE');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('D2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SEGURO');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('E2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SUB-TOTAL');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('F2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DESC');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('G2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('COMPROBANTE');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('H2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('F. VALOR');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('I2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('VALOR COBRADO');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('J2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('REF.');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('K2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NOTA');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });


                $cant = 1;
                $i = 3;
                $efectivo_sub_total = 0;
                $efectivo_f_valor = 0;
                $efectivo_descuento = 0;
                $total_efectivo_parcial = 0;
                //foreach($seguros_cierre_caja as $seguro ){
                foreach ($records as $val) {
                    $seguro = Seguro::find($val->id_seguro);
                    if ($seguro->tipo != 0) {
                        if (in_array($seguro->id, array(4, 33)) == false) {
                            $eorden = Examen_Orden::find($val->id);
                            $cierre_caja = CierreCaja::find($val->id_caja);
                            $efectivo = $eorden->detalle_forma_pago->where('id_tipo_pago', '1')->sum('valor');
                            $efectivo_parcial = $eorden->detalle_forma_pago->where('id_tipo_pago', '1')->first();
                            if ($efectivo > 0) {
                                $efectivo_sub_total += $eorden->valor;
                                $efectivo_f_valor += $eorden->total_valor;
                                $efectivo_descuento += $eorden->descuento_valor;
                                $total_efectivo_parcial += $efectivo_parcial->valor;
                                $sheet->cell('A' . $i, function ($cell) use ($eorden) {
                                    // manipulate the cel
                                    $cell->setValue($eorden->id);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $sheet->cell('B' . $i, function ($cell) use ($eorden) {
                                    // manipulate the cel
                                    $cell->setValue(substr($eorden->fecha_orden, 0, 10));
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $sheet->cell('C' . $i, function ($cell) use ($eorden) {
                                    // manipulate the cel
                                    $cell->setValue($eorden->paciente->apellido1 . " " . $eorden->paciente->apellido2 . " " . $eorden->paciente->nombre1 . " " . $eorden->paciente->nombre2);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $sheet->cell('D' . $i, function ($cell) use ($eorden) {
                                    $nivel_nombre = "";
                                    if ($eorden->id_nivel != null) {
                                        $nivel_nombre = $eorden->nivel->nombre;
                                    }
                                    $cell->setValue($eorden->seguro->nombre . " " . $nivel_nombre);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $sheet->cell('E' . $i, function ($cell) use ($eorden) {
                                    // manipulate the cel
                                    $cell->setValue($eorden->valor);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->getStyle('E' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                                $sheet->cell('F' . $i, function ($cell) use ($eorden) {
                                    // manipulate the cel
                                    $cell->setValue($eorden->descuento_valor);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->getStyle('F' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                                $sheet->cell('G' . $i, function ($cell) use ($eorden) {
                                    if ($eorden->comprobante != null) {
                                        $cell->setValue($eorden->comprobante);
                                    } else {
                                        $cell->setValue("Sin Facturar");
                                    }
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $sheet->cell('H' . $i, function ($cell) use ($eorden) {
                                    // manipulate the cel
                                    $cell->setValue($eorden->total_valor);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->getStyle('H' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                                $sheet->cell('I' . $i, function ($cell) use ($eorden, $efectivo_parcial) {
                                    // manipulate the cel
                                    $cell->setValue($efectivo_parcial->valor);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->getStyle('I' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                                $sheet->cell('J' . $i, function ($cell) {
                                    // manipulate the cel
                                    $cell->setValue("EFECTIVO");
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $sheet->cell('K' . $i, function ($cell) use ($cierre_caja) {
                                    // manipulate the cel
                                    $cell->setValue($cierre_caja->observacion);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $i++;
                            }
                        }
                    }
                }
                $posicion = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W");
                $array = ["", "", "TOTAL", "", $efectivo_sub_total, $efectivo_descuento, "", $efectivo_f_valor, $total_efectivo_parcial,"",""];
                for ($y = 0; $y < count($array); $y++) {
                    $sheet->cell('' . $posicion[$y] . '' . $i, function ($cell) use ($array, $y) {
                        $cell->setValue($array[$y]);
                        $cell->setBackground('#D1F2EB');
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->getStyle('' . $posicion[$y] . '' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                }
            });

            $excel->sheet('CHEQUE', function ($sheet) use ($records, $cabecera_excel, $seguros_cierre_caja, $ct_fecha_desde, $ct_fecha_hasta, $fecha_desde) {
                $sheet->mergeCells('A1:L1');
                $sheet->cell('A1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CHEQUE');
                    $cell->setFontSize(20);
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('A2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ORDEN');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('B2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('C2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PACIENTE');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('D2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SEGURO');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('E2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SUB-TOTAL');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('F2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DESC');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('G2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('COMPROBANTE');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('H2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('F. VALOR');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('I2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('VALOR COBRADO');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('J2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('BANCO');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('K2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('REF.');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('L2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NOTA');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $cant = 1;
                $i = 3;
                $cheque_sub_total = 0;
                $cheque_f_valor = 0;
                $cheque_descuento = 0;
                $total_cheque_parcial = 0;
                //foreach($seguros_cierre_caja as $seguro ){
                foreach ($records as $val) {
                    $seguro = Seguro::find($val->id_seguro);
                    if ($seguro->tipo != 0) {
                        if (in_array($seguro->id, array(4, 33)) == false) {
                            $eorden = Examen_Orden::find($val->id);
                            $cierre_caja = CierreCaja::find($val->id_caja);
                            $cheque = $eorden->detalle_forma_pago->where('id_tipo_pago', '2')->sum('valor');
                            $cheque_parcial = $eorden->detalle_forma_pago->where('id_tipo_pago', '2')->first();
                            if ($cheque > 0) {
                                $cheque_sub_total += $eorden->valor;
                                $cheque_f_valor += $eorden->total_valor;
                                $cheque_descuento += $eorden->descuento_valor;
                                $total_cheque_parcial += $cheque_parcial->valor;
                                $sheet->cell('A' . $i, function ($cell) use ($eorden) {
                                    // manipulate the cel
                                    $cell->setValue($eorden->id);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $sheet->cell('B' . $i, function ($cell) use ($eorden) {
                                    // manipulate the cel
                                    $cell->setValue(substr($eorden->fecha_orden, 0, 10));
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $sheet->cell('C' . $i, function ($cell) use ($eorden) {
                                    // manipulate the cel
                                    $cell->setValue($eorden->paciente->apellido1 . " " . $eorden->paciente->apellido2 . " " . $eorden->paciente->nombre1 . " " . $eorden->paciente->nombre2);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $sheet->cell('D' . $i, function ($cell) use ($eorden) {
                                    $nivel_nombre = "";
                                    if ($eorden->id_nivel != null) {
                                        $nivel_nombre = $eorden->nivel->nombre;
                                    }
                                    $cell->setValue($eorden->seguro->nombre . " " . $nivel_nombre);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $sheet->cell('E' . $i, function ($cell) use ($eorden) {
                                    // manipulate the cel
                                    $cell->setValue($eorden->valor);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->getStyle('E' . $i)->getNumberFormat()->setFormatCode('$ 0.00');


                                $sheet->cell('F' . $i, function ($cell) use ($eorden) {
                                    // manipulate the cel
                                    $cell->setValue($eorden->descuento_valor);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->getStyle('F' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                                $sheet->cell('G' . $i, function ($cell) use ($eorden) {
                                    if ($eorden->comprobante != null) {
                                        $cell->setValue($eorden->comprobante);
                                    } else {
                                        $cell->setValue("Sin Facturar");
                                    }
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->getStyle('G' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                                $sheet->cell('H' . $i, function ($cell) use ($eorden) {
                                    // manipulate the cel
                                    $cell->setValue($eorden->total_valor);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->getStyle('H' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                                $sheet->cell('I' . $i, function ($cell) use ($eorden, $cheque_parcial) {
                                    // manipulate the cel
                                    $cell->setValue($cheque_parcial->valor);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->getStyle('I' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                                $sheet->cell('J' . $i, function ($cell) use ($eorden) {
                                    // manipulate the cel
                                    if (!is_null($eorden->detalle_forma_pago->first())) {
                                        $banco = \Sis_medico\Ct_Bancos::find($eorden->detalle_forma_pago->where('id_tipo_pago', '2')->first()->banco);
                                        if (!is_null($banco)) {
                                            $cell->setValue($banco->nombre);
                                        } else {
                                            $cell->setValue("");
                                        }
                                    }
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $sheet->cell('K' . $i, function ($cell) use ($eorden) {
                                    if(!is_null($eorden->detalle_forma_pago->first())){
                                        $ref = $eorden->detalle_forma_pago->where('id_tipo_pago', '2')->first()->numero;
                                        if (!is_null($ref)) {
                                            $cell->setValue($ref);
                                        } else {
                                            $cell->setValue("");
                                        }
                                    }
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $sheet->cell('L' . $i, function ($cell) use ($cierre_caja) {
                                    // manipulate the cel
                                    $cell->setValue($cierre_caja->observacion);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $i++;
                            }
                        }
                    }
                }

                $posicion = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W");
                $array = ["", "", "TOTAL", "", $cheque_sub_total, $cheque_descuento, "", $cheque_f_valor,$total_cheque_parcial,"","",""];
                for ($y = 0; $y < count($array); $y++) {
                    $sheet->cell('' . $posicion[$y] . '' . $i, function ($cell) use ($array, $y) {
                        $cell->setValue($array[$y]);
                        $cell->setBackground('#D1F2EB');
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->getStyle('' . $posicion[$y] . '' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                }
                //}
            });

            $excel->sheet('DEPOSITO', function ($sheet) use ($records, $cabecera_excel, $seguros_cierre_caja, $ct_fecha_desde, $ct_fecha_hasta, $fecha_desde) {
                $sheet->mergeCells('A1:L1');
                $sheet->cell('A1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DEPOSITO');
                    $cell->setFontSize(20);
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('A2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ORDEN');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('B2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('C2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PACIENTE');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('D2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SEGURO');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('E2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SUB-TOTAL');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('F2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DESC');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('G2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('COMPROBANTE');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('H2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('F. VALOR');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('I2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('VALOR COBRADO');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('J2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('BANCO');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('K2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('REF.');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('L2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NOTA');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $cant = 1;
                $i = 3;
                $deposito_sub_total = 0;
                $deposito_f_valor = 0;
                $deposito_descuento = 0;
                $total_deposito_parcial = 0;

                //foreach($seguros_cierre_caja as $seguro ){
                foreach ($records as $val) {
                    $seguro = Seguro::find($val->id_seguro);
                    if ($seguro->tipo != 0) {
                        if (in_array($seguro->id, array(4, 33)) == false) {
                            $eorden = Examen_Orden::find($val->id);
                            $cierre_caja = CierreCaja::find($val->id_caja);
                            $deposito = $eorden->detalle_forma_pago->where('id_tipo_pago', '3')->sum('valor');
                            $deposito_parcial = $eorden->detalle_forma_pago->where('id_tipo_pago', '3')->first();
                            
                            if ($deposito > 0) {
                                $deposito_sub_total += $eorden->valor;
                                $deposito_f_valor += $eorden->total_valor;
                                $deposito_descuento += $eorden->descuento_valor;
                                $total_deposito_parcial += $deposito_parcial->valor;

                                $sheet->cell('A' . $i, function ($cell) use ($eorden) {
                                    // manipulate the cel
                                    $cell->setValue($eorden->id);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $sheet->cell('B' . $i, function ($cell) use ($eorden) {
                                    // manipulate the cel
                                    $cell->setValue(substr($eorden->fecha_orden, 0, 10));
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $sheet->cell('C' . $i, function ($cell) use ($eorden) {
                                    // manipulate the cel
                                    $cell->setValue($eorden->paciente->apellido1 . " " . $eorden->paciente->apellido2 . " " . $eorden->paciente->nombre1 . " " . $eorden->paciente->nombre2);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $sheet->cell('D' . $i, function ($cell) use ($eorden) {
                                    $nivel_nombre = "";
                                    if ($eorden->id_nivel != null) {
                                        $nivel_nombre = $eorden->nivel->nombre;
                                    }
                                    $cell->setValue($eorden->seguro->nombre . " " . $nivel_nombre);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $sheet->cell('E' . $i, function ($cell) use ($eorden) {
                                    // manipulate the cel
                                    $cell->setValue($eorden->valor);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->getStyle('E' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                                $sheet->cell('F' . $i, function ($cell) use ($eorden) {
                                    // manipulate the cel
                                    $cell->setValue($eorden->descuento_valor);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->getStyle('F' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                                $sheet->cell('G' . $i, function ($cell) use ($eorden) {
                                    if ($eorden->comprobante != null) {
                                        $cell->setValue($eorden->comprobante);
                                    } else {
                                        $cell->setValue("Sin Facturar");
                                    }
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->getStyle('G' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                                $sheet->cell('H' . $i, function ($cell) use ($eorden) {
                                    // manipulate the cel
                                    $cell->setValue($eorden->total_valor);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->getStyle('H' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                                $sheet->cell('I' . $i, function ($cell) use ($eorden, $deposito_parcial) {
                                    // manipulate the cel
                                    $cell->setValue($deposito_parcial->valor);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->getStyle('I' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                                $sheet->cell('J' . $i, function ($cell) use ($eorden) {
                                    // manipulate the cel
                                    if (!is_null($eorden->detalle_forma_pago->first())) {
                                        $banco = \Sis_medico\Ct_Bancos::find($eorden->detalle_forma_pago->where('id_tipo_pago', '3')->first()->banco);
                                        if (!is_null($banco)) {
                                            $cell->setValue($banco->nombre);
                                        } else {
                                            $cell->setValue("");
                                        }
                                    }
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $sheet->cell('K' . $i, function ($cell) use ($eorden) {
                                    if(!is_null($eorden->detalle_forma_pago->first())){
                                        $ref = $eorden->detalle_forma_pago->where('id_tipo_pago', '3')->first()->numero;
                                        if (!is_null($ref)) {
                                            $cell->setValue($ref);
                                        } else {
                                            $cell->setValue("");
                                        }
                                    }
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $sheet->cell('L' . $i, function ($cell) use ($cierre_caja) {
                                    // manipulate the cel
                                    $cell->setValue($cierre_caja->observacion);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $i++;
                            }
                        }
                    }
                }

                $posicion = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W");
                $array = ["", "", "TOTAL", "", $deposito_sub_total, $deposito_descuento, "", $deposito_f_valor,$total_deposito_parcial,"","",""];
                for ($y = 0; $y < count($array); $y++) {
                    $sheet->cell('' . $posicion[$y] . '' . $i, function ($cell) use ($array, $y) {
                        $cell->setValue($array[$y]);
                        $cell->setBackground('#D1F2EB');
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->getStyle('' . $posicion[$y] . '' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                }
                //}
            });

            $excel->sheet('TRANSFERENCIA', function ($sheet) use ($records, $ct_fecha_desde, $ct_fecha_hasta, $fecha_desde) {
                $sheet->mergeCells('A1:L1');
                $sheet->cell('A1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TRANSFERENCIA');
                    $cell->setFontSize(20);
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('A2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ORDEN');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('B2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('C2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PACIENTE');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('D2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SEGURO');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('E2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SUB-TOTAL');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('F2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DESC');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('G2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('COMPROBANTE');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('H2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('F. VALOR');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('I2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('VALOR COBRADO');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('J2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('BANCO');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('K2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('REF.');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('L2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NOTA');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $cant = 1;
                $i = 3;
                $trasferencia_sub_total = 0;
                $trasferencia_f_valor = 0;
                $trasferencia_descuento = 0;
                $total_transferencia_parcial = 0;

                //foreach($seguros_cierre_caja as $seguro ){
                foreach ($records as $val) {
                    $seguro = Seguro::find($val->id_seguro);
                    if ($seguro->tipo != 0) {
                        if (in_array($seguro->id, array(4, 33)) == false) {
                            $eorden = Examen_Orden::find($val->id);
                            $cierre_caja = CierreCaja::find($val->id_caja);
                            $transferencia = $eorden->detalle_forma_pago->where('id_tipo_pago', '5')->sum('valor');
                            $transferencia_parcial = $eorden->detalle_forma_pago->where('id_tipo_pago', '5')->first();
                            if ($transferencia > 0) {
                                $trasferencia_sub_total += $eorden->valor;
                                $trasferencia_f_valor += $eorden->total_valor;
                                $trasferencia_descuento += $eorden->descuento_valor;
                                $total_transferencia_parcial += $transferencia_parcial->valor;

                                $sheet->cell('A' . $i, function ($cell) use ($eorden) {
                                    // manipulate the cel
                                    $cell->setValue($eorden->id);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $sheet->cell('B' . $i, function ($cell) use ($eorden) {
                                    // manipulate the cel
                                    $cell->setValue(substr($eorden->fecha_orden, 0, 10));
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $sheet->cell('C' . $i, function ($cell) use ($eorden) {
                                    // manipulate the cel
                                    $cell->setValue($eorden->paciente->apellido1 . " " . $eorden->paciente->apellido2 . " " . $eorden->paciente->nombre1 . " " . $eorden->paciente->nombre2);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $sheet->cell('D' . $i, function ($cell) use ($eorden) {
                                    $nivel_nombre = "";
                                    if ($eorden->id_nivel != null) {
                                        $nivel_nombre = $eorden->nivel->nombre;
                                    }
                                    $cell->setValue($eorden->seguro->nombre . " " . $nivel_nombre);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $sheet->cell('E' . $i, function ($cell) use ($eorden) {
                                    // manipulate the cel
                                    $cell->setValue($eorden->valor);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->getStyle('E' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                                $sheet->cell('F' . $i, function ($cell) use ($eorden) {
                                    // manipulate the cel
                                    $cell->setValue($eorden->descuento_valor);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->getStyle('F' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                                $sheet->cell('G' . $i, function ($cell) use ($eorden) {
                                    if ($eorden->comprobante != null) {
                                        $cell->setValue($eorden->comprobante);
                                    } else {
                                        $cell->setValue("Sin Facturar");
                                    }
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $sheet->cell('H' . $i, function ($cell) use ($eorden) {
                                    // manipulate the cel
                                    $cell->setValue($eorden->total_valor);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->getStyle('H' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                                $sheet->cell('I' . $i, function ($cell) use ($eorden, $transferencia_parcial) {
                                    // manipulate the cel
                                    $cell->setValue($transferencia_parcial->valor);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->getStyle('I' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                                $sheet->cell('J' . $i, function ($cell) use ($eorden) {
                                    // manipulate the cel
                                //dd($eorden->detalle_forma_pago);
                                    if (!is_null($eorden->detalle_forma_pago->first())) {
                                        //dd($eorden->detalle_forma_pago->first()->banco);
                                        $banco = \Sis_medico\Ct_Bancos::find($eorden->detalle_forma_pago->where('id_tipo_pago', '5')->first()->banco);
                                        if (!is_null($banco)) {
                                            $cell->setValue($banco->nombre);
                                        } else {
                                            $cell->setValue("");
                                        }
                                    }
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $sheet->cell('K' . $i, function ($cell) use ($eorden) {
                                    if(!is_null($eorden->detalle_forma_pago->first())){
                                        $ref = $eorden->detalle_forma_pago->where('id_tipo_pago', '5')->first()->numero;
                                        if (!is_null($ref)) {
                                            $cell->setValue($ref);
                                        } else {
                                            $cell->setValue("");
                                        }
                                    }
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $sheet->cell('L' . $i, function ($cell) use ($cierre_caja) {
                                    // manipulate the cel
                                    $cell->setValue($cierre_caja->observacion);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $i++;
                            }
                        }
                    }
                }

                $posicion = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W");
                $array = ["", "", "TOTAL", "", $trasferencia_sub_total, $trasferencia_descuento, "", $trasferencia_f_valor,$total_transferencia_parcial,"","",""];
                for ($y = 0; $y < count($array); $y++) {
                    $sheet->cell('' . $posicion[$y] . '' . $i, function ($cell) use ($array, $y) {
                        $cell->setValue($array[$y]);
                        $cell->setBackground('#D1F2EB');
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->getStyle('' . $posicion[$y] . '' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                }
                //}
            });

            $excel->sheet('TARJETAS', function ($sheet) use ($records, $cabecera_excel, $seguros_cierre_caja, $ct_fecha_desde, $ct_fecha_hasta, $fecha_desde) {
                $sheet->mergeCells('A1:P1');
                $sheet->cell('A1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TARJETA DE CREDITO/DEBITO');
                    $cell->setFontSize(20);
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('A2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ORDEN');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('B2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('C2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PACIENTE');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('D2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SEGURO');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('E2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SUB-TOTAL');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('F2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DESC');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('G2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('7% T/C');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('H2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('4.5% T/D');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('I2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('REF.');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('J2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('COMPROBANTE');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('K2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('F. VALOR');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('L2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('VALOR COBRADO');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('M2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('BANCO');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('N2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TARJETA');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('O2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('REF.');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('P2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NOTA');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $cant = 1;
                $i = 3;
                $creditodebito_subtotal = 0;
                $creditodebito_f_valor =0;
                $creditodebito_descuento = 0;
                $total_tarjetas_pacial = 0;

                //foreach($seguros_cierre_caja as $seguro ){
                foreach ($records as $val) {
                    $seguro = Seguro::find($val->id_seguro);
                    if ($seguro->tipo != 0) {
                        if (in_array($seguro->id, array(4, 33)) == false) {
                            $eorden = Examen_Orden::find($val->id);
                            $cierre_caja = CierreCaja::find($val->id_caja);
                            $tarjeta_credito = $eorden->detalle_forma_pago->where('id_tipo_pago', '4')->sum('valor');
                            $tarjeta_debito = $eorden->detalle_forma_pago->where('id_tipo_pago', '6')->sum('valor');
                            if ($tarjeta_credito > 0 or $tarjeta_debito > 0) {
                                if($tarjeta_credito > 0){
                                    $tarjetas_parcial = Examen_Detalle_Forma_Pago::where('id_examen_orden', $eorden->id)->where('id_tipo_pago', '4')->first();
                                }elseif($tarjeta_debito > 0){
                                    $tarjetas_parcial = Examen_Detalle_Forma_Pago::where('id_examen_orden', $eorden->id)->where('id_tipo_pago', '6')->first();
                                }



                                $creditodebito_subtotal +=$eorden->valor;
                                $creditodebito_f_valor +=$eorden->total_valor;
                                $creditodebito_descuento += $eorden->descuento_valor;
                                $total_tarjetas_pacial += $tarjetas_parcial->valor;

                                $sheet->cell('A' . $i, function ($cell) use ($eorden) {
                                    // manipulate the cel
                                    $cell->setValue($eorden->id);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $sheet->cell('B' . $i, function ($cell) use ($eorden) {
                                    // manipulate the cel
                                    $cell->setValue(substr($eorden->fecha_orden, 0, 10));
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $sheet->cell('C' . $i, function ($cell) use ($eorden) {
                                    // manipulate the cel
                                    $cell->setValue($eorden->paciente->apellido1 . " " . $eorden->paciente->apellido2 . " " . $eorden->paciente->nombre1 . " " . $eorden->paciente->nombre2);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $sheet->cell('D' . $i, function ($cell) use ($eorden) {
                                    $nivel_nombre = "";
                                    if ($eorden->id_nivel != null) {
                                        $nivel_nombre = $eorden->nivel->nombre;
                                    }
                                    $cell->setValue($eorden->seguro->nombre . " " . $nivel_nombre);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $sheet->cell('E' . $i, function ($cell) use ($eorden) {
                                    // manipulate the cel
                                    $cell->setValue($eorden->valor);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->getStyle('E' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                                $sheet->cell('F' . $i, function ($cell) use ($eorden) {
                                    // manipulate the cel
                                    $cell->setValue($eorden->descuento_valor);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->getStyle('F' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                                $sheet->cell('G' . $i, function ($cell) use ($eorden, $tarjeta_credito) {
                                    if ($tarjeta_credito > 0) {
                                        $cell->setValue($eorden->recargo_valor);
                                    } else {
                                        $cell->setValue(0);
                                    }
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->getStyle('G' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                                $sheet->cell('H' . $i, function ($cell) use ($eorden, $tarjeta_debito) {
                                    if ($tarjeta_debito > 0) {
                                        $cell->setValue($eorden->recargo_valor);
                                    } else {
                                        $cell->setValue(0);
                                    }
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->getStyle('H' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                                $sheet->cell('I' . $i, function ($cell) use ($eorden, $tarjeta_credito, $tarjeta_debito) {
                                    
                                    if (!is_null($eorden->detalle_forma_pago->first())) {
                                        if ($tarjeta_credito > 0){
                                            $ref = $eorden->detalle_forma_pago->where('id_tipo_pago', '4')->first()->numero;
                                            if (!is_null($ref)) {
                                                $cell->setValue($ref);
                                            } else {
                                                $cell->setValue("");
                                            }
                                        }elseif($tarjeta_debito > 0){
                                            $ref = $eorden->detalle_forma_pago->where('id_tipo_pago', '6')->first()->numero;
                                            if (!is_null($ref)) {
                                                $cell->setValue($ref);
                                            } else {
                                                $cell->setValue("");
                                            }
                                        }
                                        
                                    }

                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $sheet->cell('J' . $i, function ($cell) use ($eorden) {
                                    if ($eorden->comprobante != null) {
                                        $cell->setValue($eorden->comprobante);
                                    } else {
                                        $cell->setValue("Sin Facturar");
                                    }
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $sheet->cell('K' . $i, function ($cell) use ($eorden) {
                                    // manipulate the cel
                                    $cell->setValue($eorden->total_valor);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->getStyle('K' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                                $sheet->cell('L' . $i, function ($cell) use ($eorden, $tarjetas_parcial) {
                                    // manipulate the cel
                                    $cell->setValue($tarjetas_parcial->valor);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->getStyle('L' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                                $sheet->cell('M' . $i, function ($cell) use ($eorden, $tarjeta_credito, $tarjeta_debito) {
                                    // manipulate the cel
                                    if (!is_null($eorden->detalle_forma_pago->first())) {
                                        if ($tarjeta_credito > 0){
                                            $banco = \Sis_medico\Ct_Bancos::find($eorden->detalle_forma_pago->where('id_tipo_pago', '4')->first()->banco);
                                            if (!is_null($banco)) {
                                                $cell->setValue($banco->nombre);
                                            } else {
                                                $cell->setValue("");
                                            }
                                        }elseif($tarjeta_debito > 0){
                                            $banco = \Sis_medico\Ct_Bancos::find($eorden->detalle_forma_pago->where('id_tipo_pago', '6')->first()->banco);
                                            if (!is_null($banco)) {
                                                $cell->setValue($banco->nombre);
                                            } else {
                                                $cell->setValue("");
                                            }
                                        }

                                    }
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $sheet->cell('N' . $i, function ($cell) use ($eorden, $tarjeta_credito, $tarjeta_debito) {
                                    // manipulate the cel
                                    if (!is_null($eorden->detalle_forma_pago->first())) {
                                        if ($tarjeta_credito > 0){
                                            $tarjeta = \Sis_medico\Ct_Tipo_Tarjeta::find($eorden->detalle_forma_pago->where('id_tipo_pago', '4')->first()->tipo_tarjeta);
                                            if (!is_null($tarjeta)) {
                                                $cell->setValue($tarjeta->nombre);
                                            } else {
                                                $cell->setValue("");
                                            }
                                        }elseif($tarjeta_debito > 0){
                                            $tarjeta = \Sis_medico\Ct_Tipo_Tarjeta::find($eorden->detalle_forma_pago->where('id_tipo_pago', '6')->first()->tipo_tarjeta);
                                            if (!is_null($tarjeta)) {
                                                $cell->setValue($tarjeta->nombre);
                                            } else {
                                                $cell->setValue("");
                                            }
                                        }
                                    }
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $sheet->cell('O' . $i, function ($cell) use ($eorden, $tarjeta_credito, $tarjeta_debito) {
                                    if (!is_null($eorden->detalle_forma_pago->first())) {

                                        if ($tarjeta_credito > 0) {
                                            $cell->setValue("T/C");
                                        }elseif($tarjeta_debito > 0) {
                                            $cell->setValue("T/D");
                                        }
                                    }
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $sheet->cell('P' . $i, function ($cell) use ($cierre_caja) {
                                    // manipulate the cel
                                    $cell->setValue($cierre_caja->observacion);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $i++;
                            }
                        }
                    }
                }
     
                $posicion = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W");
                $array = ["", "", "TOTAL", "", $creditodebito_subtotal, $creditodebito_descuento, "", "","","",$creditodebito_f_valor,$total_tarjetas_pacial,"","","",""];
                for ($y = 0; $y < count($array); $y++) {
                    $sheet->cell('' . $posicion[$y] . '' . $i, function ($cell) use ($array, $y) {
                        $cell->setValue($array[$y]);
                        $cell->setBackground('#D1F2EB');
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->getStyle('' . $posicion[$y] . '' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                }
                //}
            });

            /*$excel->sheet('T. CREDITO', function ($sheet) use ($records, $cabecera_excel, $seguros_cierre_caja, $ct_fecha_desde, $ct_fecha_hasta, $fecha_desde) {
                $sheet->mergeCells('A1:M1'); 
                $sheet->cell('A1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TARJETA DE CREDITO');
                    $cell->setFontSize(20);
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('A2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ORDEN');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('B2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('C2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PACIENTE');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('D2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SEGURO');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('E2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SUB-TOTAL');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('F2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DESC');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('G2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('7% T/C');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('H2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FACTURA');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                
                $sheet->cell('I2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FACTURA VALOR');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('J2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('BANCO');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('K2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TARJETA');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('L2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('REFERENCIA');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('M2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NOTA');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                

                $cant = 1; $i = 3; $ac_efectivo = 0; $ac_cheque = 0; $ac_deposito = 0; $ac_transf = 0; $ac_tar_cre = 0; $ac_tar_deb = 0; $ac_ppago = 0; $total_final = 0;
                
                foreach($seguros_cierre_caja as $seguro ){
                    foreach($records as $val){
                        if($seguro->id == $val->id_seguro){
                            $eorden = Examen_Orden::find($val->id); 
                            $cierre_caja = CierreCaja::find($val->id_caja);
                            $tarjeta_credito = $eorden->detalle_forma_pago->where('id_tipo_pago','4')->sum('valor');
                            
                            if($tarjeta_credito > 0){
                                $sheet->cell('A' . $i, function ($cell) use ($eorden) {
                                // manipulate the cel
                                $cell->setValue($eorden->id);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $sheet->cell('B' . $i, function ($cell) use ($eorden) {
                                // manipulate the cel
                                $cell->setValue(substr($eorden->fecha_orden,0,10));
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $sheet->cell('C' . $i, function ($cell) use ($eorden) {
                                // manipulate the cel
                                $cell->setValue($eorden->paciente->apellido1 ." ". $eorden->paciente->apellido2 ." ". $eorden->paciente->nombre1 ." ". $eorden->paciente->nombre2);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                
                                $sheet->cell('D' . $i, function ($cell) use ($eorden) {
                                $nivel_nombre = "";
                                if($eorden->id_nivel != null){
                                    $nivel_nombre = $eorden->nivel->nombre;
                                }
                                $cell->setValue($eorden->seguro->nombre ." ". $nivel_nombre);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $sheet->cell('E' . $i, function ($cell) use ($eorden) {
                                // manipulate the cel
                                $cell->setValue(sprintf($eorden->valor, 2, ',', ' '));
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $sheet->cell('F' . $i, function ($cell) use ($eorden) {
                                // manipulate the cel
                                $cell->setValue(sprintf($eorden->descuento_valor, 2, ',', ' '));
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $sheet->cell('G' . $i, function ($cell) use ($eorden) {
                                // manipulate the cel
                                $cell->setValue(sprintf($eorden->recargo_valor, 2, ',', ' '));
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $sheet->cell('H' . $i, function ($cell) use ($eorden) {
                                if($eorden->comprobante != null){
                                    $cell->setValue($eorden->comprobante);
                                }else{
                                    $cell->setValue("Sin Facturar");
                                }
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $sheet->cell('I' . $i, function ($cell) use ($eorden) {
                                // manipulate the cel
                                $cell->setValue(sprintf($eorden->total_valor, 2, ',', ' '));
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $sheet->cell('J' . $i, function ($cell) use ($eorden) {
                                // manipulate the cel
                                if (!is_null($eorden->detalle_forma_pago->first())) {
                                    $banco = \Sis_medico\Ct_Bancos::find($eorden->detalle_forma_pago->first()->banco);
                                    if(!is_null($banco)){
                                        $cell->setValue($banco->nombre);
                                    }else{
                                        $cell->setValue("");
                                    }
                                }
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $sheet->cell('K' . $i, function ($cell) use ($eorden) {
                                // manipulate the cel
                                if (!is_null($eorden->detalle_forma_pago->first())) {
                                    $tarjeta = \Sis_medico\Ct_Tipo_Tarjeta::find($eorden->detalle_forma_pago->first()->tipo_tarjeta);
                                    if(!is_null($tarjeta)){
                                        $cell->setValue($tarjeta->nombre);
                                    }else{
                                        $cell->setValue("");
                                    }
                                }
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $sheet->cell('L' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue("TARJETA DE CREDITO");
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $sheet->cell('M' . $i, function ($cell) use ($cierre_caja) {
                                // manipulate the cel
                                $cell->setValue($cierre_caja->observacion);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $i++;
                            }
                        }
                    }
                }
            });*/

            /*$excel->sheet('T. DEBITO', function ($sheet) use ($records, $cabecera_excel, $seguros_cierre_caja, $ct_fecha_desde, $ct_fecha_hasta, $fecha_desde) {
                $sheet->mergeCells('A1:M1');
                $sheet->cell('A1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TARJETA DE DEBITO');
                    $cell->setFontSize(20);
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('A2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ORDEN');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('B2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('C2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PACIENTE');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('D2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SEGURO');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('E2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SUB-TOTAL');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('F2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DESC');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('G2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('4.5% T/D');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('H2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FACTURA');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                
                $sheet->cell('I2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FACTURA VALOR');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('J2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('BANCO');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('K2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TARJETA');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('L2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('REFERENCIA');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('M2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NOTA');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                

                $cant = 1; $i = 3; $ac_efectivo = 0; $ac_cheque = 0; $ac_deposito = 0; $ac_transf = 0; $ac_tar_cre = 0; $ac_tar_deb = 0; $ac_ppago = 0; $total_final = 0;
                
                foreach($seguros_cierre_caja as $seguro ){
                    foreach($records as $val){
                        if($seguro->id == $val->id_seguro){
                            $eorden = Examen_Orden::find($val->id); 
                            $cierre_caja = CierreCaja::find($val->id_caja);
                            $tarjeta_debito = $eorden->detalle_forma_pago->where('id_tipo_pago','6')->sum('valor');
                            
                            if($tarjeta_debito > 0){
                                $sheet->cell('A' . $i, function ($cell) use ($eorden) {
                                // manipulate the cel
                                $cell->setValue($eorden->id);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $sheet->cell('B' . $i, function ($cell) use ($eorden) {
                                // manipulate the cel
                                $cell->setValue(substr($eorden->fecha_orden,0,10));
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $sheet->cell('C' . $i, function ($cell) use ($eorden) {
                                // manipulate the cel
                                $cell->setValue($eorden->paciente->apellido1 ." ". $eorden->paciente->apellido2 ." ". $eorden->paciente->nombre1 ." ". $eorden->paciente->nombre2);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                
                                $sheet->cell('D' . $i, function ($cell) use ($eorden) {
                                $nivel_nombre = "";
                                if($eorden->id_nivel != null){
                                    $nivel_nombre = $eorden->nivel->nombre;
                                }
                                $cell->setValue($eorden->seguro->nombre ." ". $nivel_nombre);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $sheet->cell('E' . $i, function ($cell) use ($eorden) {
                                // manipulate the cel
                                $cell->setValue(sprintf($eorden->valor, 2, ',', ' '));
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $sheet->cell('F' . $i, function ($cell) use ($eorden) {
                                // manipulate the cel
                                $cell->setValue(sprintf($eorden->descuento_valor, 2, ',', ' '));
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $sheet->cell('G' . $i, function ($cell) use ($eorden) {
                                // manipulate the cel
                                $cell->setValue(sprintf($eorden->recargo_valor, 2, ',', ' '));
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $sheet->cell('H' . $i, function ($cell) use ($eorden) {
                                if($eorden->comprobante != null){
                                    $cell->setValue($eorden->comprobante);
                                }else{
                                    $cell->setValue("Sin Facturar");
                                }
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $sheet->cell('I' . $i, function ($cell) use ($eorden) {
                                // manipulate the cel
                                $cell->setValue(sprintf($eorden->total_valor, 2, ',', ' '));
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $sheet->cell('J' . $i, function ($cell) use ($eorden) {
                                // manipulate the cel
                                if (!is_null($eorden->detalle_forma_pago->first())) {
                                    $banco = \Sis_medico\Ct_Bancos::find($eorden->detalle_forma_pago->first()->banco);
                                    if(!is_null($banco)){
                                        $cell->setValue($banco->nombre);
                                    }else{
                                        $cell->setValue("");
                                    }
                                }
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $sheet->cell('K' . $i, function ($cell) use ($eorden) {
                                // manipulate the cel
                                if (!is_null($eorden->detalle_forma_pago->first())) {
                                    $tarjeta = \Sis_medico\Ct_Tipo_Tarjeta::find($eorden->detalle_forma_pago->first()->tipo_tarjeta);
                                    if(!is_null($tarjeta)){
                                        $cell->setValue($tarjeta->nombre);
                                    }else{
                                        $cell->setValue("");
                                    }
                                }
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $sheet->cell('L' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue("TARJETA DE DEBITO");
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $sheet->cell('M' . $i, function ($cell) use ($cierre_caja) {
                                // manipulate the cel
                                $cell->setValue($cierre_caja->observacion);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $i++;
                            }
                        }
                    }
                }
            });*/

            $excel->sheet('P. PAGO', function ($sheet) use ($records, $ct_fecha_desde, $ct_fecha_hasta, $fecha_desde) {
                $sheet->mergeCells('A1:K1');
                $sheet->cell('A1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PENDIENTE DE PAGO');
                    $cell->setFontSize(20);
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('A2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ORDEN');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('B2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('C2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PACIENTE');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('D2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SEGURO');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('E2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SUB-TOTAL');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('F2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DESC');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('G2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('COMPROBANTE');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('H2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('F. VALOR');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('I2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('VALOR COBRADO.');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('J2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('REF.');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('K2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NOTA');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $cant = 1;
                $i = 3;
                $p_pago_sub_total = 0;
                $p_pago_f_valor = 0;
                $p_pago_descuento = 0;
                $total_ppago_parcial = 0;

                //foreach($seguros_cierre_caja as $seguro ){
                foreach ($records as $val) {
                    $seguro = Seguro::find($val->id_seguro);
                    if ($seguro->tipo != 0) {
                        if (in_array($seguro->id, array(4, 33)) == false) {
                            $eorden = Examen_Orden::find($val->id);
                            $cierre_caja = CierreCaja::find($val->id_caja);
                            $ppago = $eorden->detalle_forma_pago->where('id_tipo_pago', '7')->sum('valor');
                            $ppago_parcial = $eorden->detalle_forma_pago->where('id_tipo_pago', '7')->first();

                            if ($ppago > 0) {
                                $p_pago_sub_total += $eorden->valor;
                                $p_pago_f_valor += $eorden->total_valor;
                                $p_pago_descuento += $eorden->descuento_valor;
                                $total_ppago_parcial += $ppago_parcial->valor;

                                $sheet->cell('A' . $i, function ($cell) use ($eorden) {
                                    // manipulate the cel
                                    $cell->setValue($eorden->id);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $sheet->cell('B' . $i, function ($cell) use ($eorden) {
                                    // manipulate the cel
                                    $cell->setValue(substr($eorden->fecha_orden, 0, 10));
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $sheet->cell('C' . $i, function ($cell) use ($eorden) {
                                    // manipulate the cel
                                    $cell->setValue($eorden->paciente->apellido1 . " " . $eorden->paciente->apellido2 . " " . $eorden->paciente->nombre1 . " " . $eorden->paciente->nombre2);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $sheet->cell('D' . $i, function ($cell) use ($eorden) {
                                    $nivel_nombre = "";
                                    if ($eorden->id_nivel != null) {
                                        $nivel_nombre = $eorden->nivel->nombre;
                                    }
                                    $cell->setValue($eorden->seguro->nombre . " " . $nivel_nombre);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $sheet->cell('E' . $i, function ($cell) use ($eorden) {
                                    // manipulate the cel
                                    $cell->setValue($eorden->valor);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->getStyle('E' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                                $sheet->cell('F' . $i, function ($cell) use ($eorden) {
                                    // manipulate the cel
                                    $cell->setValue($eorden->descuento_valor);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->getStyle('F' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                                $sheet->cell('G' . $i, function ($cell) use ($eorden) {
                                    if ($eorden->comprobante != null) {
                                        $cell->setValue($eorden->comprobante);
                                    } else {
                                        $cell->setValue("Sin Facturar");
                                    }
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $sheet->cell('H' . $i, function ($cell) use ($eorden) {
                                    // manipulate the cel
                                    $cell->setValue($eorden->total_valor);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->getStyle('H' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                                $sheet->cell('I' . $i, function ($cell) use ($eorden, $ppago_parcial) {
                                    // manipulate the cel
                                    $cell->setValue($ppago_parcial->valor);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->getStyle('I' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                                $sheet->cell('J' . $i, function ($cell) {
                                    // manipulate the cel
                                    $cell->setValue("PENDIENTE DE PAGO");
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $sheet->cell('K' . $i, function ($cell) use ($cierre_caja) {
                                    // manipulate the cel
                                    $cell->setValue($cierre_caja->observacion);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $i++;
                            }
                        }
                    }
                }

                $posicion = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W");
                $array = ["", "", "TOTAL", "", $p_pago_sub_total, $p_pago_descuento, "",$p_pago_f_valor,$total_ppago_parcial,"",""];
                for ($y = 0; $y < count($array); $y++) {
                    $sheet->cell('' . $posicion[$y] . '' . $i, function ($cell) use ($array, $y) {
                        $cell->setValue($array[$y]);
                        $cell->setBackground('#D1F2EB');
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->getStyle('' . $posicion[$y] . '' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                }
                //}
            });


            $excel->sheet('ONLINE', function ($sheet) use ($records, $ct_fecha_desde, $ct_fecha_hasta, $fecha_desde) {
                $sheet->mergeCells('A1:J1');
                $sheet->cell('A1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PAGO ONLINE');
                    $cell->setFontSize(20);
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('A2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ORDEN');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('B2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('C2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PACIENTE');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('D2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SEGURO');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('E2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SUB-TOTAL');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('F2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DESC');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('G2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('COMPROBANTE');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                
                $sheet->cell('H2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('F. VALOR');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('I2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('REF.');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('J2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NOTA');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $cant = 1;
                $i = 3;
                $p_pago_sub_total = 0;
                $p_pago_f_valor = 0;
                $p_pago_descuento = 0;
                $total_ppago_parcial = 0;

                //foreach($seguros_cierre_caja as $seguro ){
                foreach ($records as $val) {
                    $seguro = Seguro::find($val->id_seguro);
                    if ($seguro->tipo != 0) {
                        if (in_array($seguro->id, array(4, 33)) == false) {
                            $eorden = Examen_Orden::find($val->id);
                            $cierre_caja = CierreCaja::find($val->id_caja);
                            //$ppago = $eorden->detalle_forma_pago->where('id_tipo_pago', '7')->sum('valor');
                            //$ppago_parcial = $eorden->detalle_forma_pago->where('id_tipo_pago', '7')->first();

                            if ($eorden->pago_online == 1) {
                                $p_pago_sub_total += $eorden->valor;
                                $p_pago_f_valor += $eorden->total_valor;
                                $p_pago_descuento += $eorden->descuento_valor;
                               
                                $sheet->cell('A' . $i, function ($cell) use ($eorden) {
                                    // manipulate the cel
                                    $cell->setValue($eorden->id);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    });

                                    $sheet->cell('B' . $i, function ($cell) use ($eorden) {
                                    // manipulate the cel
                                    $cell->setValue(substr($eorden->fecha_orden,0,10));
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    });

                                    $sheet->cell('C' . $i, function ($cell) use ($eorden) {
                                    // manipulate the cel
                                    $cell->setValue($eorden->paciente->apellido1 ." ". $eorden->paciente->apellido2 ." ". $eorden->paciente->nombre1 ." ". $eorden->paciente->nombre2);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    });
                                    
                                    $sheet->cell('D' . $i, function ($cell) use ($eorden) {
                                    $nivel_nombre = "";
                                    if($eorden->id_nivel != null){
                                        $nivel_nombre = $eorden->nivel->nombre;
                                    }
                                    $cell->setValue($eorden->seguro->nombre ." ". $nivel_nombre);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    });

                                    $sheet->cell('E' . $i, function ($cell) use ($eorden) {
                                    // manipulate the cel
                                    $cell->setValue($eorden->valor);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->getStyle('E' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                                    $sheet->cell('F' . $i, function ($cell) use ($eorden) {
                                    // manipulate the cel
                                    $cell->setValue($eorden->descuento_valor);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->getStyle('F' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                                    $sheet->cell('G' . $i, function ($cell) use ($eorden) {
                                    if($eorden->comprobante != null){
                                        $cell->setValue($eorden->comprobante);
                                    }else{
                                        $cell->setValue("Sin Facturar");
                                    }
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    });

                                    $sheet->cell('H' . $i, function ($cell) use ($eorden) {
                                    // manipulate the cel
                                    $cell->setValue($eorden->total_valor);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->getStyle('H' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                                $sheet->cell('I' . $i, function ($cell) {
                                    // manipulate the cel
                                    $cell->setValue("PAGO ONLINE");
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    });

                                $sheet->cell('J' . $i, function ($cell) use ($cierre_caja) {
                                    // manipulate the cel
                                    $cell->setValue($cierre_caja->observacion);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    });

                                $i++;
                            }
                        }
                    }
                }

                $posicion = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W");
                $array = ["", "", "TOTAL", "", $p_pago_sub_total, $p_pago_descuento, "",$p_pago_f_valor,"",""];
                for ($y = 0; $y < count($array); $y++) {
                    $sheet->cell('' . $posicion[$y] . '' . $i, function ($cell) use ($array, $y) {
                        $cell->setValue($array[$y]);
                        $cell->setBackground('#D1F2EB');
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->getStyle('' . $posicion[$y] . '' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                }
                //}
            });
            $excel->sheet('DATALINK', function ($sheet) use ($records, $cabecera_excel, $seguros_cierre_caja, $ct_fecha_desde, $ct_fecha_hasta, $fecha_desde) {
                $sheet->mergeCells('A1:K1');
                $sheet->cell('A1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DATALINK');
                    $cell->setFontSize(20);
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('A2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ORDEN');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PACIENTE');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SEGURO');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SUB-TOTAL');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DESC');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('G2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('COMPROBANTE');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('H2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('F. VALOR');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('I2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('VALOR COBRADO');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('J2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('REF.');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('K2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NOTA');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $cant = 1;
                $i = 3;
                $datalink_sub_total = 0;
                $datalink_f_valor = 0;
                $datalink_descuento = 0;
                $total_datalink_parcial = 0;
                //foreach($seguros_cierre_caja as $seguro ){
                foreach ($records as $val) {
                    $seguro = Seguro::find($val->id_seguro);
                    if ($seguro->tipo != 0) {
                        if (in_array($seguro->id, array(4, 33)) == false) {
                            $eorden = Examen_Orden::find($val->id);
                            $cierre_caja = CierreCaja::find($val->id_caja);
                            $datalink = $eorden->detalle_forma_pago->where('id_tipo_pago', '9')->sum('valor');
                            $datalink_parcial = $eorden->detalle_forma_pago->where('id_tipo_pago', '9')->first();
                            if ($datalink > 0) {
                                $datalink_sub_total += $eorden->valor;
                                $datalink_f_valor += $eorden->total_valor;
                                $datalink_descuento += $eorden->descuento_valor;
                                $total_datalink_parcial += $datalink_parcial->valor;
                                $sheet->cell('A' . $i, function ($cell) use ($eorden) {
                                    // manipulate the cel
                                    $cell->setValue($eorden->id);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    });
                                    $sheet->cell('B' . $i, function ($cell) use ($eorden) {
                                    // manipulate the cel
                                    $cell->setValue(substr($eorden->fecha_orden,0,10));
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    });
                                    $sheet->cell('C' . $i, function ($cell) use ($eorden) {
                                    // manipulate the cel
                                    $cell->setValue($eorden->paciente->apellido1 ." ". $eorden->paciente->apellido2 ." ". $eorden->paciente->nombre1 ." ". $eorden->paciente->nombre2);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    });
                                    $sheet->cell('D' . $i, function ($cell) use ($eorden) {
                                    $nivel_nombre = "";
                                    if($eorden->id_nivel != null){
                                        $nivel_nombre = $eorden->nivel->nombre;
                                    }
                                    $cell->setValue($eorden->seguro->nombre ." ". $nivel_nombre);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    });
                                    $sheet->cell('E' . $i, function ($cell) use ($eorden) {
                                    // manipulate the cel
                                    $cell->setValue($eorden->valor);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->getStyle('E' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                                    $sheet->cell('F' . $i, function ($cell) use ($eorden) {
                                    // manipulate the cel
                                    $cell->setValue($eorden->descuento_valor);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->getStyle('F' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                                    $sheet->cell('G' . $i, function ($cell) use ($eorden) {
                                    if($eorden->comprobante != null){
                                        $cell->setValue($eorden->comprobante);
                                    }else{
                                        $cell->setValue("Sin Facturar");
                                    }
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    });
                                    $sheet->cell('H' . $i, function ($cell) use ($eorden) {
                                    // manipulate the cel
                                    $cell->setValue($eorden->total_valor);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->getStyle('H' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                                $sheet->cell('I' . $i, function ($cell) use ($eorden, $datalink_parcial) {
                                    // manipulate the cel
                                    $cell->setValue($datalink_parcial->valor);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->getStyle('I' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                                $sheet->cell('J' . $i, function ($cell) {
                                    // manipulate the cel
                                    $cell->setValue("DATALINK");
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    });
                                $sheet->cell('K' . $i, function ($cell) use ($cierre_caja) {
                                    // manipulate the cel
                                    $cell->setValue($cierre_caja->observacion);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    });
                                $i++;
                            }
                        }
                    }
                }
                $posicion = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W");
                $array = ["", "", "TOTAL", "", $datalink_sub_total, $datalink_descuento, "", $datalink_f_valor, $total_datalink_parcial,"",""];
                for ($y = 0; $y < count($array); $y++) {
                    $sheet->cell('' . $posicion[$y] . '' . $i, function ($cell) use ($array, $y) {
                        $cell->setValue($array[$y]);
                        $cell->setBackground('#D1F2EB');
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->getStyle('' . $posicion[$y] . '' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                }
            });
            $excel->sheet('ODA', function ($sheet) use ($records, $cabecera_excel, $seguros_cierre_caja, $ct_fecha_desde, $ct_fecha_hasta, $fecha_desde) {
                $sheet->mergeCells('A1:J1');
                $sheet->cell('A1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ODA');
                    $cell->setFontSize(20);
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('A2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ORDEN');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PACIENTE');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SEGURO');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SUB-TOTAL');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DESC');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('G2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('COMPROBANTE');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('H2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('F. VALOR');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
            
                $sheet->cell('I2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('REF.');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('J2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NOTA');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $cant = 1;
                $i = 3;
                $oda_sub_total = 0;
                $oda_f_valor = 0;
                $oda_descuento = 0;
                $total_oda_parcial = 0;
                foreach ($records as $val) {
                    $seguro = Seguro::find($val->id_seguro);
                    if ($seguro->tipo != 0) {
                        if (in_array($seguro->id, array(4, 33)) == false) {
                            $eorden = Examen_Orden::find($val->id);
                            $cierre_caja = CierreCaja::find($val->id_caja);
                            $valor_oda = $eorden->valor - $eorden->valor_con_oda;
                            if ($eorden->cobrar_pac_pct < 100) {
                                $oda_sub_total += $eorden->valor;
                                $oda_f_valor += $eorden->valor - $eorden->valor_con_oda;
                                $oda_descuento += $eorden->descuento_valor;
                                $sheet->cell('A' . $i, function ($cell) use ($eorden) {
                                    // manipulate the cel
                                    $cell->setValue($eorden->id);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    });
                                    $sheet->cell('B' . $i, function ($cell) use ($eorden) {
                                    // manipulate the cel
                                    $cell->setValue(substr($eorden->fecha_orden,0,10));
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    });
                                    $sheet->cell('C' . $i, function ($cell) use ($eorden) {
                                    // manipulate the cel
                                    $cell->setValue($eorden->paciente->apellido1 ." ". $eorden->paciente->apellido2 ." ". $eorden->paciente->nombre1 ." ". $eorden->paciente->nombre2);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    });
                                    $sheet->cell('D' . $i, function ($cell) use ($eorden) {
                                    $nivel_nombre = "";
                                    if($eorden->id_nivel != null){
                                        $nivel_nombre = $eorden->nivel->nombre;
                                    }
                                    $cell->setValue($eorden->seguro->nombre ." ". $nivel_nombre);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    });
                                    $sheet->cell('E' . $i, function ($cell) use ($eorden) {
                                    // manipulate the cel
                                    $cell->setValue($eorden->valor);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->getStyle('E' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                                    $sheet->cell('F' . $i, function ($cell) use ($eorden) {
                                    // manipulate the cel
                                    $cell->setValue($eorden->descuento_valor);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                $sheet->getStyle('F' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                                    $sheet->cell('G' . $i, function ($cell) use ($eorden) {
                                    if($eorden->comprobante != null){
                                        $cell->setValue($eorden->comprobante);
                                    }else{
                                        $cell->setValue("Sin Facturar");
                                    }
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    });
                                    $sheet->cell('H' . $i, function ($cell) use ($eorden) {
                                    // manipulate the cel
                                    $cell->setValue($eorden->valor - $eorden->valor_con_oda);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    });
                                $sheet->getStyle('H' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                                $sheet->cell('I' . $i, function ($cell) {
                                    // manipulate the cel
                                    $cell->setValue("ODA");
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    });
                                $sheet->cell('J' . $i, function ($cell) use ($cierre_caja) {
                                    // manipulate the cel
                                    $cell->setValue($cierre_caja->observacion);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    });
                                $i++;
                            }
                        }
                    }
                }
                $posicion = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W");
                $array = ["", "", "TOTAL", "", $oda_sub_total, $oda_descuento, "", $oda_f_valor, "" ,""];
                for ($y = 0; $y < count($array); $y++) {
                    $sheet->cell('' . $posicion[$y] . '' . $i, function ($cell) use ($array, $y) {
                        $cell->setValue($array[$y]);
                        $cell->setBackground('#D1F2EB');
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->getStyle('' . $posicion[$y] . '' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                }
            });

            $titulos = array("ORDEN", "FECHA", "PACIENTE", "SEGURO", "SUB-TOTAL", "DESC", "COMPROBANTE", "F. VALOR", "NOTA");
            $posicion = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W");
            $excel->sheet('SIN FACTURACIÓN', function ($sheet) use ($titulos, $posicion, $records) {
                $sheet->mergeCells('A1:I1');
                $sheet->cell('A1', function ($cell) {
                    $cell->setValue('SIN FACTURAR');
                    $cell->setBackground('#D1F2EB');
                    $cell->setFontSize(20);
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $comienzo = 2;
                for ($i = 0; $i < count($titulos); $i++) {
                    $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($titulos, $i) {
                        $cell->setValue($titulos[$i]);
                        $cell->setFontWeight('bold');
                        $cell->setBackground('#D1F2EB');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                }
                $comienzo++;
                $subtotal = 0;
                $valor = 0;
                foreach ($records as $val) {
                    $datos_excel = array();
                    $eorden = Examen_Orden::find($val->id);
                    $cierre_caja = CierreCaja::find($val->id_caja);
                    $nivel_nombre = "";

                    if (is_null($eorden->comprobante)) {
                        if (!is_null($eorden->id_nivel)) {
                            $nivel_nombre = $eorden->nivel->nombre;
                        }
                        $subtotal += $eorden->valor;
                        $valor += $eorden->total_valor;
                        array_push($datos_excel, $eorden->id, substr($eorden->fecha_orden, 0, 10), $eorden->paciente->apellido1 . " " . $eorden->paciente->apellido2 . " " . $eorden->paciente->nombre1 . " " . $eorden->paciente->nombre2, $eorden->seguro->nombre . " " . $nivel_nombre, $eorden->valor, $eorden->descuento_valor, "no tiene comprobante", $eorden->total_valor, $cierre_caja->observacion);
                        for ($i = 0; $i < count($datos_excel); $i++) {
                            $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($datos_excel, $i) {
                                $cell->setValue($datos_excel[$i]);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->getStyle('E' . $comienzo)->getNumberFormat()->setFormatCode('$ 0.00');
                            $sheet->getStyle('F' . $comienzo)->getNumberFormat()->setFormatCode('$ 0.00');
                            $sheet->getStyle('H' . $comienzo)->getNumberFormat()->setFormatCode('$ 0.00');
                        }

                        $comienzo++;
                    }
                }
                $array = ["", "", "TOTAL", "", $subtotal, "", "", $valor, ""];
                for ($y = 0; $y < count($array); $y++) {
                    $sheet->cell('' . $posicion[$y] . '' . $comienzo, function ($cell) use ($array, $y) {
                        $cell->setValue($array[$y]);
                        $cell->setBackground('#D1F2EB');
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->getStyle('' . $posicion[$y] . '' . $comienzo)->getNumberFormat()->setFormatCode('$ 0.00');
                }
            });
        })->export('xlsx');
    }
}
