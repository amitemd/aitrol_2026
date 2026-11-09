<?php

namespace Sis_medico\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Sis_medico\Agenda;
use Sis_medico\Examen_Orden;
use Sis_medico\Hc_Evolucion;
use Sis_medico\Historiaclinica;
use Sis_medico\Ho_Solicitud;
use Sis_medico\Paciente;
use Sis_medico\Paciente_Alergia;
use Sis_medico\hc_procedimientos;

class ClinicalHospitalQueryService
{
    public function autocompletarcie(Request $request)
    {

        $codigo = $request['term'];
        $data = null;
        $nuevo_nombre = explode(' ', $codigo);
        $seteo = "%";
        foreach ($nuevo_nombre as $value) {
            $seteo = $seteo . $value . '%';
        }
        $query = "SELECT CONCAT_WS(' ', id, descripcion) as completo
                  FROM `Cie_10_3`
                  WHERE CONCAT_WS(' ', id, descripcion) like '" . $seteo . "' 
                  ";
        $query1 = "SELECT CONCAT_WS(' ', id, descripcion) as completo1
                  FROM `Cie_10_4`
                  WHERE CONCAT_WS(' ', id, descripcion) like '" . $seteo . "' 
                  ";
        $nombres = DB::select($query);
        $nombres1 = DB::select($query1);
        foreach ($nombres as $product) {
            $data[] = array('value' => $product->completo);
        }
        foreach ($nombres1 as $product) {
            $data[] = array('value' => $product->completo1);
        }

        if (count($data)) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados'];
        }
    }

    public function detalles($id, Request $request)
    {

        //$paciente = Paciente::find($id_paciente);
        $solicitud = Ho_Solicitud::findOrFail($id);
        if (in_array((int) $solicitud->estado_paso, [1, 2, 3, 5, 6, 7], true)) {
            app(ClinicalHospitalTransferService::class)->authorize($solicitud, 'hospital.view', ['SISTEMAS', 'DOCTORES', 'DOCTOR', 'ENFERMERIA', 'ENFERMERÍA', 'ANESTESIÓLOGO', 'ANESTESIOLOGO'], false);
            if (!$solicitud->id_agenda) {
                return redirect()->route('hospital.ingreso008', ['id_solicitud' => $solicitud->id]);
            }
        }
        //dd($solicitud);

        //nuevo cambio
        $paciente = Paciente::find($solicitud->id_paciente);
        $sql = "SELECT sol.fecha_ingreso, a.id_paciente,p.religion,TIMESTAMPDIFF(DAY,sol.fecha_ingreso, CURDATE()) as dias_hospitalizados,
        CONCAT(p.apellido1, ' ', p.apellido2, ' ', p.nombre1, ' ', p.nombre2) as nombre_paciente,seg.nombre as seguro, 
        CONCAT(u.apellido1, ' ', u.apellido2, ' ', u.nombre1, ' ', u.nombre2) as nombre_doctor,
        p.fecha_nacimiento, YEAR(CURDATE())-YEAR(p.fecha_nacimiento) + IF(DATE_FORMAT(CURDATE(),'%m-%d') > DATE_FORMAT(p.fecha_nacimiento,'%m-%d'), 0 , -1 ) as edad, 
        if(p.antecedentes_pat is null,'No tiene antecedentes patologicos',p.antecedentes_pat) as ante_patologicos, 
        if(p.antecedentes_fam is null,'No tiene antecedentes familiares',p.antecedentes_fam) as ante_familiares, 
        if(p.antecedentes_quir is null,'No tiene antecedentes quirurgicos',p.antecedentes_quir) as ante_quirurgicos from ho_solicitud sol
        join agenda a on a.id = sol.id_agenda
        join paciente p on p.id = a.id_paciente 
        join users u on u.id = a.id_doctor1
        left join seguros seg on seg.id = a.id_seguro where sol.id = ?";
        $data = DB::select($sql, [$solicitud->id]);
        abort_unless(count($data), 422, 'Complete los datos del ingreso administrativo.');

        $alergiasDB = Paciente_Alergia::where('id_paciente', $paciente->id)
            ->join('principio_activo as pa', 'pa.id', 'paciente_alergia.id_principio_activo')
            ->where('paciente_alergia.id_paciente', $paciente->id)->select('pa.nombre')->get();
        $str_alergias = '';
        $separador = '';
        if (count($alergiasDB) == 1) {
            $separador = '';
        } else {
            $separador = ', ';
        }
        foreach ($alergiasDB as  $alergia) {
            $str_alergias .= $alergia->nombre . $separador;
        }
        $id_paciente = $paciente->id;

        $arr_listado = [];
        $examen = Examen_Orden::where('id_paciente', $id_paciente)->where('estado', 1)->select('fecha_orden', 'id')->first();
        if (!is_null($examen)) {
            $arr_listado[0] = [$examen->fecha_orden, 'LABORATORIO', $examen->id];
        }

        $procedimiento = Agenda::join('historiaclinica as hc', 'hc.id_agenda', '=', 'agenda.id')->join('hc_protocolo as proto', 'proto.hcid', '=', 'hc.hcid')->join('hc_procedimientos as hc_proc', 'hc_proc.id_hc', '=', 'hc.hcid')->join('hc_procedimiento_final as hc_procf', 'hc_procf.id_hc_procedimientos', '=', 'hc_proc.id')->join('procedimiento as proc', 'proc.id', '=', 'hc_procf.id_procedimiento')->where('agenda.id_paciente', $id_paciente)->where('agenda.estado', 1)->where('agenda.proc_consul', 1)->wherenotNull('proto.hallazgos')->wherenotNull('proc.id_grupo_procedimiento')->select('agenda.id as id_agenda', 'agenda.fechaini', 'proto.id_hc_procedimientos', 'proc.nombre', 'proto.id as id_protocolo')->first();
        if (!is_null($procedimiento)) {
            $arr_listado[1] = [$procedimiento->fechaini, $procedimiento->nombre, $procedimiento->id_protocolo];
        }

        $consulta = Agenda::join('historiaclinica as hc', 'hc.id_agenda', '=', 'agenda.id')->join('hc_evolucion as evo', 'evo.hcid', '=', 'hc.hcid')->join('especialidad as esp', 'esp.id', '=', 'agenda.espid')->where('agenda.id_paciente', $id_paciente)->where('agenda.estado', 1)->where('agenda.proc_consul', 0)->where('evo.secuencia', '=', '0')->wherenotNull('evo.cuadro_clinico')->orderby('agenda.fechaini', 'desc')->select('agenda.id as id_agenda', 'agenda.fechaini', 'evo.id as id_hc_evolucion', 'esp.nombre')->first();
        if (!is_null($consulta)) {
            $arr_listado[2] = [$consulta->fechaini, $consulta->nombre, $consulta->id_hc_evolucion];
        }

        $historia = Historiaclinica::where('id_agenda', $solicitud->id_agenda)
            ->where('id_paciente', $solicitud->id_paciente)
            ->when($solicitud->encounter_id, function ($query) use ($solicitud) {
                $query->where('encounter_id', $solicitud->encounter_id);
            })
            ->first();
        if (!$historia && $solicitud->encounter_id) {
            $historia = Historiaclinica::where('encounter_id', $solicitud->encounter_id)
                ->where('id_paciente', $solicitud->id_paciente)
                ->orderByDesc('hcid')
                ->first();
        }
        $procedimiento = $historia
            ? hc_procedimientos::where('id_hc', $historia->hcid)->orderByDesc('id')->first()
            : null;
        $evoluciones = $procedimiento
            ? Hc_Evolucion::where('hc_id_procedimiento', $procedimiento->id)->orderByDesc('created_at')->get()
            : collect();

        return view('hospital.detalles', [
            'arr_listado' => $arr_listado,
            'solicitud' => $solicitud,
            'data' => $data[0],
            'str_alergias' => $str_alergias,
            'evolucionesEmergencia' => $evoluciones,
        ]);
    }

}
