<?php

namespace Sis_medico\Http\Controllers\archivo_plano;

use Sis_medico\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\Seguro;
use Sis_medico\Tipo_Seguro;
use Sis_medico\Empresa;
use Sis_medico\Codigo_Dependencia;
use Sis_medico\Codigo_Derivacion;
use Sis_medico\Agenda;
use Sis_medico\Historiaclinica;
use Sis_medico\Paciente;
use Sis_medico\Archivo_Plano_Cabecera;
use Sis_medico\Archivo_Plano_Detalle;
use Sis_medico\Ap_Tipo_Examen;
use Sis_medico\Cie_10_3;
use Sis_medico\Cie_10_4;
use Sis_medico\Convenio;
use Sis_medico\Examen_Orden;
use Sis_medico\ApProcedimiento;
use Sis_medico\ApPlantilla;
use Sis_medico\Ap_Agrupado;
use Sis_medico\Ap_Tipo_Seg;
use Sis_medico\ApPlantillaItem;
use Sis_medico\ApProcedimientoNivel;
use Excel;
use PHPExcel_Style_NumberFormat;
use Sis_medico\hc_procedimientos;
use Sis_medico\Hc_Evolucion;
use Sis_medico\Examen_Nivel;
use Sis_medico\Ap_Interconsulta_Espe;
use Sis_medico\Log_Agenda;
use Sis_medico\LogAuditoriaPublica;
use PHPExcel_Worksheet_Drawing;
use Sis_medico\Aud_Hc_Cie10;
use Sis_medico\Aud_Hc_Procedimientos;
use Sis_medico\User;


