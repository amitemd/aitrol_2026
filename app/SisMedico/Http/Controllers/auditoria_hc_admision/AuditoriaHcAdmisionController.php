<?php

namespace Sis_medico\Http\Controllers\auditoria_hc_admision;
use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\User;
use Sis_medico\Bodega;
use Illuminate\Support\Facades\DB;
use Sis_medico\tipousuario;
use Sis_medico\Hospital;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Sis_medico\Seguro;
use Sis_medico\Empresa;
use Sis_medico\Subseguro;
use Sis_medico\Documento;
use Sis_medico\Archivo_historico;
use Sis_medico\Historiaclinica;
use Sis_medico\Agenda;
use Sis_medico\Procedimiento;
use Sis_medico\AgendaProcedimiento;
use Sis_medico\Paciente;
use Sis_medico\Pentax;
use Sis_medico\PentaxProc;
use Sis_medico\Log_usuario;
use Sis_medico\hc_receta;
use Sis_medico\Hc_Evolucion;
use Sis_medico\hc_protocolo;
use Sis_medico\Hc_Evolucion_Indicacion;
use Sis_medico\hc_procedimientos;
use Sis_medico\hc_child_pugh;
use Sis_medico\Paciente_Alergia;
use Sis_medico\Paciente_Biopsia;
use Sis_medico\procedimiento_completo;
use Sis_medico\Hc_Procedimiento_Final;
use Excel;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Response;
use Sis_medico\Aud_Hc_Procedimientos;
use Sis_medico\Aud_Hc_Protocolo;
use Sis_medico\Aud_Hc_Evolucion;
use Sis_medico\Aud_Hc_Epicrisis;
use Sis_medico\Aud_Hc_Cie10;
use Sis_medico\Aud_Hc_Cpre_Eco;
use Sis_medico\Aud_Hc_Child_Pugh; 
use Sis_medico\Aud_Hc_Procedimiento_Final;
use Sis_medico\Hc_Epicrisis;
use Sis_medico\Hc_Cie10;
use Sis_medico\Hc_cpre_eco;

