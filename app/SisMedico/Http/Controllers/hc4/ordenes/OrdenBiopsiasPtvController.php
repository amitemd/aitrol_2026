<?php

namespace Sis_medico\Http\Controllers\hc4\ordenes;

use Carbon\Carbon;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PHPExcel_Style_Alignment;
use Response;
use Sis_medico\Agenda;
use Sis_medico\Cie_10_3;
use Sis_medico\Cie_10_4;
use Sis_medico\Empresa;
use Sis_medico\Firma_Usuario;
use Sis_medico\Hc_Cie10;
use Sis_medico\Hc_Evolucion;
use Sis_medico\Historiaclinica;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Opcion_Usuario;
use Sis_medico\Orden_012;
use Sis_medico\Orden_012_Cie10;
use Sis_medico\Paciente;
use Sis_medico\Procedimiento;
use Sis_medico\hc_procedimientos;
use Sis_medico\Orden;
use Sis_medico\Seguro;
use Sis_medico\User;
use Sis_medico\Hc4_Tipo_Biopsias_Ptv;
use Sis_medico\Hc4_Biopsias_Ptv;
use Sis_medico\Hc4_Biopsias_Ptv_Detalle;

class OrdenBiopsiasPtvController extends Controller
{
    //NUEVA FUNCION ROL CON TABLA OPCION_USUARIO
    private function rol_new($opcion)
    {
        $rolUsuario     = Auth::user()->id_tipo_usuario;
        $opcion_usuario = Opcion_Usuario::where('id_opcion', $opcion)->where('id_tipo_usuario', $rolUsuario)->first();
        if (is_null($opcion_usuario)) {
            return true;
        }
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if (in_array($rolUsuario, array(1, 3)) == false) {
            return true;
        }
    }

    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    //Muestra el historial de las ordenes de Imagenes
    //Solo para Doctores

    public function index($hc_id_procedimientos)
    {

        $this->rol();
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $hc_procedimiento   = hc_procedimientos::find($hc_id_procedimientos);
        $ptv_biopsias       = $hc_procedimiento->ptv_biopsias->where('estado','1'); 

        return view('hc4.ordenes.ptv_biopsia.index', ['ptv_biopsias' => $ptv_biopsias]);

    }

    public function crear($tipo, $hc_id_procedimientos)
    {

        $this->rol();
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $hc_procedimiento   = hc_procedimientos::find($hc_id_procedimientos);//d($hc_procedimiento->historia->evoluciones->where('secuencia','0')->first());
        $evolucion          = $hc_procedimiento->historia->evoluciones->where('secuencia','0')->first();
        $dxs                = $hc_procedimiento->historia->diagnosticos;//dd($dxs);

        $tipo_biopsia       = Hc4_Tipo_Biopsias_Ptv::find($tipo);
        $detalles_tipo      = $tipo_biopsia->detalles->where('estado',1);//dd($detalles_tipo);

        $datos_clinicos = '';
        if(!is_null($evolucion)){
            $datos_clinicos = $evolucion->cuadro_clinico;
        }
        $diagnostico = '';
        if($dxs->count() > 0){
            foreach ($dxs as $dx) {
                $diagnostico = $diagnostico.$dx->cie10.' : '.$dx->ingreso_egreso.' - '.$dx->presuntivo_definitivo.'<br>';    
            }
        }

        $arr = [
            'id_paciente'               => $hc_procedimiento->historia->id_paciente,
            'datos_clinicos'            => $datos_clinicos,
            'diagnostico'               => $diagnostico,
            'id_usuariocrea'            => $idusuario,
            'id_usuariomod'             => $idusuario,
            'ip_creacion'               => $ip_cliente,
            'ip_modificacion'           => $ip_cliente,
            'id_doctor_solicita'        => $hc_procedimiento->id_doctor_examinador,
            'hc_id_procedimientos'      => $hc_procedimiento->id,
            'id_hc4_tipo_biopsias_ptv'  => $tipo_biopsia->id,
        ];

        $id_biopsia_ptv = Hc4_Biopsias_Ptv::insertGetId($arr);   

        foreach ($detalles_tipo as $detalle) {
            
            $arr_det = [
                'id_hc4_biopsia_ptv'  => $id_biopsia_ptv,
                'descripcion'         => $detalle->descripcion,
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
            ];

            Hc4_Biopsias_Ptv_Detalle::create($arr_det);

        }         

        return [ 'id_biopsia_ptv' => $id_biopsia_ptv ];
    }

    public function editar( $id_biopsia )
    {

        $this->rol();
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $biopsia_ptv = Hc4_Biopsias_Ptv::find($id_biopsia);

        return view('hc4.ordenes.ptv_biopsia.editar', ['biopsia_ptv' => $biopsia_ptv]);
    }

    public function eliminar( $id_biopsia )
    {

        $this->rol();
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $biopsia_ptv = Hc4_Biopsias_Ptv::find($id_biopsia);

        $biopsia_ptv->update([
            'estado' => 0,
            'id_usuariomod' => $idusuario,
            'ip_modificacion' => $ip_cliente,
        ]);

        return "ok";
    }

    public function update( Request $request )
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $id_biopsia = $request->id_biopsia;
        $otras_localizaciones = $request->otras_localizaciones;
        $otros_organos = $request->otros_organos;
        $datos_clinicos = $request->datos_clinicos;
        $diagnostico = $request->diagnostico;

        $biopsia_ptv = Hc4_Biopsias_Ptv::find($id_biopsia);
        
        foreach ($biopsia_ptv->detalles as $detalle) {
        
            if(isset($request['detalle-'.$detalle->id])){

                $arr = [
                    'detalle'       => $request['detalle-'.$detalle->id],
                    'id_usuariomod' => $idusuario,
                    'ip_modificacion' => $ip_cliente,
                ];    

                $detalle->update($arr);
            }    

        }    

        $arr2 = [
            'otras_localizaciones' => $otras_localizaciones,
            'otros_organos'        => $otros_organos,
            'datos_clinicos'       => $datos_clinicos, 
            'diagnostico'          => $diagnostico,
            'ip_modificacion'      => $ip_cliente,
            'id_usuariomod'        => $idusuario,
        ];

        $biopsia_ptv->update($arr2);

        return "ok";
    
    }

    public function imprimir($id_biopsia){

        $this->rol();
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $biopsia_ptv = Hc4_Biopsias_Ptv::find($id_biopsia);

        $empresa = Empresa::where('prioridad',1)->first();
        $vistaurl='hc4.ordenes.ptv_biopsia.imprimir';
        $view =  \View::make($vistaurl, compact('biopsia_ptv','empresa'))->render();
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->stream('orden_biopsia-'.$id_biopsia.'.pdf');
        
    }
}
