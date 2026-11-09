<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use Sis_medico\Agenda;
use Sis_medico\Hospital;
use Sis_medico\Sala;

class ClinicalHistoryDetailService
{
    public function show($id)
    {

        $usuarios   = DB::table('users')->where('id_tipo_usuario', '=', 3)->get(); //3=DOCTORES;
        $enfermeros = DB::table('users')->where('id_tipo_usuario', '=', 6)->get(); //6=ENFERMEROS;
        $salas      = DB::table('sala')
            ->join('hospital', 'hospital.id', '=', 'sala.id_hospital')
            ->select('sala.*', 'hospital.nombre_hospital as nombre_hospital')
            ->get();

        $agenda = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'paciente.id_seguro', '=', 'seguros.id')
            ->join('users as uc', 'uc.id', 'agenda.id_usuariocrea')
            ->join('users as um', 'um.id', 'agenda.id_usuariomod')
            ->leftjoin('historiaclinica as h', 'h.id_agenda', 'agenda.id')
            ->leftjoin('sala', 'agenda.id_sala', 'sala.id')
            ->leftjoin('hospital', 'sala.id_hospital', 'hospital.id')
            ->leftjoin('users as ud', 'ud.id', 'agenda.id_doctor1')
            ->leftjoin('especialidad', 'especialidad.id', 'agenda.espid')
            ->leftjoin('empresa', 'empresa.id', 'agenda.id_empresa')
            ->leftjoin('procedimiento', 'procedimiento.id', 'agenda.id_procedimiento')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'paciente.alergias as palergias', 'paciente.antecedentes_pat as pantecedentes_pat', 'paciente.antecedentes_fam as pantecedentes_fam', 'paciente.antecedentes_quir as pantecedentes_quir', 'seguros.color as color', 'uc.apellido1 as ucapellido', 'um.apellido1 as umapellido', 'uc.nombre1 as ucnombre', 'um.nombre1 as umnombre', 'paciente.fecha_nacimiento', 'paciente.ocupacion', 'h.parentesco as hparentesco', 'paciente.parentesco as pparentesco', 'paciente.estadocivil', 'paciente.ciudad', 'paciente.lugar_nacimiento', 'paciente.direccion', 'paciente.telefono1', 'paciente.telefono2', 'seguros.nombre as snombre', 'sala.nombre_sala as slnombre', 'hospital.nombre_hospital as hsnombre', 'ud.nombre1 as udnombre', 'ud.apellido1 as udapellido', 'especialidad.nombre as esnombre', 'procedimiento.nombre as pnombre', 'empresa.nombrecomercial')
            ->where('agenda.id', '=', $id)
            ->first();

        $agendaprocs = DB::table('agenda_procedimiento')
            ->join('procedimiento', 'procedimiento.id', 'agenda_procedimiento.id_procedimiento')
            ->select('agenda_procedimiento.*', 'procedimiento.nombre')
            ->where('id_agenda', $agenda->id)->get();

        /*23/11/2017 AGREGAR LA HISTORIA CLINICA CANTIDAD DE HISTORIA CLINICA */
        $hcp = DB::select("SELECT h.*, s.nombre as snombre ,e.nombre as especialidad, d.nombre1 as dnombre1, d.nombre2 as dnombre2, d.apellido1 as dapellido1, d.apellido2 as dapellido2, a.proc_consul as tipo_cita, a.fechaini as fechainicio
                            FROM historiaclinica h, seguros s, agenda a, especialidad e, users d
                            WHERE h.id_paciente = '" . $agenda->id_paciente . "'  AND
                            a.id = h.id_agenda AND
                            s.id = h.id_seguro AND
                            a.espid =  e.id AND
                            a.id_doctor1 = d.id AND
                            h.id_agenda <> " . $id . "
                            ORDER BY a.fechaini DESC");

        $historiaclinica = DB::table('historiaclinica as hc')
            ->join('users as d1', 'hc.id_doctor1', 'd1.id')
            ->join('seguros', 'hc.id_seguro', '=', 'seguros.id')
            ->leftjoin('pentax', 'pentax.hcid', 'hc.hcid')
            ->leftjoin('users as d2', 'hc.id_doctor2', 'd2.id')
            ->leftjoin('users as d3', 'hc.id_doctor3', 'd3.id')
            ->leftjoin('users as up', 'up.id', 'hc.id_usuario')
            ->leftjoin('subseguro', 'hc.id_subseguro', '=', 'subseguro.id')
            ->select('hc.*', 'seguros.nombre as snombre', 'seguros.color as color', 'seguros.tipo as stipo', 'subseguro.nombre as sbnombre', 'pentax.id', 'd1.nombre1 as d1nombre1', 'd1.apellido1 as d1apellido1', 'd2.nombre1 as d2nombre1', 'd2.apellido1 as d2apellido1', 'd3.nombre1 as d3nombre1', 'd3.apellido1 as d3apellido1', 'pentax.id as id_pentax', 'up.nombre1 as upnombre1', 'up.apellido1 as upapellido1', 'up.nombre2 as upnombre2', 'up.apellido2 as upapellido2')
            ->where('hc.id_agenda', '=', $agenda->id)
            ->first();

        $cantidad_doc = 0;
        $pentaxprocs  = null;
        $pentax_logs  = null;
        if (!is_null($historiaclinica)) {

            $ControlDocController = new ControlDocController;
            $cantidad_doc         = $ControlDocController->carga_documentos_union($historiaclinica->hcid, $agenda->proc_consul, $historiaclinica->stipo)->count();

            if (!is_null($historiaclinica->id_pentax)) {
                $pentaxprocs = DB::table('pentax_procedimiento')
                    ->join('procedimiento', 'procedimiento.id', 'pentax_procedimiento.id_procedimiento')
                    ->select('pentax_procedimiento.*', 'procedimiento.nombre')
                    ->where('id_pentax', $historiaclinica->id_pentax)->get();

                $pentax_logs = DB::table('pentax_log')->where('pentax_log.id_pentax', $historiaclinica->id_pentax)->join('users as d1', 'd1.id', '=', 'pentax_log.id_doctor1')->join('seguros', 'seguros.id', '=', 'pentax_log.id_seguro')->join('sala', 'sala.id', '=', 'pentax_log.id_sala')->join('users as um', 'um.id', '=', 'pentax_log.id_usuariomod')->leftJoin('users as d2', 'd2.id', '=', 'pentax_log.id_doctor2')->leftJoin('users as d3', 'd3.id', '=', 'pentax_log.id_doctor3')->select('pentax_log.*', 'd1.nombre1 as d1nombre1', 'd1.apellido1 as d1apellido1', 'd2.nombre1 as d2nombre1', 'd2.apellido1 as d2apellido1', 'd3.nombre1 as d3nombre1', 'd3.apellido1 as d3apellido1', 'seguros.nombre as snombre', 'sala.nombre_sala as nbrsala', 'um.nombre1 as umnombre1', 'um.apellido1 as umapellido1')->orderBy('pentax_log.created_at')->get();
                //dd($pentaxprocs);
                //dd($pentax_logs);
            }

        }

        $fecha_dia = date('Y-m-d', strtotime($agenda->fechaini));

        $nuevafecha = strtotime('+1 day', strtotime($fecha_dia));
        $nuevafecha = date('Y-m-d', $nuevafecha);

        $cant_cortesias = Agenda::where('id_doctor1', $agenda->id_doctor1)->where('fechaini', '>', $fecha_dia)->where('fechaini', '<', $nuevafecha)->where('cortesia', 'SI')->count();

        $hcagenda = DB::table('agenda_archivo')->where('id_agenda', '=', $id)->get();

        $sala     = null;
        $hospital = null;
        if (!is_null($agenda->id_sala)) {
            $sala     = Sala::find($agenda->id_sala);
            $hospital = Hospital::find($sala->id_hospital);
        }

        /*23/11/2017 AGREGAR LA HISTORIA CLINICA CANTIDAD DE HISTORIA CLINICA */

        return view('historiaclinica/detalle', ['agenda' => $agenda, 'usuarios' => $usuarios, 'enfermeros' => $enfermeros, 'salas' => $salas, 'agendaprocs' => $agendaprocs, 'hcp' => $hcp, 'cant_cortesias' => $cant_cortesias, 'hcagenda' => $hcagenda, 'sala' => $sala, 'hospital' => $hospital, 'historiaclinica' => $historiaclinica, 'pentaxprocs' => $pentaxprocs, 'pentax_logs' => $pentax_logs]);
    }

    /*public function procedimiento($id)
    {
    $rolUsuario = Auth::user()->id_tipo_usuario;
    if($this->rol()){
    return response()->view('errors.404');
    }

    return view('hc_admision/historia/procedimiento');
    $usuarios = DB::table('users')->where('id_tipo_usuario', '=', 3)->get(); //3=DOCTORES;
    $enfermeros = DB::table('users')->where('id_tipo_usuario', '=', 6)->get(); //6=ENFERMEROS;
    $salas = DB::table('sala')
    ->join('hospital', 'hospital.id', '=', 'sala.id_hospital')
    ->select('sala.*', 'hospital.nombre_hospital as nombre_hospital')
    ->get();

    $agenda = DB::table('agenda')
    ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
    ->join('seguros', 'paciente.id_seguro', '=', 'seguros.id')
    ->join('users as uc','uc.id','agenda.id_usuariocrea')
    ->join('users as um','um.id','agenda.id_usuariomod')
    ->leftjoin('historiaclinica as h','h.id_agenda','agenda.id')
    ->leftjoin('sala','agenda.id_sala','sala.id')
    ->leftjoin('hospital','sala.id_hospital','hospital.id')
    ->leftjoin('users as ud','ud.id','agenda.id_doctor1')
    ->leftjoin('especialidad','especialidad.id','agenda.espid')
    ->leftjoin('empresa','empresa.id','agenda.id_empresa')
    ->leftjoin('procedimiento','procedimiento.id','agenda.id_procedimiento')
    ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1','paciente.apellido2 as papellido2','paciente.alergias as palergias', 'paciente.antecedentes_pat as pantecedentes_pat', 'paciente.antecedentes_fam as pantecedentes_fam', 'paciente.antecedentes_quir as pantecedentes_quir', 'seguros.color as color', 'uc.apellido1 as ucapellido', 'um.apellido1 as umapellido', 'uc.nombre1 as ucnombre', 'um.nombre1 as umnombre','paciente.fecha_nacimiento','paciente.ocupacion','h.parentesco as hparentesco','paciente.parentesco as pparentesco','paciente.estadocivil','paciente.ciudad','paciente.lugar_nacimiento','paciente.direccion','paciente.telefono1','paciente.telefono2','seguros.nombre as snombre','sala.nombre_sala as slnombre','hospital.nombre_hospital as hsnombre','ud.nombre1 as udnombre','ud.apellido1 as udapellido','especialidad.nombre as esnombre','procedimiento.nombre as pnombre','empresa.nombrecomercial')
    ->where('agenda.id', '=', $id)
    ->first();

    $agendaprocs= DB::table('agenda_procedimiento')
    ->join('procedimiento','procedimiento.id','agenda_procedimiento.id_procedimiento')
    ->select('agenda_procedimiento.*','procedimiento.nombre')
    ->where('id_agenda',$agenda->id)->get();

    /*23/11/2017 AGREGAR LA HISTORIA CLINICA CANTIDAD DE HISTORIA CLINICA
    $hcp =  DB::select("SELECT h.*, s.nombre as snombre ,e.nombre as especialidad, d.nombre1 as dnombre1, d.nombre2 as dnombre2, d.apellido1 as dapellido1, d.apellido2 as dapellido2, a.proc_consul as tipo_cita, a.fechaini as fechainicio
    FROM historiaclinica h, seguros s, agenda a, especialidad e, users d
    WHERE h.id_paciente = '".$agenda->id_paciente."'  AND
    a.id = h.id_agenda AND
    s.id = h.id_seguro AND
    a.espid =  e.id AND
    a.id_doctor1 = d.id AND
    h.id_agenda <> ".$id."
    ORDER BY a.fechaini DESC");

    $historiaclinica = DB::table('historiaclinica as hc')
    ->join('users as d1','hc.id_doctor1', 'd1.id')
    ->join('seguros', 'hc.id_seguro', '=', 'seguros.id')
    ->leftjoin('pentax', 'pentax.hcid','hc.hcid')
    ->leftjoin('users as d2','hc.id_doctor2', 'd2.id')
    ->leftjoin('users as d3','hc.id_doctor3', 'd3.id')
    ->leftjoin('users as up', 'up.id', 'hc.id_usuario')
    ->leftjoin('subseguro', 'hc.id_subseguro', '=', 'subseguro.id')
    ->select('hc.*', 'seguros.nombre as snombre','seguros.color as color', 'seguros.tipo as stipo', 'subseguro.nombre as sbnombre', 'pentax.id', 'd1.nombre1 as d1nombre1', 'd1.apellido1 as d1apellido1', 'd2.nombre1 as d2nombre1', 'd2.apellido1 as d2apellido1', 'd3.nombre1 as d3nombre1', 'd3.apellido1 as d3apellido1','pentax.id as id_pentax','up.nombre1 as upnombre1','up.apellido1 as upapellido1' ,'up.nombre2 as upnombre2','up.apellido2 as upapellido2' )
    ->where('hc.id_agenda', '=', $agenda->id)
    ->first();

    $cantidad_doc=0;
    $pentaxprocs=null;
    $pentax_logs=null;
    if(!is_null($historiaclinica)){

    $ControlDocController = new ControlDocController;
    $cantidad_doc = $ControlDocController->carga_documentos_union($historiaclinica->hcid, $agenda->proc_consul, $historiaclinica->stipo)->count();

    if(!is_null($historiaclinica->id_pentax)){
    $pentaxprocs= DB::table('pentax_procedimiento')
    ->join('procedimiento','procedimiento.id','pentax_procedimiento.id_procedimiento')
    ->select('pentax_procedimiento.*','procedimiento.nombre')
    ->where('id_pentax',$historiaclinica->id_pentax)->get();

    $pentax_logs = DB::table('pentax_log')->where('pentax_log.id_pentax',$historiaclinica->id_pentax)->join('users as d1','d1.id','=','pentax_log.id_doctor1')->join('seguros','seguros.id','=','pentax_log.id_seguro')->join('sala','sala.id','=','pentax_log.id_sala')->join('users as um','um.id','=','pentax_log.id_usuariomod')->leftJoin('users as d2','d2.id','=','pentax_log.id_doctor2')->leftJoin('users as d3','d3.id','=','pentax_log.id_doctor3')->select('pentax_log.*','d1.nombre1 as d1nombre1','d1.apellido1 as d1apellido1','d2.nombre1 as d2nombre1','d2.apellido1 as d2apellido1','d3.nombre1 as d3nombre1','d3.apellido1 as d3apellido1','seguros.nombre as snombre','sala.nombre_sala as nbrsala','um.nombre1 as umnombre1','um.apellido1 as umapellido1')->orderBy('pentax_log.created_at')->get();
    //dd($pentaxprocs);
    //dd($pentax_logs);
    }

    }

    $fecha_dia = date('Y-m-d',strtotime($agenda->fechaini));

    $nuevafecha = strtotime ( '+1 day' , strtotime ( $fecha_dia ) ) ;
    $nuevafecha = date ( 'Y-m-d' , $nuevafecha );

    $cant_cortesias =Agenda::where('id_doctor1',$agenda->id_doctor1)->where('fechaini','>',$fecha_dia)->where('fechaini','<',$nuevafecha)->where('cortesia','SI')->count();

    $hcagenda = DB::table('agenda_archivo')->where('id_agenda', '=', $id)->get();

    $sala=null;
    $hospital=null;
    if(!is_null($agenda->id_sala)){
    $sala = Sala::find($agenda->id_sala);
    $hospital = Hospital::find($sala->id_hospital);
    }

    /*23/11/2017 AGREGAR LA HISTORIA CLINICA CANTIDAD DE HISTORIA CLINICA

    return view('hc_admision/historia/detalle', ['agenda' => $agenda, 'usuarios' => $usuarios, 'enfermeros' => $enfermeros, 'salas' => $salas, 'agendaprocs' => $agendaprocs, 'hcp' => $hcp, 'cant_cortesias' => $cant_cortesias, 'hcagenda' => $hcagenda, 'sala' => $sala, 'hospital' => $hospital, 'historiaclinica' => $historiaclinica, 'pentaxprocs' => $pentaxprocs, 'pentax_logs' => $pentax_logs]);
    }*/

}