class AuditoriaHcAdmisionController extends Controller
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
        if (in_array($rolUsuario, array(1)) == false) {
            return true;
        }
    }

    public function duplicar_registros($id_agenda){

    	//dd("entra");

    	$ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $agenda = Agenda::find($id_agenda);

        $historia = $agenda->historia_clinica;


        if (!is_null($historia)) {
      	$hc_procedimientos = hc_procedimientos::where('id_hc',$historia->hcid)->get();

    		if (!is_null($hc_procedimientos)) {
    			$cont = 0;
    			foreach ($hc_procedimientos as $procedimiento) {
    				$cont ++;
    				
    				$aud_procedimientos = Aud_Hc_Procedimientos::where('id_procedimientos_org', $procedimiento->id)->first();

	    			$hc_proc_final = Hc_Procedimiento_Final::where('id_hc_procedimientos', $procedimiento->id)->get(); 

	        			if (is_null($aud_procedimientos)) {

	        				$arr_proc = [
	        					'id_hc'							=> $procedimiento->id_hc,
	        					'id_seguro'						=> $procedimiento->id_seguro,
	        					'id_subseguro'					=> $procedimiento->id_subseguro,
	        					'id_empresa'					=> $procedimiento->id_empresa,
	        					'id_procedimiento_completo'		=> $procedimiento->id_procedimiento_completo,
	        					'fecha'							=> $procedimiento->fecha,
	        					'hora_inicio'					=> $procedimiento->hora_inicio,
	        					'estado'						=> $procedimiento->estado,
	        					'hora_fin'						=> $procedimiento->hora_fin,
	        					'id_doctor_examinador'			=> $procedimiento->id_doctor_examinador,
	        					'id_doctor_examinador2'			=> $procedimiento->id_doctor_examinador2,
	        					'id_doctor_responsable'			=> $procedimiento->id_doctor_responsable,
	        					'id_doctor_ayudante_con'		=> $procedimiento->id_doctor_ayudante_con,
	        					'observaciones'					=> $procedimiento->observaciones,
	        					'estado_pago'					=> $procedimiento->estado_pago,
	        					'tipo_procedimiento'			=> $procedimiento->tipo_procedimiento,
	        					'estimado_minimo'				=> $procedimiento->estimado_minimo,
	        					'copago'						=> $procedimiento->copago,
	        					'pagado'						=> $procedimiento->pagado,
	        					'pago_copago'					=> $procedimiento->pago_copago,
	        					'cuadro_clinico_bp'				=> $procedimiento->cuadro_clinico_bp,
	        					'diagnosticos_bp'				=> $procedimiento->diagnosticos_bp,
	        					'id_procedimientos_org'			=> $procedimiento->id,
	        					'id_usuariocrea'     			=> $idusuario,
					            'id_usuariomod'     			=> $idusuario,
					            'ip_creacion'        			=> $ip_cliente,
					            'ip_modificacion'    			=> $ip_cliente,
	        				];
	        				

	        				Aud_Hc_Procedimientos::create($arr_proc);


	        				foreach ($hc_proc_final as $proc_final) {
	        					$arr_proc_final = [
			        				'id_hc_procedimientos'			=> $proc_final->id_hc_procedimientos,
			        				'id_procedimiento'				=> $proc_final->id_procedimiento,
			        				'id_usuariocrea'     			=> $idusuario,
						            'id_usuariomod'     			=> $idusuario,
						            'ip_creacion'        			=> $ip_cliente,
						            'ip_modificacion'    			=> $ip_cliente,
			        			];

			        			Aud_Hc_Procedimiento_Final::create($arr_proc_final);
	        				}
	        			}
    			}
    		}

        	$hc_protocolo = hc_protocolo::where('hcid', $historia->hcid)->get();

        	if (!is_null($hc_protocolo)) {
        		foreach($hc_protocolo as $protocolo){
        			$aud_protocolo = Aud_Hc_Protocolo::where('id_protocolo_org', $protocolo->id)->first();

        			if (is_null($aud_protocolo)) {
        				$arr_protocolo = [
        					'motivo'						=> $protocolo->motivo,
        					'conclusion'					=> $protocolo->conclusion,
        					'hallazgos'						=> $protocolo->hallazgos,
        					'fecha'							=> $protocolo->fecha,
        					'hcid'							=> $protocolo->hcid,
        					'id_hc_procedimientos'			=> $protocolo->id_hc_procedimientos,
        					'fecha_operacion'				=> $protocolo->fecha_operacion,
        					'hora_inicio'					=> $protocolo->hora_inicio,
        					'hora_fin'						=> $protocolo->hora_fin,
        					'tipo_anestesia'				=> $protocolo->tipo_anestesia,
        					'intervalo_anestesia'			=> $protocolo->intervalo_anestesia,
        					'complicaciones'				=> $protocolo->complicaciones,
        					'estado_final'					=> $protocolo->estado_final,
        					'observaciones'					=> $protocolo->observaciones,
        					'complicacion'					=> $protocolo->complicacion,
        					'estado_paciente'				=> $protocolo->estado_paciente,
        					'plan'							=> $protocolo->plan,
        					'estudio_patologico'			=> $protocolo->estudio_patologico,
        					'id_anestesiologo'				=> $protocolo->id_anestesiologo,
        					'tipo_procedimiento'			=> $protocolo->tipo_procedimiento,
        					'referido_por'					=> $protocolo->referido_por,
        					'id_usuariocrea'     			=> $idusuario,
				            'id_usuariomod'     			=> $idusuario,
				            'ip_creacion'        			=> $ip_cliente,
				            'ip_modificacion'    			=> $ip_cliente,
				            'id_protocolo_org'				=> $protocolo->id,
        				];

						Aud_Hc_Protocolo::insert($arr_protocolo);
					} else {
						if ($aud_protocolo->hallazgos == null && $aud_protocolo->motivo == null && $aud_protocolo->conclusion == null) {
							$arr_protocolo = [
								'motivo'						=> $protocolo->motivo,
								'conclusion'					=> $protocolo->conclusion,
								'hallazgos'						=> $protocolo->hallazgos,
								'fecha'							=> $protocolo->fecha,
								'hcid'							=> $protocolo->hcid,
								'id_hc_procedimientos'			=> $protocolo->id_hc_procedimientos,
								'fecha_operacion'				=> $protocolo->fecha_operacion,
								'hora_inicio'					=> $protocolo->hora_inicio,
								'hora_fin'						=> $protocolo->hora_fin,
								'tipo_anestesia'				=> $protocolo->tipo_anestesia,
								'intervalo_anestesia'			=> $protocolo->intervalo_anestesia,
								'complicaciones'				=> $protocolo->complicaciones,
								'estado_final'					=> $protocolo->estado_final,
								'observaciones'					=> $protocolo->observaciones,
								'complicacion'					=> $protocolo->complicacion,
								'estado_paciente'				=> $protocolo->estado_paciente,
								'plan'							=> $protocolo->plan,
								'estudio_patologico'			=> $protocolo->estudio_patologico,
								'id_anestesiologo'				=> $protocolo->id_anestesiologo,
								'tipo_procedimiento'			=> $protocolo->tipo_procedimiento,
								'referido_por'					=> $protocolo->referido_por,

							];
							$aud_protocolo->update($arr_protocolo);
						}
					}
				}
			}


	        $hc_evolucion = Hc_Evolucion::where('hcid', $historia->hcid)->get();

	        if (!is_null($hc_evolucion)) {
	        	foreach($hc_evolucion as $evolucion){
	        		$hc_child_pugh = hc_child_pugh::where('id_hc_evolucion',$evolucion->id)->first();
	        		//dd($hc_child_pugh);
	        		$aud_evolucion = Aud_Hc_Evolucion::where('id_evolucion_org',$evolucion->id)->first();
	        		if (is_null($aud_evolucion)) {
	        			$arr_evol = [
	        				'hc_id_procedimiento'			=> $evolucion->hc_id_procedimiento,
	        				'hcid'							=> $evolucion->hcid,
	        				'secuencia'						=> $evolucion->secuencia,
	        				'motivo'						=> $evolucion->motivo,
	        				'cuadro_clinico'				=> $evolucion->cuadro_clinico,
	        				'indicacion'					=> $evolucion->indicacion,
	        				'laboratorio'					=> $evolucion->laboratorio,
	        				'fecha_ingreso'					=> $evolucion->fecha_ingreso,
	        				'resultado'						=> $evolucion->resultado,
	        				'fecha_doctor'					=> $evolucion->fecha_doctor,
	        				'indicaciones'					=> $evolucion->indicaciones,
	        				'estado'						=> $evolucion->estado,
	        				'id_usuariocrea'     			=> $idusuario,
				            'id_usuariomod'     			=> $idusuario,
				            'ip_creacion'        			=> $ip_cliente,
				            'ip_modificacion'    			=> $ip_cliente,
				            'id_evolucion_org'				=> $evolucion->id,
	        			];

	        			Aud_Hc_Evolucion::create($arr_evol);

	        		}
	        		if(!is_null($hc_child_pugh)){
	        			$arr_child = [
	        				'id_hc_evolucion'				=> $hc_child_pugh->id_hc_evolucion,
	        				'ascitis'						=> $hc_child_pugh->ascitis,
	        				'encefalopatia'					=> $hc_child_pugh->encefalopatia,
	        				'albumina'						=> $hc_child_pugh->albumina,
	        				'bilirrubina'					=> $hc_child_pugh->bilirrubina,
	        				'inr'							=> $hc_child_pugh->inr,
	        				'examen_fisico'					=> $hc_child_pugh->examen_fisico,
	        				'id_usuariocrea'     			=> $idusuario,
				            'id_usuariomod'     			=> $idusuario,
				            'ip_creacion'        			=> $ip_cliente,
				            'ip_modificacion'    			=> $ip_cliente,
				            'id_child_pugh_org'				=> $hc_child_pugh->id,
	        			];

	        			Aud_Hc_Child_Pugh::create($arr_child);
	        		}
	        	}
	        	
	        }

	        $hc_epicrisis = Hc_Epicrisis::where('hcid', $historia->hcid)->get();

	        if (!is_null($hc_epicrisis)) {
	        	foreach($hc_epicrisis as $epicrisis){
	        		$aud_epicrisis = Aud_Hc_Epicrisis::where('id_epricrisis_org', $epicrisis->id)->first();
	        		if (is_null($aud_epicrisis)) {
	        			$arr_epicrisis = [
	        				'hc_id_procedimiento'			=> $epicrisis->hc_id_procedimiento,
	        				'hcid'							=> $epicrisis->hcid,
	        				'cuadro_clinico'				=> $epicrisis->cuadro_clinico,
	        				'favorable_des'					=> $epicrisis->favorable_des,
	        				'complicacion'					=> $epicrisis->complicacion,
	        				'hallazgo'						=> $epicrisis->hallazgo,
	        				'resumen'						=> $epicrisis->resumen,
	        				'condicion'						=> $epicrisis->condicion,
	        				'pronostico'					=> $epicrisis->pronostico,
	        				'alta'							=> $epicrisis->alta,
	        				'discapacidad'					=> $epicrisis->discapacidad,
	        				'retiro'						=> $epicrisis->retiro,
	        				'defuncion'						=> $epicrisis->defuncion,
	        				'dias_estadia'					=> $epicrisis->dias_estadia,
	        				'dias_incapacidad'				=> $epicrisis->dias_incapacidad,
	        				'fecha_imprime'					=> $epicrisis->fecha_imprime,
	        				'ep_resumen_evolucion'			=> $epicrisis->ep_resumen_evolucion,
	        				'receta'						=> $epicrisis->receta,
	        				'id_usuariocrea'     			=> $idusuario,
				            'id_usuariomod'     			=> $idusuario,
				            'ip_creacion'        			=> $ip_cliente,
				            'ip_modificacion'    			=> $ip_cliente,
				            'id_epricrisis_org'				=> $epicrisis->id,
	        			];

	        			Aud_Hc_Epicrisis::create($arr_epicrisis);
	        		}
	        	}
	        }

	        $hc_cie10 = Hc_Cie10::where('hcid', $historia->hcid)->get();

	        if (!is_null($hc_cie10)) {
	        	foreach($hc_cie10 as $cie10){
	        		$aud_cie10 = Aud_Hc_Cie10::where('id_cie10_org',$cie10->id)->first();

	        		if (is_null($aud_cie10)) {
	      				$arr_cie = [
	      					'hc_id_procedimiento'			=> $cie10->hc_id_procedimiento,
	      					'hcid'							=> $cie10->hcid,
	      					'cie10'							=> $cie10->cie10,
	      					'ingreso_egreso'				=> $cie10->ingreso_egreso,
	      					'presuntivo_definitivo'			=> $cie10->presuntivo_definitivo,
	      					'id_usuariocrea'     			=> $idusuario,
				            'id_usuariomod'     			=> $idusuario,
				            'ip_creacion'        			=> $ip_cliente,
				            'ip_modificacion'    			=> $ip_cliente,
				            'id_cie10_org'					=> $cie10->id,
	      				];

	      				Aud_Hc_Cie10::create($arr_cie);
	        		}
	        	}
	        }

	        $hc_cpre_eco = Hc_cpre_eco::where('hcid', $historia->hcid)->first();

	        if (!is_null($hc_cpre_eco)) {
	        	$aud_cpre_eco = Aud_Hc_Cpre_Eco::where('id_cpre_eco_org', $hc_cpre_eco->id)->first();
	        	 if (is_null($aud_cpre_eco)) {
	        	 	$arr_cpre = [
	        	 		'hcid'							=> $hc_cpre_eco->hcid,
	        	 		'fecha_operacion'				=> $hc_cpre_eco->fecha_operacion,
	        	 		'id_doctor1'					=> $hc_cpre_eco->id_doctor1,
	        	 		'id_doctor2'					=> $hc_cpre_eco->id_doctor2,
	        	 		'tipo_anestesia'				=> $hc_cpre_eco->tipo_anestesia,
	        	 		'hora_inicio'					=> $hc_cpre_eco->hora_inicio,
	        	 		'hora_fin'						=> $hc_cpre_eco->hora_fin,
	        	 		'intervalo_anestesia'			=> $hc_cpre_eco->intervalo_anestesia,
	        	 		'conclusion'					=> $hc_cpre_eco->conclusion,
	        	 		'hallazgos'						=> $hc_cpre_eco->hallazgos,
	        	 		'id_usuariocrea'     			=> $idusuario,
			            'id_usuariomod'     			=> $idusuario,
			            'ip_creacion'        			=> $ip_cliente,
			            'ip_modificacion'    			=> $ip_cliente,
			            'id_cpre_eco_org'				=> $hc_cpre_eco->id,
	        	 	];

	        	 	Aud_Hc_Cpre_Eco::create($arr_cpre);

	        	 }
	        }

	       // $procedimientos = Agenda::find($id_agenda);
        }

        return ['estado' => "ok", 'id_agenda' => $id_agenda];
    }
   

}