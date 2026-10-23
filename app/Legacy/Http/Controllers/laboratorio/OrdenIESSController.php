<?php

namespace Sis_medico\Http\Controllers\laboratorio;

use Carbon\Carbon;
use Cookie;
use DateTime;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Sis_medico\Cie_10_3;
use Sis_medico\Cie_10_4;
use Sis_medico\Empresa;
use Sis_medico\Examen_Agrupador;
use Sis_medico\Examen_Agrupador_labs;
use Sis_medico\Examen_Detalle;
use Sis_medico\Examen_Orden;
use Sis_medico\Examen_Parametro;
use Sis_medico\Hc_Cie10;
use Sis_medico\Historiaclinica;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Paciente;
use Sis_medico\User;
use Sis_medico\Aud_Hc_Cie10;

class OrdenIESSController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function imprimir_resultado($id)
    {
        $id_empresa = Session::get('id_empresa');
        $empresa = Empresa::find($id_empresa);
        $tipo_usuario  = Auth::user()->id_tipo_usuario;
        $orden    = Examen_Orden::find($id);
        $paciente = Paciente::find($orden->id_paciente);
        $user     = User::find($paciente->id_usuario);
        $detalle = Examen_Detalle::where('id_examen_orden', $orden->id)->join('examen as e', 'e.id', 'id_examen')->select('examen_detalle.*', 'e.secuencia')->orderBy('e.secuencia')->get();
        $resultados = $orden->resultados;
        $parametros = Examen_parametro::orderBy('orden')->get();
        
        //Recalcula Porcentaje
        $cant_par = 0;
        foreach ($detalle as $d) {
            if ($d->id_examen == '639') {
                $xpar = $resultados->where('id_examen', '639')->where('valor', '<>', '0');
                if ($xpar->count() > 0) {
                    $cant_par = $cant_par + $xpar->count();
                } else {
                    $cant_par = $cant_par + 10;
                }
            } else {
                if ($d->examen->no_resultado == '0') {

                    if (count($d->parametros->where('estado', '1')) == '0') {
                        $cant_par++;
                    }
                    if ($d->examen->sexo_n_s == '0') {
                        $parametro_nuevo = $d->parametros->where('sexo', '3')->where('estado', '1');
                    } else {
                        $parametro_nuevo = $d->parametros->where('sexo', $orden->paciente->sexo)->where('estado', '1');
                    }
                    foreach ($parametro_nuevo as $p) {
                        $cant_par++;
                    }
                }
            }
        }

        $certificados = 0;
        $cantidad     = 0;
        foreach ($resultados as $r) {
            $cantidad++;
            if ($r->certificado == '1') {
                $certificados++;
            }
        }
        if ($certificados > $cant_par) {
            $certificados = $cant_par;
        }

        if ($cant_par == '0') {
            $pct = 0;
        } else {
            $pct = $certificados / $cant_par * 100;
        }

        if ($orden->seguro->tipo == '0') {
            $agrupador = Examen_Agrupador::all();
        } else {
            $agrupador = Examen_Agrupador_labs::orderBy('secuencia')->get();
        }

        $ucreador = $orden->crea;
        $edad      = Carbon::createFromDate(substr($orden->paciente->fecha_nacimiento, 0, 4), substr($orden->paciente->fecha_nacimiento, 5, 2), substr($orden->paciente->fecha_nacimiento, 8, 2))->age;
        $fechaNacimiento = new DateTime($orden->pfecha_nacimiento);
        
        $diaNacimiento = $fechaNacimiento->format('d');
        $fecha=date('Y-m-d');
        $hora = date('H:i:s');
        $mesNacimiento = $fechaNacimiento->format('m');
        //nuevo
        $fecha_evolucion = $orden->fecha_orden; 
        if($orden->fecha_convenios != null){
            $fecha_evolucion = $orden->fecha_convenios;
        }
        if(!is_null($orden->protocolo)){
            if($orden->protocolo->pre_post == 'POST'){
                $fecha_evolucion = date('Y-m-d', strtotime('-1 day', strtotime($fecha_evolucion)));
            }
            
        }
        $evolucion = Historiaclinica::where('historiaclinica.id_paciente', $orden->id_paciente)
            ->join('hc_evolucion as hc_evo', 'hc_evo.hcid', 'historiaclinica.hcid')
            ->where('hc_evo.secuencia', 0)
            ->whereNotNull('hc_evo.motivo')
            ->whereRaw("DATE(hc_evo.created_at) <= ?", [$fecha_evolucion])
            //->where('hc_evo.created_at','<=',$orden->fecha_orden)
            ->orderby('hc_evo.created_at', 'desc')
            ->select('*')
            ->first();

        $diagnosticos = [];
        if (!is_null($evolucion)) {
            $hc_cie10 = Aud_Hc_Cie10::where('hc_id_procedimiento', $evolucion->hc_id_procedimiento)
                ->groupBy('cie10')->limit(3)->get();
            $descripcion = '';
            $i=0;
            foreach ($hc_cie10 as $value) {
                $c3 = Cie_10_3::find($value->cie10);
                if (!is_null($c3)) {
                    $descripcion = $c3->descripcion;
                }
    
                $c4 = Cie_10_4::find($value->cie10);
                if (!is_null($c4)) {
                    $descripcion = $c4->descripcion;
                }
    
                $diagnosticos[$i]['cie10'] = $value->cie10;
                $diagnosticos[$i]['descripcion'] = $descripcion;
                $i++;
            }
        }
        //fin nuevo

        $vistaurl = "laboratorio.orden.resultados_iess_pdf";
        $view     = View::make($vistaurl, compact('orden', 'pct', 'detalle', 'resultados', 'agrupador', 'parametros', 'edad', 'ucreador', 'user', 'empresa', 'diaNacimiento','mesNacimiento','fecha','hora','diagnosticos'))->render();
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->setPaper('A4', 'portrait');
        $vt_name = '';
        if ($tipo_usuario == 11) {
            $vt_name = 'C_RESULTADOS.pdf';
            return $pdf->stream($vt_name);
        }
	
        return $pdf->stream('formulario10B.pdf', array("Attachment" => false));
    }
}