class Ap_PlanillaController extends Controller
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
        if (in_array($rolUsuario, array(1, 4, 5, 11,22)) == false) {
            return true;
        }
    }


    public function planilla(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $seguros = Seguro::all();
        $tipo_seguro = Tipo_Seguro::all();
        $nombre = Codigo_Derivacion::where('estado', '1')->get();
        $nombre_cod = Codigo_Dependencia::where('estado', '1')->get();
        $empresa = Empresa::all();

        return view('archivo_plano/planilla/planilla_iess', ['seguros' => $seguros, 'tipo_seguro' => $tipo_seguro, 'empresa' => $empresa, 'nombre' => $nombre, 'nombre_cod' => $nombre_cod]);
    }

    public function planilla_hcid_create($hcid,$id_seguro)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $id_hc_procedimiento = $hcid;
        $id_seguro = $id_seguro;
        $hc_procedimientos = hc_procedimientos::find($id_hc_procedimiento);
        $historia_clinica = Historiaclinica::find($hc_procedimientos->id_hc);
        //dd($historia_clinica);
        $archivo_plano = Archivo_Plano_Cabecera::where('id_hc_procedimimentos', $id_hc_procedimiento)->where('id_seguro', $id_seguro)->where('estado', '1')->first();
        $anio = substr($historia_clinica->agenda->fechaini, 0, 4);
        $mes = substr($historia_clinica->agenda->fechaini, 5, 2);
        $id_nivel = null;
        $convenio = Convenio::where('id_seguro', $id_seguro)->where('id_empresa', $historia_clinica->agenda->id_empresa)->first();
        //$mes_plano = $mes.'-'.$anio;
        //dd($convenio);
        if (!is_null($convenio)) {
            $id_nivel = $convenio->id_nivel;
        }

        $doctor_ap = null;
        $doctor_seleccionado = Aud_Hc_Procedimientos::where('id_procedimientos_org', $id_hc_procedimiento)
            ->where('id_hc', $historia_clinica->hcid)
            ->first();
        if (!is_null($doctor_seleccionado)) {
            $doctor_ap = $doctor_seleccionado->id_doctor_examinador;
        } elseif (!is_null($hc_procedimientos)) {
            $doctor_ap = $hc_procedimientos->id_doctor_examinador;
        }

        if (is_null($archivo_plano)) {
            $parentesco = $this->equivalente_parentesco($historia_clinica->parentesco);
            $arr_ar = [
                'id_paciente' => $historia_clinica->id_paciente,
                'id_hc' => $historia_clinica->hcid,
                'id_hc_procedimimentos' => $id_hc_procedimiento,
                'id_doctor' => $doctor_ap,
                //'id_usuario' => $historia_clinica->id_usuario,
                //'id_empresa' => $historia_clinica->agenda->id_empresa,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
                //'parentesco' => $parentesco,
                //'mes_plano' => '',
                //'id_nivel' => $id_nivel,
                //'id_seguro' => $id_seguro,
            ];
            $id_plano = Archivo_Plano_Cabecera::insertGetId($arr_ar);
            $archivo_plano = Archivo_Plano_Cabecera::find($id_plano);
        }
        $agenda = $historia_clinica->agenda;
        $fecha_desde = $agenda->fechaini;
        $fecha_hasta = $agenda->fechaini;
        $fecha_hasta = strtotime('+3 days', strtotime($fecha_hasta));
        $fecha_hasta = date('Y/m/d', $fecha_hasta);
        $ordenes = Examen_Orden::where('id_paciente',$agenda->id_paciente)-> where('estado', '1')->where('id_seguro',2)->whereBetween('fecha_orden',[$fecha_desde, $fecha_hasta])->get();
        foreach($ordenes as $orden){
            $detalles = $orden->detalles;
            foreach($detalles as $detalle){
                $examen=$detalle->examen;
                if($examen->tarifario == '380004' ){
                    $this->cargar_plano_detalle($archivo_plano, $detalle);
                }
                if($examen->tarifario == '360130' ){
                    $this->cargar_plano_detalle($archivo_plano, $detalle);
                }
                if($examen->tarifario == '270343' ){
                    $this->cargar_plano_detalle($archivo_plano, $detalle);
                }
                if($examen->tarifario == '270344' ){
                    $this->cargar_plano_detalle($archivo_plano, $detalle);
                }
                if($examen->tarifario == '270345' ){
                    $this->cargar_plano_detalle($archivo_plano, $detalle);
                }
                if($examen->tarifario == '270346' ){
                    $this->cargar_plano_detalle($archivo_plano, $detalle);
                }
                if($examen->tarifario == '270347' ){
                    $this->cargar_plano_detalle($archivo_plano, $detalle);
                }
                if($examen->tarifario == '270348' ){
                    $this->cargar_plano_detalle($archivo_plano, $detalle);
                }
                if($examen->tarifario == '270351' ){
                    $this->cargar_plano_detalle($archivo_plano, $detalle);
                }
            }
        }
        $txt_cie10 = null;
        $cie10 = Cie_10_3::find($archivo_plano->cie10);
        if (is_null($cie10)) {
            $cie10 = Cie_10_4::find($archivo_plano->cie10);
            if (!is_null($cie10)) {
                $txt_cie10 = '(' . $archivo_plano->cie10 . ') ' . $cie10->descripcion;
            }
        } else {
            $txt_cie10 = '(' . $archivo_plano->cie10 . ') ' . $cie10->descripcion;
        }
        $seguros = Seguro::where('inactivo', '1')->where('tipo', '1')->orderBy('nombre')->get();
        $tipo_seguros = Tipo_Seguro::where('estado', '1')->orderBy('nombre')->get();
        $codigos_dev = Codigo_Derivacion::where('estado', '1')->get();
        $codigos_dep = Codigo_Dependencia::where('estado', '1')->get();
        $empresas = Empresa::where('estado', '1')->get();
        $seguros_publicos = Seguro::where('inactivo', '1')->where('tipo', '0')->orderBy('nombre')->get();
        $lista = ApPlantilla::orderBy('descripcion', 'ASC')->get();
        $doctores = User::where('id_tipo_usuario', 3)->where('estado', '1')->get();
        return view('archivo_plano/planilla/planilla_paciente', ['doctores' => $doctores, 'archivo_plano' => $archivo_plano, 'seguros' => $seguros, 'tipo_seguros' => $tipo_seguros, 'empresas' => $empresas, 'codigos_dev' => $codigos_dev, 'codigos_dep' => $codigos_dep, 'seguros_publicos' => $seguros_publicos, 'txt_cie10' => $txt_cie10, 'lista' => $lista, 'historia_clinica' => $historia_clinica]);
    }
    public function cargar_plano_detalle($archivo_plano,$detalle){
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $desc = $detalle->examen->nombre_iess;
        if($desc == null){
            $desc = $detalle->examen->nombre;
        }
        $arr = [
            'id_ap_cabecera' => $archivo_plano->id,
            'fecha' => $archivo_plano->fecha_ing,
            'id_detalle_labs' => $detalle->id,
            'id_orden_labs' => $detalle->id_examen_orden,
            'tipo' => 'EX',
            'codigo' => $detalle->examen->tarifario,
            'descripcion' =>  $desc,
            'cantidad' => '1',
            'subtotal' => $detalle->valor,
            'valor' => $detalle->valor,
            'iva' => '0',
            'clasif_porcentaje_msp' => '100',
            'clasificador' => 'SA11-43',
            'total' => $detalle->valor,
            'total_solicitado_usd' => $detalle->valor,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario,
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
        ];
        Archivo_Plano_Detalle::create($arr);     
    }
    public function ap_nuevo_edit( $id_plano ){

        $seguros = Seguro::where('inactivo', '1')->where('tipo', '1')->orderBy('nombre')->get();
        $archivo_plano = Archivo_Plano_Cabecera::find($id_plano);
        if (is_null($archivo_plano)) {
            abort(404);
        }
        if ((int) $archivo_plano->id_seguro === 5) {
            return redirect()->route('archivo_plano.planilla_msp', [
                'hcid' => $archivo_plano->id_hc_procedimimentos,
                'id_seguro' => $archivo_plano->id_seguro,
            ]);
        }
        if (in_array((int) $archivo_plano->id_seguro, [3, 6], true)) {
            return redirect()->route('archivo_plano.planilla_hcid', [
                'hcid' => $archivo_plano->id_hc_procedimimentos,
                'id_seguro' => $archivo_plano->id_seguro,
            ]);
        }
        $tipo_seguros = Tipo_Seguro::where('estado', '1')->orderBy('nombre')->get();
        $empresas = Empresa::where('estado', '1')->get();
        $codigos_dev = Codigo_Derivacion::where('estado', '1')->get();
        $codigos_dep = Codigo_Dependencia::where('estado', '1')->get();
        $txt_cie10 = null;

        $hc_procedimientos = hc_procedimientos::find($archivo_plano->id_hc_procedimimentos);
        $historia_clinica = Historiaclinica::find($hc_procedimientos->id_hc);

        $cie10 = Cie_10_3::find($archivo_plano->cie10);
        if (is_null($cie10)) {
            $cie10 = Cie_10_4::find($archivo_plano->cie10);
            if (!is_null($cie10)) {
                $txt_cie10 = '(' . $archivo_plano->cie10 . ') ' . $cie10->descripcion;
            }
        } else {
            $txt_cie10 = '(' . $archivo_plano->cie10 . ') ' . $cie10->descripcion;
        }

        $lista = ApPlantilla::orderBy('descripcion', 'ASC')->get();
        $seguros_publicos = Seguro::where('inactivo', '1')->where('tipo', '0')->orderBy('nombre')->get();

        LogAuditoriaPublica::guardarLog_visualizar($id_plano, $historia_clinica, $archivo_plano->id_seguro, 'CONSULTA PLANILLAS GENERADAS' );

        $doctores = User::where('id_tipo_usuario', 3)->where('estado', '1')->get();
        $doctor_ap = null;
        $doctor_seleccionado = Aud_Hc_Procedimientos::where('id_procedimientos_org', $archivo_plano->id_hc_procedimimentos)
            ->where('id_hc', $archivo_plano->id_hc)
            ->first();

        if (!is_null($doctor_seleccionado)) {
            $doctor_ap = $doctor_seleccionado->id_doctor_examinador;
        } else {
            $doctor_seleccionado = hc_procedimientos::where('id', $archivo_plano->id_hc_procedimimentos)
                ->where('id_hc', $archivo_plano->id_hc)
                ->first();
            if (!is_null($doctor_seleccionado)) {
                $doctor_ap = $doctor_seleccionado->id_doctor_examinador;
            }
        }

        if (is_null($archivo_plano->id_doctor) && !is_null($doctor_ap)) {
            $archivo_plano->update(['id_doctor' => $doctor_ap]);
            LogAuditoriaPublica::guardarLog_asigna_doctor($archivo_plano->id, 'CREA - ACT CABECERA');
        }

        return view('archivo_plano/planilla/planilla_paciente', ['doctores' => $doctores, 'archivo_plano' => $archivo_plano, 'seguros' => $seguros, 'tipo_seguros' => $tipo_seguros, 'empresas' => $empresas, 'codigos_dev' => $codigos_dev, 'codigos_dep' => $codigos_dep, 'seguros_publicos' => $seguros_publicos, 'txt_cie10' => $txt_cie10, 'lista' => $lista, 'historia_clinica' => $historia_clinica]);
    }

    /***********************************/
    /*****Ingreso de Planilla IESS******/
    /***********************************/
    public function planilla_hcid($id_hc_procedimiento, $id_seguro)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $hc_procedimientos = hc_procedimientos::find($id_hc_procedimiento);
        $historia_clinica = Historiaclinica::find($hc_procedimientos->id_hc);
        //dd($historia_clinica);
        $archivo_plano = Archivo_Plano_Cabecera::where('id_hc_procedimimentos', $id_hc_procedimiento)->where('id_seguro', $id_seguro)->where('estado', '1')->first();
        $anio = substr($historia_clinica->agenda->fechaini, 0, 4);
        $mes = substr($historia_clinica->agenda->fechaini, 5, 2);
        $id_nivel = null;
        $convenio = Convenio::where('id_seguro', $id_seguro)->where('id_empresa', $historia_clinica->agenda->id_empresa)->first();

        $anio = substr($historia_clinica->agenda->fechaini, 0, 4);
        $mes = substr($historia_clinica->agenda->fechaini, 5, 2);
        
        if (!is_null($convenio)) {
            $id_nivel = $convenio->id_nivel;
        }

        $doctor_ap = null;
        $doctor_seleccionado = Aud_Hc_Procedimientos::where('id_procedimientos_org', $id_hc_procedimiento)
            ->where('id_hc', $historia_clinica->hcid)
            ->first();
        if (!is_null($doctor_seleccionado)) {
            $doctor_ap = $doctor_seleccionado->id_doctor_examinador;
        } elseif (!is_null($hc_procedimientos)) {
            $doctor_ap = $hc_procedimientos->id_doctor_examinador;
        }

        if (is_null($archivo_plano)) {
            $parentesco = $this->equivalente_parentesco($historia_clinica->parentesco);
            $arr_ar = [
                'id_paciente' => $historia_clinica->id_paciente,
                'id_hc' => $historia_clinica->hcid,
                'id_hc_procedimimentos' => $id_hc_procedimiento,
                'id_usuario' => $historia_clinica->id_usuario,
                'id_empresa' => $historia_clinica->agenda->id_empresa,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
                'parentesco' => $parentesco,
                'mes_plano'             => $mes.$anio ,
                'id_nivel' => $id_nivel,
                'id_seguro' => $id_seguro,
                'fecha_ing'             => $historia_clinica->agenda->fechaini,
                'fecha_alt'             => $historia_clinica->agenda->fechaini,
                'id_doctor'             => $doctor_ap,
            ];
            $id_plano = Archivo_Plano_Cabecera::insertGetId($arr_ar);

            LogAuditoriaPublica::guardarLog($id_plano, $historia_clinica, $id_seguro, 'FUNCION ANTERIOR');  

            $archivo_plano = Archivo_Plano_Cabecera::find($id_plano);
        }else {
            if (is_null($archivo_plano->id_doctor) && !is_null($doctor_ap)) {
                $archivo_plano->update(['id_doctor' => $doctor_ap]);
                LogAuditoriaPublica::guardarLog_asigna_doctor($archivo_plano->id, 'CREA - ACT CABECERA');
            }
            LogAuditoriaPublica::guardarLog_visualizar($archivo_plano->id, $historia_clinica, $id_seguro, 'FUNCION ANTERIOR');
        }

        $txt_cie10 = null;

        $cie10 = Cie_10_3::find($archivo_plano->cie10);
        if (is_null($cie10)) {
            $cie10 = Cie_10_4::find($archivo_plano->cie10);
            if (!is_null($cie10)) {
                $txt_cie10 = '(' . $archivo_plano->cie10 . ') ' . $cie10->descripcion;
            }
        } else {
            $txt_cie10 = '(' . $archivo_plano->cie10 . ') ' . $cie10->descripcion;
        }

        $seguros = Seguro::where('inactivo', '1')->where('tipo', '1')->orderBy('nombre')->get();
        $tipo_seguros = Tipo_Seguro::where('estado', '1')->orderBy('nombre')->get();
        $codigos_dev = Codigo_Derivacion::where('estado', '1')->get();
        $codigos_dep = Codigo_Dependencia::where('estado', '1')->get();
        $empresas = Empresa::where('estado', '1')->get();
        $seguros_publicos = Seguro::where('inactivo', '1')->where('tipo', '0')->orderBy('nombre')->get();
        $lista = ApPlantilla::orderBy('descripcion', 'ASC')->get();


        $doctores = User::where('id_tipo_usuario', 3)->where('estado', '1')->get();
        return view('archivo_plano/planilla/planilla_paciente', ['doctores' => $doctores, 'archivo_plano' => $archivo_plano, 'seguros' => $seguros, 'tipo_seguros' => $tipo_seguros, 'empresas' => $empresas, 'codigos_dev' => $codigos_dev, 'codigos_dep' => $codigos_dep, 'seguros_publicos' => $seguros_publicos, 'txt_cie10' => $txt_cie10, 'lista' => $lista, 'historia_clinica' => $historia_clinica]);
    }

    /**********************************/
    /*****Ingreso de Planilla MSP******/
    /**********************************/
    public function planilla_msp($id_hc_procedimiento, $id_seguro)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $hc_procedimientos = hc_procedimientos::find($id_hc_procedimiento);
        $historia_clinica = Historiaclinica::find($hc_procedimientos->id_hc);

        $diagn_ingre = [];
        $diagnosticos_ingreso = Aud_Hc_Cie10::where('hc_id_procedimiento', $id_hc_procedimiento)->get();
        foreach ($diagnosticos_ingreso as $diagnostico) {
            if (strtoupper($diagnostico->ingreso_egreso) !== 'INGRESO') {
                continue;
            }
            $cie10_ingreso = Cie_10_3::find($diagnostico->cie10);
            if (is_null($cie10_ingreso)) {
                $cie10_ingreso = Cie_10_4::find($diagnostico->cie10);
            }
            if (!is_null($cie10_ingreso)) {
                $diagn_ingre[] = [
                    'cie10' => $diagnostico->cie10,
                    'id' => $diagnostico->id,
                    'descripcion' => $cie10_ingreso->descripcion,
                    'pre_def' => $diagnostico->presuntivo_definitivo,
                    'ingreso_egreso' => $diagnostico->ingreso_egreso,
                ];
            }
        }
        $texto_procedimiento = DB::table('aud_hc_protocolo as p')
            ->where('id_hc_procedimientos', $id_hc_procedimiento)
            ->first();

        $archivo_plano = Archivo_Plano_Cabecera::where('id_hc_procedimimentos', $id_hc_procedimiento)->where('id_seguro', $id_seguro)->where('estado', '1')->first();
        $anio = substr($historia_clinica->agenda->fechaini, 0, 4);
        $mes = substr($historia_clinica->agenda->fechaini, 5, 2);
        $id_nivel = null;
        $convenio = Convenio::where('id_seguro', $id_seguro)->where('id_empresa', $historia_clinica->agenda->id_empresa)->first();
        //$mes_plano = $mes.'-'.$anio;


        if (!is_null($convenio)) {
            $id_nivel = $convenio->id_nivel;
        }
        if (is_null($archivo_plano)) {
            $parentesco = $this->equivalente_parentesco($historia_clinica->parentesco);
            $arr_ar = [
                'id_paciente' => $historia_clinica->id_paciente,
                'id_hc' => $historia_clinica->hcid,
                'id_hc_procedimimentos' => $id_hc_procedimiento,
                'id_usuario' => $historia_clinica->id_usuario,
                'fecha_ing' => $historia_clinica->agenda->fechaini,
                'fecha_alt' => $historia_clinica->agenda->fechaini,
                'id_tipo_seguro' => 1,
                'id_empresa' => is_null($historia_clinica->agenda->id_empresa) ? session("id_empresa") : $historia_clinica->agenda->id_empresa,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
                'parentesco' => $parentesco,
                'mes_plano' => date('mY'),
                'id_nivel' => $id_nivel,
                'id_seguro' => $id_seguro,
            ];
            $id_plano = Archivo_Plano_Cabecera::insertGetId($arr_ar);
            LogAuditoriaPublica::guardarLog($id_plano, $historia_clinica, $id_seguro,'BOTON MSP'); 
            $archivo_plano = Archivo_Plano_Cabecera::find($id_plano);
        }else{
            LogAuditoriaPublica::guardarLog_visualizar($archivo_plano->id, $historia_clinica, $id_seguro, 'BOTON MSP'); 
        }

        $txt_cie10 = null;

        $cie10 = Cie_10_3::find($archivo_plano->cie10);
        if (is_null($cie10)) {
            $cie10 = Cie_10_4::find($archivo_plano->cie10);
            if (!is_null($cie10)) {
                $txt_cie10 = '(' . $archivo_plano->cie10 . ') ' . $cie10->descripcion;
            }
        } else {
            $txt_cie10 = '(' . $archivo_plano->cie10 . ') ' . $cie10->descripcion;
        }

        $seguros = Seguro::where('inactivo', '1')->where('tipo', '1')->orderBy('nombre')->get();
        $tipo_seguros = Tipo_Seguro::where('estado', '1')->orderBy('nombre')->get();
        $codigos_dev = Codigo_Derivacion::where('estado', '1')->get();
        $codigos_dep = Codigo_Dependencia::where('estado', '1')->get();
        $empresas = Empresa::where('estado', '1')->get();
        $seguros_publicos = Seguro::where('inactivo', '1')->where('tipo', '0')->orderBy('nombre')->get();
        $lista = ApPlantilla::orderBy('descripcion', 'ASC')->get();

        $agenda = $historia_clinica->agenda;

        return view('archivo_plano/planilla/planilla_msp', [
            'diagn_ingre' => $diagn_ingre,
            'texto_procedimiento' => $texto_procedimiento,
            'agenda' => $agenda,
            'archivo_plano' => $archivo_plano,
            'seguros' => $seguros,
            'tipo_seguros' => $tipo_seguros,
            'empresas' => $empresas,
            'codigos_dev' => $codigos_dev,
            'codigos_dep' => $codigos_dep,
            'seguros_publicos' => $seguros_publicos,
            'txt_cie10' => $txt_cie10,
            'lista' => $lista,
        ]);
    }


    public function equivalente_parentesco($parentesco_siam)
    {

        if ($parentesco_siam == 'Principal') {
            return "TITULAR";
        } elseif ($parentesco_siam == 'Conyugue') {
            return "CONYUGE";
        } elseif ($parentesco_siam == 'Hijo(a)') {
            return "HIJO/HIJA";
        } else {
            return "PARIENTE";
        }
    }

    public function guardar(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $archivo_plano = Archivo_Plano_Cabecera::find($request->id_archivo_plano);

        if (is_null($archivo_plano)) {
            return response()->json(['estado' => 'error', 'mensaje' => 'Planilla no encontrada'], 404);
        }

        if ((int) $archivo_plano->id_seguro === 5) {
            $codigo_msp = trim((string) $request->input('code_derivacion', $archivo_plano->cod_deriva_msp));
            $partes_codigo = array_pad(explode('-', $codigo_msp, 3), 3, '');
            $request->merge([
                'hc_iess' => $request->input('hc_iess', $archivo_plano->id_dig_pac),
                'derivacion_cv' => $partes_codigo[0],
                'derivacion_num_caso' => $partes_codigo[1],
                'derivacion_secuencial' => $partes_codigo[2],
            ]);

            return $this->guardar_planilla_msp($request);
        }

        $fech_ing  = substr($request['fecha_ing'], 6, 4) . '-' . substr($request['fecha_ing'], 3, 2) . '-' . substr($request['fecha_ing'], 0, 2);
        $fech_alt  = substr($request['fecha_alt'], 6, 4) . '-' . substr($request['fecha_alt'], 3, 2) . '-' . substr($request['fecha_alt'], 0, 2);
        //$fech_ing  = date('d-m-Y',strtotime($request['fecha_ing']));
        //$fech_alt  = date('d-m-Y',strtotime($request['fecha_alt']));

        //$fech_ing  = $request['fecha_ing'];
        //$fech_alt  = $request['fecha_alt'];

        //Verificamos Cierre en la Tabla ap_agrupado
        $empr = $request['id_empresa'];
        $mes_plan = $request['mes_plano'];
        $id_tip_seguro = $request['id_tipo_seguro'];

        $verif_exist_agrup = Ap_Agrupado::where('ap_agrupado.mes_plano', $mes_plan)
            ->where('ap_agrupado.empresa', $empr)
            ->where('ap_agrupado.id_tipo_seg', $id_tip_seguro)
            ->where('ap_agrupado.seguro', '2')
            ->first();

        if (!is_null($verif_exist_agrup)) {

            return "existe";
        } else {

            $hc_proc = hc_procedimientos::find($archivo_plano->id_hc_procedimimentos);

            $hc_proc->update(['id_empresa' => $request['id_empresa']]);

            $arr = [
                //'hc_iess' => $request['hc_iess'],
                //'id_paciente' => $request['cedula'], cagada
                'id_usuario' => $request['cedula'],
                'nombres' => $request['nombre'],
                'id_dig_pac' => $request['hc_iess'],
                'estado' => $request['estado'],
                'parentesco' => $request['parentesco'],
                'fecha_ing' => $fech_ing,
                'fecha_alt' => $fech_alt,
                'id_tipo_seguro' => $request['id_tipo_seguro'],
                'id_seguro_priv' => $request['id_seguro_priv'],
                'id_cobertura_comp' => $request['id_cobertura_comp'],
                'cie10' => $request['codigo'],
                'mes_plano' => $request['mes_plano'],
                'id_empresa' => $request['id_empresa'],
                'id_cod_deriva' => $request['id_cod_deriva'],
                'presuntivo_def' => $request['presuntivo_def'],
                'id_cod_dep' => $request['id_cod_dep'],
                'nom_planilla' => $request['nom_planilla'],
                'nom_procedimiento' => $request['nom_procedimiento'],
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod' => $idusuario,
                'codigo_iess' =>  $request['code_derivacion'],
                'fecha_autorizacion' => $request['fecha_autorizacion'],
                'id_doctor' => $request->input('doctor_archivo', $archivo_plano->id_doctor),
            ];

            $archivo_plano->update($arr);

            LogAuditoriaPublica::guardarLog_actualizar($archivo_plano->id, $archivo_plano->id_hc, $archivo_plano->id_seguro, 'FUNCION ACTUALIZA CABECERA', $archivo_plano->nom_procedimiento, $archivo_plano->cie10, $archivo_plano->estado, $archivo_plano->codigo_iess,$archivo_plano->fecha_autorizacion);

            $agenda = $hc_proc->historia->agenda;

            Log_agenda::create([
                'id_agenda'          => $agenda->id,
                'descripcion'        => 'CODIGO VALIDACION '.$request['code_derivacion'],
                'descripcion2'       => 'PLANO ID '.$archivo_plano->id,
                'descripcion3'       => 'CODIGO VALIDACION ANTERIOR '.$agenda->cod_val,
       
                'id_usuariomod'      => $idusuario,
                'id_usuariocrea'     => $idusuario,
                'ip_modificacion'    => $ip_cliente,
                'ip_creacion'        => $ip_cliente,
            ]);

            //$agenda->update(['cod_val' => $request['code_derivacion']]);

            $detalles = $archivo_plano->detalles;
            foreach ($detalles as $detalle) {
                $detalle->update(['fecha' => $fech_ing ]);
            }

            return "ok";
        }
    }

    /**********************************/
    /*******Guardar Planilla MSP*******/
    /**********************************/
    public function guardar_planilla_msp(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $archivo_plano = Archivo_Plano_Cabecera::find($request->id_archivo_plano);

        $cv = $request['derivacion_cv'];
        $nc = $request['derivacion_num_caso'];
        $sec = $request['derivacion_secuencial'];
        $cod_der_msp = $cv . '-' . $nc . '-' . $sec;

        //$fech_ing  = date('Y-m-d',strtotime($request['fecha_ing']));
        //$fech_alt  = date('Y-m-d',strtotime($request['fecha_alt']));

        $fech_ing  = substr($request['fecha_ing'], 6, 4) . '-' . substr($request['fecha_ing'], 3, 2) . '-' . substr($request['fecha_ing'], 0, 2);
        $fech_alt  = substr($request['fecha_alt'], 6, 4) . '-' . substr($request['fecha_alt'], 3, 2) . '-' . substr($request['fecha_alt'], 0, 2);



        $empr = $request['id_empresa'];
        $mes_plan = $request['mes_plano'];


        $verif_exist_agrup = Ap_Agrupado::where('ap_agrupado.mes_plano', $mes_plan)
            ->where('ap_agrupado.empresa', $empr)
            ->where('ap_agrupado.id_tipo_seg', '1')
            ->where('ap_agrupado.seguro', '5')
            ->first();

        if (!is_null($verif_exist_agrup)) {

            return "existe";
        } else {

            $arr = [

                'id_dig_pac' => $request['hc_iess'],
                'estado' => $request['estado'],
                'fecha_ing' => $fech_ing,
                'fecha_alt' => $fech_alt,
                'cie10' => $request['codigo'],
                'mes_plano' => $request['mes_plano'],
                'nombres' => $request['nombre'],
                'id_empresa' => $request['id_empresa'],
                'derivacion_cv_msp' => $request['derivacion_cv'],
                'derivacion_nc_msp' => $request['derivacion_num_caso'],
                'derivacion_sec_msp' => $request['derivacion_secuencial'],
                'cod_deriva_msp' => $cod_der_msp,
                'presuntivo_def' => $request['presuntivo_def'],
                //'nom_planilla' => $request['nom_planilla'],
                'nom_procedimiento' => $request['nom_procedimiento'],
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod' => $idusuario,
                'id_doctor' => $request->input('doctor_archivo', $archivo_plano->id_doctor),

            ];

            $archivo_plano->update($arr);

            $detalles = $archivo_plano->detalles;
            foreach ($detalles as $detalle) {
                $detalle->update(['fecha' => $fech_ing ]);
            }

            return "ok";
        }
    }

    public function auto(Request $request)
    {
        $id_paciente = $request['term'];
        $data             = null;
        $nuevo_nombre     = explode(' ', $id_paciente);
        $seteo            = "%";

        foreach ($nuevo_nombre as $value) {
            $seteo = $seteo . $value . '%';
        }
        $query = "SELECT id
        FROM `paciente`
        WHERE paciente.id like '" . $seteo . "' ";
        $nombres = DB::select($query);
        foreach ($nombres as $product) {
            $data[] = array('value' => $product->id);
        }

        if (count($data)) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }
        return 'error';
    }

    //AUTOCOMPLETA LOS DATOS DE FILIACION
    public function auto2(Request $request)
    {
        $id_paciente = $request['id_paciente'];
        $data  = null;
        $nuevo_nombre = explode(' ', $id_paciente);
        $seteo        = "%";
        foreach ($nuevo_nombre as $value) {
            $seteo = $seteo . $value . '%';
        }
        //aqui en el select trae los campos que necesites
        $query = "SELECT nombre1, nombre2, apellido1, apellido2, telefono1, telefono2, id_seguro, sexo, id, estadocivil, cedulafamiliar, nombre1familiar, nombre2familiar, apellido1familiar, apellido2familiar, religion,fecha_nacimiento,trabajo,lugar_nacimiento,alergias,ciudad,gruposanguineo,direccion,antecedentes_pat, antecedentes_fam, hc.hcid
                  FROM paciente, historiaclinica
                  JOIN historiaclinica as hc ON hc.id_paciente = paciente.id
                  WHERE paciente.id like '" . $seteo . "'";
        $nombres = DB::select($query);
        //luego los pones aqui con el nombre que quieras
        foreach ($nombres as $product) {
            $data[] = array(
                'nombre1' => $product->nombre1, 'nombre2' => $product->nombre2, 'apellido1' => $product->apellido1, 'apellido2' => $product->apellido2,
                'fecha' => $product->fecha_nacimiento, 'telefono1' => $product->telefono1, 'telefono2' => $product->telefono2,
                'seguro' => $product->id_seguro, 'sexo' => $product->sexo, 'id' => $product->id, 'estadoc' => $product->estadocivil, 'cedula' => $product->cedulafamiliar, 'nombre1familiar' => $product->nombre1familiar, 'nombre2familiar' => $product->nombre2familiar, 'apellido1familiar' => $product->apellido1familiar, 'apellido2familiar' => $product->apellido2familiar, 'hcid' => $hcid,
                'religion' => $product->religion, 'alergia' => $product->alergias, 'lugar_nacimiento' => $product->lugar_nacimiento, 'ciudad' => $product->ciudad,
                'grupos' => $product->gruposanguineo, 'direccion' => $product->direccion, 'antp' => $product->antecedentes_pat, 'antf' => $product->antecedentes_fam
            );
        }
        //ahora le enviamos todo esto por ajax nos toca setearlo en la vista :)
        if (count($data)) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }
        return $data;
    }

    public function busca_ordenes_labs($cabecera)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $ap = Archivo_Plano_Cabecera::find($cabecera);

        //dd($ordenes);
        $seguro = Seguro::find($ap->id_seguro);
        $fecha_hasta = date('Y/m/d');
        $fecha = strtotime('-3 month', strtotime($fecha_hasta));
        $fecha = date('Y/m/d', $fecha);
       // dd($ap->id_seguro);
        $ordenes = Examen_Orden::where('id_paciente', $ap->id_paciente)->where('id_seguro', $ap->id_seguro)->where('estado', '1')->where('realizado', '1')->wherebetween('fecha_orden', [$fecha . '  0:00:00', $fecha_hasta . ' 23:59:59'])->get();
       // dd($ordenes);

        return view('archivo_plano/planilla/laboratorio', ['ordenes' => $ordenes, 'seguro' => $seguro, 'fecha' => $fecha, 'fecha_hasta' => $fecha_hasta, 'ap' => $ap]);
    }

    public function buscar_labs(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $ap = Archivo_Plano_Cabecera::find($request->id);

        //dd($ordenes);
        $seguro = Seguro::find($ap->id_seguro);
        $fecha_hasta = $request->fecha_hasta;
        $fecha = $request->fecha;
        $ordenes = Examen_Orden::where('id_paciente', $ap->id_paciente)->where('id_seguro', $ap->id_seguro)->where('estado', '1')->where('realizado', '1');
        if ($fecha != null) {
            $ordenes = $ordenes->wherebetween('fecha_orden', [$fecha . '  0:00:00', $fecha_hasta . ' 23:59:59']);
        }

        $ordenes = $ordenes->get();
        //return "ok";

        return view('archivo_plano/planilla/listado', ['ordenes' => $ordenes, 'seguro' => $seguro, 'fecha' => $fecha, 'fecha_hasta' => $fecha_hasta, 'ap' => $ap]);
    }

    public function mostrar_detalle($cabecera, $idseguro)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        //$ap = Archivo_Plano_Cabecera::find($cabecera);
        //$detalles = $ap->detalles;

        $detalles = Archivo_Plano_Detalle::where('id_ap_cabecera', $cabecera)
            ->where('estado', '1')->get();


        return view('archivo_plano/planilla/detalle', ['detalles' => $detalles, 'idseguro' => $idseguro]);
    }

    public function ingresa_ordenes_labs($orden, $cabecera)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $orden = Examen_Orden::find($orden);

        //Traer el nivel y la empresa y Guadarla en detalle
        /*$nivel =  $orden->id_nivel;
        $empresa =  $orden->id_empresa;*/

        $detalles = $orden->detalles;
        $apcabecera = Archivo_Plano_Cabecera::find($cabecera);
        if ($orden->anio == '2021' && $orden->mes < '03') {
            $covid = $orden->detalles->where('id_examen', '1191');
            if ($covid->count() > 0) {
                $nivel = $orden->id_nivel;
                $detalles = $orden->detalles;
                foreach ($detalles as $value) {
                    if ($value->id_examen == '1191') {
                        $ex_nivel = Examen_Nivel::where('id_examen', '1191')->where('nivel', $nivel)->first();
                        $input_det = [
                            'valor'  => $ex_nivel->valor1,
                        ];
                        $value->update($input_det);
                        //dd($ex_nivel->valor1);
                    }
                }

                $orden2 = Examen_Orden::find($orden->id);
                $valor2 = $orden2->detalles->sum('valor');
                $input_ex = [
                    'total_valor'     => $valor2,
                    'valor'           => $valor2,
                ];
                $orden2->update($input_ex);
            }
        }
        //$nivel = $orden->id_nivel;
        $nivel = $apcabecera->id_nivel;
        foreach ($detalles as $value) {
            if ($value->id_examen == '1191' || $value->id_examen == '1225') {
                //IGG
                $codigo = '270051';
                $valor = 0;

                $ap_proc_nivel = ApProcedimientoNivel::where('codigo', $codigo)->where('cod_conv', $nivel)->first();
                if (!is_null($ap_proc_nivel)) {
                    $valor = $ap_proc_nivel->uvr1 * $ap_proc_nivel->prc1;
                    //$valor = round($valor,4);
                    if ($apcabecera->id_seguro == '5') {
                        $valor = round($valor, 2);
                    } else {
                        $valor = round($valor, 4);
                    }
                }

                $arr1 = [
                    'id_ap_cabecera' => $cabecera,
                    'fecha' => $apcabecera->fecha_ing,
                    'id_detalle_labs' => $value->id,
                    'id_orden_labs' => $orden->id,
                    'tipo' => 'EX',
                    'codigo' => $codigo,
                    'descripcion' =>  'IGG',
                    'cantidad' => '1',
                    'subtotal' => $valor,
                    'valor' => $valor,
                    'iva' => '0',
                    'valor_unitario' => $valor,
                    'clasif_porcentaje_msp' => '100',
                    'clasificador' => 'SA11-43',
                    'total' => $valor,
                    'total_solicitado_usd' => $valor,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                ];
                Archivo_Plano_Detalle::create($arr1);

                //IGM
                $codigo = '270052';
                $valor = 0;

                $ap_proc_nivel = ApProcedimientoNivel::where('codigo', $codigo)->where('cod_conv', $nivel)->first();
                if (!is_null($ap_proc_nivel)) {
                    $valor = $ap_proc_nivel->uvr1 * $ap_proc_nivel->prc1;
                    //$valor = round($valor,4);
                    if ($apcabecera->id_seguro == '5') {
                        $valor = round($valor, 2);
                    } else {
                        $valor = round($valor, 4);
                    }
                }

                $arr2 = [
                    'id_ap_cabecera' => $cabecera,
                    'fecha' => $apcabecera->fecha_ing,
                    'id_detalle_labs' => $value->id,
                    'id_orden_labs' => $orden->id,
                    'tipo' => 'EX',
                    'codigo' => $codigo,
                    'descripcion' =>  'IGM',
                    'cantidad' => '1',
                    'subtotal' => $valor,
                    'valor' => $valor,
                    'iva' => '0',
                    'valor_unitario' => $valor,
                    'clasif_porcentaje_msp' => '100',
                    'clasificador' => 'SA11-43',
                    'total' => $valor,
                    'total_solicitado_usd' => $valor,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                ];
                Archivo_Plano_Detalle::create($arr2);
            } else if ($value->id_examen == '639') { //INTOLERANCIA ALIMENTARIA

                $codigo = '380004';
                $valor = 0;

                $ap_proc_nivel = ApProcedimientoNivel::where('codigo', $codigo)->where('cod_conv', $nivel)->first();
                if (!is_null($ap_proc_nivel)) {
                    $valor = $ap_proc_nivel->uvr1 * $ap_proc_nivel->prc1;
                    //$valor = round($valor,4);
                    if ($apcabecera->id_seguro == '5') {
                        $valor = round($valor, 2);
                    } else {
                        $valor = round($valor, 4);
                    }
                }

                $lactosa = [
                    'id_ap_cabecera' => $cabecera,
                    //'fecha' => $orden->fecha_orden,
                    'fecha' => $apcabecera->fecha_ing,
                    'id_detalle_labs' => $value->id,
                    'id_orden_labs' => $orden->id,
                    'tipo' => 'EX',
                    'codigo' => $codigo,
                    'descripcion' =>  'CURVA DE LACTOSA',
                    'cantidad' => '1',
                    'subtotal' => $valor,
                    'valor' => $valor,
                    'iva' => '0',
                    'valor_unitario' => $valor,
                    'clasif_porcentaje_msp' => '100',
                    'clasificador' => 'SA11-43',
                    'total' => $valor,
                    'total_solicitado_usd' => $valor,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                ];
                Archivo_Plano_Detalle::create($lactosa);


                $codigo = '360130';
                $valor = 0;

                $ap_proc_nivel = ApProcedimientoNivel::where('codigo', $codigo)->where('cod_conv', $nivel)->first();
                if (!is_null($ap_proc_nivel)) {
                    $valor = $ap_proc_nivel->uvr1 * $ap_proc_nivel->prc1;
                    //$valor = round($valor,4);
                    if ($apcabecera->id_seguro == '5') {
                        $valor = round($valor, 2);
                    } else {
                        $valor = round($valor, 4);
                    }
                }

                $fructosamina = [
                    'id_ap_cabecera' => $cabecera,
                    //'fecha' => $orden->fecha_orden,
                    'fecha' => $apcabecera->fecha_ing,
                    'id_detalle_labs' => $value->id,
                    'id_orden_labs' => $orden->id,
                    'tipo' => 'EX',
                    'codigo' => $codigo,
                    'descripcion' =>  'FRUCTOSAMINA',
                    'cantidad' => '1',
                    'subtotal' => $valor,
                    'valor' => $valor,
                    'iva' => '0',
                    'valor_unitario' => $valor,
                    'clasif_porcentaje_msp' => '100',
                    'clasificador' => 'SA11-43',
                    'total' => $valor,
                    'total_solicitado_usd' => $valor,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                ];
                Archivo_Plano_Detalle::create($fructosamina);

                $codigo = '270343';
                $valor = 0;

                $ap_proc_nivel = ApProcedimientoNivel::where('codigo', $codigo)->where('cod_conv', $nivel)->first();
                if (!is_null($ap_proc_nivel)) {
                    $valor = $ap_proc_nivel->uvr1 * $ap_proc_nivel->prc1;
                    $valor = round($valor, 4);
                    //$valor = round($valor,4);
                    if ($apcabecera->id_seguro == '5') {
                        $valor = round($valor, 2);
                    } else {
                        $valor = round($valor, 4);
                    }
                }

                $clara = [
                    'id_ap_cabecera' => $cabecera,
                    //'fecha' => $orden->fecha_orden,
                    'fecha' => $apcabecera->fecha_ing,
                    'id_detalle_labs' => $value->id,
                    'id_orden_labs' => $orden->id,
                    'tipo' => 'EX',
                    'codigo' => $codigo,
                    'descripcion' =>  'PRUEBA ALERGIA CLARA',
                    'cantidad' => '1',
                    'subtotal' => $valor,
                    'valor' => $valor,
                    'iva' => '0',
                    'valor_unitario' => $valor,
                    'clasif_porcentaje_msp' => '100',
                    'clasificador' => 'SA11-43',
                    'total' => $valor,
                    'total_solicitado_usd' => $valor,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                ];
                Archivo_Plano_Detalle::create($clara);

                $codigo = '270344';
                $valor = 0;

                $ap_proc_nivel = ApProcedimientoNivel::where('codigo', $codigo)->where('cod_conv', $nivel)->first();
                if (!is_null($ap_proc_nivel)) {
                    $valor = $ap_proc_nivel->uvr1 * $ap_proc_nivel->prc1;
                    //$valor = round($valor,4);
                    if ($apcabecera->id_seguro == '5') {
                        $valor = round($valor, 2);
                    } else {
                        $valor = round($valor, 4);
                    }
                }

                $especifica = [
                    'id_ap_cabecera' => $cabecera,
                    //'fecha' => $orden->fecha_orden,
                    'fecha' => $apcabecera->fecha_ing,
                    'id_detalle_labs' => $value->id,
                    'id_orden_labs' => $orden->id,
                    'tipo' => 'EX',
                    'codigo' => $codigo,
                    'descripcion' =>  'PRUEBA ALERGIA ESPECIFICA',
                    'cantidad' => '1',
                    'subtotal' => $valor,
                    'valor' => $valor,
                    'iva' => '0',
                    'valor_unitario' => $valor,
                    'clasif_porcentaje_msp' => '100',
                    'clasificador' => 'SA11-43',
                    'total' => $valor,
                    'total_solicitado_usd' => $valor,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                ];
                Archivo_Plano_Detalle::create($especifica);

                $codigo = '270345';
                $valor = 0;

                $ap_proc_nivel = ApProcedimientoNivel::where('codigo', $codigo)->where('cod_conv', $nivel)->first();
                if (!is_null($ap_proc_nivel)) {
                    $valor = $ap_proc_nivel->uvr1 * $ap_proc_nivel->prc1;
                    //$valor = round($valor,4);
                    if ($apcabecera->id_seguro == '5') {
                        $valor = round($valor, 2);
                    } else {
                        $valor = round($valor, 4);
                    }
                }

                $fresa = [
                    'id_ap_cabecera' => $cabecera,
                    //'fecha' => $orden->fecha_orden,
                    'fecha' => $apcabecera->fecha_ing,
                    'id_detalle_labs' => $value->id,
                    'id_orden_labs' => $orden->id,
                    'tipo' => 'EX',
                    'codigo' => $codigo,
                    'descripcion' =>  'PRUEBA ALERGIA FRESA O FRUTILLA',
                    'cantidad' => '1',
                    'subtotal' => $valor,
                    'valor' => $valor,
                    'iva' => '0',
                    'valor_unitario' => $valor,
                    'clasif_porcentaje_msp' => '100',
                    'clasificador' => 'SA11-43',
                    'total' => $valor,
                    'total_solicitado_usd' => $valor,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                ];
                Archivo_Plano_Detalle::create($fresa);

                $codigo = '270346';
                $valor = 0;

                $ap_proc_nivel = ApProcedimientoNivel::where('codigo', $codigo)->where('cod_conv', $nivel)->first();
                if (!is_null($ap_proc_nivel)) {
                    $valor = $ap_proc_nivel->uvr1 * $ap_proc_nivel->prc1;
                    //$valor = round($valor,4);
                    if ($apcabecera->id_seguro == '5') {
                        $valor = round($valor, 2);
                    } else {
                        $valor = round($valor, 4);
                    }
                }

                $leche = [
                    'id_ap_cabecera' => $cabecera,
                    //'fecha' => $orden->fecha_orden,
                    'fecha' => $apcabecera->fecha_ing,
                    'id_detalle_labs' => $value->id,
                    'id_orden_labs' => $orden->id,
                    'tipo' => 'EX',
                    'codigo' => $codigo,
                    'descripcion' =>  'PRUEBA ALERGIA LECHE',
                    'cantidad' => '1',
                    'subtotal' => $valor,
                    'valor' => $valor,
                    'iva' => '0',
                    'valor_unitario' => $valor,
                    'clasif_porcentaje_msp' => '100',
                    'clasificador' => 'SA11-43',
                    'total' => $valor,
                    'total_solicitado_usd' => $valor,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                ];
                Archivo_Plano_Detalle::create($leche);

                $codigo = '270347';
                $valor = 0;

                $ap_proc_nivel = ApProcedimientoNivel::where('codigo', $codigo)->where('cod_conv', $nivel)->first();
                if (!is_null($ap_proc_nivel)) {
                    $valor = $ap_proc_nivel->uvr1 * $ap_proc_nivel->prc1;
                    //$valor = round($valor,4);
                    if ($apcabecera->id_seguro == '5') {
                        $valor = round($valor, 2);
                    } else {
                        $valor = round($valor, 4);
                    }
                }

                $mani = [
                    'id_ap_cabecera' => $cabecera,
                    //'fecha' => $orden->fecha_orden,
                    'fecha' => $apcabecera->fecha_ing,
                    'id_detalle_labs' => $value->id,
                    'id_orden_labs' => $orden->id,
                    'tipo' => 'EX',
                    'codigo' => $codigo,
                    'descripcion' =>  'PRUEBA ALERGIA MANI',
                    'cantidad' => '1',
                    'subtotal' => $valor,
                    'valor' => $valor,
                    'iva' => '0',
                    'valor_unitario' => $valor,
                    'clasif_porcentaje_msp' => '100',
                    'clasificador' => 'SA11-43',
                    'total' => $valor,
                    'total_solicitado_usd' => $valor,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                ];
                Archivo_Plano_Detalle::create($mani);

                $codigo = '270348';
                $valor = 0;

                $ap_proc_nivel = ApProcedimientoNivel::where('codigo', $codigo)->where('cod_conv', $nivel)->first();
                if (!is_null($ap_proc_nivel)) {
                    $valor = $ap_proc_nivel->uvr1 * $ap_proc_nivel->prc1;
                    //$valor = round($valor,4);
                    if ($apcabecera->id_seguro == '5') {
                        $valor = round($valor, 2);
                    } else {
                        $valor = round($valor, 4);
                    }
                }

                $naranja = [
                    'id_ap_cabecera' => $cabecera,
                    //'fecha' => $orden->fecha_orden,
                    'fecha' => $apcabecera->fecha_ing,
                    'id_detalle_labs' => $value->id,
                    'id_orden_labs' => $orden->id,
                    'tipo' => 'EX',
                    'codigo' => $codigo,
                    'descripcion' =>  'PRUEBA ALERGIA NARANJA',
                    'cantidad' => '1',
                    'subtotal' => $valor,
                    'valor' => $valor,
                    'iva' => '0',
                    'valor_unitario' => $valor,
                    'clasif_porcentaje_msp' => '100',
                    'clasificador' => 'SA11-43',
                    'total' => $valor,
                    'total_solicitado_usd' => $valor,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                ];
                Archivo_Plano_Detalle::create($naranja);

                $codigo = '270351';
                $valor = 0;

                $ap_proc_nivel = ApProcedimientoNivel::where('codigo', $codigo)->where('cod_conv', $nivel)->first();
                if (!is_null($ap_proc_nivel)) {
                    $valor = $ap_proc_nivel->uvr1 * $ap_proc_nivel->prc1;
                    //$valor = round($valor,4);
                    if ($apcabecera->id_seguro == '5') {
                        $valor = round($valor, 2);
                    } else {
                        $valor = round($valor, 4);
                    }
                }

                $yema = [
                    'id_ap_cabecera' => $cabecera,
                    //'fecha' => $orden->fecha_orden,
                    'fecha' => $apcabecera->fecha_ing,
                    'id_detalle_labs' => $value->id,
                    'id_orden_labs' => $orden->id,
                    'tipo' => 'EX',
                    'codigo' => $codigo,
                    'descripcion' =>  'PRUEBA ALERGIA YEMA',
                    'cantidad' => '1',
                    'subtotal' => $valor,
                    'valor' => $valor,
                    'iva' => '0',
                    'valor_unitario' => $valor,
                    'clasif_porcentaje_msp' => '100',
                    'clasificador' => 'SA11-43',
                    'total' => $valor,
                    'total_solicitado_usd' => $valor,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                ];
                Archivo_Plano_Detalle::create($yema);
            } else if ($value->id_examen == '632') {


                $codigo = '78267';
                $valor = 0;

                $ap_proc_nivel = ApProcedimientoNivel::where('codigo', $codigo)->where('cod_conv', $nivel)->first();
                if (!is_null($ap_proc_nivel)) {
                    $valor = $ap_proc_nivel->uvr1 * $ap_proc_nivel->prc1;
                    //$valor = round($valor,4);
                    if ($apcabecera->id_seguro == '5') {
                        $valor = round($valor, 2);
                    } else {
                        $valor = round($valor, 4);
                    }
                }

                $adquisicion = [
                    'id_ap_cabecera' => $cabecera,
                    //'fecha' => $orden->fecha_orden,
                    'fecha' => $apcabecera->fecha_ing,
                    'id_detalle_labs' => $value->id,
                    'id_orden_labs' => $orden->id,
                    'tipo' => 'EX',
                    'codigo' => $codigo,
                    'descripcion' =>  'TEST DE UREA EN ALIENTO, C-14 (ISOTÓPICO), ADQUISICIÓN PARA ANÁLISIS',
                    'cantidad' => '1',
                    'subtotal' => $valor,
                    'valor' => $valor,
                    'iva' => '0',
                    'valor_unitario' => $valor,
                    'clasif_porcentaje_msp' => '100',
                    'clasificador' => 'SA11-43',
                    'total' => $valor,
                    'total_solicitado_usd' => $valor,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                ];
                Archivo_Plano_Detalle::create($adquisicion);

                $codigo = '78268';
                $valor = 0;

                $ap_proc_nivel = ApProcedimientoNivel::where('codigo', $codigo)->where('cod_conv', $nivel)->first();
                if (!is_null($ap_proc_nivel)) {
                    $valor = $ap_proc_nivel->uvr1 * $ap_proc_nivel->prc1;
                    //$valor = round($valor,4);
                    if ($apcabecera->id_seguro == '5') {
                        $valor = round($valor, 2);
                    } else {
                        $valor = round($valor, 4);
                    }
                }

                $analisis = [
                    'id_ap_cabecera' => $cabecera,
                    //'fecha' => $orden->fecha_orden,
                    'fecha' => $apcabecera->fecha_ing,
                    'id_detalle_labs' => $value->id,
                    'id_orden_labs' => $orden->id,
                    'tipo' => 'EX',
                    'codigo' => $codigo,
                    'descripcion' =>  'TEST DE UREA EN ALIENTO, C-14 (ISOTÓPICO), ANÁLISIS',
                    'cantidad' => '1',
                    'subtotal' => $valor,
                    'valor' => $valor,
                    'iva' => '0',
                    'valor_unitario' => $valor,
                    'clasif_porcentaje_msp' => '100',
                    'clasificador' => 'SA11-43',
                    'total' => $valor,
                    'total_solicitado_usd' => $valor,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                ];
                Archivo_Plano_Detalle::create($analisis);

                $codigo = '270287';
                $valor = 0;

                $ap_proc_nivel = ApProcedimientoNivel::where('codigo', $codigo)->where('cod_conv', $nivel)->first();
                if (!is_null($ap_proc_nivel)) {
                    $valor = $ap_proc_nivel->uvr1 * $ap_proc_nivel->prc1;
                    //$valor = round($valor,4);
                    if ($apcabecera->id_seguro == '5') {
                        $valor = round($valor, 2);
                    } else {
                        $valor = round($valor, 4);
                    }
                }

                $igg = [
                    'id_ap_cabecera' => $cabecera,
                    //'fecha' => $orden->fecha_orden,
                    'fecha' => $apcabecera->fecha_ing,
                    'id_detalle_labs' => $value->id,
                    'id_orden_labs' => $orden->id,
                    'tipo' => 'EX',
                    'codigo' => $codigo,
                    'descripcion' =>  'HELICOBACTER PYL.IGG',
                    'cantidad' => '1',
                    'subtotal' => $valor,
                    'valor' => $valor,
                    'iva' => '0',
                    'valor_unitario' => $valor,
                    'clasif_porcentaje_msp' => '100',
                    'clasificador' => 'SA11-43',
                    'total' => $valor,
                    'total_solicitado_usd' => $valor,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                ];
                Archivo_Plano_Detalle::create($igg);
            } else if ($value->id_examen == '1231') {

                $codigo = '380004';
                $valor = 0;

                $ap_proc_nivel = ApProcedimientoNivel::where('codigo', $codigo)->where('cod_conv', $nivel)->first();
                if (!is_null($ap_proc_nivel)) {
                    $valor = $ap_proc_nivel->uvr1 * $ap_proc_nivel->prc1;
                    //$valor = round($valor,4);
                    if ($apcabecera->id_seguro == '5') {
                        $valor = round($valor, 2);
                    } else {
                        $valor = round($valor, 4);
                    }
                }

                $curva_lac = [
                    'id_ap_cabecera' => $cabecera,
                    //'fecha' => $orden->fecha_orden,
                    'fecha' => $apcabecera->fecha_ing,
                    'id_detalle_labs' => $value->id,
                    'id_orden_labs' => $orden->id,
                    'tipo' => 'EX',
                    'codigo' => $codigo,
                    'descripcion' =>  'CURVA DE LACTOSA',
                    'cantidad' => '1',
                    'subtotal' => $valor,
                    'valor' => $valor,
                    'iva' => '0',
                    'valor_unitario' => $valor,
                    'clasif_porcentaje_msp' => '100',
                    'clasificador' => 'SA11-43',
                    'total' => $valor,
                    'total_solicitado_usd' => $valor,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                ];
                Archivo_Plano_Detalle::create($curva_lac);

                $codigo = '270346';
                $valor = 0;

                $ap_proc_nivel = ApProcedimientoNivel::where('codigo', $codigo)->where('cod_conv', $nivel)->first();
                if (!is_null($ap_proc_nivel)) {
                    $valor = $ap_proc_nivel->uvr1 * $ap_proc_nivel->prc1;
                    //$valor = round($valor,4);
                    if ($apcabecera->id_seguro == '5') {
                        $valor = round($valor, 2);
                    } else {
                        $valor = round($valor, 4);
                    }
                }

                $prb_leche = [
                    'id_ap_cabecera' => $cabecera,
                    //'fecha' => $orden->fecha_orden,
                    'fecha' => $apcabecera->fecha_ing,
                    'id_detalle_labs' => $value->id,
                    'id_orden_labs' => $orden->id,
                    'tipo' => 'EX',
                    'codigo' => $codigo,
                    'descripcion' =>  'PRUEBA ALERGIA LECHE',
                    'cantidad' => '1',
                    'subtotal' => $valor,
                    'valor' => $valor,
                    'iva' => '0',
                    'valor_unitario' => $valor,
                    'clasif_porcentaje_msp' => '100',
                    'clasificador' => 'SA11-43',
                    'total' => $valor,
                    'total_solicitado_usd' => $valor,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                ];
                Archivo_Plano_Detalle::create($prb_leche);
            } else if ($value->id_examen == '1232') {

                $codigo = '360130';
                $valor = 0;

                $ap_proc_nivel = ApProcedimientoNivel::where('codigo', $codigo)->where('cod_conv', $nivel)->first();
                if (!is_null($ap_proc_nivel)) {
                    $valor = $ap_proc_nivel->uvr1 * $ap_proc_nivel->prc1;
                    //$valor = round($valor,4);
                    if ($apcabecera->id_seguro == '5') {
                        $valor = round($valor, 2);
                    } else {
                        $valor = round($valor, 4);
                    }
                }

                $fructo = [
                    'id_ap_cabecera' => $cabecera,
                    //'fecha' => $orden->fecha_orden,
                    'fecha' => $apcabecera->fecha_ing,
                    'id_detalle_labs' => $value->id,
                    'id_orden_labs' => $orden->id,
                    'tipo' => 'EX',
                    'codigo' => $codigo,
                    'descripcion' =>  'FRUCTOSAMINA',
                    'cantidad' => '1',
                    'subtotal' => $valor,
                    'valor' => $valor,
                    'valor_unitario' => $valor,
                    'iva' => '0',
                    'clasif_porcentaje_msp' => '100',
                    'clasificador' => 'SA11-43',
                    'total' => $valor,
                    'total_solicitado_usd' => $valor,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                ];
                Archivo_Plano_Detalle::create($fructo);

                $codigo = '270345';
                $valor = 0;

                $ap_proc_nivel = ApProcedimientoNivel::where('codigo', $codigo)->where('cod_conv', $nivel)->first();
                if (!is_null($ap_proc_nivel)) {
                    $valor = $ap_proc_nivel->uvr1 * $ap_proc_nivel->prc1;
                    //$valor = round($valor,4);
                    if ($apcabecera->id_seguro == '5') {
                        $valor = round($valor, 2);
                    } else {
                        $valor = round($valor, 4);
                    }
                }

                $frutilla = [
                    'id_ap_cabecera' => $cabecera,
                    //'fecha' => $orden->fecha_orden,
                    'fecha' => $apcabecera->fecha_ing,
                    'id_detalle_labs' => $value->id,
                    'id_orden_labs' => $orden->id,
                    'tipo' => 'EX',
                    'codigo' => $codigo,
                    'descripcion' =>  'PRUEBA ALERGIA FRESA O FRUTILLA',
                    'cantidad' => '1',
                    'subtotal' => $valor,
                    'valor' => $valor,
                    'iva' => '0',
                    'valor_unitario' => $valor,
                    'clasif_porcentaje_msp' => '100',
                    'clasificador' => 'SA11-43',
                    'total' => $valor,
                    'total_solicitado_usd' => $valor,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                ];
                Archivo_Plano_Detalle::create($frutilla);

                $codigo = '270348';
                $valor = 0;

                $ap_proc_nivel = ApProcedimientoNivel::where('codigo', $codigo)->where('cod_conv', $nivel)->first();
                if (!is_null($ap_proc_nivel)) {
                    $valor = $ap_proc_nivel->uvr1 * $ap_proc_nivel->prc1;
                    //$valor = round($valor,4);
                    if ($apcabecera->id_seguro == '5') {
                        $valor = round($valor, 2);
                    } else {
                        $valor = round($valor, 4);
                    }
                }

                $naranja = [
                    'id_ap_cabecera' => $cabecera,
                    //'fecha' => $orden->fecha_orden,
                    'fecha' => $apcabecera->fecha_ing,
                    'id_detalle_labs' => $value->id,
                    'id_orden_labs' => $orden->id,
                    'tipo' => 'EX',
                    'codigo' => $codigo,
                    'descripcion' =>  'PRUEBA ALERGIA NARANJA',
                    'cantidad' => '1',
                    'subtotal' => $valor,
                    'valor' => $valor,
                    'iva' => '0',
                    'valor_unitario' => $valor,
                    'clasif_porcentaje_msp' => '100',
                    'clasificador' => 'SA11-43',
                    'total' => $valor,
                    'total_solicitado_usd' => $valor,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                ];
                Archivo_Plano_Detalle::create($naranja);
            } else {
                $codigo = $value->examen->tarifario;
                $valor = 0;

                $ap_proc_nivel = ApProcedimientoNivel::where('codigo', $codigo)->where('cod_conv', $nivel)->first();
                if (!is_null($ap_proc_nivel)) {
                    $valor = $ap_proc_nivel->uvr1 * $ap_proc_nivel->prc1;
                    //$valor = round($valor,4);
                    if ($apcabecera->id_seguro == '5') {
                        $valor = round($valor, 2);
                    } else {
                        $valor = round($valor, 4);
                    }
                }

                $desc = $value->examen->nombre_iess;
                if ($desc == null) {
                    $desc = $value->examen->nombre;
                }
                $arr = [
                    'id_ap_cabecera' => $cabecera,
                    //'fecha' => $orden->fecha_orden,
                    'fecha' => $apcabecera->fecha_ing,
                    'id_detalle_labs' => $value->id,
                    'id_orden_labs' => $orden->id,
                    'tipo' => 'EX',
                    'codigo' => $value->examen->tarifario,
                    //'descripcion' => $value->examen->nombre,
                    'descripcion' =>  $desc,
                    'cantidad' => '1',
                    'subtotal' => $valor,
                    'valor' => $valor,
                    'iva' => '0',
                    'valor_unitario' => $valor,
                    'clasif_porcentaje_msp' => '100',
                    'clasificador' => 'SA11-43',
                    'total' => $valor,
                    'total_solicitado_usd' => $valor,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                ];
                Archivo_Plano_Detalle::create($arr);
            }
        }
        LogAuditoriaPublica::guardarLog_agrega_laboratorio($cabecera, 'FUNCION AGREGA POR LABORATORIO', $orden->id);

        return "ok";
    }

    public function elimino_procedimiento($id)
    {
        //$dcat=Archivo_Plano_Detalle::find($id);
        $detalle = Archivo_Plano_Detalle::find($id);

        $arr = [
            'estado' => '0',
        ];

        $cabecera = $detalle->cabecera;

        $detalle->update($arr);

        
        LogAuditoriaPublica::guardarLog_elimina_detalle($id,'ELIMINA DETALLE ITEM');

        return ['estado' => 'ok'];

        //$dcat->delete();
    }

    /************************************************/
    /**************ELIMINA ITEM IESS*****************/
    /************************************************/
    public function delete_todo_items_iess(Request $request)
    {

        $id_cab = $request['id_archivo_plano'];

        $plan_detalle = Archivo_Plano_Detalle::where('id_ap_cabecera', $id_cab)
            ->where('estado', '1')->get();


        foreach ($plan_detalle as $value) {

            $arr = [
                'estado' => '0',
            ];

            Archivo_Plano_Detalle::where('id', $value->id)
                ->update($arr);
        }
        LogAuditoriaPublica::guardarLog_elimanar_todo($id_cab,'ELIMINA TODOS LOS DETALLES');

        return "ok";
    }

    /************************************************/
    /**************ELIMINA ITEM MSP*****************/
    /************************************************/
    public function delete_todo_items_msp(Request $request)
    {

        $id_cab = $request['id_archivo_plano'];

        $plan_detalle = Archivo_Plano_Detalle::where('id_ap_cabecera', $id_cab)
            ->where('estado', '1')->get();


        $arr_cab = [
            'nom_procedimiento' => '',
        ];

        Archivo_Plano_Cabecera::where('id', $id_cab)
            ->where('estado', '1')
            ->update($arr_cab);

        foreach ($plan_detalle as $value) {

            $arr = [
                'estado' => '0',
            ];

            Archivo_Plano_Detalle::where('id', $value->id)
                ->update($arr);
        }
        LogAuditoriaPublica::guardarLog_elimanar_todo($id_cab, 'ELIMINA TODOS LOS DETALLES');
        return "ok";
    }

    /************************************************/
    /**************ELIMINA CABECERA PLANILLA*********/
    /************************************************/
    public function elimina_cabecera_planilla($idcab)
    {

        $arr = [
            'estado' => '0',
        ];

        Archivo_Plano_Cabecera::where('id', $idcab)
            ->update($arr);
    }


    /*
    public function ingresa_procedimiento($id,$cabecera){
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $orden = ApProcedimiento::where('codigo', $id)->first();
        //$ldate = date('Y-m-d H:i:s');
        $regi = new Archivo_Plano_Detalle();
        $regi->id_ap_cabecera = $cabecera;
        $regi->tipo = $orden->tipo;
        $regi->codigo = $orden->codigo;
        $regi->descripcion = $orden->descripcion;
        $regi->cantidad = $orden->cantidad;
        $regi->valor = $orden->valor;
        $regi->iva = $orden->iva;
        $regi->total = $orden->valor;
        $regi->estado = 1;
        $regi->id_usuariocrea = $idusuario;
        $regi->id_usuariomod = $idusuario;
        $regi->ip_creacion = $ip_cliente;
        $regi->ip_modificacion = $ip_cliente;
        $regi->save();

    }
    */

    /*Esta funcion se la va eliminar*/
    /*public function ingresa_procedimiento($id,$cabecera,$fecha){
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;

        $ordenes = "SELECT b.tipo, b.codigo, b.descripcion, a.cantidad, b.valor,b.porcentaje10,b.iva from ap_plantilla_items a, ap_procedimiento b
        where cod_plantilla='$cabecera' and a.id_procedimiento=b.codigo;";
        $ordenes = DB::select($ordenes);

        foreach ($ordenes as $orden){

            $regi = new Archivo_Plano_Detalle();
            $regi->id_ap_cabecera = $id;
            $regi->fecha = $fecha;
            $regi->tipo = $orden->tipo;
            $regi->codigo = $orden->codigo;
            $regi->descripcion = $orden->descripcion;
            $regi->cantidad = $orden->cantidad;
            $regi->valor = $orden->valor;
            $regi->subtotal = ($orden->cantidad)*($orden->valor);
            $regi->porcentaje10 = (($orden->cantidad)*($orden->valor))*($orden->porcentaje10);
            $regi->iva = (($orden->cantidad)*($orden->valor))*($orden->iva);
            $regi->total = (($orden->cantidad)*($orden->valor))+((($orden->cantidad)*($orden->valor))*($orden->porcentaje10))+((($orden->cantidad)*($orden->valor))*($orden->iva));
            $regi->clasif_porcentaje_msp = 100;
            $regi->subtotal_msp = ($orden->cantidad)*($orden->valor);
            //$r->certificado == '1'
            if($orden->tipo == 'S'){
             //return $orden->tipo;
             $porcent_clasif = Ap_Tipo_Examen::where('tipo',$orden->tipo)->where('estado', '1')->first();
             $regi->valor_modificador_msp = (($orden->cantidad)*($orden->valor))*($porcent_clasif->porcentaje_modif_msp);
             $regi->total_solicitado_msp = (($orden->cantidad)*($orden->valor))+((($orden->cantidad)*($orden->valor))*($porcent_clasif->porcentaje_modif_msp));
            }else{
             //return $orden->tipo;
             $regi->valor_modificador_msp = 0;
             $regi->total_solicitado_msp = ($orden->cantidad)*($orden->valor);
            }

            $regi->estado = 1;
            $regi->id_usuariocrea = $idusuario;
            $regi->id_usuariomod = $idusuario;
            $regi->ip_creacion = $ip_cliente;
            $regi->ip_modificacion = $ip_cliente;
            $regi->save();
        }


    }*/

    /************************************************/
    /*********INGRESO PLANTILLA PROCEDIMIENTO********/
    /************************************************/
    public function ingresa_procedimiento_detalle(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;

        $id_plan_cab = $request['plan_cabecera'];
        $cod_plant   = $request['id_procedimiento'];
        $fecha_ok    = $request['fecha'];

        $fecha_ok = date("Y-m-d", strtotime(str_replace('/', '-', $fecha_ok)));

        $fecha    = substr($request['fecha'], 6, 4) . '-' . substr($request['fecha'], 3, 2) . '-' . substr($request['fecha'], 0, 2);
        $id_nivel = $request['nivel_convenio'];
        //$id_seguro = $request['id_seguro'];

        /*
        if($cod_plant == 39){

            $ordenes = ApPlantillaItem::where('cod_plantilla', $cod_plant)->where('ap_plantilla_items.estado', '1')
            ->join('ap_procedimiento as ap_proc', 'ap_proc.id', 'ap_plantilla_items.procedimiento')
            ->select('ap_proc.descripcion', 'ap_proc.codigo', 'ap_plantilla_items.cantidad', 'ap_proc.iva', 'ap_proc.tipo', 'ap_proc.valor', 'ap_plantilla_items.orden', 'ap_proc.porcentaje10')->get();


        }else{

            $ordenes = ApPlantillaItem::where('cod_plantilla', $cod_plant)->where('ap_plantilla_items.estado', '1')
            ->join('ap_procedimiento as ap_proc', 'ap_proc.codigo', 'ap_plantilla_items.id_procedimiento')
            ->select('ap_proc.descripcion', 'ap_proc.codigo', 'ap_plantilla_items.cantidad', 'ap_proc.iva', 'ap_proc.tipo', 'ap_proc.valor', 'ap_plantilla_items.orden', 'ap_proc.porcentaje10')->get();


        }*/

        $ordenes = ApPlantillaItem::where('cod_plantilla', $cod_plant)->where('ap_plantilla_items.estado', '1')
            ->join('ap_procedimiento as ap_proc', 'ap_proc.id', 'ap_plantilla_items.procedimiento')
            ->select('ap_proc.descripcion', 'ap_proc.codigo', 'ap_plantilla_items.cantidad', 'ap_proc.iva', 'ap_proc.tipo', 'ap_proc.valor', 'ap_plantilla_items.orden', 'ap_proc.porcentaje10','ap_proc.id', 'ap_proc.codigo_medicina')->get();
        
        //return $ordenes;
        $vic = 0;
        foreach ($ordenes as $value) {

            $vic++;
            $clasif = DB::table('ap_tipo_examen')->where('ap_tipo_examen.tipo', $value->tipo)->first();

            $clas_porcent = '';
            $val_clasif = 0;
            $total_sol = 0;
            $val_unit = 0;
            $valor_iva = 0;
            $valor10 = 0;
            $clasif_porcent = 0;
            $k_valor = $value->valor;
            $k_cantidad = $value->cantidad;
            $tipo_an = 'AN';
            $porce_hono = 100;
            $valor_an = 0;

            $iva = $value->iva;
            if ($fecha == null) {
                $iva = $value->iva;
            }

            if ($fecha >= '2024-04-01') {

                $iva = $value->iva_nuevo;
            }

            $valor_nivel = ApProcedimientoNivel::where('id_procedimiento', $value->id)->where('cod_conv', $id_nivel)->first();
            if (!is_null($valor_nivel)) {
                $k_valor = round(($valor_nivel->uvr1 * $valor_nivel->prc1), 4);
            }

            if ($value->orden == '3') {
                $k_valor = $k_valor / 2;
                $porce_hono = 50;
            }

            //Ingreso de Plantilla IESS y MSP
            if ($value->tipo == 'S') {
                $subtotal = $k_valor * $k_cantidad;
                $porcent_clasif = Ap_Tipo_Examen::where('tipo', $value->tipo)->where('estado', '1')->first();
                $val = $k_valor;
                $val_clasif = $subtotal * $porcent_clasif->porcentaje_modif_msp;
                $total_sol = $subtotal + $val_clasif;
                $total = $subtotal;
            } else {
                //$val =$k_valor/(1+$value->porcentaje10);
                $val = $k_valor;
                $val_unit = $val / (1 + $value->porcentaje10);
                $subtotal = $value->cantidad * $val_unit;

                $val_unit  = round(($val_unit), 4);
                $iva_unitario = $val_unit * $iva;
                $p10_unitario = $val_unit * $value->porcentaje10;

                $iva_unitario = $this->redondea_menos_4_dec($iva_unitario);
                $p10_unitario = $this->redondea_menos_4_dec($p10_unitario);

                //$valor10 = $subtotal * $value->porcentaje10;
                //$valor_iva = $subtotal * $value->iva;
                //$total = $subtotal + $valor10 + $valor_iva;

                $valor10      = $value->cantidad * $p10_unitario;
                $valor_iva    = $value->cantidad * $iva_unitario;

                $total        = $subtotal + $valor10 + $valor_iva;

                $total_sol = $total;
            }

            if ($vic == 17) {
                //return [$value, $clasif];
            }

            if ($clasif->clasificado == 'SA07-50') {

                $clasif_porcent = 50;
            } else {
                $clasif_porcent = 100;
            }

            $codigo_medicina = null;
            if($value->tipo == 'M'){
                $codigo_medicina = $value->codigo_medicina;
            }

            $arr = [
                'id_ap_cabecera'          => $id_plan_cab,
                'fecha'                   => $fecha,
                'id_nivel'                => $id_nivel,
                'tipo'                    => $value->tipo,
                'codigo'                  => $value->codigo,
                'clasificador'            => $clasif->clasificado,
                'descripcion'             => $value->descripcion,
                'cantidad'                => $value->cantidad,
                'valor'                   => round(($val), 4),
                'subtotal'                => round(($subtotal), 4),
                'porcent_10'              => $value->porcentaje10,
                'porcentaje10'            => round(($valor10), 4),
                'porcentaje_iva'          => $iva,
                'valor_unitario'          => round(($val_unit), 4),
                'iva'                     => $valor_iva,
                'clasif_porcentaje_msp'   => $clasif_porcent,
                'valor_porcent_clasifi'   => round(($val_clasif), 4),
                'total'                   => round(($total), 4),
                'total_solicitado_usd'    => round(($total_sol), 4),
                'orden_plantilla_item'    => $value->orden,
                'porcentaje_honorario'    => $porce_hono,
                'id_usuariocrea'          => $idusuario,
                'id_usuariomod'           => $idusuario,
                'ip_creacion'             => $ip_cliente,
                'ip_modificacion'         => $ip_cliente,
                'codigo_medicina'         => $codigo_medicina,
		'iva_unitario'            => $iva_unitario,
                'p10_unitario'            => $p10_unitario,

            ];

            Archivo_Plano_Detalle::insert($arr);


            //Insertamos AN
            if ($value->orden == 1) {

                if (!is_null($valor_nivel)) {
                    $valor_an = ($valor_nivel->uvr1a) * ($valor_nivel->prc1a);
                }

                $subtotal_an = $value->cantidad * $valor_an;
                $total_an = $subtotal_an;

                $arr = [
                    'id_ap_cabecera'          => $id_plan_cab,
                    'fecha'                   => $fecha,
                    'id_nivel'                => $id_nivel,
                    'tipo'                    => $tipo_an,
                    'codigo'                  => $value->codigo,
                    'clasificador'            => 'SA19-84',
                    'descripcion'             => $value->descripcion,
                    'cantidad'                => $value->cantidad,
                    'valor'                   => round(($valor_an), 4),
                    'subtotal'                => round(($subtotal_an), 4),
                    //'porcent_10'              => $value->porcentaje10,
                    //'porcentaje10'            => round(($valor10),2),
                    //'porcentaje_iva'          => $value->iva,
                    //'valor_unitario'          => round(($val),2),
                    //'iva'                     => $valor_iva,
                    'clasif_porcentaje_msp'   => $clasif_porcent,
                    //'valor_porcent_clasifi'   => $val_clasif,
                    'total'                   => $total_an,
                    'total_solicitado_usd'    => $total_an,
                    'porcentaje_honorario'    => $porce_hono,
                    'id_usuariocrea'          => $idusuario,
                    'id_usuariomod'           => $idusuario,
                    'ip_creacion'             => $ip_cliente,
                    'ip_modificacion'         => $ip_cliente,
                ];

                Archivo_Plano_Detalle::insert($arr);
            }
        }

        //$obtener_plant = ApPlantilla::where('id',$cod_plant)->where('estado', '1')->first();

        //return $obtener_plant->descripcion;

        //Archivo_Plano_Cabecera::where('id',$id_plan_cab)->where('estado', '1')->first();

        $plan_cabecera = Archivo_Plano_Cabecera::find($id_plan_cab);

        $obtener_plantilla = ApPlantilla::where('codigo', $cod_plant)->where('estado', '1')->first();

        $txt_texto = null;

        if (is_null($plan_cabecera->nom_procedimiento)) {

            $txt_texto = $plan_cabecera->nom_procedimiento . ' / ' . $obtener_plantilla->descripcion;

            if (is_null($plan_cabecera->nom_procedimiento)) {

                $txt_texto = $obtener_plantilla->descripcion;
            }
        } else {

            $txt_texto = $plan_cabecera->nom_procedimiento . ' / ' . $obtener_plantilla->descripcion;
        }

        $nombre_plantilla = [

            'nom_procedimiento' => $txt_texto,

        ];

        Archivo_Plano_Cabecera::where('id', $id_plan_cab)->update($nombre_plantilla);

        LogAuditoriaPublica::guardarLog_agrega_plantilla($id_plan_cab, 'FUNCION AGREGA POR PLANTILLA', $obtener_plantilla->descripcion);

        return "ok";
    }

    /************************************************/
    /*********OBTENER CLASIFICADOR POR TIPO**********/
    /************************************************/
    public function obtener_clasificador($tipo)
    {

        $clasificador = 0;
        $porcent_clasif = 0;

        $clasif = DB::table('ap_tipo_examen')->where('ap_tipo_examen.tipo', $tipo)->first();


        if (!is_null($clasif)) {

            $clasificador =  $clasif->clasificado;
            $porcent_clasif =  $clasif->porcentaje_clasificado;
        }

        return ['clasificador' => $clasificador, 'porcent_clasif' => $porcent_clasif];
    }


    /************************************************/
    /*********OBTENER PRECIO ITEMS*******************/
    /************************************************/
    /*public function obtener_precio_item($id_ap_proced, $idnivel, $tipo)
    {


        $iva = 0;
        $precio = 0;
        $hono_anast = 0;
        $val_tiemp_anest = 0;
        $separ = '';

        if (($tipo == 'M') || ($tipo == 'I') || ($tipo == 'IV')) {

            $inf_proced = ApProcedimiento::find($id_ap_proced);
            $precio = round(($inf_proced->valor), 2);
            return ['precio' => $precio, 'hono_anast' => $hono_anast, 'separ' => $separ, 'val_tiemp_anest' => $val_tiemp_anest];
        } else {

            $inf_proc_nivel = ApProcedimientoNivel::where('id_procedimiento', $id_ap_proced)
                ->where('cod_conv', $idnivel)
                ->select(
                    'ap_procedimiento_nivel.uvr1',
                    'ap_procedimiento_nivel.prc1',
                    'ap_procedimiento_nivel.uvr1a',
                    'ap_procedimiento_nivel.prc1a',
                    'ap_procedimiento_nivel.separado'
                )
                ->first();

            if(!is_null($inf_proc_nivel)){

                //Procedimiento
                $precio = round(($inf_proc_nivel->uvr1) * ($inf_proc_nivel->prc1), 2);
                //Anastesiologo
                $hono_anast  = round(($inf_proc_nivel->uvr1a) * ($inf_proc_nivel->prc1a), 2);
                //Tiempo de Anestesia
                $inf_tiemp_anest = ApProcedimientoNivel::where('codigo', 'Z99999')
                    ->where('cod_conv', $idnivel)->first();
                if (!is_null($inf_tiemp_anest)) {
                    $val_tiemp_anest  = round(($inf_tiemp_anest->uvr1) * ($inf_tiemp_anest->prc1), 2);
                }

                $separ = $inf_proc_nivel->separado;

                return ['precio' => $precio, 'hono_anast' => $hono_anast, 'separ' => $separ, 'val_tiemp_anest' => $val_tiemp_anest];

            }


        }
    }*/
    public function obtener_precio_item($id_ap_proced, $idnivel, $tipo)
    {

        $iva = 0;
        $precio = 0;
        $hono_anast = 0;
        $val_tiemp_anest = 0;
        $separ = '';

        $inf_proced = ApProcedimiento::find($id_ap_proced);
        if (!is_null($inf_proced)) {
            $precio = round(($inf_proced->valor), 4);

            $inf_proc_nivel = ApProcedimientoNivel::where('id_procedimiento', $id_ap_proced)
                ->where('cod_conv', $idnivel)
                ->select(
                    'ap_procedimiento_nivel.uvr1',
                    'ap_procedimiento_nivel.prc1',
                    'ap_procedimiento_nivel.uvr1a',
                    'ap_procedimiento_nivel.prc1a',
                    'ap_procedimiento_nivel.separado'
                )
                ->first();

            if (!is_null($inf_proc_nivel)) {

                //Procedimiento
                $precio = round(($inf_proc_nivel->uvr1) * ($inf_proc_nivel->prc1), 4);
                //Anastesiologo
                $hono_anast  = round(($inf_proc_nivel->uvr1a) * ($inf_proc_nivel->prc1a), 4);
                //Tiempo de Anestesia
                $inf_tiemp_anest = ApProcedimientoNivel::where('codigo', 'Z99999')
                    ->where('cod_conv', $idnivel)->first();
                if (!is_null($inf_tiemp_anest)) {
                    $val_tiemp_anest  = round(($inf_tiemp_anest->uvr1) * ($inf_tiemp_anest->prc1), 4);
                }

                $separ = $inf_proc_nivel->separado;
            }
        }

        return ['precio' => $precio, 'hono_anast' => $hono_anast, 'separ' => $separ, 'val_tiemp_anest' => $val_tiemp_anest];
    }

    /*public function ingresa_procedimiento_detalle($id_plan_cab,$cod_plant,$fecha){

        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;

        $ordenes = "SELECT b.tipo, b.codigo, b.descripcion, a.cantidad,b.valor,b.porcentaje10,b.iva from ap_plantilla_items a,ap_procedimiento b
        where cod_plantilla='$cod_plant' and a.id_procedimiento=b.codigo";
        $ordenes = DB::select($ordenes);

        foreach ($ordenes as $value){

            $arr = [
                'id_ap_cabecera'   => $id_plan_cab,
                'fecha'            => $fecha,
                'tipo'             => $value->tipo,
                'codigo'           => $value->codigo,
                'descripcion'      => $value->descripcion,
                'cantidad'         => $value->cantidad,
                'valor'            => $value->valor,
                'subtotal'         => ($value->cantidad)*($value->valor),
                'porcent_10'       => $value->porcentaje10,
                'porcentaje10'     => (($value->cantidad)*($value->valor))*($value->porcentaje10),
                'porcentaje_iva'   => $value->iva,
                'iva'              => (($value->cantidad)*($value->valor))*($value->iva),
                'total'            => (($value->cantidad)*($value->valor))+((($value->cantidad)*($value->valor))*($value->porcentaje10))+((($value->cantidad)*($value->valor))*($value->iva)),
                'id_usuariocrea'   => $idusuario,
                'id_usuariomod'    => $idusuario,
                'ip_creacion'      => $ip_cliente,
                'ip_modificacion'  => $ip_cliente,
            ];

            Archivo_Plano_Detalle::insert($arr);
        }

        return "ok";

    }

    /************************************/
    /********ACTUALIZA ITEM IESS******** /
    /************************************/
    public function edito_proce($id)
    {
        $cat = Archivo_Plano_Detalle::find($id);
        return view('archivo_plano/procedimientos/proce', compact('cat'));
    }

    public function edito_proce2(Request $request, $id)
    {
        $cat = Archivo_Plano_Detalle::find($id);
        $cat->cantidad = $request->cantidad;
        $cat->valor = $request->valor;
        $cat->iva = $request->iva;
        $cat->total = $request->total;
        $cat->save();
        return back();
    }


    public function planilla_individual($hcid)
    {
        //hcid es el id de cabecera no el de historia clinica
        $archivo_plano = Archivo_Plano_Cabecera::where('id', $hcid)
            ->where('archivo_plano_cabecera.estado', '1')
            ->first();
        $ap = Archivo_Plano_Cabecera::find($archivo_plano->id);
        $detalles = $ap->detalles;

        $txt_cie10 = null;

        $cie10 = Cie_10_3::find($archivo_plano->cie10);
        if (is_null($cie10)) {
            $cie10 = Cie_10_4::find($archivo_plano->cie10);
            if (!is_null($cie10)) {
                $txt_cie10 = '(' . $archivo_plano->cie10 . ') ' . $cie10->descripcion;
            }
        } else {
            $txt_cie10 = '(' . $archivo_plano->cie10 . ') ' . $cie10->descripcion;
        }

        $honor_medicos = Db::table('archivo_plano_detalle as apd')
            ->where('id_ap_cabecera', $archivo_plano->id)
            ->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')
            ->orderby('apd.porcentaje_honorario', 'desc')
            ->orderby('apt.secuencia', 'asc')
            ->where('apt.tipo_ex', 'HME')
            //->orWhere('apt.tipo_ex','P')
            ->where('apd.estado', '1')
            ->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')
            ->get();

        $medicinas = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total', 'apd.codigo_medicina')->where('apt.tipo_ex', 'FAR')->where('apd.estado', '1')->get();

        $insumos = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total', 'apd.valor_unitario', 'apd.porcentaje_iva')->where('apt.tipo_ex', 'IMM')->where('apd.estado', '1')->get();

        $laboratorio = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')->where('apt.tipo_ex', 'LAB')->where('apd.estado', '1')->get();

        $servicios_ins = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')->where('apt.tipo_ex', 'HOSP/QUIR')->where('apd.estado', '1')->get();

        $imagen = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')->where('apt.tipo_ex', 'IMA')->where('apd.estado', '1')->get();

        $equipos = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')->where('apt.tipo_ex', 'PRO/ESP')->where('apd.estado', '1')->get();

        Excel::create('Formato Planilla Individual Iess', function ($excel) use ($archivo_plano, $detalles, $txt_cie10, $honor_medicos, $medicinas, $insumos, $laboratorio, $servicios_ins, $imagen, $equipos) {
            $excel->sheet(date('Y-m-d'), function ($sheet) use ($archivo_plano, $detalles, $txt_cie10, $honor_medicos, $medicinas, $insumos, $laboratorio, $servicios_ins, $imagen, $equipos) {
                $sheet->mergeCells('A1:P1');
                $sheet->mergeCells('A2:D2');
                $sheet->cell('A2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NOMBRE DEL PRESTADOR');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->mergeCells('E2:P2');
                $sheet->cell('E2', function ($cell) use ($archivo_plano) {
                    $cell->setValue($archivo_plano->empresa->razonsocial);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('A3:D3');
                $sheet->cell('A3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SEGURO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->mergeCells('E3:P3');
                $sheet->cell('E3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('IESS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    /*if ($archivo_plano->id_tipo_seguro !=null) {
                        $cell->setValue($archivo_plano->tiposeguro->nombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    }*/
                });
                $sheet->mergeCells('A4:D4');
                $sheet->cell('A4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NOMBRE DEL PACIENTE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->mergeCells('E4:P4');
                $sheet->cell('E4', function ($cell) use ($archivo_plano) {
                    // manipulate the cel
                    $cell->setValue($archivo_plano->paciente->apellido1 . ' ' . $archivo_plano->paciente->apellido2 . ' ' . $archivo_plano->paciente->nombre1 . ' ' . $archivo_plano->paciente->nombre2);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('A5:D5');
                $sheet->cell('A5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CÉDULA DE IDENTIDAD');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->mergeCells('E5:P5');
                $sheet->cell('E5', function ($cell)  use ($archivo_plano) {
                    // manipulate the cel
                    $cell->setValue($archivo_plano->id_paciente);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('A6:D6');
                $sheet->cell('A6', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('HISTORIA CLÍNICA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $hc = substr($archivo_plano->id_paciente, 5, 10);
                $sheet->mergeCells('E6:P6');
                $sheet->cell('E6', function ($cell) use ($hc) {
                    // manipulate the cel
                    $cell->setValue($hc);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('A7:D7');
                $sheet->cell('A7', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA DE INGRESO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });

                $fecha_ing = substr($archivo_plano->fecha_ing, 0, 10);
                $fecha_ing_inv = date("d/m/Y", strtotime($fecha_ing));

                $sheet->mergeCells('E7:P7');
                $sheet->cell('E7', function ($cell) use ($fecha_ing_inv) {
                    // manipulate the cel
                    $cell->setValue($fecha_ing_inv);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('A8:D8');
                $sheet->cell('A8', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA DE EGRESO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });

                $fecha_alt = substr($archivo_plano->fecha_alt, 0, 10);
                $fecha_alt_inv = date("d/m/Y", strtotime($fecha_alt));

                $sheet->mergeCells('E8:P8');
                $sheet->cell('E8', function ($cell) use ($fecha_alt_inv) {
                    // manipulate the cel
                    $cell->setValue($fecha_alt_inv);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('A9:D9');
                $sheet->cell('A9', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PROCEDIMIENTO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->mergeCells('E9:P9');
                $sheet->cell('E9', function ($cell) use ($archivo_plano) {
                    // manipulate the cel
                    $cell->setValue($archivo_plano->nom_procedimiento);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('A10:D10');
                $sheet->cell('A10', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DIAGNÓSTICO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->mergeCells('E10:P10');
                $sheet->cell('E10', function ($cell) use ($archivo_plano, $txt_cie10) {
                    // manipulate the cel
                    $cell->setValue($txt_cie10);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('A11:P11');
                $sheet->cell('A11', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PLANILLA DE CARGOS DEL PROVEEDOR (CONSULTA EXTERNA,HOSPITALIZACIÓN Y EMERGENCIA)');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });
                $sheet->mergeCells('A12:P12');
                $sheet->cell('A12', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('HONORARIOS MEDICOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });
                $sheet->cell('A13', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('B13', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CÓDIGO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->mergeCells('C13:J13');
                $sheet->cell('C13', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DESCRIPCIÓN');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('K13', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CANTIDAD');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('L13', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('VALOR UNITARIO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('M13', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SUBTOTAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('N13', function ($cell) {
                    // anipulate the cel
                    $cell->setValue('10%');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('O13', function ($cell) {
                    // anipulate the cel
                    $cell->setValue('IVA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('P13', function ($cell) {
                    // anipulate the cel
                    $cell->setValue('TOTAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setAlignment('right');
                    $cell->setFontWeight('bold');
                });
                $i = 14;
                $total = 0;

                $sheet->setColumnFormat(array(
                    'N' => '$ 0.00',
                    'O' => '$ 0.00',
                    'P' => '$ 0.00',

                ));
                foreach ($honor_medicos as $value) {
                    $total += $value->total;
                    $fecha_honor = substr($value->fecha, 0, 10);
                    $fecha_honor_inv = date("d/m/Y", strtotime($fecha_honor));

                    $sheet->cell('A' . $i, function ($cell) use ($fecha_honor_inv) {
                        // manipulate the cel
                        $cell->setValue($fecha_honor_inv);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('B' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->codigo);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->mergeCells('C' . $i . ':J' . $i);
                    $sheet->cell('C' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->descripcion);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('K' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->cantidad);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->getStyle('L' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                    $sheet->cell('L' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->valor);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });


                    $sheet->getStyle('M' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                    $sheet->cell('M' . $i, function ($cell)  use ($value) {
                        // manipulate the cel
                        $cell->setValue(round($value->subtotal, 2));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('N' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue(round($value->porcentaje10, 2));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('O' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue(round($value->iva, 2));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('P' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue(round($value->total, 2));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $i++;
                }
                $i = $i;
                $sheet->mergeCells('A' . $i . ':J' . $i);
                $sheet->cell('A' . $i, function ($cell) {
                    // anipulate the cel
                    $cell->setValue('TOTAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setAlignment('right');
                    $cell->setFontWeight('bold');
                });
                $sheet->mergeCells('K' . $i . ':O' . $i);
                $sheet->cell('K' . $i, function ($cell) {
                    // anipulate the cel
                    $cell->setValue('HONORARIOS MEDICOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('P' . $i, function ($cell) use ($total) {
                    // anipulate the cel
                    $cell->setValue(round($total, 2));
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $i++;
                $sheet->mergeCells('A' . $i . ':P' . $i);
                $sheet->cell('A' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('MEDICINAS VALOR AL ORIGEN');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });
                $i = $i + 1;
                $sheet->cell('A' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('B' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CÓDIGO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->mergeCells('C' . $i . ':J' . $i);

                $sheet->cell('C' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DESCRIPCIÓN');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('K' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CANTIDAD');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('L' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('VALOR UNITARIO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('M' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SUBTOTAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('N' . $i, function ($cell) {
                    // anipulate the cel
                    $cell->setValue('10%');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('O' . $i, function ($cell) {
                    // anipulate the cel
                    $cell->setValue('IVA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('P' . $i, function ($cell) {
                    // anipulate the cel
                    $cell->setValue('TOTAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $i = $i + 1;
                $totalmed = 0;
                foreach ($medicinas as $medicina) {
                    $totalmed += $medicina->total;
                    $fecha_med = substr($medicina->fecha, 0, 10);
                    $fecha_med_inv = date("d/m/Y", strtotime($fecha_med));
                    $sheet->cell('A' . $i, function ($cell) use ($fecha_med_inv) {
                        // manipulate the cel
                        $cell->setValue($fecha_med_inv);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('B' . $i, function ($cell) use ($medicina) {
                        // manipulate the cel
                        $cell->setValue($medicina->codigo_medicina);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->getStyle('B' . $i)->getNumberFormat()->setFormatCode('0');

                    $sheet->mergeCells('C' . $i . ':J' . $i);
                    $sheet->cell('C' . $i, function ($cell) use ($medicina) {
                        // manipulate the cel
                        $cell->setValue($medicina->descripcion);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('K' . $i, function ($cell) use ($medicina) {
                        // manipulate the cel
                        $cell->setValue($medicina->cantidad);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->getStyle('L' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                    $sheet->cell('L' . $i, function ($cell) use ($medicina) {
                        // manipulate the cel
                        $cell->setValue($medicina->valor);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->getStyle('M' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                    $sheet->cell('M' . $i, function ($cell) use ($medicina) {
                        // manipulate the cel
                        $cell->setValue(round($medicina->subtotal, 2));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('N' . $i, function ($cell) use ($medicina) {
                        // manipulate the cel
                        $cell->setValue(round($medicina->porcentaje10, 2));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('O' . $i, function ($cell) use ($medicina) {
                        // manipulate the cel
                        $cell->setValue(round($medicina->iva, 2));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('P' . $i, function ($cell) use ($medicina) {
                        // manipulate the cel
                        $cell->setValue(round($medicina->total, 2));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $i++;
                }
                $i = $i;
                $sheet->mergeCells('A' . $i . ':J' . $i);
                $sheet->cell('A' . $i, function ($cell) {
                    // anipulate the cel
                    $cell->setValue('TOTAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setAlignment('right');
                    $cell->setFontWeight('bold');
                });
                $sheet->mergeCells('K' . $i . ':O' . $i);
                $sheet->cell('K' . $i, function ($cell) {
                    // anipulate the cel
                    $cell->setValue('MEDICINAS VALOR AL ORIGEN');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('P' . $i, function ($cell) use ($totalmed) {
                    // anipulate the cel
                    $cell->setValue(round($totalmed, 2));
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $i++;
                $sheet->mergeCells('A' . $i . ':P' . $i);
                $sheet->cell('A' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('INSUMOS - VALOR AL ORIGEN');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });
                $i = $i + 1;
                $sheet->cell('A' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('B' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CÓDIGO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->mergeCells('C' . $i . ':J' . $i);
                $sheet->cell('C' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DESCRIPCIÓN');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('K' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CANTIDAD');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('L' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('VALOR UNITARIO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('M' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SUBTOTAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('N' . $i, function ($cell) {
                    // anipulate the cel
                    $cell->setValue('10%');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('O' . $i, function ($cell) {
                    // anipulate the cel
                    $cell->setValue('IVA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('P' . $i, function ($cell) {
                    // anipulate the cel
                    $cell->setValue('TOTAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });

                $i = $i + 1;
                $totalins = 0;
                foreach ($insumos as $insumo) {
                    //$totalins+=$insumo->total;
                    //$totalins += (round($insumo->subtotal, 2) + round($insumo->porcentaje10, 2) + ($insumo->subtotal * $insumo->porcentaje_iva));
                    $fecha_ins = substr($insumo->fecha, 0, 10);
                    $fecha_ins_inv = date("d/m/Y", strtotime($fecha_ins));

                    $sheet->cell('A' . $i, function ($cell) use ($fecha_ins_inv) {
                        // manipulate the cel
                        $cell->setValue($fecha_ins_inv);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('B' . $i, function ($cell) use ($insumo) {
                        // manipulate the cel
                        $cell->setValue('');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->mergeCells('C' . $i . ':J' . $i);
                    $sheet->cell('C' . $i, function ($cell) use ($insumo) {
                        // manipulate the cel
                        $cell->setValue($insumo->descripcion);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('K' . $i, function ($cell) use ($insumo) {
                        // manipulate the cel
                        $cell->setValue($insumo->cantidad);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->getStyle('L' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                    $sheet->cell('L' . $i, function ($cell) use ($insumo) {
                        // manipulate the cel
                        $cell->setValue($insumo->valor_unitario);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->getStyle('M' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                    $sheet->cell('M' . $i, function ($cell)  use ($insumo) {
                        // manipulate the cel
                        $cell->setValue(round($insumo->subtotal, 2));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('N' . $i, function ($cell) use ($insumo) {
                        // manipulate the cel
                        $cell->setValue(round($insumo->porcentaje10, 2));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('O' . $i, function ($cell) use ($insumo) {
                        // manipulate the cel
                        //$cell->setValue(round($insumo->iva,2));
                        $xviva = $insumo->subtotal * $insumo->porcentaje_iva;
                        $cell->setValue($xviva);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $xvtotal = $insumo->subtotal + $insumo->porcentaje10 + ($insumo->subtotal * $insumo->porcentaje_iva);
                    $xvtotal = round($xvtotal, 2);
                    $totalins += $xvtotal;

                    $sheet->cell('P' . $i, function ($cell) use ($xvtotal) {
                        // manipulate the cel
                        //$cell->setValue(round($insumo->total,2));

                        $cell->setValue($xvtotal);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $i++;
                }
                //$i = $i;
                $sheet->mergeCells('A' . $i . ':J' . $i);
                $sheet->cell('A' . $i, function ($cell) {
                    // anipulate the cel
                    $cell->setValue('TOTAL');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->mergeCells('K' . $i . ':O' . $i);
                $sheet->cell('K' . $i, function ($cell) {
                    // anipulate the cel
                    $cell->setValue('INSUMOS - VALOR AL ORIGEN');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('P' . $i, function ($cell) use ($totalins) {
                    // anipulate the cel
                    $cell->setValue(round($totalins, 2));
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $i++;
                $sheet->mergeCells('A' . $i . ':P' . $i);
                $sheet->cell('A' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('LABORATORIO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });
                $i = $i + 1;
                $sheet->cell('A' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('B' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CÓDIGO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->mergeCells('C' . $i . ':J' . $i);
                $sheet->cell('C' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DESCRIPCIÓN');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('K' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CANTIDAD');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('L' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('VALOR UNITARIO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('M' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SUBTOTAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('N' . $i, function ($cell) {
                    // anipulate the cel
                    $cell->setValue('10%');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('O' . $i, function ($cell) {
                    // anipulate the cel
                    $cell->setValue('IVA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('P' . $i, function ($cell) {
                    // anipulate the cel
                    $cell->setValue('TOTAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $i = $i + 1;
                $totallab = 0;
                foreach ($laboratorio as $lab) {
                    $totallab += $lab->total;
                    $fecha_lab = substr($lab->fecha, 0, 10);
                    $fecha_lab_inv = date("d/m/Y", strtotime($fecha_lab));

                    $sheet->cell('A' . $i, function ($cell) use ($fecha_lab_inv) {
                        // manipulate the cel
                        $cell->setValue($fecha_lab_inv);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('B' . $i, function ($cell) use ($lab) {
                        // manipulate the cel
                        $cell->setValue($lab->codigo);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->mergeCells('C' . $i . ':J' . $i);
                    $sheet->cell('C' . $i, function ($cell) use ($lab) {
                        // manipulate the cel
                        $cell->setValue($lab->descripcion);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('K' . $i, function ($cell) use ($lab) {
                        // manipulate the cel
                        $cell->setValue($lab->cantidad);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->getStyle('L' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                    $sheet->cell('L' . $i, function ($cell) use ($lab) {
                        // manipulate the cel
                        $cell->setValue($lab->valor);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->getStyle('M' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                    $sheet->cell('M' . $i, function ($cell)  use ($lab) {
                        // manipulate the cel
                        $cell->setValue(round($lab->subtotal, 2));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('N' . $i, function ($cell) use ($lab) {
                        // manipulate the cel
                        $cell->setValue(round($lab->porcentaje10));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('O' . $i, function ($cell) use ($lab) {
                        // manipulate the cel
                        $cell->setValue(round($lab->iva, 2));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('P' . $i, function ($cell) use ($lab) {
                        // manipulate the cel
                        $cell->setValue(round($lab->total, 2));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $i++;
                }
                $i = $i;
                $sheet->mergeCells('A' . $i . ':J' . $i);
                $sheet->cell('A' . $i, function ($cell) {
                    // anipulate the cel
                    $cell->setValue('TOTAL');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->mergeCells('K' . $i . ':O' . $i);
                $sheet->cell('K' . $i, function ($cell) {
                    // anipulate the cel
                    $cell->setValue('LABORATORIO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('P' . $i, function ($cell) use ($totallab) {
                    // anipulate the cel
                    $cell->setValue(round($totallab, 2));
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $i++;
                $sheet->mergeCells('A' . $i . ':P' . $i);
                $sheet->cell('A' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('IMAGEN(*)');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });
                $i = $i + 1;
                $sheet->cell('A' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('B' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CÓDIGO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->mergeCells('C' . $i . ':J' . $i);
                $sheet->cell('C' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DESCRIPCIÓN');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('K' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CANTIDAD');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('L' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('VALOR UNITARIO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('M' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SUBTOTAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('N' . $i, function ($cell) {
                    // anipulate the cel
                    $cell->setValue('10%');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('O' . $i, function ($cell) {
                    // anipulate the cel
                    $cell->setValue('IVA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('P' . $i, function ($cell) {
                    // anipulate the cel
                    $cell->setValue('TOTAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });

                $i = $i + 1;
                $totalima = 0;
                foreach ($imagen as $ima) {
                    $totalima += $ima->total;
                    $fecha_ima = substr($ima->fecha, 0, 10);
                    $fecha_ima_inv = date("d/m/Y", strtotime($fecha_ima));
                    $sheet->cell('A' . $i, function ($cell) use ($fecha_ima_inv) {
                        // manipulate the cel
                        $cell->setValue($fecha_ima_inv);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('B' . $i, function ($cell) use ($ima) {
                        // manipulate the cel
                        $cell->setValue($ima->codigo);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->mergeCells('C' . $i . ':J' . $i);
                    $sheet->cell('C' . $i, function ($cell) use ($ima) {
                        // manipulate the cel
                        $cell->setValue($ima->descripcion);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('K' . $i, function ($cell) use ($ima) {
                        // manipulate the cel
                        $cell->setValue($ima->cantidad);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });


                    $sheet->getStyle('L' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                    $sheet->cell('L' . $i, function ($cell) use ($ima) {
                        // manipulate the cel
                        $cell->setValue($ima->valor);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->getStyle('M' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                    $sheet->cell('M' . $i, function ($cell)  use ($ima) {
                        // manipulate the cel
                        $cell->setValue(round($ima->subtotal, 2));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('N' . $i, function ($cell) use ($ima) {
                        // manipulate the cel
                        $cell->setValue(round($ima->porcentaje10, 2));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('O' . $i, function ($cell) use ($ima) {
                        // manipulate the cel
                        $cell->setValue(round($ima->iva, 2));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('P' . $i, function ($cell) use ($ima) {
                        // manipulate the cel
                        $cell->setValue(round($ima->total, 2));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $i++;
                }
                $i = $i;
                $sheet->mergeCells('A' . $i . ':J' . $i);
                $sheet->cell('A' . $i, function ($cell) {
                    // anipulate the cel
                    $cell->setValue('TOTAL');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->mergeCells('K' . $i . ':O' . $i);
                $sheet->cell('K' . $i, function ($cell) {
                    // anipulate the cel
                    $cell->setValue('IMAGEN(*)');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('P' . $i, function ($cell) use ($totalima) {
                    // anipulate the cel
                    $cell->setValue(round($totalima, 2));
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $i++;
                $sheet->mergeCells('A' . $i . ':P' . $i);
                $sheet->cell('A' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SERVICIOS INSTITUCIONALES');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });
                $i = $i + 1;
                $sheet->cell('A' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('B' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CÓDIGO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->mergeCells('C' . $i . ':J' . $i);
                $sheet->cell('C' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DESCRIPCIÓN');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('K' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CANTIDAD');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('L' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('VALOR UNITARIO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('M' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SUBTOTAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('N' . $i, function ($cell) {
                    // anipulate the cel
                    $cell->setValue('10%');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('O' . $i, function ($cell) {
                    // anipulate the cel
                    $cell->setValue('IVA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('P' . $i, function ($cell) {
                    // anipulate the cel
                    $cell->setValue('TOTAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $i = $i + 1;
                $totalserv = 0;
                foreach ($servicios_ins as $servicio) {
                    $totalserv += $servicio->total;
                    $fecha_serv = substr($servicio->fecha, 0, 10);
                    $fecha_serv_inv = date("d/m/Y", strtotime($fecha_serv));

                    $sheet->cell('A' . $i, function ($cell) use ($fecha_serv_inv) {
                        // manipulate the cel
                        $cell->setValue($fecha_serv_inv);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('B' . $i, function ($cell) use ($servicio) {
                        // manipulate the cel
                        $cell->setValue($servicio->codigo);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->mergeCells('C' . $i . ':J' . $i);
                    $sheet->cell('C' . $i, function ($cell) use ($servicio) {
                        // manipulate the cel
                        $cell->setValue($servicio->descripcion);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('K' . $i, function ($cell) use ($servicio) {
                        // manipulate the cel
                        $cell->setValue($servicio->cantidad);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->getStyle('L' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                    $sheet->cell('L' . $i, function ($cell) use ($servicio) {
                        // manipulate the cel
                        $cell->setValue($servicio->valor);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->getStyle('M' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                    $sheet->cell('M' . $i, function ($cell) use ($servicio) {
                        // manipulate the cel
                        $cell->setValue(round($servicio->subtotal, 2));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('N' . $i, function ($cell) use ($servicio) {
                        // manipulate the cel
                        $cell->setValue(round($servicio->porcentaje10, 2));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('O' . $i, function ($cell) use ($servicio) {
                        // manipulate the cel
                        $cell->setValue(round($servicio->iva, 2));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('P' . $i, function ($cell) use ($servicio) {
                        // manipulate the cel
                        $cell->setValue(round($servicio->total, 2));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $i++;
                }
                $i = $i;
                $sheet->mergeCells('A' . $i . ':J' . $i);
                $sheet->cell('A' . $i, function ($cell) {
                    // anipulate the cel
                    $cell->setValue('TOTAL');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->mergeCells('K' . $i . ':O' . $i);
                $sheet->cell('K' . $i, function ($cell) {
                    // anipulate the cel
                    $cell->setValue('SERVICIOS INSTITUCIONALES');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('P' . $i, function ($cell) use ($totalserv) {
                    // anipulate the cel
                    $cell->setValue(round($totalserv, 2));
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $i++;
                $sheet->mergeCells('A' . $i . ':P' . $i);
                $sheet->cell('A' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('EQUIPOS ESPECIALES');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });
                $i = $i + 1;
                $sheet->cell('A' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('B' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CÓDIGO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->mergeCells('C' . $i . ':J' . $i);
                $sheet->cell('C' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DESCRIPCIÓN');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('K' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CANTIDAD');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('L' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('VALOR UNITARIO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('M' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SUBTOTAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('N' . $i, function ($cell) {
                    // anipulate the cel
                    $cell->setValue('10%');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('O' . $i, function ($cell) {
                    // anipulate the cel
                    $cell->setValue('IVA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('P' . $i, function ($cell) {
                    // anipulate the cel
                    $cell->setValue('TOTAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $i = $i + 1;
                $totalequip = 0;
                foreach ($equipos as $equip) {
                    $totalequip += $equip->total;
                    $fecha_equi = substr($equip->fecha, 0, 10);
                    $fecha_equi_inv = date("d/m/Y", strtotime($fecha_equi));
                    $sheet->cell('A' . $i, function ($cell) use ($fecha_equi_inv) {
                        // manipulate the cel
                        $cell->setValue($fecha_equi_inv);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('B' . $i, function ($cell) use ($equip) {
                        // manipulate the cel
                        $cell->setValue($equip->codigo);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->mergeCells('C' . $i . ':J' . $i);
                    $sheet->cell('C' . $i, function ($cell) use ($equip) {
                        // manipulate the cel
                        $cell->setValue($equip->descripcion);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('K' . $i, function ($cell) use ($equip) {
                        // manipulate the cel
                        $cell->setValue($equip->cantidad);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->getStyle('L' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                    $sheet->cell('L' . $i, function ($cell) use ($equip) {
                        // manipulate the cel
                        $cell->setValue($equip->valor);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });


                    $sheet->getStyle('M' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                    $sheet->cell('M' . $i, function ($cell)  use ($equip) {
                        // manipulate the cel
                        $cell->setValue(round($equip->subtotal, 2));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('N' . $i, function ($cell) use ($equip) {
                        // manipulate the cel
                        $cell->setValue(round($equip->porcentaje10, 2));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('O' . $i, function ($cell) use ($equip) {
                        // manipulate the cel
                        $cell->setValue(round($equip->iva, 2));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('P' . $i, function ($cell) use ($equip) {
                        // manipulate the cel
                        $cell->setValue(round($equip->total, 2));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $i++;
                }
                $i = $i;
                $sheet->mergeCells('A' . $i . ':J' . $i);
                $sheet->cell('A' . $i, function ($cell) {
                    // anipulate the cel
                    $cell->setValue('TOTAL');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->mergeCells('K' . $i . ':O' . $i);
                $sheet->cell('K' . $i, function ($cell) {
                    // anipulate the cel
                    $cell->setValue('EQUIPOS ESPECIALES');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('P' . $i, function ($cell) use ($totalequip) {
                    // anipulate the cel
                    $cell->setValue(round($totalequip, 2));
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $i++;
                $sheet->mergeCells('A' . $i . ':O' . $i);
                $sheet->cell('A' . $i, function ($cell) {
                    // anipulate the cel
                    $cell->setValue('TOTAL LIQUIDACIÓN');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });
                $total_liq = $total + $totalmed + $totallab + $totalins + $totalserv + $totalequip + $totalima;
                $sheet->cell('P' . $i, function ($cell) use ($total_liq) {
                    // anipulate the cel
                    $cell->setValue(round($total_liq, 2));
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
            });

            /*$excel->getActiveSheet()->getColumnDimension("A")->setWidth(12)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("B")->setWidth(9)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("C")->setWidth(7)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("D")->setWidth(7)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("E")->setWidth(7)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("F")->setWidth(7)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("G")->setWidth(7)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("H")->setWidth(7)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("I")->setWidth(7)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("J")->setWidth(7)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("K")->setWidth(9)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("L")->setWidth(14)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("M")->setWidth(9)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("N")->setWidth(7)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("O")->setWidth(7)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("P")->setWidth(7)->setAutosize(false);
            $excel->getActiveSheet()->getStyle("A2:P11")->getFont()->setSize(10)->setName('Arial');
            $excel->getActiveSheet()->getStyle("A12:P100")->getFont()->setSize(8)->setName('Arial');*/


            $excel->getActiveSheet()->getStyle('C17')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('C18')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('C19')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('C20')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('C21')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('C22')->getAlignment()->setWrapText(true);
        })->export('xlsx');
    }


    /*public function planilla_individual_msp($hcid){

            $historia_clinica = Historiaclinica::find($hcid);
            $archivo_plano = Archivo_Plano_Cabecera::where('id_hc',$hcid)->first();
            $ap = Archivo_Plano_Cabecera::find($archivo_plano->id);
            $detalles = $ap->detalles;

            $txt_cie10 = null;

            $cie10 = Cie_10_3:: find($archivo_plano->cie10);
            if(is_null($cie10)){
                $cie10 = Cie_10_4:: find($archivo_plano->cie10);
                if(!is_null($cie10)){
                    $txt_cie10 = '('.$archivo_plano->cie10.') '.$cie10->descripcion;
                }
            }else{
                $txt_cie10 = '('.$archivo_plano->cie10.') '.$cie10->descripcion;
            }
            $honor_medicos= Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera',$archivo_plano->id)->join('ap_tipo_examen as apt','apt.tipo','apd.tipo')->select('apd.tipo','apt.tipo_ex','apd.fecha','apd.descripcion','apd.codigo','apd.cantidad','apd.valor','apd.subtotal','apd.porcentaje10','apd.iva','apd.total')->where('apt.tipo_ex','HME')->get();

            $medicinas= Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera',$archivo_plano->id)->join('ap_tipo_examen as apt','apt.tipo','apd.tipo')->select('apd.tipo','apt.tipo_ex','apd.fecha','apd.descripcion','apd.codigo','apd.cantidad','apd.valor','apd.subtotal','apd.porcentaje10','apd.iva','apd.total')->where('apt.tipo_ex','FAR')->get();

            $insumos= Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera',$archivo_plano->id)->join('ap_tipo_examen as apt','apt.tipo','apd.tipo')->select('apd.tipo','apt.tipo_ex','apd.fecha','apd.descripcion','apd.codigo','apd.cantidad','apd.valor','apd.subtotal','apd.porcentaje10','apd.iva','apd.total')->where('apt.tipo_ex','IMM')->get();

            $laboratorio= Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera',$archivo_plano->id)->join('ap_tipo_examen as apt','apt.tipo','apd.tipo')->select('apd.tipo','apt.tipo_ex','apd.fecha','apd.descripcion','apd.codigo','apd.cantidad','apd.valor','apd.subtotal','apd.porcentaje10','apd.iva','apd.total')->where('apt.tipo_ex','LAB')->get();

            $servicios_ins = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera',$archivo_plano->id)->join('ap_tipo_examen as apt','apt.tipo','apd.tipo')->select('apd.tipo','apt.tipo_ex','apd.fecha','apd.descripcion','apd.codigo','apd.cantidad','apd.valor','apd.subtotal','apd.porcentaje10','apd.iva','apd.total')->where('apt.tipo_ex','HOSP/QUIR')->get();

            $imagen = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera',$archivo_plano->id)->join('ap_tipo_examen as apt','apt.tipo','apd.tipo')->select('apd.tipo','apt.tipo_ex','apd.fecha','apd.descripcion','apd.codigo','apd.cantidad','apd.valor','apd.subtotal','apd.porcentaje10','apd.iva','apd.total')->where('apt.tipo_ex','IMA')->get();

            $equipos = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera',$archivo_plano->id)->join('ap_tipo_examen as apt','apt.tipo','apd.tipo')->select('apd.tipo','apt.tipo_ex','apd.fecha','apd.descripcion','apd.codigo','apd.cantidad','apd.valor','apd.subtotal','apd.porcentaje10','apd.iva','apd.total')->where('apt.tipo_ex','PRO/ESP')->get();

            Excel::create('Formato Planilla Individual ', function ($excel) use($archivo_plano, $detalles, $txt_cie10,$honor_medicos,$medicinas,$insumos,$laboratorio,$servicios_ins,$imagen,$equipos) {
                $excel->sheet(date('Y-m-d'), function ($sheet) use($archivo_plano, $detalles, $txt_cie10,$honor_medicos,$medicinas,$insumos,$laboratorio,$servicios_ins,$imagen,$equipos){
                    $sheet->mergeCells('A1:P1');
                    $sheet->mergeCells('A2:D2');
                    $sheet->cell('A2', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Nombre del Prestador');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');

                    });
                    $sheet->mergeCells('E2:P2');
                    $sheet->cell('E2', function ($cell) use($archivo_plano) {

                        $nombre_prestador ='';

                        if($archivo_plano->id_empresa =='0992704152001'){
                            $nombre_prestador = 'INSTITUTO ECUATORIANO DE ENFERMEDADES DIGESTIVAS GASTROCLINICA S.A';
                        }elseif($archivo_plano->id_empresa ='1307189140001'){
                            $nombre_prestador = 'ROBLES MEDRANDA CARLOS ANTONIO';
                        }
                        // manipulate the cel
                        $cell->setValue($nombre_prestador);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->mergeCells('A3:D3');
                    $sheet->cell('A3', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Seguro');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');

                    });
                    $sheet->mergeCells('E3:P3');
                    $sheet->cell('E3', function ($cell){
                        // manipulate the cel
                        $cell->setValue('MSP');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->mergeCells('A4:D4');
                    $sheet->cell('A4', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Nombre del Paciente');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');

                    });
                    $sheet->mergeCells('E4:P4');
                    $sheet->cell('E4', function ($cell) use($archivo_plano){
                        // manipulate the cel
                        $cell->setValue($archivo_plano->paciente->apellido1.' '.$archivo_plano->paciente->apellido2.' '.$archivo_plano->paciente->nombre1.' '.$archivo_plano->paciente->nombre2);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->mergeCells('A5:D5');
                    $sheet->cell('A5', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Cedula de Identidad');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');

                    });
                    $sheet->mergeCells('E5:P5');
                    $sheet->cell('E5', function ($cell)  use($archivo_plano){
                        // manipulate the cel
                        $cell->setValue($archivo_plano->id_paciente);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->mergeCells('A6:D6');
                    $sheet->cell('A6', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Historia Clinica');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');

                    });
                    $sheet->mergeCells('E6:P6');
                    $sheet->cell('E6', function ($cell) use($archivo_plano){
                        // manipulate the cel
                        $cell->setValue($archivo_plano->id_hc);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->mergeCells('A7:D7');
                    $sheet->cell('A7', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Fecha de Ingreso');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');

                    });
                    $sheet->mergeCells('E7:P7');
                    $sheet->cell('E7', function ($cell) use($archivo_plano) {
                        // manipulate the cel
                        $cell->setValue(substr($archivo_plano->fecha_ing,0,10));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->mergeCells('A8:D8');
                    $sheet->cell('A8', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Fecha de Egreso');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');

                    });
                    $sheet->mergeCells('E8:P8');
                    $sheet->cell('E8', function ($cell) use($archivo_plano) {
                        // manipulate the cel
                        $cell->setValue(substr($archivo_plano->fecha_alt,0,10));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->mergeCells('A9:D9');
                    $sheet->cell('A9', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Procedimiento');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');

                    });
                    $sheet->mergeCells('E9:P9');
                    $sheet->cell('E9', function ($cell) use($archivo_plano){
                        // manipulate the cel
                        $cell->setValue($archivo_plano->nom_procedimiento);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    $sheet->mergeCells('A10:D10');
                    $sheet->cell('A10', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Diagnostico');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->mergeCells('E10:P10');
                    $sheet->cell('E10', function ($cell) use($archivo_plano, $txt_cie10){
                        // manipulate the cel
                        $cell->setValue($txt_cie10);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->mergeCells('A11:P11');
                    $sheet->cell('A11', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('PLANILLA DE CARGOS DEL PROVEEDOR  (CONSULTA EXTERNA,HOSPITALIZACION Y EMERGENCIA)');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                    });
                    $sheet->mergeCells('A12:P12');
                    $sheet->cell('A12', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('HONORARIOS MEDICOS');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                    });
                    $sheet->cell('A13', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Fecha');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('B13', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Codigo');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->mergeCells('C13:J13');
                    $sheet->cell('C13', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Descripcion');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('K13', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Cantidad');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('L13', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Valor Unitario');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('M13', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Subtotal');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('N13', function ($cell) {
                        // anipulate the cel
                        $cell->setValue('10%');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('O13', function ($cell) {
                        // anipulate the cel
                        $cell->setValue('Iva');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('P13', function ($cell) {
                        // anipulate the cel
                        $cell->setValue('Total');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $i=14;$total=0;

                    foreach ($honor_medicos as $value) {
                    $total+=$value->total;
                    $sheet->cell('A'.$i, function ($cell) use($value) {
                        // manipulate the cel
                        $cell->setValue(substr($value->fecha,0,10));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('B'.$i, function ($cell) use($value){
                        // manipulate the cel
                        $cell->setValue($value->codigo);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->mergeCells('C'.$i.':J'.$i);
                    $sheet->cell('C'.$i, function ($cell) use($value){
                        // manipulate the cel
                        $cell->setValue($value->descripcion);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('K'.$i, function ($cell) use($value) {
                        // manipulate the cel
                        $cell->setValue($value->cantidad);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('L'.$i, function ($cell) use($value){
                        // manipulate the cel
                        $cell->setValue($value->valor);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('M'.$i, function ($cell)  use($value){
                        // manipulate the cel
                        $cell->setValue(round($value->subtotal,2));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('N'.$i, function ($cell) use($value){
                        // manipulate the cel
                        $cell->setValue(round($value->porcentaje10,2));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('O'.$i, function ($cell) use($value){
                        // manipulate the cel
                        $cell->setValue(round($value->iva,2));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('P'.$i, function ($cell) use($value){
                        // manipulate the cel
                        $cell->setValue(round($value->total,2));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $i++;

                    }
                    $i=$i;
                    $sheet->mergeCells('A'.$i.':J'.$i);
                    $sheet->cell('A'.$i, function ($cell) {
                        // anipulate the cel
                        $cell->setValue('Total');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->mergeCells('K'.$i.':O'.$i);
                    $sheet->cell('K'.$i, function ($cell) {
                        // anipulate the cel
                        $cell->setValue('HONORARIOS MEDICOS');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('P'.$i, function ($cell) use($total) {
                        // anipulate the cel
                        $cell->setValue(round($total,2));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $i++;
                    $sheet->mergeCells('A'.$i.':P'.$i);
                    $sheet->cell('A'.$i, function ($cell) {
                        // manipulate the cel
                        $cell->setValue('MEDICINAS VALOR AL ORIGEN');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                    });
                    $i=$i+1;
                    $sheet->cell('A'.$i, function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Fecha');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('B'.$i, function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Codigo');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->mergeCells('C'.$i.':J'.$i);
                    $sheet->cell('C'.$i, function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Descripcion');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('K'.$i, function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Cantidad');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('L'.$i, function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Valor Unitario');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('M'.$i, function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Subtotal');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('N'.$i, function ($cell) {
                        // anipulate the cel
                        $cell->setValue('10%');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('O'.$i, function ($cell) {
                        // anipulate the cel
                        $cell->setValue('Iva');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('P'.$i, function ($cell) {
                        // anipulate the cel
                        $cell->setValue('Total');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $i=$i+1;$totalmed=0;
                    foreach ($medicinas as $medicina) {
                    $totalmed+=$medicina->total;
                    $sheet->cell('A'.$i, function ($cell) use($medicina) {
                    // manipulate the cel
                    $cell->setValue(substr($medicina->fecha,0,10));
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('B'.$i, function ($cell) use($medicina) {
                        // manipulate the cel
                        $cell->setValue($medicina->codigo);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->mergeCells('C'.$i.':J'.$i);
                    $sheet->cell('C'.$i, function ($cell) use($medicina) {
                        // manipulate the cel
                        $cell->setValue($medicina->descripcion);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('K'.$i, function ($cell) use($medicina) {
                        // manipulate the cel
                        $cell->setValue($medicina->cantidad);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('L'.$i, function ($cell) use($medicina) {
                        // manipulate the cel
                        $cell->setValue($medicina->valor);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('M'.$i, function ($cell) use($medicina) {
                        // manipulate the cel
                        $cell->setValue(round($medicina->subtotal,2));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('N'.$i, function ($cell) use($medicina) {
                        // manipulate the cel
                        $cell->setValue(round($medicina->porcentaje10,2));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('O'.$i, function ($cell) use($medicina) {
                        // manipulate the cel
                        $cell->setValue(round($medicina->iva,2));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('P'.$i, function ($cell) use($medicina) {
                        // manipulate the cel
                        $cell->setValue(round($medicina->total,2));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $i++;

                    }
                    $i=$i;
                    $sheet->mergeCells('A'.$i.':J'.$i);
                    $sheet->cell('A'.$i, function ($cell) {
                        // anipulate the cel
                        $cell->setValue('Total');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->mergeCells('K'.$i.':O'.$i);
                    $sheet->cell('K'.$i, function ($cell) {
                        // anipulate the cel
                        $cell->setValue('MEDICINAS VALOR AL ORIGEN');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('P'.$i, function ($cell) use($totalmed) {
                        // anipulate the cel
                        $cell->setValue(round($totalmed,2));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $i++;
                    $sheet->mergeCells('A'.$i.':P'.$i);
                    $sheet->cell('A'.$i, function ($cell) {
                        // manipulate the cel
                        $cell->setValue('INSUMOS - VALOR AL ORIGEN');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                    });
                    $i=$i+1;
                    $sheet->cell('A'.$i, function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Fecha');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('B'.$i, function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Codigo');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->mergeCells('C'.$i.':J'.$i);
                    $sheet->cell('C'.$i, function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Descripcion');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('K'.$i, function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Cantidad');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('L'.$i, function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Valor Unitario');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('M'.$i, function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Subtotal');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('N'.$i, function ($cell) {
                        // anipulate the cel
                        $cell->setValue('10%');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('O'.$i, function ($cell) {
                        // anipulate the cel
                        $cell->setValue('Iva');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('P'.$i, function ($cell) {
                        // anipulate the cel
                        $cell->setValue('Total');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $i=$i+1;$totalins=0;
                    foreach ($insumos as $insumo) {
                    $totalins+=$insumo->total;
                    $sheet->cell('A'.$i, function ($cell) use($insumo) {
                    // manipulate the cel
                    $cell->setValue(substr($insumo->fecha,0,10));
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('B'.$i, function ($cell) use($insumo) {
                        // manipulate the cel
                        $cell->setValue($insumo->codigo);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->mergeCells('C'.$i.':J'.$i);
                    $sheet->cell('C'.$i, function ($cell) use($insumo) {
                        // manipulate the cel
                        $cell->setValue($insumo->descripcion);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('K'.$i, function ($cell) use($insumo) {
                        // manipulate the cel
                        $cell->setValue($insumo->cantidad);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('L'.$i, function ($cell) use($insumo) {
                        // manipulate the cel
                        $cell->setValue($insumo->valor);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('M'.$i, function ($cell)  use($insumo){
                        // manipulate the cel
                        $cell->setValue(round($insumo->subtotal));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('N'.$i, function ($cell) use($insumo) {
                        // manipulate the cel
                        $cell->setValue(round($insumo->iva,2));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('O'.$i, function ($cell) use($insumo) {
                        // manipulate the cel
                        $cell->setValue(round($insumo->iva,2));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('P'.$i, function ($cell) use($insumo) {
                        // manipulate the cel
                        $cell->setValue(round($insumo->total,2));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $i++;

                    }
                    $i=$i;
                    $sheet->mergeCells('A'.$i.':J'.$i);
                    $sheet->cell('A'.$i, function ($cell) {
                        // anipulate the cel
                        $cell->setValue('Total');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->mergeCells('K'.$i.':O'.$i);
                    $sheet->cell('K'.$i, function ($cell) {
                        // anipulate the cel
                        $cell->setValue('INSUMOS - VALOR AL ORIGEN');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('P'.$i, function ($cell) use($totalins) {
                        // anipulate the cel
                        $cell->setValue(round($totalins,2));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $i++;
                    $sheet->mergeCells('A'.$i.':P'.$i);
                    $sheet->cell('A'.$i, function ($cell) {
                        // manipulate the cel
                        $cell->setValue('LABORATORIO');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                    });
                    $i=$i+1;
                    $sheet->cell('A'.$i, function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Fecha');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('B'.$i, function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Codigo');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->mergeCells('C'.$i.':J'.$i);
                    $sheet->cell('C'.$i, function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Descripcion');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('K'.$i, function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Cantidad');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('L'.$i, function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Valor Unitario');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('M'.$i, function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Subtotal');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('N'.$i, function ($cell) {
                        // anipulate the cel
                        $cell->setValue('10%');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('O'.$i, function ($cell) {
                        // anipulate the cel
                        $cell->setValue('Iva');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('P'.$i, function ($cell) {
                        // anipulate the cel
                        $cell->setValue('Total');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $i=$i+1;$totallab=0;
                    foreach ($laboratorio as $lab) {
                    $totallab+=$lab->total;
                    $sheet->cell('A'.$i, function ($cell) use($lab) {
                    // manipulate the cel
                    $cell->setValue(substr($lab->fecha,0,10));
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('B'.$i, function ($cell) use($lab) {
                        // manipulate the cel
                        $cell->setValue($lab->codigo);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->mergeCells('C'.$i.':J'.$i);
                    $sheet->cell('C'.$i, function ($cell) use($lab) {
                        // manipulate the cel
                        $cell->setValue($lab->descripcion);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('K'.$i, function ($cell) use($lab) {
                        // manipulate the cel
                        $cell->setValue($lab->cantidad);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('L'.$i, function ($cell) use($lab) {
                        // manipulate the cel
                        $cell->setValue($lab->valor);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('M'.$i, function ($cell)  use($lab){
                        // manipulate the cel
                        $cell->setValue(round($lab->subtotal));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('N'.$i, function ($cell) use($lab) {
                        // manipulate the cel
                        $cell->setValue(round($lab->porcentaje10));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('O'.$i, function ($cell) use($lab) {
                        // manipulate the cel
                        $cell->setValue(round($lab->iva,2));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('P'.$i, function ($cell) use($lab) {
                        // manipulate the cel
                        $cell->setValue(round($lab->total,2));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $i++;
                    }
                    $i=$i;
                    $sheet->mergeCells('A'.$i.':J'.$i);
                    $sheet->cell('A'.$i, function ($cell) {
                        // anipulate the cel
                        $cell->setValue('Total');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->mergeCells('K'.$i.':O'.$i);
                    $sheet->cell('K'.$i, function ($cell) {
                        // anipulate the cel
                        $cell->setValue('LABORATORIO');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('P'.$i, function ($cell) use($totallab){
                        // anipulate the cel
                        $cell->setValue(round($totallab,2));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $i++;
                    $sheet->mergeCells('A'.$i.':P'.$i);
                    $sheet->cell('A'.$i, function ($cell) {
                        // manipulate the cel
                        $cell->setValue('IMAGEN(*)');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                    });
                    $i=$i+1;
                    $sheet->cell('A'.$i, function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Fecha');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('B'.$i, function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Codigo');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->mergeCells('C'.$i.':J'.$i);
                    $sheet->cell('C'.$i, function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Descripcion');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('K'.$i, function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Cantidad');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('L'.$i, function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Valor Unitario');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('M'.$i, function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Subtotal');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('N'.$i, function ($cell) {
                        // anipulate the cel
                        $cell->setValue('10%');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('O'.$i, function ($cell) {
                        // anipulate the cel
                        $cell->setValue('Iva');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('P'.$i, function ($cell) {
                        // anipulate the cel
                        $cell->setValue('Total');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });

                    $i=$i+1;$totalima=0;
                    foreach($imagen as $ima){
                    $totalima+=$ima->total;
                    $sheet->cell('A'.$i, function ($cell) use($ima){
                    // manipulate the cel
                    $cell->setValue(substr($ima->fecha,0,10));
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('B'.$i, function ($cell) use($ima){
                        // manipulate the cel
                        $cell->setValue($ima->codigo);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->mergeCells('C'.$i.':J'.$i);
                    $sheet->cell('C'.$i, function ($cell) use($ima){
                        // manipulate the cel
                        $cell->setValue($ima->descripcion);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('K'.$i, function ($cell) use($ima){
                        // manipulate the cel
                        $cell->setValue($ima->cantidad);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('L'.$i, function ($cell) use($ima){
                        // manipulate the cel
                        $cell->setValue($ima->valor);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('M'.$i, function ($cell)  use($ima){
                        // manipulate the cel
                        $cell->setValue(round($ima->subtotal,2));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('N'.$i, function ($cell) use($ima){
                        // manipulate the cel
                        $cell->setValue(round($ima->porcentaje10,2));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('O'.$i, function ($cell) use($ima){
                        // manipulate the cel
                        $cell->setValue(round($ima->iva,2));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('P'.$i, function ($cell) use($ima){
                        // manipulate the cel
                        $cell->setValue(round($ima->total,2));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $i++;
                    }
                    $i=$i;
                    $sheet->mergeCells('A'.$i.':J'.$i);
                    $sheet->cell('A'.$i, function ($cell) {
                        // anipulate the cel
                        $cell->setValue('Total');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->mergeCells('K'.$i.':O'.$i);
                    $sheet->cell('K'.$i, function ($cell) {
                        // anipulate the cel
                        $cell->setValue('IMAGEN(*)');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('P'.$i, function ($cell) use($totalima){
                        // anipulate the cel
                        $cell->setValue(round($totalima,2));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $i++;
                    $sheet->mergeCells('A'.$i.':P'.$i);
                    $sheet->cell('A'.$i, function ($cell) {
                        // manipulate the cel
                        $cell->setValue('SERVICIOS INSTITUCIONALES');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                    });
                    $i=$i+1;
                    $sheet->cell('A'.$i, function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Fecha');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('B'.$i, function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Codigo');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->mergeCells('C'.$i.':J'.$i);
                    $sheet->cell('C'.$i, function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Descripcion');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('K'.$i, function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Cantidad');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('L'.$i, function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Valor Unitario');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('M'.$i, function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Subtotal');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('N'.$i, function ($cell) {
                        // anipulate the cel
                        $cell->setValue('10%');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('O'.$i, function ($cell) {
                        // anipulate the cel
                        $cell->setValue('Iva');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('P'.$i, function ($cell) {
                        // anipulate the cel
                        $cell->setValue('Total');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $i=$i+1;$totalserv=0;
                    foreach ($servicios_ins as $servicio) {
                    $totalserv+=$servicio->total;
                    $sheet->cell('A'.$i, function ($cell) use($servicio) {
                    // manipulate the cel
                    $cell->setValue(substr($servicio->fecha,0,10));
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('B'.$i, function ($cell) use($servicio) {
                        // manipulate the cel
                        $cell->setValue($servicio->codigo);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->mergeCells('C'.$i.':J'.$i);
                    $sheet->cell('C'.$i, function ($cell) use($servicio) {
                        // manipulate the cel
                        $cell->setValue($servicio->descripcion);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('K'.$i, function ($cell) use($servicio) {
                        // manipulate the cel
                        $cell->setValue($servicio->cantidad);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('L'.$i, function ($cell) use($servicio) {
                        // manipulate the cel
                        $cell->setValue($servicio->valor);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('M'.$i, function ($cell) use($servicio) {
                        // manipulate the cel
                        $cell->setValue(round($servicio->subtotal,2));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('N'.$i, function ($cell) use($servicio) {
                        // manipulate the cel
                        $cell->setValue(round($servicio->porcentaje10,2));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('O'.$i, function ($cell) use($servicio) {
                        // manipulate the cel
                        $cell->setValue(round($servicio->iva,2));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('P'.$i, function ($cell) use($servicio) {
                        // manipulate the cel
                        $cell->setValue(round($servicio->total,2));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $i++;
                    }
                    $i=$i;
                    $sheet->mergeCells('A'.$i.':J'.$i);
                    $sheet->cell('A'.$i, function ($cell) {
                        // anipulate the cel
                        $cell->setValue('Total');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->mergeCells('K'.$i.':O'.$i);
                    $sheet->cell('K'.$i, function ($cell) {
                        // anipulate the cel
                        $cell->setValue('SERVICIOS INSTITUCIONALES');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('P'.$i, function ($cell) use($totalserv){
                        // anipulate the cel
                        $cell->setValue(round($totalserv,2));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $i++;
                    $sheet->mergeCells('A'.$i.':P'.$i);
                    $sheet->cell('A'.$i, function ($cell) {
                        // manipulate the cel
                        $cell->setValue('EQUIPOS ESPECIALES');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                    });
                    $i=$i+1;
                    $sheet->cell('A'.$i, function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Fecha');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('B'.$i, function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Codigo');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->mergeCells('C'.$i.':J'.$i);
                    $sheet->cell('C'.$i, function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Descripcion');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('K'.$i, function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Cantidad');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('L'.$i, function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Valor Unitario');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('M'.$i, function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Subtotal');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('N'.$i, function ($cell) {
                        // anipulate the cel
                        $cell->setValue('10%');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('O'.$i, function ($cell) {
                        // anipulate the cel
                        $cell->setValue('Iva');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('P'.$i, function ($cell) {
                        // anipulate the cel
                        $cell->setValue('Total');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $i=$i+1;$totalequip=0;
                    foreach($equipos as $equip){
                    $totalequip+=$equip->total;
                    $sheet->cell('A'.$i, function ($cell) use($equip) {
                    // manipulate the cel
                    $cell->setValue(substr($equip->fecha,0,10));
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('B'.$i, function ($cell) use($equip) {
                        // manipulate the cel
                        $cell->setValue($equip->codigo);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->mergeCells('C'.$i.':J'.$i);
                    $sheet->cell('C'.$i, function ($cell) use($equip) {
                        // manipulate the cel
                        $cell->setValue($equip->descripcion);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('K'.$i, function ($cell) use($equip) {
                        // manipulate the cel
                        $cell->setValue($equip->cantidad);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('L'.$i, function ($cell) use($equip) {
                        // manipulate the cel
                        $cell->setValue($equip->valor);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('M'.$i, function ($cell)  use($equip){
                        // manipulate the cel
                        $cell->setValue(round($equip->subtotal,2));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('N'.$i, function ($cell) use($equip) {
                        // manipulate the cel
                        $cell->setValue(round($equip->porcentaje10,2));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('O'.$i, function ($cell) use($equip) {
                        // manipulate the cel
                        $cell->setValue(round($equip->iva,2));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('P'.$i, function ($cell) use($equip) {
                        // manipulate the cel
                        $cell->setValue(round($equip->total,2));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $i++;
                    }
                    $i=$i;
                    $sheet->mergeCells('A'.$i.':J'.$i);
                    $sheet->cell('A'.$i, function ($cell) {
                        // anipulate the cel
                        $cell->setValue('Total');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->mergeCells('K'.$i.':O'.$i);
                    $sheet->cell('K'.$i, function ($cell) {
                        // anipulate the cel
                        $cell->setValue('EQUIPOS ESPECIALES');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $sheet->cell('P'.$i, function ($cell) use($totalequip){
                        // anipulate the cel
                        $cell->setValue(round($totalequip,2));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                    $i++;
                    $sheet->mergeCells('A'.$i.':O'.$i);
                    $sheet->cell('A'.$i, function ($cell) {
                        // anipulate the cel
                        $cell->setValue('TOTAL LIQUIDACION');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                    });
                    $total_liq=$total+$totalmed+$totallab+$totalins+$totalserv+$totalequip+$totalima;
                    $sheet->cell('P'.$i, function ($cell) use($total_liq) {
                        // anipulate the cel
                        $cell->setValue(round($total_liq,2));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                    });
                });
            })->export('xlsx');



    }*/


    public function planilla_cargo_individual_msp($id_cab)
    {

        $fecha_elaboracion = date('d/m/Y');


        //dd($id_cab);


        //$archivo_plano = Archivo_Plano_Cabecera::where('id_hc',$hcid)->first();
        $archivo_plano = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.id', $id_cab)
            ->where('archivo_plano_cabecera.estado', '1')
            ->first();
        $ap = Archivo_Plano_Cabecera::find($archivo_plano->id);
        $detalles = $ap->detalles;


        $historia_clinica = Historiaclinica::find($archivo_plano->id_hc);
        $agend = agenda::find($historia_clinica->id_agenda);


        //Detalle de Servicio Desde
        $fecha_desde = substr($archivo_plano->fecha_ing, 0, 10);
        $invert_fech_desde = explode('-', $fecha_desde);
        $fecha_desde_invert = $invert_fech_desde[2] . "/" . $invert_fech_desde[1] . "/" . $invert_fech_desde[0];

        //Detalle de Servicio Hasta
        $fecha_hasta = substr($archivo_plano->fecha_alt, 0, 10);
        $invert_fech_hasta = explode('-', $fecha_hasta);
        $fecha_hasta_invert = $invert_fech_hasta[2] . "/" . $invert_fech_hasta[1] . "/" . $invert_fech_hasta[0];

        //Anio servicio
        $anio_servicio = $invert_fech_desde[0];

        //Mes Servicio
        $mes_servicio = $invert_fech_desde[1];

        //Obtenemos el mes
        $txt_mes = '';
        if ($mes_servicio == '12') {
            $txt_mes = 'DICIEMBRE';
        } elseif ($mes_servicio == '11') {
            $txt_mes = 'NOVIEMBRE';
        } elseif ($mes_servicio == '10') {
            $txt_mes = 'OCTUBRE';
        } elseif ($mes_servicio == '09') {
            $txt_mes = 'SEPTIEMBRE';
        } elseif ($mes_servicio == '08') {
            $txt_mes = 'AGOSTO';
        } elseif ($mes_servicio == '07') {
            $txt_mes = 'JULIO';
        } elseif ($mes_servicio == '06') {
            $txt_mes = 'JUNIO';
        } elseif ($mes_servicio == '05') {
            $txt_mes = 'MAYO';
        } elseif ($mes_servicio == '04') {
            $txt_mes = 'ABRIL';
        } elseif ($mes_servicio == '03') {
            $txt_mes = 'MARZO';
        } elseif ($mes_servicio == '02') {
            $txt_mes = 'FEBRERO';
        } elseif ($mes_servicio == '01') {
            $txt_mes = 'ENERO';
        }


        $mes_anio_servicio = $txt_mes . "-" . $anio_servicio;

        $txt_cie10 = null;

        $cie10 = Cie_10_3::find($archivo_plano->cie10);
        if (is_null($cie10)) {
            $cie10 = Cie_10_4::find($archivo_plano->cie10);
            if (!is_null($cie10)) {
                $txt_cie10 = '(' . $archivo_plano->cie10 . ') ' . $cie10->descripcion;
            }
        } else {
            $txt_cie10 = '(' . $archivo_plano->cie10 . ') ' . $cie10->descripcion;
        }

        //$planilla_msp_detalle = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera',$archivo_plano->id)->join('ap_tipo_examen as apt','apt.tipo','apd.tipo')->select('apd.fecha','apd.codigo','apd.descripcion','apt.clasificado','apd.cantidad','apd.valor','apd.subtotal_msp','apd.valor_modificador_msp','apd.total_solicitado_msp','apd.clasif_porcentaje_msp')->get();
        $planilla_msp_detalle = Db::table('archivo_plano_detalle as apd')
            ->where('apd.id_ap_cabecera', $archivo_plano->id)
            ->where('apd.estado', '1')
            ->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')
            ->select('apd.fecha', 'apd.codigo', 'apd.descripcion', 'apt.clasificado', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.clasif_porcentaje_msp', 'apd.valor_porcent_clasifi', 'apd.total_solicitado_usd', 'apd.tipo', 'apd.clasificador', 'apd.clasif_porcentaje_msp', 'apd.valor_unitario', 'apd.porcentaje_iva', 'apd.porcent_10', 'apd.iva_unitario', 'apd.p10_unitario','apd.total')
            ->orderBy('apt.orden_msp','asc')
            ->orderBy('apd.id', 'asc')
            ->get();

        //dd($planilla_msp_detalle);

        //Calcula Valor Total al Inicio
        $total_inicio = 0;
        //$total_sala = 0;
        //$total_ind = 0;
        foreach ($planilla_msp_detalle as $value) {

            /*if($value->clasificado == 'SA04-30'){
                $total_sala+= $value->total_solicitado_usd;
            }else{
                $total_ind+= $value->total;
            }*/

            $total_inicio += $value->total_solicitado_usd;
        }

        //$total_inicio = number_format((($total_sala)+($total_ind)),2);


        Excel::create('Planilla MSP ', function ($excel) use ($agend, $archivo_plano, $detalles, $txt_cie10, $fecha_desde_invert, $fecha_hasta_invert, $fecha_elaboracion, $mes_anio_servicio, $planilla_msp_detalle, $total_inicio) {
            $excel->sheet('Planilla MSP', function ($sheet) use ($agend, $archivo_plano, $detalles, $txt_cie10, $fecha_desde_invert, $fecha_hasta_invert, $fecha_elaboracion, $mes_anio_servicio, $planilla_msp_detalle, $total_inicio) {

                /*$sheet->mergeCells('A1:Q1');
                $sheet->cell('A1', function($cell){
                    // manipulate the cel
                    $cell->setValue('GASTROCLINICA');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('14');
                });*/
                $sheet->mergeCells('B2:Q2');
                $sheet->cell('B2', function ($cell) use ($archivo_plano) {
                    // manipulate the cel
                    //$nombre_comercial ='';

                    /*if($archivo_plano->id_empresa =='0992704152001'){
                        $nombre_comercial = 'INSTITUTO ECUATORIANO DE ENFERMEDADES DIGESTIVAS GASTROCLINICA S.A';
                    }elseif($archivo_plano->id_empresa ='1307189140001'){
                        $nombre_comercial = 'ROBLES MEDRANDA CARLOS ANTONIO';
                    }*/
                    $cell->setValue($archivo_plano->empresa->razonsocial);
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                });

                $sheet->mergeCells('B3:Q3');
                $sheet->cell('B3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PLANILLA DE CARGOS INDIVIDUAL');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('B5:E5');
                $sheet->cell('B5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Tipo Servicio:');
                    $cell->setFontSize('12');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', '', '', 'thin');
                });

                $sheet->mergeCells('F5:K5');
                $sheet->cell('F5', function ($cell) use ($agend) {
                    // manipulate the cel
                    //$tipo_servicio ='';

                    /*if($agend->est_amb_hos=='0'){
                       $tipo_servicio = 'AMBULATORIO';
                    }elseif($agend->est_amb_hos=='1'){
                        $tipo_servicio = 'HOSPITALIZADO';
                    }*/

                    //$cell->setValue($tipo_servicio);
                    $cell->setValue('AMBULATORIO');
                    $cell->setAlignment('left');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', '', '', '');
                });
                $sheet->mergeCells('L5:N5');
                $sheet->cell('L5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Fecha de Elaboración:');
                    $cell->setFontSize('12');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', '', '', '');
                });
                $sheet->mergeCells('O5:Q5');
                $sheet->cell('O5', function ($cell) use ($fecha_elaboracion) {
                    // manipulate the cel
                    $cell->setValue($fecha_elaboracion);
                    $cell->setAlignment('left');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', '', '');
                });
                $sheet->mergeCells('B6:E6');
                $sheet->cell('B6', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Monto Solicitado:');
                    $cell->setFontSize('12');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('', '', '', 'thin');
                });
                $sheet->mergeCells('F6:K6');
                $sheet->getStyle('F6')->getNumberFormat()->setFormatCode('$ 0.00');
                $sheet->cell('F6', function ($cell) use ($total_inicio) {
                    // manipulate the cel
                    //$valor_inicio = number_format($total_inicio, 2);
                    $cell->setValue($total_inicio);
                    $cell->setAlignment('left');
                    $cell->setFontSize('10');
                    $cell->setBorder('', '', '', '');
                });
                $sheet->mergeCells('L6:N6');
                $sheet->cell('L6', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Mes y Año de Servicio:');
                    $cell->setFontSize('12');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('', '', '', '');
                });

                $sheet->mergeCells('O6:Q6');
                $sheet->cell('O6', function ($cell) use ($mes_anio_servicio) {
                    // manipulate the cel
                    $cell->setValue($mes_anio_servicio);
                    $cell->setFontSize('10');
                    $cell->setAlignment('left');
                    $cell->setBorder('', 'thin', '', '');
                });

                $sheet->mergeCells('B7:E7');
                $sheet->cell('B7', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Nro. CV:');
                    $cell->setFontSize('12');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('', '', '', 'thin');
                });

                $sheet->mergeCells('F7:K7');
                $sheet->cell('F7', function ($cell) use ($archivo_plano) {
                    // manipulate the cel
                    $cell->setValue($archivo_plano->cod_deriva_msp);
                    $cell->setAlignment('left');
                    $cell->setFontSize('10');
                    $cell->setBorder('', '', '', '');
                });
                $sheet->mergeCells('L7:N7');
                $sheet->cell('L7', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CIE10:');
                    $cell->setFontSize('12');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('', '', '', '');
                });
                $sheet->mergeCells('O7:Q7');
                $sheet->cell('O7', function ($cell) use ($archivo_plano) {
                    // manipulate the cel
                    $cell->setValue($archivo_plano->cie10);
                    $cell->setFontSize('10');
                    $cell->setAlignment('left');
                    $cell->setBorder('', 'thin', '', '');
                });

                $sheet->mergeCells('B8:E8');
                $sheet->cell('B8', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CC No:');
                    $cell->setFontSize('12');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('', '', '', 'thin');
                });

                $sheet->mergeCells('F8:K8');
                $sheet->cell('F8', function ($cell) use ($archivo_plano) {
                    // manipulate the cel
                    $cell->setValue($archivo_plano->id_paciente);
                    $cell->setAlignment('left');
                    $cell->setFontSize('10');
                    $cell->setBorder('', '', '', '');
                });
                $sheet->mergeCells('L8:N8');
                $sheet->cell('L8', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    //$cell->setFontSize('12');
                    //$cell->setFontWeight('bold');
                    $cell->setBorder('', '', '', '');
                });
                $sheet->mergeCells('O8:Q8');
                $sheet->cell('O8', function ($cell) use ($archivo_plano) {
                    // manipulate the cel
                    $cell->setValue('');
                    //$cell->setFontSize('10');
                    //$cell->setAlignment('left');
                    $cell->setBorder('', 'thin', '', '');
                });

                $sheet->mergeCells('B9:E9');
                $sheet->cell('B9', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Beneficiario:');
                    $cell->setFontSize('12');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('', '', '', 'thin');
                });

                $sheet->mergeCells('F9:K9');
                $sheet->cell('F9', function ($cell) use ($archivo_plano) {
                    // manipulate the cel
                    $cell->setValue($archivo_plano->paciente->apellido1 . ' ' . $archivo_plano->paciente->apellido2 . ' ' . $archivo_plano->paciente->nombre1 . ' ' . $archivo_plano->paciente->nombre2);
                    $cell->setFontSize('10');
                    $cell->setAlignment('left');
                    $cell->setBorder('', '', '', '');
                });

                $sheet->mergeCells('L9:N9');
                $sheet->cell('L9', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    //$cell->setFontSize('12');
                    //$cell->setFontWeight('bold');
                    $cell->setBorder('', '', '', '');
                });
                $sheet->mergeCells('O9:Q9');
                $sheet->cell('O9', function ($cell) use ($archivo_plano) {
                    // manipulate the cel
                    $cell->setValue('');
                    //$cell->setFontSize('10');
                    //$cell->setAlignment('left');
                    $cell->setBorder('', 'thin', '', '');
                });

                $sheet->mergeCells('B10:E10');
                $sheet->cell('B10', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Detalle de Servicio Desde:');
                    $cell->setFontSize('12');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('', '', '', 'thin');
                });
                $sheet->mergeCells('F10:K10');
                $sheet->cell('F10', function ($cell) use ($fecha_desde_invert) {
                    // manipulate the cel
                    $cell->setValue($fecha_desde_invert);
                    $cell->setAlignment('left');
                    $cell->setFontSize('10');
                    $cell->setBorder('', '', '', '');
                });
                $sheet->mergeCells('L10:N10');
                $sheet->cell('L10', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Hasta:');
                    $cell->setFontSize('12');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('', '', '', '');
                });
                $sheet->mergeCells('O10:Q10');
                $sheet->cell('O10', function ($cell) use ($fecha_hasta_invert) {
                    // manipulate the cel
                    $cell->setValue($fecha_hasta_invert);
                    $cell->setFontSize('10');
                    $cell->setAlignment('left');
                    $cell->setBorder('', 'thin', '', '');
                });

                $sheet->mergeCells('B11:C11');
                $sheet->cell('B11', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Fecha');

                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D11', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Código TSNS');

                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E11', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Clasificador');

                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('F11:K11');
                $sheet->cell('F11', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Descripción');

                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('L11', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Cantidad #');

                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('M11', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Valor Unitario USD');

                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('N11', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Clasificador %');

                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('O11', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Subtotal USD');

                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('P11', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Valor por Modificador USD');

                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('Q11', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Total Solicitado USD');

                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->setColumnFormat(array(
                    'M' => '0.00',
                    'N' => '0.00',
                    'O' => '0.00',
                    'P' => '0.00',
                    'Q' => '0.00',
                ));

                //Mostrar Tabla Detalle
                $i = 12;
                $j = 0;
                $k = 0;
                $l = 0;
                $m = 0;
                $n = 0;
                $p = 0;
                $q = 0;
                $r = 0;
                $s = 0;
                $t = 0;

                $total = 0;
                //$total_sala = 0;
                //$total_ind = 0;
                foreach ($planilla_msp_detalle as $key => $value) {

                    //dd($planilla_msp_detalle);

                    //Invertir Fecha
                    $invert_fecha = substr($value->fecha, 0, 10);
                    $invert_fech_deta = explode('-', $invert_fecha);
                    $invert_fech_detalle = $invert_fech_deta[2] . "/" . $invert_fech_deta[1] . "/" . $invert_fech_deta[0];

                    $sheet->mergeCells('B' . $i . ':C' . $i);
                    $sheet->cell('B' . $i, function ($cell) use ($invert_fech_detalle) {
                        // manipulate the cel
                        $cell->setValue($invert_fech_detalle);
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('D' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        if ($value->tipo == 'IV' || $value->tipo == 'I' || $value->tipo == 'M' || $value->tipo == 'IVC') {
                            $cell->setValue(' ');
                        } else {
                            $cell->setValue($value->codigo);
                        }
                        if ($value->codigo == '16') {
                            $ap_procedimiento = ApProcedimiento::where('codigo', $value->codigo)->where('tipo', $value->tipo)->first();
                            if (!is_null($ap_procedimiento)) {
                                $cell->setValue($ap_procedimiento->cod_cb);
                            }
                        }

                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('E' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->clasificador);
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->mergeCells('F' . $i . ':K' . $i);
                    $sheet->cell('F' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->descripcion);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('L' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->cantidad);
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });


                    $sheet->getStyle('M' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                    $sheet->cell('M' . $i, function ($cell) use ($value, $archivo_plano, $key) {

                        /*if ($value->clasificador == 'SA09-38') {

                            // = $value->total_solicitado_usd / $value->cantidad;
                            //$vfinal = round($vfinal, 2);
                            $vfinal = $value->valor * ( 1 + $value->porcentaje_iva);
                            $vfinal = round($vfinal,2);

                            $cell->setValue($vfinal);
                        } else {
                            if ($value->clasif_porcentaje_msp == '50') {

                                $valor_real = (($value->valor) * (2));
                                //$valor = number_format($valor_real, 4);
                                //$cell->setValue($valor);
                                $cell->setValue($valor_real);
                            } else {
                                $cell->setValue($value->valor);
                            }
                        }*/
                        $valor_unitario = $value->valor_unitario + $value->p10_unitario + $value->iva_unitario;
                        $valor_unitario = round($valor_unitario, 2);

                        $cell->setValue($valor_unitario);
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('N' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        //dd($value->clasif_porcentaje_msp);
                        if ($value->clasif_porcentaje_msp != 0) {
                            $valor = round($value->clasif_porcentaje_msp, 2);
                        } else {
                            $valor = round($value->clasif_porcentaje_msp, 2);
                        }
                        $cell->setValue($valor);
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->getStyle('O' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                    $sheet->cell('O' . $i, function ($cell)  use ($value, $archivo_plano) {
                        // manipulate the cel
                        /*
                        if ($value->clasificador == 'SA09-38') {

                            $vfinal = $value->total_solicitado_usd;
                            $vfinal = round($vfinal, 2);

                            $cell->setValue($vfinal);
                        } else {

                            $valor = round($value->valor * $value->cantidad, 2);
                            $cell->setValue($valor);
                        }*/

                        $cell->setValue($value->total);

                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('P' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        if ($value->valor_porcent_clasifi != 0) {
                            $valor = round($value->valor_porcent_clasifi, 2);
                        } else {
                            $valor = round($value->valor_porcent_clasifi, 2);
                        }
                        $cell->setValue($valor);
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->getStyle('Q' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                    $sheet->cell('Q' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        /*$valor ='';
                        if($value->clasificado == 'SA04-30'){
                            $valor = round($value->total_solicitado_usd,2);
                        }else{
                            $valor = round($value->total,2);
                        }*/

                        if ($value->total_solicitado_usd != 0) {
                            $valor = round($value->total_solicitado_usd, 2);
                        } else {
                            $valor = round($value->total_solicitado_usd, 2);
                        }

                        $cell->setValue($valor);
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    /*if($value->clasificado == 'SA04-30'){
                        $total_sala+= $value->total_solicitado_usd;
                    }else{
                        $total_ind+= $value->total;
                    }*/

                    $total += $value->total_solicitado_usd;

                    $i++;
                }

                //$total = (($total_sala)+($total_ind));

                $i = $i;

                $sheet->mergeCells('B' . $i . ':P' . $i);
                $sheet->cell('B' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Valor Total Solicitado');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->getStyle('Q' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                $sheet->cell('Q' . $i, function ($cell) use ($total) {
                    // manipulate the cel
                    //$valor_total = number_format($total, 4);
                    //$cell->setValue($valor_total);
                    $cell->setValue($total);
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $j = $i + 2;

                $sheet->mergeCells('B' . $j . ':Q' . $j);
                $sheet->cell('B' . $j, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', '', 'thin');
                });

                $k = $j + 1;

                $sheet->mergeCells('B' . $k . ':Q' . $k);
                $sheet->cell('B' . $k, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('', 'thin', '', 'thin');
                });

                $l = $k + 1;
                $sheet->mergeCells('B' . $l . ':Q' . $l);
                $sheet->cell('B' . $l, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('', 'thin', '', 'thin');
                });

                $m = $l + 1;
                $sheet->mergeCells('B' . $m . ':Q' . $m);
                $sheet->cell('B' . $m, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('', 'thin', '', 'thin');
                });

                $n = $m + 1;
                $sheet->mergeCells('B' . $n . ':Q' . $n);
                $sheet->cell('B' . $n, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('', 'thin', '', 'thin');
                });

                $p = $n + 1;
                $sheet->mergeCells('B' . $p . ':Q' . $p);
                $sheet->cell('B' . $p, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Firma:');
                    $cell->setBorder('', 'thin', '', 'thin');
                });

                $q = $p + 1;
                $sheet->cell('B' . $q, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Nombre:');
                    $cell->setBorder('', '', '', 'thin');
                });

                $sheet->mergeCells('C' . $q . ':F' . $q);
                $sheet->cell('C' . $q, function ($cell) use ($archivo_plano) {
                    // manipulate the cel
                    //$cell->setValue($archivo_plano->paciente->apellido1.' '.$archivo_plano->paciente->apellido2.' '.$archivo_plano->paciente->nombre1.' '.$archivo_plano->paciente->nombre2);
                    $cell->setValue('DRA. ANDREA LORENA ESTRELLA HIDALGO');
                    $cell->setBorder('', '', 'hair', '');
                });

                $sheet->mergeCells('G' . $q . ':Q' . $q);
                $sheet->cell('G' . $q, function ($cell) {
                    // manipulate the cel
                    $cell->setBorder('', 'thin', '', '');
                });

                $r = $q + 1;
                $sheet->cell('B' . $r, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('N° CC:');
                    $cell->setBorder('', '', '', 'thin');
                });

                $sheet->mergeCells('C' . $r . ':F' . $r);
                $sheet->cell('C' . $r, function ($cell) use ($archivo_plano) {
                    // manipulate the cel
                    //$cell->setValue($archivo_plano->id_paciente);
                    $cell->setValue('0918552092');
                    $cell->setBorder('', '', 'hair', '');
                });

                $sheet->mergeCells('G' . $r . ':O' . $r);
                $sheet->cell('F' . $r, function ($cell) {
                    // manipulate the cel
                    $cell->setBorder('', '', '', '');
                });

                $sheet->mergeCells('P' . $r . ':Q' . $r);
                $sheet->cell('P' . $r, function ($cell) {
                    // manipulate the cel
                    $cell->setBorder('', 'thin', 'thin', '');
                });

                $s = $r + 1;
                $sheet->cell('B' . $s, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Cargo:');
                    $cell->setBorder('', '', '', 'thin');
                });

                $sheet->mergeCells('C' . $s . ':F' . $s);
                $sheet->cell('C' . $s, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('JEFE DE AUDITORIA MEDICA');
                    $cell->setBorder('', '', 'hair', '');
                });



                $sheet->mergeCells('G' . $s . ':O' . $s);
                $sheet->cell('G' . $s, function ($cell) {
                    // manipulate the cel
                    $cell->setBorder('', '', '', '');
                });

                $sheet->mergeCells('P' . $s . ':Q' . $s);
                $sheet->cell('P' . $s, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Sello');
                    $cell->setAlignment('center');
                    $cell->setBorder('', 'thin', '', '');
                });

                $t = $s + 1;
                $sheet->mergeCells('B' . $t . ':Q' . $t);
                $sheet->cell('B' . $t, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('', 'thin', 'thin', 'thin');
                });
            });
            $excel->getActiveSheet()->getColumnDimension("A")->setWidth(8)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("B")->setWidth(8)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("C")->setWidth(8)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("D")->setWidth(10)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("E")->setWidth(10)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("F")->setWidth(12)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("G")->setWidth(8)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("H")->setWidth(8)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("I")->setWidth(8)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("J")->setWidth(8)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("K")->setWidth(8)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("L")->setWidth(8)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("M")->setWidth(14)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("N")->setWidth(12)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("O")->setWidth(12)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("P")->setWidth(18)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("Q")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getStyle("A2:Q100")->getFont()->setName('Arial');
            $excel->getActiveSheet()->getStyle("A10:Q100")->getFont()->setSize(8)->setName('Arial');
            $excel->getActiveSheet()->getStyle("A5:Q9")->getFont()->setSize(8)->setName('Arial');
            $excel->getActiveSheet()->getStyle('D10')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('E10')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('L10')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('M10')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('N10')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('O10')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('P10')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('Q10')->getAlignment()->setWrapText(true);
        })->export('xlsx');
    }

    public function planilla_cargo_consolidado_msp($hcid)
    {

        $fecha_elaboracion = date('d/m/Y');

        $historia_clinica = Historiaclinica::find($hcid);
        $agend = agenda::find($historia_clinica->id_agenda);
        $archivo_plano = Archivo_Plano_Cabecera::where('id_hc', $hcid)->first();
        $ap = Archivo_Plano_Cabecera::find($archivo_plano->id);
        $detalles = $ap->detalles;

        //Detalle de Servicio Desde
        $fecha_desde = substr($archivo_plano->fecha_ing, 0, 10);
        $invert_fech_desde = explode('-', $fecha_desde);
        $fecha_desde_invert = $invert_fech_desde[2] . "/" . $invert_fech_desde[1] . "/" . $invert_fech_desde[0];

        //Anio servicio
        $anio_servicio = $invert_fech_desde[0];

        //Mes Servicio
        $mes_servicio = $invert_fech_desde[1];

        //Obtenemos el mes
        $txt_mes = '';
        if ($mes_servicio == '12') {
            $txt_mes = 'DICIEMBRE';
        } elseif ($mes_servicio == '11') {
            $txt_mes = 'NOVIEMBRE';
        } elseif ($mes_servicio == '10') {
            $txt_mes = 'OCTUBRE';
        } elseif ($mes_servicio == '09') {
            $txt_mes = 'SEPTIEMBRE';
        } elseif ($mes_servicio == '08') {
            $txt_mes = 'AGOSTO';
        } elseif ($mes_servicio == '07') {
            $txt_mes = 'JULIO';
        } elseif ($mes_servicio == '06') {
            $txt_mes = 'JUNIO';
        } elseif ($mes_servicio == '05') {
            $txt_mes = 'MAYO';
        } elseif ($mes_servicio == '04') {
            $txt_mes = 'ABRIL';
        } elseif ($mes_servicio == '03') {
            $txt_mes = 'MARZO';
        } elseif ($mes_servicio == '02') {
            $txt_mes = 'FEBRERO';
        } elseif ($mes_servicio == '01') {
            $txt_mes = 'ENERO';
        }

        $mes_anio_servicio = $txt_mes . "-" . $anio_servicio;

        $planilla_consolidado = Archivo_Plano_Cabecera::whereNotNull('cod_deriva_msp')
            ->select('id', 'derivacion_nc_msp', 'cod_deriva_msp', 'id_paciente')
            ->get();

        $total_inicio = 0;
        $numero_expediente = 0;
        foreach ($planilla_consolidado as $value) {

            $plano_det = DB::table('archivo_plano_detalle as apd')
                ->where('apd.id_ap_cabecera', $value->id)
                ->groupBy('apd.id_ap_cabecera')
                ->select(DB::raw("SUM(apd.total_solicitado_usd) as valor_solicitud"))
                ->first();

            $total_inicio += $plano_det->valor_solicitud;
            $numero_expediente = $numero_expediente + 1;
        }

        Excel::create('Reporte Planilla Consolidado MSP ', function ($excel) use ($agend, $archivo_plano, $detalles, $fecha_elaboracion, $mes_anio_servicio, $planilla_consolidado, $numero_expediente, $total_inicio) {
            $excel->sheet('Planilla ', function ($sheet) use ($agend, $archivo_plano, $detalles, $fecha_elaboracion, $mes_anio_servicio, $planilla_consolidado, $numero_expediente, $total_inicio) {

                $sheet->mergeCells('B2:G2');
                $sheet->cell('B2', function ($cell) use ($archivo_plano) {
                    // manipulate the cel
                    /*$nombre_comercial ='';
                        if($archivo_plano->id_empresa =='0992704152001'){
                            $nombre_comercial = 'INSTITUTO ECUATORIANO DE ENFERMEDADES DIGESTIVAS GASTROCLINICA S.A';
                        }elseif($archivo_plano->id_empresa ='1307189140001'){
                            $nombre_comercial = 'ROBLES MEDRANDA CARLOS ANTONIO';
                        }*/
                    $cell->setValue($archivo_plano->empresa->razonsocial);
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                });

                $sheet->mergeCells('B3:G3');
                $sheet->cell('B3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PLANILLA DE CARGOS CONSOLIDADO');
                    $cell->setAlignment('center');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('B5:D5');
                $sheet->cell('B5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Tipo Servicio:');
                    $cell->setFontSize('10');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', '', '', 'thin');
                });

                //$sheet->mergeCells('F5:K5');
                $sheet->cell('E5', function ($cell) use ($agend) {
                    // manipulate the cel
                    $tipo_servicio = '';
                    if ($agend->est_amb_hos == '0') {
                        $tipo_servicio = 'AMBULATORIO';
                    } elseif ($agend->est_amb_hos == '1') {
                        $tipo_servicio = 'HOSPITALIZADO';
                    }

                    $cell->setValue($tipo_servicio);
                    $cell->setAlignment('center');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', '', '', '');
                });


                $sheet->cell('F5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Fecha de Elaboración:');
                    $cell->setFontSize('10');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', '', '', '');
                });
                $sheet->cell('G5', function ($cell) use ($fecha_elaboracion) {
                    // manipulate the cel
                    $cell->setValue($fecha_elaboracion);
                    $cell->setAlignment('center');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', '', '');
                });
                $sheet->mergeCells('B6:D6');
                $sheet->cell('B6', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Monto Solicitado:');
                    $cell->setFontSize('10');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('', '', '', 'thin');
                });

                $sheet->cell('E6', function ($cell) use ($total_inicio) {
                    // manipulate the cel
                    $valor_inicio = number_format($total_inicio, 2);
                    $cell->setValue($valor_inicio);
                    $cell->setAlignment('center');
                    $cell->setFontSize('10');
                    $cell->setBorder('', '', '', '');
                });

                $sheet->cell('F6', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('', '', '', '');
                });
                $sheet->cell('G6', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('', 'thin', '', '');
                });
                $sheet->mergeCells('B7:D7');
                $sheet->cell('B7', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Mes y Año de Presentacion:');
                    $cell->setFontSize('10');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('', '', '', 'thin');
                });
                $sheet->cell('E7', function ($cell) use ($mes_anio_servicio) {
                    // manipulate the cel
                    $cell->setValue($mes_anio_servicio);
                    $cell->setFontSize('10');
                    $cell->setAlignment('center');
                    $cell->setBorder('', '', '', '');
                });

                $sheet->cell('F7', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('', '', '', '');
                });
                $sheet->cell('G7', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('', 'thin', '', '');
                });
                $sheet->mergeCells('B8:D8');
                $sheet->cell('B8', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Nro. Expedientes:');
                    $cell->setFontSize('10');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('', '', 'thin', 'thin');
                });

                $sheet->cell('E8', function ($cell) use ($numero_expediente) {
                    // manipulate the cel
                    $cell->setValue($numero_expediente);
                    $cell->setAlignment('center');
                    $cell->setFontSize('10');
                    $cell->setBorder('', '', 'thin', '');
                });

                $sheet->cell('F8', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('', '', 'thin', '');
                });
                $sheet->cell('G8', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('', 'thin', 'thin', '');
                });

                $sheet->cell('B10', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('No.');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontSize('8');
                });
                $sheet->cell('C10', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('No.Caso');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontSize('8');
                });
                $sheet->cell('D10', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Código Validación');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontSize('8');
                });
                $sheet->cell('E10', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CC No.');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontSize('8');
                });
                $sheet->cell('F10', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Beneficiario');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontSize('8');
                });
                $sheet->cell('G10', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Valor Solicitad');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontSize('8');
                });

                $sheet->setColumnFormat(array(
                    'G' => '0.00',
                ));

                $i = 11;
                $j = 1;
                $k = 0;
                $l = 0;
                $m = 0;
                $n = 0;
                $p = 0;
                $q = 0;
                $r = 0;
                $s = 0;
                $t = 0;
                $u = 0;

                $total = 0;
                foreach ($planilla_consolidado as $value) {

                    $datospaciente = Paciente::where('id', $value->id_paciente)
                        ->first();

                    $plano_det = DB::table('archivo_plano_detalle as apd')
                        ->where('apd.id_ap_cabecera', $value->id)
                        ->groupBy('apd.id_ap_cabecera')
                        ->select(DB::raw("SUM(apd.total_solicitado_usd) as valor_solicitud"))
                        ->first();

                    $sheet->cell('B' . $i, function ($cell) use ($j) {
                        // manipulate the cel
                        $cell->setValue($j);
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontSize('8');
                    });
                    $sheet->cell('C' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->derivacion_nc_msp);
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontSize('8');
                    });
                    $sheet->cell('D' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->cod_deriva_msp);
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontSize('8');
                    });

                    $sheet->cell('E' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->id_paciente);
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontSize('8');
                    });

                    $sheet->cell('F' . $i, function ($cell) use ($datospaciente) {
                        // manipulate the cel
                        $cell->setValue($datospaciente->apellido1 . ' ' . $datospaciente->apellido2 . ' ' . $datospaciente->nombre1 . ' ' . $datospaciente->nombre2);
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontSize('8');
                    });

                    $sheet->cell('G' . $i, function ($cell) use ($plano_det) {
                        // manipulate the cel
                        if ($plano_det->valor_solicitud != 0) {
                            $valor = number_format($plano_det->valor_solicitud, 2);
                        } else {
                            $valor = number_format($plano_det->valor_solicitud, 2);
                        }

                        $cell->setValue($valor);
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontSize('8');
                    });

                    $total += $plano_det->valor_solicitud;

                    $j = $j + 1;
                    $i++;
                }

                $sheet->mergeCells('B' . $i . ':F' . $i);
                $sheet->cell('B' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    $cell->setValue('Total Valor Solicitado:');
                    $cell->setAlignment('right');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontSize('8');
                });

                $sheet->cell('G' . $i, function ($cell) use ($total) {
                    // manipulate the cel
                    $valor_total = round($total, 2);
                    $cell->setValue($valor_total);
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontSize('8');
                });


                $k = $i + 2;

                $sheet->mergeCells('B' . $k . ':G' . $k);
                $sheet->cell('B' . $k, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', '', 'thin');
                });

                $l = $k + 1;

                $sheet->mergeCells('B' . $l . ':G' . $l);
                $sheet->cell('B' . $l, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('', 'thin', '', 'thin');
                });

                $m = $l + 1;
                $sheet->mergeCells('B' . $m . ':G' . $m);
                $sheet->cell('B' . $m, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('', 'thin', '', 'thin');
                });

                $n = $m + 1;
                $sheet->mergeCells('B' . $n . ':G' . $n);
                $sheet->cell('B' . $n, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('', 'thin', '', 'thin');
                });

                $p = $n + 1;
                $sheet->mergeCells('B' . $p . ':G' . $p);
                $sheet->cell('B' . $p, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('', 'thin', '', 'thin');
                });

                $q = $p + 1;
                $sheet->mergeCells('B' . $q . ':G' . $q);
                $sheet->cell('B' . $q, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Firma:');
                    $cell->setBorder('', 'thin', '', 'thin');
                    $cell->setFontSize('8');
                });
                $r = $q + 1;
                $sheet->cell('B' . $r, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Nombre:');
                    $cell->setBorder('', '', '', 'thin');
                    $cell->setFontSize('8');
                });
                $sheet->mergeCells('C' . $r . ':E' . $r);
                $sheet->cell('C' . $r, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('', '', 'hair', '');
                });
                $sheet->mergeCells('F' . $r . ':G' . $r);
                $sheet->cell('F' . $r, function ($cell) {
                    // manipulate the cel
                    $cell->setBorder('', 'thin', '', '');
                });


                $s = $r + 1;
                $sheet->cell('B' . $s, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('N° CC:');
                    $cell->setBorder('', '', '', 'thin');
                    $cell->setFontSize('8');
                });

                $sheet->mergeCells('C' . $s . ':E' . $s);
                $sheet->cell('C' . $s, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('', '', 'hair', '');
                });
                $sheet->cell('F' . $s, function ($cell) {
                    // manipulate the cel
                    $cell->setBorder('', '', 'thin', '');
                });
                $sheet->cell('G' . $s, function ($cell) {
                    // manipulate the cel
                    $cell->setBorder('', 'thin', 'thin', '');
                });

                $t = $s + 1;
                $sheet->cell('B' . $t, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Cargo:');
                    $cell->setBorder('', '', '', 'thin');
                    $cell->setFontSize('8');
                });

                $sheet->mergeCells('C' . $t . ':E' . $t);
                $sheet->cell('C' . $t, function ($cell) {
                    // manipulate the cel
                    $cell->setBorder('', '', 'hair', '');
                });

                $sheet->cell('F' . $t, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Sello');
                    $cell->setAlignment('right');
                    $cell->setBorder('', '', '', '');
                    $cell->setFontSize('8');
                });

                $sheet->cell('G' . $t, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('center');
                    $cell->setBorder('', 'thin', '', '');
                });

                $u = $t + 1;
                $sheet->mergeCells('B' . $u . ':G' . $u);
                $sheet->cell('B' . $u, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('', 'thin', 'thin', 'thin');
                });
            });

            $excel->getActiveSheet()->getColumnDimension("A")->setWidth(2)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("B")->setWidth(8)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("C")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("D")->setWidth(22)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("E")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("F")->setWidth(35)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("G")->setWidth(13)->setAutosize(false);
        })->export('xlsx');
    }


    //Genera Consolidado Msp
    public function obtener_consolidado_msp(Request $request)
    {

        $fecha_elaboracion = date('d/m/Y');

        $cedula = $request['cedula'];
        $nombres = $request['paciente'];
        $mes_plano = $request['mes_plano'];
        $id_seguro = $request['id_seguro'];
        $id_empresa = $request['id_empresa'];
        $codigo_validacion = $request['codigo_validacion'];

        $inf_empresa = Empresa::where('id', $id_empresa)
            ->where('estado', '1')
            ->first();

        /*$fech_ing  = substr($request['fecha_ing'], 6, 4).'-'.substr($request['fecha_ing'], 3, 2).'-'.substr($request['fecha_ing'], 0, 2);*/

        $mes = substr($mes_plano, 0, 2);
        $anio = substr($mes_plano, 2, 5);

        //Obtenemos el mes
        $txt_mes = '';
        if ($mes == '12') {
            $txt_mes = 'DICIEMBRE';
        } elseif ($mes == '11') {
            $txt_mes = 'NOVIEMBRE';
        } elseif ($mes == '10') {
            $txt_mes = 'OCTUBRE';
        } elseif ($mes == '09') {
            $txt_mes = 'SEPTIEMBRE';
        } elseif ($mes == '08') {
            $txt_mes = 'AGOSTO';
        } elseif ($mes == '07') {
            $txt_mes = 'JULIO';
        } elseif ($mes == '06') {
            $txt_mes = 'JUNIO';
        } elseif ($mes == '05') {
            $txt_mes = 'MAYO';
        } elseif ($mes == '04') {
            $txt_mes = 'ABRIL';
        } elseif ($mes == '03') {
            $txt_mes = 'MARZO';
        } elseif ($mes == '02') {
            $txt_mes = 'FEBRERO';
        } elseif ($mes == '01') {
            $txt_mes = 'ENERO';
        }

        $mes_anio_servicio = $txt_mes . "-" . $anio;

        /*$archivo_plano_crea = Archivo_Plano_Cabecera::whereNotNull('cod_deriva_msp')
                                                ->where('mes_plano', $mes_plano)
                                                ->where('id_seguro', $id_seguro)
                                                ->first();*/

        /*$planilla_consolidado = Archivo_Plano_Cabecera::whereNotNull('cod_deriva_msp')
                                ->where('mes_plano', $mes_plano)
                                ->where('id_seguro', $id_seguro)
                                ->where('estado', '1')
                                ->where('id_paciente',$cedula)
                                ->orderby('fecha_ing','ASC')
                                ->get();*/

        $planilla_consolidado = Archivo_Plano_Cabecera::whereNotNull('cod_deriva_msp')
            ->where('estado', '1');

        //Valida que ingrese la cedula
        if ($cedula != null) {
            $paciente = Paciente::find($cedula);
            if (!is_null($paciente)) {
                $planilla_consolidado = $planilla_consolidado->where('id_paciente', $paciente->id);
            }
        }

        //Valida que ingrese el mes de Plano
        if ($mes_plano != null) {

            $planilla_consolidado = $planilla_consolidado->where('mes_plano', $mes_plano);
        }


        //Valida que ingrese el seguro
        if ($id_seguro != null) {

            $planilla_consolidado = $planilla_consolidado->where('id_seguro', $id_seguro);
        }


        //Valida que ingrese la empresa
        if ($id_empresa != null) {
            $planilla_consolidado = $planilla_consolidado->where('id_empresa', $id_empresa);
        }

        if ($codigo_validacion != null) {
            $planilla_consolidado = $planilla_consolidado->where('cod_deriva_msp', $codigo_validacion);
        }

        $archivo_plano_crea = $planilla_consolidado->get()->first();
        $archivo_plano_last = $planilla_consolidado->get()->last();

        $planilla_consolidado = $planilla_consolidado->get();


        $total_inicio = 0;
        $numero_expediente = 0;

        foreach ($planilla_consolidado as $value) {

            $plano_det = DB::table('archivo_plano_detalle as apd')
                ->where('apd.id_ap_cabecera', $value->id)
                ->where('apd.estado', '1')
                ->groupBy('apd.id_ap_cabecera')
                ->select(DB::raw("SUM(apd.total_solicitado_usd) as valor_solicitud"))
                ->first();

            $total_inicio += $plano_det->valor_solicitud;
            $numero_expediente = $numero_expediente + 1;
        }

        $fecha_desde_invert = '';
        $fecha_hasta_invert = '';

        //Detalle de Servicio Desde
        $fecha_desde = substr($archivo_plano_crea->fecha_ing, 0, 10);
        $invert_fech_desde = explode('-', $fecha_desde);
        $fecha_desde_invert = $invert_fech_desde[2] . "/" . $invert_fech_desde[1] . "/" . $invert_fech_desde[0];

        //Detalle de Servicio Hasta
        $fecha_hasta = substr($archivo_plano_last->fecha_alt, 0, 10);
        $invert_fech_hasta = explode('-', $fecha_hasta);
        $fecha_hasta_invert = $invert_fech_hasta[2] . "/" . $invert_fech_hasta[1] . "/" . $invert_fech_hasta[0];

        Excel::create('Reporte Planilla Consolidado MSP', function ($excel) use ($planilla_consolidado, $archivo_plano_crea, $fecha_elaboracion, $total_inicio, $mes_plano, $mes_anio_servicio, $fecha_desde_invert, $fecha_hasta_invert, $inf_empresa) {
            $excel->sheet('Planilla MSP', function ($sheet) use ($planilla_consolidado, $archivo_plano_crea, $fecha_elaboracion, $total_inicio, $mes_plano, $mes_anio_servicio, $fecha_desde_invert, $fecha_hasta_invert, $inf_empresa) {

                $sheet->mergeCells('B2:Q2');
                $sheet->cell('B2', function ($cell) use ($inf_empresa) {
                    $cell->setValue($inf_empresa->razonsocial);
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                });

                $sheet->mergeCells('B3:Q3');
                $sheet->cell('B3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PLANILLA DE CARGOS CONSOLIDADO');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('B5:E5');
                $sheet->cell('B5', function ($cell) {

                    $cell->setValue('Tipo Servicio:');
                    $cell->setFontSize('12');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', '', '', 'thin');
                });

                $sheet->mergeCells('F5:K5');
                $sheet->cell('F5', function ($cell) {
                    $cell->setValue('AMBULATORIO');
                    $cell->setAlignment('left');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', '', '', '');
                });

                $sheet->mergeCells('L5:N5');
                $sheet->cell('L5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Fecha de Elaboración:');
                    $cell->setFontSize('12');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', '', '', '');
                });

                $sheet->mergeCells('O5:Q5');
                $sheet->cell('O5', function ($cell) use ($fecha_elaboracion) {
                    // manipulate the cel
                    $cell->setValue($fecha_elaboracion);
                    $cell->setAlignment('left');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', '', '');
                });

                $sheet->mergeCells('B6:E6');
                $sheet->cell('B6', function ($cell) {

                    $cell->setValue('Monto Solicitado:');
                    $cell->setFontSize('12');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('', '', '', 'thin');
                });
                $sheet->mergeCells('F6:K6');
                $sheet->cell('F6', function ($cell) use ($total_inicio) {
                    $valor_inicio = round($total_inicio, 2);
                    $cell->setValue($valor_inicio);
                    $cell->setAlignment('left');
                    $cell->setFontSize('10');
                    $cell->setBorder('', '', '', '');
                });

                $sheet->mergeCells('L6:N6');
                $sheet->cell('L6', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Mes y Año de Servicio:');
                    $cell->setFontSize('12');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('', '', '', '');
                });

                $sheet->mergeCells('B7:E7');
                $sheet->cell('B7', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Nro. CV:');
                    $cell->setFontSize('12');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('', '', '', 'thin');
                });

                $sheet->mergeCells('F7:K7');
                $sheet->cell('F7', function ($cell) use ($archivo_plano_crea) {
                    // manipulate the cel
                    $cell->setValue($archivo_plano_crea->cod_deriva_msp);
                    $cell->setAlignment('left');
                    $cell->setFontSize('10');
                    $cell->setBorder('', '', '', '');
                });
                $sheet->mergeCells('L7:N7');
                $sheet->cell('L7', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CIE10:');
                    $cell->setFontSize('12');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('', '', '', '');
                });
                $sheet->mergeCells('O7:Q7');
                $sheet->cell('O7', function ($cell) use ($archivo_plano_crea) {
                    // manipulate the cel
                    $cell->setValue($archivo_plano_crea->cie10);
                    $cell->setFontSize('10');
                    $cell->setAlignment('left');
                    $cell->setBorder('', 'thin', '', '');
                });

                $sheet->mergeCells('B8:E8');
                $sheet->cell('B8', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CC No:');
                    $cell->setFontSize('12');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('', '', '', 'thin');
                });

                $sheet->mergeCells('F8:K8');
                $sheet->cell('F8', function ($cell) use ($archivo_plano_crea) {
                    // manipulate the cel
                    $cell->setValue($archivo_plano_crea->id_paciente);
                    $cell->setAlignment('left');
                    $cell->setFontSize('10');
                    $cell->setBorder('', '', '', '');
                });
                $sheet->mergeCells('L8:N8');
                $sheet->cell('L8', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    //$cell->setFontSize('12');
                    //$cell->setFontWeight('bold');
                    $cell->setBorder('', '', '', '');
                });
                $sheet->mergeCells('O8:Q8');
                $sheet->cell('O8', function ($cell) use ($archivo_plano_crea) {
                    // manipulate the cel
                    $cell->setValue('');
                    //$cell->setFontSize('10');
                    //$cell->setAlignment('left');
                    $cell->setBorder('', 'thin', '', '');
                });

                $sheet->mergeCells('B9:E9');
                $sheet->cell('B9', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Beneficiario:');
                    $cell->setFontSize('12');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('', '', '', 'thin');
                });

                $sheet->mergeCells('F9:K9');
                $sheet->cell('F9', function ($cell) use ($archivo_plano_crea) {
                    // manipulate the cel
                    $cell->setValue($archivo_plano_crea->paciente->apellido1 . ' ' . $archivo_plano_crea->paciente->apellido2 . ' ' . $archivo_plano_crea->paciente->nombre1 . ' ' . $archivo_plano_crea->paciente->nombre2);
                    $cell->setFontSize('10');
                    $cell->setAlignment('left');
                    $cell->setBorder('', '', '', '');
                });

                $sheet->mergeCells('L9:N9');
                $sheet->cell('L9', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    //$cell->setFontSize('12');
                    //$cell->setFontWeight('bold');
                    $cell->setBorder('', '', '', '');
                });
                $sheet->mergeCells('O9:Q9');
                $sheet->cell('O9', function ($cell) use ($archivo_plano_crea) {
                    // manipulate the cel
                    $cell->setValue('');
                    //$cell->setFontSize('10');
                    //$cell->setAlignment('left');
                    $cell->setBorder('', 'thin', '', '');
                });

                $sheet->mergeCells('B10:E10');
                $sheet->cell('B10', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Detalle de Servicio Desde:');
                    $cell->setFontSize('12');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('', '', '', 'thin');
                });
                $sheet->mergeCells('F10:K10');
                $sheet->cell('F10', function ($cell) use ($fecha_desde_invert) {
                    // manipulate the cel
                    $cell->setValue($fecha_desde_invert);
                    $cell->setAlignment('left');
                    $cell->setFontSize('10');
                    $cell->setBorder('', '', '', '');
                });
                $sheet->mergeCells('L10:N10');
                $sheet->cell('L10', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Hasta:');
                    $cell->setFontSize('12');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('', '', '', '');
                });
                $sheet->mergeCells('O10:Q10');
                $sheet->cell('O10', function ($cell) use ($fecha_hasta_invert) {
                    // manipulate the cel
                    $cell->setValue($fecha_hasta_invert);
                    $cell->setFontSize('10');
                    $cell->setAlignment('left');
                    $cell->setBorder('', 'thin', '', '');
                });

                $sheet->mergeCells('O6:Q6');
                $sheet->cell('O6', function ($cell) use ($mes_anio_servicio) {
                    // manipulate the cel
                    $cell->setValue($mes_anio_servicio);
                    $cell->setFontSize('10');
                    $cell->setAlignment('left');
                    $cell->setBorder('', 'thin', '', '');
                });

                $sheet->mergeCells('B11:C11');
                $sheet->cell('B11', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Fecha');

                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D11', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Código TSNS');

                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('E11', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Clasificador');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('F11:K11');
                $sheet->cell('F11', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Descripción');

                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('L11', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Cantidad #');

                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('M11', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Valor Unitario USD');

                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('N11', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Clasificador %');

                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('O11', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Subtotal USD');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('P11', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Valor por Modificador USD');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('Q11', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Total Solicitado USD');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->setColumnFormat(array(
                    'M' => '0.00',
                    'N' => '0.00',
                    'O' => '0.00',
                    'P' => '0.00',
                    'Q' => '0.00',
                ));

                //Mostrar Tabla Detalle
                $i = 12;
                $j = 0;
                $k = 0;
                $l = 0;
                $m = 0;
                $n = 0;
                $p = 0;
                $q = 0;
                $r = 0;
                $s = 0;
                $t = 0;
                $total = 0;

                foreach ($planilla_consolidado as $valcons) {

                    $datospaciente = $valcons->paciente;

                    $planilla_msp_detalle = Db::table('archivo_plano_detalle as apd')
                        ->where('apd.id_ap_cabecera', $valcons->id)
                        ->where('apd.estado', '1')
                        ->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')
                        ->select('apd.fecha', 'apd.codigo', 'apd.descripcion', 'apt.clasificado', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.clasif_porcentaje_msp', 'apd.valor_porcent_clasifi', 'apd.total_solicitado_usd', 'apd.tipo', 'apd.clasificador', 'apd.clasif_porcentaje_msp', 'apd.clasif_porcentaje_msp')
                        ->orderBy('apt.orden_msp', 'asc')
                        ->orderBy('apd.id', 'asc')
                        ->get();


                    foreach ($planilla_msp_detalle as $value) {

                        //Invertir Fecha
                        $invert_fecha = substr($value->fecha, 0, 10);
                        $invert_fech_deta = explode('-', $invert_fecha);
                        $invert_fech_detalle = $invert_fech_deta[2] . "/" . $invert_fech_deta[1] . "/" . $invert_fech_deta[0];

                        $sheet->mergeCells('B' . $i . ':C' . $i);
                        $sheet->cell('B' . $i, function ($cell) use ($invert_fech_detalle) {
                            // manipulate the cel
                            $cell->setValue($invert_fech_detalle);
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('D' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->tipo == 'IV' || $value->tipo == 'I' || $value->tipo == 'M' || $value->tipo == 'IVC') {
                                $cell->setValue(' ');
                            } else {
                                $cell->setValue($value->codigo);
                            }
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('E' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->clasificador);
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->mergeCells('F' . $i . ':K' . $i);
                        $sheet->cell('F' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->descripcion);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('L' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->cantidad);
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->getStyle('M' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                        $sheet->cell('M' . $i, function ($cell) use ($value) {

                            if ($value->clasif_porcentaje_msp == 50) {

                                $cell->setValue($value->valor * 2);
                            } else {

                                if ($value->clasificador == 'SA09-38') {

                                    $vfinal = $value->total_solicitado_usd / $value->cantidad;
                                    //$vfinal = round($vfinal, 2);

                                    $cell->setValue($vfinal);
                                } else {
                                    $cell->setValue($value->valor);
                                }
                            }


                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');


                            /*if($value->clasificador =='SA09-38'){

                            $vfinal = $value->total_solicitado_usd/$value->cantidad;
                            $cell->setValue($vfinal);

                        }else{
                            if($value->clasif_porcentaje_msp == '50'){

                                $valor_real = (($value->valor)*(2));
                                $valor = number_format($valor_real, 2);
                                $cell->setValue($valor);

                            }else{
                                $cell->setValue($value->valor);
                            }

                        }


                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');*/


                            /*if($value->clasif_porcentaje_msp == '50'){

                            $valor_real = (($value->valor)*(2));

                            if($value->valor != 0) {
                                $valor = round($valor_real, 2);
                            }else {
                               $valor = round($valor_real, 2);
                            }

                            $cell->setValue($valor);
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        }

                        $cell->setValue($value->valor);
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');*/
                        });

                        $sheet->cell('N' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->clasif_porcentaje_msp != 0) {
                                $valor = round($value->clasif_porcentaje_msp, 2);
                            } else {
                                $valor = round($value->clasif_porcentaje_msp, 2);
                            }
                            $cell->setValue($valor);
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->getStyle('O' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                        $sheet->cell('O' . $i, function ($cell)  use ($value) {
                            // manipulate the cel

                            if ($value->clasificador == 'SA09-38') {

                                $vfinal = $value->total_solicitado_usd;
                                $vfinal = round($vfinal, 2);

                                $cell->setValue($vfinal);
                            } else {
                                $cell->setValue($value->subtotal);
                            }

                            //$cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            /*if($value->clasificador =='SA09-38'){

                                $vfinal = $value->total_solicitado_usd;
                                $vfinal = round($vfinal, 2);

                                $cell->setValue($vfinal);

                            }else{

                                $valor = round($value->valor*$value->cantidad, 2);
                                $cell->setValue($valor);

                            }

                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');*/

                            /*if ($value->valor != 0) {
                                $valor = round($value->valor, 2);
                            }else {
                                $valor = round($value->valor, 2);
                            }
                            $cell->setValue($valor);
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');*/
                        });

                        $sheet->cell('P' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->valor_porcent_clasifi != 0) {
                                $valor = round($value->valor_porcent_clasifi, 2);
                            } else {
                                $valor = round($value->valor_porcent_clasifi, 2);
                            }
                            $cell->setValue($valor);
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->getStyle('Q' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                        $sheet->cell('Q' . $i, function ($cell) use ($value) {

                            $valor = round($value->total_solicitado_usd, 2);

                            $cell->setValue($valor);
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $total += $value->total_solicitado_usd;


                        $i++;
                    }
                }

                $sheet->cell('P' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Valor Total Solicitado');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->getStyle('Q' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                $sheet->cell('Q' . $i, function ($cell) use ($total) {
                    // manipulate the cel
                    $valor_total = round($total, 2);
                    $cell->setValue($valor_total);
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $j = $i + 2;

                $sheet->mergeCells('B' . $j . ':Q' . $j);
                $sheet->cell('B' . $j, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', '', 'thin');
                });

                $k = $j + 1;

                $sheet->mergeCells('B' . $k . ':Q' . $k);
                $sheet->cell('B' . $k, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('', 'thin', '', 'thin');
                });

                $l = $k + 1;
                $sheet->mergeCells('B' . $l . ':Q' . $l);
                $sheet->cell('B' . $l, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('', 'thin', '', 'thin');
                });

                $m = $l + 1;
                $sheet->mergeCells('B' . $m . ':Q' . $m);
                $sheet->cell('B' . $m, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('', 'thin', '', 'thin');
                });

                $n = $m + 1;
                $sheet->mergeCells('B' . $n . ':Q' . $n);
                $sheet->cell('B' . $n, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('', 'thin', '', 'thin');
                });

                $p = $n + 1;
                $sheet->mergeCells('B' . $p . ':Q' . $p);
                $sheet->cell('B' . $p, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Firma:');
                    $cell->setBorder('', 'thin', '', 'thin');
                });

                $q = $p + 1;
                $sheet->cell('B' . $q, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Nombre:');
                    $cell->setBorder('', '', '', 'thin');
                });

                $sheet->mergeCells('C' . $q . ':E' . $q);
                $sheet->cell('C' . $q, function ($cell) {
                    // manipulate the cel

                    $cell->setValue('');
                    $cell->setBorder('', '', 'hair', '');
                });

                $sheet->mergeCells('F' . $q . ':Q' . $q);
                $sheet->cell('F' . $q, function ($cell) {
                    // manipulate the cel
                    $cell->setBorder('', 'thin', '', '');
                });

                $r = $q + 1;
                $sheet->cell('B' . $r, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('N° CC:');
                    $cell->setBorder('', '', '', 'thin');
                });

                $sheet->mergeCells('C' . $r . ':E' . $r);
                $sheet->cell('C' . $r, function ($cell) {
                    // manipulate the cel

                    $cell->setValue('');
                    $cell->setBorder('', '', 'hair', '');
                });

                $sheet->mergeCells('F' . $r . ':O' . $r);
                $sheet->cell('F' . $r, function ($cell) {
                    // manipulate the cel
                    $cell->setBorder('', '', '', '');
                });

                $sheet->mergeCells('P' . $r . ':Q' . $r);
                $sheet->cell('P' . $r, function ($cell) {
                    // manipulate the cel
                    $cell->setBorder('', 'thin', 'thin', '');
                });

                $s = $r + 1;
                $sheet->cell('B' . $s, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Cargo:');
                    $cell->setBorder('', '', '', 'thin');
                });

                $sheet->mergeCells('C' . $s . ':E' . $s);
                $sheet->cell('C' . $s, function ($cell) {
                    // manipulate the cel
                    $cell->setBorder('', '', 'hair', '');
                });

                $sheet->mergeCells('F' . $s . ':O' . $s);
                $sheet->cell('F' . $s, function ($cell) {
                    // manipulate the cel
                    $cell->setBorder('', '', '', '');
                });

                $sheet->mergeCells('P' . $s . ':Q' . $s);
                $sheet->cell('P' . $s, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Sello');
                    $cell->setAlignment('center');
                    $cell->setBorder('', 'thin', '', '');
                });

                $t = $s + 1;
                $sheet->mergeCells('B' . $t . ':Q' . $t);
                $sheet->cell('B' . $t, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('', 'thin', 'thin', 'thin');
                });
            });
            $excel->getActiveSheet()->getColumnDimension("A")->setWidth(8)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("B")->setWidth(8)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("C")->setWidth(8)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("D")->setWidth(10)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("E")->setWidth(10)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("F")->setWidth(12)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("G")->setWidth(8)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("H")->setWidth(8)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("I")->setWidth(8)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("J")->setWidth(8)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("K")->setWidth(8)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("L")->setWidth(8)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("M")->setWidth(14)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("N")->setWidth(12)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("O")->setWidth(12)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("P")->setWidth(18)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("Q")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getStyle("A2:Q2000")->getFont()->setName('Arial');
            $excel->getActiveSheet()->getStyle("A10:Q2000")->getFont()->setSize(8)->setName('Arial');
            $excel->getActiveSheet()->getStyle("A5:Q9")->getFont()->setSize(8)->setName('Arial');
            $excel->getActiveSheet()->getStyle('D10')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('E10')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('L10')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('M10')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('N10')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('O10')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('P10')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('Q10')->getAlignment()->setWrapText(true);


            //$excel->getActiveSheet()->getColumnDimension("I")->setWidth(40)->setAutosize(false);
            //$excel->getActiveSheet()->getColumnDimension("N")->setWidth(40)->setAutosize(false);


        })->export('xlsx');
    }

    public function genera_ap()
    {

        //$tipo_seguros = Db::table('tipo_seguro as ts')->join('ap_tipo_seg as apts','apts.codigo','ts.tipo')->select('apts.descripcion','apts.codigo','ts.tipo','ts.id')->orderBy('apts.descripcion')->get();
        $tipo_seguros = Ap_Tipo_Seg::where('estado', '1')->get();
        $empresas = Empresa::where('estado', '1')->get();
        $seguros_publicos = Seguro::where('inactivo', '1')->where('tipo', '0')->orderBy('nombre')->get();
        return view('archivo_plano/archivo/archivo_plano', ['tipo_seguros' => $tipo_seguros, 'empresas' => $empresas, 'seguros_publicos' => $seguros_publicos]);
    }

    public function genera_ap_excel(Request $request)
    {

        $mes_plano = $request->mes_plano;
        $seguro = $request->seguro;
        $tipo_seg = $request->id_tipo_seguro;
        $cob_compar = $request->id_cobertura_comp;
        $empresa = $request->id_empresa;
        //dd($empresa);

        //ap_tipo_seg(madre) ---> varios tipos de seguro

        $archivo_plano = Archivo_Plano_Cabecera::where('mes_plano', $mes_plano)->where('id_seguro', $seguro)->where('id_cobertura_comp', $cob_compar)->where('id_empresa', $empresa)->get(); //join(madre, key , key)->where(madre.tipo de seguro)
        //dd($request->all(),$archivo_plano);

        Excel::create('Archivo Plano ', function ($excel) {
            $excel->sheet(date('Y-m-d'), function ($sheet) {
                $sheet->cell('A1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('G1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SEXO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('H1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('I1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('J1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('K1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('L1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('M1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('N1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('O1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('P1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('Q1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('R1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('S1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('T1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('U1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('V1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('W1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('X1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('Y1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('Z1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AA1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AB1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AC1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AD1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AE1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AF1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
            });
        })->export('xlsx');
    }

    public function procedimiento_plantilla(Request $request)
    {

        $nombre_proc = $request['term'];
        $data         = null;

        $seteo = '%' . $nombre_proc . '%';

        $query1 = "SELECT id, descripcion
                  FROM ap_plantilla
                  WHERE descripcion like '" . $seteo . "' or id like '" . $seteo . "'  LIMIT 100
                  ";


        $procedimientos = DB::select($query1);

        foreach ($procedimientos as $value) {
            $data[] = array('value' => $value->descripcion, 'id' => $value->id);
        }

        if (count($data) > 0) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }
    }

    public function cardiologia($cabecera)
    {

        $a_plano = Archivo_Plano_Cabecera::find($cabecera);
        $fecha_hasta = date('Y/m/d');
        $fecha = strtotime('-3 month', strtotime($fecha_hasta));
        $fecha = date('Y/m/d', $fecha);

        $interconsulta = Ap_Interconsulta_Espe::all();

        foreach ($interconsulta as $value) {
            $ic[$value->id_esp] = Agenda::where('agenda.id_paciente', $a_plano->id_paciente)
                ->where('espid', $value->id_esp)
                ->where('agenda.estado', '!=', '0')
                ->wherebetween('agenda.fechaini', [$fecha . '  0:00:00', $fecha_hasta . ' 23:59:59'])
                ->join('historiaclinica as h', 'h.id_agenda', 'agenda.id')
                ->join('hc_evolucion as hc_e', 'hc_e.hcid', 'h.hcid')
                ->join('hc_procedimientos as hc_pro', 'hc_pro.id_hc', 'h.hcid')
                ->leftjoin('seguros as s', 's.id', 'hc_pro.id_seguro')
                ->leftjoin('empresa as emp', 'emp.id', 'hc_pro.id_empresa')
                ->leftjoin('users as u', 'u.id', 'hc_pro.id_doctor_examinador')
                ->leftjoin('especialidad as espe', 'espe.id', 'agenda.espid')
                ->select('agenda.fechaini', 'hc_pro.id_seguro', 'hc_pro.id_empresa', 'hc_pro.id_doctor_examinador', 's.nombre as nombre_seguro', 'emp.nombre_corto', 'u.nombre1', 'u.nombre2', 'u.apellido1', 'u.apellido2', 'h.hcid', 'h.id_seguro as seguro_historia', 'agenda.id_empresa as empresa_agenda', 'agenda.id as id_agenda', 'espe.nombre as nombre_espe')
                ->whereNotNull('hc_e.cuadro_clinico')
                ->orderBy('agenda.fechaini', 'desc')->get();
        }
        //dd($ic);

        return view('archivo_plano/planilla/cardiologia', ['ic' => $ic, 'fecha' => $fecha, 'fecha_hasta' => $fecha_hasta, 'a_plano' => $a_plano]);
    }

    public function busca_cardiologia(Request $request)
    {
        $a_plano = Archivo_Plano_Cabecera::find($request->id);
        $fecha_hasta = $request->fecha_hasta;
        $fecha = $request->fecha;

        $interconsulta = Ap_Interconsulta_Espe::all();

        foreach ($interconsulta as $value) {
            $ic[$value->id_esp] = Agenda::where('agenda.id_paciente', $a_plano->id_paciente)
                ->where('espid', $value->id_esp)
                ->where('agenda.estado', '!=', '0')
                ->join('historiaclinica as h', 'h.id_agenda', 'agenda.id')
                ->join('hc_evolucion as hc_e', 'hc_e.hcid', 'h.hcid')
                ->join('hc_procedimientos as hc_pro', 'hc_pro.id_hc', 'h.hcid')
                ->leftjoin('seguros as s', 's.id', 'hc_pro.id_seguro')
                ->leftjoin('empresa as emp', 'emp.id', 'hc_pro.id_empresa')
                ->leftjoin('empresa as ep', 'ep.id', 'agenda.id_empresa')
                ->leftjoin('users as u', 'u.id', 'hc_pro.id_doctor_examinador')
                ->leftjoin('especialidad as espe', 'espe.id', 'agenda.espid')
                ->select('agenda.fechaini', 'hc_pro.id_seguro', 'hc_pro.id_empresa', 'hc_pro.id_doctor_examinador', 's.nombre as nombre_seguro', 'emp.nombre_corto', 'u.nombre1', 'u.nombre2', 'u.apellido1', 'u.apellido2', 'h.hcid', 'h.id_seguro as seguro_historia', 'agenda.id_empresa as empresa_agenda', 'agenda.id as id_agenda', 'espe.nombre as nombre_espe', 'ep.nombre_corto as emp_nom_ag')
                ->whereNotNull('hc_e.cuadro_clinico')
                ->orderBy('agenda.fechaini', 'desc');

            if ($fecha != null) {
                $ic[$value->id_esp] = $ic[$value->id_esp]->wherebetween('agenda.fechaini', [$fecha . '  0:00:00', $fecha_hasta . ' 23:59:59']);
            }

            $ic[$value->id_esp] = $ic[$value->id_esp]->get();
        }


        //dd($ic);

        //dd($cardio);
        return view('archivo_plano/planilla/lista_cardio', ['a_plano' => $a_plano, 'fecha_hasta' => $fecha_hasta, 'fecha' => $fecha, 'ic' => $ic]);
    }

    public function ingresar_cardio($hc_cardio, $cabecera, $seguro, $empresa, $agenda)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $codigo = '99203';
        $hc_cardiologia = Hc_evolucion::where('hcid', $hc_cardio)->where('secuencia', '0')->first();
        //return $hc_cardiologia;
        $convenio = Convenio::where('id_seguro', $seguro)->where('id_empresa', $empresa)->first();
        $proc = ApProcedimiento::where('ap_procedimiento.codigo', $codigo)->join('ap_procedimiento_nivel as apn', 'apn.id_procedimiento', 'ap_procedimiento.id')->where('apn.cod_conv', $convenio->id_nivel)->select('ap_procedimiento.descripcion', 'apn.uvr1', 'apn.prc1')->first();
        $agenda_cardio = Agenda::find($agenda);


        $arr = [
            'id_ap_cabecera' => $cabecera,
            'fecha' => $agenda_cardio->fechaini,
            'id_nivel' => $convenio->id_nivel,
            'hc_cardio' => $hc_cardiologia->hcid,
            'tipo' => 'P',
            'codigo' => $codigo,
            'descripcion' => $proc->descripcion,
            'cantidad' => '1',
            'subtotal' => $proc->uvr1 * $proc->prc1,
            'valor' => $proc->uvr1 * $proc->prc1,
            'valor_unitario' => $proc->uvr1 * $proc->prc1,
            'iva' => '0',
            'total' => $proc->uvr1 * $proc->prc1,
            'total_solicitado_usd' => $proc->uvr1 * $proc->prc1,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario,
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
        ];
        Archivo_Plano_Detalle::create($arr);



        return "ok";
    }

    public function crear_item(Request $request)
    {

        $niv_conv = $request['nivel_convenio'];
        $fech = $request['fecha'];
        $tip = $request['tipo'];
        $codig = $request['codigo'];
        $descrip = $request['descripcion'];
        $cantid = $request['cantidad'];
        $prec = $request['precio'];
        $iv = $request['iva'];
        $hon_anest = $request['hono_Anest'];
        $val_tiemp_anest = $request['val_tmp_anest'];
        $cant_tiemp_anest = $request['tiempo_Anest'];
        $proce_sep = $request['proceso_separ'];
        $porcent_hono = $request['porcent_honorario'];

        if (($hon_anest > 0) && ($porcent_hono == 100)) {

            return view('archivo_plano/planilla/detalle_items');
        } else if (($hon_anest > 0) && ($porcent_hono == 50)) {

            return view('archivo_plano/planilla/detalle_items');
        } else if ($hon_anest == 0) {

            $id = 0;


            //Calculo de Valores a Mostrar en Detalle Items
            $total = round(($cantid * $prec), 2);
            //return view('archivo_plano/planilla/detalle_items', ['tip' => $tip,'codig' => $codig,'descrip' => $descrip,'cantid' => $cantid,'prec' => $prec,'iv' => $iv,'total' => $total]);
            return ['id' => $id, 'tip' => $tip, 'codig' => $codig, 'descrip' => $descrip, 'cantid' => $cantid, 'prec' => $prec, 'iv' => $iv, 'total' => $total];
        }
    }

    public function crear_registro(Request $request)
    {

        $servicios = DB::table('codigo_ dependencia')
            ->update(array(
                'id_usuariocrea' => '0922794102',
            ));
        //192.168.75.131
    }

    public function reporte_seguros_privados(Request $request)
    {

        $mes_plano = $request['mes_plano'];

        $seg = $request['seguro'];
        $tipo_seg = $request['id_tipo_seguro'];
        $empresa = $request['id_empresa'];
        $sub_cadena_1 = '';
        $sub_cadena_2 = '';

        if (!is_null($mes_plano)) {
            $sub_cadena_1 = substr($mes_plano, 0, 2);
        }

        if (!is_null($mes_plano)) {
            $sub_cadena_2 = substr($mes_plano, 2, 6);
        }

        $inf_empresa = Empresa::where('id', $empresa)->where('estado', '1')->first();

        $archivo_plano = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->select('id_paciente', 'id_seguro_priv')
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            ->where('archivo_plano_cabecera.estado', '1')
            ->whereNotNull('id_seguro_priv')
            ->groupBy('id_paciente', 'id_seguro_priv')
            ->orderby('id_paciente', 'id_seguro_priv')
            ->get();

        //dd($archivo_plano);

        Excel::create('ReportesegurosPrivados' . $mes_plano, function ($excel) use ($inf_empresa, $sub_cadena_1, $sub_cadena_2, $archivo_plano, $mes_plano, $empresa) {
            $excel->sheet('ReportesegurosPrivados' . $mes_plano, function ($sheet) use ($inf_empresa, $sub_cadena_1, $sub_cadena_2, $archivo_plano, $mes_plano, $empresa) {

                $objDrawing = new PHPExcel_Worksheet_Drawing;
                $objDrawing->setPath(base_path() . '/storage/app/logo/iess_logo.png');
                $objDrawing->setCoordinates('N2');
                $objDrawing->setHeight(70);
                $objDrawing->setWorksheet($sheet);

                $sheet->mergeCells('A2:N2');
                $sheet->cell('A2', function ($cell) use ($inf_empresa) {
                    $cell->setValue('INSTITUTO ECUATORIANO DE SEGURIDAD SOCIAL');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('14');
                });

                $sheet->mergeCells('A4:N4');
                $sheet->cell('A4', function ($cell) use ($inf_empresa) {
                    $cell->setValue('COORDINACIÓN PROVINCIAL DE PRESTACIONES DEL SEGURO DE SALUD GUAYAS');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                });

                $sheet->mergeCells('A6:P6');
                $sheet->cell('A6', function ($cell) use ($inf_empresa) {
                    $cell->setValue('PACIENTES COBERTURA SEGURO PRIVADO');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('A7:B7');
                $sheet->cell('A7', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Nombre prestador:');
                    $cell->setAlignment('left');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('C7:P7');
                $sheet->cell('C7', function ($cell) use ($inf_empresa) {
                    $cell->setValue($inf_empresa->razonsocial);
                    $cell->setAlignment('left');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('A8:B8');
                $sheet->cell('A8', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Teléfono:');
                    $cell->setAlignment('left');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('C8:P8');
                $sheet->cell('C8', function ($cell) use ($inf_empresa) {
                    $cell->setValue('(04)2 109 180');
                    $cell->setAlignment('left');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('A9:B9');
                $sheet->cell('A9', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Correo::');
                    $cell->setAlignment('left');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('C9:P9');
                $sheet->cell('C9', function ($cell) use ($inf_empresa) {
                    $cell->setValue('gerenciageneral@ieced.ec');
                    $cell->setAlignment('left');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('A10:B10');
                $sheet->cell('A10', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Servicio:');
                    $cell->setAlignment('left');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('C10:P10');
                $sheet->cell('C10', function ($cell) use ($inf_empresa) {
                    $cell->setValue('AMBULATORIO');
                    $cell->setAlignment('left');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('A11:B11');
                $sheet->cell('A11', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Mes:');
                    $cell->setAlignment('left');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('C11:I11');
                $sheet->cell('C11', function ($cell) use ($sub_cadena_1) {

                    $txt_mes = '';

                    if ($sub_cadena_1 == '01') {
                        $txt_mes = 'ENERO';
                    } elseif ($sub_cadena_1 == '02') {
                        $txt_mes = 'FEBRERO';
                    } elseif ($sub_cadena_1 == '03') {
                        $txt_mes = 'MARZO';
                    } elseif ($sub_cadena_1 == '04') {
                        $txt_mes = 'ABRIL';
                    } elseif ($sub_cadena_1 == '05') {
                        $txt_mes = 'MAYO';
                    } elseif ($sub_cadena_1 == '06') {
                        $txt_mes = 'JUNIO';
                    } elseif ($sub_cadena_1 == '07') {
                        $txt_mes = 'JULIO';
                    } elseif ($sub_cadena_1 == '08') {
                        $txt_mes = 'AGOSTO';
                    } elseif ($sub_cadena_1 == '09') {
                        $txt_mes = 'SEPTIEMBRE';
                    } elseif ($sub_cadena_1 == '10') {
                        $txt_mes = 'OCTUBRE';
                    } elseif ($sub_cadena_1 == '11') {
                        $txt_mes = 'NOVIEMBRE';
                    } elseif ($sub_cadena_1 == '12') {
                        $txt_mes = 'DICIEMBRE';
                    }

                    $cell->setValue($txt_mes);
                    $cell->setAlignment('left');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('J11', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Año:');
                    $cell->setAlignment('left');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('k11:P11');
                $sheet->cell('K11', function ($cell) use ($sub_cadena_2) {
                    $cell->setValue($sub_cadena_2);
                    $cell->setAlignment('left');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('A12', function ($cell) use ($inf_empresa) {
                    $cell->setValue('No');
                    $cell->setAlignment('left');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('B12:I12');
                $sheet->cell('B12', function ($cell) use ($inf_empresa) {
                    $cell->setValue('NOMBRE DE PACIENTE/USUARIO');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('J12:P12');
                $sheet->cell('J12', function ($cell) use ($inf_empresa) {
                    $cell->setValue('SEGURO PRIVADO');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $i = 13;
                $x = 1;
                $j = 0;
                $nombre_prestador = '';
                $cargo_prestador = '';

                foreach ($archivo_plano as $value) {
                    //dd($value->id_seguro_privado);
                    /*if ($value->detalles->where('estado', '1')->count() > 0) {*/

                    //Escribo en excel
                    //No
                    $sheet->cell('A' . $i, function ($cell) use ($x) {
                        // manipulate the cel
                        $cell->setValue($x);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    //NOMBRE DE PACIENTE/USUARIO
                    $sheet->mergeCells('B' . $i . ':I' . $i);
                    $sheet->cell('B' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $paciente = Paciente::find($value['id_paciente']);
                        $cell->setValue($paciente->apellido1 . ' ' . $paciente->apellido2 . ' ' . $paciente->nombre1 . ' ' . $paciente->nombre2);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->mergeCells('J' . $i . ':P' . $i);
                    $sheet->cell('J' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $seguro_privado = Seguro::find($value['id_seguro_priv']);
                        //dd($value['seguro_privado']);
                        //dd($value);
                        $cell->setValue($seguro_privado->nombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $x++;
                    $i++;
                    /* } */
                }

                if ($empresa == '0992704152001') {

                    $nombre_prestador = 'Econ. GONZALO CAICEDO LOOR';
                    $cargo_prestador = 'GERENTE GENERAL';
                } else if ($empresa == '1307189140001') {

                    $nombre_prestador = 'Dr. Carlos Robles Medranda.';
                    $cargo_prestador = 'GASTROENTEROLOGO';
                }

                $sheet->mergeCells('A' . $i . ':P' . $i);
                $sheet->cell('A' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Atentamente.');
                    $cell->setAlignment('left');
                    $cell->setBorder('', '', '', '');
                });

                $j = $i + 4;

                $sheet->mergeCells('A' . $j . ':P' . $j);
                $sheet->cell('A' . $j, function ($cell) use ($nombre_prestador) {
                    // manipulate the cel
                    $cell->setValue($nombre_prestador);
                    $cell->setAlignment('left');
                    $cell->setFontSize('12');
                    $cell->setBorder('', '', '', '');
                });

                $j = $j + 1;

                $sheet->mergeCells('A' . $j . ':P' . $j);
                $sheet->cell('A' . $j, function ($cell) use ($cargo_prestador) {
                    // manipulate the cel
                    $cell->setValue($cargo_prestador);
                    $cell->setAlignment('left');
                    $cell->setFontSize('12');
                    $cell->setBorder('', '', '', '');
                });

                $j = $j + 1;

                $sheet->mergeCells('A' . $j . ':P' . $j);
                $sheet->cell('A' . $j, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PRESTADOR EXTERNO CON PROCEDIMIENTOS.');
                    $cell->setAlignment('left');
                    $cell->setFontSize('10');
                    $cell->setBorder('', '', '', '');
                });
            });
        })->export('xlsx');
    }

    public function honorario_medico(Request $request)
    {
        //dd($request->all());
        $mes_plano = $request['mes_plano'];

        //$tipo_seg = $request['id_tipo_seguro'];
        $empresa = $request['id_empresa'];
        $seg = $request['seguro'];
        $nombre_seguro = Seguro::find($seg)->nombre;

        $honor_medicos_activos = [];
        $honor_medicos_jubilado = [];
        $honor_medicos_jub_campesino = [];
        $honor_medicos_montepio = [];


        //ACTIVO  SG
        $honor_medicos_activos = Db::table('archivo_plano_detalle as apd')
            ->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')
            ->join('archivo_plano_cabecera as ap', 'ap.id', 'apd.id_ap_cabecera')
            ->join('paciente as p', 'p.id', 'ap.id_paciente')
            ->join('tipo_seguro as ts', 'ts.id', 'ap.id_tipo_seguro')
            ->where(function ($query) {
                $query->where('ap.id_tipo_seguro', '1')
                    ->orwhere('ap.id_tipo_seguro', '2')
                    ->orwhere('ap.id_tipo_seguro', '3')
                    ->orwhere('ap.id_tipo_seguro', '4')
                    ->orwhere('ap.id_tipo_seguro', '5');
            })
            ->where('mes_plano', $mes_plano)
            ->where('apt.tipo_ex', 'HME')
            ->where('ap.estado', '1')
            ->where('apd.estado', '1')
            ->where('ap.id_seguro', $seg)
            ->where('ap.id_empresa', $empresa)
            //->orderby('ap.fecha_ing', 'asc')
            //->orderby('p.apellido1', 'asc')
            ->select(DB::raw('CONCAT(p.apellido1, p.apellido2, p.nombre1, p.nombre2) AS full_name'), 'p.*', 'apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total', 'ts.nombre as tsnombre', 'ap.id_paciente', 'p.apellido1', 'p.apellido2', 'p.nombre1', 'p.nombre2', 'ap.fecha_ing', 'ap.nom_procedimiento', 'apd.porcentaje_honorario', 'apd.clasif_porcentaje_msp', 'ap.id_nivel')
            //->orderby('apd.porcentaje_honorario','desc')
            //->orderby('apt.secuencia','asc')
            ->orderby('full_name')
            ->orderby('ap.fecha_ing', 'asc')
            ->get();

        //JUBILADO JU
        $honor_medicos_jubilado = Db::table('archivo_plano_detalle as apd')
            ->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')
            ->join('archivo_plano_cabecera as ap', 'ap.id', 'apd.id_ap_cabecera')
            ->join('paciente as p', 'p.id', 'ap.id_paciente')
            ->join('tipo_seguro as ts', 'ts.id', 'ap.id_tipo_seguro')
            ->where('mes_plano', $mes_plano)
            ->where('apt.tipo_ex', 'HME')
            ->where('ap.estado', '1')
            ->where('apd.estado', '1')
            ->where('ap.id_seguro', $seg)
            ->where('ap.id_tipo_seguro', '6')
            ->where('ap.id_empresa', $empresa)
            //->orderby('ap.fecha_ing', 'asc')
            //->orderby('p.apellido1', 'asc')
            ->select(DB::raw('CONCAT(p.apellido1, p.apellido2, p.nombre1, p.nombre2) AS full_name'), 'p.*', 'apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total', 'ts.nombre as tsnombre', 'ap.id_paciente', 'p.apellido1', 'p.apellido2', 'p.nombre1', 'p.nombre2', 'ap.fecha_ing', 'ap.nom_procedimiento', 'apd.porcentaje_honorario', 'ap.id_nivel')
            //->orderby('apd.porcentaje_honorario','desc')
            //->orderby('apt.secuencia','asc')
            ->orderby('full_name')
            ->orderby('ap.fecha_ing', 'asc')
            ->get();


        //JUBILADO CAMPESINO JC
        $honor_medicos_jub_campesino = Db::table('archivo_plano_detalle as apd')
            ->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')
            ->join('archivo_plano_cabecera as ap', 'ap.id', 'apd.id_ap_cabecera')
            ->join('paciente as p', 'p.id', 'ap.id_paciente')
            ->join('tipo_seguro as ts', 'ts.id', 'ap.id_tipo_seguro')
            ->where('mes_plano', $mes_plano)
            ->where('apt.tipo_ex', 'HME')
            ->where('ap.estado', '1')
            ->where('apd.estado', '1')
            ->where('ap.id_seguro', $seg)
            ->where('ap.id_tipo_seguro', '7')
            ->where('ap.id_empresa', $empresa)
            //->orderby('ap.fecha_ing', 'asc')
            //->orderby('p.apellido1', 'asc')
            ->select(DB::raw('CONCAT(p.apellido1, p.apellido2, p.nombre1, p.nombre2) AS full_name'), 'p.*', 'apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total', 'ts.nombre as tsnombre', 'ap.id_paciente', 'p.apellido1', 'p.apellido2', 'p.nombre1', 'p.nombre2', 'ap.fecha_ing', 'ap.nom_procedimiento', 'apd.porcentaje_honorario', 'ap.id_nivel')
            //->orderby('apd.porcentaje_honorario','desc')
            //->orderby('apt.secuencia','asc')
            ->orderby('full_name')
            ->orderby('ap.fecha_ing', 'asc')
            ->get();

        //MONTEPIO MO
        $honor_medicos_montepio = Db::table('archivo_plano_detalle as apd')
            ->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')
            ->join('archivo_plano_cabecera as ap', 'ap.id', 'apd.id_ap_cabecera')
            ->join('paciente as p', 'p.id', 'ap.id_paciente')
            ->join('tipo_seguro as ts', 'ts.id', 'ap.id_tipo_seguro')
            ->where('mes_plano', $mes_plano)
            ->where('apt.tipo_ex', 'HME')
            ->where('ap.estado', '1')
            ->where('apd.estado', '1')
            ->where('ap.id_seguro', $seg)
            ->where('ap.id_tipo_seguro', '8')
            ->where('ap.id_empresa', $empresa)
            //->orderby('ap.fecha_ing', 'asc')
            //->orderby('p.apellido1', 'asc')
            ->select(DB::raw('CONCAT(p.apellido1, p.apellido2, p.nombre1, p.nombre2) AS full_name'), 'p.*', 'apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total', 'ts.nombre as tsnombre', 'ap.id_paciente', 'p.apellido1', 'p.apellido2', 'p.nombre1', 'p.nombre2', 'ap.fecha_ing', 'ap.nom_procedimiento', 'apd.porcentaje_honorario', 'ap.id_nivel')
            //->orderby('apd.porcentaje_honorario','desc')
            //->orderby('apt.secuencia','asc')
            ->orderby('full_name')
            ->orderby('ap.fecha_ing', 'asc')
            ->get();

        //SSC
        $honor_medicos_ssc = Db::table('archivo_plano_detalle as apd')
            ->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')
            ->join('archivo_plano_cabecera as ap', 'ap.id', 'apd.id_ap_cabecera')
            ->join('paciente as p', 'p.id', 'ap.id_paciente')
            ->join('tipo_seguro as ts', 'ts.id', 'ap.id_tipo_seguro')
            ->where('mes_plano', $mes_plano)
            ->where('apt.tipo_ex', 'HME')
            ->where('ap.estado', '1')
            ->where('apd.estado', '1')
            ->where('ap.id_seguro', $seg)
            ->where('ap.id_tipo_seguro', '9')
            ->where('ap.id_empresa', $empresa)
            //->orderby('ap.fecha_ing', 'asc')
            //->orderby('p.apellido1', 'asc')
            ->select(DB::raw('CONCAT(p.apellido1, p.apellido2, p.nombre1, p.nombre2) AS full_name'), 'p.*', 'apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total', 'ts.nombre as tsnombre', 'ap.id_paciente', 'p.apellido1', 'p.apellido2', 'p.nombre1', 'p.nombre2', 'ap.fecha_ing', 'ap.nom_procedimiento', 'apd.porcentaje_honorario', 'ap.id_nivel')
            //->orderby('apd.porcentaje_honorario','desc')
            //->orderby('apt.secuencia','asc')
            ->orderby('full_name')
            ->orderby('ap.fecha_ing', 'asc')
            ->get();

        Excel::create('Honorario_medico_' . $nombre_seguro, function ($excel) use ($honor_medicos_activos, $honor_medicos_jubilado, $honor_medicos_jub_campesino, $honor_medicos_montepio, $honor_medicos_ssc, $seg) {
            $excel->sheet(date('Y-m-d'), function ($sheet) use ($honor_medicos_activos, $honor_medicos_jubilado, $honor_medicos_jub_campesino, $honor_medicos_montepio, $honor_medicos_ssc, $seg) {


                $sheet->cell('A1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TIPO SEGURO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('B1', function ($cell) {
                    $cell->setValue('CEDULA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('C1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NOMBRE BENEFICIARIO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('D1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA INGRESO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('E1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PROCEDIMIENTO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('F1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CODIGO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('G1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DESCRIPCION HONORARIO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('H1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TIPO HONORARIO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('I1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TOTAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('J1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DOCTOR');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $i = 2;
                $sheet->setColumnFormat(array(
                    'I' => '"$"* # ##0.00',
                ));

                foreach ($honor_medicos_activos as $value) {

                    if ($value->codigo != 93000 && $value->codigo != 99253 && $value->codigo != 99205 && $value->codigo != 99005 && $value->codigo != 99202 && $value->codigo != 99203 && $value->codigo != 99204 && $value->codigo != 93005 && $value->codigo != 202 && $value->codigo != 203 && $value->codigo != 204 && $value->tipo != 'TA' && $value->tipo != 'AN' && $value->codigo != 99213) {
                        $sheet->cell('A' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            //$cell->setValue($value->tsnombre);
                            $cell->setValue('ACTIVO');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('B' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_paciente);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('C' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->apellido1 . ' ' . $value->apellido2 . ' ' . $value->nombre1 . ' ' . $value->nombre2);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $fecha_ing = substr($value->fecha_ing, 0, 10);
                        $fecha_ing_inv = date("d/m/Y", strtotime($fecha_ing));
                        $sheet->cell('D' . $i, function ($cell) use ($value, $fecha_ing_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_ing_inv);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('E' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->nom_procedimiento);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $val_codigo = '';
                        //$val_codigo_2 = '';
                        $sheet->cell('F' . $i, function ($cell)  use ($value, $val_codigo) {
                            // manipulate the cel

                            /*if($value->codigo == '70200004')
                            {
                                $val_codigo = '45380';

                            }elseif($value->codigo == '70200003'){

                                $val_codigo = '43239';

                            }else{

                                $val_codigo = $value->codigo;

                            } */
                            $val_codigo = $value->codigo;

                            $cell->setValue($val_codigo);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('G' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->descripcion);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $tipo_honor = '100%';
                        if ($seg == '2') {
                            if ($value->porcentaje_honorario == '50') {
                                //$tipo_honor = 0.5;
                                $tipo_honor = '50%';
                            }
                        }
                        if ($seg == '5') {
                            if ($value->clasif_porcentaje_msp == '50') {
                                //$tipo_honor = 0.5;
                                $tipo_honor = '50%';
                            }
                        }

                        $sheet->cell('H' . $i, function ($cell) use ($tipo_honor) {
                            // manipulate the cel
                            $cell->setValue($tipo_honor);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('I' . $i, function ($cell) use ($value) {

                            $k_valor = 0;
                            $val_total = 0;
                            $val_unit = 0;
                            $subtotal = 0;
                            $valor10 = 0;
                            $valor_iva  = 0;
                            $total = 0;
                            $val = 0;
                            $val_porce = 0;

                            if ($value->codigo == '70200004') {
                                /*$val_codigo = '45380';

                                $val_proc = ApProcedimiento::where('codigo', $val_codigo)
                                            ->where('estado','1')->first();

                                $valor_nivel = ApProcedimientoNivel::where('codigo', $val_codigo)
                                            ->where('estado','1')
                                            ->where('cod_conv',$value->id_nivel)->first();


                                if(!is_null($valor_nivel)){

                                    $k_valor = round(($valor_nivel->uvr1*$valor_nivel->prc1),2);

                                }

                                $val =$k_valor;
                                if(!is_null($val_proc)){
                                  $val_porce = $val_proc->porcentaje10;
                                }

                                $val_unit = $val/(1+$val_porce);

                                $subtotal = 1 *$val_unit;

                                $valor10 =$subtotal*$val_porce;

                                if(!is_null($val_proc)){
                                 $valor_iva =$subtotal* $val_proc->iva;
                                }
                                $total = $subtotal+$valor10+$valor_iva;

                                $val_total = $total;*/
                                $val_total = 65.5;
                            } elseif ($value->codigo == '70200003') {

                                /*$val_codigo = '43239';

                                $val_proc = ApProcedimiento::where('codigo', $val_codigo)
                                            ->where('estado','1')->first();

                                $valor_nivel = ApProcedimientoNivel::where('codigo', $val_codigo)
                                            ->where('estado','1')
                                            ->where('cod_conv',$value->id_nivel)->first();


                                if(!is_null($valor_nivel)){

                                    $k_valor = round(($valor_nivel->uvr1*$valor_nivel->prc1),2);

                                }

                                $val =$k_valor;
                                if(!is_null($val_proc)){
                                  $val_porce = $val_proc->porcentaje10;
                                }

                                $val_unit = $val/(1+$val_porce);

                                $subtotal = 1 *$val_unit;

                                $valor10 =$subtotal*$val_porce;

                                if(!is_null($val_proc)){
                                 $valor_iva =$subtotal* $val_proc->iva;
                                }
                                $total = $subtotal+$valor10+$valor_iva;

                                $val_total = $total;*/
                                $val_total = 34;
                            } else {

                                $val_total = $value->total;
                            }

                            // manipulate the cel
                            $cell->setValue($val_total);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $txt_doctor = 'DR. CARLOS ROBLES';
                        if ($value->codigo == 91034 || $value->codigo == 91037 || $value->codigo == 91010 || $value->codigo == 91110 || $value->codigo == 91122 || $value->codigo == 91013) {
                            $txt_doctor = 'DR(a) HANNAH PITANGA';
                        }

                        $sheet->cell('J' . $i, function ($cell) use ($value, $txt_doctor) {
                            // manipulate the cel
                            $cell->setValue($txt_doctor);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $i++;
                    }
                }


                foreach ($honor_medicos_jubilado as $value) {

                    if ($value->codigo != 93000 && $value->codigo != 99253 && $value->codigo != 99205 && $value->codigo != 99005 && $value->codigo != 99202 && $value->codigo != 99203 && $value->codigo != 99204 && $value->codigo != 93005 && $value->codigo != 202 && $value->codigo != 203 && $value->codigo != 204 && $value->tipo != 'TA' && $value->tipo != 'AN' && $value->codigo != 99213) {
                        $sheet->cell('A' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->tsnombre);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('B' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_paciente);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('C' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->apellido1 . ' ' . $value->apellido2 . ' ' . $value->nombre1 . ' ' . $value->nombre2);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $fecha_ing = substr($value->fecha_ing, 0, 10);
                        $fecha_ing_inv = date("d/m/Y", strtotime($fecha_ing));
                        $sheet->cell('D' . $i, function ($cell) use ($value, $fecha_ing_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_ing_inv);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('E' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->nom_procedimiento);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $val_codigo = '';
                        $sheet->cell('F' . $i, function ($cell)  use ($value, $val_codigo) {
                            // manipulate the cel
                            /*if($value->codigo == '70200004')
                            {
                                $val_codigo = '45380';

                            }elseif($value->codigo == '70200003'){

                                $val_codigo = '43239';

                            }else{

                                $val_codigo = $value->codigo;

                            }*/

                            $val_codigo = $value->codigo;

                            $cell->setValue($val_codigo);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('G' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->descripcion);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $tipo_honor = '100%';
                        if ($value->porcentaje_honorario == '50') {
                            //$tipo_honor = 0.5;
                            $tipo_honor = '50%';
                        }
                        $sheet->cell('H' . $i, function ($cell) use ($tipo_honor) {
                            // manipulate the cel
                            $cell->setValue($tipo_honor);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });


                        $sheet->cell('I' . $i, function ($cell) use ($value) {

                            $k_valor = 0;
                            $val_total = 0;
                            $val_unit = 0;
                            $subtotal = 0;
                            $valor10 = 0;
                            $valor_iva  = 0;
                            $total = 0;
                            $val = 0;
                            $val_porce = 0;

                            if ($value->codigo == '70200004') {
                                /*$val_codigo = '45380';

                                $val_proc = ApProcedimiento::where('codigo', $val_codigo)
                                            ->where('estado','1')->first();

                                $valor_nivel = ApProcedimientoNivel::where('codigo', $val_codigo)
                                            ->where('estado','1')
                                            ->where('cod_conv',$value->id_nivel)->first();


                                if(!is_null($valor_nivel)){

                                    $k_valor = round(($valor_nivel->uvr1*$valor_nivel->prc1),2);

                                }

                                $val =$k_valor;
                                if(!is_null($val_proc)){
                                  $val_porce = $val_proc->porcentaje10;
                                }

                                $val_unit = $val/(1+$val_porce);

                                $subtotal = 1 *$val_unit;

                                $valor10 =$subtotal*$val_porce;

                                if(!is_null($val_proc)){
                                 $valor_iva =$subtotal* $val_proc->iva;
                                }
                                $total = $subtotal+$valor10+$valor_iva;

                                $val_total = $total;*/
                                $val_total = 65.5;
                            } elseif ($value->codigo == '70200003') {

                                /*$val_codigo = '43239';

                                $val_proc = ApProcedimiento::where('codigo', $val_codigo)
                                            ->where('estado','1')->first();

                                $valor_nivel = ApProcedimientoNivel::where('codigo', $val_codigo)
                                            ->where('estado','1')
                                            ->where('cod_conv',$value->id_nivel)->first();


                                if(!is_null($valor_nivel)){

                                    $k_valor = round(($valor_nivel->uvr1*$valor_nivel->prc1),2);

                                }

                                $val =$k_valor;
                                if(!is_null($val_proc)){
                                  $val_porce = $val_proc->porcentaje10;
                                }

                                $val_unit = $val/(1+$val_porce);

                                $subtotal = 1 *$val_unit;

                                $valor10 =$subtotal*$val_porce;

                                if(!is_null($val_proc)){
                                 $valor_iva =$subtotal* $val_proc->iva;
                                }
                                $total = $subtotal+$valor10+$valor_iva;

                                $val_total = $total;*/
                                $val_total = 34;
                            } else {

                                $val_total = $value->total;
                            }

                            // manipulate the cel
                            $cell->setValue($val_total);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $txt_doctor = 'DR. CARLOS ROBLES';
                        if ($value->codigo == 91034 || $value->codigo == 91037 || $value->codigo == 91010 || $value->codigo == 91110 || $value->codigo == 91122 || $value->codigo == 91013) {
                            $txt_doctor = 'DR(a) HANNAH PITANGA';
                        }

                        $sheet->cell('J' . $i, function ($cell) use ($value, $txt_doctor) {
                            // manipulate the cel
                            $cell->setValue($txt_doctor);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $i++;
                    }
                }


                foreach ($honor_medicos_jub_campesino as $value) {

                    if ($value->codigo != 93000 && $value->codigo != 99253 && $value->codigo != 99205 && $value->codigo != 99005 && $value->codigo != 99202 && $value->codigo != 99203 && $value->codigo != 99204 && $value->codigo != 93005 && $value->codigo != 202 && $value->codigo != 203 && $value->codigo != 204 && $value->tipo != 'TA' && $value->tipo != 'AN' && $value->codigo != 99213) {
                        $sheet->cell('A' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->tsnombre);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('B' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_paciente);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('C' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->apellido1 . ' ' . $value->apellido2 . ' ' . $value->nombre1 . ' ' . $value->nombre2);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $fecha_ing = substr($value->fecha_ing, 0, 10);
                        $fecha_ing_inv = date("d/m/Y", strtotime($fecha_ing));
                        $sheet->cell('D' . $i, function ($cell) use ($value, $fecha_ing_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_ing_inv);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('E' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->nom_procedimiento);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $val_codigo = '';
                        $sheet->cell('F' . $i, function ($cell)  use ($value, $val_codigo) {

                            /*if($value->codigo == '70200004')
                            {
                                $val_codigo = '45380';

                            }elseif($value->codigo == '70200003'){

                                $val_codigo = '43239';

                            }else{

                                $val_codigo = $value->codigo;

                            } */
                            $val_codigo = $value->codigo;

                            // manipulate the cel
                            $cell->setValue($val_codigo);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('G' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->descripcion);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $tipo_honor = '100%';
                        if ($value->porcentaje_honorario == '50') {
                            //$tipo_honor = 0.5;
                            $tipo_honor = '50%';
                        }
                        $sheet->cell('H' . $i, function ($cell) use ($tipo_honor) {
                            // manipulate the cel
                            $cell->setValue($tipo_honor);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('I' . $i, function ($cell) use ($value) {

                            $k_valor = 0;
                            $val_total = 0;
                            $val_unit = 0;
                            $subtotal = 0;
                            $valor10 = 0;
                            $valor_iva  = 0;
                            $total = 0;
                            $val = 0;
                            $val_porce = 0;

                            if ($value->codigo == '70200004') {
                                /*$val_codigo = '45380';

                                $val_proc = ApProcedimiento::where('codigo', $val_codigo)
                                            ->where('estado','1')->first();

                                $valor_nivel = ApProcedimientoNivel::where('codigo', $val_codigo)
                                            ->where('estado','1')
                                            ->where('cod_conv',$value->id_nivel)->first();


                                if(!is_null($valor_nivel)){

                                    $k_valor = round(($valor_nivel->uvr1*$valor_nivel->prc1),2);

                                }

                                $val =$k_valor;
                                if(!is_null($val_proc)){
                                  $val_porce = $val_proc->porcentaje10;
                                }

                                $val_unit = $val/(1+$val_porce);

                                $subtotal = 1 *$val_unit;

                                $valor10 =$subtotal*$val_porce;

                                if(!is_null($val_proc)){
                                 $valor_iva =$subtotal* $val_proc->iva;
                                }
                                $total = $subtotal+$valor10+$valor_iva;

                                $val_total = $total;*/
                                $val_total = 65.5;
                            } elseif ($value->codigo == '70200003') {

                                /*$val_codigo = '43239';

                                $val_proc = ApProcedimiento::where('codigo', $val_codigo)
                                            ->where('estado','1')->first();

                                $valor_nivel = ApProcedimientoNivel::where('codigo', $val_codigo)
                                            ->where('estado','1')
                                            ->where('cod_conv',$value->id_nivel)->first();


                                if(!is_null($valor_nivel)){

                                    $k_valor = round(($valor_nivel->uvr1*$valor_nivel->prc1),2);

                                }

                                $val =$k_valor;
                                if(!is_null($val_proc)){
                                  $val_porce = $val_proc->porcentaje10;
                                }

                                $val_unit = $val/(1+$val_porce);

                                $subtotal = 1 *$val_unit;

                                $valor10 =$subtotal*$val_porce;

                                if(!is_null($val_proc)){
                                 $valor_iva =$subtotal* $val_proc->iva;
                                }
                                $total = $subtotal+$valor10+$valor_iva;

                                $val_total = $total;*/
                                $val_total = 34;
                            } else {

                                $val_total = $value->total;
                            }

                            // manipulate the cel
                            $cell->setValue($val_total);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $txt_doctor = 'DR. CARLOS ROBLES';
                        if ($value->codigo == 91034 || $value->codigo == 91037 || $value->codigo == 91010 || $value->codigo == 91110 || $value->codigo == 91122 || $value->codigo == 91013) {
                            $txt_doctor = 'DR(a) HANNAH PITANGA';
                        }

                        $sheet->cell('J' . $i, function ($cell) use ($value, $txt_doctor) {
                            // manipulate the cel
                            $cell->setValue($txt_doctor);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $i++;
                    }
                }


                foreach ($honor_medicos_montepio as $value) {

                    if ($value->codigo != 93000 && $value->codigo != 99253 && $value->codigo != 99205 && $value->codigo != 99005 && $value->codigo != 99202 && $value->codigo != 99203 && $value->codigo != 99204 && $value->codigo != 93005 && $value->codigo != 202 && $value->codigo != 203 && $value->codigo != 204 && $value->tipo != 'TA' && $value->tipo != 'AN' && $value->codigo != 99213) {
                        $sheet->cell('A' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->tsnombre);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('B' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_paciente);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('C' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->apellido1 . ' ' . $value->apellido2 . ' ' . $value->nombre1 . ' ' . $value->nombre2);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $fecha_ing = substr($value->fecha_ing, 0, 10);
                        $fecha_ing_inv = date("d/m/Y", strtotime($fecha_ing));
                        $sheet->cell('D' . $i, function ($cell) use ($value, $fecha_ing_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_ing_inv);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('E' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->nom_procedimiento);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $val_codigo = '';
                        $sheet->cell('F' . $i, function ($cell)  use ($value, $val_codigo) {

                            /*if($value->codigo == '70200004')
                            {
                                $val_codigo = '45380';

                            }elseif($value->codigo == '70200003'){

                                $val_codigo = '43239';

                            }else{

                                $val_codigo = $value->codigo;

                            } */
                            $val_codigo = $value->codigo;

                            // manipulate the cel
                            $cell->setValue($val_codigo);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('G' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->descripcion);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $tipo_honor = '100%';
                        if ($value->porcentaje_honorario == '50') {
                            //$tipo_honor = 0.5;
                            $tipo_honor = '50%';
                        }
                        $sheet->cell('H' . $i, function ($cell) use ($tipo_honor) {
                            // manipulate the cel
                            $cell->setValue($tipo_honor);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('I' . $i, function ($cell) use ($value) {

                            $k_valor = 0;
                            $val_total = 0;
                            $val_unit = 0;
                            $subtotal = 0;
                            $valor10 = 0;
                            $valor_iva  = 0;
                            $total = 0;
                            $val = 0;
                            $val_porce = 0;

                            if ($value->codigo == '70200004') {
                                /*$val_codigo = '45380';

                                $val_proc = ApProcedimiento::where('codigo', $val_codigo)
                                            ->where('estado','1')->first();

                                $valor_nivel = ApProcedimientoNivel::where('codigo', $val_codigo)
                                            ->where('estado','1')
                                            ->where('cod_conv',$value->id_nivel)->first();


                                if(!is_null($valor_nivel)){

                                    $k_valor = round(($valor_nivel->uvr1*$valor_nivel->prc1),2);

                                }

                                $val =$k_valor;
                                if(!is_null($val_proc)){
                                  $val_porce = $val_proc->porcentaje10;
                                }

                                $val_unit = $val/(1+$val_porce);

                                $subtotal = 1 *$val_unit;

                                $valor10 =$subtotal*$val_porce;

                                if(!is_null($val_proc)){
                                 $valor_iva =$subtotal* $val_proc->iva;
                                }
                                $total = $subtotal+$valor10+$valor_iva;

                                $val_total = $total;*/
                                $val_total = 65.5;
                            } elseif ($value->codigo == '70200003') {

                                /*$val_codigo = '43239';

                                $val_proc = ApProcedimiento::where('codigo', $val_codigo)
                                            ->where('estado','1')->first();

                                $valor_nivel = ApProcedimientoNivel::where('codigo', $val_codigo)
                                            ->where('estado','1')
                                            ->where('cod_conv',$value->id_nivel)->first();


                                if(!is_null($valor_nivel)){

                                    $k_valor = round(($valor_nivel->uvr1*$valor_nivel->prc1),2);

                                }

                                $val =$k_valor;
                                if(!is_null($val_proc)){
                                  $val_porce = $val_proc->porcentaje10;
                                }

                                $val_unit = $val/(1+$val_porce);

                                $subtotal = 1 *$val_unit;

                                $valor10 =$subtotal*$val_porce;

                                if(!is_null($val_proc)){
                                 $valor_iva =$subtotal* $val_proc->iva;
                                }
                                $total = $subtotal+$valor10+$valor_iva;

                                $val_total = $total;*/
                                $val_total = 34;
                            } else {

                                $val_total = $value->total;
                            }


                            // manipulate the cel
                            $cell->setValue($val_total);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $txt_doctor = 'DR. CARLOS ROBLES';
                        if ($value->codigo == 91034 || $value->codigo == 91037 || $value->codigo == 91010 || $value->codigo == 91110 || $value->codigo == 91122 || $value->codigo == 91013) {
                            $txt_doctor = 'DR(a) HANNAH PITANGA';
                        }

                        $sheet->cell('J' . $i, function ($cell) use ($value, $txt_doctor) {
                            // manipulate the cel
                            $cell->setValue($txt_doctor);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $i++;
                    }
                }

                foreach ($honor_medicos_ssc as $value) {

                    if ($value->codigo != 93000 && $value->codigo != 99253 && $value->codigo != 99205 && $value->codigo != 99005 && $value->codigo != 99202 && $value->codigo != 99203 && $value->codigo != 99204 && $value->codigo != 93005 && $value->codigo != 202 && $value->codigo != 203 && $value->codigo != 204 && $value->tipo != 'TA' && $value->tipo != 'AN' && $value->codigo != 99213) {
                        $sheet->cell('A' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->tsnombre);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('B' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_paciente);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('C' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->apellido1 . ' ' . $value->apellido2 . ' ' . $value->nombre1 . ' ' . $value->nombre2);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $fecha_ing = substr($value->fecha_ing, 0, 10);
                        $fecha_ing_inv = date("d/m/Y", strtotime($fecha_ing));
                        $sheet->cell('D' . $i, function ($cell) use ($value, $fecha_ing_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_ing_inv);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('E' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->nom_procedimiento);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $val_codigo = '';
                        $sheet->cell('F' . $i, function ($cell)  use ($value, $val_codigo) {

                            /*if($value->codigo == '70200004')
                            {
                                $val_codigo = '45380';

                            }elseif($value->codigo == '70200003'){

                                $val_codigo = '43239';

                            }else{

                                $val_codigo = $value->codigo;

                            } */
                            $val_codigo = $value->codigo;

                            // manipulate the cel
                            $cell->setValue($val_codigo);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('G' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->descripcion);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $tipo_honor = '100%';
                        if ($value->porcentaje_honorario == '50') {
                            //$tipo_honor = 0.5;
                            $tipo_honor = '50%';
                        }
                        $sheet->cell('H' . $i, function ($cell) use ($tipo_honor) {
                            // manipulate the cel
                            $cell->setValue($tipo_honor);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('I' . $i, function ($cell) use ($value) {

                            $k_valor = 0;
                            $val_total = 0;
                            $val_unit = 0;
                            $subtotal = 0;
                            $valor10 = 0;
                            $valor_iva  = 0;
                            $total = 0;
                            $val = 0;
                            $val_porce = 0;

                            if ($value->codigo == '70200004') {
                                /*$val_codigo = '45380';

                                $val_proc = ApProcedimiento::where('codigo', $val_codigo)
                                            ->where('estado','1')->first();

                                $valor_nivel = ApProcedimientoNivel::where('codigo', $val_codigo)
                                            ->where('estado','1')
                                            ->where('cod_conv',$value->id_nivel)->first();


                                if(!is_null($valor_nivel)){

                                    $k_valor = round(($valor_nivel->uvr1*$valor_nivel->prc1),2);

                                }

                                $val =$k_valor;
                                if(!is_null($val_proc)){
                                  $val_porce = $val_proc->porcentaje10;
                                }

                                $val_unit = $val/(1+$val_porce);

                                $subtotal = 1 *$val_unit;

                                $valor10 =$subtotal*$val_porce;

                                if(!is_null($val_proc)){
                                 $valor_iva =$subtotal* $val_proc->iva;
                                }
                                $total = $subtotal+$valor10+$valor_iva;

                                $val_total = $total;*/
                                $val_total = 65.5;
                            } elseif ($value->codigo == '70200003') {

                                /*$val_codigo = '43239';

                                $val_proc = ApProcedimiento::where('codigo', $val_codigo)
                                            ->where('estado','1')->first();

                                $valor_nivel = ApProcedimientoNivel::where('codigo', $val_codigo)
                                            ->where('estado','1')
                                            ->where('cod_conv',$value->id_nivel)->first();


                                if(!is_null($valor_nivel)){

                                    $k_valor = round(($valor_nivel->uvr1*$valor_nivel->prc1),2);

                                }

                                $val =$k_valor;
                                if(!is_null($val_proc)){
                                  $val_porce = $val_proc->porcentaje10;
                                }

                                $val_unit = $val/(1+$val_porce);

                                $subtotal = 1 *$val_unit;

                                $valor10 =$subtotal*$val_porce;

                                if(!is_null($val_proc)){
                                 $valor_iva =$subtotal* $val_proc->iva;
                                }
                                $total = $subtotal+$valor10+$valor_iva;

                                $val_total = $total;*/
                                $val_total = 34;
                            } else {

                                $val_total = $value->total;
                            }


                            // manipulate the cel
                            $cell->setValue($val_total);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $txt_doctor = 'DR. CARLOS ROBLES';
                        if ($value->codigo == 91034 || $value->codigo == 91037 || $value->codigo == 91010 || $value->codigo == 91110 || $value->codigo == 91122 || $value->codigo == 91013) {
                            $txt_doctor = 'DR(a) HANNAH PITANGA';
                        }

                        $sheet->cell('J' . $i, function ($cell) use ($value, $txt_doctor) {
                            // manipulate the cel
                            $cell->setValue($txt_doctor);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $i++;
                    }
                }
                //dd($i);
                //Consulta
                $i++;
                $sheet->cell('A' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TIPO SEGURO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('B' . $i, function ($cell) {
                    $cell->setValue('CEDULA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('C' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NOMBRE BENEFICIARIO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('D' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA INGRESO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('E' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PROCEDIMIENTO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('F' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CODIGO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('G' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DESCRIPCION HONORARIO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('H' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TIPO HONORARIO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('I' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TOTAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('J' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DOCTOR');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });

                $i = $i + 1;

                foreach ($honor_medicos_activos as $value) {

                    if ($value->codigo == 99202 || $value->codigo == 99213) {
                        $sheet->cell('A' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            //$cell->setValue($value->tsnombre);
                            $cell->setValue('ACTIVO');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('B' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_paciente);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('C' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->apellido1 . ' ' . $value->apellido2 . ' ' . $value->nombre1 . ' ' . $value->nombre2);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $fecha_ing = substr($value->fecha_ing, 0, 10);
                        $fecha_ing_inv = date("d/m/Y", strtotime($fecha_ing));
                        $sheet->cell('D' . $i, function ($cell) use ($value, $fecha_ing_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_ing_inv);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('E' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->nom_procedimiento);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $val_codigo = '';
                        //$val_codigo_2 = '';
                        $sheet->cell('F' . $i, function ($cell)  use ($value, $val_codigo) {
                            // manipulate the cel

                            /*if($value->codigo == '70200004')
                        {
                            $val_codigo = '45380';

                        }elseif($value->codigo == '70200003'){

                            $val_codigo = '43239';

                        }else{

                            $val_codigo = $value->codigo;

                        } */
                            $val_codigo = $value->codigo;

                            $cell->setValue($val_codigo);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('G' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->descripcion);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $tipo_honor = '100%';
                        if ($seg == '2') {
                            if ($value->porcentaje_honorario == '50') {
                                //$tipo_honor = 0.5;
                                $tipo_honor = '50%';
                            }
                        }
                        if ($seg == '5') {
                            if ($value->clasif_porcentaje_msp == '50') {
                                //$tipo_honor = 0.5;
                                $tipo_honor = '50%';
                            }
                        }

                        $sheet->cell('H' . $i, function ($cell) use ($tipo_honor) {
                            // manipulate the cel
                            $cell->setValue($tipo_honor);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('I' . $i, function ($cell) use ($value) {

                            $k_valor = 0;
                            $val_total = 0;
                            $val_unit = 0;
                            $subtotal = 0;
                            $valor10 = 0;
                            $valor_iva  = 0;
                            $total = 0;
                            $val = 0;
                            $val_porce = 0;

                            if ($value->codigo == '70200004') {
                                /*$val_codigo = '45380';

                            $val_proc = ApProcedimiento::where('codigo', $val_codigo)
                                        ->where('estado','1')->first();

                            $valor_nivel = ApProcedimientoNivel::where('codigo', $val_codigo)
                                        ->where('estado','1')
                                        ->where('cod_conv',$value->id_nivel)->first();


                            if(!is_null($valor_nivel)){

                                $k_valor = round(($valor_nivel->uvr1*$valor_nivel->prc1),2);

                            }

                            $val =$k_valor;
                            if(!is_null($val_proc)){
                              $val_porce = $val_proc->porcentaje10;
                            }

                            $val_unit = $val/(1+$val_porce);

                            $subtotal = 1 *$val_unit;

                            $valor10 =$subtotal*$val_porce;

                            if(!is_null($val_proc)){
                             $valor_iva =$subtotal* $val_proc->iva;
                            }
                            $total = $subtotal+$valor10+$valor_iva;

                            $val_total = $total;*/
                                $val_total = 65.5;
                            } elseif ($value->codigo == '70200003') {

                                /*$val_codigo = '43239';

                            $val_proc = ApProcedimiento::where('codigo', $val_codigo)
                                        ->where('estado','1')->first();

                            $valor_nivel = ApProcedimientoNivel::where('codigo', $val_codigo)
                                        ->where('estado','1')
                                        ->where('cod_conv',$value->id_nivel)->first();


                            if(!is_null($valor_nivel)){

                                $k_valor = round(($valor_nivel->uvr1*$valor_nivel->prc1),2);

                            }

                            $val =$k_valor;
                            if(!is_null($val_proc)){
                              $val_porce = $val_proc->porcentaje10;
                            }

                            $val_unit = $val/(1+$val_porce);

                            $subtotal = 1 *$val_unit;

                            $valor10 =$subtotal*$val_porce;

                            if(!is_null($val_proc)){
                             $valor_iva =$subtotal* $val_proc->iva;
                            }
                            $total = $subtotal+$valor10+$valor_iva;

                            $val_total = $total;*/
                                $val_total = 34;
                            } else {

                                $val_total = $value->total;
                            }

                            // manipulate the cel
                            $cell->setValue($val_total);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $txt_doctor = 'DR. CARLOS ROBLES';
                        if ($value->codigo == 91034 || $value->codigo == 91037 || $value->codigo == 91010 || $value->codigo == 91110 || $value->codigo == 91122 || $value->codigo == 91013) {
                            $txt_doctor = 'DR(a) HANNAH PITANGA';
                        }

                        $sheet->cell('J' . $i, function ($cell) use ($value, $txt_doctor) {
                            // manipulate the cel
                            $cell->setValue($txt_doctor);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $i++;
                    }
                }


                foreach ($honor_medicos_jubilado as $value) {

                    if ($value->codigo == 99202 || $value->codigo == 99213) {
                        $sheet->cell('A' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->tsnombre);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('B' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_paciente);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('C' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->apellido1 . ' ' . $value->apellido2 . ' ' . $value->nombre1 . ' ' . $value->nombre2);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $fecha_ing = substr($value->fecha_ing, 0, 10);
                        $fecha_ing_inv = date("d/m/Y", strtotime($fecha_ing));
                        $sheet->cell('D' . $i, function ($cell) use ($value, $fecha_ing_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_ing_inv);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('E' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->nom_procedimiento);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $val_codigo = '';
                        $sheet->cell('F' . $i, function ($cell)  use ($value, $val_codigo) {
                            // manipulate the cel
                            /*if($value->codigo == '70200004')
                        {
                            $val_codigo = '45380';

                        }elseif($value->codigo == '70200003'){

                            $val_codigo = '43239';

                        }else{

                            $val_codigo = $value->codigo;

                        }*/

                            $val_codigo = $value->codigo;

                            $cell->setValue($val_codigo);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('G' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->descripcion);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $tipo_honor = '100%';
                        if ($value->porcentaje_honorario == '50') {
                            //$tipo_honor = 0.5;
                            $tipo_honor = '50%';
                        }
                        $sheet->cell('H' . $i, function ($cell) use ($tipo_honor) {
                            // manipulate the cel
                            $cell->setValue($tipo_honor);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });


                        $sheet->cell('I' . $i, function ($cell) use ($value) {

                            $k_valor = 0;
                            $val_total = 0;
                            $val_unit = 0;
                            $subtotal = 0;
                            $valor10 = 0;
                            $valor_iva  = 0;
                            $total = 0;
                            $val = 0;
                            $val_porce = 0;

                            if ($value->codigo == '70200004') {
                                /*$val_codigo = '45380';

                            $val_proc = ApProcedimiento::where('codigo', $val_codigo)
                                        ->where('estado','1')->first();

                            $valor_nivel = ApProcedimientoNivel::where('codigo', $val_codigo)
                                        ->where('estado','1')
                                        ->where('cod_conv',$value->id_nivel)->first();


                            if(!is_null($valor_nivel)){

                                $k_valor = round(($valor_nivel->uvr1*$valor_nivel->prc1),2);

                            }

                            $val =$k_valor;
                            if(!is_null($val_proc)){
                              $val_porce = $val_proc->porcentaje10;
                            }

                            $val_unit = $val/(1+$val_porce);

                            $subtotal = 1 *$val_unit;

                            $valor10 =$subtotal*$val_porce;

                            if(!is_null($val_proc)){
                             $valor_iva =$subtotal* $val_proc->iva;
                            }
                            $total = $subtotal+$valor10+$valor_iva;

                            $val_total = $total;*/
                                $val_total = 65.5;
                            } elseif ($value->codigo == '70200003') {

                                /*$val_codigo = '43239';

                            $val_proc = ApProcedimiento::where('codigo', $val_codigo)
                                        ->where('estado','1')->first();

                            $valor_nivel = ApProcedimientoNivel::where('codigo', $val_codigo)
                                        ->where('estado','1')
                                        ->where('cod_conv',$value->id_nivel)->first();


                            if(!is_null($valor_nivel)){

                                $k_valor = round(($valor_nivel->uvr1*$valor_nivel->prc1),2);

                            }

                            $val =$k_valor;
                            if(!is_null($val_proc)){
                              $val_porce = $val_proc->porcentaje10;
                            }

                            $val_unit = $val/(1+$val_porce);

                            $subtotal = 1 *$val_unit;

                            $valor10 =$subtotal*$val_porce;

                            if(!is_null($val_proc)){
                             $valor_iva =$subtotal* $val_proc->iva;
                            }
                            $total = $subtotal+$valor10+$valor_iva;

                            $val_total = $total;*/
                                $val_total = 34;
                            } else {

                                $val_total = $value->total;
                            }

                            // manipulate the cel
                            $cell->setValue($val_total);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $txt_doctor = 'DR. CARLOS ROBLES';
                        if ($value->codigo == 91034 || $value->codigo == 91037 || $value->codigo == 91010 || $value->codigo == 91110 || $value->codigo == 91122 || $value->codigo == 91013) {
                            $txt_doctor = 'DR(a) HANNAH PITANGA';
                        }

                        $sheet->cell('J' . $i, function ($cell) use ($value, $txt_doctor) {
                            // manipulate the cel
                            $cell->setValue($txt_doctor);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $i++;
                    }
                }


                foreach ($honor_medicos_jub_campesino as $value) {

                    if ($value->codigo == 99202 || $value->codigo == 99213) {
                        $sheet->cell('A' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->tsnombre);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('B' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_paciente);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('C' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->apellido1 . ' ' . $value->apellido2 . ' ' . $value->nombre1 . ' ' . $value->nombre2);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $fecha_ing = substr($value->fecha_ing, 0, 10);
                        $fecha_ing_inv = date("d/m/Y", strtotime($fecha_ing));
                        $sheet->cell('D' . $i, function ($cell) use ($value, $fecha_ing_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_ing_inv);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('E' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->nom_procedimiento);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $val_codigo = '';
                        $sheet->cell('F' . $i, function ($cell)  use ($value, $val_codigo) {

                            /*if($value->codigo == '70200004')
                        {
                            $val_codigo = '45380';

                        }elseif($value->codigo == '70200003'){

                            $val_codigo = '43239';

                        }else{

                            $val_codigo = $value->codigo;

                        } */
                            $val_codigo = $value->codigo;

                            // manipulate the cel
                            $cell->setValue($val_codigo);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('G' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->descripcion);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $tipo_honor = '100%';
                        if ($value->porcentaje_honorario == '50') {
                            //$tipo_honor = 0.5;
                            $tipo_honor = '50%';
                        }
                        $sheet->cell('H' . $i, function ($cell) use ($tipo_honor) {
                            // manipulate the cel
                            $cell->setValue($tipo_honor);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('I' . $i, function ($cell) use ($value) {

                            $k_valor = 0;
                            $val_total = 0;
                            $val_unit = 0;
                            $subtotal = 0;
                            $valor10 = 0;
                            $valor_iva  = 0;
                            $total = 0;
                            $val = 0;
                            $val_porce = 0;

                            if ($value->codigo == '70200004') {
                                /*$val_codigo = '45380';

                            $val_proc = ApProcedimiento::where('codigo', $val_codigo)
                                        ->where('estado','1')->first();

                            $valor_nivel = ApProcedimientoNivel::where('codigo', $val_codigo)
                                        ->where('estado','1')
                                        ->where('cod_conv',$value->id_nivel)->first();


                            if(!is_null($valor_nivel)){

                                $k_valor = round(($valor_nivel->uvr1*$valor_nivel->prc1),2);

                            }

                            $val =$k_valor;
                            if(!is_null($val_proc)){
                              $val_porce = $val_proc->porcentaje10;
                            }

                            $val_unit = $val/(1+$val_porce);

                            $subtotal = 1 *$val_unit;

                            $valor10 =$subtotal*$val_porce;

                            if(!is_null($val_proc)){
                             $valor_iva =$subtotal* $val_proc->iva;
                            }
                            $total = $subtotal+$valor10+$valor_iva;

                            $val_total = $total;*/
                                $val_total = 65.5;
                            } elseif ($value->codigo == '70200003') {

                                /*$val_codigo = '43239';

                            $val_proc = ApProcedimiento::where('codigo', $val_codigo)
                                        ->where('estado','1')->first();

                            $valor_nivel = ApProcedimientoNivel::where('codigo', $val_codigo)
                                        ->where('estado','1')
                                        ->where('cod_conv',$value->id_nivel)->first();


                            if(!is_null($valor_nivel)){

                                $k_valor = round(($valor_nivel->uvr1*$valor_nivel->prc1),2);

                            }

                            $val =$k_valor;
                            if(!is_null($val_proc)){
                              $val_porce = $val_proc->porcentaje10;
                            }

                            $val_unit = $val/(1+$val_porce);

                            $subtotal = 1 *$val_unit;

                            $valor10 =$subtotal*$val_porce;

                            if(!is_null($val_proc)){
                             $valor_iva =$subtotal* $val_proc->iva;
                            }
                            $total = $subtotal+$valor10+$valor_iva;

                            $val_total = $total;*/
                                $val_total = 34;
                            } else {

                                $val_total = $value->total;
                            }

                            // manipulate the cel
                            $cell->setValue($val_total);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $txt_doctor = 'DR. CARLOS ROBLES';
                        if ($value->codigo == 91034 || $value->codigo == 91037 || $value->codigo == 91010 || $value->codigo == 91110 || $value->codigo == 91122 || $value->codigo == 91013) {
                            $txt_doctor = 'DR(a) HANNAH PITANGA';
                        }

                        $sheet->cell('J' . $i, function ($cell) use ($value, $txt_doctor) {
                            // manipulate the cel
                            $cell->setValue($txt_doctor);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $i++;
                    }
                }


                foreach ($honor_medicos_montepio as $value) {

                    if ($value->codigo == 99202 || $value->codigo == 99213) {
                        $sheet->cell('A' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->tsnombre);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('B' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_paciente);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('C' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->apellido1 . ' ' . $value->apellido2 . ' ' . $value->nombre1 . ' ' . $value->nombre2);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $fecha_ing = substr($value->fecha_ing, 0, 10);
                        $fecha_ing_inv = date("d/m/Y", strtotime($fecha_ing));
                        $sheet->cell('D' . $i, function ($cell) use ($value, $fecha_ing_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_ing_inv);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('E' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->nom_procedimiento);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $val_codigo = '';
                        $sheet->cell('F' . $i, function ($cell)  use ($value, $val_codigo) {

                            /*if($value->codigo == '70200004')
                        {
                            $val_codigo = '45380';

                        }elseif($value->codigo == '70200003'){

                            $val_codigo = '43239';

                        }else{

                            $val_codigo = $value->codigo;

                        } */
                            $val_codigo = $value->codigo;

                            // manipulate the cel
                            $cell->setValue($val_codigo);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('G' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->descripcion);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $tipo_honor = '100%';
                        if ($value->porcentaje_honorario == '50') {
                            //$tipo_honor = 0.5;
                            $tipo_honor = '50%';
                        }
                        $sheet->cell('H' . $i, function ($cell) use ($tipo_honor) {
                            // manipulate the cel
                            $cell->setValue($tipo_honor);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('I' . $i, function ($cell) use ($value) {

                            $k_valor = 0;
                            $val_total = 0;
                            $val_unit = 0;
                            $subtotal = 0;
                            $valor10 = 0;
                            $valor_iva  = 0;
                            $total = 0;
                            $val = 0;
                            $val_porce = 0;

                            if ($value->codigo == '70200004') {
                                /*$val_codigo = '45380';

                            $val_proc = ApProcedimiento::where('codigo', $val_codigo)
                                        ->where('estado','1')->first();

                            $valor_nivel = ApProcedimientoNivel::where('codigo', $val_codigo)
                                        ->where('estado','1')
                                        ->where('cod_conv',$value->id_nivel)->first();


                            if(!is_null($valor_nivel)){

                                $k_valor = round(($valor_nivel->uvr1*$valor_nivel->prc1),2);

                            }

                            $val =$k_valor;
                            if(!is_null($val_proc)){
                              $val_porce = $val_proc->porcentaje10;
                            }

                            $val_unit = $val/(1+$val_porce);

                            $subtotal = 1 *$val_unit;

                            $valor10 =$subtotal*$val_porce;

                            if(!is_null($val_proc)){
                             $valor_iva =$subtotal* $val_proc->iva;
                            }
                            $total = $subtotal+$valor10+$valor_iva;

                            $val_total = $total;*/
                                $val_total = 65.5;
                            } elseif ($value->codigo == '70200003') {

                                /*$val_codigo = '43239';

                            $val_proc = ApProcedimiento::where('codigo', $val_codigo)
                                        ->where('estado','1')->first();

                            $valor_nivel = ApProcedimientoNivel::where('codigo', $val_codigo)
                                        ->where('estado','1')
                                        ->where('cod_conv',$value->id_nivel)->first();


                            if(!is_null($valor_nivel)){

                                $k_valor = round(($valor_nivel->uvr1*$valor_nivel->prc1),2);

                            }

                            $val =$k_valor;
                            if(!is_null($val_proc)){
                              $val_porce = $val_proc->porcentaje10;
                            }

                            $val_unit = $val/(1+$val_porce);

                            $subtotal = 1 *$val_unit;

                            $valor10 =$subtotal*$val_porce;

                            if(!is_null($val_proc)){
                             $valor_iva =$subtotal* $val_proc->iva;
                            }
                            $total = $subtotal+$valor10+$valor_iva;

                            $val_total = $total;*/
                                $val_total = 34;
                            } else {

                                $val_total = $value->total;
                            }


                            // manipulate the cel
                            $cell->setValue($val_total);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $txt_doctor = 'DR. CARLOS ROBLES';
                        if ($value->codigo == 91034 || $value->codigo == 91037 || $value->codigo == 91010 || $value->codigo == 91110 || $value->codigo == 91122 || $value->codigo == 91013) {
                            $txt_doctor = 'DR(a) HANNAH PITANGA';
                        }

                        $sheet->cell('J' . $i, function ($cell) use ($value, $txt_doctor) {
                            // manipulate the cel
                            $cell->setValue($txt_doctor);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $i++;
                    }
                }

                foreach ($honor_medicos_ssc as $value) {

                    if ($value->codigo == 99202 || $value->codigo == 99213) {
                        $sheet->cell('A' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->tsnombre);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('B' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_paciente);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('C' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->apellido1 . ' ' . $value->apellido2 . ' ' . $value->nombre1 . ' ' . $value->nombre2);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $fecha_ing = substr($value->fecha_ing, 0, 10);
                        $fecha_ing_inv = date("d/m/Y", strtotime($fecha_ing));
                        $sheet->cell('D' . $i, function ($cell) use ($value, $fecha_ing_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_ing_inv);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('E' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->nom_procedimiento);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $val_codigo = '';
                        $sheet->cell('F' . $i, function ($cell)  use ($value, $val_codigo) {

                            /*if($value->codigo == '70200004')
                        {
                            $val_codigo = '45380';

                        }elseif($value->codigo == '70200003'){

                            $val_codigo = '43239';

                        }else{

                            $val_codigo = $value->codigo;

                        } */
                            $val_codigo = $value->codigo;

                            // manipulate the cel
                            $cell->setValue($val_codigo);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('G' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->descripcion);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $tipo_honor = '100%';
                        if ($value->porcentaje_honorario == '50') {
                            //$tipo_honor = 0.5;
                            $tipo_honor = '50%';
                        }
                        $sheet->cell('H' . $i, function ($cell) use ($tipo_honor) {
                            // manipulate the cel
                            $cell->setValue($tipo_honor);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('I' . $i, function ($cell) use ($value) {

                            $k_valor = 0;
                            $val_total = 0;
                            $val_unit = 0;
                            $subtotal = 0;
                            $valor10 = 0;
                            $valor_iva  = 0;
                            $total = 0;
                            $val = 0;
                            $val_porce = 0;

                            if ($value->codigo == '70200004') {
                                /*$val_codigo = '45380';

                            $val_proc = ApProcedimiento::where('codigo', $val_codigo)
                                        ->where('estado','1')->first();

                            $valor_nivel = ApProcedimientoNivel::where('codigo', $val_codigo)
                                        ->where('estado','1')
                                        ->where('cod_conv',$value->id_nivel)->first();


                            if(!is_null($valor_nivel)){

                                $k_valor = round(($valor_nivel->uvr1*$valor_nivel->prc1),2);

                            }

                            $val =$k_valor;
                            if(!is_null($val_proc)){
                              $val_porce = $val_proc->porcentaje10;
                            }

                            $val_unit = $val/(1+$val_porce);

                            $subtotal = 1 *$val_unit;

                            $valor10 =$subtotal*$val_porce;

                            if(!is_null($val_proc)){
                             $valor_iva =$subtotal* $val_proc->iva;
                            }
                            $total = $subtotal+$valor10+$valor_iva;

                            $val_total = $total;*/
                                $val_total = 65.5;
                            } elseif ($value->codigo == '70200003') {

                                /*$val_codigo = '43239';

                            $val_proc = ApProcedimiento::where('codigo', $val_codigo)
                                        ->where('estado','1')->first();

                            $valor_nivel = ApProcedimientoNivel::where('codigo', $val_codigo)
                                        ->where('estado','1')
                                        ->where('cod_conv',$value->id_nivel)->first();


                            if(!is_null($valor_nivel)){

                                $k_valor = round(($valor_nivel->uvr1*$valor_nivel->prc1),2);

                            }

                            $val =$k_valor;
                            if(!is_null($val_proc)){
                              $val_porce = $val_proc->porcentaje10;
                            }

                            $val_unit = $val/(1+$val_porce);

                            $subtotal = 1 *$val_unit;

                            $valor10 =$subtotal*$val_porce;

                            if(!is_null($val_proc)){
                             $valor_iva =$subtotal* $val_proc->iva;
                            }
                            $total = $subtotal+$valor10+$valor_iva;

                            $val_total = $total;*/
                                $val_total = 34;
                            } else {

                                $val_total = $value->total;
                            }


                            // manipulate the cel
                            $cell->setValue($val_total);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $txt_doctor = 'DR. CARLOS ROBLES';
                        if ($value->codigo == 91034 || $value->codigo == 91037 || $value->codigo == 91010 || $value->codigo == 91110 || $value->codigo == 91122 || $value->codigo == 91013) {
                            $txt_doctor = 'DR(a) HANNAH PITANGA';
                        }

                        $sheet->cell('J' . $i, function ($cell) use ($value, $txt_doctor) {
                            // manipulate the cel
                            $cell->setValue($txt_doctor);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $i++;
                    }
                }
            });
        })->export('xlsx');
    }

    public function reporte_biopsias(Request $request)
    {
        //dd($request->all());
        $mes_plano = $request['mes_plano'];

        //$tipo_seg = $request['id_tipo_seguro'];
        $empresa = $request['id_empresa'];
        $seg = $request['seguro'];
        $nombre_seguro = Seguro::find($seg)->nombre;

        $honor_medicos_activos = [];
        $honor_medicos_jubilado = [];
        $honor_medicos_jub_campesino = [];
        $honor_medicos_montepio = [];

        $nombre_empresa = Empresa::find($empresa)->nombrecomercial;
        //ACTIVO  SG
        $honor_medicos_activos = Db::table('archivo_plano_detalle as apd')
            ->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')
            ->join('archivo_plano_cabecera as ap', 'ap.id', 'apd.id_ap_cabecera')
            ->join('paciente as p', 'p.id', 'ap.id_paciente')
            ->join('tipo_seguro as ts', 'ts.id', 'ap.id_tipo_seguro')
            ->where(function ($query) {
                $query->where('ap.id_tipo_seguro', '1')
                    ->orwhere('ap.id_tipo_seguro', '2')
                    ->orwhere('ap.id_tipo_seguro', '3')
                    ->orwhere('ap.id_tipo_seguro', '4')
                    ->orwhere('ap.id_tipo_seguro', '5');
            })
            ->where('mes_plano', $mes_plano)
            //->where('apt.tipo_ex', 'HME')
            ->where('ap.estado', '1')
            ->where('apd.estado', '1')
            ->where('ap.id_seguro', $seg)
            ->where('ap.id_empresa', $empresa)
            //->orderby('ap.fecha_ing', 'asc')
            //->orderby('p.apellido1', 'asc')
            ->select(DB::raw('CONCAT(p.apellido1, p.apellido2, p.nombre1, p.nombre2) AS full_name'), 'p.*', 'apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total', 'ts.nombre as tsnombre', 'ap.id_paciente', 'p.apellido1', 'p.apellido2', 'p.nombre1', 'p.nombre2', 'ap.fecha_ing', 'ap.nom_procedimiento', 'apd.porcentaje_honorario', 'apd.clasif_porcentaje_msp', 'ap.id_nivel')
            //->orderby('apd.porcentaje_honorario','desc')
            //->orderby('apt.secuencia','asc')
            ->orderby('full_name')
            ->orderby('ap.fecha_ing', 'asc')
            ->get();

        //JUBILADO JU
        $honor_medicos_jubilado = Db::table('archivo_plano_detalle as apd')
            ->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')
            ->join('archivo_plano_cabecera as ap', 'ap.id', 'apd.id_ap_cabecera')
            ->join('paciente as p', 'p.id', 'ap.id_paciente')
            ->join('tipo_seguro as ts', 'ts.id', 'ap.id_tipo_seguro')
            ->where('mes_plano', $mes_plano)
            //->where('apt.tipo_ex', 'HME')
            ->where('ap.estado', '1')
            ->where('apd.estado', '1')
            ->where('ap.id_seguro', $seg)
            ->where('ap.id_tipo_seguro', '6')
            ->where('ap.id_empresa', $empresa)
            //->orderby('ap.fecha_ing', 'asc')
            //->orderby('p.apellido1', 'asc')
            ->select(DB::raw('CONCAT(p.apellido1, p.apellido2, p.nombre1, p.nombre2) AS full_name'), 'p.*', 'apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total', 'ts.nombre as tsnombre', 'ap.id_paciente', 'p.apellido1', 'p.apellido2', 'p.nombre1', 'p.nombre2', 'ap.fecha_ing', 'ap.nom_procedimiento', 'apd.porcentaje_honorario', 'ap.id_nivel')
            //->orderby('apd.porcentaje_honorario','desc')
            //->orderby('apt.secuencia','asc')
            ->orderby('full_name')
            ->orderby('ap.fecha_ing', 'asc')
            ->get();


        //JUBILADO CAMPESINO JC
        $honor_medicos_jub_campesino = Db::table('archivo_plano_detalle as apd')
            ->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')
            ->join('archivo_plano_cabecera as ap', 'ap.id', 'apd.id_ap_cabecera')
            ->join('paciente as p', 'p.id', 'ap.id_paciente')
            ->join('tipo_seguro as ts', 'ts.id', 'ap.id_tipo_seguro')
            ->where('mes_plano', $mes_plano)
            //->where('apt.tipo_ex', 'HME')
            ->where('ap.estado', '1')
            ->where('apd.estado', '1')
            ->where('ap.id_seguro', $seg)
            ->where('ap.id_tipo_seguro', '7')
            ->where('ap.id_empresa', $empresa)
            //->orderby('ap.fecha_ing', 'asc')
            //->orderby('p.apellido1', 'asc')
            ->select(DB::raw('CONCAT(p.apellido1, p.apellido2, p.nombre1, p.nombre2) AS full_name'), 'p.*', 'apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total', 'ts.nombre as tsnombre', 'ap.id_paciente', 'p.apellido1', 'p.apellido2', 'p.nombre1', 'p.nombre2', 'ap.fecha_ing', 'ap.nom_procedimiento', 'apd.porcentaje_honorario', 'ap.id_nivel')
            //->orderby('apd.porcentaje_honorario','desc')
            //->orderby('apt.secuencia','asc')
            ->orderby('full_name')
            ->orderby('ap.fecha_ing', 'asc')
            ->get();

        //MONTEPIO MO
        $honor_medicos_montepio = Db::table('archivo_plano_detalle as apd')
            ->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')
            ->join('archivo_plano_cabecera as ap', 'ap.id', 'apd.id_ap_cabecera')
            ->join('paciente as p', 'p.id', 'ap.id_paciente')
            ->join('tipo_seguro as ts', 'ts.id', 'ap.id_tipo_seguro')
            ->where('mes_plano', $mes_plano)
            //->where('apt.tipo_ex', 'HME')
            ->where('ap.estado', '1')
            ->where('apd.estado', '1')
            ->where('ap.id_seguro', $seg)
            ->where('ap.id_tipo_seguro', '8')
            ->where('ap.id_empresa', $empresa)
            //->orderby('ap.fecha_ing', 'asc')
            //->orderby('p.apellido1', 'asc')
            ->select(DB::raw('CONCAT(p.apellido1, p.apellido2, p.nombre1, p.nombre2) AS full_name'), 'p.*', 'apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total', 'ts.nombre as tsnombre', 'ap.id_paciente', 'p.apellido1', 'p.apellido2', 'p.nombre1', 'p.nombre2', 'ap.fecha_ing', 'ap.nom_procedimiento', 'apd.porcentaje_honorario', 'ap.id_nivel')
            //->orderby('apd.porcentaje_honorario','desc')
            //->orderby('apt.secuencia','asc')
            ->orderby('full_name')
            ->orderby('ap.fecha_ing', 'asc')
            ->get();

        //SSC
        $honor_medicos_ssc = Db::table('archivo_plano_detalle as apd')
            ->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')
            ->join('archivo_plano_cabecera as ap', 'ap.id', 'apd.id_ap_cabecera')
            ->join('paciente as p', 'p.id', 'ap.id_paciente')
            ->join('tipo_seguro as ts', 'ts.id', 'ap.id_tipo_seguro')
            ->where('mes_plano', $mes_plano)
            //->where('apt.tipo_ex', 'HME')
            ->where('ap.estado', '1')
            ->where('apd.estado', '1')
            ->where('ap.id_seguro', $seg)
            ->where('ap.id_tipo_seguro', '9')
            ->where('ap.id_empresa', $empresa)
            //->orderby('ap.fecha_ing', 'asc')
            //->orderby('p.apellido1', 'asc')
            ->select(DB::raw('CONCAT(p.apellido1, p.apellido2, p.nombre1, p.nombre2) AS full_name'), 'p.*', 'apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total', 'ts.nombre as tsnombre', 'ap.id_paciente', 'p.apellido1', 'p.apellido2', 'p.nombre1', 'p.nombre2', 'ap.fecha_ing', 'ap.nom_procedimiento', 'apd.porcentaje_honorario', 'ap.id_nivel')
            //->orderby('apd.porcentaje_honorario','desc')
            //->orderby('apt.secuencia','asc')
            ->orderby('full_name')
            ->orderby('ap.fecha_ing', 'asc')
            ->get();

        Excel::create('Reporte_Biopsias_' . $nombre_seguro, function ($excel) use ($honor_medicos_activos, $honor_medicos_jubilado, $honor_medicos_jub_campesino, $honor_medicos_montepio, $honor_medicos_ssc, $seg, $nombre_empresa, $mes_plano) {
            $excel->sheet(date('Y-m-d'), function ($sheet) use ($honor_medicos_activos, $honor_medicos_jubilado, $honor_medicos_jub_campesino, $honor_medicos_montepio, $honor_medicos_ssc, $seg, $nombre_empresa, $mes_plano) {

                $sheet->cell('A2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('REPORTE DE BIOPSIAS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('B2', function ($cell) use ($mes_plano) {
                    // manipulate the cel
                    $cell->setValue($mes_plano);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('C2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('EMPRESA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('D2', function ($cell) use ($nombre_empresa) {
                    // manipulate the cel
                    $cell->setValue($nombre_empresa);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('E2', function ($cell) use ($nombre_empresa) {
                    // manipulate the cel
                    $cell->setValue('SEGURO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('F2', function ($cell) use ($seg) {
                    // manipulate the cel
                    if ($seg == '2') {
                        $cell->setValue('IESS');
                    } else {
                        $cell->setValue('MSP');
                    }

                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('A3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TIPO SEGURO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('B3', function ($cell) {
                    $cell->setValue('CEDULA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('C3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NOMBRE BENEFICIARIO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('D3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA INGRESO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('E3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PROCEDIMIENTO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('F3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CODIGO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('G3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DESCRIPCION HONORARIO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('H3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CANTIDAD');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('I3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('UNITARIO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });

                $sheet->cell('J3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TOTAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });

                $i = 4;
                $sheet->setColumnFormat(array(
                    'I' => '"$"* # ##0.00',
                    'J' => '"$"* # ##0.00',
                ));
                //280001 BIOPSIAS

                foreach ($honor_medicos_activos as $value) {

                    if ($value->codigo == '280001') {
                        $sheet->cell('A' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            //$cell->setValue($value->tsnombre);
                            $cell->setValue('ACTIVO');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('B' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_paciente);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('C' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->apellido1 . ' ' . $value->apellido2 . ' ' . $value->nombre1 . ' ' . $value->nombre2);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $fecha_ing = substr($value->fecha_ing, 0, 10);
                        $fecha_ing_inv = date("d/m/Y", strtotime($fecha_ing));
                        $sheet->cell('D' . $i, function ($cell) use ($value, $fecha_ing_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_ing_inv);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('E' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->nom_procedimiento);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $val_codigo = '';
                        //$val_codigo_2 = '';
                        $sheet->cell('F' . $i, function ($cell)  use ($value, $val_codigo) {
                            // manipulate the cel

                            /*if($value->codigo == '70200004')
                            {
                                $val_codigo = '45380';

                            }elseif($value->codigo == '70200003'){

                                $val_codigo = '43239';

                            }else{

                                $val_codigo = $value->codigo;

                            } */
                            $val_codigo = $value->codigo;

                            $cell->setValue($val_codigo);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('G' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->descripcion);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });


                        $sheet->cell('H' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->cantidad);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('I' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->valor);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('J' . $i, function ($cell) use ($value) {

                            $k_valor = 0;
                            $val_total = 0;
                            $val_unit = 0;
                            $subtotal = 0;
                            $valor10 = 0;
                            $valor_iva  = 0;
                            $total = 0;
                            $val = 0;
                            $val_porce = 0;



                            $val_total = $value->total;


                            // manipulate the cel
                            $cell->setValue($val_total);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });


                        $i++;
                    }
                }

                foreach ($honor_medicos_jubilado as $value) {

                    if ($value->codigo == '280001') {
                        $sheet->cell('A' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->tsnombre);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('B' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_paciente);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('C' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->apellido1 . ' ' . $value->apellido2 . ' ' . $value->nombre1 . ' ' . $value->nombre2);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $fecha_ing = substr($value->fecha_ing, 0, 10);
                        $fecha_ing_inv = date("d/m/Y", strtotime($fecha_ing));
                        $sheet->cell('D' . $i, function ($cell) use ($value, $fecha_ing_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_ing_inv);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('E' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->nom_procedimiento);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $val_codigo = '';
                        $sheet->cell('F' . $i, function ($cell)  use ($value, $val_codigo) {
                            // manipulate the cel
                            /*if($value->codigo == '70200004')
                            {
                                $val_codigo = '45380';

                            }elseif($value->codigo == '70200003'){

                                $val_codigo = '43239';

                            }else{

                                $val_codigo = $value->codigo;

                            }*/

                            $val_codigo = $value->codigo;

                            $cell->setValue($val_codigo);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('G' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->descripcion);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });


                        $sheet->cell('H' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->cantidad);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('I' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->valor);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('J' . $i, function ($cell) use ($value) {

                            $k_valor = 0;
                            $val_total = 0;
                            $val_unit = 0;
                            $subtotal = 0;
                            $valor10 = 0;
                            $valor_iva  = 0;
                            $total = 0;
                            $val = 0;
                            $val_porce = 0;



                            $val_total = $value->total;


                            // manipulate the cel
                            $cell->setValue($val_total);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $i++;
                    }
                }

                foreach ($honor_medicos_jub_campesino as $value) {

                    if ($value->codigo == '280001') {
                        $sheet->cell('A' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->tsnombre);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('B' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_paciente);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('C' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->apellido1 . ' ' . $value->apellido2 . ' ' . $value->nombre1 . ' ' . $value->nombre2);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $fecha_ing = substr($value->fecha_ing, 0, 10);
                        $fecha_ing_inv = date("d/m/Y", strtotime($fecha_ing));
                        $sheet->cell('D' . $i, function ($cell) use ($value, $fecha_ing_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_ing_inv);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('E' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->nom_procedimiento);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $val_codigo = '';
                        $sheet->cell('F' . $i, function ($cell)  use ($value, $val_codigo) {

                            /*if($value->codigo == '70200004')
                            {
                                $val_codigo = '45380';

                            }elseif($value->codigo == '70200003'){

                                $val_codigo = '43239';

                            }else{

                                $val_codigo = $value->codigo;

                            } */
                            $val_codigo = $value->codigo;

                            // manipulate the cel
                            $cell->setValue($val_codigo);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('G' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->descripcion);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });


                        $sheet->cell('H' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->cantidad);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('I' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->valor);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('J' . $i, function ($cell) use ($value) {

                            $k_valor = 0;
                            $val_total = 0;
                            $val_unit = 0;
                            $subtotal = 0;
                            $valor10 = 0;
                            $valor_iva  = 0;
                            $total = 0;
                            $val = 0;
                            $val_porce = 0;



                            $val_total = $value->total;


                            // manipulate the cel
                            $cell->setValue($val_total);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $i++;
                    }
                }

                foreach ($honor_medicos_montepio as $value) {

                    if ($value->codigo == '280001') {
                        $sheet->cell('A' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->tsnombre);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('B' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_paciente);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('C' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->apellido1 . ' ' . $value->apellido2 . ' ' . $value->nombre1 . ' ' . $value->nombre2);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $fecha_ing = substr($value->fecha_ing, 0, 10);
                        $fecha_ing_inv = date("d/m/Y", strtotime($fecha_ing));
                        $sheet->cell('D' . $i, function ($cell) use ($value, $fecha_ing_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_ing_inv);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('E' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->nom_procedimiento);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $val_codigo = '';
                        $sheet->cell('F' . $i, function ($cell)  use ($value, $val_codigo) {

                            /*if($value->codigo == '70200004')
                            {
                                $val_codigo = '45380';

                            }elseif($value->codigo == '70200003'){

                                $val_codigo = '43239';

                            }else{

                                $val_codigo = $value->codigo;

                            } */
                            $val_codigo = $value->codigo;

                            // manipulate the cel
                            $cell->setValue($val_codigo);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('G' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->descripcion);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });


                        $sheet->cell('H' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->cantidad);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('I' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->valor);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('J' . $i, function ($cell) use ($value) {

                            $k_valor = 0;
                            $val_total = 0;
                            $val_unit = 0;
                            $subtotal = 0;
                            $valor10 = 0;
                            $valor_iva  = 0;
                            $total = 0;
                            $val = 0;
                            $val_porce = 0;



                            $val_total = $value->total;


                            // manipulate the cel
                            $cell->setValue($val_total);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $i++;
                    }
                }

                foreach ($honor_medicos_ssc as $value) {

                    if ($value->codigo == '280001') {
                        $sheet->cell('A' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->tsnombre);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('B' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_paciente);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('C' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->apellido1 . ' ' . $value->apellido2 . ' ' . $value->nombre1 . ' ' . $value->nombre2);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $fecha_ing = substr($value->fecha_ing, 0, 10);
                        $fecha_ing_inv = date("d/m/Y", strtotime($fecha_ing));
                        $sheet->cell('D' . $i, function ($cell) use ($value, $fecha_ing_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_ing_inv);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('E' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->nom_procedimiento);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $val_codigo = '';
                        $sheet->cell('F' . $i, function ($cell)  use ($value, $val_codigo) {

                            /*if($value->codigo == '70200004')
                            {
                                $val_codigo = '45380';

                            }elseif($value->codigo == '70200003'){

                                $val_codigo = '43239';

                            }else{

                                $val_codigo = $value->codigo;

                            } */
                            $val_codigo = $value->codigo;

                            // manipulate the cel
                            $cell->setValue($val_codigo);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('G' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->descripcion);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });


                        $sheet->cell('H' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->cantidad);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('I' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->valor);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('J' . $i, function ($cell) use ($value) {

                            $k_valor = 0;
                            $val_total = 0;
                            $val_unit = 0;
                            $subtotal = 0;
                            $valor10 = 0;
                            $valor_iva  = 0;
                            $total = 0;
                            $val = 0;
                            $val_porce = 0;



                            $val_total = $value->total;


                            // manipulate the cel
                            $cell->setValue($val_total);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $i++;
                    }
                }
                //dd($i);
                //Consulta
                $i++;
            });
        })->export('xlsx');
    }

    //Verifica que existan registro al descargar la Planilla Individual Iees
    public function verifica_planilla_individual_iess(Request $request)
    {


        //return $request['id_cab'];

        $archivo_plano = Archivo_Plano_Cabecera::where('id', $request['id_cab'])
            ->where('archivo_plano_cabecera.estado', '1')
            ->first();

        $ap = Archivo_Plano_Cabecera::find($archivo_plano->id);
        $detalles = $ap->detalles;

        //Honorarios Medicos
        $honor_medicos = Db::table('archivo_plano_detalle as apd')
            ->where('id_ap_cabecera', $archivo_plano->id)
            ->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')
            ->orderby('apd.porcentaje_honorario', 'desc')
            ->orderby('apt.secuencia', 'asc')
            ->where('apt.tipo_ex', 'HME')
            ->where('apd.estado', '1')
            ->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')
            ->get();

        //Medicinas
        $medicinas = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')->where('apt.tipo_ex', 'FAR')->where('apd.estado', '1')->get();

        //Insumos
        $insumos = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total', 'apd.valor_unitario', 'apd.porcentaje_iva')->where('apt.tipo_ex', 'IMM')->where('apd.estado', '1')->get();

        //Laboratorio
        $laboratorio = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')->where('apt.tipo_ex', 'LAB')->where('apd.estado', '1')->get();

        //Servicios Institucionales
        $servicios_ins = Db::table('archivo_plano_detalle as apd')->where(
            'id_ap_cabecera',
            $archivo_plano->id
        )->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')->where('apt.tipo_ex', 'HOSP/QUIR')->where('apd.estado', '1')->get();

        //Imagen
        $imagen = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')->where('apt.tipo_ex', 'IMA')->where('apd.estado', '1')->get();

        //Equipos Especiales
        $equipos = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')->where('apt.tipo_ex', 'PRO/ESP')->where('apd.estado', '1')->get();


        if ((count($honor_medicos) > 0) || (count($medicinas) > 0) || (count($insumos) > 0) || (count($laboratorio) > 0) || (count($servicios_ins) > 0) || (count($imagen) > 0) || (count($equipos) > 0)) {
            LogAuditoriaPublica::guardarLog_descarga_excel_pindividual($archivo_plano->id, 'DESCARGA PLANILLA INDIVIDUAL XLSX');
            return "existe";
        } else {

            return "no_existe";
        }
    }


    //Verifica que existan registro al descargar la Planilla Cargo Individual MSP
    public function verifica_planilla_individual_msp(Request $request)
    {

        //return $request['id_cab'];

        $archivo_plano = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.id', $request['id_cab'])
            ->where('archivo_plano_cabecera.estado', '1')
            ->first();

        $ap = Archivo_Plano_Cabecera::find($archivo_plano->id);
        $detalles = $ap->detalles;

        $planilla_msp_detalle = Db::table('archivo_plano_detalle as apd')
            ->where('apd.id_ap_cabecera', $archivo_plano->id)
            ->where('apd.estado', '1')
            ->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')
            ->select('apd.fecha', 'apd.codigo', 'apd.descripcion', 'apt.clasificado', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.clasif_porcentaje_msp', 'apd.valor_porcent_clasifi', 'apd.total_solicitado_usd', 'apd.tipo', 'apd.clasificador', 'apd.clasif_porcentaje_msp')
            ->get();

        if (count($planilla_msp_detalle) > 0) {

            return "existe";
        } else {

            return "no_existe";
        }
    }

    private function redondea_menos_4_dec($numero)
    {

        if ($this->contarDecimales($numero) > 4) {
            $numero = floor($numero * 10000) / 10000;
        }

        return $numero;
    }

    private function contarDecimales($numero)
    {
        $partes = explode('.', strval($numero));
        if (count($partes) < 2) {
            return 0; // Si no hay punto decimal, no hay decimales
        } else {
            return strlen($partes[1]); // La longitud de la parte después del punto decimal es el número de decimales
        }
    }
}
