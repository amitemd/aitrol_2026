<?php

namespace Sis_medico\Http\Controllers\hc_admision;

use Carbon\Carbon;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PHPExcel_Style_Alignment;
use Response;
use PHPExcel_Style_Fill;
use Sis_medico\Agenda;
use Sis_medico\Cie_10_3;
use Sis_medico\Cie_10_4;
use Sis_medico\Empresa;
use Sis_medico\Examen_Orden;
use Sis_medico\Firma_Usuario;
use Sis_medico\Hc4_Biopsias;
use Sis_medico\Hc_Cie10;
use Sis_medico\Hc_Evolucion;
use Sis_medico\hc_procedimientos;
use Sis_medico\Hc_Procedimiento_Final;
use Sis_medico\hc_protocolo;
use Sis_medico\Historiaclinica;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Orden;
use Sis_medico\Orden_012;
use Sis_medico\Orden_012_Cie10;
use Sis_medico\Orden_Doctor;
use Sis_medico\Orden_Doctor_Detalle;
use Sis_medico\Paciente;
use Sis_medico\Procedimiento;
use Sis_medico\Seguro;
use Sis_medico\Tipo_Detalle_Orden;
use Sis_medico\Tipo_Procedimiento;
use Sis_medico\User;
use PHPExcel_Worksheet_Drawing;

