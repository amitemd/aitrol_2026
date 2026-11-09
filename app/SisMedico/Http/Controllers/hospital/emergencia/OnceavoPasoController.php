<?php

namespace Sis_medico\Http\Controllers\hospital\emergencia;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\Hospital_Bodega;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Opcion_Usuario;
use Sis_medico\Ho_Solicitud;
use Sis_medico\Hc_Cie10;
use Sis_medico\Hc_Log;
use Sis_medico\Cie_10_3;
use Sis_medico\Cie_10_4;
use Sis_medico\Services\ClinicalEmergencyDiagnosisService;

class OnceavoPasoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    private function rol_new($opcion)
    {
        if (Auth::user()->isSystemUser()) {
            return false;
        }
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $opcion_usuario = Opcion_Usuario::where('id_opcion', $opcion)->where('id_tipo_usuario', $rolUsuario)->first();
        if (is_null($opcion_usuario)) {
            return true;
        }
    }
    public function onceavopaso($id_sol)
    {
        $solicitud = Ho_Solicitud::where('ho_solicitud.id',$id_sol)
        ->join('agenda as ag','ag.id','ho_solicitud.id_agenda')
        ->join('historiaclinica as h','h.id_agenda','ag.id')
        ->join('hc_procedimientos as hc_proc','hc_proc.id_hc','h.hcid')
        ->select('ag.id as id_agenda','h.hcid','hc_proc.id as id_hcproc','ho_solicitud.id_paciente')
        ->first();

        return view('hospital.emergencia.onceavopaso',['id_sol' => $id_sol, 'solicitud' => $solicitud]);
    }

    public function agregar_cie10(Request $request)
    {
        //return $request->all();
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        //date_default_timezone_set('America/Guayaquil');
        $id_solicitud = $request['id_solicitud'];

        $solicitud = Ho_Solicitud::where('ho_solicitud.id',$id_solicitud)
        ->join('agenda as ag','ag.id','ho_solicitud.id_agenda')
        ->join('historiaclinica as h','h.id_agenda','ag.id')
        ->join('hc_procedimientos as hc_proc','hc_proc.id_hc','h.hcid')
        ->select('ag.id as id_agenda','h.hcid','hc_proc.id as id_hcproc','ho_solicitud.id_paciente')
        ->first();
        //dd($solicitud, $request->all());
        

        $codigo = strtoupper(trim((string) $request['codigo']));
        if ($codigo === '') {
            return ['estado' => 'err', 'msn' => 'Seleccione un CIE10'];
        }

        $catalogo = Cie_10_4::find($codigo);
        if (is_null($catalogo)) {
            $catalogo = Cie_10_3::find($codigo);
        }
        if (is_null($catalogo)) {
            return ['estado' => 'err', 'msn' => 'El CIE10 seleccionado no existe'];
        }

        $diagnosticos = Hc_Cie10::where('hc_id_procedimiento', $solicitud->id_hcproc)
            ->where('ingreso_egreso', 'INGRESO')->get();
        if ($diagnosticos->where('cie10', $codigo)->count() > 0) {
            return ['estado' => 'err', 'msn' => 'El diagnóstico ya está registrado en este episodio'];
        }

        $diagnosticosPrevios = implode(', ', $diagnosticos->pluck('cie10')->all());
        $input2 = [
            'hcid'                  => $solicitud->hcid,
            'cie10'                 => $codigo,
            'hc_id_procedimiento'   => $solicitud->id_hcproc,
            'ingreso_egreso'        => 'INGRESO',
            'presuntivo_definitivo' => $request['pre_def'],
            'ip_creacion'           => $ip_cliente,
            'ip_modificacion'       => $ip_cliente,
            'id_usuariocrea'        => $idusuario,
            'id_usuariomod'         => $idusuario,

        ];
        $id = Hc_Cie10::insertGetId($input2);

        Hc_Log::create([
            'anterior'         => 'CONSULTA -> Diagnósticos de ingreso: ' . ($diagnosticosPrevios ?: 'NINGUNO'),
            'nuevo'            => 'CONSULTA -> Diagnóstico de ingreso agregado: ' . $codigo,
            'hc_id'            => $solicitud->hcid,
            'id_paciente'      => $solicitud->id_paciente,
            'id_procedimiento' => $solicitud->id_hcproc,
            'id_usuariomod'    => $idusuario,
            'id_usuariocrea'   => $idusuario,
            'ip_modificacion'  => $ip_cliente,
            'ip_creacion'      => $ip_cliente,
        ]);

        $count = Hc_Cie10::where('hc_id_procedimiento', $solicitud->id_hcproc)->where('ingreso_egreso','INGRESO')->get()->count();

        $cie10 = Hc_Cie10::find($id);

        $descripcion = $catalogo->descripcion;

        return ['count' => $count, 'id' => $id, 'cie10' => $cie10->cie10, 'descripcion' => $descripcion, 'pre_def' => $request['pre_def'], 'in_eg' => 'INGRESO','id_hcproc' => $solicitud->id_hcproc];
    }

    public function cargar_tabla_cie(Request $request)
    {
        return app(ClinicalEmergencyDiagnosisService::class)
            ->listForProcedure($request['id_hcproc'], 'INGRESO');
    }
}
