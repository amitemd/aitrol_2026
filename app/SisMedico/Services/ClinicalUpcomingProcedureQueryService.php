<?php

namespace Sis_medico\Services;

use Sis_medico\Agenda;
use Sis_medico\Log_Agenda;
use Sis_medico\Paciente;
use Sis_medico\Procedimiento;
use Sis_medico\hc_imagenes_protocolo;

class ClinicalUpcomingProcedureQueryService
{
    public function agendaLog($agendaId)
    {
        $logs = Log_Agenda::where('id_agenda', $agendaId)->get();

        return view('hc4/consulta/log_agenda', ['logs' => $logs]);
    }

    public function reviewLog($agendaId)
    {
        return view('hc4/revisar_procedimientos/log', array(
            'logs' => Log_Agenda::where('id_agenda', $agendaId)->get(),
            'agenda' => Agenda::find($agendaId),
        ));
    }

    public function imageModal($id)
    {
        $image = hc_imagenes_protocolo::where('id', $id)->where('estado', '1')->orderBy('created_at', 'desc')->first();
        $url = secure_url('storage/app/hc_ima/'.rawurlencode($image->nombre));

        return view('hc4/modaleditor', array('imagenf' => $image, 'url' => $url, 'id' => $id));
    }

    public function index_proximos_proc($id_paciente)
    {
        $fecha = date('Y-m-d H:i:s');
        $fecha_2 = date('Y-m-d H:i:s', strtotime($fecha. "-3 month"));
        $prox_procedimientos = Agenda::where('agenda.id_paciente', $id_paciente)
        ->join('users as uc', 'uc.id', 'agenda.id_usuariocrea')
        ->join('users as um', 'um.id', 'agenda.id_usuariomod')
        //->where('agenda.estado', '<>', '0')
        ->where('agenda.proc_consul', '1')
        ->where('agenda.fechaini', '>=', $fecha)
        ->select('agenda.*', 'uc.apellido1 as ucapellido', 'um.apellido1 as umapellido', 'uc.nombre1 as ucnombre', 'um.nombre1 as umnombre')
        ->OrderBy('agenda.fechaini', 'asc')->get();
        $procedimientos = Procedimiento::all();
        $paciente = Paciente::find($id_paciente);
        return view('hc4/proximos_procedimientos/index', ['paciente' => $paciente, 'prox_procedimientos' => $prox_procedimientos, 'procedimientos' => $procedimientos]);
    }
}