class Orden_ProcController extends Controller
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
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if (in_array($rolUsuario, array(1, 3, 6, 11, 7, 20)) == false) {
            return true;
        }
    }

    private function rol_paciente()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if (in_array($rolUsuario, array(2)) == false) {
            return true;
        }
    }

    public function crear_editar($hcid)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $historiaclinica = Historiaclinica::find($hcid);

        $agenda = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'paciente.id_seguro', '=', 'seguros.id')
            ->join('users as uc', 'uc.id', 'agenda.id_usuariocrea')
            ->join('users as um', 'um.id', 'agenda.id_usuariomod')
            ->leftjoin('historiaclinica as h', 'h.id_agenda', 'agenda.id')
            ->leftjoin('seguros as hs', 'hs.id', 'h.id_seguro')
            ->leftjoin('sala', 'agenda.id_sala', 'sala.id')
            ->leftjoin('hospital', 'sala.id_hospital', 'hospital.id')
            ->leftjoin('users as ud', 'ud.id', 'agenda.id_doctor1')
            ->leftjoin('especialidad', 'especialidad.id', 'agenda.espid')
            ->leftjoin('empresa', 'empresa.id', 'agenda.id_empresa')
            ->leftjoin('procedimiento', 'procedimiento.id', 'agenda.id_procedimiento')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'paciente.alergias as palergias', 'paciente.antecedentes_pat as pantecedentes_pat', 'paciente.antecedentes_fam as pantecedentes_fam', 'paciente.antecedentes_quir as pantecedentes_quir', 'seguros.color as color', 'uc.apellido1 as ucapellido', 'um.apellido1 as umapellido', 'uc.nombre1 as ucnombre', 'um.nombre1 as umnombre', 'paciente.fecha_nacimiento', 'paciente.ocupacion', 'h.parentesco as hparentesco', 'paciente.parentesco as pparentesco', 'paciente.estadocivil', 'paciente.ciudad', 'paciente.lugar_nacimiento', 'paciente.direccion', 'paciente.telefono1', 'paciente.telefono2', 'seguros.nombre as snombre', 'sala.nombre_sala as slnombre', 'hospital.nombre_hospital as hsnombre', 'ud.nombre1 as udnombre', 'ud.apellido1 as udapellido', 'especialidad.nombre as esnombre', 'procedimiento.nombre as pnombre', 'empresa.nombrecomercial', 'paciente.sexo', 'paciente.gruposanguineo', 'paciente.transfusion', 'paciente.alergias', 'paciente.vacuna', 'paciente.historia_clinica', 'paciente.antecedentes_pat', 'paciente.antecedentes_fam', 'paciente.antecedentes_quir', 'h.hcid', 'paciente.referido', 'paciente.id_usuario', 'paciente.trabajo', 'paciente.observacion', 'paciente.alcohol', 'hs.nombre as hsnombre', 'h.presion', 'h.pulso', 'h.temperatura', 'h.o2', 'h.altura', 'h.peso', 'h.perimetro', 'h.examenes_realizar', 'h.id_seguro as h_idseguro')
            ->where('agenda.id', '=', $historiaclinica->id_agenda)
            ->first();

        $orden = Orden_Doctor::where('hcid', $hcid)->first();

        $seguros = Seguro::where('inactivo', '1')->get();

        $tipos = Tipo_Procedimiento::where('estado', '1')->get();

        $detalles = Tipo_Detalle_Orden::where('estado', '1')->orderBy('orden', 'asc')->get();

        $detalle_orden = null;
        if (!is_null($orden)) {

            $detalle_orden = Orden_Doctor_Detalle::where('id_orden_doctor', $orden->id)->get();
        } else {
            if ($agenda->proc_consul == 0) {
                $evolucion = Hc_Evolucion::where('hcid', $hcid)->first();
                $input     = [

                    'hcid'            => $hcid,
                    'id_paciente'     => $historiaclinica->id_paciente,
                    'motivo'          => $evolucion->motivo,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,

                ];
                Orden_Doctor::create($input);
            } else {
                $evolucion = hc_protocolo::where('hcid', $hcid)->first();
                $input     = [

                    'hcid'            => $hcid,
                    'id_paciente'     => $historiaclinica->id_paciente,
                    'motivo'          => $evolucion->motivo,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,

                ];
                Orden_Doctor::create($input);
            }
        }

        //dd(Date('d/m/Y',strtotime($orden->fecha_examen)));
        return view('hc_admision.orden_proc.orden_proc', ['agenda' => $agenda, 'seguros' => $seguros, 'orden' => $orden, 'tipos' => $tipos, 'detalles' => $detalles, 'detalle_orden' => $detalle_orden, 'historiaclinica' => $historiaclinica]);
    }

    public function imprimir_orden($hcid)
    {

        $historiaclinica = Historiaclinica::find($hcid);

        $agenda = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'paciente.id_seguro', '=', 'seguros.id')
            ->join('users as uc', 'uc.id', 'agenda.id_usuariocrea')
            ->join('users as um', 'um.id', 'agenda.id_usuariomod')
            ->leftjoin('historiaclinica as h', 'h.id_agenda', 'agenda.id')
            ->leftjoin('seguros as hs', 'hs.id', 'h.id_seguro')
            ->leftjoin('sala', 'agenda.id_sala', 'sala.id')
            ->leftjoin('hospital', 'sala.id_hospital', 'hospital.id')
            ->leftjoin('users as ud', 'ud.id', 'agenda.id_doctor1')
            ->leftjoin('especialidad', 'especialidad.id', 'agenda.espid')
            ->leftjoin('empresa', 'empresa.id', 'agenda.id_empresa')
            ->leftjoin('procedimiento', 'procedimiento.id', 'agenda.id_procedimiento')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'paciente.alergias as palergias', 'paciente.antecedentes_pat as pantecedentes_pat', 'paciente.antecedentes_fam as pantecedentes_fam', 'paciente.antecedentes_quir as pantecedentes_quir', 'seguros.color as color', 'uc.apellido1 as ucapellido', 'um.apellido1 as umapellido', 'uc.nombre1 as ucnombre', 'um.nombre1 as umnombre', 'paciente.fecha_nacimiento', 'paciente.ocupacion', 'h.parentesco as hparentesco', 'paciente.parentesco as pparentesco', 'paciente.estadocivil', 'paciente.ciudad', 'paciente.lugar_nacimiento', 'paciente.direccion', 'paciente.telefono1', 'paciente.telefono2', 'seguros.nombre as snombre', 'sala.nombre_sala as slnombre', 'hospital.nombre_hospital as hsnombre', 'ud.nombre1 as udnombre', 'ud.apellido1 as udapellido', 'especialidad.nombre as esnombre', 'procedimiento.nombre as pnombre', 'empresa.nombrecomercial', 'paciente.sexo', 'paciente.gruposanguineo', 'paciente.transfusion', 'paciente.alergias', 'paciente.vacuna', 'paciente.historia_clinica', 'paciente.antecedentes_pat', 'paciente.antecedentes_fam', 'paciente.antecedentes_quir', 'h.hcid', 'paciente.referido', 'paciente.id_usuario', 'paciente.trabajo', 'paciente.observacion', 'paciente.alcohol', 'hs.nombre as hsnombre', 'h.presion', 'h.pulso', 'h.temperatura', 'h.o2', 'h.altura', 'h.peso', 'h.perimetro', 'h.examenes_realizar', 'h.id_seguro as h_idseguro')
            ->where('agenda.id', '=', $historiaclinica->id_agenda)
            ->first();

        $orden = DB::table('orden_doctor as od')->where('od.hcid', $hcid)->join('users as u', 'u.id', 'od.id_usuariocrea')->select('od.*', 'u.nombre1', 'u.apellido1')->first();

        $seguros = Seguro::where('inactivo', '1')->get();

        $tipos = Tipo_Procedimiento::where('estado', '1')->get();

        $detalles = Tipo_Detalle_Orden::where('estado', '1')->orderBy('orden', 'asc')->get();

        $detalle_orden = null;
        if (!is_null($orden)) {

            $detalle_orden = Orden_Doctor_Detalle::where('id_orden_doctor', $orden->id)->get();
        }
        $age = Carbon::createFromDate(substr($agenda->fecha_nacimiento, 0, 4), substr($agenda->fecha_nacimiento, 5, 2), substr($agenda->fecha_nacimiento, 8, 2))->age;

        //return view('hc_admision.orden_proc.orden_pdf', ['agenda' => $agenda, 'seguros' => $seguros, 'orden' => $orden, 'tipos' => $tipos, 'detalles' => $detalles, 'detalle_orden' => $detalle_orden, 'age' => $age]);
        $view = \View::make('hc_admision.orden_proc.orden_pdf', compact('agenda', 'seguros', 'orden', 'tipos', 'detalles', 'detalle_orden', 'age'))->render();
        $pdf  = \App::make('dompdf.wrapper');

        $pdf->loadHTML($view);

        return $pdf->download('orden_doctor_' . $agenda->id_paciente . '.pdf');

        /*
$agenda = Agenda::find($id);
$historia = Historiaclinica::where('id_agenda', $id)->first();

//$historia = Historiaclinica::find($historia[0]->hcid);
if(!is_null($historia)){
$seguro = Seguro::find($historia->id_seguro);
$paciente = Paciente::find($historia->id_paciente);
$doctor = User::find($historia->id_doctor1);
$responsable = User::find($historia->id_usuariocrea);
if($agenda->proc_consul=='1'){
$pentax = Pentax::where('id_agenda',$agenda->id)->first();
$procedimientos = PentaxProc::where('id_pentax',$pentax->id)->get();
$procedimientos_txt = '';
foreach ($procedimientos as $value) {
if($procedimientos_txt==''){
$procedimientos_txt = procedimiento::find($value->id_procedimiento)->nombre;
}else{
$procedimientos_txt = $procedimientos_txt.'+'.procedimiento::find($value->id_procedimiento)->nombre;
}
}
}else{
$procedimientos_txt = 'CONSULTA';
}
$ControlDocController = new ControlDocController;
$documentos = $ControlDocController->carga_documentos_union($historia->hcid, $agenda->proc_consul, $seguro->tipo);

$data = $historia;
$date = $historia->created_at;

}else{
$seguro = Seguro::find($agenda->id_seguro);
$paciente = Paciente::find($agenda->id_paciente);
$doctor = User::find($agenda->id_doctor1);
$responsable = User::find($agenda->id_usuariocrea);
if($agenda->proc_consul=='1'){
$procedimientos_txt = procedimiento::find($agenda->id_procedimiento)->nombre;
$procedimientos = AgendaProcedimiento::where('id_agenda',$agenda->id)->get();
foreach ($procedimientos as $value) {

$procedimientos_txt = $procedimientos_txt.'+'.procedimiento::find($value->id_procedimiento)->nombre;

}
}else{
$procedimientos_txt = 'CONSULTA';
}

$documentos = null;

$data = $agenda;
$date = $agenda->created_at;

}

$empresa = Empresa::where('id',$agenda->id_empresa)->first();
if(is_null($empresa)){
$empresa = Empresa::find('1391707460001');
}

//$empresaxdoc = Empresa::find($agenda->id_empresa);

//nueva validacion broncoscopia 1/8/2018 ** use Sis_medico\Procedimiento_Empresa;
//$procedimiento_empresa = Procedimiento_Empresa::where('id_procedimiento',$agenda->id_procedimiento)->first();

//dd($empresa);

$paper_size = array(0,0,595,920);

//return view('hc_admision/formato/'.$documento->formato);
$view =  \View::make('hc_admision.formato.resumen', compact('data', 'date', 'empresa', 'age', 'paciente', 'agenda', 'doctor', 'seguro', 'responsable','procedimientos_txt','documentos'))->render();
$pdf = \App::make('dompdf.wrapper');

$pdf->loadHTML($view)/*->setPaper($paper_size, 'portrait');

//return view('hc_admision/formato/record',['data' => $data, 'empresa' => $empresa]);
if(!is_null($historia)){
return $pdf->download($historia->id_paciente.'_resumen_'.$historia->hcid.'.pdf');
}else{
return $pdf->download($agenda->id_paciente.'_resumenAG_'.$agenda->id.'.pdf');
} */
    }

    public function guardar(Request $request, $hcid)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        //return $request->all();

        $historiaclinica = DB::table('historiaclinica')->where('hcid', $hcid)->first();

        //return $historiaclinica->id_paciente;

        //return $request['hcid'];

        $orden_d = Orden_Doctor::where('hcid', $hcid)->first();

        if (!is_null($orden_d)) {

            $input = [

                'id_seguro'              => $request['id_seguro'],
                'motivo'                 => $request['motivo'],
                'fecha_examen'           => $request['fecha_examen'],
                'observacion'            => $request['observacion'],
                'endoscopia_urgencia'    => $request['endoscopia_urgencia'],
                'endoscopia_terapeutica' => $request['endoscopia_terapeutica'],
                'eco_doppler'            => $request['eco_doppler'],
                'ecografia'              => $request['ecografia'],
                'prueba_func'            => $request['prueba_func'],
                'campo1'                 => $request['campo1'],
                'campo2'                 => $request['campo2'],
                'campo3'                 => $request['campo3'],
                'campo4'                 => $request['campo4'],
                'campo5'                 => $request['campo5'],
                'campo6'                 => $request['campo6'],
                'campo7'                 => $request['campo7'],
                'campo8'                 => $request['campo8'],

                'id_usuariomod'          => $idusuario,

                'ip_modificacion'        => $ip_cliente,

            ];

            $orden_d->update($input);

            return "ok";
        }
    }

    public function existe($hcid, $id)
    {

        $orden = Orden_Doctor::where('hcid', $hcid)->first();
        //return $orden->id;
        if (!is_null($orden)) {
            $detalle = Orden_Doctor_Detalle::where('id_orden_doctor', $orden->id)->where('id_tipo_detalle_orden', $id)->first();
            //return $detalle
            if (is_null($detalle)) {
                return '0';
            }
        }
        return '1';
    }

    public function crear_detalle($hcid, $id)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $orden = Orden_Doctor::where('hcid', $hcid)->first();
        //return $orden->id;
        if (!is_null($orden)) {
            $detalle = Orden_Doctor_Detalle::where('id_orden_doctor', $orden->id)->where('id_tipo_detalle_orden', $id)->first();
            //return $detalle
            if (is_null($detalle)) {
                $input = [
                    'id_tipo_detalle_orden' => $id,
                    'id_orden_doctor'       => $orden->id,
                    'ip_creacion'           => $ip_cliente,
                    'ip_modificacion'       => $ip_cliente,
                    'id_usuariomod'         => $idusuario,
                    'id_usuariocrea'        => $idusuario,
                ];

                Orden_Doctor_Detalle::create($input);
            }
        }
        return 'ok';
    }

    public function eliminar($hcid, $id)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $orden = Orden_Doctor::where('hcid', $hcid)->first();
        //return $orden->id;
        if (!is_null($orden)) {
            $detalle = Orden_Doctor_Detalle::where('id_orden_doctor', $orden->id)->where('id_tipo_detalle_orden', $id)->first();
            //return $detalle
            if (!is_null($detalle)) {
                $detalle->delete();
            }
        }
        return 'ok';
    }

    //Nueva Funcionalidad Historial de Ordenes de Procedimiento: Endoscopico, Funcional, Imagenes
    public function historial_ordenes($id_paciente)
    {

        //dd($id_paciente);

        $paciente = Paciente::find($id_paciente);

        $listado_ordenes = Orden::where('id_paciente', $id_paciente)
            ->where('estado', 1)
            ->OrderBy('id', 'desc')
            ->get();
        //->OrderBy('o.created_at','desc')

        //dd($listado_ordenes);

        return view('hc_admision/orden_proc/historial_ordenes', ['listado_ordenes' => $listado_ordenes, 'paciente' => $paciente]);
    }

    //FUNCION IMPRIMIR ORDEN DE PROCEDIMIENTO
    //SOLO PARA DOCTORES
    public function imprimir_orden_hc3($id)
    {

        $orden_proc = Orden::find($id);

        if ((is_null($orden_proc->check_doctor)) && (is_null($orden_proc->id_doctor_firma))) {

            $doctor_firma = $orden_proc->id_doctor;
        } else {

            $doctor_firma = $orden_proc->id_doctor_firma;
        }

        if (!is_null($orden_proc)) {
            $firma = Firma_Usuario::where('id_usuario', $doctor_firma)->first();
        }

        $paciente = Paciente::find($orden_proc->id_paciente);

        if ($paciente->fecha_nacimiento != null) {
            $edad = Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;
        }

        //$id_doctor = Auth::user()->id;
        $doctor_solicitante = DB::table('users as us')
            ->where('us.id', $orden_proc->id_doctor)
            ->first();

        $vistaurl = "hc_admision.orden_proc.ordenes_proced_pdf";
        $view     = \View::make($vistaurl, compact('orden_proc', 'paciente', 'edad', 'doctor_solicitante', 'firma'))->render();

        /*$view =  \View::make($vistaurl, compact('orden','pct','detalle', 'resultados', 'agrupador', 'parametros', 'age', 'ucreador'))->render();*/

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('resultado-' . $id . '.pdf');
    }

    //HISTORIAL ORDENES DE LABORATORIO
    public function historial_ordenes_Laboratorio($id_paciente)
    {

        /*if($this->rol()){
        return response()->view('errors.404');
        }*/

        $paciente = Paciente::find($id_paciente);
        if($paciente==null){
            return response()->view('Paciente no encontrado');
        }

        $usuarios = User::where('id_tipo_usuario', '3')->where('estado', '1')->get();

        /*$ordenes = DB::table('examen_orden as eo')
        ->where('eo.id_paciente',$id_paciente)
        ->join('paciente as p','p.id','eo.id_paciente')
        ->join('seguros as s','s.id','eo.id_seguro')
        ->leftjoin('empresa as em','em.id','eo.id_empresa')
        ->leftjoin('nivel as n','n.id','eo.id_nivel')
        ->leftjoin('protocolo as proto','proto.id','eo.id_protocolo')
        ->leftjoin('users as d','d.id','eo.id_doctor_ieced')
        ->join('users as cu','cu.id','eo.id_usuariocrea')
        ->join('users as mu','mu.id','eo.id_usuariomod')
        ->where('eo.estado','1')
        ->select('eo.*','p.nombre1 as pnombre1','p.nombre2 as pnombre2','p.apellido2 as papellido2','p.apellido1 as papellido1','p.id as id_paciente','p.parentesco as parentesco','d.nombre1 as dnombre1','d.apellido1 as dapellido1','s.nombre as snombre','n.nombre as nnombre','em.nombrecomercial','cu.nombre1 as cnombre1','cu.apellido1 as capellido1','mu.nombre1 as mnombre1','mu.apellido1 as mapellido1','em.nombre_corto', 'proto.pre_post','s.tipo as stipo')
        ->OrderBy('created_at', 'desc')
        ->get();*/

        $ordenes = Examen_Orden::where('id_paciente', $id_paciente)
            ->where('estado', '1')
            ->OrderBy('fecha_orden', 'desc')->paginate(5);

        return view('hc_admision/orden_proc/listado_ordenes_laboratorio', ['ordenes' => $ordenes, 'usuarios' => $usuarios, 'paciente' => $paciente]);
    }

    public function historial_ordenes_paciente()
    {
        /*if ($this->rol_paciente()) {
        return response()->view('errors.404');
        }*/
        $id_paciente = Auth::user()->id;//dd($id_paciente);

        $paciente = Paciente::find($id_paciente);

        $usuarios = User::where('id_tipo_usuario', '3')->where('estado', '1')->get();

        /*$ordenes1 = Examen_Orden::join('paciente as p', 'p.id', 'examen_orden.id_paciente')
            ->join('users as usuario', 'usuario.id', 'p.id_usuario')
            ->where('usuario.id', $id_paciente)
            ->where('examen_orden.estado', '1')
            ->select('examen_orden.*')
            ->orderBy('examen_orden.fecha_orden', 'desc');

        $ordenes2 = Examen_Orden::join('paciente as p', 'p.id', 'examen_orden.id_paciente')
            ->join('labs_grupo_familiar as gf', 'gf.id', 'p.id')
            ->where('gf.id_usuario', $id_paciente)
            ->where('gf.estado', '1')
            ->where('examen_orden.estado', '1')
            ->select('examen_orden.*')
            ->orderBy('examen_orden.fecha_orden', 'desc');

        //dd($ordenes2,$ordenes1);
        $ordenes = $ordenes1->union($ordenes2)->get();*/

        $ordenes1 = Examen_Orden::join('paciente as p', 'p.id', 'examen_orden.id_paciente')
            ->join('users as usuario', 'usuario.id', 'p.id_usuario')
            ->where('usuario.id', $id_paciente)
            ->where('examen_orden.estado', '1')
            ->select('examen_orden.*');//dd($ordenes1->get());

        if(!is_null($paciente)){
             if($paciente->id != $paciente->id_usuario){

                $ordenes1 = Examen_Orden::join('paciente as p', 'p.id', 'examen_orden.id_paciente')
                    ->join('users as usuario', 'usuario.id', 'p.id')
                    ->where('usuario.id', $id_paciente)
                    ->where('examen_orden.estado', '1')
                    ->select('examen_orden.*');//dd($ordenes1->get());

            } 
        }       
           
        //->orderBy('examen_orden.fecha_orden', 'desc');

        $ordenes2 = Examen_Orden::join('paciente as p', 'p.id', 'examen_orden.id_paciente')
            ->join('labs_grupo_familiar as gf', 'gf.id', 'p.id')
            ->where('gf.id_usuario', $id_paciente)
            ->where('gf.estado', '1')
            ->where('examen_orden.estado', '1')
            ->select('examen_orden.*');


        //->orderBy('examen_orden.fecha_orden', 'desc');
        $ordenes = $ordenes1->union($ordenes2);
        $querySql = $ordenes->toSql();
        //dd($querySql);
        $ordenes = DB::table(DB::raw("($querySql order by fecha_orden desc) as a"))->mergeBindings($ordenes->getQuery());
        //dd($ordenes->get());
        $ordenes = $ordenes->paginate(5);
        return view('hc_admision/orden_proc/listado_ordenes_laboratorio', ['ordenes' => $ordenes, 'usuarios' => $usuarios, 'paciente' => $paciente]);
    }

    public function carga_012($hcid)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $evolucion       = Hc_Evolucion::where('hcid', $hcid)->where('secuencia', '0')->first();
        $historiaclinica = Historiaclinica::find($hcid);
        $agenda          = Agenda::find($historiaclinica->id_agenda);
        $fecha_agenda    = substr($agenda->fechaini, 0, 10);
        $doctores        = User::where('estado', '1')->where('id_tipo_usuario', '3')->OrderBy('apellido1', 'asc')->get();
        $seguros         = Seguro::where('inactivo', '1')->OrderBy('nombre', 'asc')->get();
        $empresas        = Empresa::where('estado', '1')->where('id', '<>', '9999999999')->OrderBy('nombrecomercial', 'asc')->get();
        //dd($seguros);

        if (!is_null($historiaclinica)) {
            $paciente = Paciente::find($historiaclinica->id_paciente); //Arriba
            if (!is_null($evolucion)) {
                $ordenes = Orden::where('id_paciente', $paciente->id)->whereBetween('fecha_orden', [$fecha_agenda . ' 0:00:00', $fecha_agenda . ' 23:59:59'])->where('estado', '1')->get();
                if ($ordenes->count() > 0) {
                    $descripcion = '';
                    $cont        = '0';
                    foreach ($ordenes as $orden) {
                        $ordenes_tipo = $orden->orden_tipo;
                        foreach ($ordenes_tipo as $ot) {
                            foreach ($ot->orden_procedimiento as $op) {
                                if ($cont == '0') {
                                    $descripcion = $op->procedimiento->nombre;
                                    $cont++;
                                } else {
                                    $descripcion = $descripcion . ' + ' . $op->procedimiento->nombre;
                                }
                            }
                        }
                    }
                    //return $descripcion;
                    //dd($ordenes[0]);
                    $orden_012 = Orden_012::whereBetween('fecha_orden', [$fecha_agenda . ' 0:00:00', $fecha_agenda . ' 23:59:59'])->where('estado', '1')->where('id_paciente', $historiaclinica->id_paciente)->first();
                    // dd($orden_012);
                    if (is_null($orden_012)) {
                        $arr = [
                            'id_paciente'        => $paciente->id,
                            //'referido' => $paciente->referido,
                            'id_doctor_solicita' => $ordenes[0]->id_doctor,
                            'id_doctor_firma'    => $ordenes[0]->id_doctor,
                            'fecha_orden'        => substr($agenda->fechaini, 0, 10),
                            'id_hc_evolucion'    => $evolucion->id,
                            'estado'             => '1',
                            'descripcion'        => $descripcion,
                            'motivo'             => $ordenes[0]->motivo_consulta,
                            'cuadro_clinico'     => $ordenes[0]->resumen_clinico,
                            'id_usuariocrea'     => $idusuario,
                            'id_usuariomod'      => $idusuario,
                            'ip_creacion'        => $ip_cliente,
                            'ip_modificacion'    => $ip_cliente,

                        ];
                        //return $arr;
                        $id_012    = Orden_012::insertGetId($arr);
                        $orden_012 = Orden_012::find($id_012);

                        $cie10 = Hc_Cie10::where('hcid', $historiaclinica->hcid)->groupBy('cie10')->get();
                        foreach ($cie10 as $value) {
                            $arr_cie10 = [
                                'id_orden_012'          => $id_012,
                                'cie10'                 => $value->cie10,
                                'presuntivo_definitivo' => $value->presuntivo_definitivo,
                                'id_usuariocrea'        => $idusuario,
                                'id_usuariomod'         => $idusuario,
                                'ip_creacion'           => $ip_cliente,
                                'ip_modificacion'       => $ip_cliente,

                            ];
                            Orden_012_Cie10::create($arr_cie10);
                        }
                    }

                    $orden_012_cie10 = Orden_012_Cie10::where('id_orden_012', $orden_012->id)->get();

                    return view('hc_admision/orden_012/ingreso_012', ['paciente' => $paciente, 'orden_012' => $orden_012, 'doctores' => $doctores, 'seguros' => $seguros, 'empresas' => $empresas, 'orden_012_cie10' => $orden_012_cie10]);
                }
                return "vacio";
            }
        }

        return "no encontro";
    }

    public function actualizar(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $orden_012 = Orden_012::find($request->id_orden);
        if (!is_null($orden_012)) {
            $arr = [
                'id_seguro'       => $request->id_seguro,
                'id_empresa'      => $request->id_empresa,
                'id_doctor_firma' => $request->id_doctor_examinador,
                'descripcion'     => $request->descripcion,
                'motivo'          => $request->motivo,
                'cuadro_clinico'  => $request->historia_clinica,
                'id_usuariomod'   => $idusuario,
                'ip_modificacion' => $ip_cliente,
            ];

            $orden_012->update($arr);
        }

        return 'ok';
    }

    public function carga_012_c10(Request $request)
    {
        //return $request->all();
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        if ($request['codigo'] == null) {
            return ['estado' => 'err', 'msn' => 'Seleccione un CIE10'];
        }

        $input2 = [
            'id_orden_012'          => $request['id_orden'],
            'cie10'                 => $request['codigo'],
            'presuntivo_definitivo' => $request['pre_def'],
            'ip_creacion'           => $ip_cliente,
            'ip_modificacion'       => $ip_cliente,
            'id_usuariocrea'        => $idusuario,
            'id_usuariomod'         => $idusuario,

        ];
        $id = Orden_012_Cie10::insertGetId($input2);

        $count = Orden_012_Cie10::where('id_orden_012', $request['id_orden'])->get()->count();

        $cie10 = Orden_012_Cie10::find($id);

        $c3 = Cie_10_3::find($cie10->cie10);
        if (!is_null($c3)) {
            $descripcion = $c3->descripcion;
        }
        $c4 = Cie_10_4::find($cie10->cie10);
        if (!is_null($c4)) {
            $descripcion = $c4->descripcion;
        }

        return ['count' => $count, 'id' => $id, 'cie10' => $cie10->cie10, 'descripcion' => $descripcion, 'pre_def' => $request['pre_def'], 'in_eg' => $request['in_eg']];
    }

    public function c012_c10eli($id)
    {

        $c10 = Orden_012_Cie10::find($id);
        $c10->delete();
        return 'ok';
    }

    public function imprimir_012($id)
    {
        //return "ok";
        $orden_012  = Orden_012::find($id);
        $paciente   = Paciente::find($orden_012->id_paciente);
        $id_empresa = $orden_012->evolucion->historiaclinica->agenda->id_empresa;
        $id_seguro  = $orden_012->evolucion->procedimiento->id_seguro;
        if ($id_seguro != null) {
            $id_seguro = $orden_012->evolucion->historiaclinica->id_seguro;
        }
        $empresa = Empresa::find($id_empresa);
        $seguro  = Seguro::find($id_seguro);
        $doctor  = User::find($orden_012->id_doctor_firma);
        $cie10   = Orden_012_Cie10::where('id_orden_012', $orden_012->id)->get();

        $age   = Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;
        $firma = Firma_Usuario::where('id_usuario', $orden_012->id_doctor_firma)->first();

        $view = \View::make('hc_admision.orden_012.orden012_pdf', compact('orden_012', 'age', 'paciente', 'empresa', 'seguro', 'doctor', 'cie10', 'firma'))->render();
        $pdf  = \App::make('dompdf.wrapper');

        $pdf->loadHTML($view);

        return $pdf->stream('orden_doctor_' . $paciente->id_paciente . '.pdf');
    }

    public function imprimir_012_excel($id)
    {
        //return "ok";
        $orden_012   = Orden_012::find($id);
        $paciente    = Paciente::find($orden_012->id_paciente);
        $id_empresa  = $orden_012->evolucion->historiaclinica->agenda->id_empresa;
        $inf_empresa = Empresa::where('id', $orden_012->id_empresa)
            ->OrderBy('estado', '1')
            ->first();

        $id_seguro = $orden_012->evolucion->procedimiento->id_seguro;
        if ($id_seguro != null) {
            $id_seguro = $orden_012->evolucion->historiaclinica->id_seguro;
        }
        $empresa = Empresa::find($id_empresa);
        $seguro  = Seguro::find($id_seguro);
        $doctor  = User::find($orden_012->id_doctor_firma);
        $cie10   = Orden_012_Cie10::where('id_orden_012', $orden_012->id)->get();

        $age   = Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;
        $firma = Firma_Usuario::where('id_usuario', $orden_012->id_doctor_firma)->first();

        $fecha_d = date('Y/m/d');
        Excel::create($paciente->apellido1 . '_' . $paciente->nombre1 . '_ORDEN_012', function ($excel) use ($orden_012, $inf_empresa, $paciente, $seguro, $empresa, $age, $doctor, $cie10) {

            $excel->sheet('Orden_012', function ($sheet) use ($orden_012, $paciente, $seguro, $inf_empresa, $empresa, $age, $doctor, $cie10) {
                $fecha_d = date('Y/m/d');

                $sheet->mergeCells('B2:H2');
                $sheet->cell('B2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('INSTITUCION DEL SISTEMA');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('I2:P2');
                $sheet->cell('I2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('UNIDAD OPERATIVA');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('Q2:T2');
                $sheet->cell('Q2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('COD. UO');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('U2:AB2');
                $sheet->cell('U2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('COD. LOCALIZACION');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('AC2:AG3');
                $sheet->cell('AC2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NUMERO DE HISTORIA CLINICA');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('B3:H4');
                $sheet->cell('B3', function ($cell) use ($seguro) {
                    // manipulate the cel
                    $cell->setValue('IESS');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('I3:P4');
                $sheet->cell('I3', function ($cell) use ($inf_empresa) {
                    // manipulate the cel
                    $cell->setValue($inf_empresa->nombrecomercial);;
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('Q3:T4');
                $sheet->cell('Q3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('U3:V3');
                $sheet->cell('U3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PARROQUIA');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('U4:V4');
                $sheet->cell('U4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TARQUI');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('W3:Z3');
                $sheet->cell('W3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CANTON');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('W4:Z4');
                $sheet->cell('W4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('GUAYAQUIL');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('AA3:AB3');
                $sheet->cell('AA3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PROVINCIA');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('AA4:AB4');
                $sheet->cell('AA4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('GUAYAS');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('AC4:AG4');
                $sheet->cell('AC4', function ($cell) use ($paciente) {
                    // manipulate the cel
                    $cell->setValue($paciente->id);
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('B5:E5');
                $sheet->cell('B5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('APELLIDO PATERNO');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('B6:E7');
                $sheet->cell('B6', function ($cell) use ($paciente) {
                    // manipulate the cel
                    $cell->setValue($paciente->apellido1);
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('F5:K5');
                $sheet->cell('F5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('APELLIDO MATERNO');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('F6:K7');
                $sheet->cell('F6', function ($cell) use ($paciente) {
                    // manipulate the cel if si el campo es null
                    if ($paciente->apellido2 != '(N/A)') {
                        $cell->setValue($paciente->apellido2);
                    }
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('L5:Q5');
                $sheet->cell('L5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PRIMER NOMBRE');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('L6:Q7');
                $sheet->cell('L6', function ($cell) use ($paciente) {
                    // manipulate the cel
                    $cell->setValue($paciente->nombre1);
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('R5:Z5');
                $sheet->cell('R5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SEGUNDO NOMBRE');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('R6:Z7');
                $sheet->cell('R6', function ($cell) use ($paciente) {
                    // manipulate the cel if si el campo es null
                    if ($paciente->nombre2 != '(N/A)') {
                        $cell->setValue($paciente->nombre2);
                    }
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('AA5:AB5');
                $sheet->cell('AA5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('EDAD');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('AA6:AB7');
                $sheet->cell('AA6', function ($cell) use ($age) {
                    // manipulate the cel
                    $cell->setValue($age);
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('AC5:AG5');
                $sheet->cell('AC5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CEDULA DE CIUDADANIA');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('AC6:AG7');
                $sheet->cell('AC6', function ($cell) use ($paciente) {
                    // manipulate the cel
                    $cell->setValue($paciente->id);
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('B8:E8');
                $sheet->cell('B8', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PERSONA QUE REFIERE');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('B9:E9');
                $sheet->cell('B9', function ($cell) use ($orden_012) {
                    // manipulate the cel
                    $cell->setValue($orden_012->referido);
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('F8:L8');
                $sheet->cell('F8', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PROFESIONAL SOLICITANTE');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('F9:L9');
                $sheet->cell('F9', function ($cell) use ($doctor) {
                    // manipulate the cel
                    $cell->setValue('DR. ' . $doctor->apellido1);
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('M8:Q8');
                $sheet->cell('M8', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SERVICIO');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('M9:Q9');
                $sheet->cell('M9', function ($cell) use ($orden_012) {
                    // manipulate the cel
                    $cell->setValue($orden_012->servicio);
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('R8:S8');
                $sheet->cell('R8', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SALA');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('R9:S9');
                $sheet->cell('R9', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('T8:U8');
                $sheet->cell('T8', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CAMA');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('T9:U9');
                $sheet->cell('T9', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('V8:AD8');
                $sheet->cell('V8', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PRIORIDAD');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('V9:W9');
                $sheet->cell('V9', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('URGENTE');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('X9', function ($cell) use ($orden_012) {
                    // manipulate the cel
                    if ($orden_012->urgente) {
                        $cell->setValue('X');
                    } else {
                        $cell->setValue(' ');
                    }
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('Y9:Z9');
                $sheet->cell('Y9', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('RUTINA');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AA9', function ($cell) use ($orden_012) {
                    // manipulate the cel
                    if ($orden_012->rutina) {
                        $cell->setValue('X');
                    } else {
                        $cell->setValue(' ');
                    }
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('AB9:AC9');
                $sheet->cell('AB9', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CONTROL');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AD9', function ($cell) use ($orden_012) {
                    // manipulate the cel
                    if ($orden_012->control) {
                        $cell->setValue('X');
                    } else {
                        $cell->setValue(' ');
                    }
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('AE8:AG8');
                $sheet->cell('AE8', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA DE TOMA');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('AE9:AG9');
                $sheet->cell('AE9', function ($cell) use ($orden_012) {
                    // manipulate the cel
                    $cell->setValue(substr($orden_012->fecha_orden, 0, 10));
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('B11:AG11');
                $sheet->cell('B11', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('1. ESTUDIO SOLICITADO');
                    $cell->setBackground('#ECEFF0');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('B12:D12');
                $sheet->cell('B12', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('RX CONVENCIONAL');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E12', function ($cell) use ($orden_012) {
                    // manipulate the cel
                    if ($orden_012->rx_convencional) {
                        $cell->setValue('X');
                    } else {
                        $cell->setValue(' ');
                    }
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('F12:I12');
                $sheet->cell('F12', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TOMOGRAFIA');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('J12', function ($cell) use ($orden_012) {
                    // manipulate the cel
                    if ($orden_012->tomografia) {
                        $cell->setValue('X');
                    } else {
                        $cell->setValue(' ');
                    }
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('K12:M12');
                $sheet->cell('K12', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('RESONANCIA');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('N12', function ($cell) use ($orden_012) {
                    // manipulate the cel
                    if ($orden_012->resonancia) {
                        $cell->setValue('X');
                    } else {
                        $cell->setValue(' ');
                    }
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('O12:Q12');
                $sheet->cell('O12', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ECOGRAFIA');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('R12', function ($cell) use ($orden_012) {
                    // manipulate the cel
                    if ($orden_012->ecografia) {
                        $cell->setValue('X');
                    } else {
                        $cell->setValue(' ');
                    }
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('S12:U12');
                $sheet->cell('S12', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PROCEDIMIENTO');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('V12', function ($cell) use ($orden_012) {
                    // manipulate the cel
                    if ($orden_012->procedimiento) {
                        $cell->setValue('X');
                    } else {
                        $cell->setValue(' ');
                    }
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('W12:X12');
                $sheet->cell('W12', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('OTROS');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('Y12', function ($cell) use ($orden_012) {
                    // manipulate the cel
                    if ($orden_012->otros) {
                        $cell->setValue('X');
                    } else {
                        $cell->setValue(' ');
                    }
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('Z12:AG12');
                $sheet->cell('Z12', function ($cell) use ($orden_012) {
                    // manipulate the cel
                    $cell->setValue($orden_012->texto_otros);
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('B13:D14');
                $sheet->cell('B13', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DESCRIPCION');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E13', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('E14', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('F13:AG13');
                $sheet->cell('F13', function ($cell) use ($orden_012) {
                    // manipulate the cel
                    $cell->setValue($orden_012->descripcion);
                    $cell->setAlignment('left');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('F14:AG14');
                $sheet->cell('F14', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('left');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('B15:AG15');
                $sheet->mergeCells('B16:I16');
                $sheet->cell('B16', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PUEDE MOVILIZARSE');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('J16', function ($cell) use ($orden_012) {
                    // manipulate the cel
                    if ($orden_012->puede_mover) {
                        $cell->setValue('X');
                    } else {
                        $cell->setValue(' ');
                    }
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('K16:Q16');
                $sheet->cell('K16', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PUEDE RETIRARSE VENDAS APOSITOS O YESOS');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('R16', function ($cell) use ($orden_012) {
                    // manipulate the cel
                    if ($orden_012->puede_retirar) {
                        $cell->setValue('X');
                    } else {
                        $cell->setValue(' ');
                    }
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('S16:X16');
                $sheet->cell('S16', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('EL MEDICO ESTARA PRESENTE EN ELE EXAMEN');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('Y16', function ($cell) use ($orden_012) {
                    // manipulate the cel
                    if ($orden_012->medico_presente) {
                        $cell->setValue('X');
                    } else {
                        $cell->setValue(' ');
                    }
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('Z16:AF16');
                $sheet->cell('Z16', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TOMA DE RADIOGRAFIA EN LA CAMA');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AG16', function ($cell) use ($orden_012) {
                    // manipulate the cel
                    if ($orden_012->toma_radio) {
                        $cell->setValue('X');
                    } else {
                        $cell->setValue(' ');
                    }
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('B18:AG18');
                $sheet->cell('B18', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('2. MOTIVO DE LA SOLICITUD.');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('B19:AG20');
                $sheet->cell('B19', function ($cell) use ($orden_012) {
                    // manipulate the cel
                    $cell->setValue($orden_012->motivo);
                    $cell->setAlignment('left');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('B21:R21');
                $sheet->cell('B21', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('3. RESUMEN CLINICO');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('left');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('B22:Q28');
                $sheet->cell('B22', function ($cell) use ($orden_012) {
                    // manipulate the cel
                    $cell->setValue(html_entity_decode(str_replace("&nbsp;", " ", strip_tags($orden_012->cuadro_clinico))));
                    $cell->setAlignment('left');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('S21:AG21');
                $sheet->cell('S21', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('4. DIAGNOSTICOS');
                    $cell->setBackground('#ECEFF0');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('S22:AD22');
                $sheet->cell('S22', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CIE= CLASIFICACION INTERNACIONAL DE ENFERMEDADES  PRE= RESUNTIVO  DEF= DEFINITIVO');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('AE22', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CIE');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('AF22', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PRE');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AG22', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DEF');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $c10_cont = 1;
                $xcell = 23;
                foreach ($cie10 as $val) {

                    if ($c10_cont <= 5) {
                        $c10 = Cie_10_3::find($val->cie10);
                        if (is_null($c10)) {
                            $c10 = Cie_10_4::find($val->cie10);
                        }

                        $sheet->cell('S' . $xcell, function ($cell) use ($c10_cont) {
                            // manipulate the cel
                            $cell->setValue($c10_cont);
                            $cell->setBackground('#ECEFF0');
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->mergeCells('T' . $xcell . ':AD' . $xcell);
                        $sheet->cell('T' . $xcell, function ($cell) use ($c10) {
                            // manipulate the cel
                            $cell->setValue($c10->descripcion);
                            $cell->setAlignment('left');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('AE' . $xcell, function ($cell) use ($val) {
                            // manipulate the cel
                            $cell->setValue($val->cie10);
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('AF' . $xcell, function ($cell) use ($val) {
                            // manipulate the cel
                            if ($val->presuntivo_definitivo == 'PRESUNTIVO') {
                                $cell->setValue('X');
                            }
                            $cell->setAlignment('center');
                            $cell->setBackground('#ECEFF0');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('AG' . $xcell, function ($cell) use ($val) {
                            // manipulate the cel @if($val->presuntivo_definitivo=='DEFINITIVO') X @endif
                            if ($val->presuntivo_definitivo == 'DEFINITIVO') {
                                $cell->setValue('X');
                            }
                            $cell->setAlignment('center');
                            $cell->setBackground('#ECEFF0');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $xcell++;
                        $c10_cont++;
                    }
                }

                if ($c10_cont < 5) {
                    for ($i = $c10_cont; $i <= 5; $i++) {
                        $sheet->cell('S' . $xcell, function ($cell) use ($c10_cont, $i) {
                            // manipulate the cel
                            $cell->setValue($i);
                            $cell->setBackground('#ECEFF0');
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->mergeCells('T' . $xcell . ':AD' . $xcell);
                        $sheet->cell('T' . $xcell, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('');
                            $cell->setAlignment('left');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('AE' . $xcell, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('');
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('AF' . $xcell, function ($cell) {
                            // manipulate the cel

                            $cell->setValue('');

                            $cell->setAlignment('center');
                            $cell->setBackground('#ECEFF0');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('AG' . $xcell, function ($cell) {
                            // manipulate the cel @if($val->presuntivo_definitivo=='DEFINITIVO') X @endif

                            $cell->setValue('');

                            $cell->setAlignment('center');
                            $cell->setBackground('#ECEFF0');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $xcell++;
                        $c10_cont++;
                    }
                }

                $sheet->cell('B29', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('C29:E29');
                $sheet->cell('C29', function ($cell) use ($orden_012) {
                    // manipulate the cel
                    $cell->setValue(substr($orden_012->fecha_orden, 0, 10));
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F29', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('HORA');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('G29', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('H29:I29');
                $sheet->cell('H29', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NOMBRE DEL PROFESIONAL');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('J29:S29');
                $sheet->cell('J29', function ($cell) use ($doctor) {
                    // manipulate the cel
                    $cell->setValue('Dr(a). ' . $doctor->apellido1 . ' ' . $doctor->nombre1);
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                //CODIGO
                $sheet->mergeCells('T28:V28');
                $sheet->cell('T28', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CODIGO');
                    $cell->setAlignment('center');
                });
                $sheet->mergeCells('T29:V29');
                $sheet->cell('T29', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('16203');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('W29:X29');
                $sheet->cell('W29', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FIRMA');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('Y29:AD29');
                $sheet->cell('Y29', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('AE29:AF29');
                $sheet->cell('AE29', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NUMERO HOJA');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AG29', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('B30:G30');
                $sheet->cell('B30', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SNS-MSP / HCU-form.012a/2008');
                });
                $sheet->mergeCells('Z30:AG30');
                $sheet->cell('Z30', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('IMAGENOLOGIA - SOLICITUD');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('right');
                });
            });
            $excel->getActiveSheet()->getStyle('B2:AG30')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle("B2:AG9")->getFont()->setSize(10);
            $excel->getActiveSheet()->getStyle("B11:AG29")->getFont()->setSize(8);
            $excel->getActiveSheet()->getStyle("B3:P4")->getFont()->setSize(12);
            $excel->getActiveSheet()->getStyle("L4:AB4")->getFont()->setSize(8);
            $excel->getActiveSheet()->getStyle("AC2:AG3")->getFont()->setSize(8);
            $excel->getActiveSheet()->getStyle("AC4:AG4")->getFont()->setSize(14);
            $excel->getActiveSheet()->getStyle("B6:AG7")->getFont()->setSize(14);
            $excel->getActiveSheet()->getStyle("F9:Q9")->getFont()->setSize(14);
            $excel->getActiveSheet()->getStyle("U9:V9")->getFont()->setSize(8);
            $excel->getActiveSheet()->getStyle("Y9:Z9")->getFont()->setSize(8);
            $excel->getActiveSheet()->getStyle("AB9:AC9")->getFont()->setSize(8);
            $excel->getActiveSheet()->getStyle("B11:AG11")->getFont()->setSize(14);
            $excel->getActiveSheet()->getStyle("F13:AG13")->getFont()->setSize(16);
            $excel->getActiveSheet()->getStyle("B18:AG18")->getFont()->setSize(14);
            $excel->getActiveSheet()->getStyle("B19:AG20")->getFont()->setSize(12);
            $excel->getActiveSheet()->getStyle("B21:AG21")->getFont()->setSize(14);
            $excel->getActiveSheet()->getStyle("B22:Q28")->getFont()->setSize(11);
            $excel->getActiveSheet()->getStyle("S23:AG27")->getFont()->setSize(12);
            $excel->getActiveSheet()->getStyle("J29:V29")->getFont()->setSize(12);
            $excel->getActiveSheet()->getStyle("B30:G30")->getFont()->setSize(10);
            $excel->getActiveSheet()->getStyle("AA30:AG30")->getFont()->setSize(14);
            $excel->getActiveSheet()->getStyle("B22:Q28")->getFont()->setSize(9);
            $excel->getActiveSheet()->getStyle("H29:I29")->getFont()->setSize(5);
            $excel->getActiveSheet()->getStyle("B22:Q28")->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getColumnDimension("A")->setWidth(5)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("B")->setWidth(5)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("C")->setWidth(5)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("D")->setWidth(5)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("E")->setWidth(5)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("F")->setWidth(5)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("G")->setWidth(5)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("H")->setWidth(7)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("I")->setWidth(7)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("J")->setWidth(5)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("K")->setWidth(5)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("L")->setWidth(5)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("M")->setWidth(5)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("N")->setWidth(5)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("O")->setWidth(5)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("P")->setWidth(5)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("Q")->setWidth(5)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("R")->setWidth(5)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("S")->setWidth(5)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("T")->setWidth(5)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("U")->setWidth(5)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("V")->setWidth(5)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("W")->setWidth(5)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("X")->setWidth(5)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("Y")->setWidth(5)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("Z")->setWidth(5)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AA")->setWidth(5)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AB")->setWidth(5)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AC")->setWidth(5)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AD")->setWidth(5)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AE")->setWidth(5)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AF")->setWidth(5)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AG")->setWidth(5)->setAutosize(false);
            /*
        //imagen
        $objDrawing = new PHPExcel_Worksheet_Drawing();    //create object for Worksheet drawing
        $objDrawing->setName('Customer Signature');        //set name to image
        $objDrawing->setDescription('Customer Signature'); //set description to image
        //Path to signature .jpg file
        $objDrawing->setPath(base_path().'\public\images\bg.jpg');
        $objDrawing->setOffsetX(25);                       //setOffsetX works properly
        $objDrawing->setOffsetY(10);                       //setOffsetY works properly
        $objDrawing->setCoordinates('A1');        //set image to cell
        $objDrawing->setWidth(32);                 //set width, height
        $objDrawing->setHeight(32);
        $objDrawing->setWorksheet($excel->getActiveSheet());  //save
         */
        })->export('xlsx');
    }

    //HISTORIAL ORDENES DE LABORATORIO
    public function historial_ordenes_biopsias($id_paciente)
    {

        //dd($id_paciente);
        $biopsias = Hc4_Biopsias::where('id_paciente', $id_paciente)->groupBy("hc_id_procedimiento")->get();

        $paciente = Paciente::find($id_paciente);

        /*if($biopsias->count()>0){

        foreach ($biopsias as $biop){

        $orden_biopsia = Biopsias::where('hc_id_procedimiento',$biop->hc_id_procedimiento)->get();
        //dd($orden_biopsia);

        }
        }*/

        return view('hc_admision/orden_proc/historial_ordenes_biopsias', ['biopsias' => $biopsias, 'paciente' => $paciente]);
    }

    //Imprimir Orden de Biopsias Recepcion
    public function imprime_orden_biopsia($id, $id_hcid, $id_doct, Request $request)
    {
      
        $hc_proced = hc_procedimientos::find($id);
        $id_empresa  = $request->session()->get('id_empresa');
        $logo_emp = Empresa::find($id_empresa);
        $count_biospsias = Hc4_Biopsias::where('hc_id_procedimiento', $id)->get()->count();
 
        if (($count_biospsias > 0) && (!is_null($hc_proced))) {

            //Aqui va consulta
            $pro   = Hc_Procedimiento_Final::where('id_hc_procedimientos', $id)->get();
            $mas   = true;
            $texto = "";

            foreach ($pro as $value) {
                if ($mas == true) {
                    $texto = $texto . $value->procedimiento->nombre;
                    $mas   = false;
                } else {
                    $texto = $texto . ' + ' . $value->procedimiento->nombre;
                }
            }

            $lista_proced = $texto;

            $historia = historiaclinica::find($id_hcid);
            $paciente = paciente::find($historia->id_paciente);

            if ($paciente->fecha_nacimiento != null) {
                $edad = Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;
            }

            $biop_first = Hc4_Biopsias::where('hc_id_procedimiento', $id)->first();

            $impr_biospsias = Hc4_Biopsias::where('hc_id_procedimiento', $id)->get();

            $doctor_solicitante = DB::table('users as us')
                ->where('us.id', $id_doct)
                ->first();

            if(!is_null($id_doct)){
                $firma = Firma_Usuario::where('id_usuario',$id_doct)->first();
            }else{
                $firma = null;
            }

            $vistaurl = "hc4.procedimiento_endoscopico.pdf_orden_biopsias";
            $view     = \View::make($vistaurl, compact('historia', 'paciente', 'edad', 'impr_biospsias', 'doctor_solicitante', 'biop_first', 'hc_proced', 'lista_proced', 'logo_emp', 'firma'))->render();

            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view);
            $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
            $pdf->setPaper('a4', 'landscape');

            return $pdf->stream($historia->id_paciente . '_Biopsias_' . $id . '.pdf');
        }
    }


    public function excel_053_nuevo($id, Request $request)
    {
        //$titulos= array();
        $orden_proc = Orden::find($id);
        // dd($orden_proc);

        //dd($orden_proc);
        $id_empresa = $request->session()->get('id_empresa');
        $empresa        = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
      //  dd($empresa);

        if ((is_null($orden_proc->check_doctor)) && (is_null($orden_proc->id_doctor_firma))) {
            $doctor_firma = $orden_proc->id_doctor;
        } else {
            $doctor_firma = $orden_proc->id_doctor_firma;
        }
        if (!is_null($orden_proc)) {
            $firma = Firma_Usuario::where('id_usuario', $doctor_firma)->first();
        }
        $paciente = Paciente::find($orden_proc->id_paciente);
        if ($paciente->fecha_nacimiento != null) {
            $edad = Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;
        }
        //dd($paciente);

        $doctor_solicitante = DB::table('users as us')
            ->where('us.id', $orden_proc->id_doctor)
            ->first();

        //Posiciones en el excel
        $mer_datos = array(
            "B9:D10", "E9:G10", "H9:J10", "K9:K9", "K10:K10", "L9:L9", "L10:L10",
            "M9:N9", "M10:N10", "O9:O9", "O10:O10", "P9:P9", "P10:P10", "E13:H13",
            "B16:C16"
        );
        $sexo = "";
        if ($paciente->sexo == 1) {
            $sexo = "H";
        } else if ($paciente->sexo == 2) {
            $sexo = "M";
        } else {
            $sexo = "O";
        }
        $fecha_nac = array();
        $fecha_nac = explode("-", $paciente->fecha_nacimiento);
        $datos = array(
            "B9:D10" => [
                'merge' => "B9:D10", "dato" => $paciente->apellido1, "position" => "B9"
            ],
            "E9:G10" => [
                "merge" => "E9:G10", "dato" => $paciente->apellido2, "position" => "E9"
            ],
            "H9:J10" => [
                "merge" => "H9:J10", "dato" => $paciente->nombre1, "position" => "H9"
            ],
            "K9:K9" => [
                "merge" => "K9:K9", "dato" => $fecha_nac[2], "position" => "K9"
            ],
            "L9:L9" => [
                "merge" => "L9:L9", "dato" => $fecha_nac[1], "position" => "L9"
            ],
            "M9:N9" => [
                "merge" => "M9:N9", "dato" => $fecha_nac[0], "position" => "M9"
            ],
            "O9:O9" => [
                "merge" => "O9:O9", "dato" => $edad, "position" => "O9"
            ],
            "P9:P9" => [
                "merge" => "P9:P9", "dato" => $sexo, "position" => "P9"
            ],
            "B13:B13" => [
                'merge' => "B13:B13", "dato" => $paciente->id_pais, "position" => "B13"
            ],
            "C13:C13" => [
                'merge' => "C13:C13", "dato" => $paciente->lugar_nacimiento, "position" => "C13"
            ],
            "D13:E13" => [
                "merge" => "D13:E13", "dato" => $paciente->id, "position" => "D13"
            ],
            "F13:I13" => [
                "merge" => "F13:I13", "dato" => $paciente->ciudad, "position" => "F13"
            ],
            "J13:M13" => [
                "merge" => "J13:M13", "dato" => $paciente->direccion, "position" => "J13"
            ],
            "N13:P13" => [
                "merge" => "N13:P13", "dato" => $paciente->telefono2, "position" => "N13"
            ], 
            "B19:C19" =>["merge"=>"B19:C19","dato"=>"IESS","position" =>"B19"],

            
            "D19:E19" => [ "merge" => "D19:E19", "dato" => $orden_proc->id, "position" => "D19"],

            "F19:J19" => [
                "merge" => "F19:J19", "dato" => $empresa->nombrecomercial, "position" => "F19"
            ],
           // "K19" => [  "dato" => "1", "position" => "K19"],

            "L19:P19" => [ "merge" => "L19:P19", "dato" => $empresa->ciudad, "position" => "L19"],

           // "B21:P21" => [ "merge" => "B21:P21", "dato" => $empresa->ciudad, "position" => "L21"],


            "B28:P28" => [
                "merge" => "B28:P28", "dato" => strip_tags($orden_proc->resumen_clinico), "position" => "B28"
            ],
            "B30:P30" => [
                "merge" => "B30:P30", "dato" => "$paciente->observacion_recepcion", "position" => "B30"
            ],

            "C32:L32" => [
                "merge" => "C32:L32", "dato" => "", "position" => "C32"
            ],
            "M32:N32" => [
                "merge" => "M32:N32", "dato" => "", "position" => "M32"
            ],
            "O32:O32" => [
                "merge" => "O32:O32", "dato" => "", "position" => "O32"
            ],
            "P32:P32" => [
                "merge" => "P32:P32", "dato" => "", "position" => "P32"
            ],


            "C33:L33" => [
                "merge" => "C33:L33", "dato" => "", "position" => "C33"
            ],
            "M33:N33" => [
                "merge" => "M33:N33", "dato" => "", "position" => "M33"
            ],
            "O33:O33" => [
                "merge" => "O33:O33", "dato" => "", "position" => "O33"
            ],
            "P33:P33" => [
                "merge" => "P33:P33", "dato" => "", "position" => "P33"
            ],



            "C37:L37" => [
                "merge" => "C37:L37", "dato" => "", "position" => "C37"
            ],
            "M37:P37" => [
                "merge" => "M37:P37", "dato" => "", "position" => "M37"
            ],
            "C38:L38" => [
                "merge" => "C38:L38", "dato" => "", "position" => "C38"
            ],
            "M38:P38" => [
                "merge" => "M38:P38", "dato" => "", "position" => "M38"
            ],
            "C39:L39" => [
                "merge" => "C39:L39", "dato" => "", "position" => "C39"
            ],
            "M39:P39" => [
                "merge" => "M39:P39", "dato" => "", "position" => "M39"
            ],
            "B48:D48" => [
                "merge" => "B48:D48", "dato" => "", "position" => "B48"
            ],
            "E48:G48" => [
                "merge" => "E48:G48", "dato" => "", "position" => "E48"
            ],
            "H48:J48" => [
                "merge" => "H48:J48", "dato" => "", "position" => "H48"
            ],
            "K48:L48" => [
                "merge" => "K48:L48", "dato" => "", "position" => "K48"
            ],
            "M48:N48" => [
                "merge" => "M48:N48", "dato" => "", "position" => "M48"
            ],
            "O48:O48" => [
                "merge" => "O48:O48", "dato" => "", "position" => "O48"
            ],
            "P48:P48" => [
                "merge" => "P48:P48", "dato" => "", "position" => "P48"
            ],
            "B51:P51" => [
                "merge" => "B51:P51", "dato" => "", "position" => "B51"
            ],
            "B52:P52" => [
                "merge" => "B52:P52", "dato" => "", "position" => "B52"
            ],
            "B55:P55" => [
                "merge" => "B55:P55", "dato" => "", "position" => "B55"
            ],
            "B56:P56" => [
                "merge" => "B56:P56", "dato" => "", "position" => "B56"
            ],
            "B58:P58" => [
                "merge" => "B58:P58", "dato" => "", "position" => "B58"
            ],
            "B59:P59" => [
                "merge" => "B59:P59", "dato" => "", "position" => "B59"
            ],
            "B62:L62" => [
                "merge" => "B62:L62", "dato" => "", "position" => "B62"
            ],
            "O62:O62" => [
                "merge" => "O62:O62", "dato" => "", "position" => "O62"
            ],
            "P62:P62" => [
                "merge" => "P62:P62", "dato" => "", "position" => "P62"
            ],
            "M62:N62" => [
                "merge" => "M62:N62", "dato" => "", "position" => "M62"
            ],
            "B63:L63" => [
                "merge" => "B63:L63", "dato" => "", "position" => "B63"
            ],
            "M63:N63" => [
                "merge" => "M63:N63", "dato" => "", "position" => "M63"
            ],
            "O63:O63" => [
                "merge" => "O63:O63", "dato" => "", "position" => "O63"
            ],
            "P63:P63" => [
                "merge" => "P63:P63", "dato" => "", "position" => "P63"
            ],
            "B66:P66" => [
                "merge" => "B66:P66", "dato" => "", "position" => "B66"
            ],
            "B67:P67" =>[
                "merge" => "B67:P67", "dato" => "", "position" => "B67"
            ],
            "B68:P68" => [
                "merge" => "B68:P68", "dato" => "", "position" => "B68"
            ],
            "I73:I74" => [
                "merge" => "I73:I74", "dato" => "", "position" => "I73"
            ],
        );

        $posicion = array(
            
            "B5:P5", "B6:P6",
            "B7:P7",
            "B8:D8", "E8:G8", "H8:J8", "K8:N8", "O8:O8", "P8:P8",
            "K10:K10", "L10:L10", "M10:N10", "O10:O10", "P10:P10",
            "B11:B12", "C11:C12", "D11:E12", "F11:I12", "J11:M12", "N11:P12",
            "B14:B14", "C14:C14", "D14:E14", "F14:F14", "G14:G14", "H14:I14", "J14:M14", "N14:P14",
            "B16:C16", "E16:E16",
            "B17:P17",
            "B18:C18", "D18:E18", "F18:J18", "K18:K18", "L18:P18",
           // "B19:C19","D19:E19","L19:P19"
            "B20:L20", "M20:P20",
           // "B21:D21", "E21:G21", "H21:J21", "K21:L21", "M21:N21", "O21:O21", "P21:P21",
            "B22:D22","E22:G22", "H22:J22", "K22:L22", "M22:N22", "O22:O22", "P22:P22",
            "B23:P23",
            "B24:C24", "E24:H24",
            "B25:C25", "E25:H25",
            "B26:C26",/*"E26:P26",*/
            "B27:P27",
            "B29:P29",
            "B31:L31", "M31:N31", "O31:O31", "P31:P31",
            "B32:B32",/*"M32:N32","O32:O32","P32:P32",*/
            "B33:B33",/*"M33:N33","O33:O33","P33:P33",*/
            "B35:L35", "M35:P35",
            "B37:B37",/*"M37:P37",*/
            "B38:B38",/*"M38:P38",*/
            "B39:B39",/*"M39:P39",*/
            "B41:C41",/*"D41:F41",*/ "G41:G41",/*"H41:K41",*/ "L41:L41",/*"M41:P41",*/
            "B43:C43", "E43:H43",/*"I43:P43",*/
            "B44:P44",
            "B45:C45", "D45:E45", "F45:I45", "J45:J45", "K45:K45", "L45:P45",
            "B47:P47",
            "B49:D49", "E49:G49", "H49:J49", "K49:L49", "M49:N49", "O49:O49", "P49:P49",
            "B50:P50",
            "B54:P54",
            "B57:P57",
            "B61:L61", "M61:N61", "O61:O61", "P61:P61",
            "B65:P65",
            "B70:D70", "I70:I70", "L70:L70",
            "B71:D71",
            "H73:H74"
        ); //Posicion de los recuadros

       

        $titulos = array(
            "MINISTERIO DE SALUD PUBLICA", "FORMULARIO DE REFERENCIA, DERIVACIÓN, CONTRAREFERENCIA Y REFERENCIA INVERSA",
            "I. DATOS DEL USUARIO/USUARIA",
            "Apellido Paterno", "Apellido Materno", "Nombres", "Fecha de Nacimiento", "Edad", "Sexo",
            "dia", "mes", "año", "d-m-a", "hombre",
            "Nacionalidad", "País", "Cédula de pasaporte", "Lugar de residencia Actual", "Dirección Domicilio", "N° Telefonico",
            "Ver instructivo", "Describir pais", "Cédula diez digitos", "Provincia", "Cantón", "Parroquia", "Calle Principal y Secundaria", "Convencional/Celular",
            "II. Referencia: 1", "DERIVACIÓN: ",
            "1. Datos Institucionales",
            "Entidad del Sistema", "Hist. Clínica", "Establecimiento de Salud", "Tipo", "Distrito/Área",
            "Refiere o Deriva a:", "Fecha",
            "IEES", "ESTABLECIMIENTO DE SALUD", "SERVICIO", "ESPECIALIDAD", "dia", "mes", "año",
            "2.Motivo de la Referencia o Derivación",
            "Limitada  capacidad  resolutiva 1", "Saturación de capacidad  instalada",
            "Ausencia temporal del profesional    2 ", "Otros /Especifique:",
            "Falta de profesional    3",
            "3. Resumen del cuadro clínico",
            "4. Hallazgos relevantes de exámenes y procedimientos diagnósticos",
            "5. Diagnostico", "CIE-10", "PRE ", "DEF",
            "1",
            "2.",
            "6. Exámenes/ procedimientos requeridos", "Código Tarifario",
            "1",
            "2",
            "3",
            "Nombre  del profesional:", "Código MSP: ", "Firma", "III. CONTRAREFERENCIA:          3", "REFERENCIA INVERSA:          4",
            "1. Datos Institucionales",
            "Entidad  del sistema", "Hist. Clínica No.", "Establecimiento de Salud", "Tipo", "Servicio", "Especialidad de Servicio",
            "Contrarefiere o  Referencia inversa  a:                                               Fecha                                                                                                                                                                                             ",
            "Entidad del sistema", "Establecimiento de Salud", "Tipo", "Distrito/Área", "día", "mes", "año",
            "2. Resumen del cuadro clínico",
            "3. Hallazgos relevantes de exámenes y procedimientos diagnósticos",
            "4. Tratamientos y procedimientos terapéuticos realizados",
            "5. Diagnóstico", "CIE-10", "PRE", "DEF",
            "6. Tratamiento recomendado a seguir en el establecimiento de salud de menor nivel de atención y/o de complejidad",
            "Nombre  del profesional especialista:", "Código MSP:", "Firma:",
            "MSP/DNISCG/form. 053/dic/2013",
            "7. Referencia Justificada"
        );
        $estilos2= array(
            "A1:p1", "A2:p2","A3:p3","A5:p5","A6:p6","A7:p7",
            "A15:p15","A16:C16","E16:F16","H16:P16","A17:P17"
        );
        $estilos = array(
           // "B7"=>["bordes"=>"", "alineado" => "left", "position"=>"B17", "weight" => "bold","color "=> array('rgb' => 'FF0000')],
            "B17"=>["bordes"=>"", "alineado" => "left", "position"=>"B17", "weight" => "bold"],
            "B20"=>["bordes"=>"", "alineado" => "center", "position"=>"B20", "weight" => "bold"],
            "B24"=>["bordes"=>"", "alineado" => "left", "position"=>"B24", "weight" => "bold"],
            "E24"=>["bordes"=>"", "alineado" => "left", "position"=>"E24", "weight" => "bold"],
            "B25"=>["bordes"=>"", "alineado" => "left", "position"=>"B25", "weight" => "bold"],
            "B26"=>["bordes"=>"", "alineado" => "left", "position"=>"B26", "weight" => "bold"],
            "B27"=>["bordes"=>"", "alineado" => "left", "position"=>"B27", "weight" => "bold"],
            "B29"=>["bordes"=>"", "alineado" => "left", "position"=>"B29", "weight" => "bold"],
            "B31"=>["bordes"=>"", "alineado" => "left", "position"=>"B31", "weight" => "bold"],
            "B32"=>["bordes"=>"", "alineado" => "left", "position"=>"B32", "weight" => "bold"],
            "B33"=>["bordes"=>"", "alineado" => "left", "position"=>"B33", "weight" => "bold"],
            "B35"=>["bordes"=>"", "alineado" => "left", "position"=>"B35", "weight" => "bold"],
            "B37"=>["bordes"=>"", "alineado" => "left", "position"=>"B37", "weight" => "bold"],
            "B38"=>["bordes"=>"", "alineado" => "left", "position"=>"B38", "weight" => "bold"],
            "B39"=>["bordes"=>"", "alineado" => "left", "position"=>"B39", "weight" => "bold"],
            "B43"=>["bordes"=>"", "alineado" => "left", "position"=>"B43", "weight" => "bold"],
            "B44"=>["bordes"=>"", "alineado" => "left", "position"=>"B44", "weight" => "bold"],
            "B50"=>["bordes"=>"", "alineado" => "left", "position"=>"B50", "weight" => "bold"],
            "B54"=>["bordes"=>"", "alineado" => "left", "position"=>"B54", "weight" => "bold"],
            "B57"=>["bordes"=>"", "alineado" => "left", "position"=>"B57", "weight" => "bold"],
            "B61"=>["bordes"=>"", "alineado" => "left", "position"=>"B61", "weight" => "bold"],
            "B65"=>["bordes"=>"", "alineado" => "left", "position"=>"B65", "weight" => "bold"],
        );
      
        $posicion2=array(
            "A1:p1", "A2:p2","A3:p3","A4:P4","A5:P5","A6:p6","A7:p7",
                    "A15:p15","A16:C16","E16:F16","H16:P16","A17:P17","A23:P23","A24:P24"
                    ,"A25:P25","A26:P26","A27:P27"
        );
        


        Excel::create('Orden 053', function ($excel) use ($titulos, $posicion, $estilos2, $posicion2, $orden_proc, $mer_datos, $datos, $empresa, $estilos) {
            $excel->sheet('Formulario Referencia', function ($sheet) use ($titulos, $posicion2,  $posicion, $orden_proc, $mer_datos, $datos, $empresa, $estilos) {

                $comienzo = 11; //EN QUE FILA ESTARN LOS TITULOS DEL EXCEL 
                /****************TITULOS DEL EXCEL*********************/
                $sheet->getSheetView()->setZoomScale(80);

                for ($i = 0; $i < count($posicion); $i++) {
                    $sheet->mergeCells($posicion[$i]);
                }


                for ($i = 0; $i < count($posicion); $i++) {
                    $posicion_titulo = [];
                    $posicion_titulo = explode(":", $posicion[$i]);

                    $sheet->cell("" . $posicion_titulo[0], function ($cell) use ($titulos, $i, $posicion_titulo) {
                      $cell->setValue($titulos[$i]);
                      $cell->setFontWeight('bold');

                                if($i > 2) {
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    }
                        $cell->setValignment('center');
                        $cell->setAlignment('center');
                        $cell->setFontSize(12);
                    });
                }

                foreach ($estilos as $e){
                    $sheet->cell("" . $e["position"], function ($cell) use ($i, $e) {
                        //$cell->setBorder($e["bordes"]);
                        $cell->setFontWeight($e["weight"]);
                        $cell->setAlignment($e["alineado"]);
                        $cell->setFontSize(12);
                    });
                }


                if ($empresa->logo != null) {
                    $sheet->mergeCells('C1:D1');
                    $objDrawing = new PHPExcel_Worksheet_Drawing;
                    $objDrawing->setPath(base_path() . '/storage/app/logo/image001.png');
                    $objDrawing->setCoordinates('C1');
                    $objDrawing->setHeight(300);
                    $objDrawing->setWidth(100);
                    $objDrawing->setWorksheet($sheet);
                }
                if ($empresa->logo != null) {
                    $sheet->mergeCells('L1:O1');
                
                    $objDrawing = new PHPExcel_Worksheet_Drawing;
                    $objDrawing->setPath(base_path() . '/storage/app/logo/image003.png');
                    $objDrawing->setCoordinates('L1');
                    $objDrawing->setHeight(100);
                    $objDrawing->setWidth(100);
                    $objDrawing->setWorksheet($sheet);
                    
                }
           
  
                


                $comienzo++;
                /*****FIN DE TITULOS DEL EXCEL***********/
                //DATOS PARA EL EXCEL
                
                foreach ($datos as $values) {
                    //dd($values);
                    $sheet->mergeCells("" . $values["merge"]);
                    $sheet->cell('' . $values["position"], function ($cell) use ($values, $i) {
                        $cell->setValue($values["dato"]);
                        $cell->setValignment('center');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                }

            for ($i2 = 0; $i2 < count($posicion2); $i2++) {
                $sheet->getStyle($posicion2[$i2])->applyFromArray(
                    array(
                        'fill' => array(
                            'type' => PHPExcel_Style_Fill::FILL_SOLID,
                            'color' => array('rgb' => 'FFFF')
                            )
                    )
                );

            }



                $comienzo++;
            });



            $abc = array("B", "C","d", "E", "F", "G", "H", "I", "J","K","L","M","N","O","P");
            $excel->getActiveSheet()->getColumnDimension('A')->setWidth(5)->setAutosize(false);

            for ($i = 0; $i < count($abc); $i++) {
                $excel->getActiveSheet()->getColumnDimension($abc[$i])->setWidth(15)->setAutosize(false);
            }

            $numero = array(
                "28" => [
                    '28' => 100
                ],
                "30" => [
                    '30' => 50
                ],
                "37" => [
                    '37' => 30
                ],
                "38" => [
                    '38' => 30
                ],
                "39" => [
                    '39' => 30
                ],
            );

            foreach ($numero as  $num) {
                foreach ($num as $key => $value) {
                    $excel->getActiveSheet()->getRowDimension($key)->setRowHeight($value);
                }
            }
        })->export('xlsx');
    }
    public function orden_biopsia(Request $request)
    {

        $constraints = [
            'id'            => $request['id'],
            'apellidos'     => $request['apellidos'],
            'nombres'       => $request['nombres'],
            'fecha_desde'   => $request['fecha_desde'],
            'fecha_hasta'   => $request['fecha_hasta']
        ];

        $fecha_desde = date('Y-m-d');
        if (!is_null($request['fecha_desde'])) {
            $fecha_desde = date($request['fecha_desde']);
        }
        $fecha_hasta = date('Y-m-d');
        if (!is_null($request['fecha_hasta'])) {
            $fecha_hasta = date($request['fecha_hasta']);
        }

        $ordenes  = Hc4_Biopsias::join("paciente","hc4_biopsias.id_paciente","paciente.id")->where('hc4_biopsias.created_at', '>', $fecha_desde . ' 00:00:00')
            ->where('hc4_biopsias.created_at', '<', $fecha_hasta . ' 23:59:59')
            ->groupBy('hc4_biopsias.hc_id_procedimiento');
        if (!is_null($request['id'])) {
            $ordenes = $ordenes->where('hc4_biopsias.id_paciente', $request['id']);
            /*
            ->whereHas('seguro', function ($query) {
                $query->where('tipo', '<>', 0);
            })*/
        }
	
	if (!empty($request['nombres'])) {
            $nombres = trim($request['nombres']);
            $ordenes = $ordenes->whereRaw("CONCAT(paciente.nombre1, ' ', COALESCE(paciente.nombre2, '')) LIKE ?", ["%$nombres%"]);
        }
        
        if (!empty($request['apellidos'])) {
            $apellidos = trim($request['apellidos']);
            $ordenes = $ordenes->whereRaw("CONCAT(paciente.apellido1, ' ', COALESCE(paciente.apellido2, '')) LIKE ?", ["%$apellidos%"]);
        }
        $ordenes = $ordenes->get();
        return view('hc_admision/orden/biopsia', ['ordenes' => $ordenes, 'searchingVals' => $constraints]);
    }

    public function modal_biopsia($id_orden){

        $biopsias = Hc4_Biopsias::where('hc_id_procedimiento', $id_orden)->get();
        $orden =  Hc4_Biopsias::where('hc_id_procedimiento', $id_orden)->first();
        if($biopsias == "[]"){
            return abort(403);
        }

        return view('hc_admision/orden/modal_biopsia', ['biopsias' => $biopsias, 'orden' => $orden]);
    }
}
