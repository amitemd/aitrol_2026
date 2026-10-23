<?php

namespace Sis_medico\Http\Controllers\archivo_plano;

use Carbon\Carbon;
use Cookie;
use DateTime;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Maatwebsite\Excel\Excel as ExcelExcel;
use PHPExcel_Style_Alignment;
use PHPExcel_Worksheet_Drawing;
use PHPExcel_Style_NumberFormat;
use PHPExcel_Cell_DataType;
use PhpParser\Node\Stmt\Foreach_;
use Response;
use Sis_medico\Ap_Agrupado;
use Sis_medico\Ap_Tipo_Seg;
use Sis_medico\Archivo_Plano_Cabecera;
use Sis_medico\Cie_10_3;
use Sis_medico\Cie_10_4;
use Sis_medico\Empresa;
use Sis_medico\Historiaclinica;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Paciente;
use Sis_medico\Seguro;
use Sis_medico\Agenda;
use Sis_medico\ApArchivoFactura;
use Sis_medico\Reporte_Consultas_Iess;
use Sis_medico\Examen_Orden;
use Sis_medico\Orden;
use Sis_medico\Archivo_Plano_Detalle;
use Sis_medico\Aud_Hc_Cie10;
use Sis_medico\Aud_Hc_Procedimientos;
use Sis_medico\Biopsias;
use Sis_medico\Hc4_Biopsias;
use Sis_medico\Hc_Cie10;
use Sis_medico\Hc_Procedimiento_Final;
use Sis_medico\hc_procedimientos;
use Sis_medico\HcFormulario013;
use Sis_medico\Tipo_Seguro;
use Sis_medico\ApProcedimiento;
use Sis_medico\Codigo_Derivacion;
use Sis_medico\Firma_Usuario;
use Sis_medico\Hc4_Auditoria_Biopsias;
use Sis_medico\User;
use Sis_medico\Convenio;
use Sis_medico\Codigo_Dependencia;
use Sis_medico\ApPlantilla;
use Sis_medico\Ap_Tipo_Examen;
use Sis_medico\Ap_Procedimiento;
use Sis_medico\ApProcedimientoNivel;
use Sis_medico\LogAuditoriaPublica;
use Sis_medico\ApPlantillaEspecial;
use Sis_medico\Subseguro;

class NuevoArchivoPlanoController extends Controller
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
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 11, 22)) == false) {
            return true;
        }
    }

    public function genera_plan_consolidada_pdf(Request $request)
    {
        //consolidada
        $idUsuario = Auth::user()->id;
        
        $cedula     = $request['cedula'];
        $nombres    = $request['paciente'];
        $mes_plano  = $request['mes_plano'];
        $id_seguro  = $request['id_seguro'];
        $id_empresa = $request['id_empresa'];
        $codigo_validacion = $request['codigo_validacion'];

        $empresa = Empresa::where('id', $id_empresa)
            ->where('estado', '1')
            ->first();

        $archivo_plano = Archivo_Plano_Cabecera::whereNotNull('fecha_ing')
            ->where('estado', '1');

        //Valida que ingrese la cedula
        if ($cedula != null) {
            $paciente = Paciente::find($cedula);
            if (!is_null($paciente)) {
                $archivo_plano = $archivo_plano->where('id_paciente', $paciente->id);
            }
        }

        //Valida que ingrese el mes de Plano
        if ($mes_plano != null) {
            $archivo_plano = $archivo_plano->where('mes_plano', $mes_plano);
        }

        //Valida que ingrese el seguro
        if ($id_seguro != null) {
            $archivo_plano = $archivo_plano->where('id_seguro', $id_seguro);
        }

        //Valida que ingrese la empresa
        if ($id_empresa != null) {
            $archivo_plano = $archivo_plano->where('id_empresa', $id_empresa);
        }

        if ($codigo_validacion != null) {
            $archivo_plano = $archivo_plano->where('cod_deriva_msp', $codigo_validacion);
        }

        $archivo_plano = $archivo_plano->orderby('fecha_ing');
        $archivo_plano_first = $archivo_plano->get()->first();
        $archivo_plano_last = $archivo_plano->get()->last();
        $archivo_plano_reg = $archivo_plano->get();

        // if($idUsuario=='0950457978'){
        //     dd($archivo_plano_reg,$archivo_plano_first,$archivo_plano_last,$archivo_plano_reg);
        // }
        //dd($archivo_plano_reg);
        $txt_texto  = null;
        $txt_texto1 = null;
        $txt_cie10  = null;
        $contador   = 0;

        foreach ($archivo_plano_reg as $value) {
            $contador = $contador + 1;
            if ($contador == 1) {
                $txt_texto1 = $value->nom_procedimiento;
            } else {
                $txt_texto1 = $txt_texto1 . ' - ' . $value->nom_procedimiento;
            }

            $cie10 = Cie_10_3::find($value->cie10);

            if (is_null($cie10)) {
                $cie10 = Cie_10_4::find($value->cie10);
                if (!is_null($cie10)) {
                    if ($contador == 1) {
                        $txt_cie10 = '(' . $value->cie10 . ')' . trim($cie10->descripcion);
                    } else {
                        $txt_cie10 = $txt_cie10 . ' - ' . '(' . $value->cie10 . ')' . trim($cie10->descripcion);
                    }
                }
            } else {
                if ($contador == 1) {
                    $txt_cie10 = '(' . $value->cie10 . ')' . trim($cie10->descripcion);
                } else {
                    $txt_cie10 = $txt_cie10 . ' - ' . '(' . $value->cie10 . ')' . trim($cie10->descripcion);
                }
            }
        }
        $view = \View::make('archivo_plano.planilla.planilla_conglomerada_pdf', compact('archivo_plano_reg','archivo_plano_first','archivo_plano_last','txt_texto1','txt_cie10','empresa','paciente'))->render();
        $pdf  = \App::make('dompdf.wrapper');
        $pdf->setPaper('A4');
        $pdf->loadHTML($view);
        //$nombres = str_replace(' ','_',$nombres);
        return $pdf->download('planilla_consolidada.pdf');
        // $nombres = str_replace(' ','_',$nombres);
        // return $pdf->stream('planilla_pdf_'.$nombres.'_.pdf');
    }

    /*public function genera_ap_excel_092022(Request $request)
    {
        //ini_set('memory_limit', '1024M');
        //dd("sistemas");
        $mes_plano = $request['mes_plano'];
        $seguro     = $request->seguro;
        $tipo_seg   = $request->id_tipo_seguro;
        //$cob_compar = $request['id_cobertura_comp'];
        $empresa    = $request->id_empresa;

        $archivo_plano_1 = [];
        $archivo_plano_2 = [];
        $archivo_plano_3 = [];
        $archivo_plano_4 = [];
        $archivo_plano_5 = [];
        $archivo_plano   = [];

        $texto_apellido_1 = null;
        $texto_apellido_2 = null;
        $texto_final      = null;
        $texto_fin        = null;
        $texto_nombre_1   = null;
        $texto_nombre_2   = null;

        $caract_remplazar_1 = array("¨", "$", "%", "&", "/", "(", ")", "#", "@", "|", "!", "/", "+", "-", "}", "{", ">", "< ", ";", ",", ":",
            ".", "*", ">", "<", "[", "]", "?", "¿", "º", "~");

        $caract_remplazar_2 = array("Ñ", "ñ");

        //ACTIVOS
        $cob_compar = null;
        $archivo_plano_1 = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
                ->whereIn('archivo_plano_cabecera.id_tipo_seguro',['1','2','3','4','5'])
                ->where('archivo_plano_cabecera.id_seguro', $seguro)
                ->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
                ->where('archivo_plano_cabecera.id_empresa', $empresa)
                ->where('archivo_plano_cabecera.estado', '1')
                ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
                ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
                ->join('archivo_plano_detalle as apd', 'apd.id_ap_cabecera', 'archivo_plano_cabecera.id')
                ->join('ap_tipo_examen as tipo_ex', 'tipo_ex.tipo', 'apd.tipo')
                ->where('apd.estado', '1')
                ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name') , 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.valor_unitario', 'apd.subtotal')
                //->orderby('archivo_plano_cabecera.id_tipo_seguro')
                ->orderby('full_name')
                ->orderby('archivo_plano_cabecera.fecha_ing')
                ->orderby('tipo_ex.orden_plano')
                ->get();
        $cont = $archivo_plano_1->count(); 
        
        $cob_compar = null;//JM
        $archivo_plano_2 = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
                ->whereIn('archivo_plano_cabecera.id_tipo_seguro',['6','8'])
                ->where('archivo_plano_cabecera.id_seguro', $seguro)
                ->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
                ->where('archivo_plano_cabecera.id_empresa', $empresa)
                ->where('archivo_plano_cabecera.estado', '1')
                ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
                ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
                ->join('archivo_plano_detalle as apd', 'apd.id_ap_cabecera', 'archivo_plano_cabecera.id')
                ->join('ap_tipo_examen as tipo_ex', 'tipo_ex.tipo', 'apd.tipo')
                ->where('apd.estado', '1')
                ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name') , 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.valor_unitario', 'apd.subtotal')
                ->orderby('archivo_plano_cabecera.id_tipo_seguro')
                ->orderby('full_name')
                ->orderby('archivo_plano_cabecera.fecha_ing')
                ->orderby('tipo_ex.orden_plano')
                ->get();
        $cont_2 = $archivo_plano_2->count(); 

        $cob_compar = 3;//ISSFA-ACTIVOS
        $archivo_plano_3 = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
                ->whereIn('archivo_plano_cabecera.id_tipo_seguro',['1','2','3','4','5'])
                ->where('archivo_plano_cabecera.id_seguro', $seguro)
                ->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
                ->where('archivo_plano_cabecera.id_empresa', $empresa)
                ->where('archivo_plano_cabecera.estado', '1')
                ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
                ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
                ->join('archivo_plano_detalle as apd', 'apd.id_ap_cabecera', 'archivo_plano_cabecera.id')
                ->join('ap_tipo_examen as tipo_ex', 'tipo_ex.tipo', 'apd.tipo')
                ->where('apd.estado', '1')
                ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name') , 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.valor_unitario', 'apd.subtotal')
                //->orderby('archivo_plano_cabecera.id_tipo_seguro')
                ->orderby('full_name')
                ->orderby('archivo_plano_cabecera.fecha_ing')
                ->orderby('tipo_ex.orden_plano')
                ->get();
        $cont_issfa_1 = $archivo_plano_3->count(); 

        $cob_compar = 3;//ISSFA-JM
        $archivo_plano_4 = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
                ->whereIn('archivo_plano_cabecera.id_tipo_seguro',['6','8'])
                ->where('archivo_plano_cabecera.id_seguro', $seguro)
                ->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
                ->where('archivo_plano_cabecera.id_empresa', $empresa)
                ->where('archivo_plano_cabecera.estado', '1')
                ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
                ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
                ->join('archivo_plano_detalle as apd', 'apd.id_ap_cabecera', 'archivo_plano_cabecera.id')
                ->join('ap_tipo_examen as tipo_ex', 'tipo_ex.tipo', 'apd.tipo')
                ->where('apd.estado', '1')
                ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name') , 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.valor_unitario', 'apd.subtotal')
                ->orderby('archivo_plano_cabecera.id_tipo_seguro')
                ->orderby('full_name')
                ->orderby('archivo_plano_cabecera.fecha_ing')
                ->orderby('tipo_ex.orden_plano')
                ->get();
        $cont_issfa_2 = $archivo_plano_4->count(); 

        $cob_compar = 6;//ISSPOL-ACTIVOS
        $archivo_plano_5 = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
                ->whereIn('archivo_plano_cabecera.id_tipo_seguro',['1','2','3','4','5'])
                ->where('archivo_plano_cabecera.id_seguro', $seguro)
                ->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
                ->where('archivo_plano_cabecera.id_empresa', $empresa)
                ->where('archivo_plano_cabecera.estado', '1')
                ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
                ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
                ->join('archivo_plano_detalle as apd', 'apd.id_ap_cabecera', 'archivo_plano_cabecera.id')
                ->join('ap_tipo_examen as tipo_ex', 'tipo_ex.tipo', 'apd.tipo')
                ->where('apd.estado', '1')
                ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name') , 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.valor_unitario', 'apd.subtotal')
                //->orderby('archivo_plano_cabecera.id_tipo_seguro')
                ->orderby('full_name')
                ->orderby('archivo_plano_cabecera.fecha_ing')
                ->orderby('tipo_ex.orden_plano')
                ->get();
        $cont_isspol_1 = $archivo_plano_5->count();

        $cob_compar = 6;//ISSPOL-JM
        $archivo_plano_6 = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
                ->whereIn('archivo_plano_cabecera.id_tipo_seguro',['6','8'])
                ->where('archivo_plano_cabecera.id_seguro', $seguro)
                ->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
                ->where('archivo_plano_cabecera.id_empresa', $empresa)
                ->where('archivo_plano_cabecera.estado', '1')
                ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
                ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
                ->join('archivo_plano_detalle as apd', 'apd.id_ap_cabecera', 'archivo_plano_cabecera.id')
                ->join('ap_tipo_examen as tipo_ex', 'tipo_ex.tipo', 'apd.tipo')
                ->where('apd.estado', '1')
                ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name') , 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.valor_unitario', 'apd.subtotal')
                ->orderby('archivo_plano_cabecera.id_tipo_seguro')
                ->orderby('full_name')
                ->orderby('archivo_plano_cabecera.fecha_ing')
                ->orderby('tipo_ex.orden_plano')
                ->get();

        $cont_isspol2 = $archivo_plano_6->count();

        Excel::create('Archivo Plano General ', function ($excel) use ($archivo_plano_1, $archivo_plano_2, $archivo_plano_3, $archivo_plano_4, $archivo_plano_5, $archivo_plano_6, $cont, $cont_2, $cont_issfa_1, $cont_issfa_2, $cont_isspol_1, $cont_isspol2, $caract_remplazar_1, $caract_remplazar_2, $texto_apellido_1, $texto_apellido_2, $texto_final, $texto_fin, $texto_nombre_1, $texto_nombre_2) {
            $excel->sheet(date('Y-m-d'), function ($sheet) use ($archivo_plano_1, $archivo_plano_2, $archivo_plano_3, $archivo_plano_4, $archivo_plano_5, $archivo_plano_6, $cont,$cont_2, $cont_issfa_1, $cont_issfa_2, $cont_isspol_1, $cont_isspol2, $caract_remplazar_1, $caract_remplazar_2, $texto_apellido_1, $texto_apellido_2, $texto_final, $texto_fin, $texto_nombre_1, $texto_nombre_2) {

                $i = 1; $x = 0; $id_temporal = 0;

                if (!is_null($archivo_plano_1)) {

                    foreach ($archivo_plano_1 as $value) {
 
                        $tipo_examen = '';
                        $ap_tipo_examen = Ap_Tipo_Examen::where('tipo',$value->tipo)->first();
                        if(!is_null($ap_tipo_examen)){
                            $tipo_examen = $ap_tipo_examen->tipo_ex;
                        }
                        if ($value->id_paciente != $id_temporal) {
                            $id_temporal = $value->id_paciente;
                            $x++;
                        }

                        $sheet->cell('A' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('515');
                        });

                        $sheet->cell('B' . $i, function ($cell) use ($value, $x) {
                            // manipulate the cel
                            $cell->setValue($x);
                        });

                        $fecha_ing     = substr($value->fecha_ing, 0, 10);
                        $fecha_ing_inv = date("d/m/Y", strtotime($fecha_ing));

                        $sheet->cell('C' . $i, function ($cell) use ($fecha_ing_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_ing_inv);
                        });

                        $sheet->cell('D' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->tiposeg);
                        });
                        $sheet->cell('E' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_paciente);

                        });
                        $sheet->cell('F' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2, $texto_apellido_1, $texto_apellido_2, $texto_final, $texto_fin, $texto_nombre_1, $texto_nombre_2) {
                            // manipulate the cel
                            //$cell->setValue($value->paciente->apellido1.' '.$value->paciente->apellido2.' '.$value->paciente->nombre1.' '.$value->paciente->nombre2);
                            //$caract_remplazar = array("¨","$","%","&","/","(",")","#","@", "|", "!","/","+","-","}", "{", ">", "< ", ";", ",", ":",
                            //".","*",">","<","[","]","?","¿","º","~");
                            if ($value->apellido1 != 'N/A' && $value->apellido1 != '(N/A)') {
                                $texto_apellido_1 = $value->apellido1;
                            }

                            if ($value->apellido2 != 'N/A' && $value->apellido2 != '(N/A)') {
                                $texto_apellido_2 = $value->apellido2;
                            }

                            if ($value->nombre1 != 'N/A' && $value->nombre1 != '(N/A)') {
                                $texto_nombre_1 = $value->nombre1;
                            }

                            if ($value->nombre2 != 'N/A' && $value->nombre2 != '(N/A)') {
                                $texto_nombre_2 = $value->nombre2;
                            }

                            $texto_final = $texto_apellido_1 . ' ' . $texto_apellido_2 . ' ' . $texto_nombre_1 . ' ' . $texto_nombre_2;

                            if($value->apellido2 == '(N/A)' || $value->apellido2 == ('N/A') || $value->apellido2 == "."){
                                $texto_final = $texto_apellido_1 . ' ' . $texto_nombre_1 . ' ' . $texto_nombre_2;
                            }

                            $texto_fin = str_replace($caract_remplazar_1, "", $texto_final);
                            $texto_fin = str_replace($caract_remplazar_2, "N", $texto_fin);
                            $cell->setValue($texto_fin);

                            //$cell->setValue($value->apellido1.' '.$value->apellido2.' '.$value->nombre1.' '.$value->nombre2);

                        });
                        $sheet->cell('G' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->paciente->sexo == 1) {
                                $cell->setValue('M');
                            } elseif ($value->paciente->sexo == 2) {
                                $cell->setValue('F');
                            }

                        });

                        $fecha_nac     = substr($value->paciente->fecha_nacimiento, 0, 10);
                        $fecha_nac_inv = date("d/m/Y", strtotime($fecha_nac));

                        $sheet->cell('H' . $i, function ($cell) use ($fecha_nac_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_nac_inv);

                        });
                        if ($value->paciente->fecha_nacimiento == null) {
                            $edad = 0;
                        } else {
                            $edad = Carbon::createFromDate(substr($value->paciente->fecha_nacimiento, 0, 4), substr($value->paciente->fecha_nacimiento, 5, 2), substr($value->paciente->fecha_nacimiento, 8, 2))->age;
                        }
                        $sheet->cell('I' . $i, function ($cell) use ($value, $edad) {
                            // manipulate the cel
                            $cell->setValue($edad);

                        });
                        $sheet->cell('J' . $i, function ($cell) use ($value,$tipo_examen) {
                            // manipulate the cel
                            if ($value->tipo == 'P' || $value->tipo == 'AN' || $value->tipo == 'TA') {
                                $cell->setValue('HME');

                            } elseif ($value->tipo == 'M') {
                                $cell->setValue('FAR');

                            } elseif ($value->tipo == 'S') {
                                $cell->setValue('HOSP/QUIR');

                            } elseif ($value->tipo == 'IM') {
                                $cell->setValue('IMA');

                            } elseif ($value->tipo == 'EX') {
                                $cell->setValue('LAB');

                            } elseif ($value->tipo == 'I' || $value->tipo == 'IV' || $value->tipo == 'IF' || $value->tipo == 'IVC' || $tipo_examen == 'IMM') {
                                $cell->setValue('IMM');

                            } elseif ($value->tipo == 'PA') {
                                $cell->setValue('PAQUE');

                            } elseif ($value->tipo == 'EQ') {
                                $cell->setValue('PRO/ESP');

                            }

                        });
                        $sheet->cell('K' . $i, function ($cell) use ($value,$tipo_examen) {
                            // manipulate the cel
                            if ($value->tipo == 'IV' || $value->tipo == 'I' || $value->tipo == 'M' || $value->tipo == 'IVC' || $tipo_examen == 'IMM') {
                                $cell->setValue(' ');
                            } else {
                                $cell->setValue($value->codigo);
                            }

                        });
                        $sheet->cell('L' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2,$tipo_examen) {
                            // manipulate the

                            $descrip = str_replace($caract_remplazar_1, "", $value->descripcion);
                            $descrip = str_replace($caract_remplazar_2, "N", $descrip);

                            if ($value->tipo == 'IV' || $value->tipo == 'I' || $value->tipo == 'M' || $value->tipo == 'IVC' || $tipo_examen == 'IMM') {
                                //$cell->setValue($value->descripcion);
                                $cell->setValue($descrip);

                            } else {

                                $cell->setValue('');

                            }
                        });
                        $sheet->cell('M' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->cie10);

                        });
                        $sheet->cell('N' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('O' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('P' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->cantidad);

                        });
                        $sheet->cell('Q' . $i, function ($cell) use ($value) {
                            // manipulate the cel

                            //$cell->setValue(round($value->valor+$value->porcentaje10,2));
                            $cell->setValue(round($value->valor, 2));

                        });
                        $sheet->cell('R' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('0');

                        });
                        $sheet->cell('S' . $i, function ($cell) use ($value) {
                            // manipulate the cel

                            if ($value->parentesco == 'TITULAR') {
                                $cell->setValue('T');

                            } elseif ($value->parentesco == 'CONYUGE') {
                                $cell->setValue('C');

                            } elseif ($value->parentesco == 'HIJO/HIJA') {
                                $cell->setValue('H');

                            } elseif ($value->parentesco == 'PARIENTE') {
                                $cell->setValue('X');

                            }

                            //$cell->setValue($value->parentesco);

                        });
                        $sheet->cell('T' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_usuario);
                        });
                        $sheet->cell('U' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2) {
                            // manipulate the cel

                            $descrip_nombre = str_replace($caract_remplazar_1, "", $value->nombres);
                            $descrip_nombre = str_replace($caract_remplazar_2, "N", $descrip_nombre);

                            $cell->setValue($descrip_nombre);

                        });
                        $sheet->cell('V' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('CVAF');

                        });
                        $sheet->cell('W' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('1');

                        });
                        $sheet->cell('X' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('1');

                        });
                        $sheet->cell('Y' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('D');

                        });
                        $sheet->cell('Z' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('0');

                        });
                        $sheet->cell('AA' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AB' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AC' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AD' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->porcentaje_iva);

                        });
                        $sheet->cell('AE' . $i, function ($cell) use ($value, $tipo_examen) {
                            // manipulate the cel
                            //$cell->setValue(round($value->iva,2));
                            //$nuevo_iva = $value->cantidad*$value->valor_unitario*$value->porcentaje_iva;
                            if ($tipo_examen == 'IMM') {
                                $nuevo_iva = $value->subtotal * $value->porcentaje_iva;
                                $nuevo_iva = $nuevo_iva / $value->cantidad;
                            }else{
                                $nuevo_iva = $value->valor_unitario * $value->porcentaje_iva;
                            }
                            $cell->setValue(round($nuevo_iva, 4));

                        });
                        $sheet->cell('AF' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('F');

                        });
                        $i++;
                    }
                    $i = $i;

                }

                if (!is_null($archivo_plano_2)) {
                    foreach ($archivo_plano_2 as $value) {

                        $tipo_examen = '';
                        $ap_tipo_examen = Ap_Tipo_Examen::where('tipo',$value->tipo)->first();
                        if(!is_null($ap_tipo_examen)){
                            $tipo_examen = $ap_tipo_examen->tipo_ex;
                        }

                        if ($value->id_paciente != $id_temporal) {
                            $id_temporal = $value->id_paciente;
                            $x++;
                        }

                        $sheet->cell('A' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('515');
                        });

                        $sheet->cell('B' . $i, function ($cell) use ($value, $x) {
                            // manipulate the cel
                            $cell->setValue($x);
                        });

                        $fecha_ing     = substr($value->fecha_ing, 0, 10);
                        $fecha_ing_inv = date("d/m/Y", strtotime($fecha_ing));

                        $sheet->cell('C' . $i, function ($cell) use ($fecha_ing_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_ing_inv);
                        });

                        $sheet->cell('D' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->tiposeg);
                        });
                        $sheet->cell('E' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_paciente);

                        });
                        $sheet->cell('F' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2, $texto_apellido_1, $texto_apellido_2, $texto_final, $texto_fin, $texto_nombre_1, $texto_nombre_2) {
                            // manipulate the cel
                            //$cell->setValue($value->paciente->apellido1.' '.$value->paciente->apellido2.' '.$value->paciente->nombre1.' '.$value->paciente->nombre2);
                            //$cell->setValue($value->apellido1.' '.$value->apellido2.' '.$value->nombre1.' '.$value->nombre2);

                            if ($value->apellido1 != 'N/A' && $value->apellido1 != '(N/A)') {
                                $texto_apellido_1 = $value->apellido1;
                            }

                            if ($value->apellido2 != 'N/A' && $value->apellido2 != '(N/A)') {
                                $texto_apellido_2 = $value->apellido2;
                            }

                            if ($value->nombre1 != 'N/A' && $value->nombre1 != '(N/A)') {
                                $texto_nombre_1 = $value->nombre1;
                            }

                            if ($value->nombre2 != 'N/A' && $value->nombre2 != '(N/A)') {
                                $texto_nombre_2 = $value->nombre2;
                            }

                            //$texto_final = $texto_apellido_1.' '.$texto_apellido_2.' '. $value->nombre1.' '.$value->nombre2;

                            $texto_final = $texto_apellido_1 . ' ' . $texto_apellido_2 . ' ' . $texto_nombre_1 . ' ' . $texto_nombre_2;

                            if($value->apellido2 == '(N/A)' || $value->apellido2 == ('N/A') || $value->apellido2 == "."){
                                $texto_final = $texto_apellido_1 . ' ' . $texto_nombre_1 . ' ' . $texto_nombre_2;
                            }

                            $texto_fin = str_replace($caract_remplazar_1, "", $texto_final);
                            $texto_fin = str_replace($caract_remplazar_2, "N", $texto_fin);
                            $cell->setValue($texto_fin);

                        });
                        $sheet->cell('G' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->paciente->sexo == 1) {
                                $cell->setValue('M');
                            } elseif ($value->paciente->sexo == 2) {
                                $cell->setValue('F');
                            }

                        });

                        $fecha_nac     = substr($value->paciente->fecha_nacimiento, 0, 10);
                        $fecha_nac_inv = date("d/m/Y", strtotime($fecha_nac));

                        $sheet->cell('H' . $i, function ($cell) use ($fecha_nac_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_nac_inv);

                        });
                        if ($value->paciente->fecha_nacimiento == null) {
                            $edad = 0;
                        } else {
                            $edad = Carbon::createFromDate(substr($value->paciente->fecha_nacimiento, 0, 4), substr($value->paciente->fecha_nacimiento, 5, 2), substr($value->paciente->fecha_nacimiento, 8, 2))->age;
                        }
                        $sheet->cell('I' . $i, function ($cell) use ($value, $edad) {
                            // manipulate the cel
                            $cell->setValue($edad);

                        });
                        $sheet->cell('J' . $i, function ($cell) use ($value,$tipo_examen) {
                            // manipulate the cel
                            if ($value->tipo == 'P' || $value->tipo == 'AN' || $value->tipo == 'TA') {
                                $cell->setValue('HME');

                            } elseif ($value->tipo == 'M') {
                                $cell->setValue('FAR');

                            } elseif ($value->tipo == 'S') {
                                $cell->setValue('HOSP/QUIR');

                            } elseif ($value->tipo == 'IM') {
                                $cell->setValue('IMA');

                            } elseif ($value->tipo == 'EX') {
                                $cell->setValue('LAB');

                            } elseif ($value->tipo == 'I' || $value->tipo == 'IV' || $value->tipo == 'IF' || $value->tipo == 'IVC' || $tipo_examen == 'IMM') {
                                $cell->setValue('IMM');

                            } elseif ($value->tipo == 'PA') {
                                $cell->setValue('PAQUE');

                            } elseif ($value->tipo == 'EQ') {
                                $cell->setValue('PRO/ESP');

                            }

                        });
                        $sheet->cell('K' . $i, function ($cell) use ($value,$tipo_examen) {
                            // manipulate the cel
                            if ($value->tipo == 'IV' || $value->tipo == 'I' || $value->tipo == 'M' || $value->tipo == 'IVC' || $tipo_examen == 'IMM') {
                                $cell->setValue(' ');
                            } else {
                                $cell->setValue($value->codigo);
                            }

                        });
                        $sheet->cell('L' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2,$tipo_examen) {
                            // manipulate the cel

                            $descrip = str_replace($caract_remplazar_1, "", $value->descripcion);
                            $descrip = str_replace($caract_remplazar_2, "N", $descrip);

                            if ($value->tipo == 'IV' || $value->tipo == 'I' || $value->tipo == 'M' || $value->tipo == 'IVC' || $tipo_examen == 'IMM') {
                                //$cell->setValue($value->descripcion);
                                $cell->setValue($descrip);

                            } else {

                                $cell->setValue('');

                            }
                            //$cell->setValue($value->descripcion);

                        });
                        $sheet->cell('M' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->cie10);

                        });
                        $sheet->cell('N' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('O' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('P' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->cantidad);

                        });
                        $sheet->cell('Q' . $i, function ($cell) use ($value) {
                            // manipulate the cel

                            //$cell->setValue(round($value->valor+$value->porcentaje10,2));
                            $cell->setValue(round($value->valor, 2));

                        });
                        $sheet->cell('R' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('0');

                        });
                        $sheet->cell('S' . $i, function ($cell) use ($value) {
                            // manipulate the cel

                            if ($value->parentesco == 'TITULAR') {
                                $cell->setValue('T');

                            } elseif ($value->parentesco == 'CONYUGE') {
                                $cell->setValue('C');

                            } elseif ($value->parentesco == 'HIJO/HIJA') {
                                $cell->setValue('H');

                            } elseif ($value->parentesco == 'PARIENTE') {
                                $cell->setValue('X');

                            }

                            //$cell->setValue($value->parentesco);

                        });
                        $sheet->cell('T' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_usuario);
                        });
                        $sheet->cell('U' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2) {
                            // manipulate the cel
                            //$cell->setValue($value->nombres);
                            //$descrip_nombre = str_replace($caract_remplazar,"",$value->nombres);

                            $descrip_nombre = str_replace($caract_remplazar_1, "", $value->nombres);
                            $descrip_nombre = str_replace($caract_remplazar_2, "N", $descrip_nombre);

                            $cell->setValue($descrip_nombre);

                        });
                        $sheet->cell('V' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('CVAF');

                        });
                        $sheet->cell('W' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('1');

                        });
                        $sheet->cell('X' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('1');

                        });
                        $sheet->cell('Y' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('D');

                        });
                        $sheet->cell('Z' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('0');

                        });
                        $sheet->cell('AA' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AB' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AC' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AD' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->porcentaje_iva);

                        });
                        $sheet->cell('AE' . $i, function ($cell) use ($value, $tipo_examen) {
                            // manipulate the cel
                            //$nuevo_iva = $value->cantidad*$value->valor_unitario*$value->porcentaje_iva;
                            if ($tipo_examen == 'IMM') {
                                $nuevo_iva = $value->subtotal * $value->porcentaje_iva;
                                $nuevo_iva = $nuevo_iva / $value->cantidad;
                            }else{
                                $nuevo_iva = $value->valor_unitario * $value->porcentaje_iva;
                            }
                            $cell->setValue(round($nuevo_iva, 4));

                        });
                        $sheet->cell('AF' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('F');

                        });
                        $i++;
                    }
                    $i = $i;

                }

                if (!is_null($archivo_plano_3)) {

                    foreach ($archivo_plano_3 as $value) {

                        $tipo_examen = '';
                        $ap_tipo_examen = Ap_Tipo_Examen::where('tipo',$value->tipo)->first();
                        if(!is_null($ap_tipo_examen)){
                            $tipo_examen = $ap_tipo_examen->tipo_ex;
                        }
                        if ($value->id_paciente != $id_temporal) {
                            $id_temporal = $value->id_paciente;
                            $x++;
                        }

                        $sheet->cell('A' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('515');
                        });

                        $sheet->cell('B' . $i, function ($cell) use ($value, $x) {
                            // manipulate the cel
                            $cell->setValue($x);
                        });

                        $fecha_ing     = substr($value->fecha_ing, 0, 10);
                        $fecha_ing_inv = date("d/m/Y", strtotime($fecha_ing));

                        $sheet->cell('C' . $i, function ($cell) use ($fecha_ing_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_ing_inv);
                        });

                        $sheet->cell('D' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->tiposeg);
                        });
                        $sheet->cell('E' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_paciente);

                        });
                        $sheet->cell('F' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2, $texto_apellido_1, $texto_apellido_2, $texto_final, $texto_fin, $texto_nombre_1, $texto_nombre_2) {
                            // manipulate the cel
                            //$cell->setValue($value->paciente->apellido1.' '.$value->paciente->apellido2.' '.$value->paciente->nombre1.' '.$value->paciente->nombre2);
                            //$cell->setValue($value->apellido1.' '.$value->apellido2.' '.$value->nombre1.' '.$value->nombre2);
                            if ($value->apellido1 != 'N/A' && $value->apellido1 != '(N/A)') {
                                $texto_apellido_1 = $value->apellido1;
                            }

                            if ($value->apellido2 != 'N/A' && $value->apellido2 != '(N/A)') {
                                $texto_apellido_2 = $value->apellido2;
                            }

                            if ($value->nombre1 != 'N/A' && $value->nombre1 != '(N/A)') {
                                $texto_nombre_1 = $value->nombre1;
                            }

                            if ($value->nombre2 != 'N/A' && $value->nombre2 != '(N/A)') {
                                $texto_nombre_2 = $value->nombre2;
                            }

                            $texto_final = $texto_apellido_1 . ' ' . $texto_apellido_2 . ' ' . $texto_nombre_1 . ' ' . $texto_nombre_2;

                            if($value->apellido2 == '(N/A)' || $value->apellido2 == ('N/A') || $value->apellido2 == "."){
                                $texto_final = $texto_apellido_1 . ' ' . $texto_nombre_1 . ' ' . $texto_nombre_2;
                            }

                            //$texto_final = $texto_apellido_1.' '.$texto_apellido_2.' '. $value->nombre1.' '.$value->nombre2;

                            $texto_fin = str_replace($caract_remplazar_1, "", $texto_final);
                            $texto_fin = str_replace($caract_remplazar_2, "N", $texto_fin);
                            $cell->setValue($texto_fin);

                        });
                        $sheet->cell('G' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->paciente->sexo == 1) {
                                $cell->setValue('M');
                            } elseif ($value->paciente->sexo == 2) {
                                $cell->setValue('F');
                            }

                        });

                        $fecha_nac     = substr($value->paciente->fecha_nacimiento, 0, 10);
                        $fecha_nac_inv = date("d/m/Y", strtotime($fecha_nac));

                        $sheet->cell('H' . $i, function ($cell) use ($fecha_nac_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_nac_inv);

                        });
                        if ($value->paciente->fecha_nacimiento == null) {
                            $edad = 0;
                        } else {
                            $edad = Carbon::createFromDate(substr($value->paciente->fecha_nacimiento, 0, 4), substr($value->paciente->fecha_nacimiento, 5, 2), substr($value->paciente->fecha_nacimiento, 8, 2))->age;
                        }
                        $sheet->cell('I' . $i, function ($cell) use ($value, $edad) {
                            // manipulate the cel
                            $cell->setValue($edad);

                        });
                        $sheet->cell('J' . $i, function ($cell) use ($value,$tipo_examen) {
                            // manipulate the cel
                            if ($value->tipo == 'P' || $value->tipo == 'AN' || $value->tipo == 'TA') {
                                $cell->setValue('HME');

                            } elseif ($value->tipo == 'M') {
                                $cell->setValue('FAR');

                            } elseif ($value->tipo == 'S') {
                                $cell->setValue('HOSP/QUIR');

                            } elseif ($value->tipo == 'IM') {
                                $cell->setValue('IMA');

                            } elseif ($value->tipo == 'EX') {
                                $cell->setValue('LAB');

                            } elseif ($value->tipo == 'I' || $value->tipo == 'IV' || $value->tipo == 'IF' || $value->tipo == 'IVC' || $tipo_examen == 'IMM') {
                                $cell->setValue('IMM');

                            } elseif ($value->tipo == 'PA') {
                                $cell->setValue('PAQUE');

                            } elseif ($value->tipo == 'EQ') {
                                $cell->setValue('PRO/ESP');

                            }

                        });
                        $sheet->cell('K' . $i, function ($cell) use ($value,$tipo_examen) {
                            // manipulate the cel
                            if ($value->tipo == 'IV' || $value->tipo == 'I' || $value->tipo == 'M' || $value->tipo == 'IVC' || $tipo_examen == 'IMM') {
                                $cell->setValue(' ');
                            } else {
                                $cell->setValue($value->codigo);
                            }

                        });
                        $sheet->cell('L' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2,$tipo_examen) {
                            // manipulate the cel

                            //$cell->setValue($value->descripcion);
                            //$descrip = str_replace($caract_remplazar,"",$value->descripcion);
                            $descrip = str_replace($caract_remplazar_1, "", $value->descripcion);
                            $descrip = str_replace($caract_remplazar_2, "N", $descrip);

                            if ($value->tipo == 'IV' || $value->tipo == 'I' || $value->tipo == 'M' || $value->tipo == 'IVC' || $tipo_examen == 'IMM') {
                                //$cell->setValue($value->descripcion);
                                $cell->setValue($descrip);

                            } else {

                                $cell->setValue('');

                            }

                        });
                        $sheet->cell('M' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->cie10);

                        });
                        $sheet->cell('N' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('O' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('P' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->cantidad);

                        });
                        $sheet->cell('Q' . $i, function ($cell) use ($value) {
                            // manipulate the cel

                            //$cell->setValue(round($value->valor+$value->porcentaje10,2));
                            $cell->setValue(round($value->valor, 2));

                        });
                        $sheet->cell('R' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('0');

                        });
                        $sheet->cell('S' . $i, function ($cell) use ($value) {
                            // manipulate the cel

                            if ($value->parentesco == 'TITULAR') {
                                $cell->setValue('T');

                            } elseif ($value->parentesco == 'CONYUGE') {
                                $cell->setValue('C');

                            } elseif ($value->parentesco == 'HIJO/HIJA') {
                                $cell->setValue('H');

                            } elseif ($value->parentesco == 'PARIENTE') {
                                $cell->setValue('X');

                            }

                            //$cell->setValue($value->parentesco);

                        });
                        $sheet->cell('T' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_usuario);
                        });
                        $sheet->cell('U' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2) {
                            // manipulate the cel
                            //$cell->setValue($value->usuario->apellido1.' '.$value->usuario->apellido2.' '.$value->usuario->nombre1.' '.$value->usuario->nombre2);
                            //$cell->setValue($value->nombres);

                            //$descrip_nombre = str_replace($caract_remplazar,"",$value->nombres);
                            $descrip_nombre = str_replace($caract_remplazar_1, "", $value->nombres);
                            $descrip_nombre = str_replace($caract_remplazar_2, "N", $descrip_nombre);

                            $cell->setValue($descrip_nombre);

                        });
                        $sheet->cell('V' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('CVAF');

                        });
                        $sheet->cell('W' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('1');

                        });
                        $sheet->cell('X' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('1');

                        });
                        $sheet->cell('Y' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('D');

                        });
                        $sheet->cell('Z' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('0');

                        });
                        $sheet->cell('AA' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AB' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AC' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AD' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->porcentaje_iva);

                        });
                        $sheet->cell('AE' . $i, function ($cell) use ($value, $tipo_examen) {
                            // manipulate the cel
                            //$nuevo_iva = $value->cantidad*$value->valor_unitario*$value->porcentaje_iva;
                            if ($tipo_examen == 'IMM') {
                                $nuevo_iva = $value->subtotal * $value->porcentaje_iva;
                                $nuevo_iva = $nuevo_iva / $value->cantidad;
                            }else{
                                $nuevo_iva = $value->valor_unitario * $value->porcentaje_iva;
                            }
                            $cell->setValue(round($nuevo_iva, 4));

                        });
                        $sheet->cell('AF' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('F');

                        });
                        $i++;
                    }
                    $i = $i;

                }

                if (!is_null($archivo_plano_4)) {

                    foreach ($archivo_plano_4 as $value) {

                        $tipo_examen = '';
                        $ap_tipo_examen = Ap_Tipo_Examen::where('tipo',$value->tipo)->first();
                        if(!is_null($ap_tipo_examen)){
                            $tipo_examen = $ap_tipo_examen->tipo_ex;
                        }
                        if ($value->id_paciente != $id_temporal) {
                            $id_temporal = $value->id_paciente;
                            $x++;
                        }

                        $sheet->cell('A' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('515');
                        });

                        $sheet->cell('B' . $i, function ($cell) use ($value, $x) {
                            // manipulate the cel
                            $cell->setValue($x);
                        });

                        $fecha_ing     = substr($value->fecha_ing, 0, 10);
                        $fecha_ing_inv = date("d/m/Y", strtotime($fecha_ing));

                        $sheet->cell('C' . $i, function ($cell) use ($fecha_ing_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_ing_inv);
                        });

                        $sheet->cell('D' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->tiposeg);
                        });
                        $sheet->cell('E' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_paciente);

                        });
                        $sheet->cell('F' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2, $texto_apellido_1, $texto_apellido_2, $texto_final, $texto_fin, $texto_nombre_1, $texto_nombre_2) {
                            // manipulate the cel
                            //$cell->setValue($value->paciente->apellido1.' '.$value->paciente->apellido2.' '.$value->paciente->nombre1.' '.$value->paciente->nombre2);
                            //$cell->setValue($value->apellido1.' '.$value->apellido2.' '.$value->nombre1.' '.$value->nombre2);
                            if ($value->apellido1 != 'N/A' && $value->apellido1 != '(N/A)') {
                                $texto_apellido_1 = $value->apellido1;
                            }

                            if ($value->apellido2 != 'N/A' && $value->apellido2 != '(N/A)') {
                                $texto_apellido_2 = $value->apellido2;
                            }

                            if ($value->nombre1 != 'N/A' && $value->nombre1 != '(N/A)') {
                                $texto_nombre_1 = $value->nombre1;
                            }

                            if ($value->nombre2 != 'N/A' && $value->nombre2 != '(N/A)') {
                                $texto_nombre_2 = $value->nombre2;
                            }

                            $texto_final = $texto_apellido_1 . ' ' . $texto_apellido_2 . ' ' . $texto_nombre_1 . ' ' . $texto_nombre_2;

                            if($value->apellido2 == '(N/A)' || $value->apellido2 == ('N/A') || $value->apellido2 == "."){
                                $texto_final = $texto_apellido_1 . ' ' . $texto_nombre_1 . ' ' . $texto_nombre_2;
                            }

                            //$texto_final = $texto_apellido_1.' '.$texto_apellido_2.' '. $value->nombre1.' '.$value->nombre2;

                            $texto_fin = str_replace($caract_remplazar_1, "", $texto_final);
                            $texto_fin = str_replace($caract_remplazar_2, "N", $texto_fin);
                            $cell->setValue($texto_fin);

                        });
                        $sheet->cell('G' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->paciente->sexo == 1) {
                                $cell->setValue('M');
                            } elseif ($value->paciente->sexo == 2) {
                                $cell->setValue('F');
                            }

                        });

                        $fecha_nac     = substr($value->paciente->fecha_nacimiento, 0, 10);
                        $fecha_nac_inv = date("d/m/Y", strtotime($fecha_nac));

                        $sheet->cell('H' . $i, function ($cell) use ($fecha_nac_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_nac_inv);

                        });
                        if ($value->paciente->fecha_nacimiento == null) {
                            $edad = 0;
                        } else {
                            $edad = Carbon::createFromDate(substr($value->paciente->fecha_nacimiento, 0, 4), substr($value->paciente->fecha_nacimiento, 5, 2), substr($value->paciente->fecha_nacimiento, 8, 2))->age;
                        }
                        $sheet->cell('I' . $i, function ($cell) use ($value, $edad) {
                            // manipulate the cel
                            $cell->setValue($edad);

                        });
                        $sheet->cell('J' . $i, function ($cell) use ($value,$tipo_examen) {
                            // manipulate the cel
                            if ($value->tipo == 'P' || $value->tipo == 'AN' || $value->tipo == 'TA') {
                                $cell->setValue('HME');

                            } elseif ($value->tipo == 'M') {
                                $cell->setValue('FAR');

                            } elseif ($value->tipo == 'S') {
                                $cell->setValue('HOSP/QUIR');

                            } elseif ($value->tipo == 'IM') {
                                $cell->setValue('IMA');

                            } elseif ($value->tipo == 'EX') {
                                $cell->setValue('LAB');

                            } elseif ($value->tipo == 'I' || $value->tipo == 'IV' || $value->tipo == 'IF' || $value->tipo == 'IVC' || $tipo_examen == 'IMM') {
                                $cell->setValue('IMM');

                            } elseif ($value->tipo == 'PA') {
                                $cell->setValue('PAQUE');

                            } elseif ($value->tipo == 'EQ') {
                                $cell->setValue('PRO/ESP');

                            }

                        });
                        $sheet->cell('K' . $i, function ($cell) use ($value,$tipo_examen) {
                            // manipulate the cel
                            if ($value->tipo == 'IV' || $value->tipo == 'I' || $value->tipo == 'M' || $value->tipo == 'IVC' || $tipo_examen == 'IMM') {
                                $cell->setValue(' ');
                            } else {
                                $cell->setValue($value->codigo);
                            }

                        });
                        $sheet->cell('L' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2,$tipo_examen) {
                            // manipulate the cel

                            //$cell->setValue($value->descripcion);
                            //$descrip = str_replace($caract_remplazar,"",$value->descripcion);
                            $descrip = str_replace($caract_remplazar_1, "", $value->descripcion);
                            $descrip = str_replace($caract_remplazar_2, "N", $descrip);

                            if ($value->tipo == 'IV' || $value->tipo == 'I' || $value->tipo == 'M' || $value->tipo == 'IVC' || $tipo_examen == 'IMM') {
                                //$cell->setValue($value->descripcion);
                                $cell->setValue($descrip);

                            } else {

                                $cell->setValue('');

                            }

                        });
                        $sheet->cell('M' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->cie10);

                        });
                        $sheet->cell('N' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('O' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('P' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->cantidad);

                        });
                        $sheet->cell('Q' . $i, function ($cell) use ($value) {
                            // manipulate the cel

                            //$cell->setValue(round($value->valor+$value->porcentaje10,2));
                            $cell->setValue(round($value->valor, 2));

                        });
                        $sheet->cell('R' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('0');

                        });
                        $sheet->cell('S' . $i, function ($cell) use ($value) {
                            // manipulate the cel

                            if ($value->parentesco == 'TITULAR') {
                                $cell->setValue('T');

                            } elseif ($value->parentesco == 'CONYUGE') {
                                $cell->setValue('C');

                            } elseif ($value->parentesco == 'HIJO/HIJA') {
                                $cell->setValue('H');

                            } elseif ($value->parentesco == 'PARIENTE') {
                                $cell->setValue('X');

                            }

                            //$cell->setValue($value->parentesco);

                        });
                        $sheet->cell('T' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_usuario);
                        });
                        $sheet->cell('U' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2) {
                            // manipulate the cel
                            //$cell->setValue($value->usuario->apellido1.' '.$value->usuario->apellido2.' '.$value->usuario->nombre1.' '.$value->usuario->nombre2);
                            //$cell->setValue($value->nombres);

                            //$descrip_nombre = str_replace($caract_remplazar,"",$value->nombres);
                            $descrip_nombre = str_replace($caract_remplazar_1, "", $value->nombres);
                            $descrip_nombre = str_replace($caract_remplazar_2, "N", $descrip_nombre);

                            $cell->setValue($descrip_nombre);

                        });
                        $sheet->cell('V' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('CVAF');

                        });
                        $sheet->cell('W' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('1');

                        });
                        $sheet->cell('X' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('1');

                        });
                        $sheet->cell('Y' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('D');

                        });
                        $sheet->cell('Z' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('0');

                        });
                        $sheet->cell('AA' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AB' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AC' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AD' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->porcentaje_iva);

                        });
                        $sheet->cell('AE' . $i, function ($cell) use ($value, $tipo_examen) {
                            // manipulate the cel
                            //$nuevo_iva = $value->cantidad*$value->valor_unitario*$value->porcentaje_iva;
                            if ($tipo_examen == 'IMM') {
                                $nuevo_iva = $value->subtotal * $value->porcentaje_iva;
                                $nuevo_iva = $nuevo_iva / $value->cantidad;
                            }else{
                                $nuevo_iva = $value->valor_unitario * $value->porcentaje_iva;
                            }
                            $cell->setValue(round($nuevo_iva, 4));

                        });
                        $sheet->cell('AF' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('F');

                        });
                        $i++;
                    }
                    $i = $i;

                }

                if (!is_null($archivo_plano_5)) {

                    foreach ($archivo_plano_5 as $value) {

                        $tipo_examen = '';
                        $ap_tipo_examen = Ap_Tipo_Examen::where('tipo',$value->tipo)->first();
                        if(!is_null($ap_tipo_examen)){
                            $tipo_examen = $ap_tipo_examen->tipo_ex;
                        }

                        if ($value->id_paciente != $id_temporal) {
                            $id_temporal = $value->id_paciente;
                            $x++;
                        }

                        $sheet->cell('A' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('515');
                        });

                        $sheet->cell('B' . $i, function ($cell) use ($value, $x) {
                            // manipulate the cel
                            $cell->setValue($x);
                        });

                        $fecha_ing     = substr($value->fecha_ing, 0, 10);
                        $fecha_ing_inv = date("d/m/Y", strtotime($fecha_ing));

                        $sheet->cell('C' . $i, function ($cell) use ($fecha_ing_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_ing_inv);
                        });

                        $sheet->cell('D' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->tiposeg);
                        });
                        $sheet->cell('E' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_paciente);

                        });
                        $sheet->cell('F' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2, $texto_apellido_1, $texto_apellido_2, $texto_final, $texto_fin, $texto_nombre_1, $texto_nombre_2) {
                            // manipulate the cel
                            //$cell->setValue($value->paciente->apellido1.' '.$value->paciente->apellido2.' '.$value->paciente->nombre1.' '.$value->paciente->nombre2);
                            //$cell->setValue($value->apellido1.' '.$value->apellido2.' '.$value->nombre1.' '.$value->nombre2);
                            if ($value->apellido1 != 'N/A' && $value->apellido1 != '(N/A)') {
                                $texto_apellido_1 = $value->apellido1;
                            }

                            if ($value->apellido2 != 'N/A' && $value->apellido2 != '(N/A)') {
                                $texto_apellido_2 = $value->apellido2;
                            }

                            if ($value->nombre1 != 'N/A' && $value->nombre1 != '(N/A)') {
                                $texto_nombre_1 = $value->nombre1;
                            }

                            if ($value->nombre2 != 'N/A' && $value->nombre2 != '(N/A)') {
                                $texto_nombre_2 = $value->nombre2;
                            }

                            $texto_final = $texto_apellido_1 . ' ' . $texto_apellido_2 . ' ' . $texto_nombre_1 . ' ' . $texto_nombre_2;

                            if($value->apellido2 == '(N/A)' || $value->apellido2 == ('N/A') || $value->apellido2 == "."){
                                $texto_final = $texto_apellido_1 . ' ' . $texto_nombre_1 . ' ' . $texto_nombre_2;
                            }

                            //$texto_final = $texto_apellido_1.' '.$texto_apellido_2.' '. $value->nombre1.' '.$value->nombre2;

                            $texto_fin = str_replace($caract_remplazar_1, "", $texto_final);
                            $texto_fin = str_replace($caract_remplazar_2, "N", $texto_fin);
                            $cell->setValue($texto_fin);

                        });
                        $sheet->cell('G' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->paciente->sexo == 1) {
                                $cell->setValue('M');
                            } elseif ($value->paciente->sexo == 2) {
                                $cell->setValue('F');
                            }

                        });

                        $fecha_nac     = substr($value->paciente->fecha_nacimiento, 0, 10);
                        $fecha_nac_inv = date("d/m/Y", strtotime($fecha_nac));

                        $sheet->cell('H' . $i, function ($cell) use ($fecha_nac_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_nac_inv);

                        });
                        if ($value->paciente->fecha_nacimiento == null) {
                            $edad = 0;
                        } else {
                            $edad = Carbon::createFromDate(substr($value->paciente->fecha_nacimiento, 0, 4), substr($value->paciente->fecha_nacimiento, 5, 2), substr($value->paciente->fecha_nacimiento, 8, 2))->age;
                        }
                        $sheet->cell('I' . $i, function ($cell) use ($value, $edad) {
                            // manipulate the cel
                            $cell->setValue($edad);

                        });
                        $sheet->cell('J' . $i, function ($cell) use ($value,$tipo_examen) {
                            // manipulate the cel
                            if ($value->tipo == 'P' || $value->tipo == 'AN' || $value->tipo == 'TA') {
                                $cell->setValue('HME');

                            } elseif ($value->tipo == 'M') {
                                $cell->setValue('FAR');

                            } elseif ($value->tipo == 'S') {
                                $cell->setValue('HOSP/QUIR');

                            } elseif ($value->tipo == 'IM') {
                                $cell->setValue('IMA');

                            } elseif ($value->tipo == 'EX') {
                                $cell->setValue('LAB');

                            } elseif ($value->tipo == 'I' || $value->tipo == 'IV' || $value->tipo == 'IF' || $value->tipo == 'IVC' || $tipo_examen == 'IMM') {
                                $cell->setValue('IMM');

                            } elseif ($value->tipo == 'PA') {
                                $cell->setValue('PAQUE');

                            } elseif ($value->tipo == 'EQ') {
                                $cell->setValue('PRO/ESP');

                            }

                        });
                        $sheet->cell('K' . $i, function ($cell) use ($value,$tipo_examen) {
                            // manipulate the cel
                            if ($value->tipo == 'IV' || $value->tipo == 'I' || $value->tipo == 'M' || $value->tipo == 'IVC' || $tipo_examen == 'IMM') {
                                $cell->setValue(' ');
                            } else {
                                $cell->setValue($value->codigo);
                            }

                        });
                        $sheet->cell('L' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2,$tipo_examen) {
                            // manipulate the cel

                            //$cell->setValue($value->descripcion);
                            //$descrip = str_replace($caract_remplazar,"",$value->descripcion);
                            $descrip = str_replace($caract_remplazar_1, "", $value->descripcion);
                            $descrip = str_replace($caract_remplazar_2, "N", $descrip);

                            if ($value->tipo == 'IV' || $value->tipo == 'I' || $value->tipo == 'M' || $value->tipo == 'IVC' || $tipo_examen == 'IMM') {
                                //$cell->setValue($value->descripcion);
                                $cell->setValue($descrip);

                            } else {

                                $cell->setValue('');

                            }

                        });
                        $sheet->cell('M' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->cie10);

                        });
                        $sheet->cell('N' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('O' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('P' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->cantidad);

                        });
                        $sheet->cell('Q' . $i, function ($cell) use ($value) {
                            // manipulate the cel

                            //$cell->setValue(round($value->valor+$value->porcentaje10,2));
                            $cell->setValue(round($value->valor, 2));

                        });
                        $sheet->cell('R' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('0');

                        });
                        $sheet->cell('S' . $i, function ($cell) use ($value) {
                            // manipulate the cel

                            if ($value->parentesco == 'TITULAR') {
                                $cell->setValue('T');

                            } elseif ($value->parentesco == 'CONYUGE') {
                                $cell->setValue('C');

                            } elseif ($value->parentesco == 'HIJO/HIJA') {
                                $cell->setValue('H');

                            } elseif ($value->parentesco == 'PARIENTE') {
                                $cell->setValue('X');

                            }

                            //$cell->setValue($value->parentesco);

                        });
                        $sheet->cell('T' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_usuario);
                        });
                        $sheet->cell('U' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2) {
                            // manipulate the cel
                            //$cell->setValue($value->usuario->apellido1.' '.$value->usuario->apellido2.' '.$value->usuario->nombre1.' '.$value->usuario->nombre2);
                            //$cell->setValue($value->nombres);

                            //$descrip_nombre = str_replace($caract_remplazar,"",$value->nombres);
                            $descrip_nombre = str_replace($caract_remplazar_1, "", $value->nombres);
                            $descrip_nombre = str_replace($caract_remplazar_2, "N", $descrip_nombre);

                            $cell->setValue($descrip_nombre);

                        });
                        $sheet->cell('V' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('CVAF');

                        });
                        $sheet->cell('W' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('1');

                        });
                        $sheet->cell('X' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('1');

                        });
                        $sheet->cell('Y' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('D');

                        });
                        $sheet->cell('Z' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('0');

                        });
                        $sheet->cell('AA' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AB' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AC' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AD' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->porcentaje_iva);

                        });
                        $sheet->cell('AE' . $i, function ($cell) use ($value, $tipo_examen) {
                            // manipulate the cel
                            //$nuevo_iva = $value->cantidad*$value->valor_unitario*$value->porcentaje_iva;
                            if ($tipo_examen == 'IMM') {
                                $nuevo_iva = $value->subtotal * $value->porcentaje_iva;
                                $nuevo_iva = $nuevo_iva / $value->cantidad;
                            }else{
                                $nuevo_iva = $value->valor_unitario * $value->porcentaje_iva;
                            }
                            $cell->setValue(round($nuevo_iva, 4));

                        });
                        $sheet->cell('AF' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('F');

                        });
                        $i++;
                    }
                    $i = $i;

                }

                if (!is_null($archivo_plano_6)) {

                    foreach ($archivo_plano_6 as $value) {

                        $tipo_examen = '';
                        $ap_tipo_examen = Ap_Tipo_Examen::where('tipo',$value->tipo)->first();
                        if(!is_null($ap_tipo_examen)){
                            $tipo_examen = $ap_tipo_examen->tipo_ex;
                        }

                        if ($value->id_paciente != $id_temporal) {
                            $id_temporal = $value->id_paciente;
                            $x++;
                        }

                        $sheet->cell('A' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('515');
                        });

                        $sheet->cell('B' . $i, function ($cell) use ($value, $x) {
                            // manipulate the cel
                            $cell->setValue($x);
                        });

                        $fecha_ing     = substr($value->fecha_ing, 0, 10);
                        $fecha_ing_inv = date("d/m/Y", strtotime($fecha_ing));

                        $sheet->cell('C' . $i, function ($cell) use ($fecha_ing_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_ing_inv);
                        });

                        $sheet->cell('D' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->tiposeg);
                        });
                        $sheet->cell('E' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_paciente);

                        });
                        $sheet->cell('F' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2, $texto_apellido_1, $texto_apellido_2, $texto_final, $texto_fin, $texto_nombre_1, $texto_nombre_2) {
                            // manipulate the cel
                            //$cell->setValue($value->paciente->apellido1.' '.$value->paciente->apellido2.' '.$value->paciente->nombre1.' '.$value->paciente->nombre2);
                            //$cell->setValue($value->apellido1.' '.$value->apellido2.' '.$value->nombre1.' '.$value->nombre2);
                            if ($value->apellido1 != 'N/A' && $value->apellido1 != '(N/A)') {
                                $texto_apellido_1 = $value->apellido1;
                            }

                            if ($value->apellido2 != 'N/A' && $value->apellido2 != '(N/A)') {
                                $texto_apellido_2 = $value->apellido2;
                            }

                            if ($value->nombre1 != 'N/A' && $value->nombre1 != '(N/A)') {
                                $texto_nombre_1 = $value->nombre1;
                            }

                            if ($value->nombre2 != 'N/A' && $value->nombre2 != '(N/A)') {
                                $texto_nombre_2 = $value->nombre2;
                            }

                            $texto_final = $texto_apellido_1 . ' ' . $texto_apellido_2 . ' ' . $texto_nombre_1 . ' ' . $texto_nombre_2;

                            if($value->apellido2 == '(N/A)' || $value->apellido2 == ('N/A') || $value->apellido2 == "."){
                                $texto_final = $texto_apellido_1 . ' ' . $texto_nombre_1 . ' ' . $texto_nombre_2;
                            }

                            //$texto_final = $texto_apellido_1.' '.$texto_apellido_2.' '. $value->nombre1.' '.$value->nombre2;

                            $texto_fin = str_replace($caract_remplazar_1, "", $texto_final);
                            $texto_fin = str_replace($caract_remplazar_2, "N", $texto_fin);
                            $cell->setValue($texto_fin);

                        });
                        $sheet->cell('G' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->paciente->sexo == 1) {
                                $cell->setValue('M');
                            } elseif ($value->paciente->sexo == 2) {
                                $cell->setValue('F');
                            }

                        });

                        $fecha_nac     = substr($value->paciente->fecha_nacimiento, 0, 10);
                        $fecha_nac_inv = date("d/m/Y", strtotime($fecha_nac));

                        $sheet->cell('H' . $i, function ($cell) use ($fecha_nac_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_nac_inv);

                        });
                        if ($value->paciente->fecha_nacimiento == null) {
                            $edad = 0;
                        } else {
                            $edad = Carbon::createFromDate(substr($value->paciente->fecha_nacimiento, 0, 4), substr($value->paciente->fecha_nacimiento, 5, 2), substr($value->paciente->fecha_nacimiento, 8, 2))->age;
                        }
                        $sheet->cell('I' . $i, function ($cell) use ($value, $edad) {
                            // manipulate the cel
                            $cell->setValue($edad);

                        });
                        $sheet->cell('J' . $i, function ($cell) use ($value,$tipo_examen) {
                            // manipulate the cel
                            if ($value->tipo == 'P' || $value->tipo == 'AN' || $value->tipo == 'TA') {
                                $cell->setValue('HME');

                            } elseif ($value->tipo == 'M') {
                                $cell->setValue('FAR');

                            } elseif ($value->tipo == 'S') {
                                $cell->setValue('HOSP/QUIR');

                            } elseif ($value->tipo == 'IM') {
                                $cell->setValue('IMA');

                            } elseif ($value->tipo == 'EX') {
                                $cell->setValue('LAB');

                            } elseif ($value->tipo == 'I' || $value->tipo == 'IV' || $value->tipo == 'IF' || $value->tipo == 'IVC' || $tipo_examen == 'IMM') {
                                $cell->setValue('IMM');

                            } elseif ($value->tipo == 'PA') {
                                $cell->setValue('PAQUE');

                            } elseif ($value->tipo == 'EQ') {
                                $cell->setValue('PRO/ESP');

                            }

                        });
                        $sheet->cell('K' . $i, function ($cell) use ($value,$tipo_examen) {
                            // manipulate the cel
                            if ($value->tipo == 'IV' || $value->tipo == 'I' || $value->tipo == 'M' || $value->tipo == 'IVC' || $tipo_examen == 'IMM') {
                                $cell->setValue(' ');
                            } else {
                                $cell->setValue($value->codigo);
                            }

                        });
                        $sheet->cell('L' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2,$tipo_examen) {
                            // manipulate the cel

                            //$cell->setValue($value->descripcion);
                            //$descrip = str_replace($caract_remplazar,"",$value->descripcion);
                            $descrip = str_replace($caract_remplazar_1, "", $value->descripcion);
                            $descrip = str_replace($caract_remplazar_2, "N", $descrip);

                            if ($value->tipo == 'IV' || $value->tipo == 'I' || $value->tipo == 'M' || $value->tipo == 'IVC' || $tipo_examen == 'IMM') {
                                //$cell->setValue($value->descripcion);
                                $cell->setValue($descrip);

                            } else {

                                $cell->setValue('');

                            }

                        });
                        $sheet->cell('M' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->cie10);

                        });
                        $sheet->cell('N' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('O' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('P' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->cantidad);

                        });
                        $sheet->cell('Q' . $i, function ($cell) use ($value) {
                            // manipulate the cel

                            //$cell->setValue(round($value->valor+$value->porcentaje10,2));
                            $cell->setValue(round($value->valor, 2));

                        });
                        $sheet->cell('R' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('0');

                        });
                        $sheet->cell('S' . $i, function ($cell) use ($value) {
                            // manipulate the cel

                            if ($value->parentesco == 'TITULAR') {
                                $cell->setValue('T');

                            } elseif ($value->parentesco == 'CONYUGE') {
                                $cell->setValue('C');

                            } elseif ($value->parentesco == 'HIJO/HIJA') {
                                $cell->setValue('H');

                            } elseif ($value->parentesco == 'PARIENTE') {
                                $cell->setValue('X');

                            }

                            //$cell->setValue($value->parentesco);

                        });
                        $sheet->cell('T' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_usuario);
                        });
                        $sheet->cell('U' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2) {
                            // manipulate the cel
                            //$cell->setValue($value->usuario->apellido1.' '.$value->usuario->apellido2.' '.$value->usuario->nombre1.' '.$value->usuario->nombre2);
                            //$cell->setValue($value->nombres);

                            //$descrip_nombre = str_replace($caract_remplazar,"",$value->nombres);
                            $descrip_nombre = str_replace($caract_remplazar_1, "", $value->nombres);
                            $descrip_nombre = str_replace($caract_remplazar_2, "N", $descrip_nombre);

                            $cell->setValue($descrip_nombre);

                        });
                        $sheet->cell('V' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('CVAF');

                        });
                        $sheet->cell('W' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('1');

                        });
                        $sheet->cell('X' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('1');

                        });
                        $sheet->cell('Y' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('D');

                        });
                        $sheet->cell('Z' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('0');

                        });
                        $sheet->cell('AA' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AB' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AC' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AD' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->porcentaje_iva);

                        });
                        $sheet->cell('AE' . $i, function ($cell) use ($value, $tipo_examen) {
                            // manipulate the cel
                            //$nuevo_iva = $value->cantidad*$value->valor_unitario*$value->porcentaje_iva;
                            if ($tipo_examen == 'IMM') {
                                $nuevo_iva = $value->subtotal * $value->porcentaje_iva;
                                $nuevo_iva = $nuevo_iva / $value->cantidad;
                            }else{
                                $nuevo_iva = $value->valor_unitario * $value->porcentaje_iva;
                            }
                            $cell->setValue(round($nuevo_iva, 4));

                        });
                        $sheet->cell('AF' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('F');

                        });
                        $i++;
                    }
                    $i = $i;

                }
            });

            $excel->getActiveSheet()->getColumnDimension("A")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("B")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("C")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("D")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("E")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("F")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("G")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("H")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("I")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("J")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("K")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("L")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("M")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("N")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("O")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("P")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("Q")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("R")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("S")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("T")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("U")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("V")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("W")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("X")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("Y")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("Z")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AA")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AB")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AC")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AD")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AE")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AF")->setWidth(11)->setAutosize(false);
        })->export('xlsx');
    }*/

    public function genera_plan_consolidada_codigo_pdf($idcab)
    {
        try {
            $cabecera = Archivo_Plano_Cabecera::find($idcab);
            if (is_null($cabecera)) {
                return redirect()->back()->with('error', 'No se encontr&oacute; la planilla.');
            }

            $archivo_planos = Archivo_Plano_Cabecera::where('cod_deriva_msp', $cabecera->cod_deriva_msp)
                ->where('id_paciente', $cabecera->id_paciente)
                ->where('mes_plano', $cabecera->mes_plano)
                ->where('id_seguro', $cabecera->id_seguro)
                ->where('id_empresa', $cabecera->id_empresa)
                ->where('estado', '1')
                ->orderBy('fecha_ing', 'desc')
                ->get();

            $archivo_plano = $archivo_planos->first();
            $primera_atencion = $archivo_planos->sortBy('fecha_ing')->first();
            $ultima_atencion = $archivo_planos->sortByDesc('fecha_ing')->first();

            if (is_null($archivo_plano) || is_null($primera_atencion) || is_null($ultima_atencion)) {
                return redirect()->back()->with('error', 'No se encontraron planillas para el c&oacute;digo de validaci&oacute;n.');
            }

            $fecha_elaboracion = date('d/m/Y', strtotime($primera_atencion->fecha_ing));

            $fecha_desde = substr($primera_atencion->fecha_ing, 0, 10);
            $invert_fech_desde = explode('-', $fecha_desde);
            $fecha_desde_invert = $invert_fech_desde[2] . '/' . $invert_fech_desde[1] . '/' . $invert_fech_desde[0];

            $fecha_hasta = substr($ultima_atencion->fecha_ing, 0, 10);
            $invert_fech_hasta = explode('-', $fecha_hasta);
            $fecha_hasta_invert = $invert_fech_hasta[2] . '/' . $invert_fech_hasta[1] . '/' . $invert_fech_hasta[0];

            $meses = [
                '01' => 'ENERO', '02' => 'FEBRERO', '03' => 'MARZO', '04' => 'ABRIL',
                '05' => 'MAYO', '06' => 'JUNIO', '07' => 'JULIO', '08' => 'AGOSTO',
                '09' => 'SEPTIEMBRE', '10' => 'OCTUBRE', '11' => 'NOVIEMBRE', '12' => 'DICIEMBRE',
            ];
            $mes_anio_servicio = $meses[$invert_fech_desde[1]] . '-' . $invert_fech_desde[0];

            $txt_cie10 = null;
            $cie10 = Cie_10_3::find($archivo_plano->cie10);
            if (is_null($cie10)) {
                $cie10 = Cie_10_4::find($archivo_plano->cie10);
            }
            if (!is_null($cie10)) {
                $txt_cie10 = '(' . $archivo_plano->cie10 . ') ' . $cie10->descripcion;
            }

            $planilla_msp_detalle = DB::table('archivo_plano_detalle as apd')
                ->whereIn('apd.id_ap_cabecera', $archivo_planos->pluck('id')->toArray())
                ->where('apd.estado', '1')
                ->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')
                ->select(
                    'apd.fecha', 'apd.codigo', 'apd.descripcion', 'apt.clasificado',
                    'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.clasif_porcentaje_msp',
                    'apd.valor_porcent_clasifi', 'apd.total_solicitado_usd', 'apd.tipo',
                    'apd.clasificador', 'apd.valor_unitario', 'apd.porcentaje_iva',
                    'apd.porcent_10', 'apd.iva_unitario', 'apd.p10_unitario', 'apd.total'
                )
                ->orderBy('apd.fecha', 'asc')
                ->orderBy('apt.orden_msp', 'asc')
                ->orderBy('apd.id', 'asc')
                ->get();

            $detalles_pdf = [];
            $total = 0;
            foreach ($planilla_msp_detalle as $value) {
                $fecha = substr($value->fecha, 0, 10);
                $fecha_partes = explode('-', $fecha);
                $fecha_detalle = $fecha_partes[2] . '/' . $fecha_partes[1] . '/' . $fecha_partes[0];

                $codigo = '';
                if (!in_array($value->tipo, ['IV', 'I', 'M', 'IVC'])) {
                    $codigo = $value->codigo;
                }

                if ($value->codigo == '16') {
                    $ap_procedimiento = ApProcedimiento::where('codigo', $value->codigo)
                        ->where('tipo', $value->tipo)
                        ->first();
                    if (!is_null($ap_procedimiento)) {
                        $codigo = $ap_procedimiento->cod_cb;
                    }
                }

                $total_solicitado = (float) $value->total_solicitado_usd;
                if ($value->valor_porcent_clasifi > 0) {
                    $total_solicitado += (float) $value->valor_porcent_clasifi;
                }
                $total += $total_solicitado;

                $detalles_pdf[] = [
                    'fecha' => $fecha_detalle,
                    'codigo' => $codigo,
                    'tipo' => $value->tipo,
                    'clasificador' => $value->clasificador,
                    'descripcion' => $value->descripcion,
                    'cantidad' => $value->cantidad,
                    'valor_unitario' => round((float) $value->valor_unitario + (float) $value->p10_unitario + (float) $value->iva_unitario, 2),
                    'clasificador_porcentaje' => round((float) $value->clasif_porcentaje_msp, 2),
                    'subtotal_usd' => round((float) $value->total, 2),
                    'valor_modificador' => round((float) $value->valor_porcent_clasifi, 2),
                    'total_solicitado' => round($total_solicitado, 2),
                ];
            }

            $empresa_nombre = !is_null($archivo_plano->empresa) ? $archivo_plano->empresa->razonsocial : '';
            $paciente_nombre = !is_null($archivo_plano->paciente)
                ? trim($archivo_plano->paciente->apellido1 . ' ' . $archivo_plano->paciente->apellido2 . ' ' . $archivo_plano->paciente->nombre1 . ' ' . $archivo_plano->paciente->nombre2)
                : '';

            $data = [
                'titulo_planilla' => 'PLANILLA DE CARGOS INDIVIDUAL',
                'empresa_nombre' => $empresa_nombre,
                'tipo_servicio' => 'AMBULATORIO',
                'fecha_elaboracion' => $fecha_elaboracion,
                'monto_solicitado' => round($total, 2),
                'mes_anio_servicio' => $mes_anio_servicio,
                'nro_cv' => $archivo_plano->cod_deriva_msp,
                'cie10' => $archivo_plano->cie10,
                'txt_cie10' => $txt_cie10,
                'paciente_identificacion' => $archivo_plano->id_paciente,
                'paciente_nombre' => $paciente_nombre,
                'fecha_desde' => $fecha_desde_invert,
                'fecha_hasta' => $fecha_hasta_invert,
                'detalles' => $detalles_pdf,
                'total' => round($total, 2),
                'firma_nombre' => 'DRA. AURA EDITH ZAMBRANO PAREDES',
                'firma_cc' => '0926862152',
                'firma_cargo' => 'COORDINADORA DE AUDITORIA MEDICA',
            ];

            $pdf = \App::make('dompdf.wrapper');
            $pdf->setPaper('a4', 'portrait');
            $pdf->loadView('archivo_plano/planilla/planilla_msp_PDF', $data);
            return $pdf->stream('Planilla_MSP_Consolidada_' . $archivo_plano->id . '.pdf');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ocurri&oacute; un error al generar el PDF.');
        }
    }

    public function genera_ap_excel_092022(Request $request)
    {
        //ini_set('memory_limit', '1024M');
        //dd("sistemas");
        $mes_plano = $request['mes_plano'];
        $seguro     = $request->seguro;
        $tipo_seg   = $request->id_tipo_seguro;
        //$cob_compar = $request['id_cobertura_comp'];
        $empresa    = $request->id_empresa;

        $archivo_plano_1 = [];
        $archivo_plano_2 = [];
        $archivo_plano_3 = [];
        $archivo_plano_4 = [];
        $archivo_plano_5 = [];
        $archivo_plano   = [];

        $texto_apellido_1 = null;
        $texto_apellido_2 = null;
        $texto_final      = null;
        $texto_fin        = null;
        $texto_nombre_1   = null;
        $texto_nombre_2   = null;

        $caract_remplazar_1 = array(
            "¨",
            "$",
            "%",
            "&",
            "/",
            "(",
            ")",
            "#",
            "@",
            "|",
            "!",
            "/",
            "+",
            "-",
            "}",
            "{",
            ">",
            "< ",
            ";",
            ",",
            ":",
            ".",
            "*",
            ">",
            "<",
            "[",
            "]",
            "?",
            "¿",
            "º",
            "~"
        );

        $caract_remplazar_2 = array("Ñ", "ñ");

        //ACTIVOS
        $cob_compar = null;
        $arr_plano = [];
        $archivo_plano_1 = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->where('archivo_plano_cabecera.id_seguro', $seguro)
            ->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            ->where('archivo_plano_cabecera.estado', '1')
            ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd', 'apd.id_ap_cabecera', 'archivo_plano_cabecera.id')
            ->join('ap_tipo_examen as tipo_ex', 'tipo_ex.tipo', 'apd.tipo')
            ->where('apd.estado', '1')
            ->where('tseg.tipo_principal', 'ACTIVO')
            ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.valor_unitario', 'apd.subtotal', 'archivo_plano_cabecera.id as id_cab')
            ->orderby('full_name')
            ->orderby('archivo_plano_cabecera.fecha_ing')
            ->orderby('tipo_ex.orden_plano')
            ->get();

        array_push($arr_plano, $archivo_plano_1);

        $cob_compar = null; //JUBILADO
        $archivo_plano_2 = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->where('archivo_plano_cabecera.id_seguro', $seguro)
            ->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            ->where('archivo_plano_cabecera.estado', '1')
            ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd', 'apd.id_ap_cabecera', 'archivo_plano_cabecera.id')
            ->join('ap_tipo_examen as tipo_ex', 'tipo_ex.tipo', 'apd.tipo')
            ->where('apd.estado', '1')
            ->where('tseg.tipo_principal', 'JUBILADO')
            ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.valor_unitario', 'apd.subtotal', 'archivo_plano_cabecera.id as id_cab')
            ->orderby('archivo_plano_cabecera.id_tipo_seguro')
            ->orderby('full_name')
            ->orderby('archivo_plano_cabecera.fecha_ing')
            ->orderby('tipo_ex.orden_plano')
            ->get();

        array_push($arr_plano, $archivo_plano_2);

        $cob_compar = null; //MONTEPIO
        $archivo_plano_2a = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->where('archivo_plano_cabecera.id_seguro', $seguro)
            ->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            ->where('archivo_plano_cabecera.estado', '1')
            ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd', 'apd.id_ap_cabecera', 'archivo_plano_cabecera.id')
            ->join('ap_tipo_examen as tipo_ex', 'tipo_ex.tipo', 'apd.tipo')
            ->where('apd.estado', '1')
            ->where('tseg.tipo_principal', 'MONTEPIO')
            ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.valor_unitario', 'apd.subtotal', 'archivo_plano_cabecera.id as id_cab')
            ->orderby('archivo_plano_cabecera.id_tipo_seguro')
            ->orderby('full_name')
            ->orderby('archivo_plano_cabecera.fecha_ing')
            ->orderby('tipo_ex.orden_plano')
            ->get();

        array_push($arr_plano, $archivo_plano_2a);

        $cob_compar = 3; //ISSFA-ACTIVOS
        $archivo_plano_3 = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->where('archivo_plano_cabecera.id_seguro', $seguro)
            ->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            ->where('archivo_plano_cabecera.estado', '1')
            ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd', 'apd.id_ap_cabecera', 'archivo_plano_cabecera.id')
            ->join('ap_tipo_examen as tipo_ex', 'tipo_ex.tipo', 'apd.tipo')
            ->where('apd.estado', '1')
            ->where('tseg.tipo_principal', 'ACTIVO')
            ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.valor_unitario', 'apd.subtotal', 'archivo_plano_cabecera.id as id_cab')
            ->orderby('full_name')
            ->orderby('archivo_plano_cabecera.fecha_ing')
            ->orderby('tipo_ex.orden_plano')
            ->get();

        array_push($arr_plano, $archivo_plano_3);

        $cob_compar = 3; //ISSFA-JUBILADO
        $archivo_plano_4 = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->where('archivo_plano_cabecera.id_seguro', $seguro)
            ->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            ->where('archivo_plano_cabecera.estado', '1')
            ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd', 'apd.id_ap_cabecera', 'archivo_plano_cabecera.id')
            ->join('ap_tipo_examen as tipo_ex', 'tipo_ex.tipo', 'apd.tipo')
            ->where('apd.estado', '1')
            ->where('tseg.tipo_principal', 'JUBILADO')
            ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.valor_unitario', 'apd.subtotal', 'archivo_plano_cabecera.id as id_cab')
            ->orderby('archivo_plano_cabecera.id_tipo_seguro')
            ->orderby('full_name')
            ->orderby('archivo_plano_cabecera.fecha_ing')
            ->orderby('tipo_ex.orden_plano')
            ->get();

        array_push($arr_plano, $archivo_plano_4);

        $cob_compar = 3; //ISSFA-MONTEPIO
        $archivo_plano_4a = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->where('archivo_plano_cabecera.id_seguro', $seguro)
            ->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            ->where('archivo_plano_cabecera.estado', '1')
            ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd', 'apd.id_ap_cabecera', 'archivo_plano_cabecera.id')
            ->join('ap_tipo_examen as tipo_ex', 'tipo_ex.tipo', 'apd.tipo')
            ->where('apd.estado', '1')
            ->where('tseg.tipo_principal', 'MONTEPIO')
            ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.valor_unitario', 'apd.subtotal', 'archivo_plano_cabecera.id as id_cab')
            ->orderby('archivo_plano_cabecera.id_tipo_seguro')
            ->orderby('full_name')
            ->orderby('archivo_plano_cabecera.fecha_ing')
            ->orderby('tipo_ex.orden_plano')
            ->get();

        array_push($arr_plano, $archivo_plano_4a);

        $cob_compar = 6; //ISSPOL-ACTIVOS
        $archivo_plano_5 = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->where('archivo_plano_cabecera.id_seguro', $seguro)
            ->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            ->where('archivo_plano_cabecera.estado', '1')
            ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd', 'apd.id_ap_cabecera', 'archivo_plano_cabecera.id')
            ->join('ap_tipo_examen as tipo_ex', 'tipo_ex.tipo', 'apd.tipo')
            ->where('apd.estado', '1')
            ->where('tseg.tipo_principal', 'ACTIVO')
            ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.valor_unitario', 'apd.subtotal', 'archivo_plano_cabecera.id as id_cab')
            ->orderby('full_name')
            ->orderby('archivo_plano_cabecera.fecha_ing')
            ->orderby('tipo_ex.orden_plano')
            ->get();

        array_push($arr_plano, $archivo_plano_5);

        $cob_compar = 6; //ISSPOL-JUBILADO
        $archivo_plano_6 = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->where('archivo_plano_cabecera.id_seguro', $seguro)
            ->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            ->where('archivo_plano_cabecera.estado', '1')
            ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd', 'apd.id_ap_cabecera', 'archivo_plano_cabecera.id')
            ->join('ap_tipo_examen as tipo_ex', 'tipo_ex.tipo', 'apd.tipo')
            ->where('apd.estado', '1')
            ->where('tseg.tipo_principal', 'JUBILADO')
            ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.valor_unitario', 'apd.subtotal', 'archivo_plano_cabecera.id as id_cab')
            ->orderby('archivo_plano_cabecera.id_tipo_seguro')
            ->orderby('full_name')
            ->orderby('archivo_plano_cabecera.fecha_ing')
            ->orderby('tipo_ex.orden_plano')
            ->get();

        array_push($arr_plano, $archivo_plano_6);

        $cob_compar = 6; //ISSPOL-MONTEPIO
        $archivo_plano_6a = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->where('archivo_plano_cabecera.id_seguro', $seguro)
            ->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            ->where('archivo_plano_cabecera.estado', '1')
            ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd', 'apd.id_ap_cabecera', 'archivo_plano_cabecera.id')
            ->join('ap_tipo_examen as tipo_ex', 'tipo_ex.tipo', 'apd.tipo')
            ->where('apd.estado', '1')
            ->where('tseg.tipo_principal', 'MONTEPIO')
            ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.valor_unitario', 'apd.subtotal', 'archivo_plano_cabecera.id as id_cab')
            ->orderby('archivo_plano_cabecera.id_tipo_seguro')
            ->orderby('full_name')
            ->orderby('archivo_plano_cabecera.fecha_ing')
            ->orderby('tipo_ex.orden_plano')
            ->get();

        array_push($arr_plano, $archivo_plano_6a);

        //dd($arr_plano);

        Excel::create('Archivo Plano General ', function ($excel) use ($archivo_plano_1, $archivo_plano_2, $archivo_plano_3, $archivo_plano_4, $archivo_plano_5, $archivo_plano_6, $caract_remplazar_1, $caract_remplazar_2, $texto_apellido_1, $texto_apellido_2, $texto_final, $texto_fin, $texto_nombre_1, $texto_nombre_2, $arr_plano, $mes_plano) {
            $excel->sheet(date('Y-m-d'), function ($sheet) use ($archivo_plano_1, $archivo_plano_2, $archivo_plano_3, $archivo_plano_4, $archivo_plano_5, $archivo_plano_6, $caract_remplazar_1, $caract_remplazar_2, $texto_apellido_1, $texto_apellido_2, $texto_final, $texto_fin, $texto_nombre_1, $texto_nombre_2, $arr_plano, $mes_plano) {

                $i = 1;
                $x = 0;
                $id_temporal = 0;

                $cantidad_letras = strlen($mes_plano);
                if ($cantidad_letras > 5) {
                    $anio = substr($mes_plano, '2');
                } else {
                    $anio = substr($mes_plano, '1');
                }
                //dd($anio);

                foreach ($arr_plano as $archivo_plano) {

                    foreach ($archivo_plano as $value) {

                        $tipo_examen = '';
                        $ap_tipo_examen = Ap_Tipo_Examen::where('tipo', $value->tipo)->first();
                        if (!is_null($ap_tipo_examen)) {
                            $tipo_examen = $ap_tipo_examen->tipo_ex;
                        }
                        if ($value->id_paciente != $id_temporal) {
                            $id_temporal = $value->id_paciente;
                            $x++;
                        }

                        $sheet->cell('A' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('515');
                        });

                        $sheet->cell('B' . $i, function ($cell) use ($value, $x) {
                            // manipulate the cel
                            $cell->setValue($x);
                        });
                        $pc = Archivo_Plano_Cabecera::find($value->id_cab);
                        if ($pc->numero_paciente != null or $pc->numero_paciente != $x) {
                            $pc->numero_paciente = $x;
                            $pc->save();
                        }

                        $fecha_ing     = substr($value->fecha_ing, 0, 10);
                        $fecha_ing_inv = date("d/m/Y", strtotime($fecha_ing));

                        $sheet->cell('C' . $i, function ($cell) use ($fecha_ing_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_ing_inv);
                        });

                        $sheet->cell('D' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->tiposeg);
                        });
                        $sheet->cell('E' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_paciente);
                        });
                        $sheet->cell('F' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2, $texto_apellido_1, $texto_apellido_2, $texto_final, $texto_fin, $texto_nombre_1, $texto_nombre_2) {
                            // manipulate the cel
                            //$cell->setValue($value->paciente->apellido1.' '.$value->paciente->apellido2.' '.$value->paciente->nombre1.' '.$value->paciente->nombre2);
                            /*$caract_remplazar = array("¨","$","%","&","/","(",")","#","@", "|", "!","/","+","-","}", "{", ">", "< ", ";", ",", ":",
                            ".","*",">","<","[","]","?","¿","º","~");*/
                            if ($value->apellido1 != 'N/A' && $value->apellido1 != '(N/A)') {
                                $texto_apellido_1 = $value->apellido1;
                            }

                            if ($value->apellido2 != 'N/A' && $value->apellido2 != '(N/A)') {
                                $texto_apellido_2 = $value->apellido2;
                            }

                            if ($value->nombre1 != 'N/A' && $value->nombre1 != '(N/A)') {
                                $texto_nombre_1 = $value->nombre1;
                            }

                            if ($value->nombre2 != 'N/A' && $value->nombre2 != '(N/A)') {
                                $texto_nombre_2 = $value->nombre2;
                            }

                            $texto_final = $texto_apellido_1 . ' ' . $texto_apellido_2 . ' ' . $texto_nombre_1 . ' ' . $texto_nombre_2;

                            if ($value->apellido2 == '(N/A)' || $value->apellido2 == ('N/A') || $value->apellido2 == ".") {
                                $texto_final = $texto_apellido_1 . ' ' . $texto_nombre_1 . ' ' . $texto_nombre_2;
                            }

                            $texto_fin = str_replace($caract_remplazar_1, "", $texto_final);
                            $texto_fin = str_replace($caract_remplazar_2, "N", $texto_fin);
                            $cell->setValue($texto_fin);

                            /*$cell->setValue($value->apellido1.' '.$value->apellido2.' '.$value->nombre1.' '.$value->nombre2);*/
                        });
                        $sheet->cell('G' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->paciente->sexo == 1) {
                                $cell->setValue('M');
                            } elseif ($value->paciente->sexo == 2) {
                                $cell->setValue('F');
                            }
                        });

                        $fecha_nac     = substr($value->paciente->fecha_nacimiento, 0, 10);
                        $fecha_nac_inv = date("d/m/Y", strtotime($fecha_nac));

                        $sheet->cell('H' . $i, function ($cell) use ($fecha_nac_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_nac_inv);
                        });
                        if ($value->paciente->fecha_nacimiento == null) {
                            $edad = 0;
                        } else {
                            $edad = Carbon::createFromDate(substr($value->paciente->fecha_nacimiento, 0, 4), substr($value->paciente->fecha_nacimiento, 5, 2), substr($value->paciente->fecha_nacimiento, 8, 2))->age;
                            if ($anio == '2023') {
                                $edad = $edad - 1;
                            }
                        }
                        $sheet->cell('I' . $i, function ($cell) use ($value, $edad) {
                            // manipulate the cel
                            $cell->setValue($edad);
                        });
                        $sheet->cell('J' . $i, function ($cell) use ($value, $tipo_examen) {
                            // manipulate the cel
                            if ($value->tipo == 'P' || $value->tipo == 'AN' || $value->tipo == 'TA') {
                                $cell->setValue('HME');
                            } elseif ($value->tipo == 'M') {
                                $cell->setValue('FAR');
                            } elseif ($value->tipo == 'S') {
                                $cell->setValue('HOSP/QUIR');
                            } elseif ($value->tipo == 'IM') {
                                $cell->setValue('IMA');
                            } elseif ($value->tipo == 'EX') {
                                $cell->setValue('LAB');
                            } elseif ($value->tipo == 'I' || $value->tipo == 'IV' || $value->tipo == 'IF' || $value->tipo == 'IVC' || $tipo_examen == 'IMM') {
                                $cell->setValue('IMM');
                            } elseif ($value->tipo == 'PA') {
                                $cell->setValue('PAQUE');
                            } elseif ($value->tipo == 'EQ') {
                                $cell->setValue('PRO/ESP');
                            }
                        });
                        $sheet->cell('K' . $i, function ($cell) use ($value, $tipo_examen) {
                            // manipulate the cel
                            if ($value->tipo == 'IV' || $value->tipo == 'I' || $value->tipo == 'M' || $value->tipo == 'IVC' || $tipo_examen == 'IMM') {
                                $cell->setValue(' ');
                            } else {
                                $cell->setValue($value->codigo);
                            }
                        });
                        $sheet->cell('L' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2, $tipo_examen) {
                            // manipulate the

                            $descrip = str_replace($caract_remplazar_1, "", $value->descripcion);
                            $descrip = str_replace($caract_remplazar_2, "N", $descrip);

                            /*if ($value->tipo == 'IV' || $value->tipo == 'I' || $value->tipo == 'M' || $value->tipo == 'IVC' || $tipo_examen == 'IMM') {
                                //$cell->setValue($value->descripcion);
                                $cell->setValue($descrip);

                            } else {

                                $cell->setValue('');

                            }*/
                            $cell->setValue($descrip);
                        });
                        $sheet->cell('M' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->cie10);
                        });
                        $sheet->cell('N' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');
                        });
                        $sheet->cell('O' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');
                        });
                        $sheet->cell('P' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->cantidad);
                        });
                        $sheet->cell('Q' . $i, function ($cell) use ($value) {
                            // manipulate the cel

                            //$cell->setValue(round($value->valor+$value->porcentaje10,2));
                            $cell->setValue(round($value->valor, 2));
                        });
                        $sheet->cell('R' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('0');
                        });
                        $sheet->cell('S' . $i, function ($cell) use ($value) {
                            // manipulate the cel

                            if ($value->parentesco == 'TITULAR') {
                                $cell->setValue('T');
                            } elseif ($value->parentesco == 'CONYUGE') {
                                $cell->setValue('C');
                            } elseif ($value->parentesco == 'HIJO/HIJA') {
                                $cell->setValue('H');
                            } elseif ($value->parentesco == 'PARIENTE') {
                                $cell->setValue('X');
                            }

                            //$cell->setValue($value->parentesco);

                        });
                        $sheet->cell('T' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_usuario);
                        });
                        $sheet->cell('U' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2) {
                            // manipulate the cel

                            $descrip_nombre = str_replace($caract_remplazar_1, "", $value->nombres);
                            $descrip_nombre = str_replace($caract_remplazar_2, "N", $descrip_nombre);

                            $cell->setValue($descrip_nombre);
                        });
                        $sheet->cell('V' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('CVAF');
                        });
                        $sheet->cell('W' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('1');
                        });
                        $sheet->cell('X' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('1');
                        });
                        $sheet->cell('Y' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('D');
                        });
                        $sheet->cell('Z' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('0');
                        });
                        $sheet->cell('AA' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');
                        });
                        $sheet->cell('AB' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');
                        });
                        $sheet->cell('AC' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');
                        });
                        $sheet->cell('AD' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->porcentaje_iva);
                        });
                        $sheet->cell('AE' . $i, function ($cell) use ($value, $tipo_examen) {
                            // manipulate the cel
                            //$cell->setValue(round($value->iva,2));
                            //$nuevo_iva = $value->cantidad*$value->valor_unitario*$value->porcentaje_iva;
                            if ($tipo_examen == 'IMM') {
                                $nuevo_iva = $value->subtotal * $value->porcentaje_iva;
                                $nuevo_iva = $nuevo_iva / $value->cantidad;
                            } else {
                                $nuevo_iva = $value->valor_unitario * $value->porcentaje_iva;
                            }
                            $cell->setValue(round($nuevo_iva, 4));
                        });
                        $sheet->cell('AF' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('F');
                        });
                        $i++;
                    }
                    $i = $i;
                }
            });

            $excel->getActiveSheet()->getColumnDimension("A")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("B")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("C")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("D")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("E")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("F")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("G")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("H")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("I")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("J")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("K")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("L")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("M")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("N")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("O")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("P")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("Q")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("R")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("S")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("T")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("U")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("V")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("W")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("X")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("Y")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("Z")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AA")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AB")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AC")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AD")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AE")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AF")->setWidth(11)->setAutosize(false);
        })->export('xlsx');
    }

    public function genera_ap_excel_jubilado_ssc(Request $request)
    {
        //ini_set('memory_limit', '1024M');
        $mes_plano = $request['mes_plano'];
        $seguro     = $request->seguro;
        $tipo_seg   = $request->id_tipo_seguro;
        //$cob_compar = $request['id_cobertura_comp'];
        $empresa    = $request->id_empresa;

        $archivo_plano_1 = [];
        $archivo_plano_2 = [];
        $archivo_plano_3 = [];
        $archivo_plano_4 = [];
        $archivo_plano_5 = [];
        $archivo_plano   = [];

        $texto_apellido_1 = null;
        $texto_apellido_2 = null;
        $texto_final      = null;
        $texto_fin        = null;
        $texto_nombre_1   = null;
        $texto_nombre_2   = null;

        $caract_remplazar_1 = array("¨", "$", "%", "&", "/", "(", ")", "#", "@", "|", "!", "/", "+", "-", "}", "{", ">", "< ", ";", ",", ":",
            ".", "*", ">", "<", "[", "]", "?", "¿", "º", "~");

        $caract_remplazar_2 = array("Ñ", "ñ");

        //SSC
        $cob_compar = null;
        $archivo_plano_1 = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
                ->whereIn('archivo_plano_cabecera.id_tipo_seguro',['9'])
                ->where('archivo_plano_cabecera.id_seguro', $seguro)
                //->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
                ->where('archivo_plano_cabecera.id_empresa', $empresa)
                ->where('archivo_plano_cabecera.estado', '1')
                ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
                ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
                ->join('archivo_plano_detalle as apd', 'apd.id_ap_cabecera', 'archivo_plano_cabecera.id')
                ->join('ap_tipo_examen as tipo_ex', 'tipo_ex.tipo', 'apd.tipo')
                ->where('apd.estado', '1')
                ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name') , 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.valor_unitario', 'apd.subtotal', 'apd.iva_unitario')
                ->orderby('archivo_plano_cabecera.id_tipo_seguro')
                ->orderby('full_name')
                ->orderby('archivo_plano_cabecera.fecha_ing')
                ->orderby('tipo_ex.orden_plano')
                ->get();
        $cont = $archivo_plano_1->count(); 
        
        $cob_compar = null;//JUBILADO CAMPESINO
        $archivo_plano_2 = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
                ->whereIn('archivo_plano_cabecera.id_tipo_seguro',['7'])
                ->where('archivo_plano_cabecera.id_seguro', $seguro)
                //->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
                ->where('archivo_plano_cabecera.id_empresa', $empresa)
                ->where('archivo_plano_cabecera.estado', '1')
                ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
                ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
                ->join('archivo_plano_detalle as apd', 'apd.id_ap_cabecera', 'archivo_plano_cabecera.id')
                ->join('ap_tipo_examen as tipo_ex', 'tipo_ex.tipo', 'apd.tipo')
                ->where('apd.estado', '1')
                ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name') , 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.valor_unitario', 'apd.subtotal', 'apd.iva_unitario')
                ->orderby('archivo_plano_cabecera.id_tipo_seguro')
                ->orderby('full_name')
                ->orderby('archivo_plano_cabecera.fecha_ing')
                ->orderby('tipo_ex.orden_plano')
                ->get();
        $cont_2 = $archivo_plano_2->count(); 

        // $cob_compar = 3;//ISSFA-JC
        // $archivo_plano_3 = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
        //         ->whereIn('archivo_plano_cabecera.id_tipo_seguro',['9'])
        //         ->where('archivo_plano_cabecera.id_seguro', $seguro)
        //         ->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
        //         ->where('archivo_plano_cabecera.id_empresa', $empresa)
        //         ->where('archivo_plano_cabecera.estado', '1')
        //         ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
        //         ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
        //         ->join('archivo_plano_detalle as apd', 'apd.id_ap_cabecera', 'archivo_plano_cabecera.id')
        //         ->join('ap_tipo_examen as tipo_ex', 'tipo_ex.tipo', 'apd.tipo')
        //         ->where('apd.estado', '1')
        //         ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name') , 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.valor_unitario')
        //         //->orderby('archivo_plano_cabecera.id_tipo_seguro')
        //         ->orderby('full_name')
        //         ->orderby('archivo_plano_cabecera.fecha_ing')
        //         ->orderby('tipo_ex.orden_plano')
        //         ->get();
        // $cont_issfa_1 = $archivo_plano_3->count(); 

        // $cob_compar = 3;//ISSFA-SSC
        // $archivo_plano_4 = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
        //         ->whereIn('archivo_plano_cabecera.id_tipo_seguro',['7'])
        //         ->where('archivo_plano_cabecera.id_seguro', $seguro)
        //         ->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
        //         ->where('archivo_plano_cabecera.id_empresa', $empresa)
        //         ->where('archivo_plano_cabecera.estado', '1')
        //         ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
        //         ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
        //         ->join('archivo_plano_detalle as apd', 'apd.id_ap_cabecera', 'archivo_plano_cabecera.id')
        //         ->join('ap_tipo_examen as tipo_ex', 'tipo_ex.tipo', 'apd.tipo')
        //         ->where('apd.estado', '1')
        //         ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name') , 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.valor_unitario')
        //         ->orderby('archivo_plano_cabecera.id_tipo_seguro')
        //         ->orderby('full_name')
        //         ->orderby('archivo_plano_cabecera.fecha_ing')
        //         ->orderby('tipo_ex.orden_plano')
        //         ->get();
        // $cont_issfa_2 = $archivo_plano_4->count(); 

        // $cob_compar = 6;//ISSPOL-JC
        // $archivo_plano_5 = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
        //         ->whereIn('archivo_plano_cabecera.id_tipo_seguro',['9'])
        //         ->where('archivo_plano_cabecera.id_seguro', $seguro)
        //         ->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
        //         ->where('archivo_plano_cabecera.id_empresa', $empresa)
        //         ->where('archivo_plano_cabecera.estado', '1')
        //         ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
        //         ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
        //         ->join('archivo_plano_detalle as apd', 'apd.id_ap_cabecera', 'archivo_plano_cabecera.id')
        //         ->join('ap_tipo_examen as tipo_ex', 'tipo_ex.tipo', 'apd.tipo')
        //         ->where('apd.estado', '1')
        //         ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name') , 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.valor_unitario')
        //         //->orderby('archivo_plano_cabecera.id_tipo_seguro')
        //         ->orderby('full_name')
        //         ->orderby('archivo_plano_cabecera.fecha_ing')
        //         ->orderby('tipo_ex.orden_plano')
        //         ->get();
        // $cont_isspol_1 = $archivo_plano_5->count();

        // $cob_compar = 6;//ISSPOL-SSC
        // $archivo_plano_6 = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
        //         ->whereIn('archivo_plano_cabecera.id_tipo_seguro',['7'])
        //         ->where('archivo_plano_cabecera.id_seguro', $seguro)
        //         ->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
        //         ->where('archivo_plano_cabecera.id_empresa', $empresa)
        //         ->where('archivo_plano_cabecera.estado', '1')
        //         ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
        //         ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
        //         ->join('archivo_plano_detalle as apd', 'apd.id_ap_cabecera', 'archivo_plano_cabecera.id')
        //         ->join('ap_tipo_examen as tipo_ex', 'tipo_ex.tipo', 'apd.tipo')
        //         ->where('apd.estado', '1')
        //         ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name') , 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.valor_unitario')
        //         ->orderby('archivo_plano_cabecera.id_tipo_seguro')
        //         ->orderby('full_name')
        //         ->orderby('archivo_plano_cabecera.fecha_ing')
        //         ->orderby('tipo_ex.orden_plano')
        //         ->get();

        // $cont_isspol2 = $archivo_plano_6->count();

        Excel::create('Archivo Plano SSC', function ($excel) use ($archivo_plano_1, $archivo_plano_2, $archivo_plano_3, $archivo_plano_4, $archivo_plano_5,  $cont, $cont_2, $caract_remplazar_1, $caract_remplazar_2, $texto_apellido_1, $texto_apellido_2, $texto_final, $texto_fin, $texto_nombre_1, $texto_nombre_2) {//$archivo_plano_6,$cont_issfa_1, $cont_issfa_2, $cont_isspol_1, $cont_isspol2,
            $excel->sheet(date('Y-m-d'), function ($sheet) use ($archivo_plano_1, $archivo_plano_2, $archivo_plano_3, $archivo_plano_4, $archivo_plano_5, $cont,$cont_2, $caract_remplazar_1, $caract_remplazar_2, $texto_apellido_1, $texto_apellido_2, $texto_final, $texto_fin, $texto_nombre_1, $texto_nombre_2) {//$archivo_plano_6,$cont_issfa_1, $cont_issfa_2, $cont_isspol_1, $cont_isspol2,

                $i = 1; $x = 0; $id_temporal = 0;

                if (!is_null($archivo_plano_1)) {

                    foreach ($archivo_plano_1 as $value) {

                        $tipo_examen = '';
                        $ap_tipo_examen = Ap_Tipo_Examen::where('tipo',$value->tipo)->first();
                        if(!is_null($ap_tipo_examen)){
                            $tipo_examen = $ap_tipo_examen->tipo_ex;
                        }

                        if ($value->id_paciente != $id_temporal) {
                            $id_temporal = $value->id_paciente;
                            $x++;
                        }

                        $sheet->cell('A' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('515');
                        });

                        $sheet->cell('B' . $i, function ($cell) use ($value, $x) {
                            // manipulate the cel
                            $cell->setValue($x);
                        });

                        $fecha_ing     = substr($value->fecha_ing, 0, 10);
                        $fecha_ing_inv = date("d/m/Y", strtotime($fecha_ing));

                        $sheet->cell('C' . $i, function ($cell) use ($fecha_ing_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_ing_inv);
                        });

                        $sheet->cell('D' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->tiposeg);
                        });
                        $sheet->cell('E' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_paciente);

                        });
                        $sheet->cell('F' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2, $texto_apellido_1, $texto_apellido_2, $texto_final, $texto_fin, $texto_nombre_1, $texto_nombre_2) {
                            // manipulate the cel
                            //$cell->setValue($value->paciente->apellido1.' '.$value->paciente->apellido2.' '.$value->paciente->nombre1.' '.$value->paciente->nombre2);
                            /*$caract_remplazar = array("¨","$","%","&","/","(",")","#","@", "|", "!","/","+","-","}", "{", ">", "< ", ";", ",", ":",
                            ".","*",">","<","[","]","?","¿","º","~");*/
                            if ($value->apellido1 != 'N/A' && $value->apellido1 != '(N/A)') {
                                $texto_apellido_1 = $value->apellido1;
                            }

                            if ($value->apellido2 != 'N/A' && $value->apellido2 != '(N/A)') {
                                $texto_apellido_2 = $value->apellido2;
                            }

                            if ($value->nombre1 != 'N/A' && $value->nombre1 != '(N/A)') {
                                $texto_nombre_1 = $value->nombre1;
                            }

                            if ($value->nombre2 != 'N/A' && $value->nombre2 != '(N/A)') {
                                $texto_nombre_2 = $value->nombre2;
                            }

                            $texto_final = $texto_apellido_1 . ' ' . $texto_apellido_2 . ' ' . $texto_nombre_1 . ' ' . $texto_nombre_2;

                            if($value->apellido2 == '(N/A)' || $value->apellido2 == ('N/A') || $value->apellido2 == "."){
                                $texto_final = $texto_apellido_1 . ' ' . $texto_nombre_1 . ' ' . $texto_nombre_2;
                            }

                            $texto_fin = str_replace($caract_remplazar_1, "", $texto_final);
                            $texto_fin = str_replace($caract_remplazar_2, "N", $texto_fin);
                            $cell->setValue($texto_fin);

                            /*$cell->setValue($value->apellido1.' '.$value->apellido2.' '.$value->nombre1.' '.$value->nombre2);*/

                        });
                        $sheet->cell('G' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->paciente->sexo == 1) {
                                $cell->setValue('M');
                            } elseif ($value->paciente->sexo == 2) {
                                $cell->setValue('F');
                            }

                        });

                        $fecha_nac     = substr($value->paciente->fecha_nacimiento, 0, 10);
                        $fecha_nac_inv = date("d/m/Y", strtotime($fecha_nac));

                        $sheet->cell('H' . $i, function ($cell) use ($fecha_nac_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_nac_inv);

                        });
                        if ($value->paciente->fecha_nacimiento == null) {
                            $edad = 0;
                        } else {
                            $edad = Carbon::createFromDate(substr($value->paciente->fecha_nacimiento, 0, 4), substr($value->paciente->fecha_nacimiento, 5, 2), substr($value->paciente->fecha_nacimiento, 8, 2))->age;
                        }
                        $sheet->cell('I' . $i, function ($cell) use ($value, $edad) {
                            // manipulate the cel
                            $cell->setValue($edad);

                        });
                        $sheet->cell('J' . $i, function ($cell) use ($value, $tipo_examen) {
                            // manipulate the cel
                            if ($value->tipo == 'P' || $value->tipo == 'AN' || $value->tipo == 'TA') {
                                $cell->setValue('HME');

                            } elseif ($value->tipo == 'M') {
                                $cell->setValue('FAR');

                            } elseif ($value->tipo == 'S') {
                                $cell->setValue('HOSP/QUIR');

                            } elseif ($value->tipo == 'IM') {
                                $cell->setValue('IMA');

                            } elseif ($value->tipo == 'EX') {
                                $cell->setValue('LAB');

                            } elseif ($value->tipo == 'I' || $value->tipo == 'IV' || $value->tipo == 'IF' || $value->tipo == 'IVC' || $tipo_examen == 'IMM') {
                                $cell->setValue('IMM');

                            } elseif ($value->tipo == 'PA') {
                                $cell->setValue('PAQUE');

                            } elseif ($value->tipo == 'EQ') {
                                $cell->setValue('PRO/ESP');

                            }

                        });
                        $sheet->cell('K' . $i, function ($cell) use ($value, $tipo_examen) {
                            // manipulate the cel
                            if ($value->tipo == 'IV' || $value->tipo == 'I' || $value->tipo == 'M' || $value->tipo == 'IVC' || $tipo_examen == 'IMM') {
                                $cell->setValue(' ');
                            } else {
                                $cell->setValue($value->codigo);
                            }

                        });
                        $sheet->cell('L' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2, $tipo_examen) {
                            // manipulate the

                            $descrip = str_replace($caract_remplazar_1, "", $value->descripcion);
                            $descrip = str_replace($caract_remplazar_2, "N", $descrip);

                            if ($value->tipo == 'IV' || $value->tipo == 'I' || $value->tipo == 'M' || $value->tipo == 'IVC' || $tipo_examen == 'IMM') {
                                //$cell->setValue($value->descripcion);
                                $cell->setValue($descrip);

                            } else {

                                $cell->setValue('');

                            }
                        });
                        $sheet->cell('M' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->cie10);

                        });
                        $sheet->cell('N' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('O' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('P' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->cantidad);

                        });
                        $sheet->cell('Q' . $i, function ($cell) use ($value) {
                            // manipulate the cel

                            //$cell->setValue(round($value->valor+$value->porcentaje10,2));
                            $cell->setValue(round($value->valor, 4));

                        });
                        $sheet->cell('R' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('0');

                        });
                        $sheet->cell('S' . $i, function ($cell) use ($value) {
                            // manipulate the cel

                            if ($value->parentesco == 'TITULAR') {
                                $cell->setValue('T');

                            } elseif ($value->parentesco == 'CONYUGE') {
                                $cell->setValue('C');

                            } elseif ($value->parentesco == 'HIJO/HIJA') {
                                $cell->setValue('H');

                            } elseif ($value->parentesco == 'PARIENTE') {
                                $cell->setValue('X');

                            }

                            //$cell->setValue($value->parentesco);

                        });
                        $sheet->cell('T' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_usuario);
                        });
                        $sheet->cell('U' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2) {
                            // manipulate the cel

                            $descrip_nombre = str_replace($caract_remplazar_1, "", $value->nombres);
                            $descrip_nombre = str_replace($caract_remplazar_2, "N", $descrip_nombre);

                            $cell->setValue($descrip_nombre);

                        });
                        $sheet->cell('V' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('CVAF');

                        });
                        $sheet->cell('W' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('1');

                        });
                        $sheet->cell('X' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('1');

                        });
                        $sheet->cell('Y' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('D');

                        });
                        $sheet->cell('Z' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('0');

                        });
                        $sheet->cell('AA' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AB' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AC' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AD' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->porcentaje_iva);

                        });
                        $sheet->cell('AE' . $i, function ($cell) use ($value, $tipo_examen) {
                            // manipulate the cel
                            //$nuevo_iva = $value->cantidad*$value->valor_unitario*$value->porcentaje_iva;
                            if ($tipo_examen == 'IMM') {
                                //$nuevo_iva = $value->subtotal * $value->porcentaje_iva;
                                //$nuevo_iva = $nuevo_iva / $value->cantidad;
                                $nuevo_iva = $value->iva_unitario;
                            }else{
                                $nuevo_iva = $value->valor_unitario * $value->porcentaje_iva;
                            }
                            $cell->setValue(round($nuevo_iva, 4));

                        });
                        $sheet->cell('AF' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('F');

                        });
                        $i++;
                    }
                    $i = $i;

                }

                if (!is_null($archivo_plano_2)) {
                    foreach ($archivo_plano_2 as $value) {

                        $tipo_examen = '';
                        $ap_tipo_examen = Ap_Tipo_Examen::where('tipo',$value->tipo)->first();
                        if(!is_null($ap_tipo_examen)){
                            $tipo_examen = $ap_tipo_examen->tipo_ex;
                        }

                        if ($value->id_paciente != $id_temporal) {
                            $id_temporal = $value->id_paciente;
                            $x++;
                        }

                        $sheet->cell('A' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('515');
                        });

                        $sheet->cell('B' . $i, function ($cell) use ($value, $x) {
                            // manipulate the cel
                            $cell->setValue($x);
                        });

                        $fecha_ing     = substr($value->fecha_ing, 0, 10);
                        $fecha_ing_inv = date("d/m/Y", strtotime($fecha_ing));

                        $sheet->cell('C' . $i, function ($cell) use ($fecha_ing_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_ing_inv);
                        });

                        $sheet->cell('D' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->tiposeg);
                        });
                        $sheet->cell('E' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_paciente);

                        });
                        $sheet->cell('F' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2, $texto_apellido_1, $texto_apellido_2, $texto_final, $texto_fin, $texto_nombre_1, $texto_nombre_2) {
                            // manipulate the cel
                            //$cell->setValue($value->paciente->apellido1.' '.$value->paciente->apellido2.' '.$value->paciente->nombre1.' '.$value->paciente->nombre2);
                            /*$cell->setValue($value->apellido1.' '.$value->apellido2.' '.$value->nombre1.' '.$value->nombre2);*/

                            if ($value->apellido1 != 'N/A' && $value->apellido1 != '(N/A)') {
                                $texto_apellido_1 = $value->apellido1;
                            }

                            if ($value->apellido2 != 'N/A' && $value->apellido2 != '(N/A)') {
                                $texto_apellido_2 = $value->apellido2;
                            }

                            if ($value->nombre1 != 'N/A' && $value->nombre1 != '(N/A)') {
                                $texto_nombre_1 = $value->nombre1;
                            }

                            if ($value->nombre2 != 'N/A' && $value->nombre2 != '(N/A)') {
                                $texto_nombre_2 = $value->nombre2;
                            }

                            //$texto_final = $texto_apellido_1.' '.$texto_apellido_2.' '. $value->nombre1.' '.$value->nombre2;

                            $texto_final = $texto_apellido_1 . ' ' . $texto_apellido_2 . ' ' . $texto_nombre_1 . ' ' . $texto_nombre_2;

                            if($value->apellido2 == '(N/A)' || $value->apellido2 == ('N/A') || $value->apellido2 == "."){
                                $texto_final = $texto_apellido_1 . ' ' . $texto_nombre_1 . ' ' . $texto_nombre_2;
                            }

                            $texto_fin = str_replace($caract_remplazar_1, "", $texto_final);
                            $texto_fin = str_replace($caract_remplazar_2, "N", $texto_fin);
                            $cell->setValue($texto_fin);

                        });
                        $sheet->cell('G' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->paciente->sexo == 1) {
                                $cell->setValue('M');
                            } elseif ($value->paciente->sexo == 2) {
                                $cell->setValue('F');
                            }

                        });

                        $fecha_nac     = substr($value->paciente->fecha_nacimiento, 0, 10);
                        $fecha_nac_inv = date("d/m/Y", strtotime($fecha_nac));

                        $sheet->cell('H' . $i, function ($cell) use ($fecha_nac_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_nac_inv);

                        });
                        if ($value->paciente->fecha_nacimiento == null) {
                            $edad = 0;
                        } else {
                            $edad = Carbon::createFromDate(substr($value->paciente->fecha_nacimiento, 0, 4), substr($value->paciente->fecha_nacimiento, 5, 2), substr($value->paciente->fecha_nacimiento, 8, 2))->age;
                        }
                        $sheet->cell('I' . $i, function ($cell) use ($value, $edad) {
                            // manipulate the cel
                            $cell->setValue($edad);

                        });
                        $sheet->cell('J' . $i, function ($cell) use ($value , $tipo_examen) {
                            // manipulate the cel
                            if ($value->tipo == 'P' || $value->tipo == 'AN' || $value->tipo == 'TA') {
                                $cell->setValue('HME');

                            } elseif ($value->tipo == 'M') {
                                $cell->setValue('FAR');

                            } elseif ($value->tipo == 'S') {
                                $cell->setValue('HOSP/QUIR');

                            } elseif ($value->tipo == 'IM') {
                                $cell->setValue('IMA');

                            } elseif ($value->tipo == 'EX') {
                                $cell->setValue('LAB');

                            } elseif ($value->tipo == 'I' || $value->tipo == 'IV' || $value->tipo == 'IF' || $value->tipo == 'IVC' || $tipo_examen == 'IMM') {
                                $cell->setValue('IMM');

                            } elseif ($value->tipo == 'PA') {
                                $cell->setValue('PAQUE');

                            } elseif ($value->tipo == 'EQ') {
                                $cell->setValue('PRO/ESP');

                            }

                        });
                        $sheet->cell('K' . $i, function ($cell) use ($value, $tipo_examen) {
                            // manipulate the cel
                            if ($value->tipo == 'IV' || $value->tipo == 'I' || $value->tipo == 'M' || $value->tipo == 'IVC' || $tipo_examen == 'IMM') {
                                $cell->setValue(' ');
                            } else {
                                $cell->setValue($value->codigo);
                            }

                        });
                        $sheet->cell('L' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2, $tipo_examen) {
                            // manipulate the cel

                            $descrip = str_replace($caract_remplazar_1, "", $value->descripcion);
                            $descrip = str_replace($caract_remplazar_2, "N", $descrip);

                            if ($value->tipo == 'IV' || $value->tipo == 'I' || $value->tipo == 'M' || $value->tipo == 'IVC' || $tipo_examen == 'IMM') {
                                //$cell->setValue($value->descripcion);
                                $cell->setValue($descrip);

                            } else {

                                $cell->setValue('');

                            }
                            //$cell->setValue($value->descripcion);

                        });
                        $sheet->cell('M' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->cie10);

                        });
                        $sheet->cell('N' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('O' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('P' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->cantidad);

                        });
                        $sheet->cell('Q' . $i, function ($cell) use ($value) {
                            // manipulate the cel

                            //$cell->setValue(round($value->valor+$value->porcentaje10,2));
                            $cell->setValue(round($value->valor, 4));

                        });
                        $sheet->cell('R' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('0');

                        });
                        $sheet->cell('S' . $i, function ($cell) use ($value) {
                            // manipulate the cel

                            if ($value->parentesco == 'TITULAR') {
                                $cell->setValue('T');

                            } elseif ($value->parentesco == 'CONYUGE') {
                                $cell->setValue('C');

                            } elseif ($value->parentesco == 'HIJO/HIJA') {
                                $cell->setValue('H');

                            } elseif ($value->parentesco == 'PARIENTE') {
                                $cell->setValue('X');

                            }

                            //$cell->setValue($value->parentesco);

                        });
                        $sheet->cell('T' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_usuario);
                        });
                        $sheet->cell('U' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2) {
                            // manipulate the cel
                            //$cell->setValue($value->nombres);
                            //$descrip_nombre = str_replace($caract_remplazar,"",$value->nombres);

                            $descrip_nombre = str_replace($caract_remplazar_1, "", $value->nombres);
                            $descrip_nombre = str_replace($caract_remplazar_2, "N", $descrip_nombre);

                            $cell->setValue($descrip_nombre);

                        });
                        $sheet->cell('V' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('CVAF');

                        });
                        $sheet->cell('W' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('1');

                        });
                        $sheet->cell('X' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('1');

                        });
                        $sheet->cell('Y' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('D');

                        });
                        $sheet->cell('Z' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('0');

                        });
                        $sheet->cell('AA' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AB' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AC' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AD' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->porcentaje_iva);

                        });
                        $sheet->cell('AE' . $i, function ($cell) use ($value, $tipo_examen) {
                            // manipulate the cel
                            //$nuevo_iva = $value->cantidad*$value->valor_unitario*$value->porcentaje_iva;
                            if ($tipo_examen == 'IMM') {
                                //$nuevo_iva = $value->subtotal * $value->porcentaje_iva;
                                //$nuevo_iva = $nuevo_iva / $value->cantidad;
                                $nuevo_iva = $value->iva_unitario;
                            }else{
                                $nuevo_iva = $value->valor_unitario * $value->porcentaje_iva;
                            }
                            $cell->setValue($nuevo_iva);

                        });
                        $sheet->cell('AF' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('F');

                        });
                        $i++;
                    }
                    $i = $i;

                }

                if (!is_null($archivo_plano_3)) {

                    foreach ($archivo_plano_3 as $value) {

                        $tipo_examen = '';
                        $ap_tipo_examen = Ap_Tipo_Examen::where('tipo',$value->tipo)->first();
                        if(!is_null($ap_tipo_examen)){
                            $tipo_examen = $ap_tipo_examen->tipo_ex;
                        }    
                        if ($value->id_paciente != $id_temporal) {
                            $id_temporal = $value->id_paciente;
                            $x++;
                        }

                        $sheet->cell('A' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('515');
                        });

                        $sheet->cell('B' . $i, function ($cell) use ($value, $x) {
                            // manipulate the cel
                            $cell->setValue($x);
                        });

                        $fecha_ing     = substr($value->fecha_ing, 0, 10);
                        $fecha_ing_inv = date("d/m/Y", strtotime($fecha_ing));

                        $sheet->cell('C' . $i, function ($cell) use ($fecha_ing_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_ing_inv);
                        });

                        $sheet->cell('D' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->tiposeg);
                        });
                        $sheet->cell('E' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_paciente);

                        });
                        $sheet->cell('F' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2, $texto_apellido_1, $texto_apellido_2, $texto_final, $texto_fin, $texto_nombre_1, $texto_nombre_2) {
                            // manipulate the cel
                            //$cell->setValue($value->paciente->apellido1.' '.$value->paciente->apellido2.' '.$value->paciente->nombre1.' '.$value->paciente->nombre2);
                            /*$cell->setValue($value->apellido1.' '.$value->apellido2.' '.$value->nombre1.' '.$value->nombre2);*/
                            if ($value->apellido1 != 'N/A' && $value->apellido1 != '(N/A)') {
                                $texto_apellido_1 = $value->apellido1;
                            }

                            if ($value->apellido2 != 'N/A' && $value->apellido2 != '(N/A)') {
                                $texto_apellido_2 = $value->apellido2;
                            }

                            if ($value->nombre1 != 'N/A' && $value->nombre1 != '(N/A)') {
                                $texto_nombre_1 = $value->nombre1;
                            }

                            if ($value->nombre2 != 'N/A' && $value->nombre2 != '(N/A)') {
                                $texto_nombre_2 = $value->nombre2;
                            }

                            $texto_final = $texto_apellido_1 . ' ' . $texto_apellido_2 . ' ' . $texto_nombre_1 . ' ' . $texto_nombre_2;

                            if($value->apellido2 == '(N/A)' || $value->apellido2 == ('N/A') || $value->apellido2 == "."){
                                $texto_final = $texto_apellido_1 . ' ' . $texto_nombre_1 . ' ' . $texto_nombre_2;
                            }

                            /*$texto_final = $texto_apellido_1.' '.$texto_apellido_2.' '. $value->nombre1.' '.$value->nombre2;*/

                            $texto_fin = str_replace($caract_remplazar_1, "", $texto_final);
                            $texto_fin = str_replace($caract_remplazar_2, "N", $texto_fin);
                            $cell->setValue($texto_fin);

                        });
                        $sheet->cell('G' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->paciente->sexo == 1) {
                                $cell->setValue('M');
                            } elseif ($value->paciente->sexo == 2) {
                                $cell->setValue('F');
                            }

                        });

                        $fecha_nac     = substr($value->paciente->fecha_nacimiento, 0, 10);
                        $fecha_nac_inv = date("d/m/Y", strtotime($fecha_nac));

                        $sheet->cell('H' . $i, function ($cell) use ($fecha_nac_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_nac_inv);

                        });
                        if ($value->paciente->fecha_nacimiento == null) {
                            $edad = 0;
                        } else {
                            $edad = Carbon::createFromDate(substr($value->paciente->fecha_nacimiento, 0, 4), substr($value->paciente->fecha_nacimiento, 5, 2), substr($value->paciente->fecha_nacimiento, 8, 2))->age;
                        }
                        $sheet->cell('I' . $i, function ($cell) use ($value, $edad) {
                            // manipulate the cel
                            $cell->setValue($edad);

                        });
                        $sheet->cell('J' . $i, function ($cell) use ($value,$tipo_examen) {
                            // manipulate the cel
                            if ($value->tipo == 'P' || $value->tipo == 'AN' || $value->tipo == 'TA') {
                                $cell->setValue('HME');

                            } elseif ($value->tipo == 'M') {
                                $cell->setValue('FAR');

                            } elseif ($value->tipo == 'S') {
                                $cell->setValue('HOSP/QUIR');

                            } elseif ($value->tipo == 'IM') {
                                $cell->setValue('IMA');

                            } elseif ($value->tipo == 'EX') {
                                $cell->setValue('LAB');

                            } elseif ($value->tipo == 'I' || $value->tipo == 'IV' || $value->tipo == 'IF' || $value->tipo == 'IVC' || $tipo_examen == 'IMM') {
                                $cell->setValue('IMM');

                            } elseif ($value->tipo == 'PA') {
                                $cell->setValue('PAQUE');

                            } elseif ($value->tipo == 'EQ') {
                                $cell->setValue('PRO/ESP');

                            }

                        });
                        $sheet->cell('K' . $i, function ($cell) use ($value,$tipo_examen) {
                            // manipulate the cel
                            if ($value->tipo == 'IV' || $value->tipo == 'I' || $value->tipo == 'M' || $value->tipo == 'IVC' || $tipo_examen == 'IMM') {
                                $cell->setValue(' ');
                            } else {
                                $cell->setValue($value->codigo);
                            }

                        });
                        $sheet->cell('L' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2,$tipo_examen) {
                            // manipulate the cel

                            //$cell->setValue($value->descripcion);
                            //$descrip = str_replace($caract_remplazar,"",$value->descripcion);
                            $descrip = str_replace($caract_remplazar_1, "", $value->descripcion);
                            $descrip = str_replace($caract_remplazar_2, "N", $descrip);

                            if ($value->tipo == 'IV' || $value->tipo == 'I' || $value->tipo == 'M' || $value->tipo == 'IVC' || $tipo_examen == 'IMM') {
                                //$cell->setValue($value->descripcion);
                                $cell->setValue($descrip);

                            } else {

                                $cell->setValue('');

                            }

                        });
                        $sheet->cell('M' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->cie10);

                        });
                        $sheet->cell('N' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('O' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('P' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->cantidad);

                        });
                        $sheet->cell('Q' . $i, function ($cell) use ($value) {
                            // manipulate the cel

                            //$cell->setValue(round($value->valor+$value->porcentaje10,2));
                            $cell->setValue(round($value->valor, 4));

                        });
                        $sheet->cell('R' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('0');

                        });
                        $sheet->cell('S' . $i, function ($cell) use ($value) {
                            // manipulate the cel

                            if ($value->parentesco == 'TITULAR') {
                                $cell->setValue('T');

                            } elseif ($value->parentesco == 'CONYUGE') {
                                $cell->setValue('C');

                            } elseif ($value->parentesco == 'HIJO/HIJA') {
                                $cell->setValue('H');

                            } elseif ($value->parentesco == 'PARIENTE') {
                                $cell->setValue('X');

                            }

                            //$cell->setValue($value->parentesco);

                        });
                        $sheet->cell('T' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_usuario);
                        });
                        $sheet->cell('U' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2) {
                            // manipulate the cel
                            //$cell->setValue($value->usuario->apellido1.' '.$value->usuario->apellido2.' '.$value->usuario->nombre1.' '.$value->usuario->nombre2);
                            //$cell->setValue($value->nombres);

                            //$descrip_nombre = str_replace($caract_remplazar,"",$value->nombres);
                            $descrip_nombre = str_replace($caract_remplazar_1, "", $value->nombres);
                            $descrip_nombre = str_replace($caract_remplazar_2, "N", $descrip_nombre);

                            $cell->setValue($descrip_nombre);

                        });
                        $sheet->cell('V' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('CVAF');

                        });
                        $sheet->cell('W' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('1');

                        });
                        $sheet->cell('X' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('1');

                        });
                        $sheet->cell('Y' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('D');

                        });
                        $sheet->cell('Z' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('0');

                        });
                        $sheet->cell('AA' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AB' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AC' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AD' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->porcentaje_iva);

                        });
                        $sheet->cell('AE' . $i, function ($cell) use ($value, $tipo_examen) {
                            // manipulate the cel
                            //$nuevo_iva = $value->cantidad*$value->valor_unitario*$value->porcentaje_iva;
                            if ($tipo_examen == 'IMM') {
                                //$nuevo_iva = $value->subtotal * $value->porcentaje_iva;
                                //$nuevo_iva = $nuevo_iva / $value->cantidad;
                                $nuevo_iva = $value->iva_unitario;
                            }else{
                                $nuevo_iva = $value->valor_unitario * $value->porcentaje_iva;
                            }
                            $cell->setValue(round($nuevo_iva, 4));

                        });
                        $sheet->cell('AF' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('F');

                        });
                        $i++;
                    }
                    $i = $i;

                }

                if (!is_null($archivo_plano_4)) {

                    foreach ($archivo_plano_4 as $value) {

                        $tipo_examen = '';
                        $ap_tipo_examen = Ap_Tipo_Examen::where('tipo',$value->tipo)->first();
                        if(!is_null($ap_tipo_examen)){
                            $tipo_examen = $ap_tipo_examen->tipo_ex;
                        }  
                        if ($value->id_paciente != $id_temporal) {
                            $id_temporal = $value->id_paciente;
                            $x++;
                        }

                        $sheet->cell('A' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('515');
                        });

                        $sheet->cell('B' . $i, function ($cell) use ($value, $x) {
                            // manipulate the cel
                            $cell->setValue($x);
                        });

                        $fecha_ing     = substr($value->fecha_ing, 0, 10);
                        $fecha_ing_inv = date("d/m/Y", strtotime($fecha_ing));

                        $sheet->cell('C' . $i, function ($cell) use ($fecha_ing_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_ing_inv);
                        });

                        $sheet->cell('D' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->tiposeg);
                        });
                        $sheet->cell('E' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_paciente);

                        });
                        $sheet->cell('F' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2, $texto_apellido_1, $texto_apellido_2, $texto_final, $texto_fin, $texto_nombre_1, $texto_nombre_2) {
                            // manipulate the cel
                            //$cell->setValue($value->paciente->apellido1.' '.$value->paciente->apellido2.' '.$value->paciente->nombre1.' '.$value->paciente->nombre2);
                            /*$cell->setValue($value->apellido1.' '.$value->apellido2.' '.$value->nombre1.' '.$value->nombre2);*/
                            if ($value->apellido1 != 'N/A' && $value->apellido1 != '(N/A)') {
                                $texto_apellido_1 = $value->apellido1;
                            }

                            if ($value->apellido2 != 'N/A' && $value->apellido2 != '(N/A)') {
                                $texto_apellido_2 = $value->apellido2;
                            }

                            if ($value->nombre1 != 'N/A' && $value->nombre1 != '(N/A)') {
                                $texto_nombre_1 = $value->nombre1;
                            }

                            if ($value->nombre2 != 'N/A' && $value->nombre2 != '(N/A)') {
                                $texto_nombre_2 = $value->nombre2;
                            }

                            $texto_final = $texto_apellido_1 . ' ' . $texto_apellido_2 . ' ' . $texto_nombre_1 . ' ' . $texto_nombre_2;

                            if($value->apellido2 == '(N/A)' || $value->apellido2 == ('N/A') || $value->apellido2 == "."){
                                $texto_final = $texto_apellido_1 . ' ' . $texto_nombre_1 . ' ' . $texto_nombre_2;
                            }

                            /*$texto_final = $texto_apellido_1.' '.$texto_apellido_2.' '. $value->nombre1.' '.$value->nombre2;*/

                            $texto_fin = str_replace($caract_remplazar_1, "", $texto_final);
                            $texto_fin = str_replace($caract_remplazar_2, "N", $texto_fin);
                            $cell->setValue($texto_fin);

                        });
                        $sheet->cell('G' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->paciente->sexo == 1) {
                                $cell->setValue('M');
                            } elseif ($value->paciente->sexo == 2) {
                                $cell->setValue('F');
                            }

                        });

                        $fecha_nac     = substr($value->paciente->fecha_nacimiento, 0, 10);
                        $fecha_nac_inv = date("d/m/Y", strtotime($fecha_nac));

                        $sheet->cell('H' . $i, function ($cell) use ($fecha_nac_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_nac_inv);

                        });
                        if ($value->paciente->fecha_nacimiento == null) {
                            $edad = 0;
                        } else {
                            $edad = Carbon::createFromDate(substr($value->paciente->fecha_nacimiento, 0, 4), substr($value->paciente->fecha_nacimiento, 5, 2), substr($value->paciente->fecha_nacimiento, 8, 2))->age;
                        }
                        $sheet->cell('I' . $i, function ($cell) use ($value, $edad) {
                            // manipulate the cel
                            $cell->setValue($edad);

                        });
                        $sheet->cell('J' . $i, function ($cell) use ($value,$tipo_examen) {
                            // manipulate the cel
                            if ($value->tipo == 'P' || $value->tipo == 'AN' || $value->tipo == 'TA') {
                                $cell->setValue('HME');

                            } elseif ($value->tipo == 'M') {
                                $cell->setValue('FAR');

                            } elseif ($value->tipo == 'S') {
                                $cell->setValue('HOSP/QUIR');

                            } elseif ($value->tipo == 'IM') {
                                $cell->setValue('IMA');

                            } elseif ($value->tipo == 'EX') {
                                $cell->setValue('LAB');

                            } elseif ($value->tipo == 'I' || $value->tipo == 'IV' || $value->tipo == 'IF' || $value->tipo == 'IVC' || $tipo_examen == 'IMM') {
                                $cell->setValue('IMM');

                            } elseif ($value->tipo == 'PA') {
                                $cell->setValue('PAQUE');

                            } elseif ($value->tipo == 'EQ') {
                                $cell->setValue('PRO/ESP');

                            }

                        });
                        $sheet->cell('K' . $i, function ($cell) use ($value,$tipo_examen) {
                            // manipulate the cel
                            if ($value->tipo == 'IV' || $value->tipo == 'I' || $value->tipo == 'M' || $value->tipo == 'IVC' || $tipo_examen == 'IMM') {
                                $cell->setValue(' ');
                            } else {
                                $cell->setValue($value->codigo);
                            }

                        });
                        $sheet->cell('L' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2,$tipo_examen) {
                            // manipulate the cel

                            //$cell->setValue($value->descripcion);
                            //$descrip = str_replace($caract_remplazar,"",$value->descripcion);
                            $descrip = str_replace($caract_remplazar_1, "", $value->descripcion);
                            $descrip = str_replace($caract_remplazar_2, "N", $descrip);

                            if ($value->tipo == 'IV' || $value->tipo == 'I' || $value->tipo == 'M' || $value->tipo == 'IVC' || $tipo_examen == 'IMM') {
                                //$cell->setValue($value->descripcion);
                                $cell->setValue($descrip);

                            } else {

                                $cell->setValue('');

                            }

                        });
                        $sheet->cell('M' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->cie10);

                        });
                        $sheet->cell('N' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('O' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('P' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->cantidad);

                        });
                        $sheet->cell('Q' . $i, function ($cell) use ($value) {
                            // manipulate the cel

                            //$cell->setValue(round($value->valor+$value->porcentaje10,2));
                            $cell->setValue(round($value->valor, 4));

                        });
                        $sheet->cell('R' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('0');

                        });
                        $sheet->cell('S' . $i, function ($cell) use ($value) {
                            // manipulate the cel

                            if ($value->parentesco == 'TITULAR') {
                                $cell->setValue('T');

                            } elseif ($value->parentesco == 'CONYUGE') {
                                $cell->setValue('C');

                            } elseif ($value->parentesco == 'HIJO/HIJA') {
                                $cell->setValue('H');

                            } elseif ($value->parentesco == 'PARIENTE') {
                                $cell->setValue('X');

                            }

                            //$cell->setValue($value->parentesco);

                        });
                        $sheet->cell('T' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_usuario);
                        });
                        $sheet->cell('U' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2) {
                            // manipulate the cel
                            //$cell->setValue($value->usuario->apellido1.' '.$value->usuario->apellido2.' '.$value->usuario->nombre1.' '.$value->usuario->nombre2);
                            //$cell->setValue($value->nombres);

                            //$descrip_nombre = str_replace($caract_remplazar,"",$value->nombres);
                            $descrip_nombre = str_replace($caract_remplazar_1, "", $value->nombres);
                            $descrip_nombre = str_replace($caract_remplazar_2, "N", $descrip_nombre);

                            $cell->setValue($descrip_nombre);

                        });
                        $sheet->cell('V' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('CVAF');

                        });
                        $sheet->cell('W' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('1');

                        });
                        $sheet->cell('X' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('1');

                        });
                        $sheet->cell('Y' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('D');

                        });
                        $sheet->cell('Z' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('0');

                        });
                        $sheet->cell('AA' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AB' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AC' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AD' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->porcentaje_iva);

                        });
                        $sheet->cell('AE' . $i, function ($cell) use ($value, $tipo_examen) {
                            // manipulate the cel
                            //$nuevo_iva = $value->cantidad*$value->valor_unitario*$value->porcentaje_iva;
                            if ($tipo_examen == 'IMM') {
                                //$nuevo_iva = $value->subtotal * $value->porcentaje_iva;
                                //$nuevo_iva = $nuevo_iva / $value->cantidad;
                                $nuevo_iva = $value->iva_unitario;
                            }else{
                                $nuevo_iva = $value->valor_unitario * $value->porcentaje_iva;
                            }
                            $cell->setValue(round($nuevo_iva, 4));

                        });
                        $sheet->cell('AF' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('F');

                        });
                        $i++;
                    }
                    $i = $i;

                }

                if (!is_null($archivo_plano_5)) {

                    foreach ($archivo_plano_5 as $value) {

                        $tipo_examen = '';
                        $ap_tipo_examen = Ap_Tipo_Examen::where('tipo',$value->tipo)->first();
                        if(!is_null($ap_tipo_examen)){
                            $tipo_examen = $ap_tipo_examen->tipo_ex;
                        }
                        if ($value->id_paciente != $id_temporal) {
                            $id_temporal = $value->id_paciente;
                            $x++;
                        }

                        $sheet->cell('A' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('515');
                        });

                        $sheet->cell('B' . $i, function ($cell) use ($value, $x) {
                            // manipulate the cel
                            $cell->setValue($x);
                        });

                        $fecha_ing     = substr($value->fecha_ing, 0, 10);
                        $fecha_ing_inv = date("d/m/Y", strtotime($fecha_ing));

                        $sheet->cell('C' . $i, function ($cell) use ($fecha_ing_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_ing_inv);
                        });

                        $sheet->cell('D' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->tiposeg);
                        });
                        $sheet->cell('E' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_paciente);

                        });
                        $sheet->cell('F' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2, $texto_apellido_1, $texto_apellido_2, $texto_final, $texto_fin, $texto_nombre_1, $texto_nombre_2) {
                            // manipulate the cel
                            //$cell->setValue($value->paciente->apellido1.' '.$value->paciente->apellido2.' '.$value->paciente->nombre1.' '.$value->paciente->nombre2);
                            /*$cell->setValue($value->apellido1.' '.$value->apellido2.' '.$value->nombre1.' '.$value->nombre2);*/
                            if ($value->apellido1 != 'N/A' && $value->apellido1 != '(N/A)') {
                                $texto_apellido_1 = $value->apellido1;
                            }

                            if ($value->apellido2 != 'N/A' && $value->apellido2 != '(N/A)') {
                                $texto_apellido_2 = $value->apellido2;
                            }

                            if ($value->nombre1 != 'N/A' && $value->nombre1 != '(N/A)') {
                                $texto_nombre_1 = $value->nombre1;
                            }

                            if ($value->nombre2 != 'N/A' && $value->nombre2 != '(N/A)') {
                                $texto_nombre_2 = $value->nombre2;
                            }

                            $texto_final = $texto_apellido_1 . ' ' . $texto_apellido_2 . ' ' . $texto_nombre_1 . ' ' . $texto_nombre_2;

                            if($value->apellido2 == '(N/A)' || $value->apellido2 == ('N/A') || $value->apellido2 == "."){
                                $texto_final = $texto_apellido_1 . ' ' . $texto_nombre_1 . ' ' . $texto_nombre_2;
                            }

                            /*$texto_final = $texto_apellido_1.' '.$texto_apellido_2.' '. $value->nombre1.' '.$value->nombre2;*/

                            $texto_fin = str_replace($caract_remplazar_1, "", $texto_final);
                            $texto_fin = str_replace($caract_remplazar_2, "N", $texto_fin);
                            $cell->setValue($texto_fin);

                        });
                        $sheet->cell('G' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->paciente->sexo == 1) {
                                $cell->setValue('M');
                            } elseif ($value->paciente->sexo == 2) {
                                $cell->setValue('F');
                            }

                        });

                        $fecha_nac     = substr($value->paciente->fecha_nacimiento, 0, 10);
                        $fecha_nac_inv = date("d/m/Y", strtotime($fecha_nac));

                        $sheet->cell('H' . $i, function ($cell) use ($fecha_nac_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_nac_inv);

                        });
                        if ($value->paciente->fecha_nacimiento == null) {
                            $edad = 0;
                        } else {
                            $edad = Carbon::createFromDate(substr($value->paciente->fecha_nacimiento, 0, 4), substr($value->paciente->fecha_nacimiento, 5, 2), substr($value->paciente->fecha_nacimiento, 8, 2))->age;
                        }
                        $sheet->cell('I' . $i, function ($cell) use ($value, $edad) {
                            // manipulate the cel
                            $cell->setValue($edad);

                        });
                        $sheet->cell('J' . $i, function ($cell) use ($value,$tipo_examen) {
                            // manipulate the cel
                            if ($value->tipo == 'P' || $value->tipo == 'AN' || $value->tipo == 'TA') {
                                $cell->setValue('HME');

                            } elseif ($value->tipo == 'M') {
                                $cell->setValue('FAR');

                            } elseif ($value->tipo == 'S') {
                                $cell->setValue('HOSP/QUIR');

                            } elseif ($value->tipo == 'IM') {
                                $cell->setValue('IMA');

                            } elseif ($value->tipo == 'EX') {
                                $cell->setValue('LAB');

                            } elseif ($value->tipo == 'I' || $value->tipo == 'IV' || $value->tipo == 'IF' || $value->tipo == 'IVC' || $tipo_examen == 'IMM') {
                                $cell->setValue('IMM');

                            } elseif ($value->tipo == 'PA') {
                                $cell->setValue('PAQUE');

                            } elseif ($value->tipo == 'EQ') {
                                $cell->setValue('PRO/ESP');

                            }

                        });
                        $sheet->cell('K' . $i, function ($cell) use ($value,$tipo_examen) {
                            // manipulate the cel
                            if ($value->tipo == 'IV' || $value->tipo == 'I' || $value->tipo == 'M' || $value->tipo == 'IVC' || $tipo_examen == 'IMM') {
                                $cell->setValue(' ');
                            } else {
                                $cell->setValue($value->codigo);
                            }

                        });
                        $sheet->cell('L' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2,$tipo_examen) {
                            // manipulate the cel

                            //$cell->setValue($value->descripcion);
                            //$descrip = str_replace($caract_remplazar,"",$value->descripcion);
                            $descrip = str_replace($caract_remplazar_1, "", $value->descripcion);
                            $descrip = str_replace($caract_remplazar_2, "N", $descrip);

                            if ($value->tipo == 'IV' || $value->tipo == 'I' || $value->tipo == 'M' || $value->tipo == 'IVC' || $tipo_examen == 'IMM') {
                                //$cell->setValue($value->descripcion);
                                $cell->setValue($descrip);

                            } else {

                                $cell->setValue('');

                            }

                        });
                        $sheet->cell('M' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->cie10);

                        });
                        $sheet->cell('N' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('O' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('P' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->cantidad);

                        });
                        $sheet->cell('Q' . $i, function ($cell) use ($value) {
                            // manipulate the cel

                            //$cell->setValue(round($value->valor+$value->porcentaje10,2));
                            $cell->setValue(round($value->valor, 4));

                        });
                        $sheet->cell('R' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('0');

                        });
                        $sheet->cell('S' . $i, function ($cell) use ($value) {
                            // manipulate the cel

                            if ($value->parentesco == 'TITULAR') {
                                $cell->setValue('T');

                            } elseif ($value->parentesco == 'CONYUGE') {
                                $cell->setValue('C');

                            } elseif ($value->parentesco == 'HIJO/HIJA') {
                                $cell->setValue('H');

                            } elseif ($value->parentesco == 'PARIENTE') {
                                $cell->setValue('X');

                            }

                            //$cell->setValue($value->parentesco);

                        });
                        $sheet->cell('T' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_usuario);
                        });
                        $sheet->cell('U' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2) {
                            // manipulate the cel
                            //$cell->setValue($value->usuario->apellido1.' '.$value->usuario->apellido2.' '.$value->usuario->nombre1.' '.$value->usuario->nombre2);
                            //$cell->setValue($value->nombres);

                            //$descrip_nombre = str_replace($caract_remplazar,"",$value->nombres);
                            $descrip_nombre = str_replace($caract_remplazar_1, "", $value->nombres);
                            $descrip_nombre = str_replace($caract_remplazar_2, "N", $descrip_nombre);

                            $cell->setValue($descrip_nombre);

                        });
                        $sheet->cell('V' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('CVAF');

                        });
                        $sheet->cell('W' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('1');

                        });
                        $sheet->cell('X' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('1');

                        });
                        $sheet->cell('Y' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('D');

                        });
                        $sheet->cell('Z' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('0');

                        });
                        $sheet->cell('AA' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AB' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AC' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AD' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->porcentaje_iva);

                        });
                        $sheet->cell('AE' . $i, function ($cell) use ($value, $tipo_examen) {
                            // manipulate the cel
                            //$nuevo_iva = $value->cantidad*$value->valor_unitario*$value->porcentaje_iva;
                            if ($tipo_examen == 'IMM') {
                                //$nuevo_iva = $value->subtotal * $value->porcentaje_iva;
                                //$nuevo_iva = $nuevo_iva / $value->cantidad;
                                $nuevo_iva = $value->iva_unitario;
                            }else{
                                $nuevo_iva = $value->valor_unitario * $value->porcentaje_iva;
                            }
                            $cell->setValue(round($nuevo_iva, 4));

                        });
                        $sheet->cell('AF' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('F');

                        });
                        $i++;
                    }
                    $i = $i;

                }

             
            });

            $excel->getActiveSheet()->getColumnDimension("A")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("B")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("C")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("D")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("E")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("F")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("G")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("H")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("I")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("J")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("K")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("L")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("M")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("N")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("O")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("P")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("Q")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("R")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("S")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("T")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("U")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("V")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("W")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("X")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("Y")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("Z")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AA")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AB")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AC")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AD")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AE")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AF")->setWidth(11)->setAutosize(false);
        })->export('xlsx');
    }

    public function generar_ap_conglomerada(Request $request)
    {
        $idUsuario = Auth::user()->id;
        
        $mes_plano = $request['mes_plano'];
        $seguro     = $request->seguro;
        $tipo_seg   = $request->id_tipo_seguro;
        //$cob_compar = $request['id_cobertura_comp'];
        $empresa    = $request->id_empresa;

        $mes_plano  = $request->mes_plano;
        $seguro     = $request->seguro;

        if($mes_plano == null){
            $mes_plano = date('mY');
        }

        if($seguro == null){
            $seguro = '2';        
        }    

        $agrupados = Ap_Agrupado::where('mes_plano',$mes_plano)->where('seguro',$seguro)->get();

        $seguros = Seguro::where('inactivo','1')->where('tipo','0')->get();

        $archivo_plano_1 = [];
        $archivo_plano_2 = [];
        $archivo_plano_3 = [];
        $archivo_plano_4 = [];
        $archivo_plano_5 = [];
        $archivo_plano   = [];

        $texto_apellido_1 = null;
        $texto_apellido_2 = null;
        $texto_final      = null;
        $texto_fin        = null;
        $texto_nombre_1   = null;
        $texto_nombre_2   = null;

        $caract_remplazar_1 = array("¨", "$", "%", "&", "/", "(", ")", "#", "@", "|", "!", "/", "+", "-", "}", "{", ">", "< ", ";", ",", ":",
            ".", "*", ">", "<", "[", "]", "?", "¿", "º", "~");

        $caract_remplazar_2 = array("Ñ", "ñ");

        $cob_compar = null;
        $archivo_plano = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
                ->whereIn('archivo_plano_cabecera.id_tipo_seguro',['1','2','3','4','5'])
                ->where('archivo_plano_cabecera.id_seguro', $seguro)
                ->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
                ->where('archivo_plano_cabecera.id_empresa', $empresa)
                ->where('archivo_plano_cabecera.estado', '1')
                ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
                ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
                ->join('archivo_plano_detalle as apd', 'apd.id_ap_cabecera', 'archivo_plano_cabecera.id')
                ->join('ap_tipo_examen as tipo_ex', 'tipo_ex.tipo', 'apd.tipo')
                ->where('apd.estado', '1')
                ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name') , 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.valor_unitario')
                //->orderby('archivo_plano_cabecera.id_tipo_seguro')
                ->orderby('full_name')
                ->orderby('archivo_plano_cabecera.fecha_ing')
                ->orderby('tipo_ex.orden_plano')
                ->get();
        $cont = $archivo_plano->count(); 
        
        $cob_compar = null;//JM
        $archivo_plano_2 = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
                ->whereIn('archivo_plano_cabecera.id_tipo_seguro',['6','8'])
                ->where('archivo_plano_cabecera.id_seguro', $seguro)
                ->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
                ->where('archivo_plano_cabecera.id_empresa', $empresa)
                ->where('archivo_plano_cabecera.estado', '1')
                ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
                ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
                ->join('archivo_plano_detalle as apd', 'apd.id_ap_cabecera', 'archivo_plano_cabecera.id')
                ->join('ap_tipo_examen as tipo_ex', 'tipo_ex.tipo', 'apd.tipo')
                ->where('apd.estado', '1')
                ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name') , 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.valor_unitario')
                ->orderby('archivo_plano_cabecera.id_tipo_seguro')
                ->orderby('full_name')
                ->orderby('archivo_plano_cabecera.fecha_ing')
                ->orderby('tipo_ex.orden_plano')
                ->get();
        $cont_2 = $archivo_plano_2->count(); 

        $cob_compar = 3;//ISSFA-ACTIVOS
        $archivo_plano_3 = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
                ->whereIn('archivo_plano_cabecera.id_tipo_seguro',['1','2','3','4','5'])
                ->where('archivo_plano_cabecera.id_seguro', $seguro)
                ->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
                ->where('archivo_plano_cabecera.id_empresa', $empresa)
                ->where('archivo_plano_cabecera.estado', '1')
                ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
                ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
                ->join('archivo_plano_detalle as apd', 'apd.id_ap_cabecera', 'archivo_plano_cabecera.id')
                ->join('ap_tipo_examen as tipo_ex', 'tipo_ex.tipo', 'apd.tipo')
                ->where('apd.estado', '1')
                ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name') , 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.valor_unitario')
                //->orderby('archivo_plano_cabecera.id_tipo_seguro')
                ->orderby('full_name')
                ->orderby('archivo_plano_cabecera.fecha_ing')
                ->orderby('tipo_ex.orden_plano')
                ->get();
        $cont_issfa_1 = $archivo_plano_3->count(); 

        $cob_compar = 3;//ISSFA-JM
        $archivo_plano_4 = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
                ->whereIn('archivo_plano_cabecera.id_tipo_seguro',['6','8'])
                ->where('archivo_plano_cabecera.id_seguro', $seguro)
                ->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
                ->where('archivo_plano_cabecera.id_empresa', $empresa)
                ->where('archivo_plano_cabecera.estado', '1')
                ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
                ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
                ->join('archivo_plano_detalle as apd', 'apd.id_ap_cabecera', 'archivo_plano_cabecera.id')
                ->join('ap_tipo_examen as tipo_ex', 'tipo_ex.tipo', 'apd.tipo')
                ->where('apd.estado', '1')
                ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name') , 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.valor_unitario')
                ->orderby('archivo_plano_cabecera.id_tipo_seguro')
                ->orderby('full_name')
                ->orderby('archivo_plano_cabecera.fecha_ing')
                ->orderby('tipo_ex.orden_plano')
                ->get();
        $cont_issfa_2 = $archivo_plano_4->count(); 

        $cob_compar = 6;//ISSPOL-ACTIVOS
        $archivo_plano_5 = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
                ->whereIn('archivo_plano_cabecera.id_tipo_seguro',['1','2','3','4','5'])
                ->where('archivo_plano_cabecera.id_seguro', $seguro)
                ->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
                ->where('archivo_plano_cabecera.id_empresa', $empresa)
                ->where('archivo_plano_cabecera.estado', '1')
                ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
                ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
                ->join('archivo_plano_detalle as apd', 'apd.id_ap_cabecera', 'archivo_plano_cabecera.id')
                ->join('ap_tipo_examen as tipo_ex', 'tipo_ex.tipo', 'apd.tipo')
                ->where('apd.estado', '1')
                ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name') , 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.valor_unitario')
                //->orderby('archivo_plano_cabecera.id_tipo_seguro')
                ->orderby('full_name')
                ->orderby('archivo_plano_cabecera.fecha_ing')
                ->orderby('tipo_ex.orden_plano')
                ->get();
        $cont_isspol_1 = $archivo_plano_5->count();

        $cob_compar = 6;//ISSPOL-JM
        $archivo_plano_6 = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
                ->whereIn('archivo_plano_cabecera.id_tipo_seguro',['6','8'])
                ->where('archivo_plano_cabecera.id_seguro', $seguro)
                ->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
                ->where('archivo_plano_cabecera.id_empresa', $empresa)
                ->where('archivo_plano_cabecera.estado', '1')
                ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
                ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
                ->join('archivo_plano_detalle as apd', 'apd.id_ap_cabecera', 'archivo_plano_cabecera.id')
                ->join('ap_tipo_examen as tipo_ex', 'tipo_ex.tipo', 'apd.tipo')
                ->where('apd.estado', '1')
                ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name') , 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.valor_unitario')
                ->orderby('archivo_plano_cabecera.id_tipo_seguro')
                ->orderby('full_name')
                ->orderby('archivo_plano_cabecera.fecha_ing')
                ->orderby('tipo_ex.orden_plano')
                ->get();

        $cont_isspol2 = $archivo_plano_6->count();
        
        // if(!is_null($archivo_plano)){
        //     $txt_cie10 = null;

        //     $cie10 = Cie_10_3::find($archivo_plano->cie10);
        //     if (is_null($cie10)) {
        //         $cie10 = Cie_10_4::find($archivo_plano->cie10);
        //         if (!is_null($cie10)) {
        //             $txt_cie10 = '(' . $archivo_plano->cie10 . ') ' . $cie10->descripcion;
        //         }
        //     } else {
        //         $txt_cie10 = '(' . $archivo_plano->cie10 . ') ' . $cie10->descripcion;
        //     }
        
        //     $honor_medicos = Db::table('archivo_plano_detalle as apd')
        //         ->where('id_ap_cabecera', $archivo_plano->id)
        //         ->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')
        //         ->orderby('apd.porcentaje_honorario', 'desc')
        //         ->orderby('apt.secuencia', 'asc')
        //         ->where('apt.tipo_ex', 'HME')
        //         //->orWhere('apt.tipo_ex','P')
        //         ->where('apd.estado', '1')
        //         ->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')
        //         ->get();


        //     $medicinas = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')->where('apt.tipo_ex', 'FAR')->where('apd.estado', '1')->get();

        //     $insumos = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total', 'apd.valor_unitario', 'apd.porcentaje_iva')->where('apt.tipo_ex', 'IMM')->where('apd.estado', '1')->get();

        //     $laboratorio = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')->where('apt.tipo_ex', 'LAB')->where('apd.estado', '1')->get();

        //     $servicios_ins = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')->where('apt.tipo_ex', 'HOSP/QUIR')->where('apd.estado', '1')->get();

        //     $imagen = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')->where('apt.tipo_ex', 'IMA')->where('apd.estado', '1')->get();
            
        //     $equipos = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')->where('apt.tipo_ex', 'PRO/ESP')->where('apd.estado', '1')->get();
        
        // }
        
        // if($idUsuario=='0950457978'){
        //     //dd('consolidada1');
        // }

        $view = View::make('archivo_plano.planilla.planilla_pdf', compact('archivo_plano','txt_cie10','medicinas','insumos','laboratorio','servicios_ins','imagen','equipos','honor_medicos'))->render();
        $pdf  = \App::make('dompdf.wrapper');
        $pdf->setOptions(['chroot'  => base_path('/')]);
        $pdf->loadHTML($view);
        return $pdf->download('planilla_consolidada.pdf');
    }

    public function nuevo_reporte_consolidado_general(Request $request)
    {
        $idUsuario = Auth::user()->id;
        
        //ini_set('memory_limit', '10024M');
        $mes_plano = $request['mes_plano'];
        $seg       = $request['seguro'];
        $tipo_seg  = $request['id_tipo_seguro'];
        $empresa   = $request['id_empresa'];

        $sub_cadena_1 = '';
        $sub_cadena_2 = '';

        $cantidad_1_activo          = 0;
        $cantidad_2_activo_conyugue = 0;
        $cantidad_0_1               = 0;
        $cantidad_2_6               = 0;
        $cantidad_7_17              = 0;
        $cantidad_jubilado          = 0;
        $cantidad_montepio          = 0;

        $archivo_plano_activo         = [];
        $archivo_plan_activo_conyugue = [];
        $archivo_plano_0_1            = [];
        $archivo_plano_2_6            = [];
        $archivo_plano_7_17           = [];
        $archivo_plano_jubilado       = [];
        $archivo_plano_montepio       = [];
        $archivo_plano_isspol_activo  = [];
        $archivo_plano_isspol_jubilado  = [];
        $archivo_plano_isspol_montepio  = [];
        $archivo_plano_issfa_activo  = [];
        $archivo_plano_isspol_jubilado  = [];
        $archivo_plano_isspol_montepio  = [];
        $texto_nombre = null;

        $caract_remplazar_1 = array("¨", "$", "%", "&", "/", "(", ")", "#", "@", "|", "!", "/", "+", "-", "}", "{", ">", "< ", ";", ",", ":",
            ".", "*", ">", "<", "[", "]", "?", "¿", "º", "~");

        $caract_remplazar_2 = array("Ñ", "ñ");

        if (!is_null($mes_plano)) {
            $sub_cadena_1 = substr($mes_plano, 0, 2);
        }

        if (!is_null($mes_plano)) {
            $sub_cadena_2 = substr($mes_plano, 2, 6);
        }

        $inf_empresa = Empresa::where('id', $empresa)->where('estado', '1')->first();

        //REPORTE CONSOLIDADO IESS GENERAL
        //ACTIVO  SG
        $cob_compar = null;
        $archivo_plano_activo = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->where(function ($query) {
                $query->where('archivo_plano_cabecera.id_tipo_seguro', '1')
                    ->orwhere('archivo_plano_cabecera.id_tipo_seguro', '2')
                    ->orwhere('archivo_plano_cabecera.id_tipo_seguro', '3')
                    ->orwhere('archivo_plano_cabecera.id_tipo_seguro', '4')
                    ->orwhere('archivo_plano_cabecera.id_tipo_seguro', '5');
            })
            ->where('archivo_plano_cabecera.id_seguro', $seg)
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            ->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
            ->where('archivo_plano_cabecera.estado', '1')
            ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd', 'archivo_plano_cabecera.id', 'apd.id_ap_cabecera')
            ->where('apd.estado', '1')
            ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'archivo_plano_cabecera.id_empresa', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.subtotal', 'apd.total', 'apd.valor_unitario', 'apd.iva_unitario','apd.p10_unitario')
            ->orderby('full_name')
            ->get();

        //JUBILADO JU
        $cob_compar = null;
        $archivo_plano_jubilado = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->where('archivo_plano_cabecera.id_tipo_seguro', '6')
            ->where('archivo_plano_cabecera.id_seguro', $seg)
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            ->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
            ->where('archivo_plano_cabecera.estado', '1')
            ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd', 'archivo_plano_cabecera.id', 'apd.id_ap_cabecera')
            ->where('apd.estado', '1')
            ->orderby('pac.apellido1', 'asc')
            ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'archivo_plano_cabecera.id_empresa', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.subtotal', 'apd.total', 'apd.valor_unitario', 'apd.iva_unitario','apd.p10_unitario')
            ->orderby('full_name')
            ->get();

        //MONTEPIO MO
        $cob_compar = null;
        $archivo_plano_montepio = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->where('archivo_plano_cabecera.id_tipo_seguro', '8')
            ->where('archivo_plano_cabecera.id_seguro', $seg)
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            ->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
            ->where('archivo_plano_cabecera.estado', '1')
            ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd', 'archivo_plano_cabecera.id', 'apd.id_ap_cabecera')
            ->where('apd.estado', '1')
            ->orderby('pac.apellido1', 'asc')
            ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'archivo_plano_cabecera.id_empresa', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.subtotal', 'apd.total', 'apd.valor_unitario', 'apd.iva_unitario','apd.p10_unitario')
            ->orderby('full_name')
            ->get();
        //ISSFA
        //ACTIVO  SG
        $cob_compar = 3;
        $archivo_plano_issfa_activo = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->where(function ($query) {
                $query->where('archivo_plano_cabecera.id_tipo_seguro', '1')
                    ->orwhere('archivo_plano_cabecera.id_tipo_seguro', '2')
                    ->orwhere('archivo_plano_cabecera.id_tipo_seguro', '3')
                    ->orwhere('archivo_plano_cabecera.id_tipo_seguro', '4')
                    ->orwhere('archivo_plano_cabecera.id_tipo_seguro', '5');
            })
            ->where('archivo_plano_cabecera.id_seguro', $seg)
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            ->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
            ->where('archivo_plano_cabecera.estado', '1')
            ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd', 'archivo_plano_cabecera.id', 'apd.id_ap_cabecera')
            ->where('apd.estado', '1')
            ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'archivo_plano_cabecera.id_empresa', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.subtotal', 'apd.total', 'apd.valor_unitario', 'apd.iva_unitario','apd.p10_unitario')
            ->orderby('full_name')
            ->get();

        //JUBILADO JU
        $cob_compar = 3;
        $archivo_plano_issfa_jubilado = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->where('archivo_plano_cabecera.id_tipo_seguro', '6')
            ->where('archivo_plano_cabecera.id_seguro', $seg)
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            ->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
            ->where('archivo_plano_cabecera.estado', '1')
            ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd', 'archivo_plano_cabecera.id', 'apd.id_ap_cabecera')
            ->where('apd.estado', '1')
            ->orderby('pac.apellido1', 'asc')
            ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'archivo_plano_cabecera.id_empresa', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.subtotal', 'apd.total', 'apd.valor_unitario', 'apd.iva_unitario','apd.p10_unitario')
            ->orderby('full_name')
            ->get();

        //MONTEPIO MO
        $cob_compar = 3;
        $archivo_plano_issfa_montepio = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->where('archivo_plano_cabecera.id_tipo_seguro', '8')
            ->where('archivo_plano_cabecera.id_seguro', $seg)
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            ->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
            ->where('archivo_plano_cabecera.estado', '1')
            ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd', 'archivo_plano_cabecera.id', 'apd.id_ap_cabecera')
            ->where('apd.estado', '1')
            ->orderby('pac.apellido1', 'asc')
            ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'archivo_plano_cabecera.id_empresa', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.subtotal', 'apd.total', 'apd.valor_unitario', 'apd.iva_unitario','apd.p10_unitario')
            ->orderby('full_name')
            ->get();

        //ISSPOL
        //ACTIVO  SG
        $cob_compar = 6;
        $archivo_plano_isspol_activo = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->where(function ($query) {
                $query->where('archivo_plano_cabecera.id_tipo_seguro', '1')
                    ->orwhere('archivo_plano_cabecera.id_tipo_seguro', '2')
                    ->orwhere('archivo_plano_cabecera.id_tipo_seguro', '3')
                    ->orwhere('archivo_plano_cabecera.id_tipo_seguro', '4')
                    ->orwhere('archivo_plano_cabecera.id_tipo_seguro', '5');
            })
            ->where('archivo_plano_cabecera.id_seguro', $seg)
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            ->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
            ->where('archivo_plano_cabecera.estado', '1')
            ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd', 'archivo_plano_cabecera.id', 'apd.id_ap_cabecera')
            ->where('apd.estado', '1')
            ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'archivo_plano_cabecera.id_empresa', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.subtotal', 'apd.total', 'apd.valor_unitario', 'apd.iva_unitario','apd.p10_unitario')
            ->orderby('full_name')
            ->get();

        //JUBILADO JU
        $cob_compar = 6;
        $archivo_plano_isspol_jubilado = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->where('archivo_plano_cabecera.id_tipo_seguro', '6')
            ->where('archivo_plano_cabecera.id_seguro', $seg)
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            ->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
            ->where('archivo_plano_cabecera.estado', '1')
            ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd', 'archivo_plano_cabecera.id', 'apd.id_ap_cabecera')
            ->where('apd.estado', '1')
            ->orderby('pac.apellido1', 'asc')
            ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'archivo_plano_cabecera.id_empresa', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.subtotal', 'apd.total', 'apd.valor_unitario', 'apd.iva_unitario','apd.p10_unitario')
            ->orderby('full_name')
            ->get();

        //MONTEPIO MO
        $cob_compar = 6;
        $archivo_plano_isspol_montepio = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->where('archivo_plano_cabecera.id_tipo_seguro', '8')
            ->where('archivo_plano_cabecera.id_seguro', $seg)
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            ->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
            ->where('archivo_plano_cabecera.estado', '1')
            ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd', 'archivo_plano_cabecera.id', 'apd.id_ap_cabecera')
            ->where('apd.estado', '1')
            ->orderby('pac.apellido1', 'asc')
            ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'archivo_plano_cabecera.id_empresa', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.subtotal', 'apd.total', 'apd.valor_unitario', 'apd.iva_unitario','apd.p10_unitario')
            ->orderby('full_name')
            ->get();
        
        Excel::create('ReporteConsolidadoIESS', function ($excel) use ($inf_empresa, $sub_cadena_1, $sub_cadena_2, $cantidad_1_activo, $cantidad_2_activo_conyugue, $cantidad_0_1, $cantidad_2_6, $cantidad_7_17, $cantidad_jubilado, $cantidad_montepio, $archivo_plano_activo, $archivo_plano_jubilado, $archivo_plano_montepio, $archivo_plano_isspol_activo, $archivo_plano_isspol_jubilado, $archivo_plano_isspol_montepio, $archivo_plano_issfa_activo, $archivo_plano_issfa_jubilado, $archivo_plano_issfa_montepio, $caract_remplazar_1, $caract_remplazar_2, $texto_nombre,$idUsuario) {
            $excel->sheet('ReporteConsolidadoIESS', function ($sheet) use ($inf_empresa, $sub_cadena_1, $sub_cadena_2, $cantidad_1_activo, $cantidad_2_activo_conyugue, $cantidad_0_1, $cantidad_2_6, $cantidad_7_17, $cantidad_jubilado, $cantidad_montepio, $archivo_plano_activo, $archivo_plano_jubilado, $archivo_plano_montepio, $archivo_plano_isspol_activo, $archivo_plano_isspol_jubilado, $archivo_plano_isspol_montepio, $archivo_plano_issfa_activo, $archivo_plano_issfa_jubilado, $archivo_plano_issfa_montepio, $caract_remplazar_1, $caract_remplazar_2, $texto_nombre,$idUsuario) {

                $sheet->setColumnFormat(array(
                    'I' => '$### ### ### ##0.00',
                    'K' => '$### ### ### ##0.00',
                    'M' => '$### ### ### ##0.00',
                    'N' => '$### ### ### ##0.00',
                    'O' => '$### ### ### ##0.00',
                ));

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
                    $cell->setValue('PLANILLA CONSOLIDADO DE PRESTACION DE SERVICIO');
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
                $sheet->mergeCells('J11:P11');
                $sheet->cell('J11', function ($cell) use ($sub_cadena_2) {
                    // manipulate the cel
                    $cell->setValue('Año:' . ($sub_cadena_2));
                    $cell->setAlignment('left');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                /*$sheet->mergeCells('k11:P11');
                $sheet->cell('K11', function($cell) use($sub_cadena_2){
                $cell->setValue($sub_cadena_2);
                $cell->setAlignment('left');
                $cell->setFontSize('10');
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });*/

                $sheet->cell('A12', function ($cell) use ($inf_empresa) {
                    $cell->setValue('No');
                    $cell->setAlignment('left');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('B12:G12');
                $sheet->cell('B12', function ($cell) use ($inf_empresa) {
                    $cell->setValue('NOMBRE DE PACIENTE/USUARIO');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('H12', function ($cell) use ($inf_empresa) {
                    $cell->setValue('TIPO DE SEGURO');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('I12:J12');
                $sheet->cell('I12', function ($cell) use ($inf_empresa) {
                    $cell->setValue('BASE0');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('K12:L12');
                $sheet->cell('K12', function ($cell) use ($inf_empresa) {
                    $cell->setValue('BASE12');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('M12', function ($cell) use ($inf_empresa) {
                    $cell->setValue('VALOR IVA 12%');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('N12', function ($cell) use ($inf_empresa) {
                    $cell->setValue('GASTO ADM');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('O12:P12');
                $sheet->cell('O12', function ($cell) use ($inf_empresa) {
                    $cell->setValue('SUBTOTAL:');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $i           = 13;
                $base        = 0;
                $id_temporal = 0;
                $x           = 1;
                $sub_total   = 0;
                $iva         = 0;
                $total       = 0;

                $total_activo     = 0;
                $iva_activo       = 0;
                $sub_total_activo = 0;

                $total_conyugue     = 0;
                $iva_conyugue       = 0;
                $sub_total_conyugue = 0;

                $total_0_1     = 0;
                $iva_0_1       = 0;
                $sub_total_0_1 = 0;

                $total_2_6     = 0;
                $iva_2_6       = 0;
                $sub_total_2_6 = 0;

                $total_7     = 0;
                $iva_7       = 0;
                $sub_total_7 = 0;

                $total_jub     = 0;
                $iva_jub       = 0;
                $sub_total_jub = 0;


                $total_montepio     = 0;
                $iva_montepio       = 0;
                $sub_total_montepio = 0;

                $total_general     = 0;
                $sub_total_general = 0;
                $iva_general       = 0;

                $acum_bas_0 = 0; $acum_bas_iva = 0; $acum_v_iva = 0; $acum_gasto_amd = 0;
                $value      = null;

                //MUESTRA ACTIVO  SG
                if (!is_null($archivo_plano_activo)) {

                    $base_0          = 0;
                    $sum             = 0;
                    $sum_bas_0       = 0;
                    $sum_bas_iva     = 0;
                    $sum_v_iva       = 0;
                    $sum_gasto_amd   = 0;
                    $total_m_iva     = 0;
                    $nombre_anterior = '';
                    $tipo_seguro_ant = '';
                    //$total_activo = 0;
                    //$iva_activo = 0;
                    $id_temporal = 0;
                   
                    foreach ($archivo_plano_activo as $value) {
                        

                        if ($value->id_paciente != $id_temporal) {

                            if ($id_temporal != 0) {
                                

                                //Escribo en excel
                                //No
                                $sheet->cell('A' . $i, function ($cell) use ($x) {
                                    // manipulate the cel
                                    $cell->setValue($x);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //NOMBRE DE PACIENTE/USUARIO
                                $sheet->mergeCells('B' . $i . ':G' . $i);
                                $sheet->cell('B' . $i, function ($cell) use ($value, $nombre_anterior) {
                                    // manipulate the cel
                                    $cell->setValue($nombre_anterior);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });

                                //TIPO DE SEGURO
                                $sheet->cell('H' . $i, function ($cell) use ($value, $tipo_seguro_ant) {
                                    // manipulate the cel

                                    $cell->setValue($tipo_seguro_ant);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //BASE0
                                $sheet->mergeCells('I' . $i . ':J' . $i);
                                $sheet->cell('I' . $i, function ($cell) use ($sum_bas_0) {
                                    // manipulate the cel
                                    $cell->setValue($sum_bas_0);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //BASE12
                                $sheet->mergeCells('K' . $i . ':L' . $i);
                                $sheet->cell('K' . $i, function ($cell) use ($sum_bas_iva) {
                                    // manipulate the cel
                                    $cell->setValue($sum_bas_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //VALOR IVA 12%
                                $sheet->cell('M' . $i, function ($cell) use ($sum_v_iva) {
                                    // manipulate the cel
                                    $cell->setValue($sum_v_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //GASTO ADM
                                $sheet->cell('N' . $i, function ($cell) use ($sum_gasto_amd) {
                                    // manipulate the cel
                                    $cell->setValue($sum_gasto_amd);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //SUBTOTAL
                                $sheet->mergeCells('O' . $i . ':P' . $i);
                                $sheet->cell('O' . $i, function ($cell) use ($total_m_iva) {
                                    // manipulate the cel
                                    $cell->setValue($total_m_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });

                                $total_activo += $total_m_iva;
                                $iva_activo += $sum_v_iva;
                                $sub_total_activo = $total_activo - $iva_activo;

                                //Fin de Escritura del Excel
                                $base_0          = 0;
                                $sum             = 0;
                                $sum_bas_0       = 0;
                                $sum_bas_iva     = 0;
                                $sum_v_iva       = 0;
                                $sum_gasto_amd   = 0;
                                $total_m_iva     = 0;
                                $nombre_anterior = '';
                                $tipo_seguro_ant = '';
                                $x++;
                                $i++;

                            }

                            $id_temporal = $value->id_paciente;

                        }

                        //Si porcentaje_iva == 0  BASE_0
                        if ($value->porcentaje_iva == 0) {
                            $sum_bas_0 = $sum_bas_0 + $value->subtotal;
                            $acum_bas_0 += $value->subtotal;
                        }
                        //Si porcentaje_iva == 0.12  BASE_IVA
                        if ($value->porcentaje_iva == 0.12) {
                            $sum_bas_iva = $sum_bas_iva + $value->subtotal;
                            $acum_bas_iva += $value->subtotal;
                        }
                        //Suma v_iva
                        //$sum_v_iva = $sum_v_iva + ($value->valor_unitario * $value->cantidad * $value->porcentaje_iva);
                        $sum_v_iva = $sum_v_iva + ($value->iva_unitario * $value->cantidad );
                        //$acum_v_iva += $value->iva;
                        //$acum_v_iva += ($value->valor_unitario * $value->cantidad * $value->porcentaje_iva);
                        $acum_v_iva += ($value->iva_unitario * $value->cantidad);
                        //  GAST_ADM_10
                        //$sum_gasto_amd = $sum_gasto_amd + $value->porcentaje10;
                        $sum_gasto_amd = $sum_gasto_amd + ($value->p10_unitario * $value->cantidad);
                        //$acum_gasto_amd += $value->porcentaje10;
                        $acum_gasto_amd += ($value->p10_unitario * $value->cantidad);

                        //TOTAL_M_IVA
                        $total_m_iva = $sum_bas_0 + $sum_bas_iva + $sum_v_iva + $sum_gasto_amd;

                        /*$nombre_anterior = $value->paciente->apellido1.' '.$value->paciente->apellido2.' '.$value->paciente->nombre1.' '.$value->paciente->nombre2;*/
                        $nombre_anterior = '';
                        if ($value->paciente->apellido1 != 'N/A' && $value->paciente->apellido1 != '(N/A)') {
                            $nombre_anterior = $value->paciente->apellido1;
                        }
                        if ($value->paciente->apellido2 != 'N/A' && $value->paciente->apellido2 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->apellido2;
                        }
                        if ($value->paciente->nombre1 != 'N/A' && $value->paciente->nombre1 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->nombre1;
                        }

                        if ($value->paciente->nombre2 != 'N/A' && $value->paciente->nombre2 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->nombre2;
                        }

                        $nombre_anterior = str_replace($caract_remplazar_1, "", $nombre_anterior);
                        $nombre_anterior = str_replace($caract_remplazar_2, "N", $nombre_anterior);

                        $tipo_seguro_ant = $value->tiposeg;

                    }
                    //Escribo en excel
                    //No
                    $sheet->cell('A' . $i, function ($cell) use ($x) {
                        // manipulate the cel
                        $cell->setValue($x);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    //NOMBRE DE PACIENTE/USUARIO
                    $sheet->mergeCells('B' . $i . ':G' . $i);
                    $sheet->cell('B' . $i, function ($cell) use ($nombre_anterior) {
                        // manipulate the cel
                        $cell->setValue($nombre_anterior);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    //TIPO DE SEGURO
                    $sheet->cell('H' . $i, function ($cell) use ($tipo_seguro_ant) {
                        // manipulate the cel

                        $cell->setValue($tipo_seguro_ant);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    //BASE0
                    $sheet->mergeCells('I' . $i . ':J' . $i);
                    $sheet->cell('I' . $i, function ($cell) use ($sum_bas_0) {
                        // manipulate the cel
                        $cell->setValue($sum_bas_0);
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    //BASE12
                    $sheet->mergeCells('K' . $i . ':L' . $i);
                    $sheet->cell('K' . $i, function ($cell) use ($sum_bas_iva) {
                        // manipulate the cel
                        $cell->setValue($sum_bas_iva);
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    //VALOR IVA 12%
                    $sheet->cell('M' . $i, function ($cell) use ($sum_v_iva) {
                        // manipulate the cel
                        $cell->setValue($sum_v_iva);
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    //GASTO ADM
                    $sheet->cell('N' . $i, function ($cell) use ($sum_gasto_amd) {
                        // manipulate the cel
                        $cell->setValue($sum_gasto_amd);
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    //SUBTOTAL
                    $sheet->mergeCells('O' . $i . ':P' . $i);
                    $sheet->cell('O' . $i, function ($cell) use ($total_m_iva) {
                        // manipulate the cel
                        $cell->setValue($total_m_iva);
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    //Fin de Escritura del Excel
                    $base_0          = 0;
                    $sum             = 0;
                    $sum_bas_0       = 0;
                    $sum_bas_iva     = 0;
                    $sum_v_iva       = 0;
                    $sum_gasto_amd   = 0;
                    $total_m_iva     = 0;
                    $nombre_anterior = '';
                    $tipo_seguro_ant = '';
                    $x++;
                    $i++;
                }

                //JUBILADO
                if (!is_null($archivo_plano_jubilado)) {

                    $base_0          = 0;
                    $sum             = 0;
                    $sum_bas_0       = 0;
                    $sum_bas_iva     = 0;
                    $sum_v_iva       = 0;
                    $sum_gasto_amd   = 0;
                    $total_m_iva     = 0;
                    $nombre_anterior = '';
                    $tipo_seguro_ant = '';
                    $id_temporal     = 0;

                    foreach ($archivo_plano_jubilado as $value) {
                        if ($value->id_paciente != $id_temporal) {

                            if ($id_temporal != 0) {

                                //Escribo en excel
                                //No
                                $sheet->cell('A' . $i, function ($cell) use ($x) {
                                    // manipulate the cel
                                    $cell->setValue($x);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //NOMBRE DE PACIENTE/USUARIO
                                $sheet->mergeCells('B' . $i . ':G' . $i);
                                $sheet->cell('B' . $i, function ($cell) use ($value, $nombre_anterior) {
                                    // manipulate the cel
                                    $cell->setValue($nombre_anterior);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });

                                //TIPO DE SEGURO
                                $sheet->cell('H' . $i, function ($cell) use ($value, $tipo_seguro_ant) {
                                    // manipulate the cel

                                    $cell->setValue($tipo_seguro_ant);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //BASE0
                                $sheet->mergeCells('I' . $i . ':J' . $i);
                                $sheet->cell('I' . $i, function ($cell) use ($sum_bas_0) {
                                    // manipulate the cel
                                    $cell->setValue($sum_bas_0);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //BASE12
                                $sheet->mergeCells('K' . $i . ':L' . $i);
                                $sheet->cell('K' . $i, function ($cell) use ($sum_bas_iva) {
                                    // manipulate the cel
                                    $cell->setValue($sum_bas_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //VALOR IVA 12%
                                $sheet->cell('M' . $i, function ($cell) use ($sum_v_iva) {
                                    // manipulate the cel
                                    $cell->setValue($sum_v_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //GASTO ADM
                                $sheet->cell('N' . $i, function ($cell) use ($sum_gasto_amd) {
                                    // manipulate the cel
                                    $cell->setValue($sum_gasto_amd);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //SUBTOTAL
                                $sheet->mergeCells('O' . $i . ':P' . $i);
                                $sheet->cell('O' . $i, function ($cell) use ($total_m_iva) {
                                    // manipulate the cel
                                    $cell->setValue($total_m_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                $total_jub += $total_m_iva;
                                $iva_jub += $sum_v_iva;
                                $sub_total_jub = $total_jub - $iva_jub;
                                //Fin de Escritura del Excel
                                $base_0          = 0;
                                $sum             = 0;
                                $sum_bas_0       = 0;
                                $sum_bas_iva     = 0;
                                $sum_v_iva       = 0;
                                $sum_gasto_amd   = 0;
                                $total_m_iva     = 0;
                                $nombre_anterior = '';
                                $tipo_seguro_ant = '';
                                $x++;
                                $i++;

                            }

                            $id_temporal = $value->id_paciente;

                        }

                        //Si porcentaje_iva == 0  BASE_0
                        if ($value->porcentaje_iva == 0) {
                            $sum_bas_0 = $sum_bas_0 + $value->subtotal;
                            $acum_bas_0 += $value->subtotal;
                        }
                        //Si porcentaje_iva == 0.12  BASE_IVA
                        if ($value->porcentaje_iva == 0.12) {
                            $sum_bas_iva = $sum_bas_iva + $value->subtotal;
                            $acum_bas_iva += $value->subtotal;
                        }
                        //Suma v_iva
                        //$sum_v_iva = $sum_v_iva + ($value->valor_unitario * $value->cantidad * $value->porcentaje_iva);
                        $sum_v_iva = $sum_v_iva + ($value->iva_unitario * $value->cantidad );
                        //$acum_v_iva += $value->iva;
                        //$acum_v_iva += ($value->valor_unitario * $value->cantidad * $value->porcentaje_iva);
                        $acum_v_iva += ($value->iva_unitario * $value->cantidad);
                        //  GAST_ADM_10
                        //$sum_gasto_amd = $sum_gasto_amd + $value->porcentaje10;
                        $sum_gasto_amd = $sum_gasto_amd + ($value->p10_unitario * $value->cantidad);
                        //$acum_gasto_amd += $value->porcentaje10;
                        $acum_gasto_amd += ($value->p10_unitario * $value->cantidad);

                        //TOTAL_M_IVA
                        $total_m_iva = $sum_bas_0 + $sum_bas_iva + $sum_v_iva + $sum_gasto_amd;

                        /*$nombre_anterior = $value->paciente->apellido1.' '.$value->paciente->apellido2.' '.$value->paciente->nombre1.' '.$value->paciente->nombre2;*/
                        $nombre_anterior = '';
                        if ($value->paciente->apellido1 != 'N/A' && $value->paciente->apellido1 != '(N/A)') {
                            $nombre_anterior = $value->paciente->apellido1;
                        }
                        if ($value->paciente->apellido2 != 'N/A' && $value->paciente->apellido2 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->apellido2;
                        }
                        if ($value->paciente->nombre1 != 'N/A' && $value->paciente->nombre1 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->nombre1;
                        }

                        if ($value->paciente->nombre2 != 'N/A' && $value->paciente->nombre2 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->nombre2;
                        }

                        $nombre_anterior = str_replace($caract_remplazar_1, "", $nombre_anterior);
                        $nombre_anterior = str_replace($caract_remplazar_2, "N", $nombre_anterior);

                        $tipo_seguro_ant = $value->tiposeg;

                    }

                    //Escribo en excel
                    //No
                    if ($nombre_anterior != '') {
                        $sheet->cell('A' . $i, function ($cell) use ($x) {
                            // manipulate the cel
                            $cell->setValue($x);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //NOMBRE DE PACIENTE/USUARIO
                        $sheet->mergeCells('B' . $i . ':G' . $i);
                        $sheet->cell('B' . $i, function ($cell) use ($nombre_anterior) {
                            // manipulate the cel
                            $cell->setValue($nombre_anterior);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        //TIPO DE SEGURO
                        $sheet->cell('H' . $i, function ($cell) use ($tipo_seguro_ant) {
                            // manipulate the cel

                            $cell->setValue($tipo_seguro_ant);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //BASE0
                        $sheet->mergeCells('I' . $i . ':J' . $i);
                        $sheet->cell('I' . $i, function ($cell) use ($sum_bas_0) {
                            // manipulate the cel
                            $cell->setValue($sum_bas_0);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //BASE12
                        $sheet->mergeCells('K' . $i . ':L' . $i);
                        $sheet->cell('K' . $i, function ($cell) use ($sum_bas_iva) {
                            // manipulate the cel
                            $cell->setValue($sum_bas_iva);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //VALOR IVA 12%
                        $sheet->cell('M' . $i, function ($cell) use ($sum_v_iva) {
                            // manipulate the cel
                            $cell->setValue($sum_v_iva);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //GASTO ADM
                        $sheet->cell('N' . $i, function ($cell) use ($sum_gasto_amd) {
                            // manipulate the cel
                            $cell->setValue($sum_gasto_amd);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //SUBTOTAL
                        $sheet->mergeCells('O' . $i . ':P' . $i);
                        $sheet->cell('O' . $i, function ($cell) use ($total_m_iva) {
                            // manipulate the cel
                            $cell->setValue($total_m_iva);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        $x++;
                        $i++;
                    }

                    //Fin de Escritura del Excel
                    $base_0          = 0;
                    $sum             = 0;
                    $sum_bas_0       = 0;
                    $sum_bas_iva     = 0;
                    $sum_v_iva       = 0;
                    $sum_gasto_amd   = 0;
                    $total_m_iva     = 0;
                    $nombre_anterior = '';
                    $tipo_seguro_ant = '';
                    //$x++;
                    //$i++;
                }

                //MONTEPIO
                if (!is_null($archivo_plano_montepio)) {

                    $base_0          = 0;
                    $sum             = 0;
                    $sum_bas_0       = 0;
                    $sum_bas_iva     = 0;
                    $sum_v_iva       = 0;
                    $sum_gasto_amd   = 0;
                    $total_m_iva     = 0;
                    $nombre_anterior = '';
                    $tipo_seguro_ant = '';
                    $id_temporal     = 0;

                    foreach ($archivo_plano_montepio as $value) {
                        if ($value->id_paciente != $id_temporal) {

                            if ($id_temporal != 0) {

                                //Escribo en excel
                                //No
                                $sheet->cell('A' . $i, function ($cell) use ($x) {
                                    // manipulate the cel
                                    $cell->setValue($x);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //NOMBRE DE PACIENTE/USUARIO
                                $sheet->mergeCells('B' . $i . ':G' . $i);
                                $sheet->cell('B' . $i, function ($cell) use ($value, $nombre_anterior) {
                                    // manipulate the cel
                                    $cell->setValue($nombre_anterior);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });

                                //TIPO DE SEGURO
                                $sheet->cell('H' . $i, function ($cell) use ($value, $tipo_seguro_ant) {
                                    // manipulate the cel

                                    $cell->setValue($tipo_seguro_ant);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //BASE0
                                $sheet->mergeCells('I' . $i . ':J' . $i);
                                $sheet->cell('I' . $i, function ($cell) use ($sum_bas_0) {
                                    // manipulate the cel
                                    $cell->setValue($sum_bas_0);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //BASE12
                                $sheet->mergeCells('K' . $i . ':L' . $i);
                                $sheet->cell('K' . $i, function ($cell) use ($sum_bas_iva) {
                                    // manipulate the cel
                                    $cell->setValue($sum_bas_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //VALOR IVA 12%
                                $sheet->cell('M' . $i, function ($cell) use ($sum_v_iva) {
                                    // manipulate the cel
                                    $cell->setValue($sum_v_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //GASTO ADM
                                $sheet->cell('N' . $i, function ($cell) use ($sum_gasto_amd) {
                                    // manipulate the cel
                                    $cell->setValue($sum_gasto_amd);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //SUBTOTAL
                                $sheet->mergeCells('O' . $i . ':P' . $i);
                                $sheet->cell('O' . $i, function ($cell) use ($total_m_iva) {
                                    // manipulate the cel
                                    $cell->setValue($total_m_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });

                                $total_montepio += $total_m_iva;
                                $iva_montepio += $sum_v_iva;
                                $sub_total_montepio = $total_montepio - $iva_montepio;

                                //Fin de Escritura del Excel
                                $base_0          = 0;
                                $sum             = 0;
                                $sum_bas_0       = 0;
                                $sum_bas_iva     = 0;
                                $sum_v_iva       = 0;
                                $sum_gasto_amd   = 0;
                                $total_m_iva     = 0;
                                $nombre_anterior = '';
                                $tipo_seguro_ant = '';
                                $x++;
                                $i++;

                            }

                            $id_temporal = $value->id_paciente;

                        }

                        //Si porcentaje_iva == 0  BASE_0
                        if ($value->porcentaje_iva == 0) {
                            $sum_bas_0 = $sum_bas_0 + $value->subtotal;
                            $acum_bas_0 += $value->subtotal;
                        }
                        //Si porcentaje_iva == 0.12  BASE_IVA
                        if ($value->porcentaje_iva == 0.12) {
                            $sum_bas_iva = $sum_bas_iva + $value->subtotal;
                            $acum_bas_iva += $value->subtotal;
                        }
                        //Suma v_iva
                        //$sum_v_iva = $sum_v_iva + ($value->valor_unitario * $value->cantidad * $value->porcentaje_iva);
                        $sum_v_iva = $sum_v_iva + ($value->iva_unitario * $value->cantidad );
                        //$acum_v_iva += $value->iva;
                        //$acum_v_iva += ($value->valor_unitario * $value->cantidad * $value->porcentaje_iva);
                        $acum_v_iva += ($value->iva_unitario * $value->cantidad);
                        //  GAST_ADM_10
                        //$sum_gasto_amd = $sum_gasto_amd + $value->porcentaje10;
                        $sum_gasto_amd = $sum_gasto_amd + ($value->p10_unitario * $value->cantidad);
                        //$acum_gasto_amd += $value->porcentaje10;
                        $acum_gasto_amd += ($value->p10_unitario * $value->cantidad);

                        //TOTAL_M_IVA
                        $total_m_iva = $sum_bas_0 + $sum_bas_iva + $sum_v_iva + $sum_gasto_amd;

                        /*$nombre_anterior = $value->paciente->apellido1.' '.$value->paciente->apellido2.' '.$value->paciente->nombre1.' '.$value->paciente->nombre2;*/
                        $nombre_anterior = '';
                        if ($value->paciente->apellido1 != 'N/A' && $value->paciente->apellido1 != '(N/A)') {
                            $nombre_anterior = $value->paciente->apellido1;
                        }
                        if ($value->paciente->apellido2 != 'N/A' && $value->paciente->apellido2 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->apellido2;
                        }
                        if ($value->paciente->nombre1 != 'N/A' && $value->paciente->nombre1 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->nombre1;
                        }

                        if ($value->paciente->nombre2 != 'N/A' && $value->paciente->nombre2 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->nombre2;
                        }

                        $nombre_anterior = str_replace($caract_remplazar_1, "", $nombre_anterior);
                        $nombre_anterior = str_replace($caract_remplazar_2, "N", $nombre_anterior);

                        $tipo_seguro_ant = $value->tiposeg;

                    }

                    //Escribo en excel
                    //No
                    if ($nombre_anterior != '') {
                        $sheet->cell('A' . $i, function ($cell) use ($x) {
                            // manipulate the cel
                            $cell->setValue($x);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        //NOMBRE DE PACIENTE/USUARIO
                        $sheet->mergeCells('B' . $i . ':G' . $i);
                        $sheet->cell('B' . $i, function ($cell) use ($nombre_anterior) {
                            // manipulate the cel
                            $cell->setValue($nombre_anterior);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        //TIPO DE SEGURO
                        $sheet->cell('H' . $i, function ($cell) use ($tipo_seguro_ant) {
                            // manipulate the cel
                            $cell->setValue($tipo_seguro_ant);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        //BASE0
                        $sheet->mergeCells('I' . $i . ':J' . $i);
                        $sheet->cell('I' . $i, function ($cell) use ($sum_bas_0) {
                            // manipulate the cel
                            $cell->setValue($sum_bas_0);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        //BASE12
                        $sheet->mergeCells('K' . $i . ':L' . $i);
                        $sheet->cell('K' . $i, function ($cell) use ($sum_bas_iva) {
                            // manipulate the cel
                            $cell->setValue($sum_bas_iva);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        //VALOR IVA 12%
                        $sheet->cell('M' . $i, function ($cell) use ($sum_v_iva) {
                            // manipulate the cel
                            $cell->setValue($sum_v_iva);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        //GASTO ADM
                        $sheet->cell('N' . $i, function ($cell) use ($sum_gasto_amd) {
                            // manipulate the cel
                            $cell->setValue($sum_gasto_amd);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        //SUBTOTAL
                        $sheet->mergeCells('O' . $i . ':P' . $i);
                        $sheet->cell('O' . $i, function ($cell) use ($total_m_iva) {
                            // manipulate the cel
                            $cell->setValue($total_m_iva);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $x++;
                        $i++;

                    }

                    //Fin de Escritura del Excel
                    $base_0          = 0;
                    $sum             = 0;
                    $sum_bas_0       = 0;
                    $sum_bas_iva     = 0;
                    $sum_v_iva       = 0;
                    $sum_gasto_amd   = 0;
                    $total_m_iva     = 0;
                    $nombre_anterior = '';
                    $tipo_seguro_ant = '';
                    //$x++;
                    //$i++;
                }

                //ISSFA Activo
                if (count($archivo_plano_issfa_activo)>0) {

                    $base_0          = 0;
                    $sum             = 0;
                    $sum_bas_0       = 0;
                    $sum_bas_iva     = 0;
                    $sum_v_iva       = 0;
                    $sum_gasto_amd   = 0;
                    $total_m_iva     = 0;
                    $nombre_anterior = '';
                    $tipo_seguro_ant = '';
                    //$total_activo = 0;
                    //$iva_activo = 0;
                    $id_temporal = 0;
                    $contPrueba = 0;
                    foreach ($archivo_plano_issfa_activo as $value) {

                        if ($value->id_paciente != $id_temporal) {

                            if ($id_temporal != 0) {
                                

                                //Escribo en excel
                                //No
                                $sheet->cell('A' . $i, function ($cell) use ($x) {
                                    // manipulate the cel
                                    $cell->setValue($x);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //NOMBRE DE PACIENTE/USUARIO
                                $sheet->mergeCells('B' . $i . ':G' . $i);
                                $sheet->cell('B' . $i, function ($cell) use ($value, $nombre_anterior) {
                                    // manipulate the cel
                                    $cell->setValue($nombre_anterior);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });

                                //TIPO DE SEGURO
                                $sheet->cell('H' . $i, function ($cell) use ($value, $tipo_seguro_ant) {
                                    // manipulate the cel

                                    $cell->setValue($tipo_seguro_ant);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //BASE0
                                $sheet->mergeCells('I' . $i . ':J' . $i);
                                $sheet->cell('I' . $i, function ($cell) use ($sum_bas_0) {
                                    // manipulate the cel
                                    $cell->setValue($sum_bas_0);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //BASE12
                                $sheet->mergeCells('K' . $i . ':L' . $i);
                                $sheet->cell('K' . $i, function ($cell) use ($sum_bas_iva) {
                                    // manipulate the cel
                                    $cell->setValue($sum_bas_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //VALOR IVA 12%
                                $sheet->cell('M' . $i, function ($cell) use ($sum_v_iva) {
                                    // manipulate the cel
                                    $cell->setValue($sum_v_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //GASTO ADM
                                $sheet->cell('N' . $i, function ($cell) use ($sum_gasto_amd) {
                                    // manipulate the cel
                                    $cell->setValue($sum_gasto_amd);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //SUBTOTAL
                                $sheet->mergeCells('O' . $i . ':P' . $i);
                                $sheet->cell('O' . $i, function ($cell) use ($total_m_iva) {
                                    // manipulate the cel
                                    $cell->setValue($total_m_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });

                                $total_activo += $total_m_iva;
                                $iva_activo += $sum_v_iva;
                                $sub_total_activo = $total_activo - $iva_activo;

                                //Fin de Escritura del Excel
                                $base_0          = 0;
                                $sum             = 0;
                                $sum_bas_0       = 0;
                                $sum_bas_iva     = 0;
                                $sum_v_iva       = 0;
                                $sum_gasto_amd   = 0;
                                $total_m_iva     = 0;
                                $nombre_anterior = '';
                                $tipo_seguro_ant = '';
                                $x++;
                                $i++;

                            }

                            $id_temporal = $value->id_paciente;

                        }

                        //Si porcentaje_iva == 0  BASE_0
                        if ($value->porcentaje_iva == 0) {
                            $sum_bas_0 = $sum_bas_0 + $value->subtotal;
                            $acum_bas_0 += $value->subtotal;
                        }
                        //Si porcentaje_iva == 0.12  BASE_IVA
                        if ($value->porcentaje_iva == 0.12) {
                            $sum_bas_iva = $sum_bas_iva + $value->subtotal;
                            $acum_bas_iva += $value->subtotal;
                        }
                        //Suma v_iva
                        //$sum_v_iva = $sum_v_iva + ($value->valor_unitario * $value->cantidad * $value->porcentaje_iva);
                        $sum_v_iva = $sum_v_iva + ($value->iva_unitario * $value->cantidad );
                        //$acum_v_iva += $value->iva;
                        //$acum_v_iva += ($value->valor_unitario * $value->cantidad * $value->porcentaje_iva);
                        $acum_v_iva += ($value->iva_unitario * $value->cantidad);
                        //  GAST_ADM_10
                        //$sum_gasto_amd = $sum_gasto_amd + $value->porcentaje10;
                        $sum_gasto_amd = $sum_gasto_amd + ($value->p10_unitario * $value->cantidad);
                        //$acum_gasto_amd += $value->porcentaje10;
                        $acum_gasto_amd += ($value->p10_unitario * $value->cantidad);

                        //TOTAL_M_IVA
                        $total_m_iva = $sum_bas_0 + $sum_bas_iva + $sum_v_iva + $sum_gasto_amd;

                        /*$nombre_anterior = $value->paciente->apellido1.' '.$value->paciente->apellido2.' '.$value->paciente->nombre1.' '.$value->paciente->nombre2;*/
                        $nombre_anterior = '';
                        if ($value->paciente->apellido1 != 'N/A' && $value->paciente->apellido1 != '(N/A)') {
                            $nombre_anterior = $value->paciente->apellido1;
                        }
                        if ($value->paciente->apellido2 != 'N/A' && $value->paciente->apellido2 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->apellido2;
                        }
                        if ($value->paciente->nombre1 != 'N/A' && $value->paciente->nombre1 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->nombre1;
                        }

                        if ($value->paciente->nombre2 != 'N/A' && $value->paciente->nombre2 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->nombre2;
                        }

                        $nombre_anterior = str_replace($caract_remplazar_1, "", $nombre_anterior);
                        $nombre_anterior = str_replace($caract_remplazar_2, "N", $nombre_anterior);

                        $tipo_seguro_ant = $value->tiposeg;

                        if($idUsuario == '0950457978'){
                            if($contPrueba==4){
                                //dd($nombre_anterior,$archivo_plano_isspol_montepio, $archivo_plano_issfa_activo->count(),$value);
                            }
                        }

                        $contPrueba++;
                    }
                    //Escribo en excel
                    //No
                    $sheet->cell('A' . $i, function ($cell) use ($x) {
                        // manipulate the cel
                        $cell->setValue($x);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    //NOMBRE DE PACIENTE/USUARIO
                    $sheet->mergeCells('B' . $i . ':G' . $i);
                    $sheet->cell('B' . $i, function ($cell) use ($nombre_anterior) {
                        // manipulate the cel
                        $cell->setValue($nombre_anterior);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    //TIPO DE SEGURO
                    $sheet->cell('H' . $i, function ($cell) use ($tipo_seguro_ant) {
                        // manipulate the cel

                        $cell->setValue($tipo_seguro_ant);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    //BASE0
                    $sheet->mergeCells('I' . $i . ':J' . $i);
                    $sheet->cell('I' . $i, function ($cell) use ($sum_bas_0) {
                        // manipulate the cel
                        $cell->setValue($sum_bas_0);
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    //BASE12
                    $sheet->mergeCells('K' . $i . ':L' . $i);
                    $sheet->cell('K' . $i, function ($cell) use ($sum_bas_iva) {
                        // manipulate the cel
                        $cell->setValue($sum_bas_iva);
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    //VALOR IVA 12%
                    $sheet->cell('M' . $i, function ($cell) use ($sum_v_iva) {
                        // manipulate the cel
                        $cell->setValue($sum_v_iva);
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    //GASTO ADM
                    $sheet->cell('N' . $i, function ($cell) use ($sum_gasto_amd) {
                        // manipulate the cel
                        $cell->setValue($sum_gasto_amd);
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    //SUBTOTAL
                    $sheet->mergeCells('O' . $i . ':P' . $i);
                    $sheet->cell('O' . $i, function ($cell) use ($total_m_iva) {
                        // manipulate the cel
                        $cell->setValue($total_m_iva);
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    //Fin de Escritura del Excel
                    $base_0          = 0;
                    $sum             = 0;
                    $sum_bas_0       = 0;
                    $sum_bas_iva     = 0;
                    $sum_v_iva       = 0;
                    $sum_gasto_amd   = 0;
                    $total_m_iva     = 0;
                    $nombre_anterior = '';
                    $tipo_seguro_ant = '';
                    $x++;
                    $i++;
                }
                
                //ISSFA Jubilado
                if (count($archivo_plano_issfa_jubilado)>0) {

                    $base_0          = 0;
                    $sum             = 0;
                    $sum_bas_0       = 0;
                    $sum_bas_iva     = 0;
                    $sum_v_iva       = 0;
                    $sum_gasto_amd   = 0;
                    $total_m_iva     = 0;
                    $nombre_anterior = '';
                    $tipo_seguro_ant = '';
                    //$total_activo = 0;
                    //$iva_activo = 0;
                    $id_temporal = 0;
                   
                    foreach ($archivo_plano_issfa_jubilado as $value) {
                        

                        if ($value->id_paciente != $id_temporal) {

                            if ($id_temporal != 0) {
                                

                                //Escribo en excel
                                //No
                                $sheet->cell('A' . $i, function ($cell) use ($x) {
                                    // manipulate the cel
                                    $cell->setValue($x);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //NOMBRE DE PACIENTE/USUARIO
                                $sheet->mergeCells('B' . $i . ':G' . $i);
                                $sheet->cell('B' . $i, function ($cell) use ($value, $nombre_anterior) {
                                    // manipulate the cel
                                    $cell->setValue($nombre_anterior);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });

                                //TIPO DE SEGURO
                                $sheet->cell('H' . $i, function ($cell) use ($value, $tipo_seguro_ant) {
                                    // manipulate the cel

                                    $cell->setValue($tipo_seguro_ant);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //BASE0
                                $sheet->mergeCells('I' . $i . ':J' . $i);
                                $sheet->cell('I' . $i, function ($cell) use ($sum_bas_0) {
                                    // manipulate the cel
                                    $cell->setValue($sum_bas_0);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //BASE12
                                $sheet->mergeCells('K' . $i . ':L' . $i);
                                $sheet->cell('K' . $i, function ($cell) use ($sum_bas_iva) {
                                    // manipulate the cel
                                    $cell->setValue($sum_bas_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //VALOR IVA 12%
                                $sheet->cell('M' . $i, function ($cell) use ($sum_v_iva) {
                                    // manipulate the cel
                                    $cell->setValue($sum_v_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //GASTO ADM
                                $sheet->cell('N' . $i, function ($cell) use ($sum_gasto_amd) {
                                    // manipulate the cel
                                    $cell->setValue($sum_gasto_amd);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //SUBTOTAL
                                $sheet->mergeCells('O' . $i . ':P' . $i);
                                $sheet->cell('O' . $i, function ($cell) use ($total_m_iva) {
                                    // manipulate the cel
                                    $cell->setValue($total_m_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });

                                $total_activo += $total_m_iva;
                                $iva_activo += $sum_v_iva;
                                $sub_total_activo = $total_activo - $iva_activo;

                                //Fin de Escritura del Excel
                                $base_0          = 0;
                                $sum             = 0;
                                $sum_bas_0       = 0;
                                $sum_bas_iva     = 0;
                                $sum_v_iva       = 0;
                                $sum_gasto_amd   = 0;
                                $total_m_iva     = 0;
                                $nombre_anterior = '';
                                $tipo_seguro_ant = '';
                                $x++;
                                $i++;

                            }

                            $id_temporal = $value->id_paciente;

                        }

                        //Si porcentaje_iva == 0  BASE_0
                        if ($value->porcentaje_iva == 0) {
                            $sum_bas_0 = $sum_bas_0 + $value->subtotal;
                            $acum_bas_0 += $value->subtotal;
                        }
                        //Si porcentaje_iva == 0.12  BASE_IVA
                        if ($value->porcentaje_iva == 0.12) {
                            $sum_bas_iva = $sum_bas_iva + $value->subtotal;
                            $acum_bas_iva += $value->subtotal;
                        }
                        //Suma v_iva
                        //$sum_v_iva = $sum_v_iva + ($value->valor_unitario * $value->cantidad * $value->porcentaje_iva);
                        $sum_v_iva = $sum_v_iva + ($value->iva_unitario * $value->cantidad );
                        //$acum_v_iva += $value->iva;
                        //$acum_v_iva += ($value->valor_unitario * $value->cantidad * $value->porcentaje_iva);
                        $acum_v_iva += ($value->iva_unitario * $value->cantidad);
                        //  GAST_ADM_10
                        //$sum_gasto_amd = $sum_gasto_amd + $value->porcentaje10;
                        $sum_gasto_amd = $sum_gasto_amd + ($value->p10_unitario * $value->cantidad);
                        //$acum_gasto_amd += $value->porcentaje10;
                        $acum_gasto_amd += ($value->p10_unitario * $value->cantidad);

                        //TOTAL_M_IVA
                        $total_m_iva = $sum_bas_0 + $sum_bas_iva + $sum_v_iva + $sum_gasto_amd;

                        /*$nombre_anterior = $value->paciente->apellido1.' '.$value->paciente->apellido2.' '.$value->paciente->nombre1.' '.$value->paciente->nombre2;*/
                        $nombre_anterior = '';
                        if ($value->paciente->apellido1 != 'N/A' && $value->paciente->apellido1 != '(N/A)') {
                            $nombre_anterior = $value->paciente->apellido1;
                        }
                        if ($value->paciente->apellido2 != 'N/A' && $value->paciente->apellido2 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->apellido2;
                        }
                        if ($value->paciente->nombre1 != 'N/A' && $value->paciente->nombre1 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->nombre1;
                        }

                        if ($value->paciente->nombre2 != 'N/A' && $value->paciente->nombre2 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->nombre2;
                        }

                        $nombre_anterior = str_replace($caract_remplazar_1, "", $nombre_anterior);
                        $nombre_anterior = str_replace($caract_remplazar_2, "N", $nombre_anterior);

                        $tipo_seguro_ant = $value->tiposeg;

                    }
                    //Escribo en excel
                    //No
                    $sheet->cell('A' . $i, function ($cell) use ($x) {
                        // manipulate the cel
                        $cell->setValue($x);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    //NOMBRE DE PACIENTE/USUARIO
                    $sheet->mergeCells('B' . $i . ':G' . $i);
                    $sheet->cell('B' . $i, function ($cell) use ($nombre_anterior) {
                        // manipulate the cel
                        $cell->setValue($nombre_anterior);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    //TIPO DE SEGURO
                    $sheet->cell('H' . $i, function ($cell) use ($tipo_seguro_ant) {
                        // manipulate the cel

                        $cell->setValue($tipo_seguro_ant);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    //BASE0
                    $sheet->mergeCells('I' . $i . ':J' . $i);
                    $sheet->cell('I' . $i, function ($cell) use ($sum_bas_0) {
                        // manipulate the cel
                        $cell->setValue($sum_bas_0);
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    //BASE12
                    $sheet->mergeCells('K' . $i . ':L' . $i);
                    $sheet->cell('K' . $i, function ($cell) use ($sum_bas_iva) {
                        // manipulate the cel
                        $cell->setValue($sum_bas_iva);
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    //VALOR IVA 12%
                    $sheet->cell('M' . $i, function ($cell) use ($sum_v_iva) {
                        // manipulate the cel
                        $cell->setValue($sum_v_iva);
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    //GASTO ADM
                    $sheet->cell('N' . $i, function ($cell) use ($sum_gasto_amd) {
                        // manipulate the cel
                        $cell->setValue($sum_gasto_amd);
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    //SUBTOTAL
                    $sheet->mergeCells('O' . $i . ':P' . $i);
                    $sheet->cell('O' . $i, function ($cell) use ($total_m_iva) {
                        // manipulate the cel
                        $cell->setValue($total_m_iva);
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    //Fin de Escritura del Excel
                    $base_0          = 0;
                    $sum             = 0;
                    $sum_bas_0       = 0;
                    $sum_bas_iva     = 0;
                    $sum_v_iva       = 0;
                    $sum_gasto_amd   = 0;
                    $total_m_iva     = 0;
                    $nombre_anterior = '';
                    $tipo_seguro_ant = '';
                    $x++;
                    $i++;
                }

                //ISSFA Montepio
                if (count($archivo_plano_issfa_montepio)>0) {

                    $base_0          = 0;
                    $sum             = 0;
                    $sum_bas_0       = 0;
                    $sum_bas_iva     = 0;
                    $sum_v_iva       = 0;
                    $sum_gasto_amd   = 0;
                    $total_m_iva     = 0;
                    $nombre_anterior = '';
                    $tipo_seguro_ant = '';
                    //$total_activo = 0;
                    //$iva_activo = 0;
                    $id_temporal = 0;
                   
                    foreach ($archivo_plano_issfa_montepio as $value) {
                        

                        if ($value->id_paciente != $id_temporal) {

                            if ($id_temporal != 0) {
                                

                                //Escribo en excel
                                //No
                                $sheet->cell('A' . $i, function ($cell) use ($x) {
                                    // manipulate the cel
                                    $cell->setValue($x);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //NOMBRE DE PACIENTE/USUARIO
                                $sheet->mergeCells('B' . $i . ':G' . $i);
                                $sheet->cell('B' . $i, function ($cell) use ($value, $nombre_anterior) {
                                    // manipulate the cel
                                    $cell->setValue($nombre_anterior);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });

                                //TIPO DE SEGURO
                                $sheet->cell('H' . $i, function ($cell) use ($value, $tipo_seguro_ant) {
                                    // manipulate the cel

                                    $cell->setValue($tipo_seguro_ant);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //BASE0
                                $sheet->mergeCells('I' . $i . ':J' . $i);
                                $sheet->cell('I' . $i, function ($cell) use ($sum_bas_0) {
                                    // manipulate the cel
                                    $cell->setValue($sum_bas_0);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //BASE12
                                $sheet->mergeCells('K' . $i . ':L' . $i);
                                $sheet->cell('K' . $i, function ($cell) use ($sum_bas_iva) {
                                    // manipulate the cel
                                    $cell->setValue($sum_bas_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //VALOR IVA 12%
                                $sheet->cell('M' . $i, function ($cell) use ($sum_v_iva) {
                                    // manipulate the cel
                                    $cell->setValue($sum_v_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //GASTO ADM
                                $sheet->cell('N' . $i, function ($cell) use ($sum_gasto_amd) {
                                    // manipulate the cel
                                    $cell->setValue($sum_gasto_amd);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //SUBTOTAL
                                $sheet->mergeCells('O' . $i . ':P' . $i);
                                $sheet->cell('O' . $i, function ($cell) use ($total_m_iva) {
                                    // manipulate the cel
                                    $cell->setValue($total_m_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });

                                $total_activo += $total_m_iva;
                                $iva_activo += $sum_v_iva;
                                $sub_total_activo = $total_activo - $iva_activo;

                                //Fin de Escritura del Excel
                                $base_0          = 0;
                                $sum             = 0;
                                $sum_bas_0       = 0;
                                $sum_bas_iva     = 0;
                                $sum_v_iva       = 0;
                                $sum_gasto_amd   = 0;
                                $total_m_iva     = 0;
                                $nombre_anterior = '';
                                $tipo_seguro_ant = '';
                                $x++;
                                $i++;

                            }

                            $id_temporal = $value->id_paciente;

                        }

                        //Si porcentaje_iva == 0  BASE_0
                        if ($value->porcentaje_iva == 0) {
                            $sum_bas_0 = $sum_bas_0 + $value->subtotal;
                            $acum_bas_0 += $value->subtotal;
                        }
                        //Si porcentaje_iva == 0.12  BASE_IVA
                        if ($value->porcentaje_iva == 0.12) {
                            $sum_bas_iva = $sum_bas_iva + $value->subtotal;
                            $acum_bas_iva += $value->subtotal;
                        }
                        //Suma v_iva
                        //$sum_v_iva = $sum_v_iva + ($value->valor_unitario * $value->cantidad * $value->porcentaje_iva);
                        $sum_v_iva = $sum_v_iva + ($value->iva_unitario * $value->cantidad );
                        //$acum_v_iva += $value->iva;
                        //$acum_v_iva += ($value->valor_unitario * $value->cantidad * $value->porcentaje_iva);
                        $acum_v_iva += ($value->iva_unitario * $value->cantidad);
                        //  GAST_ADM_10
                        //$sum_gasto_amd = $sum_gasto_amd + $value->porcentaje10;
                        $sum_gasto_amd = $sum_gasto_amd + ($value->p10_unitario * $value->cantidad);
                        //$acum_gasto_amd += $value->porcentaje10;
                        $acum_gasto_amd += ($value->p10_unitario * $value->cantidad);

                        //TOTAL_M_IVA
                        $total_m_iva = $sum_bas_0 + $sum_bas_iva + $sum_v_iva + $sum_gasto_amd;

                        /*$nombre_anterior = $value->paciente->apellido1.' '.$value->paciente->apellido2.' '.$value->paciente->nombre1.' '.$value->paciente->nombre2;*/
                        $nombre_anterior = '';
                        if ($value->paciente->apellido1 != 'N/A' && $value->paciente->apellido1 != '(N/A)') {
                            $nombre_anterior = $value->paciente->apellido1;
                        }
                        if ($value->paciente->apellido2 != 'N/A' && $value->paciente->apellido2 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->apellido2;
                        }
                        if ($value->paciente->nombre1 != 'N/A' && $value->paciente->nombre1 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->nombre1;
                        }

                        if ($value->paciente->nombre2 != 'N/A' && $value->paciente->nombre2 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->nombre2;
                        }

                        $nombre_anterior = str_replace($caract_remplazar_1, "", $nombre_anterior);
                        $nombre_anterior = str_replace($caract_remplazar_2, "N", $nombre_anterior);

                        $tipo_seguro_ant = $value->tiposeg;

                    }
                    //Escribo en excel
                    //No
                    $sheet->cell('A' . $i, function ($cell) use ($x) {
                        // manipulate the cel
                        $cell->setValue($x);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    //NOMBRE DE PACIENTE/USUARIO
                    $sheet->mergeCells('B' . $i . ':G' . $i);
                    $sheet->cell('B' . $i, function ($cell) use ($nombre_anterior) {
                        // manipulate the cel
                        $cell->setValue($nombre_anterior);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    //TIPO DE SEGURO
                    $sheet->cell('H' . $i, function ($cell) use ($tipo_seguro_ant) {
                        // manipulate the cel

                        $cell->setValue($tipo_seguro_ant);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    //BASE0
                    $sheet->mergeCells('I' . $i . ':J' . $i);
                    $sheet->cell('I' . $i, function ($cell) use ($sum_bas_0) {
                        // manipulate the cel
                        $cell->setValue($sum_bas_0);
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    //BASE12
                    $sheet->mergeCells('K' . $i . ':L' . $i);
                    $sheet->cell('K' . $i, function ($cell) use ($sum_bas_iva) {
                        // manipulate the cel
                        $cell->setValue($sum_bas_iva);
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    //VALOR IVA 12%
                    $sheet->cell('M' . $i, function ($cell) use ($sum_v_iva) {
                        // manipulate the cel
                        $cell->setValue($sum_v_iva);
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    //GASTO ADM
                    $sheet->cell('N' . $i, function ($cell) use ($sum_gasto_amd) {
                        // manipulate the cel
                        $cell->setValue($sum_gasto_amd);
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    //SUBTOTAL
                    $sheet->mergeCells('O' . $i . ':P' . $i);
                    $sheet->cell('O' . $i, function ($cell) use ($total_m_iva) {
                        // manipulate the cel
                        $cell->setValue($total_m_iva);
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    //Fin de Escritura del Excel
                    $base_0          = 0;
                    $sum             = 0;
                    $sum_bas_0       = 0;
                    $sum_bas_iva     = 0;
                    $sum_v_iva       = 0;
                    $sum_gasto_amd   = 0;
                    $total_m_iva     = 0;
                    $nombre_anterior = '';
                    $tipo_seguro_ant = '';
                    $x++;
                    $i++;
                }
                
                //ISSPOL Activo
                 if (count($archivo_plano_isspol_activo)>0) {

                    $base_0          = 0;
                    $sum             = 0;
                    $sum_bas_0       = 0;
                    $sum_bas_iva     = 0;
                    $sum_v_iva       = 0;
                    $sum_gasto_amd   = 0;
                    $total_m_iva     = 0;
                    $nombre_anterior = '';
                    $tipo_seguro_ant = '';
                    //$total_activo = 0;
                    //$iva_activo = 0;
                    $id_temporal = 0;
                   
                    foreach ($archivo_plano_isspol_activo as $value) {
                        

                        if ($value->id_paciente != $id_temporal) {

                            if ($id_temporal != 0) {
                                

                                //Escribo en excel
                                //No
                                $sheet->cell('A' . $i, function ($cell) use ($x) {
                                    // manipulate the cel
                                    $cell->setValue($x);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //NOMBRE DE PACIENTE/USUARIO
                                $sheet->mergeCells('B' . $i . ':G' . $i);
                                $sheet->cell('B' . $i, function ($cell) use ($value, $nombre_anterior) {
                                    // manipulate the cel
                                    $cell->setValue($nombre_anterior);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });

                                //TIPO DE SEGURO
                                $sheet->cell('H' . $i, function ($cell) use ($value, $tipo_seguro_ant) {
                                    // manipulate the cel

                                    $cell->setValue($tipo_seguro_ant);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //BASE0
                                $sheet->mergeCells('I' . $i . ':J' . $i);
                                $sheet->cell('I' . $i, function ($cell) use ($sum_bas_0) {
                                    // manipulate the cel
                                    $cell->setValue($sum_bas_0);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //BASE12
                                $sheet->mergeCells('K' . $i . ':L' . $i);
                                $sheet->cell('K' . $i, function ($cell) use ($sum_bas_iva) {
                                    // manipulate the cel
                                    $cell->setValue($sum_bas_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //VALOR IVA 12%
                                $sheet->cell('M' . $i, function ($cell) use ($sum_v_iva) {
                                    // manipulate the cel
                                    $cell->setValue($sum_v_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //GASTO ADM
                                $sheet->cell('N' . $i, function ($cell) use ($sum_gasto_amd) {
                                    // manipulate the cel
                                    $cell->setValue($sum_gasto_amd);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //SUBTOTAL
                                $sheet->mergeCells('O' . $i . ':P' . $i);
                                $sheet->cell('O' . $i, function ($cell) use ($total_m_iva) {
                                    // manipulate the cel
                                    $cell->setValue($total_m_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });

                                $total_activo += $total_m_iva;
                                $iva_activo += $sum_v_iva;
                                $sub_total_activo = $total_activo - $iva_activo;

                                //Fin de Escritura del Excel
                                $base_0          = 0;
                                $sum             = 0;
                                $sum_bas_0       = 0;
                                $sum_bas_iva     = 0;
                                $sum_v_iva       = 0;
                                $sum_gasto_amd   = 0;
                                $total_m_iva     = 0;
                                $nombre_anterior = '';
                                $tipo_seguro_ant = '';
                                $x++;
                                $i++;

                            }

                            $id_temporal = $value->id_paciente;

                        }

                        //Si porcentaje_iva == 0  BASE_0
                        if ($value->porcentaje_iva == 0) {
                            $sum_bas_0 = $sum_bas_0 + $value->subtotal;
                            $acum_bas_0 += $value->subtotal;
                        }
                        //Si porcentaje_iva == 0.12  BASE_IVA
                        if ($value->porcentaje_iva == 0.12) {
                            $sum_bas_iva = $sum_bas_iva + $value->subtotal;
                            $acum_bas_iva += $value->subtotal;
                        }
                        //Suma v_iva
                        //$sum_v_iva = $sum_v_iva + ($value->valor_unitario * $value->cantidad * $value->porcentaje_iva);
                        $sum_v_iva = $sum_v_iva + ($value->iva_unitario * $value->cantidad );
                        //$acum_v_iva += $value->iva;
                        //$acum_v_iva += ($value->valor_unitario * $value->cantidad * $value->porcentaje_iva);
                        $acum_v_iva += ($value->iva_unitario * $value->cantidad);
                        //  GAST_ADM_10
                        //$sum_gasto_amd = $sum_gasto_amd + $value->porcentaje10;
                        $sum_gasto_amd = $sum_gasto_amd + ($value->p10_unitario * $value->cantidad);
                        //$acum_gasto_amd += $value->porcentaje10;
                        $acum_gasto_amd += ($value->p10_unitario * $value->cantidad);

                        //TOTAL_M_IVA
                        $total_m_iva = $sum_bas_0 + $sum_bas_iva + $sum_v_iva + $sum_gasto_amd;

                        /*$nombre_anterior = $value->paciente->apellido1.' '.$value->paciente->apellido2.' '.$value->paciente->nombre1.' '.$value->paciente->nombre2;*/
                        $nombre_anterior = '';
                        if ($value->paciente->apellido1 != 'N/A' && $value->paciente->apellido1 != '(N/A)') {
                            $nombre_anterior = $value->paciente->apellido1;
                        }
                        if ($value->paciente->apellido2 != 'N/A' && $value->paciente->apellido2 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->apellido2;
                        }
                        if ($value->paciente->nombre1 != 'N/A' && $value->paciente->nombre1 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->nombre1;
                        }

                        if ($value->paciente->nombre2 != 'N/A' && $value->paciente->nombre2 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->nombre2;
                        }

                        $nombre_anterior = str_replace($caract_remplazar_1, "", $nombre_anterior);
                        $nombre_anterior = str_replace($caract_remplazar_2, "N", $nombre_anterior);

                        $tipo_seguro_ant = $value->tiposeg;

                    }
                    //Escribo en excel
                    //No
                    $sheet->cell('A' . $i, function ($cell) use ($x) {
                        // manipulate the cel
                        $cell->setValue($x);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    //NOMBRE DE PACIENTE/USUARIO
                    $sheet->mergeCells('B' . $i . ':G' . $i);
                    $sheet->cell('B' . $i, function ($cell) use ($nombre_anterior) {
                        // manipulate the cel
                        $cell->setValue($nombre_anterior);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    //TIPO DE SEGURO
                    $sheet->cell('H' . $i, function ($cell) use ($tipo_seguro_ant) {
                        // manipulate the cel

                        $cell->setValue($tipo_seguro_ant);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    //BASE0
                    $sheet->mergeCells('I' . $i . ':J' . $i);
                    $sheet->cell('I' . $i, function ($cell) use ($sum_bas_0) {
                        // manipulate the cel
                        $cell->setValue($sum_bas_0);
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    //BASE12
                    $sheet->mergeCells('K' . $i . ':L' . $i);
                    $sheet->cell('K' . $i, function ($cell) use ($sum_bas_iva) {
                        // manipulate the cel
                        $cell->setValue($sum_bas_iva);
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    //VALOR IVA 12%
                    $sheet->cell('M' . $i, function ($cell) use ($sum_v_iva) {
                        // manipulate the cel
                        $cell->setValue($sum_v_iva);
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    //GASTO ADM
                    $sheet->cell('N' . $i, function ($cell) use ($sum_gasto_amd) {
                        // manipulate the cel
                        $cell->setValue($sum_gasto_amd);
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    //SUBTOTAL
                    $sheet->mergeCells('O' . $i . ':P' . $i);
                    $sheet->cell('O' . $i, function ($cell) use ($total_m_iva) {
                        // manipulate the cel
                        $cell->setValue($total_m_iva);
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    //Fin de Escritura del Excel
                    $base_0          = 0;
                    $sum             = 0;
                    $sum_bas_0       = 0;
                    $sum_bas_iva     = 0;
                    $sum_v_iva       = 0;
                    $sum_gasto_amd   = 0;
                    $total_m_iva     = 0;
                    $nombre_anterior = '';
                    $tipo_seguro_ant = '';
                    $x++;
                    $i++;
                }

                //ISSPOL Jubilado
                if (count($archivo_plano_isspol_jubilado)>0) {

                    $base_0          = 0;
                    $sum             = 0;
                    $sum_bas_0       = 0;
                    $sum_bas_iva     = 0;
                    $sum_v_iva       = 0;
                    $sum_gasto_amd   = 0;
                    $total_m_iva     = 0;
                    $nombre_anterior = '';
                    $tipo_seguro_ant = '';
                    //$total_activo = 0;
                    //$iva_activo = 0;
                    $id_temporal = 0;
                   
                    foreach ($archivo_plano_isspol_jubilado as $value) {
                        

                        if ($value->id_paciente != $id_temporal) {

                            if ($id_temporal != 0) {
                                

                                //Escribo en excel
                                //No
                                $sheet->cell('A' . $i, function ($cell) use ($x) {
                                    // manipulate the cel
                                    $cell->setValue($x);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //NOMBRE DE PACIENTE/USUARIO
                                $sheet->mergeCells('B' . $i . ':G' . $i);
                                $sheet->cell('B' . $i, function ($cell) use ($value, $nombre_anterior) {
                                    // manipulate the cel
                                    $cell->setValue($nombre_anterior);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });

                                //TIPO DE SEGURO
                                $sheet->cell('H' . $i, function ($cell) use ($value, $tipo_seguro_ant) {
                                    // manipulate the cel

                                    $cell->setValue($tipo_seguro_ant);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //BASE0
                                $sheet->mergeCells('I' . $i . ':J' . $i);
                                $sheet->cell('I' . $i, function ($cell) use ($sum_bas_0) {
                                    // manipulate the cel
                                    $cell->setValue($sum_bas_0);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //BASE12
                                $sheet->mergeCells('K' . $i . ':L' . $i);
                                $sheet->cell('K' . $i, function ($cell) use ($sum_bas_iva) {
                                    // manipulate the cel
                                    $cell->setValue($sum_bas_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //VALOR IVA 12%
                                $sheet->cell('M' . $i, function ($cell) use ($sum_v_iva) {
                                    // manipulate the cel
                                    $cell->setValue($sum_v_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //GASTO ADM
                                $sheet->cell('N' . $i, function ($cell) use ($sum_gasto_amd) {
                                    // manipulate the cel
                                    $cell->setValue($sum_gasto_amd);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //SUBTOTAL
                                $sheet->mergeCells('O' . $i . ':P' . $i);
                                $sheet->cell('O' . $i, function ($cell) use ($total_m_iva) {
                                    // manipulate the cel
                                    $cell->setValue($total_m_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });

                                $total_activo += $total_m_iva;
                                $iva_activo += $sum_v_iva;
                                $sub_total_activo = $total_activo - $iva_activo;

                                //Fin de Escritura del Excel
                                $base_0          = 0;
                                $sum             = 0;
                                $sum_bas_0       = 0;
                                $sum_bas_iva     = 0;
                                $sum_v_iva       = 0;
                                $sum_gasto_amd   = 0;
                                $total_m_iva     = 0;
                                $nombre_anterior = '';
                                $tipo_seguro_ant = '';
                                $x++;
                                $i++;

                            }

                            $id_temporal = $value->id_paciente;

                        }

                        //Si porcentaje_iva == 0  BASE_0
                        if ($value->porcentaje_iva == 0) {
                            $sum_bas_0 = $sum_bas_0 + $value->subtotal;
                            $acum_bas_0 += $value->subtotal;
                        }
                        //Si porcentaje_iva == 0.12  BASE_IVA
                        if ($value->porcentaje_iva == 0.12) {
                            $sum_bas_iva = $sum_bas_iva + $value->subtotal;
                            $acum_bas_iva += $value->subtotal;
                        }
                        //Suma v_iva
                        //$sum_v_iva = $sum_v_iva + ($value->valor_unitario * $value->cantidad * $value->porcentaje_iva);
                        $sum_v_iva = $sum_v_iva + ($value->iva_unitario * $value->cantidad );
                        //$acum_v_iva += $value->iva;
                        //$acum_v_iva += ($value->valor_unitario * $value->cantidad * $value->porcentaje_iva);
                        $acum_v_iva += ($value->iva_unitario * $value->cantidad);
                        //  GAST_ADM_10
                        //$sum_gasto_amd = $sum_gasto_amd + $value->porcentaje10;
                        $sum_gasto_amd = $sum_gasto_amd + ($value->p10_unitario * $value->cantidad);
                        //$acum_gasto_amd += $value->porcentaje10;
                        $acum_gasto_amd += ($value->p10_unitario * $value->cantidad);

                        //TOTAL_M_IVA
                        $total_m_iva = $sum_bas_0 + $sum_bas_iva + $sum_v_iva + $sum_gasto_amd;

                        /*$nombre_anterior = $value->paciente->apellido1.' '.$value->paciente->apellido2.' '.$value->paciente->nombre1.' '.$value->paciente->nombre2;*/
                        $nombre_anterior = '';
                        if ($value->paciente->apellido1 != 'N/A' && $value->paciente->apellido1 != '(N/A)') {
                            $nombre_anterior = $value->paciente->apellido1;
                        }
                        if ($value->paciente->apellido2 != 'N/A' && $value->paciente->apellido2 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->apellido2;
                        }
                        if ($value->paciente->nombre1 != 'N/A' && $value->paciente->nombre1 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->nombre1;
                        }

                        if ($value->paciente->nombre2 != 'N/A' && $value->paciente->nombre2 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->nombre2;
                        }

                        $nombre_anterior = str_replace($caract_remplazar_1, "", $nombre_anterior);
                        $nombre_anterior = str_replace($caract_remplazar_2, "N", $nombre_anterior);

                        $tipo_seguro_ant = $value->tiposeg;

                    }
                    //Escribo en excel
                    //No
                    $sheet->cell('A' . $i, function ($cell) use ($x) {
                        // manipulate the cel
                        $cell->setValue($x);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    //NOMBRE DE PACIENTE/USUARIO
                    $sheet->mergeCells('B' . $i . ':G' . $i);
                    $sheet->cell('B' . $i, function ($cell) use ($nombre_anterior) {
                        // manipulate the cel
                        $cell->setValue($nombre_anterior);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    //TIPO DE SEGURO
                    $sheet->cell('H' . $i, function ($cell) use ($tipo_seguro_ant) {
                        // manipulate the cel

                        $cell->setValue($tipo_seguro_ant);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    //BASE0
                    $sheet->mergeCells('I' . $i . ':J' . $i);
                    $sheet->cell('I' . $i, function ($cell) use ($sum_bas_0) {
                        // manipulate the cel
                        $cell->setValue($sum_bas_0);
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    //BASE12
                    $sheet->mergeCells('K' . $i . ':L' . $i);
                    $sheet->cell('K' . $i, function ($cell) use ($sum_bas_iva) {
                        // manipulate the cel
                        $cell->setValue($sum_bas_iva);
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    //VALOR IVA 12%
                    $sheet->cell('M' . $i, function ($cell) use ($sum_v_iva) {
                        // manipulate the cel
                        $cell->setValue($sum_v_iva);
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    //GASTO ADM
                    $sheet->cell('N' . $i, function ($cell) use ($sum_gasto_amd) {
                        // manipulate the cel
                        $cell->setValue($sum_gasto_amd);
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    //SUBTOTAL
                    $sheet->mergeCells('O' . $i . ':P' . $i);
                    $sheet->cell('O' . $i, function ($cell) use ($total_m_iva) {
                        // manipulate the cel
                        $cell->setValue($total_m_iva);
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    //Fin de Escritura del Excel
                    $base_0          = 0;
                    $sum             = 0;
                    $sum_bas_0       = 0;
                    $sum_bas_iva     = 0;
                    $sum_v_iva       = 0;
                    $sum_gasto_amd   = 0;
                    $total_m_iva     = 0;
                    $nombre_anterior = '';
                    $tipo_seguro_ant = '';
                    $x++;
                    $i++;
                }

                //ISSPOL Montepio
                if (count($archivo_plano_isspol_montepio)>0) {

                    $base_0          = 0;
                    $sum             = 0;
                    $sum_bas_0       = 0;
                    $sum_bas_iva     = 0;
                    $sum_v_iva       = 0;
                    $sum_gasto_amd   = 0;
                    $total_m_iva     = 0;
                    $nombre_anterior = '';
                    $tipo_seguro_ant = '';
                    //$total_activo = 0;
                    //$iva_activo = 0;
                    $id_temporal = 0;
                   
                    foreach ($archivo_plano_isspol_montepio as $value) {
                        

                        if ($value->id_paciente != $id_temporal) {

                            if ($id_temporal != 0) {
                                

                                //Escribo en excel
                                //No
                                $sheet->cell('A' . $i, function ($cell) use ($x) {
                                    // manipulate the cel
                                    $cell->setValue($x);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //NOMBRE DE PACIENTE/USUARIO
                                $sheet->mergeCells('B' . $i . ':G' . $i);
                                $sheet->cell('B' . $i, function ($cell) use ($value, $nombre_anterior) {
                                    // manipulate the cel
                                    $cell->setValue($nombre_anterior);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });

                                //TIPO DE SEGURO
                                $sheet->cell('H' . $i, function ($cell) use ($value, $tipo_seguro_ant) {
                                    // manipulate the cel

                                    $cell->setValue($tipo_seguro_ant);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //BASE0
                                $sheet->mergeCells('I' . $i . ':J' . $i);
                                $sheet->cell('I' . $i, function ($cell) use ($sum_bas_0) {
                                    // manipulate the cel
                                    $cell->setValue($sum_bas_0);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //BASE12
                                $sheet->mergeCells('K' . $i . ':L' . $i);
                                $sheet->cell('K' . $i, function ($cell) use ($sum_bas_iva) {
                                    // manipulate the cel
                                    $cell->setValue($sum_bas_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //VALOR IVA 12%
                                $sheet->cell('M' . $i, function ($cell) use ($sum_v_iva) {
                                    // manipulate the cel
                                    $cell->setValue($sum_v_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //GASTO ADM
                                $sheet->cell('N' . $i, function ($cell) use ($sum_gasto_amd) {
                                    // manipulate the cel
                                    $cell->setValue($sum_gasto_amd);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //SUBTOTAL
                                $sheet->mergeCells('O' . $i . ':P' . $i);
                                $sheet->cell('O' . $i, function ($cell) use ($total_m_iva) {
                                    // manipulate the cel
                                    $cell->setValue($total_m_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });

                                $total_activo += $total_m_iva;
                                $iva_activo += $sum_v_iva;
                                $sub_total_activo = $total_activo - $iva_activo;

                                //Fin de Escritura del Excel
                                $base_0          = 0;
                                $sum             = 0;
                                $sum_bas_0       = 0;
                                $sum_bas_iva     = 0;
                                $sum_v_iva       = 0;
                                $sum_gasto_amd   = 0;
                                $total_m_iva     = 0;
                                $nombre_anterior = '';
                                $tipo_seguro_ant = '';
                                $x++;
                                $i++;

                            }

                            $id_temporal = $value->id_paciente;

                        }

                        //Si porcentaje_iva == 0  BASE_0
                        if ($value->porcentaje_iva == 0) {
                            $sum_bas_0 = $sum_bas_0 + $value->subtotal;
                            $acum_bas_0 += $value->subtotal;
                        }
                        //Si porcentaje_iva == 0.12  BASE_IVA
                        if ($value->porcentaje_iva == 0.12) {
                            $sum_bas_iva = $sum_bas_iva + $value->subtotal;
                            $acum_bas_iva += $value->subtotal;
                        }
                        //Suma v_iva
                        //$sum_v_iva = $sum_v_iva + ($value->valor_unitario * $value->cantidad * $value->porcentaje_iva);
                        $sum_v_iva = $sum_v_iva + ($value->iva_unitario * $value->cantidad );
                        //$acum_v_iva += $value->iva;
                        //$acum_v_iva += ($value->valor_unitario * $value->cantidad * $value->porcentaje_iva);
                        $acum_v_iva += ($value->iva_unitario * $value->cantidad);
                        //  GAST_ADM_10
                        //$sum_gasto_amd = $sum_gasto_amd + $value->porcentaje10;
                        $sum_gasto_amd = $sum_gasto_amd + ($value->p10_unitario * $value->cantidad);
                        //$acum_gasto_amd += $value->porcentaje10;
                        $acum_gasto_amd += ($value->p10_unitario * $value->cantidad);

                        //TOTAL_M_IVA
                        $total_m_iva = $sum_bas_0 + $sum_bas_iva + $sum_v_iva + $sum_gasto_amd;

                        /*$nombre_anterior = $value->paciente->apellido1.' '.$value->paciente->apellido2.' '.$value->paciente->nombre1.' '.$value->paciente->nombre2;*/
                        $nombre_anterior = '';
                        if ($value->paciente->apellido1 != 'N/A' && $value->paciente->apellido1 != '(N/A)') {
                            $nombre_anterior = $value->paciente->apellido1;
                        }
                        if ($value->paciente->apellido2 != 'N/A' && $value->paciente->apellido2 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->apellido2;
                        }
                        if ($value->paciente->nombre1 != 'N/A' && $value->paciente->nombre1 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->nombre1;
                        }

                        if ($value->paciente->nombre2 != 'N/A' && $value->paciente->nombre2 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->nombre2;
                        }

                        $nombre_anterior = str_replace($caract_remplazar_1, "", $nombre_anterior);
                        $nombre_anterior = str_replace($caract_remplazar_2, "N", $nombre_anterior);

                        $tipo_seguro_ant = $value->tiposeg;

                    }
                    //Escribo en excel
                    //No
                    $sheet->cell('A' . $i, function ($cell) use ($x) {
                        // manipulate the cel
                        $cell->setValue($x);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    //NOMBRE DE PACIENTE/USUARIO
                    $sheet->mergeCells('B' . $i . ':G' . $i);
                    $sheet->cell('B' . $i, function ($cell) use ($nombre_anterior) {
                        // manipulate the cel
                        $cell->setValue($nombre_anterior);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    //TIPO DE SEGURO
                    $sheet->cell('H' . $i, function ($cell) use ($tipo_seguro_ant) {
                        // manipulate the cel

                        $cell->setValue($tipo_seguro_ant);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    //BASE0
                    $sheet->mergeCells('I' . $i . ':J' . $i);
                    $sheet->cell('I' . $i, function ($cell) use ($sum_bas_0) {
                        // manipulate the cel
                        $cell->setValue($sum_bas_0);
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    //BASE12
                    $sheet->mergeCells('K' . $i . ':L' . $i);
                    $sheet->cell('K' . $i, function ($cell) use ($sum_bas_iva) {
                        // manipulate the cel
                        $cell->setValue($sum_bas_iva);
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    //VALOR IVA 12%
                    $sheet->cell('M' . $i, function ($cell) use ($sum_v_iva) {
                        // manipulate the cel
                        $cell->setValue($sum_v_iva);
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    //GASTO ADM
                    $sheet->cell('N' . $i, function ($cell) use ($sum_gasto_amd) {
                        // manipulate the cel
                        $cell->setValue($sum_gasto_amd);
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    //SUBTOTAL
                    $sheet->mergeCells('O' . $i . ':P' . $i);
                    $sheet->cell('O' . $i, function ($cell) use ($total_m_iva) {
                        // manipulate the cel
                        $cell->setValue($total_m_iva);
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    //Fin de Escritura del Excel
                    $base_0          = 0;
                    $sum             = 0;
                    $sum_bas_0       = 0;
                    $sum_bas_iva     = 0;
                    $sum_v_iva       = 0;
                    $sum_gasto_amd   = 0;
                    $total_m_iva     = 0;
                    $nombre_anterior = '';
                    $tipo_seguro_ant = '';
                    $x++;
                    $i++;
                }

                

                /* if($idUsuario == '0950457978'){
                    $conP =0;
                    foreach ($archivo_plano_issfa_activo as $value) {
                        if($conP==2){
                            //dd($value,$conP);
                        }
                        $conP++;
                    }
                    //dd($archivo_plano_issfa_activo,$archivo_plano_issfa_montepio);
                } */
                $sheet->mergeCells('A' . $i . ':N' . $i);
                $sheet->cell('A' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    $cell->setValue('SUBTOTAL:');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                });
                $subtotal_final = $acum_bas_0 + $acum_bas_iva + $acum_gasto_amd;
                $sheet->mergeCells('O' . $i . ':P' . $i);
                $sheet->cell('O' . $i, function ($cell) use ($value, $subtotal_final) {
                    // manipulate the cel
                    $cell->setValue($subtotal_final);
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                });
                $i++;

                $sheet->mergeCells('A' . $i . ':N' . $i);
                $sheet->cell('A' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    $cell->setValue('IVA:');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                });

                $sheet->mergeCells('O' . $i . ':P' . $i);
                $sheet->cell('O' . $i, function ($cell) use ($value, $acum_v_iva) {
                    // manipulate the cel
                    $cell->setValue($acum_v_iva);
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                });
                $i++;

                $sheet->mergeCells('A' . $i . ':N' . $i);
                $sheet->cell('A' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    $cell->setValue('TOTAL');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                });
                $total_final = $subtotal_final + $acum_v_iva;
                $sheet->mergeCells('O' . $i . ':P' . $i);
                $sheet->cell('O' . $i, function ($cell) use ($value, $total_final) {
                    // manipulate the cel
                    $cell->setValue($total_final);
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                });

                $i = $i + 7;
                $sheet->mergeCells('G' . $i . ':J' . $i);
                $sheet->cell('G' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('t', '', 'thin', '');

                });
                $i++;
                $sheet->mergeCells('G' . $i . ':J' . $i);
                $sheet->cell('G' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    $cell->setValue('FIRMA / SELLO PRESTADOR');
                    $cell->setAlignment('center');
                    $cell->setBorder('t', '', '', '');

                });
            });
        })->export('xlsx'); 

    }

    public function nuevo_reporte_consolidado_campesino(Request $request)
    {
        //ini_set('memory_limit', '10024M');
        $mes_plano = $request['mes_plano'];
        $seg       = $request['seguro'];
        $tipo_seg  = $request['id_tipo_seguro'];
        $empresa   = $request['id_empresa'];

        $sub_cadena_1 = '';
        $sub_cadena_2 = '';

        $archivo_plano_jub_campesino  = [];
        $archivo_plano_ssc = [];

        $texto_nombre = null;

        $caract_remplazar_1 = array("¨", "$", "%", "&", "/", "(", ")", "#", "@", "|", "!", "/", "+", "-", "}", "{", ">", "< ", ";", ",", ":",
            ".", "*", ">", "<", "[", "]", "?", "¿", "º", "~");

        $caract_remplazar_2 = array("Ñ", "ñ");

        if (!is_null($mes_plano)) {
            $sub_cadena_1 = substr($mes_plano, 0, 2);
        }

        if (!is_null($mes_plano)) {
            $sub_cadena_2 = substr($mes_plano, 2, 6);
        }

        $inf_empresa = Empresa::where('id', $empresa)->where('estado', '1')->first();
        
        //REPORTE CONSOLIDADO IEESS GENERAL
        //Seguro social campesino ssc
        $archivo_plano_ssc = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->whereIn('archivo_plano_cabecera.id_tipo_seguro',['9'])
            ->where('archivo_plano_cabecera.id_seguro', $seg)
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            //->whereNull('archivo_plano_cabecera.id_cobertura_comp')
            ->where('archivo_plano_cabecera.estado', '1')
            ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd', 'archivo_plano_cabecera.id', 'apd.id_ap_cabecera')
            ->where('apd.estado', '1')
            ->orderby('pac.apellido1', 'asc')
            ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'archivo_plano_cabecera.id_empresa', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.subtotal', 'apd.total', 'apd.valor_unitario', 'apd.iva_unitario')
            ->orderby('full_name')
            ->get();

        //JUBILADO CAMPESINO JC
        $archivo_plano_jub_campesino = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->whereIn('archivo_plano_cabecera.id_tipo_seguro',['7'])
            ->where('archivo_plano_cabecera.id_seguro', $seg)
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            //->whereNull('archivo_plano_cabecera.id_cobertura_comp')
            ->where('archivo_plano_cabecera.estado', '1')
            ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd', 'archivo_plano_cabecera.id', 'apd.id_ap_cabecera')
            ->where('apd.estado', '1')
            ->orderby('pac.apellido1', 'asc')
            ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'archivo_plano_cabecera.id_empresa', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.subtotal', 'apd.total', 'apd.valor_unitario', 'apd.iva_unitario')
            ->orderby('full_name')
            ->get();
            
        Excel::create('ReporteConsolidadoIESS', function ($excel) use ($inf_empresa, $sub_cadena_1, $sub_cadena_2,$archivo_plano_ssc, $archivo_plano_jub_campesino, $caract_remplazar_1, $caract_remplazar_2, $texto_nombre) {
            $excel->sheet('ReporteConsolidadoIESS', function ($sheet) use ($inf_empresa, $sub_cadena_1, $sub_cadena_2,$archivo_plano_ssc, $archivo_plano_jub_campesino, $caract_remplazar_1, $caract_remplazar_2, $texto_nombre) {

                $sheet->setColumnFormat(array(
                    'I' => '$### ### ### ##0.0000',
                    'K' => '$### ### ### ##0.0000',
                    'M' => '$### ### ### ##0.0000',
                    'N' => '$### ### ### ##0.0000',
                    'O' => '$### ### ### ##0.0000',
                ));

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
                    $cell->setValue('PLANILLA CONSOLIDADO DE PRESTACION DE SERVICIO');
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
                $sheet->mergeCells('J11:P11');
                $sheet->cell('J11', function ($cell) use ($sub_cadena_2) {
                    // manipulate the cel
                    $cell->setValue('Año:' . ($sub_cadena_2));
                    $cell->setAlignment('left');
                    $cell->setFontWeight('bold');
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

                $sheet->mergeCells('B12:G12');
                $sheet->cell('B12', function ($cell) use ($inf_empresa) {
                    $cell->setValue('NOMBRE DE PACIENTE/USUARIO');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('H12', function ($cell) use ($inf_empresa) {
                    $cell->setValue('TIPO DE SEGURO');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('I12:J12');
                $sheet->cell('I12', function ($cell) use ($inf_empresa) {
                    $cell->setValue('BASE0');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('K12:L12');
                $sheet->cell('K12', function ($cell) use ($inf_empresa) {
                    $cell->setValue('BASE12');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('M12', function ($cell) use ($inf_empresa) {
                    $cell->setValue('VALOR IVA 12%');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('N12', function ($cell) use ($inf_empresa) {
                    $cell->setValue('GASTO ADM');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('O12:P12');
                $sheet->cell('O12', function ($cell) use ($inf_empresa) {
                    $cell->setValue('SUBTOTAL:');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $i           = 13;
                $base        = 0;
                $id_temporal = 0;
                $x           = 1;
                $sub_total   = 0;
                $iva         = 0;
                $total       = 0;



                $total_0_1     = 0;
                $iva_0_1       = 0;
                $sub_total_0_1 = 0;

                $total_2_6     = 0;
                $iva_2_6       = 0;
                $sub_total_2_6 = 0;

                $total_7     = 0;
                $iva_7       = 0;
                $sub_total_7 = 0;

                $total_jub     = 0;
                $iva_jub       = 0;
                $sub_total_jub = 0;

                $total_jub_camp     = 0;
                $iva_jub_camp       = 0;
                $sub_total_jub_camp = 0;

                $total_ssc     = 0;
                $iva_ssc       = 0;
                $sub_total_ssc = 0;

                $total_general     = 0;
                $sub_total_general = 0;
                $iva_general       = 0;

                $acum_bas_0 = 0; $acum_bas_iva = 0; $acum_v_iva = 0; $acum_gasto_amd = 0;
                $value      = null;

                //SSc
                if (!is_null($archivo_plano_ssc)) {

                    $base_0          = 0;
                    $sum             = 0;
                    $sum_bas_0       = 0;
                    $sum_bas_iva     = 0;
                    $sum_v_iva       = 0;
                    $sum_gasto_amd   = 0;
                    $total_m_iva     = 0;
                    $nombre_anterior = '';
                    $tipo_seguro_ant = '';
                    $id_temporal     = 0;

                    foreach ($archivo_plano_ssc as $value) {
                        if ($value->id_paciente != $id_temporal) {

                            if ($id_temporal != 0) {

                                //Escribo en excel
                                //No
                                $sheet->cell('A' . $i, function ($cell) use ($x) {
                                    // manipulate the cel
                                    $cell->setValue($x);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //NOMBRE DE PACIENTE/USUARIO
                                $sheet->mergeCells('B' . $i . ':G' . $i);
                                $sheet->cell('B' . $i, function ($cell) use ($value, $nombre_anterior) {
                                    // manipulate the cel
                                    $cell->setValue($nombre_anterior);
                                    //$cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });

                                //TIPO DE SEGURO
                                $sheet->cell('H' . $i, function ($cell) use ($value, $tipo_seguro_ant) {
                                    // manipulate the cel

                                    $cell->setValue($tipo_seguro_ant);
                                    //$cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //BASE0
                                $sheet->mergeCells('I' . $i . ':J' . $i);
                                $sheet->cell('I' . $i, function ($cell) use ($sum_bas_0) {
                                    // manipulate the cel
                                    $cell->setValue($sum_bas_0);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //BASE12
                                $sheet->mergeCells('K' . $i . ':L' . $i);
                                $sheet->cell('K' . $i, function ($cell) use ($sum_bas_iva) {
                                    // manipulate the cel
                                    $cell->setValue($sum_bas_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //VALOR IVA 12%
                                $sheet->cell('M' . $i, function ($cell) use ($sum_v_iva) {
                                    // manipulate the cel
                                    $cell->setValue($sum_v_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //GASTO ADM
                                $sheet->cell('N' . $i, function ($cell) use ($sum_gasto_amd) {
                                    // manipulate the cel
                                    $cell->setValue($sum_gasto_amd);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //SUBTOTAL
                                $sheet->mergeCells('O' . $i . ':P' . $i);
                                $sheet->cell('O' . $i, function ($cell) use ($total_m_iva) {
                                    // manipulate the cel
                                    $cell->setValue($total_m_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });

                                $total_jub_camp += $total_m_iva;
                                $iva_jub_camp += $sum_v_iva;
                                $sub_total_jub_camp = $total_jub_camp - $iva_jub_camp;
                                //Fin de Escritura del Excel
                                $base_0          = 0;
                                $sum             = 0;
                                $sum_bas_0       = 0;
                                $sum_bas_iva     = 0;
                                $sum_v_iva       = 0;
                                $sum_gasto_amd   = 0;
                                $total_m_iva     = 0;
                                $nombre_anterior = '';
                                $tipo_seguro_ant = '';
                                $x++;
                                $i++;

                            }

                            $id_temporal = $value->id_paciente;

                        }

                        //Si porcentaje_iva == 0  BASE_0
                        if ($value->porcentaje_iva == 0) {
                            //$sum_bas_0 = $sum_bas_0 + $value->subtotal;
                            //$acum_bas_0 += $value->subtotal;
                            $valorq = round($value->valor,4);
                            $valorpq = $value->cantidad * $valorq;
                            $sum_bas_0  += $valorpq;
                            $acum_bas_0 += $valorpq;
                        }
                        //Si porcentaje_iva == 0.12  BASE_IVA
                        if ($value->porcentaje_iva > 0) {
                            //$sum_bas_iva = $sum_bas_iva + $value->subtotal;
                            //$sum_bas_iva += (($value->valor * $value->cantidad) - $value->porcentaje10);
                            $sum_bas_iva += ($value->valor_unitario * $value->cantidad);
                            //$acum_bas_iva += $value->subtotal;
                            //$acum_bas_iva += (($value->valor * $value->cantidad) - $value->porcentaje10);
                            $acum_bas_iva += ($value->valor_unitario * $value->cantidad);
                        }
                        //Suma v_iva
                        //$sum_v_iva = $sum_v_iva+$value->iva;
                        //$acum_v_iva += $value->iva;
                        //$sum_v_iva = $sum_v_iva + ($value->valor_unitario * $value->cantidad * $value->porcentaje_iva);
                        $sum_v_iva += ($value->iva_unitario * $value->cantidad);
                        //$acum_v_iva += ($value->valor_unitario * $value->cantidad * $value->porcentaje_iva);
                        $acum_v_iva += ($value->iva_unitario * $value->cantidad);
                        //Porcentaje_10  GAST_ADM_10
                        $sum_gasto_amd = $sum_gasto_amd + $value->porcentaje10;
                        $acum_gasto_amd += $value->porcentaje10;

                        //TOTAL_M_IVA
                        $total_m_iva = $sum_bas_0 + $sum_bas_iva + $sum_v_iva + $sum_gasto_amd;

                        /*$nombre_anterior = $value->paciente->apellido1.' '.$value->paciente->apellido2.' '.$value->paciente->nombre1.' '.$value->paciente->nombre2;*/
                        $nombre_anterior = '';
                        if ($value->paciente->apellido1 != 'N/A' && $value->paciente->apellido1 != '(N/A)') {
                            $nombre_anterior = $value->paciente->apellido1;
                        }
                        if ($value->paciente->apellido2 != 'N/A' && $value->paciente->apellido2 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->apellido2;
                        }
                        if ($value->paciente->nombre1 != 'N/A' && $value->paciente->nombre1 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->nombre1;
                        }

                        if ($value->paciente->nombre2 != 'N/A' && $value->paciente->nombre2 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->nombre2;
                        }

                        $nombre_anterior = str_replace($caract_remplazar_1, "", $nombre_anterior);
                        $nombre_anterior = str_replace($caract_remplazar_2, "N", $nombre_anterior);

                        $tipo_seguro_ant = $value->tiposeg;

                    }
                    //Escribo en excel
                    //No
                    if ($nombre_anterior != '') {
                        $sheet->cell('A' . $i, function ($cell) use ($x) {
                            // manipulate the cel
                            $cell->setValue($x);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //NOMBRE DE PACIENTE/USUARIO
                        $sheet->mergeCells('B' . $i . ':G' . $i);
                        $sheet->cell('B' . $i, function ($cell) use ($nombre_anterior) {
                            // manipulate the cel
                            $cell->setValue($nombre_anterior);
                            //$cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        //TIPO DE SEGURO
                        $sheet->cell('H' . $i, function ($cell) use ($tipo_seguro_ant) {
                            // manipulate the cel

                            $cell->setValue($tipo_seguro_ant);
                            //$cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //BASE0
                        $sheet->mergeCells('I' . $i . ':J' . $i);
                        $sheet->cell('I' . $i, function ($cell) use ($sum_bas_0) {
                            // manipulate the cel
                            $cell->setValue($sum_bas_0);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //BASE12
                        $sheet->mergeCells('K' . $i . ':L' . $i);
                        $sheet->cell('K' . $i, function ($cell) use ($sum_bas_iva) {
                            // manipulate the cel
                            $cell->setValue($sum_bas_iva);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //VALOR IVA 12%
                        $sheet->cell('M' . $i, function ($cell) use ($sum_v_iva) {
                            // manipulate the cel
                            $cell->setValue($sum_v_iva);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //GASTO ADM
                        $sheet->cell('N' . $i, function ($cell) use ($sum_gasto_amd) {
                            // manipulate the cel
                            $cell->setValue($sum_gasto_amd);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //SUBTOTAL
                        $sheet->mergeCells('O' . $i . ':P' . $i);
                        $sheet->cell('O' . $i, function ($cell) use ($total_m_iva) {
                            // manipulate the cel
                            $cell->setValue($total_m_iva);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        $x++;
                        $i++;
                    }

                    //Fin de Escritura del Excel
                    $base_0          = 0;
                    $sum             = 0;
                    $sum_bas_0       = 0;
                    $sum_bas_iva     = 0;
                    $sum_v_iva       = 0;
                    $sum_gasto_amd   = 0;
                    $total_m_iva     = 0;
                    $nombre_anterior = '';
                    $tipo_seguro_ant = '';
                    //$x++;
                    //$i++;

                }

                //JUBILADO CAMPESINO
                if (!is_null($archivo_plano_jub_campesino)) {

                    $base_0          = 0;
                    $sum             = 0;
                    $sum_bas_0       = 0;
                    $sum_bas_iva     = 0;
                    $sum_v_iva       = 0;
                    $sum_gasto_amd   = 0;
                    $total_m_iva     = 0;
                    $nombre_anterior = '';
                    $tipo_seguro_ant = '';
                    $id_temporal     = 0;

                    foreach ($archivo_plano_jub_campesino as $value) {
                        if ($value->id_paciente != $id_temporal) {

                            if ($id_temporal != 0) {

                                //Escribo en excel
                                //No
                                $sheet->cell('A' . $i, function ($cell) use ($x) {
                                    // manipulate the cel
                                    $cell->setValue($x);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //NOMBRE DE PACIENTE/USUARIO
                                $sheet->mergeCells('B' . $i . ':G' . $i);
                                $sheet->cell('B' . $i, function ($cell) use ($value, $nombre_anterior) {
                                    // manipulate the cel
                                    $cell->setValue($nombre_anterior);
                                    //$cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });

                                //TIPO DE SEGURO
                                $sheet->cell('H' . $i, function ($cell) use ($value, $tipo_seguro_ant) {
                                    // manipulate the cel

                                    $cell->setValue($tipo_seguro_ant);
                                    //$cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //BASE0
                                $sheet->mergeCells('I' . $i . ':J' . $i);
                                $sheet->cell('I' . $i, function ($cell) use ($sum_bas_0) {
                                    // manipulate the cel
                                    $cell->setValue($sum_bas_0);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //BASE12
                                $sheet->mergeCells('K' . $i . ':L' . $i);
                                $sheet->cell('K' . $i, function ($cell) use ($sum_bas_iva) {
                                    // manipulate the cel
                                    $cell->setValue($sum_bas_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //VALOR IVA 12%
                                $sheet->cell('M' . $i, function ($cell) use ($sum_v_iva) {
                                    // manipulate the cel
                                    $cell->setValue($sum_v_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //GASTO ADM
                                $sheet->cell('N' . $i, function ($cell) use ($sum_gasto_amd) {
                                    // manipulate the cel
                                    $cell->setValue($sum_gasto_amd);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //SUBTOTAL
                                $sheet->mergeCells('O' . $i . ':P' . $i);
                                $sheet->cell('O' . $i, function ($cell) use ($total_m_iva) {
                                    // manipulate the cel
                                    $cell->setValue($total_m_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });

                                $total_jub_camp += $total_m_iva;
                                $iva_jub_camp += $sum_v_iva;
                                $sub_total_jub_camp = $total_jub_camp - $iva_jub_camp;
                                //Fin de Escritura del Excel
                                $base_0          = 0;
                                $sum             = 0;
                                $sum_bas_0       = 0;
                                $sum_bas_iva     = 0;
                                $sum_v_iva       = 0;
                                $sum_gasto_amd   = 0;
                                $total_m_iva     = 0;
                                $nombre_anterior = '';
                                $tipo_seguro_ant = '';
                                $x++;
                                $i++;

                            }

                            $id_temporal = $value->id_paciente;

                        }

                        //Si porcentaje_iva == 0  BASE_0
                        if ($value->porcentaje_iva == 0) {
                            //$sum_bas_0 = $sum_bas_0 + $value->subtotal;
                            //$acum_bas_0 += $value->subtotal;
                            $valorq = round($value->valor,4);
                            $valorpq = $value->cantidad * $valorq;
                            $sum_bas_0  += $valorpq;
                            $acum_bas_0 += $valorpq;
                        }
                        //Si porcentaje_iva == 0.12  BASE_IVA
                        if ($value->porcentaje_iva > 0) {
                            $sum_bas_iva = $sum_bas_iva + $value->subtotal;
                            $acum_bas_iva += $value->subtotal;
                        }
                        //Suma v_iva
                        //$sum_v_iva = $sum_v_iva+$value->iva;
                        //$acum_v_iva += $value->iva;
                        $sum_v_iva = $sum_v_iva + ($value->valor_unitario * $value->cantidad * $value->porcentaje_iva);
                        $acum_v_iva += ($value->valor_unitario * $value->cantidad * $value->porcentaje_iva);
                        //Porcentaje_10  GAST_ADM_10
                        $sum_gasto_amd = $sum_gasto_amd + $value->porcentaje10;
                        $acum_gasto_amd += $value->porcentaje10;

                        //TOTAL_M_IVA
                        $total_m_iva = $sum_bas_0 + $sum_bas_iva + $sum_v_iva + $sum_gasto_amd;

                        /*$nombre_anterior = $value->paciente->apellido1.' '.$value->paciente->apellido2.' '.$value->paciente->nombre1.' '.$value->paciente->nombre2;*/
                        $nombre_anterior = '';
                        if ($value->paciente->apellido1 != 'N/A' && $value->paciente->apellido1 != '(N/A)') {
                            $nombre_anterior = $value->paciente->apellido1;
                        }
                        if ($value->paciente->apellido2 != 'N/A' && $value->paciente->apellido2 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->apellido2;
                        }
                        if ($value->paciente->nombre1 != 'N/A' && $value->paciente->nombre1 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->nombre1;
                        }

                        if ($value->paciente->nombre2 != 'N/A' && $value->paciente->nombre2 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->nombre2;
                        }

                        $nombre_anterior = str_replace($caract_remplazar_1, "", $nombre_anterior);
                        $nombre_anterior = str_replace($caract_remplazar_2, "N", $nombre_anterior);

                        $tipo_seguro_ant = $value->tiposeg;

                    }
                    //Escribo en excel
                    //No
                    if ($nombre_anterior != '') {
                        $sheet->cell('A' . $i, function ($cell) use ($x) {
                            // manipulate the cel
                            $cell->setValue($x);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //NOMBRE DE PACIENTE/USUARIO
                        $sheet->mergeCells('B' . $i . ':G' . $i);
                        $sheet->cell('B' . $i, function ($cell) use ($nombre_anterior) {
                            // manipulate the cel
                            $cell->setValue($nombre_anterior);
                            //$cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        //TIPO DE SEGURO
                        $sheet->cell('H' . $i, function ($cell) use ($tipo_seguro_ant) {
                            // manipulate the cel

                            $cell->setValue($tipo_seguro_ant);
                            //$cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //BASE0
                        $sheet->mergeCells('I' . $i . ':J' . $i);
                        $sheet->cell('I' . $i, function ($cell) use ($sum_bas_0) {
                            // manipulate the cel
                            $cell->setValue($sum_bas_0);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //BASE12
                        $sheet->mergeCells('K' . $i . ':L' . $i);
                        $sheet->cell('K' . $i, function ($cell) use ($sum_bas_iva) {
                            // manipulate the cel
                            $cell->setValue($sum_bas_iva);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //VALOR IVA 12%
                        $sheet->cell('M' . $i, function ($cell) use ($sum_v_iva) {
                            // manipulate the cel
                            $cell->setValue($sum_v_iva);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //GASTO ADM
                        $sheet->cell('N' . $i, function ($cell) use ($sum_gasto_amd) {
                            // manipulate the cel
                            $cell->setValue($sum_gasto_amd);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //SUBTOTAL
                        $sheet->mergeCells('O' . $i . ':P' . $i);
                        $sheet->cell('O' . $i, function ($cell) use ($total_m_iva) {
                            // manipulate the cel
                            $cell->setValue($total_m_iva);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        $x++;
                        $i++;
                    }

                    //Fin de Escritura del Excel
                    $base_0          = 0;
                    $sum             = 0;
                    $sum_bas_0       = 0;
                    $sum_bas_iva     = 0;
                    $sum_v_iva       = 0;
                    $sum_gasto_amd   = 0;
                    $total_m_iva     = 0;
                    $nombre_anterior = '';
                    $tipo_seguro_ant = '';
                    //$x++;
                    //$i++;

                }
                

                $sheet->mergeCells('A' . $i . ':N' . $i);
                $sheet->cell('A' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    $cell->setValue('SUBTOTAL:');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                });
                $subtotal_final = $acum_bas_0 + $acum_bas_iva + $acum_gasto_amd;
                $sheet->mergeCells('O' . $i . ':P' . $i);
                $sheet->cell('O' . $i, function ($cell) use ($value, $subtotal_final) {
                    // manipulate the cel
                    $cell->setValue($subtotal_final);
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                });
                $i++;

                $sheet->mergeCells('A' . $i . ':N' . $i);
                $sheet->cell('A' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    $cell->setValue('IVA:');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                });

                $sheet->mergeCells('O' . $i . ':P' . $i);
                $sheet->cell('O' . $i, function ($cell) use ($value, $acum_v_iva) {
                    // manipulate the cel
                    $cell->setValue($acum_v_iva);
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                });
                $i++;

                $sheet->mergeCells('A' . $i . ':N' . $i);
                $sheet->cell('A' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    $cell->setValue('TOTAL');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                });
                $total_final = $subtotal_final + $acum_v_iva;
                $sheet->mergeCells('O' . $i . ':P' . $i);
                $sheet->cell('O' . $i, function ($cell) use ($value, $total_final) {
                    // manipulate the cel
                    $cell->setValue($total_final);
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                });

                $i = $i + 7;
                $sheet->mergeCells('G' . $i . ':J' . $i);
                $sheet->cell('G' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('t', '', 'thin', '');
                });
                $i++;
                $sheet->mergeCells('G' . $i . ':J' . $i);
                $sheet->cell('G' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    $cell->setValue('FIRMA / SELLO PRESTADOR');
                    $cell->setAlignment('center');
                    $cell->setBorder('t', '', '', '');
                });
            });
        })->export('xlsx');
    }

    public function formulario_013($id_hc_procedimientos,Request $request)
    {
        ini_set('max_execution_time',300);
        ini_set('memory_limit', '1024M');
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $rolUsuario = Auth::user()->id_tipo_usuario;
        //$this->verificar_sesion($request->session()->get('id_empresa'));
        $formulario013 = HcFormulario013::where('id_hc_procedimiento',$id_hc_procedimientos)->first();

        if(is_null($formulario013)){
            $id_empresa = $request->session()->get('id_empresa');
            $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
            $procedimiento = hc_procedimientos::find($id_hc_procedimientos);
            if(!is_null($procedimiento)){
                $historiaClinica = $procedimiento->historia;
                if(!is_null($historiaClinica)){
                    $agenda = $historiaClinica->agenda;
                    $paciente = $historiaClinica->paciente;
                    $evoluciones = $historiaClinica->evoluciones;
                    $evolucion_0 = "";
                    $resumen_clinico = "";
                    $fecha_toma = null;
                    foreach ($evoluciones as $value) {
                        if($value->secuencia == 0){
                            $biopsia = Hc4_Biopsias::where('hc_id_procedimiento',$id_hc_procedimientos)->first();
                            if(!is_null($biopsia)){
                                $evolucion_0 = 'Se toma biopsia de ' . $biopsia->descripcion_frasco;
                                $fecha_toma = date_format(new DateTime($biopsia->created_at), 'Y-m-d H:i:s');
                            }
                            $resumen_clinico = $value->cuadro_clinico;
                        }
                    }
                    $tratamiento = "";
                    $procedimiento_final = Hc_Procedimiento_Final::where('id_hc_procedimientos',$id_hc_procedimientos)->get();
                    foreach ($procedimiento_final as $value) {
                        if(!is_null($value->procedimiento)){
                            if($tratamiento == ""){
                                $tratamiento = $value->procedimiento->nombre;
                            }
                            else{
                                $tratamiento = $tratamiento. ' + ' . $value->procedimiento->nombre;
                            }
                        }
                    }
                    $arr_formulario013 = [
                        'id_hc_procedimiento' => $id_hc_procedimientos,
                        'hcid' => $historiaClinica->hcid,
                        'id_paciente' => $paciente->id,
                        'id_empresa' => $id_empresa,
                        'resumen_clinico' => $resumen_clinico,
                        'estudio_solicitado' => 0,
                        'descripcion_estudio' => $evolucion_0,
                        'prioridad' => 2,
                        'fecha_toma' => $fecha_toma,
                        'tratamiento' => $tratamiento,
                        'estado' => 1,
                        'id_usuariocrea' => $idusuario,
                        'id_usuariomod' => $idusuario,
                        'ip_creacion' => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                    ];
                    //dd($arr_formulario013);
                    $id_formulario013 = HcFormulario013::insertGetId($arr_formulario013);
                    $formulario013 = HcFormulario013::find($id_formulario013);

                    $biopsias = Hc4_Biopsias::where('hc_id_procedimiento',$id_hc_procedimientos)->where('estado',1)->get();

                    foreach ($biopsias as $value) {
                        $arr_auditoria_biopsias = [
                            'hc_id_procedimiento' => $value->hc_id_procedimiento,
                            'hcid' => $value->hcid,
                            'id_paciente' => $value->id_paciente,
                            'id_doctor' => $value->id_doctor,
                            'descripcion_frasco' => $value->descripcion_frasco,
                            'observacion' => $value->observacion,
                            'numero_frasco' => $value->numero_frasco,
                            'id_tipo_usuario' => $value->id_tipo_usuario,
                            'estado' => 1,
                            'id_usuariocrea' => $idusuario,
                            'id_usuariomod' => $idusuario,
                            'ip_creacion' => $ip_cliente,
                            'ip_modificacion' => $ip_cliente,
                            'subido' => $value->subido,
                            'muestra_biopsia' => $value->muestra_biopsia,
                            'id_seguro' => $value->id_seguro,
                            'id_hc_formulario013' => $id_formulario013,
                        ];
                        Hc4_Auditoria_Biopsias::create($arr_auditoria_biopsias);
                    }
                }
            }
        }
        Excel::create('Formulario 013', function($excel) use($formulario013,$rolUsuario) { 
            $excel->sheet('FORMULARIO 013', function($sheet) use($formulario013,$rolUsuario){
                $paciente = Paciente::find($formulario013->id_paciente);
                if($rolUsuario=='11' || $rolUsuario = '14'){
                    $diagnosticos = Aud_Hc_Cie10::where('hc_id_procedimiento',$formulario013->id_hc_procedimiento)->limit(5)->get();
                }
                else{
                    $diagnosticos = Hc_Cie10::where('hc_id_procedimiento',$formulario013->id_hc_procedimiento)->limit(5)->get();
                }
                $biopsias = Hc4_Auditoria_Biopsias::where('id_hc_formulario_013',$formulario013->id)->where('estado',1)->limit(5)->get();
                //dd($biopsias);
                $procedimiento = hc_procedimientos::find($formulario013->id_hc_procedimiento);
                $doctor = User::find($procedimiento->id_doctor_examinador);
                $firma = null;
                $ruta_firma = "";
                if(!is_null($doctor)){
                    $firma = Firma_Usuario::where('id_usuario',$doctor->id)->first();
                    if(!is_null($firma)){
                        $ruta_firma = base_path().'/storage/app/avatars/' . $firma->nombre;
                    }
                }
                $i = 4;
                $sheet->mergeCells('A3:H4');
                $sheet->cell('A3', function($cell) {
                    // manipulate the cel
                    $cell->setValue('INSTITUCIÓN DEL SISTEMA');
                    $cell->setFontSize('12');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('A3:H4', function($cells) {
                    $cells->setBackground('#BADAC8');
                    $cells->setFontColor('#000000'); 
                    $cells->setFontWeight('bold');
                });
                $sheet->mergeCells('I3:L4');
                $sheet->cell('I3', function($cell){
                    // manipulate the cel
                    $cell->setValue('UNIDAD OPERATIVA');
                    $cell->setFontWeight('bold'); 
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('I3:L4', function($cells) {
                    $cells->setBackground('#BADAC8');
                    $cells->setFontColor('#000000'); 
                    $cells->setFontWeight('bold');
                });
                $sheet->mergeCells('M3:O4');
                $sheet->cell('M3', function($cell){
                    // manipulate the cel
                    $cell->setValue('COD. UO');
                    $cell->setFontWeight('bold'); 
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('M3:O4', function($cells) {
                    $cells->setBackground('#BADAC8');
                    $cells->setFontColor('#000000'); 
                    $cells->setFontWeight('bold');
                });
                $sheet->mergeCells('P3:U3');
                $sheet->cell('P3', function($cell) {
                    // manipulate the cel
                    $cell->setValue('COD. LOCALIZACIÓN');
                    $cell->setFontWeight('bold'); 
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('P3:U3', function($cells) {
                    $cells->setBackground('#BADAC8');
                    $cells->setFontColor('#000000'); 
                    $cells->setFontWeight('bold');
                });
                $sheet->mergeCells('P4:Q4');
                $sheet->cell('P4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('PARROQUIA');
                    $cell->setFontWeight('bold'); 
                    $cell->setFontSize('10');
                    $cell->setBackground('#BADAC8');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('R4:S4');
                $sheet->cell('R4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('CANTÓN');
                    $cell->setFontWeight('bold'); 
                    $cell->setFontSize('10');
                    $cell->setBackground('#BADAC8');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('T4:U4');
                $sheet->cell('T4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('PROVINCIA');
                    $cell->setFontWeight('bold'); 
                    $cell->setFontSize('10');
                    $cell->setBackground('#BADAC8');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('V3:AB4');
                $sheet->cell('V3', function($cell){
                    // manipulate the cel
                    $cell->setValue('NÚMERO DE HISTORIA CLÍNICA');
                    $cell->setFontWeight('bold'); 
                    $cell->setFontSize('12');
                    $cell->setBackground('#BADAC8');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('V3:AB4', function($cells) {
                    $cells->setBackground('#BADAC8');
                    $cells->setFontColor('#000000'); 
                    $cells->setFontWeight('bold');
                });
                //SEGUNDA FILA
                $sheet->mergeCells('A5:H7');
                $sheet->cell('A5', function($cell) use ($paciente) {
                    // manipulate the cel
                    if(!is_null($paciente)){
                        $seguro = Seguro::find($paciente->id_seguro);
                        if(!is_null($seguro))
                            $cell->setValue(strtoupper($seguro->nombre));
                        else
                            $cell->setValue('');
                    }
                    else{
                        $cell->setValue('');
                    }
                    $cell->setFontSize('12');
                    $cell->setFontWeight('bold'); 
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('I5:L7');
                $sheet->cell('I5', function($cell) use ($formulario013) {
                    // manipulate the cel
                    $empresa = Empresa::find($formulario013->id_empresa);
                    if(!is_null($empresa)){
                        $cell->setValue($empresa->nombrecomercial);
                    }
                    else{
                        $cell->setValue('');
                    }
                    $cell->setFontSize('12');
                    $cell->setFontWeight('bold'); 
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('M5:O7');
                $sheet->cell('M5', function($cell) use ($paciente) {
                    // manipulate the cel
                    if(!is_null($paciente))
                        $cell->setValue($paciente->cod_val);
                    else
                        $cell->setValue('');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('P5:Q7');
                $sheet->cell('P5', function($cell) {
                    // manipulate the cel
                    $cell->setValue('TARQUI');
                    $cell->setFontSize('10');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('R5:S7');
                $sheet->cell('R5', function($cell) {
                    // manipulate the cel
                    $cell->setValue('GUAYAQUIL');
                    $cell->setFontSize('10');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('T5:U7');
                $sheet->cell('T5', function($cell) {
                    // manipulate the cel
                    $cell->setValue('GUAYAS');
                    $cell->setFontSize('10');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('V5:AB7');
                $sheet->cell('V5', function($cell) use ($paciente) {
                    // manipulate the cel
                    if(!is_null($paciente))
                        $cell->setValue($paciente->id);
                    else
                        $cell->setValue('');
                    
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                
                //TERCERA FILA
                $sheet->mergeCells('A8:D8');
                $sheet->cell('A8', function($cell) {
                    // manipulate the cel
                    $cell->setValue('APELLIDO PATERNO');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', '', 'thin', 'thin');
                });
                $sheet->cells('A8:D8', function($cells) {
                    $cells->setBackground('#BADAC8');
                    $cells->setFontColor('#000000');
                });
                $sheet->mergeCells('E8:I8');
                $sheet->cell('E8', function($cell){
                    // manipulate the cel
                    $cell->setValue('APELLIDO MATERNO');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', '', 'thin', '');
                });
                $sheet->cells('E8:I8', function($cells) {
                    $cells->setBackground('#BADAC8');
                    $cells->setFontColor('#000000');
                });
                $sheet->mergeCells('J8:N8');
                $sheet->cell('J8', function($cell){
                    // manipulate the cel
                    $cell->setValue('PRIMER NOMBRE');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', '', 'thin', '');
                });
                $sheet->cells('J8:N8', function($cells) {
                    $cells->setBackground('#BADAC8');
                    $cells->setFontColor('#000000');
                });
                $sheet->mergeCells('O8:S8');
                $sheet->cell('O8', function($cell){
                    // manipulate the cel
                    $cell->setValue('SEGUNDO NOMBRE');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', '', 'thin', '');
                });
                $sheet->cells('O8:S8', function($cells) {
                    $cells->setBackground('#BADAC8');
                    $cells->setFontColor('#000000');
                });
                $sheet->mergeCells('T8:U8');
                $sheet->cell('T8', function($cell) {
                    // manipulate the cel
                    $cell->setValue('EDAD');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('T8:U8', function($cells) {
                    $cells->setBackground('#BADAC8');
                    $cells->setFontColor('#000000');
                });
                $sheet->mergeCells('V8:AB8');
                $sheet->cell('V8', function($cell) {
                    // manipulate the cel
                    $cell->setValue('CÉDULA DE CIUDADANIA');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('V8:AB8', function($cells) {
                    $cells->setBackground('#BADAC8');
                    $cells->setFontColor('#000000');
                });

                //CUARTA FILA
                $sheet->mergeCells('A9:D9');
                $sheet->cell('A9', function($cell) use($paciente) {
                    // manipulate the cel
                    if(!is_null($paciente))
                        $cell->setValue($paciente->apellido1);
                    else
                        $cell->setValue('');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('E9:I9');
                $sheet->cell('E9', function($cell) use($paciente) {
                    // manipulate the cel
                    if(!is_null($paciente))
                        $cell->setValue($paciente->apellido2);
                    else
                        $cell->setValue('');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('J9:N9');
                $sheet->cell('J9', function($cell) use($paciente) {
                    // manipulate the cel
                    if(!is_null($paciente))
                        $cell->setValue($paciente->nombre1);
                    else
                        $cell->setValue('');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('O9:S9');
                $sheet->cell('O9', function($cell) use($paciente) {
                    // manipulate the cel
                    if(!is_null($paciente))
                        $cell->setValue($paciente->nombre2);
                    else
                        $cell->setValue('');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('T9:U9');
                $sheet->cell('T9', function($cell) use($paciente) {
                    // manipulate the cel
                    $edad = null;
                    $fechaActual = new DateTime();
                    if(!is_null($paciente)){
                        $fechaNacimiento = new DateTime($paciente->fecha_nacimiento);
                        $edad = $fechaNacimiento->diff($fechaActual);
                        $cell->setValue($edad->y);
                    }
                    else{
                        $cell->setValue('');
                    }
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('V9:AB9');
                $sheet->cell('V9', function($cell) use($paciente) {
                    // manipulate the cel
                    if(!is_null($paciente))
                        $cell->setValue($paciente->id);
                    else
                        $cell->setValue('');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                //QUINTA FILA
                $sheet->mergeCells('A11:H11');
                $sheet->cell('A11', function($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', '', 'thin', 'thin');
                });
                $sheet->cells('A11:D11', function($cells) {
                    $cells->setBackground('#BADAC8');
                    $cells->setFontColor('#000000');
                });
                $sheet->mergeCells('I11:K11');
                $sheet->cell('I11', function($cell){
                    // manipulate the cel
                    $cell->setValue('SERVICIO');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', '', 'thin', '');
                });
                $sheet->cells('I11:K11', function($cells) {
                    $cells->setBackground('#BADAC8');
                    $cells->setFontColor('#000000');
                });
                $sheet->mergeCells('L11:M11');
                $sheet->cell('L11', function($cell){
                    // manipulate the cel
                    $cell->setValue('SALA');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', '', 'thin', '');
                });
                $sheet->cells('L11:M11', function($cells) {
                    $cells->setBackground('#BADAC8');
                    $cells->setFontColor('#000000');
                });
                $sheet->mergeCells('N11:O11');
                $sheet->cell('N11', function($cell){
                    // manipulate the cel
                    $cell->setValue('CAMA');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', '', 'thin', '');
                });
                $sheet->cells('N11:O11', function($cells) {
                    $cells->setBackground('#BADAC8');
                    $cells->setFontColor('#000000');
                });
                $sheet->mergeCells('P11:X11');
                $sheet->cell('P11', function($cell) {
                    // manipulate the cel
                    $cell->setValue('PRIORIDAD');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', '', 'thin', '');
                });
                $sheet->cells('P11:X11', function($cells) {
                    $cells->setBackground('#BADAC8');
                    $cells->setFontColor('#000000');
                });
                $sheet->mergeCells('Y11:AB11');
                $sheet->cell('Y11', function($cell){
                    // manipulate the cel
                    $cell->setValue('FECHA DE TOMA');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', '');
                });
                $sheet->cells('Y11:AB11', function($cells) {
                    $cells->setBackground('#BADAC8');
                    $cells->setFontColor('#000000');
                });

                //SEXTA FILA
                $sheet->mergeCells('A12:H12');
                $sheet->cell('A12', function($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('I12:K12');
                $sheet->cell('I12', function($cell) {
                    // manipulate the cel
                    $cell->setValue('GASTROENTEROLOGIA');
                    $cell->setFontSize('10');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('L12:M12');
                $sheet->cell('L12', function($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('N12:O12');
                $sheet->cell('N12', function($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('P12:Q12');
                $sheet->cell('P12', function($cell) {
                    // manipulate the cel
                    $cell->setValue('URGENTE');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('R12', function($cell) use($formulario013){
                    // manipulate the cel
                    if($formulario013->prioridad == 0)
                        $cell->setValue('X');
                    else    
                        $cell->setValue('');
                    $cell->setFontWeight('bold'); 
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('R12', function($cells) {
                    $cells->setBackground('#F1FD77');
                    $cells->setFontColor('#000000');
                });
                $sheet->mergeCells('S12:T12');
                $sheet->cell('S12', function($cell) {
                    // manipulate the cel
                    $cell->setValue('RUTINA');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('U12', function($cell) use($formulario013){
                    // manipulate the cel
                    if($formulario013->prioridad == 1)
                        $cell->setValue('X');
                    else    
                        $cell->setValue('');
                    $cell->setFontSize('12');
                    $cell->setFontWeight('bold'); 
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('U12', function($cells) {
                    $cells->setBackground('#F1FD77');
                    $cells->setFontColor('#000000');
                });

                $sheet->mergeCells('V12:W12');
                $sheet->cell('V12', function($cell) {
                    // manipulate the cel
                    $cell->setValue('CONTROL');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('X12', function($cell) use($formulario013){
                    // manipulate the cel
                    if($formulario013->prioridad == 2)
                        $cell->setValue('X');
                    else    
                        $cell->setValue('');
                    $cell->setFontSize('12');
                    $cell->setFontWeight('bold'); 
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('X12', function($cells) {
                    $cells->setBackground('#F1FD77');
                    $cells->setFontColor('#000000');
                });

                $sheet->mergeCells('Y12:AB12');
                $sheet->cell('Y12', function($cell) use($formulario013){
                    // manipulate the cel
                    $cell->setValue(date_format(new DateTime($formulario013->fecha_toma), 'Y-m-d'));
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                //SEPTIMA FILA 
                $sheet->mergeCells('A14:AB14');
                $sheet->cell('A14', function($cell){
                    // manipulate the cel
                    $cell->setValue('1. ESTUDIO SOLICITADO');
                    $cell->setFontSize('14');
                    $cell->setAlignment('left');
                    $cell->setFontWeight('bold'); 
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('A14:AB14', function($cells) {
                    $cells->setBackground('#DDEBF7');
                    $cells->setFontColor('#000000');
                });

                //OCTAVA FILA
                $sheet->mergeCells('A15:C15');
                $sheet->cell('A15', function($cell){
                    // manipulate the cel
                    $cell->setValue('HISTOPATOLOGÍA');
                    $cell->setFontSize('10');
                    $cell->setAlignment('left');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('A15:C15', function($cells){
                    $cells->setBackground('#BADAC8');
                    $cells->setFontColor('#000000');
                });
                $sheet->cell('D15', function($cell) use($formulario013){
                    // manipulate the cel
                    if($formulario013->estudio_solicitado == 0)
                        $cell->setValue('X');
                    else    
                        $cell->setValue('');
                    $cell->setFontSize('12');
                    $cell->setFontWeight('bold'); 
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('D15', function($cells) {
                    $cells->setBackground('#F1FD77');
                    $cells->setFontColor('#000000');
                });
                $sheet->mergeCells('E15:F15');
                $sheet->cell('E15', function($cell){
                    // manipulate the cel
                    $cell->setValue('CITOLOGÍA');
                    $cell->setFontSize('10');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('E15:F15', function($cells) {
                    $cells->setBackground('#BADAC8');
                    $cells->setFontColor('#000000');
                });
                $sheet->mergeCells('G15:H15');
                $sheet->cell('G15', function($cell) use($formulario013){
                    // manipulate the cel
                    if($formulario013->estudio_solicitado == 1)
                        $cell->setValue('X');
                    else    
                        $cell->setValue('');
                    $cell->setFontSize('12');
                    $cell->setFontWeight('bold'); 
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('G15:H15', function($cells) {
                    $cells->setBackground('#F1FD77');
                    $cells->setFontColor('#000000');
                });
                $sheet->mergeCells('I15:K15');
                $sheet->cell('I15', function($cell){
                    // manipulate the cel
                    $cell->setValue('DESCRIPCIÓN');
                    $cell->setFontSize('10');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('I15:K15', function($cells) {
                    $cells->setBackground('#BADAC8');
                    $cells->setFontColor('#000000');
                });
                $sheet->mergeCells('L15:AB15');
                $sheet->cell('L15', function($cell) use($formulario013){
                    // manipulate the cel
                    $cell->setValue($formulario013->descripcion_estudio);
                    $cell->setFontSize('10');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('L15:AB15', function($cells) {
                    $cells->setFontColor('#000000');
                });
                $sheet->mergeCells('A16:AB16');
                $sheet->cell('A16', function($cell){
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setFontSize('12');
                    $cell->setAlignment('left');
                    $cell->setFontWeight('bold'); 
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('A18:AB18', function($cells) {
                    $cells->setBackground('#DDEBF7');
                    $cells->setFontColor('#000000');
                });
                //NOVENA FILA 
                $sheet->mergeCells('A18:AB18');
                $sheet->cell('A18', function($cell){
                    // manipulate the cel
                    $cell->setValue('2. RESUMEN CLÍNICO');
                    $cell->setFontSize('14');
                    $cell->setAlignment('left');
                    $cell->setFontWeight('bold'); 
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('A18:AB18', function($cells) {
                    $cells->setBackground('#DDEBF7');
                    $cells->setFontColor('#000000');
                });
                //DECIMA FILA
                
                /*$resumen = str_replace(['<p>','</p>'],'',$formulario013->resumen_clinico);
                $resumen = str_replace('<br>','-',$resumen);
                $resumen_partes = explode('-',$resumen);
                //dd($resumen_partes,$resumen,$formulario013->resumen_clinico);
                $fila = 19;
                for ($i=0; $i < count($resumen_partes)-1; $i++) {
                    $var = $resumen_partes[$i];
                    $sheet->mergeCells('A'.$fila.':AB'.$fila);
                    $sheet->cell('A'.$fila, function($cell) use($var){
                        // manipulate the cel
                        //$resumen = str_replace(['<p>','</p>','<br>'],'',$formulario013->resumen_clinico);
                        $cell->setValue($var);
                        $cell->setFontSize('12');
                        $cell->setAlignment('left');
                        $cell->setFontWeight('bold'); 
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cells('A'.$fila.':AB'.$fila, function($cells) {
                        $cells->setFontColor('#000000');
                    });
                    $fila++;
                }*/
                
                $sheet->mergeCells('A19:AB23');
                $sheet->cell('A19', function($cell) use($formulario013){
                    // manipulate the cel
                    if(!is_null($formulario013->resumen_clinico)){
                        $resumen =strip_tags($formulario013->resumen_clinico);
                        $resumen = str_replace(array('&Eacute;','&Aacute;','&Iacute;','&Oacute;','&Uacute;','&nbsp;','\n'),array('É','Á','Í','Ó','Ú','',''),$resumen);
                        //$resumen =htmlspecialchars($resumen,ENT_QUOTES|ENT_SUBSTITUTE);
                        //dd($resumen);
                        $cell->setValue($resumen);
                    }
                    else{
                        $cell->setValue('');
                    }
                    $cell->setFontSize('12');
                    $cell->setAlignment('left');
                    $cell->setFontWeight('bold'); 
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('A19:AB21', function($cells) {
                    $cells->setFontColor('#000000');
                });

                //ONCEAVAFILA
                $sheet->mergeCells('A25:D25');
                $sheet->cell('A25', function($cell){
                    // manipulate the cel
                    $cell->setValue('3. DIAGNÓSTICO');
                    $cell->setFontSize('14');
                    $cell->setAlignment('left');
                    $cell->setFontWeight('bold'); 
                    $cell->setBorder('thin', '', 'thin', 'thin');
                });
                $sheet->cells('A25:D25', function($cells) {
                    $cells->setBackground('#DDEBF7');
                    $cells->setFontColor('#000000');
                });
                
                $sheet->mergeCells('E25:H25');
                $sheet->cell('E25', function($cell){
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setFontSize('12');
                    $cell->setAlignment('left');
                    $cell->setFontWeight('bold'); 
                    $cell->setBorder('thin', '', 'thin', '');
                });
                $sheet->cells('E25:H25', function($cells) {
                    $cells->setBackground('#DDEBF7');
                    $cells->setFontColor('#000000');
                });
                $sheet->mergeCells('I25:K25');
                $sheet->cell('I25', function($cell){
                    // manipulate the cel
                    $cell->setValue('PRE=PRESUNTIVO DEF=DEFINITIVO');
                    $cell->setFontSize('10');
                    $cell->setAlignment('left');
                    $cell->setFontWeight('bold'); 
                    $cell->setBorder('thin', '', 'thin', '');
                });
                $sheet->cells('I25:K25', function($cells) {
                    $cells->setBackground('#DDEBF7');
                    $cells->setFontColor('#000000');
                });
                $sheet->mergeCells('L25:M25');
                $sheet->cell('L25', function($cell){
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setFontSize('12');
                    $cell->setAlignment('left');
                    $cell->setFontWeight('bold'); 
                    $cell->setBorder('thin', '', 'thin', '');
                });
                $sheet->cells('L25:M25', function($cells) {
                    $cells->setBackground('#DDEBF7');
                    $cells->setFontColor('#000000');
                });
                $sheet->mergeCells('N25:O25');
                $sheet->cell('N25', function($cell){
                    // manipulate the cel
                    $cell->setValue('CIE');
                    $cell->setFontSize('10');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold'); 
                    $cell->setBorder('thin', '', 'thin', '');
                });
                $sheet->cells('N25:O25', function($cells) {
                    $cells->setBackground('#DDEBF7');
                    $cells->setFontColor('#000000');
                });
                $sheet->cell('P25', function($cell){
                    // manipulate the cel
                    $cell->setValue('PRE');
                    $cell->setFontSize('10');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold'); 
                    $cell->setBorder('thin', '', 'thin', '');
                });
                $sheet->cells('P25', function($cells) {
                    $cells->setBackground('#DDEBF7');
                    $cells->setFontColor('#000000');
                });
                $sheet->cell('Q25', function($cell){
                    // manipulate the cel
                    $cell->setValue('DEF');
                    $cell->setFontSize('10');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold'); 
                    $cell->setBorder('thin', 'thin', 'thin', '');
                });
                $sheet->cells('Q25', function($cells) {
                    $cells->setBackground('#DDEBF7');
                    $cells->setFontColor('#000000');
                });
                $sheet->mergeCells('S25:V25');
                $sheet->cell('S25', function($cell){
                    // manipulate the cel
                    $cell->setValue('4. MUESTRA O PIEZA');
                    $cell->setFontSize('14');
                    $cell->setAlignment('left');
                    $cell->setFontWeight('bold'); 
                    $cell->setBorder('thin', '', 'thin', 'thin');
                });
                $sheet->cells('S25:V25', function($cells) {
                    $cells->setBackground('#DDEBF7');
                    $cells->setFontColor('#000000');
                });
                $sheet->mergeCells('W25:X25');
                $sheet->cell('W25', function($cell){
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setFontSize('12');
                    $cell->setAlignment('left');
                    $cell->setFontWeight('bold'); 
                    $cell->setBorder('thin', '', 'thin', '');
                });
                $sheet->cells('W25:X25', function($cells) {
                    $cells->setBackground('#DDEBF7');
                    $cells->setFontColor('#000000');
                });
                $sheet->mergeCells('Y25:AB25');
                $sheet->cell('Y25', function($cell){
                    // manipulate the cel
                    $cell->setValue('DESCRIBIR EL PROCEDIMIENTO, ORIGEN, NÚMERO Y ZONA');
                    $cell->setFontSize('10');
                    $cell->setAlignment('right');
                    $cell->setFontWeight('bold'); 
                    $cell->setBorder('thin', 'thin', 'thin', '');
                });
                $sheet->cells('Y25:AB25', function($cells) {
                    $cells->setBackground('#DDEBF7');
                    $cells->setFontColor('#000000');
                });
                
                $i = 26;
                foreach ($diagnosticos as $value) {
                    //DOCEAVA FILA
                    $sheet->mergeCells('A'.$i.':M'.$i);
                    $cie10 = Cie_10_3::where('id',$value->cie10)->first();
                    if(is_null($cie10)){
                        $cie10 = Cie_10_4::where('id',$value->cie10)->first();
                    }
                    $sheet->cell('A'.$i, function($cell) use($cie10){
                        // manipulate the cel
                        $cell->setValue($cie10->descripcion);
                        $cell->setFontSize('12');
                        $cell->setAlignment('left');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cells('A'.$i.':M'.$i, function($cells) {
                        $cells->setFontColor('#000000');
                    });
                    $sheet->mergeCells('N'.$i.':O'.$i);
                    $sheet->cell('N'.$i, function($cell) use($value){
                        // manipulate the cel
                        $cell->setValue($value->cie10);
                        $cell->setFontSize('12');
                        $cell->setAlignment('left');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cells('N:O', function($cells) {
                        $cells->setFontColor('#000000');
                    });
                    $sheet->cell('P'.$i, function($cell) use($value){
                        // manipulate the cel
                        if($value->presuntivo_definitivo=='PRESUNTIVO'){
                            $cell->setValue('X');
                        }
                        else{
                            $cell->setValue('');
                        }
                        $cell->setFontSize('12');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cells('P'.$i, function($cells) {
                        $cells->setBackground('#F1FD77');
                        $cells->setFontColor('#000000');
                    });
                    $sheet->cell('Q'.$i, function($cell) use($value){
                        // manipulate the cel
                        if($value->presuntivo_definitivo=='DEFINITIVO'){
                            $cell->setValue('X');
                        }
                        else{
                            $cell->setValue('');
                        }
                        $cell->setFontSize('12');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cells('Q'.$i, function($cells) {
                        $cells->setBackground('#F1FD77');
                        $cells->setFontColor('#000000');
                    });
                    $i++;
                }
                
                for ($a=$i; $a < 31; $a++) { 
                    $sheet->mergeCells('A'.$a.':M'.$a);
                    $sheet->cell('A'.$a, function($cell){
                        // manipulate the cel
                        $cell->setValue('');
                        $cell->setFontSize('12');
                        $cell->setAlignment('left');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cells('A'.$a.':M'.$a, function($cells) {
                        $cells->setFontColor('#000000');
                    });
                    $sheet->mergeCells('N'.$a.':O'.$a);
                    $sheet->cell('N'.$a, function($cell){
                        // manipulate the cel
                        $cell->setValue('');
                        $cell->setFontSize('12');
                        $cell->setAlignment('left');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cells('N:O', function($cells) {
                        $cells->setFontColor('#000000');
                    });
                    $sheet->cell('P'.$a, function($cell) {
                        // manipulate the cel
                        $cell->setValue('');
                        $cell->setFontSize('12');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cells('P'.$a, function($cells) {
                        $cells->setBackground('#F1FD77');
                        $cells->setFontColor('#000000');
                    });
                    $sheet->cell('Q'.$a, function($cell) {
                        // manipulate the cel
                        $cell->setValue('');
                        $cell->setFontSize('12');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cells('Q'.$a, function($cells) {
                        $cells->setBackground('#F1FD77');
                        $cells->setFontColor('#000000');
                    });
                }

                $j = 26;
                foreach ($biopsias as $value) {
                    $sheet->mergeCells('S'.$j.':AB'.$j);
                    $sheet->cell('S'.$j, function($cell) use($value) {
                        // manipulate the cel
                        $cell->setValue($value->descripcion_frasco);
                        $cell->setFontSize('12');
                        $cell->setAlignment('left');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cells('S'.$j.':AB'.$j, function($cells) {
                        $cells->setFontColor('#000000');
                    });
                    $j++;
                }

                for ($b=$j; $b < 31; $b++) { 
                    $sheet->mergeCells('S'.$b.':AB'.$b);
                    $sheet->cell('S'.$b, function($cell) {
                        // manipulate the cel
                        $cell->setValue('');
                        $cell->setFontSize('12');
                        $cell->setAlignment('left');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cells('S'.$b.':AB'.$b, function($cells) {
                        $cells->setFontColor('#000000');
                    });
                }

                $sheet->mergeCells('A32:AB32');
                $sheet->cell('A32', function($cell){
                    // manipulate the cel
                    $cell->setValue('5. TRATAMIENTO QUE RECIBE');
                    $cell->setFontSize('14');
                    $cell->setAlignment('left');
                    $cell->setFontWeight('bold'); 
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('A32:AB32', function($cells) {
                    $cells->setBackground('#DDEBF7');
                    $cells->setFontColor('#000000');
                });
                
                $sheet->mergeCells('A33:AB33');
                $sheet->cell('A33', function($cell) use($formulario013){
                    // manipulate the cel
                    $cell->setValue($formulario013->tratamiento);
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold'); 
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('A33:AB33', function($cells) {
                    $cells->setFontColor('#000000');
                });
                $z = 34;
                for ($b=0; $b < 4; $b++) {
                    $sheet->mergeCells('A'.($z+$b).':AB'.($z+$b));
                    $sheet->cell('A'.($z+$b), function($cell) {
                        // manipulate the cel
                        $cell->setValue('');
                        $cell->setFontSize('12');
                        $cell->setAlignment('center');
                        $cell->setFontWeight('bold'); 
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cells('A'.($z+$b).':AB'.($z+$b), function($cells) {
                        $cells->setFontColor('#000000');
                    });
                }
                $sheet->mergeCells('A39:AB39');
                $sheet->cell('A39', function($cell){
                    // manipulate the cel
                    $cell->setValue('6. DATOS BÁSICOS PARA CITOLOGÍA VAGINAL');
                    $cell->setFontSize('14');
                    $cell->setAlignment('left');
                    $cell->setFontWeight('bold'); 
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('A39:AB39', function($cells) {
                    $cells->setBackground('#DDEBF7');
                    $cells->setFontColor('#000000');
                });
                
                //SIGUIENTE FILA
                $sheet->mergeCells('A40:F40');
                $sheet->cell('A40', function($cell)  {
                    // manipulate the cel
                    $cell->setValue('MATERIAL');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('A40:F40', function($cells) {
                    $cells->setBackground('#BADAC8');
                    $cells->setFontColor('#000000');
                });
                $sheet->mergeCells('G40:K40');
                $sheet->cell('G40', function($cell) {
                    // manipulate the cel
                    $cell->setValue('ANTICONCEPCIÓN');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('G40:K40', function($cells) {
                    $cells->setBackground('#BADAC8');
                    $cells->setFontColor('#000000');
                });
                $sheet->mergeCells('N40:R40');
                $sheet->cell('N40', function($cell) {
                    // manipulate the cel
                    $cell->setValue('EDADES DE');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('N40:R40', function($cells) {
                    $cells->setBackground('#BADAC8');
                    $cells->setFontColor('#000000');
                });
                $sheet->mergeCells('S40:V40');
                $sheet->cell('S40', function($cell) {
                    // manipulate the cel
                    $cell->setValue('PARIDAD');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('S40:V40', function($cells) {
                    $cells->setBackground('#BADAC8');
                    $cells->setFontColor('#000000');
                });
                $sheet->mergeCells('W40:AB40');
                $sheet->cell('W40', function($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHAS');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('W40:AB40', function($cells) {
                    $cells->setBackground('#BADAC8');
                    $cells->setFontColor('#000000');
                });

                //FILA 13
                $sheet->cell('A41', function($cell) {
                    // manipulate the cel
                    $cell->setValue('ENDOCER VIX');
                    $cell->setFontSize('10');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('A41', function($cells) {
                    $cells->setBackground('#BADAC8');
                    $cells->setFontColor('#000000');
                });
                $sheet->cell('B41', function($cell) {
                    // manipulate the cel
                    $cell->setValue('ENDOCER VIX');
                    $cell->setFontSize('10');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('B41', function($cells) {
                    $cells->setBackground('#BADAC8');
                    $cells->setFontColor('#000000');
                });
                $sheet->cell('C41', function($cell) {
                    // manipulate the cel
                    $cell->setValue('PARED VAGINAL');
                    $cell->setFontSize('10');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('C41', function($cells) {
                    $cells->setBackground('#BADAC8');
                    $cells->setFontColor('#000000');
                });
                $sheet->cell('D41', function($cell) {
                    // manipulate the cel
                    $cell->setValue('UNION ESCAMO COLUMNA');
                    $cell->setFontSize('10');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('D41', function($cells) {
                    $cells->setBackground('#BADAC8');
                    $cells->setFontColor('#000000');
                });
                $sheet->cell('E41', function($cell) {
                    // manipulate the cel
                    $cell->setValue('MUÑON CERVICAL');
                    $cell->setFontSize('10');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('E41', function($cells) {
                    $cells->setBackground('#BADAC8');
                    $cells->setFontColor('#000000');
                });
                $sheet->cell('F41', function($cell) {
                    // manipulate the cel
                    $cell->setValue('OTRO');
                    $cell->setFontSize('10');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('F41', function($cells) {
                    $cells->setBackground('#BADAC8');
                    $cells->setFontColor('#000000');
                });
                $sheet->mergeCells('G41:H41');
                $sheet->cell('G41', function($cell) {
                    // manipulate the cel
                    $cell->setValue('ORAL O INYECTABLE');
                    $cell->setFontSize('10');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('G41:H41', function($cells) {
                    $cells->setBackground('#BADAC8');
                    $cells->setFontColor('#000000');
                });
                $sheet->cell('I41', function($cell) {
                    // manipulate the cel
                    $cell->setValue('DIU');
                    $cell->setFontSize('10');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('I41', function($cells) {
                    $cells->setBackground('#BADAC8');
                    $cells->setFontColor('#000000');
                });
                $sheet->cell('J41', function($cell) {
                    // manipulate the cel
                    $cell->setValue('LIGADURA');
                    $cell->setFontSize('10');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('J41', function($cells) {
                    $cells->setBackground('#BADAC8');
                    $cells->setFontColor('#000000');
                });
                $sheet->cell('K41', function($cell) {
                    // manipulate the cel
                    $cell->setValue('OTRO');
                    $cell->setFontSize('10');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('K41', function($cells) {
                    $cells->setBackground('#BADAC8');
                    $cells->setFontColor('#000000');
                });
                $sheet->mergeCells('L40:M41');
                $sheet->cell('L40', function($cell) {
                    // manipulate the cel
                    $cell->setValue('TERAPIA HORMONAL');
                    $cell->setFontSize('10');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('L40:M41', function($cells) {
                    $cells->setBackground('#BADAC8');
                    $cells->setFontColor('#000000');
                });
                $sheet->mergeCells('N41:O41');
                $sheet->cell('N41', function($cell) {
                    // manipulate the cel
                    $cell->setValue('MENARQUIA');
                    $cell->setFontSize('10');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('N41:O41', function($cells) {
                    $cells->setBackground('#BADAC8');
                    $cells->setFontColor('#000000');
                });

                $sheet->cell('P41', function($cell) {
                    // manipulate the cel
                    $cell->setValue('MENOPAUSIA');
                    $cell->setFontSize('10');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('P41', function($cells) {
                    $cells->setBackground('#BADAC8');
                    $cells->setFontColor('#000000');
                });
                $sheet->mergeCells('Q41:R41');
                $sheet->cell('Q41', function($cell) {
                    // manipulate the cel
                    $cell->setValue('INICIO DE RELACIONES SEXUALES');
                    $cell->setFontSize('10');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('Q41:R41', function($cells) {
                    $cells->setBackground('#BADAC8');
                    $cells->setFontColor('#000000');
                });
                $sheet->cell('S41', function($cell) {
                    // manipulate the cel
                    $cell->setValue('GESTACIONES');
                    $cell->setFontSize('10');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('S41', function($cells) {
                    $cells->setBackground('#BADAC8');
                    $cells->setFontColor('#000000');
                });
                $sheet->cell('T41', function($cell) {
                    // manipulate the cel
                    $cell->setValue('PARTOS');
                    $cell->setFontSize('10');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('T41', function($cells) {
                    $cells->setBackground('#BADAC8');
                    $cells->setFontColor('#000000');
                });
                $sheet->cell('U41', function($cell) {
                    // manipulate the cel
                    $cell->setValue('ABORTOS');
                    $cell->setFontSize('10');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('U41', function($cells) {
                    $cells->setBackground('#BADAC8');
                    $cells->setFontColor('#000000');
                });
                $sheet->cell('V41', function($cell) {
                    // manipulate the cel
                    $cell->setValue('CESAREAS');
                    $cell->setFontSize('10');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('V41', function($cells) {
                    $cells->setBackground('#BADAC8');
                    $cells->setFontColor('#000000');
                });
                $sheet->mergeCells('W41:Y41');
                $sheet->cell('W41', function($cell) {
                    // manipulate the cel
                    $cell->setValue('ÚLTIMA MENSTRUACIÓN');
                    $cell->setFontSize('10');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('W41:Y41', function($cells) {
                    $cells->setBackground('#BADAC8');
                    $cells->setFontColor('#000000');
                });
                $sheet->mergeCells('Z41:AA41');
                $sheet->cell('Z41', function($cell) {
                    // manipulate the cel
                    $cell->setValue('ÚLTIMO PARTO');
                    $cell->setFontSize('10');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('Z41:AA41', function($cells) {
                    $cells->setBackground('#BADAC8');
                    $cells->setFontColor('#000000');
                });
                $sheet->cell('AB41', function($cell) {
                    // manipulate the cel
                    $cell->setValue('ÚLTIMA CITOLOGÍA');
                    $cell->setFontSize('10');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('AB41', function($cells) {
                    $cells->setBackground('#BADAC8');
                    $cells->setFontColor('#000000');
                });
                //FILA 14
                $sheet->cell('A42', function($cell) {
                    // manipulate the cel
                    $cell->setFontSize('10');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('A42', function($cells) {
                    $cells->setBackground('#F1FD77');
                    $cells->setFontColor('#000000');
                });
                $sheet->cell('B42', function($cell) {
                    // manipulate the cel
                    $cell->setFontSize('10');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('B42', function($cells) {
                    $cells->setBackground('#F1FD77');
                    $cells->setFontColor('#000000');
                });
                $sheet->cell('C42', function($cell) {
                    // manipulate the cel
                    $cell->setFontSize('10');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('C42', function($cells) {
                    $cells->setBackground('#F1FD77');
                    $cells->setFontColor('#000000');
                });
                $sheet->cell('D42', function($cell) {
                    // manipulate the cel
                    $cell->setFontSize('10');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('D42', function($cells) {
                    $cells->setBackground('#F1FD77');
                    $cells->setFontColor('#000000');
                });
                $sheet->cell('E42', function($cell) {
                    // manipulate the cel
                    $cell->setFontSize('10');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('E42', function($cells) {
                    $cells->setBackground('#F1FD77');
                    $cells->setFontColor('#000000');
                });
                $sheet->cell('F42', function($cell) {
                    // manipulate the cel
                    $cell->setFontSize('10');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('F42', function($cells) {
                    $cells->setBackground('#F1FD77');
                    $cells->setFontColor('#000000');
                });
                $sheet->mergeCells('G42:H42');
                $sheet->cell('G42', function($cell) {
                    // manipulate the cel
                    $cell->setFontSize('10');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('G42:H42', function($cells) {
                    $cells->setBackground('#F1FD77');
                    $cells->setFontColor('#000000');
                });
                $sheet->cell('I42', function($cell) {
                    // manipulate the cel
                    $cell->setFontSize('10');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('I42', function($cells) {
                    $cells->setBackground('#F1FD77');
                    $cells->setFontColor('#000000');
                });
                $sheet->cell('J42', function($cell) {
                    // manipulate the cel
                    $cell->setFontSize('10');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('J42', function($cells) {
                    $cells->setBackground('#F1FD77');
                    $cells->setFontColor('#000000');
                });
                $sheet->cell('K42', function($cell) {
                    // manipulate the cel
                    $cell->setFontSize('10');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('K42', function($cells) {
                    $cells->setBackground('#F1FD77');
                    $cells->setFontColor('#000000');
                });
                $sheet->mergeCells('L42:M42');
                $sheet->cell('L42', function($cell) {
                    // manipulate the cel
                    $cell->setFontSize('10');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('L42:M42', function($cells) {
                    $cells->setBackground('#F1FD77');
                    $cells->setFontColor('#000000');
                });
                $sheet->mergeCells('N42:O42');
                $sheet->cell('N42', function($cell) {
                    // manipulate the cel
                    $cell->setFontSize('10');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('P42', function($cell) {
                    // manipulate the cel
                    $cell->setFontSize('10');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('Q42:R42');
                $sheet->cell('Q42', function($cell) {
                    // manipulate the cel
                    $cell->setFontSize('10');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('S42', function($cell) {
                    // manipulate the cel
                    $cell->setFontSize('10');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('T42', function($cell) {
                    // manipulate the cel
                    $cell->setFontSize('10');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('U42', function($cell) {
                    // manipulate the cel
                    $cell->setFontSize('10');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('V42', function($cell) {
                    // manipulate the cel
                    $cell->setFontSize('10');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('W42:Y42');
                $sheet->cell('W42', function($cell) {
                    // manipulate the cel
                    $cell->setFontSize('10');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('Z42:AA42');
                $sheet->cell('Z42', function($cell) {
                    // manipulate the cel
                    $cell->setFontSize('10');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AB42', function($cell) {
                    // manipulate the cel
                    $cell->setFontSize('10');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('A43:AB43');
                $sheet->cell('A43', function($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setFontSize('10');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('A44:AB44');
                $sheet->cell('A44', function($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setFontSize('10');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('T46', function($cell) {
                    // manipulate the cel
                    $cell->setValue('CÓDIGO');
                    $cell->setFontSize('10');
                    $cell->setAlignment('center');
                    $cell->setBorder('', '', '', '');
                });
                $sheet->mergeCells('A47:B47');
                $sheet->cell('A47', function($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setFontSize('10');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('A47:B47', function($cells) {
                    $cells->setBackground('#BADAC8');
                    $cells->setFontColor('#000000');
                });
                $sheet->mergeCells('C47:E47');
                $sheet->cell('C47', function($cell) use($procedimiento){
                    // manipulate the cel
                    $fecha = date_format(new DateTime($procedimiento->created_at), 'Y-m-d');
                    $cell->setValue($fecha);
                    $cell->setFontSize('10');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('F47:G47');
                $sheet->cell('F47', function($cell){
                    // manipulate the cel
                    $cell->setValue('HORA');
                    $cell->setFontSize('10');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('F47:G47', function($cells) {
                    $cells->setBackground('#BADAC8');
                    $cells->setFontColor('#000000');
                });
                $sheet->mergeCells('H47:J47');
                $sheet->cell('H47', function($cell) use ($procedimiento){
                    // manipulate the cel
                    $hora = date_format(new DateTime($procedimiento->created_at), 'H:i:s');
                    $cell->setValue($hora);
                    $cell->setFontSize('10');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('K47:N47');
                $sheet->cell('K47', function($cell){
                    // manipulate the cel
                    $cell->setValue('NOMBRE DEL PROFESIONAL');
                    $cell->setFontSize('10');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('K47:N47', function($cells) {
                    $cells->setBackground('#BADAC8');
                    $cells->setFontColor('#000000');
                });
                $sheet->mergeCells('O47:S47');
                $sheet->cell('O47', function($cell) use ($procedimiento){
                    // manipulate the cel
                    $profesional = User::find($procedimiento->id_doctor_examinador);
                    if(!is_null($profesional)){
                        $cell->setValue($profesional->nombre1 . ' ' . $profesional->apellido1);
                    }
                    else{
                        $cell->setValue('');
                    }
                    $cell->setFontSize('10');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('T47', function($cell){
                    // manipulate the cel
                    $cell->setFontSize('10');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('U47:V47');
                $sheet->cell('U47', function($cell){
                    // manipulate the cel
                    $cell->setValue('FIRMA');
                    $cell->setFontSize('10');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('U47:V47', function($cells) {
                    $cells->setBackground('#BADAC8');
                    $cells->setFontColor('#000000');
                });
                //aqui va la firma
                
                if($ruta_firma!=""){
                    $objDrawing = new PHPExcel_Worksheet_Drawing;
                    $objDrawing->setPath($ruta_firma);
                    $objDrawing->setCoordinates('W47');
                    // $w = $objDrawing->getWidth();
                    // $h =$objDrawing->getHeight();
                    $objDrawing->setOffsetX(5);
                    $objDrawing->setOffsetY(5);
                    $objDrawing->setHeight(80);
                    $objDrawing->setWorksheet($sheet);
                }
                
                $sheet->mergeCells('W47:Z47');
                $sheet->cell('W47', function($cell){
                    // manipulate the cel
                    $cell->setFontSize('10');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AA47', function($cell){
                    // manipulate the cel
                    $cell->setValue('NÚMERO DE HOJA');
                    $cell->setFontSize('10');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('AA47', function($cells) {
                    $cells->setBackground('#BADAC8');
                    $cells->setFontColor('#000000');
                });
                $sheet->cell('AB47', function($cell){
                    // manipulate the cel
                    $cell->setFontSize('10');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('A48:G48');
                $sheet->cell('A48', function($cell){
                    // manipulate the cel
                    $cell->setValue('SNS-MSP / HCU-form.013A / 2008');
                    $cell->setFontSize('12');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('left');
                });
                $sheet->mergeCells('V48:AB48');
                $sheet->cell('V48', function($cell){
                    // manipulate the cel
                    $cell->setValue('HISTOPATOLOGÍA-SOLICITUD');
                    $cell->setFontSize('12');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('right');
                });
                // FORMATEO DE COLUMNAS F Y H A DOS DECIMALES
                // $sheet->setColumnFormat(array(
                //     'AA' => '0.00',
                //     'AC' => '0.00', 
                //     'AD' => '0.00', 
                //     'AE' => '0.00', 
                //     'AH' => '0.00', 
                // ));

                /*foreach($datos_empl as $value){

                }*/
            });
            //PRIMERA FILA
            $excel->getActiveSheet()->getStyle('A3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('I3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('M3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('P3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('V3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            //SEGUNDA FILA
            $excel->getActiveSheet()->getStyle('A5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('I5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('M5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('P5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('R5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('T5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('V5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            
            //TERCERA FILA
            $excel->getActiveSheet()->getStyle('A8')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('E8')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('J8')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('O8')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('T8')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('V8')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

            //CUARTA FILA
            $excel->getActiveSheet()->getStyle('A9')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('E9')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('J9')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('O9')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('T9')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('V9')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

            //QUINTA FILA
            $excel->getActiveSheet()->getStyle('A11')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('I11')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('L11')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('N11')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('O11')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('P11')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('Y11')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

            //SEXTA FILA
            $excel->getActiveSheet()->getStyle('A12')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('I12')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('L12')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('N12')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('O12')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('P12')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('R12')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('S12')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('T12')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('U12')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('V12')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('X12')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('Y12')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

            //SEPTIMA FILA
            $excel->getActiveSheet()->getStyle('A14')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

            //OCTAVA FILA
            $excel->getActiveSheet()->getStyle('A15')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('D15')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('E15')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('G15')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('I15')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('L15')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            
            //NOVENA FILA
            $excel->getActiveSheet()->getStyle('A18')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('A19')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
            //ONCEAVA FILA
            $excel->getActiveSheet()->getStyle('A25')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('L25')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('I25')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('N25')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('P25')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('Q25')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('S25')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('Y25')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

            //DOCEAVA FILA
            $excel->getActiveSheet()->getStyle('A26')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('N26')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('P26')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('Q26')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('S26')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->getActiveSheet()->getStyle('A27')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('N27')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('P27')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('Q27')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('S27')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->getActiveSheet()->getStyle('A28')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('N28')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('P28')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('Q28')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('S28')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->getActiveSheet()->getStyle('A29')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('N29')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('P29')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('Q29')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('S29')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->getActiveSheet()->getStyle('A30')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('N30')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('P30')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('Q30')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('S30')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            
            //TRECEAVA FILA
            $excel->getActiveSheet()->getStyle('A32')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('A33')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('A34')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('A35')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('A36')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('A37')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

            //
            $excel->getActiveSheet()->getStyle('A41')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('B41')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('C41')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('D41')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('E41')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('F41')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('G41')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('I41')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('J41')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('K41')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('L41')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('N41')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('P41')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('Q41')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('S41')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('T41')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('U41')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('V41')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('W41')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('Z41')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('AB41')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->getActiveSheet()->getStyle('T46')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->getActiveSheet()->getStyle('A47')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('C47')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('F47')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('H47')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('K47')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('O47')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('T47')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('U47')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('W47')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('AA47')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $excel->getActiveSheet()->getStyle('AB47')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->getActiveSheet()->getStyle('A19')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('I25')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('Y25')->getAlignment()->setWrapText(true);

            $excel->getActiveSheet()->getStyle('A47')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('C47')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('F47')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('H47')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('K47')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('O47')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('T47')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('U47')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('W47')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('AA47')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('AB47')->getAlignment()->setWrapText(true);
            //ROTACION VERTICAL
            $excel->getActiveSheet()->getStyle('L40')->getAlignment()->setTextRotation(90);
            $excel->getActiveSheet()->getStyle('A41')->getAlignment()->setTextRotation(90);
            $excel->getActiveSheet()->getStyle('B41')->getAlignment()->setTextRotation(90);
            $excel->getActiveSheet()->getStyle('C41')->getAlignment()->setTextRotation(90);
            $excel->getActiveSheet()->getStyle('D41')->getAlignment()->setTextRotation(90);
            $excel->getActiveSheet()->getStyle('E41')->getAlignment()->setTextRotation(90);
            $excel->getActiveSheet()->getStyle('F41')->getAlignment()->setTextRotation(90);
            $excel->getActiveSheet()->getStyle('G41')->getAlignment()->setTextRotation(90);
            $excel->getActiveSheet()->getStyle('I41')->getAlignment()->setTextRotation(90);
            $excel->getActiveSheet()->getStyle('J41')->getAlignment()->setTextRotation(90);
            $excel->getActiveSheet()->getStyle('K41')->getAlignment()->setTextRotation(90);
            $excel->getActiveSheet()->getStyle('L41')->getAlignment()->setTextRotation(90);
            $excel->getActiveSheet()->getStyle('N41')->getAlignment()->setTextRotation(90);
            $excel->getActiveSheet()->getStyle('P41')->getAlignment()->setTextRotation(90);
            $excel->getActiveSheet()->getStyle('Q41')->getAlignment()->setTextRotation(90);
            $excel->getActiveSheet()->getStyle('S41')->getAlignment()->setTextRotation(90);
            $excel->getActiveSheet()->getStyle('T41')->getAlignment()->setTextRotation(90);
            $excel->getActiveSheet()->getStyle('U41')->getAlignment()->setTextRotation(90);
            $excel->getActiveSheet()->getStyle('V41')->getAlignment()->setTextRotation(90);
            //WRAPTEXT
            $excel->getActiveSheet()->getStyle('A41')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('B41')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('C41')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('D41')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('E41')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('F41')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('G41')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('I41')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('J41')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('K41')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('L41')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('N41')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('P41')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('Q41')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('S41')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('T41')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('U41')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('V41')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('W41')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('Z41')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('AB41')->getAlignment()->setWrapText(true);
            //ALTURA DE FILAS
            $excel->getActiveSheet()->getRowDimension('8')->setRowHeight(30);
            $excel->getActiveSheet()->getRowDimension('9')->setRowHeight(30);
            $excel->getActiveSheet()->getRowDimension('12')->setRowHeight(30);
            $excel->getActiveSheet()->getRowDimension('14')->setRowHeight(30);
            $excel->getActiveSheet()->getRowDimension('15')->setRowHeight(30);
            $excel->getActiveSheet()->getRowDimension('16')->setRowHeight(30); 
            $excel->getActiveSheet()->getRowDimension('18')->setRowHeight(30);           
            $excel->getActiveSheet()->getRowDimension('19')->setRowHeight(35);
            $excel->getActiveSheet()->getRowDimension('20')->setRowHeight(35);
            $excel->getActiveSheet()->getRowDimension('21')->setRowHeight(35);
            $excel->getActiveSheet()->getRowDimension('25')->setRowHeight(30);
            $excel->getActiveSheet()->getRowDimension('26')->setRowHeight(30);
            $excel->getActiveSheet()->getRowDimension('27')->setRowHeight(30);
            $excel->getActiveSheet()->getRowDimension('28')->setRowHeight(30);
            $excel->getActiveSheet()->getRowDimension('29')->setRowHeight(30);
            $excel->getActiveSheet()->getRowDimension('30')->setRowHeight(30);
            $excel->getActiveSheet()->getRowDimension('32')->setRowHeight(30);
            $excel->getActiveSheet()->getRowDimension('33')->setRowHeight(30);
            $excel->getActiveSheet()->getRowDimension('34')->setRowHeight(30);
            $excel->getActiveSheet()->getRowDimension('35')->setRowHeight(30);
            $excel->getActiveSheet()->getRowDimension('36')->setRowHeight(30);
            $excel->getActiveSheet()->getRowDimension('37')->setRowHeight(30);
            $excel->getActiveSheet()->getRowDimension('40')->setRowHeight(30);
            $excel->getActiveSheet()->getRowDimension('41')->setRowHeight(50);
            $excel->getActiveSheet()->getRowDimension('42')->setRowHeight(30);
            $excel->getActiveSheet()->getRowDimension('47')->setRowHeight(70);
            //ANCHO DE COLUMNAS
            $excel->getActiveSheet()->getColumnDimension("A")->setWidth(5)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("B")->setWidth(5)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("C")->setWidth(5)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("D")->setWidth(6)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("E")->setWidth(6)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("F")->setWidth(5)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("G")->setWidth(3)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("H")->setWidth(3)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("I")->setWidth(6)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("J")->setWidth(6)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("K")->setWidth(6)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("L")->setWidth(3)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("M")->setWidth(4)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("N")->setWidth(4)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("O")->setWidth(4)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("P")->setWidth(5)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("Q")->setWidth(5)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("R")->setWidth(5)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("S")->setWidth(6)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("T")->setWidth(7)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("U")->setWidth(6)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("V")->setWidth(6)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("W")->setWidth(5)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("X")->setWidth(5)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("Y")->setWidth(5)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("Z")->setWidth(5)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AA")->setWidth(10)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AB")->setWidth(10)->setAutosize(false);
        //  $excel->getActiveSheet()->getStyle('Q4')->getAlignment()->setWrapText(true);

        })->export('xlsx');
        

    }

    public function verificar_sesion($id_empresa){
        if(!is_null($id_empresa)){
            return redirect()->to('/login');
        }
    }

    public function subir_insumos_2023(){

        Excel::filter('chunk')->load('insumos2023.xlsx')->chunk(600, function ($reader) {
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $idusuario  = Auth::user()->id;
            
            foreach ($reader as $book) {
                $tipo = $book->tipo;
                $codigo = $book->codigo;
                $precio = $book->precio;    
                //dd($book);
                $ap_procedimiento = ApProcedimiento::where('tipo',$tipo)->where('codigo',$codigo)->first();
                if (!is_null($ap_procedimiento)) {
                    //dd($book);
                    $ap_procedimiento->update([
                        'valor' => $precio,
                        'ip_creacion' => 'AC0106',    
                    ]);

                }    
            }

        });

        return "ok";

    }

    public function actualiza_plano_2023(){

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        
        $ap_procedimiento = ApProcedimiento::where('ip_creacion','AC0806')->get();
        //dd($ap_procedimiento);
        foreach ($ap_procedimiento as $value){

            $detalles = Archivo_Plano_Detalle::where('tipo',$value->tipo)->where('codigo',$value->codigo)->where('fecha','>=','2023-01-01 00:00:00')->where('fecha','<','2023-05-01 00:00:00')->get();
            //dd($detalles);
            foreach($detalles as $detalle){

                $cabecera = $detalle->cabecera;
                if($cabecera->id_seguro == 2){
                    if($cabecera->id_tipo_seguro != 7 and $cabecera->id_tipo_seguro != 9){
                        if($value->iva > 0){

                            $valor = $value->valor; //308
                            $valor = round($valor,2);
                            $valor_unitario = $valor *100/110; //280
                            $valor_unitario = round($valor_unitario,2);
                            $subtotal = $detalle->cantidad*$valor_unitario;//208
                            $subtotal = round($subtotal,2);
                            $porcentaje10 = $subtotal * 0.1;//20.8
                            $porcentaje10 = round($porcentaje10,2);
                            $iva = $subtotal * 0.12; //33,60
                            $total = $subtotal + $porcentaje10 + $iva; //262,40
                            $total = round($total,2);
                            $detalle->update([
                                'valor' => $valor,
                                'subtotal' => $subtotal,
                                'valor_unitario'=> $valor_unitario,
                                'porcentaje10' => $porcentaje10,
                                'iva' => $iva,
                                'total' => $total,
                                'total_solicitado_usd' => $total,
                                'ip_creacion' => 'AC150623'
                            ]);
                        }else{
                            $valor = $value->valor;
                            $valor = round($valor,2);
                            $valor_unitario = $valor;
                        
                            $subtotal = $detalle->cantidad * $valor;
                            
                            $total = $subtotal;
                        
                            $detalle->update([
                                'valor' => $valor,
                                'subtotal' => $subtotal,
                                'valor_unitario'=> $valor_unitario,
                                'total' => $total,
                                'total_solicitado_usd' => $total,
                                'ip_creacion' => 'AC150623'
                            ]);

                        }
                    }    
                        
                }

            }

        }
                
        return "ok";

    }

    /*public function obtener_cabecera($tipo_seg,$cob_compar,$empresa,$seguro,$mes_plano)
    {
        if($tipo_seg == '1'){
            $archivo_plano_1 = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->whereIn('archivo_plano_cabecera.id_tipo_seguro',['1','2','3','4','5'])
            ->where('archivo_plano_cabecera.id_seguro', $seguro)
            ->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            ->where('archivo_plano_cabecera.estado', '1')
            ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name') , 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'archivo_plano_cabecera.fecha_alt', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres','archivo_plano_cabecera.id as id_plano')
            //->orderby('archivo_plano_cabecera.id_tipo_seguro')
            ->orderby('full_name')
            ->orderby('archivo_plano_cabecera.fecha_ing')
            ->get();
            $cont = $archivo_plano_1->count(); 
        }else{
            $archivo_plano_1 = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->where('archivo_plano_cabecera.id_tipo_seguro',$tipo_seg)
            ->where('archivo_plano_cabecera.id_seguro', $seguro)
            ->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            ->where('archivo_plano_cabecera.estado', '1')
            ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name') , 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'archivo_plano_cabecera.fecha_alt', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres','archivo_plano_cabecera.id as id_plano')
            ->orderby('archivo_plano_cabecera.id_tipo_seguro')
            ->orderby('full_name')
            ->orderby('archivo_plano_cabecera.fecha_ing')
            ->get();
            $cont = $archivo_plano_1->count(); 
        }*/
    public function obtener_cabecera($tipo_seg,$cob_compar,$empresa,$seguro,$mes_plano)
    {
        //$tipo_seg = 'ACTIVO' 'JUBILADO' 'MONTEPIO'
        $archivo_plano = Archivo_Plano_Cabecera::join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->where('archivo_plano_cabecera.mes_plano', $mes_plano)
            //->whereIn('archivo_plano_cabecera.id_tipo_seguro',['1','2','3','4','5'])
            ->where('archivo_plano_cabecera.id_seguro', $seguro)
            ->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            ->where('archivo_plano_cabecera.estado', '1')
            ->where('tseg.tipo_principal', $tipo_seg)
            ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name') , 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'archivo_plano_cabecera.fecha_alt', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres','archivo_plano_cabecera.id as id_plano', 'archivo_plano_cabecera.id_hc_procedimimentos', 'archivo_plano_cabecera.id_cod_deriva', 'archivo_plano_cabecera.atencion')
            //->orderby('archivo_plano_cabecera.id_tipo_seguro')
            ->orderby('full_name')
            ->orderby('archivo_plano_cabecera.fecha_ing')
            ->orderby('archivo_plano_cabecera.id')
            ->get();

        

        return $archivo_plano;

    }

    public function obtener_detalles_plano($id_cab){
        $detalles = Archivo_Plano_Detalle::where('archivo_plano_detalle.id_ap_cabecera', $id_cab)
        ->join('ap_tipo_examen as tipo_ex', 'tipo_ex.tipo', 'archivo_plano_detalle.tipo')
        ->where('archivo_plano_detalle.estado', '1')
        ->orderby('tipo_ex.orden_plano')
        ->select('archivo_plano_detalle.*','tipo_ex.tipo_ex')
        ->get();

        return $detalles;
    }

    public function numera_carpetas_v2023($mes_plano, $seguro, $tipo_seg, $empresa)
    {

        $cob_compar = null;
        $archivo_plano_1 = $this->obtener_cabecera('ACTIVO',$cob_compar,$empresa,$seguro,$mes_plano);//dd($archivo_plano_1);
        //array_push($arr_cabeceras,$archivo_plano_1);//dd($arr_cabeceras);
        $arr_cabeceras[0] = $archivo_plano_1;
        $archivo_plano_2 = $this->obtener_cabecera('JUBILADO',$cob_compar,$empresa,$seguro,$mes_plano);
        //array_push($arr_cabeceras,$archivo_plano_2);
        $arr_cabeceras[1] = $archivo_plano_2;
        $archivo_plano_3 = $this->obtener_cabecera('MONTEPIO',$cob_compar,$empresa,$seguro,$mes_plano);
        //array_push($arr_cabeceras,$archivo_plano_3);
        $arr_cabeceras[2] = $archivo_plano_3;
        $cob_compar = 3;//ISSFA-ACTIVOS
        $archivo_plano_4 = $this->obtener_cabecera('ACTIVO',$cob_compar,$empresa,$seguro,$mes_plano);
        //array_push($arr_cabeceras,$archivo_plano_4);
        $arr_cabeceras[3] = $archivo_plano_4;
        $archivo_plano_5 = $this->obtener_cabecera('JUBILADO',$cob_compar,$empresa,$seguro,$mes_plano);
        //array_push($arr_cabeceras,$archivo_plano_5);
        $arr_cabeceras[4] = $archivo_plano_5;
        $archivo_plano_6 = $this->obtener_cabecera('MONTEPIO',$cob_compar,$empresa,$seguro,$mes_plano);
        //array_push($arr_cabeceras,$archivo_plano_6);
        $arr_cabeceras[5] = $archivo_plano_6;
        $cob_compar = 6;//ISSPOL-ACTIVOS
        $archivo_plano_7 = $this->obtener_cabecera('ACTIVO',$cob_compar,$empresa,$seguro,$mes_plano);
        //array_push($arr_cabeceras,$archivo_plano_7);
        $arr_cabeceras[6] = $archivo_plano_7;
        $archivo_plano_8 = $this->obtener_cabecera('JUBILADO',$cob_compar,$empresa,$seguro,$mes_plano);
        //array_push($arr_cabeceras,$archivo_plano_8);
        $arr_cabeceras[7] = $archivo_plano_8;
        $archivo_plano_9 = $this->obtener_cabecera('MONTEPIO',$cob_compar,$empresa,$seguro,$mes_plano);
        //array_push($arr_cabeceras,$archivo_plano_9);
        $arr_cabeceras[8] = $archivo_plano_9;
                
        $x = 0;           
        for($xv = 0; $xv < 9 ; $xv++){
            
            foreach($arr_cabeceras[$xv] as $value){
                
                $x++;
                $cabecera = Archivo_Plano_Cabecera::find($value->id_plano);
                $cabecera->update(['atencion' => $x]);
            }
        }
        
        return "ok";
    }  

    //VERSION 2023 de Archivo Plano 
    public function genera_ap_excel_v2023(Request $request)
    {
        //ini_set('memory_limit', '1024M');
        //dd("sistemas");
        $mes_plano = $request['mes_plano'];
        $seguro     = $request->seguro;
        $tipo_seg   = $request->id_tipo_seguro;
        //$cob_compar = $request['id_cobertura_comp'];
        $empresa    = $request->id_empresa;

        $this->numera_carpetas_v2023($mes_plano, $seguro, $tipo_seg, $empresa);


        $texto_apellido_1 = null;
        $texto_apellido_2 = null;
        $texto_final      = null;
        $texto_fin        = null;
        $texto_nombre_1   = null;
        $texto_nombre_2   = null;
        $cob_compar = null;

        $caract_remplazar_1 = array("¨", "$", "%", "&", "/", "(", ")", "#", "@", "|", "!", "/", "+", "-", "}", "{", ">", "< ", ";", ",", ":",
            ".", "*", ">", "<", "[", "]", "?", "¿", "º", "~");

        $caract_remplazar_2 = array("Ñ", "ñ");

        //ACTIVOS

        Excel::create('Archivo Plano General ', function ($excel) use ($tipo_seg, $cob_compar, $empresa, $seguro, $mes_plano,$caract_remplazar_1, $caract_remplazar_2) {
            $excel->sheet(date('Y-m-d'), function ($sheet) use ($tipo_seg, $cob_compar, $empresa, $seguro, $mes_plano, $caract_remplazar_1, $caract_remplazar_2) {

                $i = 1; $x = 0; $id_temporal = 0;

                $archivo_plano_1 = [];
                $archivo_plano_2 = [];
                $archivo_plano_3 = [];
                $archivo_plano_4 = [];
                $archivo_plano_5 = [];
                $archivo_plano_6 = [];
                $archivo_plano_7 = [];
                $archivo_plano_8 = [];
                $archivo_plano_9 = [];
                $arr_cabeceras   = [];

                $cob_compar = null;
                $archivo_plano_1 = $this->obtener_cabecera('ACTIVO',$cob_compar,$empresa,$seguro,$mes_plano);//dd($archivo_plano_1);
                //array_push($arr_cabeceras,$archivo_plano_1);//dd($arr_cabeceras);
                $arr_cabeceras[0] = $archivo_plano_1;
                $archivo_plano_2 = $this->obtener_cabecera('JUBILADO',$cob_compar,$empresa,$seguro,$mes_plano);
                //array_push($arr_cabeceras,$archivo_plano_2);
                $arr_cabeceras[1] = $archivo_plano_2;
                $archivo_plano_3 = $this->obtener_cabecera('MONTEPIO',$cob_compar,$empresa,$seguro,$mes_plano);
                //array_push($arr_cabeceras,$archivo_plano_3);
                $arr_cabeceras[2] = $archivo_plano_3;
                $cob_compar = 3;//ISSFA-ACTIVOS
                $archivo_plano_4 = $this->obtener_cabecera('ACTIVO',$cob_compar,$empresa,$seguro,$mes_plano);
                //array_push($arr_cabeceras,$archivo_plano_4);
                $arr_cabeceras[3] = $archivo_plano_4;
                $archivo_plano_5 = $this->obtener_cabecera('JUBILADO',$cob_compar,$empresa,$seguro,$mes_plano);
                //array_push($arr_cabeceras,$archivo_plano_5);
                $arr_cabeceras[4] = $archivo_plano_5;
                $archivo_plano_6 = $this->obtener_cabecera('MONTEPIO',$cob_compar,$empresa,$seguro,$mes_plano);
                //array_push($arr_cabeceras,$archivo_plano_6);
                $arr_cabeceras[5] = $archivo_plano_6;
                $cob_compar = 6;//ISSPOL-ACTIVOS
                $archivo_plano_7 = $this->obtener_cabecera('ACTIVO',$cob_compar,$empresa,$seguro,$mes_plano);
                //array_push($arr_cabeceras,$archivo_plano_7);
                $arr_cabeceras[6] = $archivo_plano_7;
                $archivo_plano_8 = $this->obtener_cabecera('JUBILADO',$cob_compar,$empresa,$seguro,$mes_plano);
                //array_push($arr_cabeceras,$archivo_plano_8);
                $arr_cabeceras[7] = $archivo_plano_8;
                $archivo_plano_9 = $this->obtener_cabecera('MONTEPIO',$cob_compar,$empresa,$seguro,$mes_plano);
                //array_push($arr_cabeceras,$archivo_plano_9);
                $arr_cabeceras[8] = $archivo_plano_9;
                //dd($arr_cabeceras);
                $arr_hd = [ '76700', '91034', '91037', '395162', '395272', '91010', '99204', '91110', '91122' ];
                   
                for($xv = 0; $xv < 9 ; $xv++){
                    //dd($xkey);
                    foreach($arr_cabeceras[$xv] as $value){
                        //dd($cabeceras);
                        
                            //dd($value);
                            /*if ($value->id_paciente != $id_temporal) {
                                $id_temporal = $value->id_paciente;
                                $x++;
                            }*/
                            $x++;
                            $detalles = $this->obtener_detalles_plano($value->id_plano);
                            
                            //dd($detalles);
                            $acum_hd = '';
                            foreach($detalles as $detalle){
                                //if($detalle->codigo == '43259'){
                                //    dd($detalle);
                                //}
                                //$ap_procedimiento = Ap_Procedimiento::where('tipo', $detalle->tipo)->where('codigo', $detalle->codigo)->first();
                                $tipo_examen = '';
                                $ap_tipo_examen = Ap_Tipo_Examen::where('tipo',$detalle->tipo)->first();
                                if(!is_null($ap_tipo_examen)){
                                    $tipo_examen = $ap_tipo_examen->tipo_ex;
                                }

                                $sheet->cell('A' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    $cell->setValue('515');
                                });

                                $sheet->cell('B' . $i, function ($cell) use ($value, $x) {
                                    // manipulate the cel
                                    //$cell->setValue($x);
                                    $cell->setValue($value->atencion);
                                });

                                //$fecha_ing     = substr($value->fecha_ing, 0, 10);
                                $fecha_ing     = substr($detalle->fecha, 0, 10);
                                $fecha_ing_inv = date("d/m/Y", strtotime($fecha_ing));

                                $sheet->cell('C' . $i, function ($cell) use ($fecha_ing_inv) {
                                    // manipulate the cel
                                    $cell->setValue($fecha_ing_inv);
                                });

                                $sheet->cell('D' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    $cell->setValue($value->tiposeguro->tipo);
                                });

                                $sheet->cell('E' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    $cell->setValue($value->id_paciente);

                                });
                                $texto_apellido_1 = '';$texto_apellido_2 = '';$texto_final = '';$texto_fin = '';$texto_nombre_1 = '';$texto_nombre_2 = '';
                                $sheet->cell('F' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2, $texto_apellido_1, $texto_apellido_2, $texto_final, $texto_fin, $texto_nombre_1, $texto_nombre_2) {
                                    if ($value->apellido1 != 'N/A' && $value->apellido1 != '(N/A)') {
                                        $texto_apellido_1 = $value->apellido1;
                                    }

                                    if ($value->apellido2 != 'N/A' && $value->apellido2 != '(N/A)') {
                                        $texto_apellido_2 = $value->apellido2;
                                    }

                                    if ($value->nombre1 != 'N/A' && $value->nombre1 != '(N/A)') {
                                        $texto_nombre_1 = $value->nombre1;
                                    }

                                    if ($value->nombre2 != 'N/A' && $value->nombre2 != '(N/A)') {
                                        $texto_nombre_2 = $value->nombre2;
                                    }

                                    $texto_final = $texto_apellido_1 . ' ' . $texto_apellido_2 . ' ' . $texto_nombre_1 . ' ' . $texto_nombre_2;

                                    if($value->apellido2 == '(N/A)' || $value->apellido2 == ('N/A') || $value->apellido2 == "."){
                                        $texto_final = $texto_apellido_1 . ' ' . $texto_nombre_1 . ' ' . $texto_nombre_2;
                                    }

                                    $texto_fin = str_replace($caract_remplazar_1, "", $texto_final);
                                    $texto_fin = str_replace($caract_remplazar_2, "N", $texto_fin);
                                    $cell->setValue($texto_fin);

                                    /*$cell->setValue($value->apellido1.' '.$value->apellido2.' '.$value->nombre1.' '.$value->nombre2);*/

                                });

                                $sheet->cell('G' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    if ($value->paciente->sexo == 1) {
                                        $cell->setValue('M');
                                    } elseif ($value->paciente->sexo == 2) {
                                        $cell->setValue('F');
                                    }

                                });

                                $fecha_nac     = substr($value->paciente->fecha_nacimiento, 0, 10);
                                $fecha_nac_inv = date("d/m/Y", strtotime($fecha_nac));

                                $sheet->cell('H' . $i, function ($cell) use ($fecha_nac_inv) {
                                    // manipulate the cel
                                    $cell->setValue($fecha_nac_inv);

                                });
                                if ($value->paciente->fecha_nacimiento == null) {
                                    $edad = 0;
                                } else {
                                    $edad = Carbon::createFromDate(substr($value->paciente->fecha_nacimiento, 0, 4), substr($value->paciente->fecha_nacimiento, 5, 2), substr($value->paciente->fecha_nacimiento, 8, 2))->age;
                                }
                                $sheet->cell('I' . $i, function ($cell) use ($value, $edad) {
                                    // manipulate the cel
                                    $cell->setValue($edad);

                                });

                                

                                $tipo_medico = '1';$derivacion = 'CVAV';$sec_der = ''; $tipo_ex = ''; 
                                $hc_id_procedimientos = $value->id_hc_procedimimentos;
                                $hc_procedimiento = hc_procedimientos::find($hc_id_procedimientos);
                                if(!is_null($hc_procedimiento)){
                                    $historia = $hc_procedimiento->historia;
                                    $agenda   = $historia->agenda;   
                                    if($agenda->proc_consul == 0){
                                        $tipo_medico = '8';
                                        $derivacion = 'CVCEC';
                                        $sec_der = '999999999';
                                        $tipo_ex = 'C/EXT';
                                        if($detalle->tipo == 'IM'){
                                           $tipo_ex = 'IMAGEN';
                                        }
                                        if($detalle->tipo == 'EX'){
                                            $tipo_ex = 'LAB';
                                         }
                                    }else{
                                        $sec_der = $agenda->cod_val;
                                        $tipo_ex = 'PAQUE/AMBU';
                                        $tipo_medico = '1';
                                        if($detalle->tipo == 'IM'){
                                            $tipo_ex = 'IMAGEN';
                                        }
                                        if($detalle->tipo == 'EX'){
                                           //$tipo_ex = 'LAB';
                                        }
                                        if( $detalle->tipo == 'AN' or $detalle->tipo == 'TA' ){ 
                                            $tipo_medico = '6';
                                        }

                                    }
                                }



                                if( in_array( $detalle->codigo, $arr_hd ) ){
                                    
                                    if($detalle->tipo == 'P'){
                                            
                                        $tipo_ex = 'HD'; 
                                        $acum_hd = 'HD';       
                                        
                                    }
                                    
                                }
                                if($acum_hd == 'HD'){
                                    if( $detalle->tipo == 'I' or $detalle->tipo == 'IV' or $detalle->tipo == 'IVC' or $detalle->tipo == 'P' or $tipo_examen == 'IMM'){
                                        $tipo_ex = 'HD';
                                    }
                                }    
                               

                                $sheet->cell('J' . $i, function ($cell) use ($tipo_ex) {
                                    // manipulate the cel
                                    $cell->setValue($tipo_ex);

                                });

                                $sheet->cell('K' . $i, function ($cell) use ($detalle, $tipo_examen) {
                                    // manipulate the cel
                                    if ($detalle->tipo == 'IV' || $detalle->tipo == 'I' || $detalle->tipo == 'IVC' || $tipo_examen == 'IMM' ) {
                                        $cell->setValue(' ');
                                    }elseif($detalle->tipo == 'M'){
                                        if($detalle->codigo_medicina == null){
                                            
                                            $codigo_new = $this->guardar_codigo( $detalle->codigo , $detalle->id);
                                            $cell->setValue($codigo_new);
                                        
                                        }else{
                                            
                                            $cell->setValue($detalle->codigo_medicina);
                                        
                                        }
                                        
                                        //$hoja->setCellValueExplicit('A1', $numeroLargo, PHPExcel_Cell_DataType::TYPE_NUMERIC);
                                        
                                    }else {
                                        $cell->setValue($detalle->codigo);
                                    }

                                });

                                //$sheet->setCellValueExplicit('K'.$i, $detalle->codigo_medicina, PHPExcel_Cell_DataType::TYPE_NUMERIC);
                                $sheet->getStyle('K'.$i)->getNumberFormat()->setFormatCode('0');

                                $sheet->cell('L' . $i, function ($cell) use ($detalle, $caract_remplazar_1, $caract_remplazar_2, $tipo_examen) {
                                    // manipulate the

                                    $descrip = str_replace($caract_remplazar_1, "", $detalle->descripcion);
                                    $descrip = str_replace($caract_remplazar_2, "N", $descrip);

                                    if ($detalle->tipo == 'IV' || $detalle->tipo == 'I' || $detalle->tipo == 'IVC' || $tipo_examen == 'IMM') {
                                    
                                        $cell->setValue($descrip);

                                    } else {

                                        $cell->setValue('');

                                    }
                                });

                                $sheet->cell('M' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    $cell->setValue($value->cie10);

                                });

                                $sheet->cell('N' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    $cell->setValue('');

                                });

                                $sheet->cell('O' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    $cell->setValue('');

                                });

                                $sheet->cell('P' . $i, function ($cell) use ($detalle) {
                                    // manipulate the cel
                                    $cell->setValue($detalle->cantidad);

                                });

                                $sheet->cell('Q' . $i, function ($cell) use ($detalle) {
                                    // manipulate the cel
                                    //$cell->setValue(round($detalle->valor, 2));
                                    if($detalle->tipo == 'EX' or $detalle->tipo == 'S'){
                                        $cell->setValue(round($detalle->valor, 4));
                                    }else{    
                                        $cell->setValue(round($detalle->valor_unitario, 4));
                                    }

                                });

                                $sheet->cell('R' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    $cell->setValue('0');

                                });

                                $sheet->cell('S' . $i, function ($cell) use ($value) {
                                    // manipulate the cel

                                    if ($value->parentesco == 'TITULAR') {
                                        $cell->setValue('T');

                                    } elseif ($value->parentesco == 'CONYUGE') {
                                        $cell->setValue('C');

                                    } elseif ($value->parentesco == 'HIJO/HIJA') {
                                        $cell->setValue('H');

                                    } elseif ($value->parentesco == 'PARIENTE') {
                                        $cell->setValue('X');

                                    }

                                });

                                $sheet->cell('T' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    $cell->setValue($value->id_usuario);
                                });

                                $sheet->cell('U' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2) {
                                    // manipulate the cel

                                    $descrip_nombre = str_replace($caract_remplazar_1, "", $value->nombres);
                                    $descrip_nombre = str_replace($caract_remplazar_2, "N", $descrip_nombre);

                                    $cell->setValue($descrip_nombre);

                                });

                                $sheet->cell('V' . $i, function ($cell) use ( $value ) {
                                    // manipulate the cel
                                    $derivacion = Codigo_Derivacion::find($value->id_cod_deriva);
                                    if(!is_null($derivacion)){
                                        $cell->setValue( $derivacion->codigo );
                                    }else{
                                        $cell->setValue( '' );
                                    }
                                    
                                    

                                });

                                $sheet->cell('W' . $i, function ($cell) use ($value, $sec_der) {
                                    // manipulate the cel
                                    $cell->setValue($sec_der);

                                });

                                $sheet->cell('X' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    $cell->setValue('1');

                                });
                            
                                $sheet->cell('Y' . $i, function ($cell) use ($value) {
                                // manipulate the cel
                                    $cell->setValue('D');

                                });

                                $sheet->cell('Z' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    $cell->setValue('0');

                                });

                                $sheet->cell('AA' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    $cell->setValue('');

                                });

                                $sheet->cell('AB' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    $cell->setValue('');

                                });

                                $sheet->cell('AC' . $i, function ($cell) use ($value) {
                                    // manipulate the cel
                                    $cell->setValue('');

                                });

                                $sheet->cell('AD' . $i, function ($cell) use ($detalle) {
                                    // manipulate the cel
                                    if($detalle->porcentaje_iva == 0.12){
                                        $cell->setValue(12);    
                                    }else{
                                        $cell->setValue(0); 
                                    }
                                    

                                });

                                $nuevo_iva = $detalle->valor_unitario * $detalle->porcentaje_iva;
                                $sheet->cell('AE' . $i, function ($cell) use ($detalle, $nuevo_iva) {
                                    // manipulate the cel
                                    
                                    $cell->setValue(round($nuevo_iva, 4));

                                });
                                //***CAMBIOS 2023*/////////
                                $sheet->cell('AF' . $i, function ($cell) use ($detalle,$nuevo_iva) {
                                    // manipulate the cel
                                    /////////////////////////////////
                                    if($detalle->tipo == 'EX' or $detalle->tipo == 'S'){
                                        $vtotal = $detalle->cantidad * $detalle->valor;
                                        $cell->setValue(round($vtotal, 4));
                                    }else{    
                                        $vtotal = $detalle->cantidad * $detalle->valor + $nuevo_iva * $detalle->cantidad;
                                        $cell->setValue(round($vtotal, 4));
                                    }

                                });
                                $sheet->cell('AG' . $i, function ($cell) use ($detalle) {
                                    // manipulate the cel
                                    $valor_ag = $detalle->valor - $detalle->valor_unitario;
                                    if($detalle->tipo == 'EX' or $detalle->tipo == 'S'){
                                        $cell->setValue(0);
                                    }else{    
                                        $cell->setValue(round($valor_ag, 4));
                                    }

                                });
                                $fecha_ing     = substr($value->fecha_ing, 0, 10);
                                $fecha_ing_inv = date("d/m/Y", strtotime($fecha_ing));

                                $sheet->cell('AH' . $i, function ($cell) use ($fecha_ing_inv) {
                                    // manipulate the cel
                                    $cell->setValue($fecha_ing_inv);
                                });
                                $fecha_alt     = substr($value->fecha_alt, 0, 10);
                                $fecha_alt_inv = date("d/m/Y", strtotime($fecha_alt));

                                $sheet->cell('AI' . $i, function ($cell) use ($fecha_alt_inv) {
                                    // manipulate the cel
                                    $cell->setValue($fecha_alt_inv);
                                });

                                $egreso = 1;
                                if($value->id_tipo_egreso != null) {
                                    $egreso = $value->id_tipo_egreso;
                                }
                                $sheet->cell('AJ' . $i, function ($cell) use ($egreso) {
                                    // manipulate the cel
                                    $cell->setValue($egreso);
                                });
                                $cobertura = '';
                                if($value->cobertura == '3' or $value->cobertura == '6')
                                {
                                    $cobertura = 'SI';
                                }else{
                                    $cobertura = 'NO';
                                }
                                $sheet->cell('AK' . $i, function ($cell) use ($cobertura) {
                                    // manipulate the cel
                                    $cell->setValue($cobertura);
                                });
                                $tipo_cob = '';
                                if($value->cobertura == '3'){
                                    $tipo_cob = 'ISSFA';
                                }
                                if($value->cobertura == '6'){
                                    $tipo_cob = 'ISSPOL';
                                }

                                $sheet->cell('AL' . $i, function ($cell) use ($tipo_cob) {
                                    // manipulate the cel
                                    $cell->setValue($tipo_cob);
                                });
                                ///// ESTE DATO DEBE SALIR DEL PACIENTE ----- PILAS VT ------- ////
                                $discapacidad_cer = 'NO';       
                                $sheet->cell('AM' . $i, function ($cell) use ($discapacidad_cer) {
                                    // manipulate the cel
                                    $cell->setValue($discapacidad_cer);
                                });
                                $tipo_pres = 'P';
                                if($detalle->tipo == 'I' or $detalle->tipo == 'IV' or $detalle->tipo == 'IVC' or $tipo_examen == 'IMM'){
                                    $tipo_pres = 'I';
                                }
                                if($detalle->tipo == 'M'){
                                    $tipo_pres = 'M';
                                }

                                $sheet->cell('AN' . $i, function ($cell) use ($tipo_pres) {
                                    // manipulate the cel
                                    $cell->setValue($tipo_pres);
                                });

                                
                                $sheet->cell('AO' . $i, function ($cell) use ($tipo_medico) {
                                    // manipulate the cel
                                    $cell->setValue($tipo_medico);

                                });
                                //**     */
                                $sheet->cell('AQ' . $i, function ($cell) use ($value) {
                                        // manipulate the cel
                                        $cell->setValue('F');

                                });
                                $i++; 
                            }     
                        
                    }

                }


            });

            $excel->getActiveSheet()->getColumnDimension("A")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("B")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("C")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("D")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("E")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("F")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("G")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("H")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("I")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("J")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("K")->setWidth(30)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("L")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("M")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("N")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("O")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("P")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("Q")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("R")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("S")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("T")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("U")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("V")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("W")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("X")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("Y")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("Z")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AA")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AB")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AC")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AD")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AE")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AF")->setWidth(11)->setAutosize(false);
            

        })->export('xlsx');
    }

    public function actualizar_nuevo_codigo(){
        try {
            if ($this->rol()) {
                return response()->view('errors.404');
            }
            ini_set('max_execution_time', 300);
           
            DB::beginTransaction();
            //$total_filas = Excel::filter('chunk')->load('Lista_examen.xlsx')->setActiveSheetIndex(0)->getHighestRow();
            Excel::filter('chunk')->load('medicina_codigo_manta.xlsx')->chunk(100, function ($reader) {
                foreach ($reader as $book) {
                   
                    $id                 = $book->id;
                    $codigo_medicina    = $book->codigo_medicina;
                    $descripcion        = $book->descripcion;
                    $valor              = $book->valor;

                    $item = ApProcedimiento::find($id);
                    if(!is_null($item)){
                        $arr_item = array();
                        if($valor  !=null or $valor != ''){
                            $arr_item['valor'] = $valor;
                        }
                        if($codigo_medicina != null or $codigo_medicina != ''){
                            $arr_item['codigo_medicina'] = $codigo_medicina;
                        }
                        if($descripcion != null or $descripcion != ''){
                            $arr_item['descripcion'] = $descripcion;
                        }
                        $arr_item['ip_creacion'] = 'AC230623';
                        //dd($book, $arr_item);
                        $item->update($arr_item);
                    }
                    if($valor  !=null or $valor != ''){
                        $detalles = Archivo_Plano_Detalle::where('codigo',$item->codigo)->where('tipo','M')->get();
                        foreach($detalles as $detalle){
                            $subtotal = $detalle->cantidad * $valor;
                            $array1 = [
                                'valor'         => $valor,
                                'subtotal'      => $subtotal,
                                'valor_unitario'=> $valor,
                                'total'         => $subtotal,
                                'total_solicitado_usd'  =>  $subtotal,
                                'ip_creacion'       => 'AC230623',
                            ];
                            $detalle->update($array1);
                        }
                    }
                    if($codigo_medicina != null or $codigo_medicina != ''){
                        $detalles = Archivo_Plano_Detalle::where('codigo',$item->codigo)->where('tipo','M')->get();
                        foreach($detalles as $detalle){
                            
                            $array2 = [
                                'codigo_medicina'=> $codigo_medicina,
                                'ip_creacion'       => 'AC230623',
                            ];
                            $detalle->update($array2);
                        }
                    }
                    if($descripcion != null or $descripcion != ''){
                        $detalles = Archivo_Plano_Detalle::where('codigo',$item->codigo)->where('tipo','M')->get();
                        foreach($detalles as $detalle){
                            
                            $array3 = [
                                'descripcion'=> $descripcion,
                                'ip_creacion'       => 'AC230623',
                            ];
                            $detalle->update($array3);
                        }
                    }
                }
                
            });
            DB::commit();
            return 'Registros procesados con exito';
        } catch (\Exception $e) {
            DB::rollBack();
            return ['error' => $e->getMessage(), 'linea' => $e->getLine()];
        }
    }
    public function reporte_consolidado_general_v2023(Request $request)
    {
        $idUsuario = Auth::user()->id;
        
        //ini_set('memory_limit', '10024M');
        //dd($request->all());
        $mes_plano = $request['mes_plano'];
        $seguro       = $request['seguro'];
        $empresa   = $request['id_empresa'];
        $cob_compar = null;

        $sub_cadena_1 = '';
        $sub_cadena_2 = '';

        $texto_nombre = null;

        $caract_remplazar_1 = array("¨", "$", "%", "&", "/", "(", ")", "#", "@", "|", "!", "/", "+", "-", "}", "{", ">", "< ", ";", ",", ":",
            ".", "*", ">", "<", "[", "]", "?", "¿", "º", "~");

        $caract_remplazar_2 = array("Ñ", "ñ");

        if (!is_null($mes_plano)) {
            $sub_cadena_1 = substr($mes_plano, 0, 2);
        }

        if (!is_null($mes_plano)) {
            $sub_cadena_2 = substr($mes_plano, 2, 6);
        }

        $inf_empresa = Empresa::where('id', $empresa)->where('estado', '1')->first();
       // dd($inf_empresa, $empresa);
         
        Excel::create('ReporteConsolidadoIESS', function ($excel) use ($inf_empresa,$cob_compar,$empresa,$seguro,$mes_plano, $sub_cadena_1, $sub_cadena_2,$caract_remplazar_1, $caract_remplazar_2, $texto_nombre,$idUsuario) {
            $excel->sheet('ReporteConsolidadoIESS', function ($sheet) use ($inf_empresa,$cob_compar,$empresa,$seguro,$mes_plano, $sub_cadena_1, $sub_cadena_2,$caract_remplazar_1, $caract_remplazar_2, $texto_nombre,$idUsuario) {

                $archivo_plano_1 = [];
                $archivo_plano_2 = [];
                $archivo_plano_3 = [];
                $archivo_plano_4 = [];
                $archivo_plano_5 = [];
                $archivo_plano_6 = [];
                $archivo_plano_7 = [];
                $archivo_plano_8 = [];
                $archivo_plano_9 = [];
                $arr_cabeceras   = [];
        
                $cob_compar = null;
                $archivo_plano_1 = $this->obtener_cabecera('ACTIVO',$cob_compar,$empresa,$seguro,$mes_plano);//dd($archivo_plano_1);
                //array_push($arr_cabeceras,$archivo_plano_1);//dd($arr_cabeceras);
                $arr_cabeceras[0] = $archivo_plano_1;
                $archivo_plano_2 = $this->obtener_cabecera('JUBILADO',$cob_compar,$empresa,$seguro,$mes_plano);
                //array_push($arr_cabeceras,$archivo_plano_2);
                $arr_cabeceras[1] = $archivo_plano_2;
                $archivo_plano_3 = $this->obtener_cabecera('MONTEPIO',$cob_compar,$empresa,$seguro,$mes_plano);
                //array_push($arr_cabeceras,$archivo_plano_3);
                $arr_cabeceras[2] = $archivo_plano_3;
                $cob_compar = 3;//ISSFA-ACTIVOS
                $archivo_plano_4 = $this->obtener_cabecera('ACTIVO',$cob_compar,$empresa,$seguro,$mes_plano);
                //array_push($arr_cabeceras,$archivo_plano_4);
                $arr_cabeceras[3] = $archivo_plano_4;
                $archivo_plano_5 = $this->obtener_cabecera('JUBILADO',$cob_compar,$empresa,$seguro,$mes_plano);
                //array_push($arr_cabeceras,$archivo_plano_5);
                $arr_cabeceras[4] = $archivo_plano_5;
                $archivo_plano_6 = $this->obtener_cabecera('MONTEPIO',$cob_compar,$empresa,$seguro,$mes_plano);
                //array_push($arr_cabeceras,$archivo_plano_6);
                $arr_cabeceras[5] = $archivo_plano_6;
                $cob_compar = 6;//ISSPOL-ACTIVOS
                $archivo_plano_7 = $this->obtener_cabecera('ACTIVO',$cob_compar,$empresa,$seguro,$mes_plano);
                //array_push($arr_cabeceras,$archivo_plano_7);
                $arr_cabeceras[6] = $archivo_plano_7;
                $archivo_plano_8 = $this->obtener_cabecera('JUBILADO',$cob_compar,$empresa,$seguro,$mes_plano);
                //array_push($arr_cabeceras,$archivo_plano_8);
                $arr_cabeceras[7] = $archivo_plano_8;
                $archivo_plano_9 = $this->obtener_cabecera('MONTEPIO',$cob_compar,$empresa,$seguro,$mes_plano);
                //array_push($arr_cabeceras,$archivo_plano_9);
                $arr_cabeceras[8] = $archivo_plano_9;
                //dd($arr_cabeceras);

                $sheet->setColumnFormat(array(

                    'L' => '$### ### ### ##0.00',
                ));

                $objDrawing = new PHPExcel_Worksheet_Drawing;
                $objDrawing->setPath(base_path() . '/storage/app/logo/iess_logo.png');
                $objDrawing->setCoordinates('L2');
                $objDrawing->setHeight(70);
                $objDrawing->setOffsetX(100);
                $objDrawing->setWorksheet($sheet);

                //$sheet->getStyle('L2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

                $sheet->mergeCells('A2:K2');
                $sheet->cell('A2', function ($cell) use ($inf_empresa) {
                    $cell->setValue('INSTITUTO ECUATORIANO DE SEGURIDAD SOCIAL');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('14');
                });

                $sheet->mergeCells('A4:K4');
                $sheet->cell('A4', function ($cell) use ($inf_empresa) {
                    $cell->setValue('COORDINACIÓN PROVINCIAL DE PRESTACIONES DEL SEGURO DE SALUD '.$inf_empresa->provincia);
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                });

                $sheet->mergeCells('A6:K6');
                $sheet->cell('A6', function ($cell) use ($inf_empresa) {
                    $cell->setValue('PLANILLA CONSOLIDADO DE PRESTACION DE SERVICIO');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('A7:C7');
                $sheet->cell('A7', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Nombre prestador:');
                    $cell->setAlignment('left');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('D7:L7');
                $sheet->cell('D7', function ($cell) use ($inf_empresa) {
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

                $sheet->mergeCells('C8:L8');
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

                $sheet->mergeCells('C9:L9');
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

                $sheet->mergeCells('C10:L10');
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
                $sheet->mergeCells('C11:H11');
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
                    } elseif ($sub_cadena_1 == '12') {
                        $txt_mes = 'NOVIEMBRE';
                    } elseif ($sub_cadena_1 == '12') {
                        $txt_mes = 'DICIEMBRE';
                    }

                    $cell->setValue($txt_mes);
                    $cell->setAlignment('left');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                });
                $sheet->mergeCells('I11:L11');
                $sheet->cell('I11', function ($cell) use ($sub_cadena_2) {
                    // manipulate the cel
                    $cell->setValue('Año:' . ($sub_cadena_2));
                    $cell->setAlignment('left');
                    $cell->setFontWeight('bold');
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

                $sheet->mergeCells('B12:H12');
                $sheet->cell('B12', function ($cell) use ($inf_empresa) {
                    $cell->setValue('NOMBRE DE PACIENTE/USUARIO');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                /*$sheet->cell('H12', function ($cell) use ($inf_empresa) {
                    $cell->setValue('CEDULA');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });*/

                //$sheet->mergeCells('I12:J12');
                /*$sheet->cell('I12', function ($cell) use ($inf_empresa) {
                    $cell->setValue('HISTORIA CLINICA');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

               // $sheet->mergeCells('K12:L12');
                $sheet->cell('J12', function ($cell) use ($inf_empresa) {
                    $cell->setValue('FECHA ADMISION');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });*/
                $sheet->mergeCells('I12:K12');
                $sheet->cell('I12', function ($cell) use ($inf_empresa) {
                    $cell->setValue('FECHA ATENCION');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                //$sheet->mergeCells('N12:O12');
                $sheet->cell('L12', function ($cell) use ($inf_empresa) {
                    $cell->setValue('TOTAL:');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

             
                $x = 0;
                $i = 13;
                $acum_total = 0;
                $value      = null;
                for($xv = 0; $xv < 9 ; $xv++){
                    foreach($arr_cabeceras[$xv] as $value){
                        $x++; $i++;
                        $detalles = $this->obtener_detalles_plano($value->id_plano);
                        $total_ap = 0;

                        $texto_apellido_1 = "";$texto_apellido_2 = "";$texto_nombre_1 = "";$texto_nombre_2 = "";

                        foreach($detalles as $detalle){    

                            if($detalle->tipo == 'EX' or $detalle->tipo == 'S'){
                                $vtotal = $detalle->cantidad * $detalle->valor;
                                $vtotal = round($vtotal, 4);
                            }else{    
                                //$nuevo_iva = $detalle->valor_unitario * $detalle->porcentaje_iva;
                                //$vtotal = $detalle->cantidad * $detalle->valor + $nuevo_iva * $detalle->cantidad;
                                $vtotal = $detalle->cantidad * ($detalle->valor_unitario + $detalle->iva_unitario + $detalle->p10_unitario);
                                //$vtotal = round($vtotal, 4);
                            }
                            $total_ap += $vtotal;
                            $acum_total += $vtotal;
                        }                    

                        $sheet->cell('A' . $i, function ($cell) use ($x) {
                            // manipulate the cel
                            $cell->setValue($x);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //NOMBRE DE PACIENTE/USUARIO
                        if ($value->apellido1 != 'N/A' && $value->apellido1 != '(N/A)') {
                            $texto_apellido_1 = $value->apellido1;
                        }

                        if ($value->apellido2 != 'N/A' && $value->apellido2 != '(N/A)') {
                            $texto_apellido_2 = $value->apellido2;
                        }

                        if ($value->nombre1 != 'N/A' && $value->nombre1 != '(N/A)') {
                            $texto_nombre_1 = $value->nombre1;
                        }

                        if ($value->nombre2 != 'N/A' && $value->nombre2 != '(N/A)') {
                            $texto_nombre_2 = $value->nombre2;
                        }

                        $texto_final = $texto_apellido_1 . ' ' . $texto_apellido_2 . ' ' . $texto_nombre_1 . ' ' . $texto_nombre_2;

                        if($value->apellido2 == '(N/A)' || $value->apellido2 == ('N/A') || $value->apellido2 == "."){
                            $texto_final = $texto_apellido_1 . ' ' . $texto_nombre_1 . ' ' . $texto_nombre_2;
                        }

                        $texto_fin = str_replace($caract_remplazar_1, "", $texto_final);
                        $texto_fin = str_replace($caract_remplazar_2, "N", $texto_fin);
                            
                        $sheet->mergeCells('B' . $i . ':H' . $i);
                        $sheet->cell('B' . $i, function ($cell) use ($value, $texto_fin) {
                            // manipulate the cel
                            $cell->setValue($texto_fin);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        //TIPO DE SEGURO
                        /*$sheet->cell('H' . $i, function ($cell) use ($value) {
                            // manipulate the cel

                            $cell->setValue($value->id_paciente);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });*/
                        //BASE0
                        $sheet->mergeCells('I' . $i . ':K' . $i);
                        /*$sheet->cell('I' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_paciente);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        $fecha_ing     = substr($value->fecha_ing, 0, 10);
                        $fecha_ing = date("d/m/Y", strtotime($fecha_ing));
                        $sheet->cell('J' . $i, function ($cell) use ($fecha_ing) {
                            // manipulate the cel
                            $cell->setValue($fecha_ing);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });*/
                        //$fecha_alt     = substr($value->fecha_alt, 0, 10);
                        $fecha_alt     = substr($value->fecha_ing, 0, 10);
                        $fecha_alt = date("d/m/Y", strtotime($fecha_alt));
                        $sheet->cell('I' . $i, function ($cell) use ($fecha_alt) {
                            // manipulate the cel
                            $cell->setValue($fecha_alt);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //SUBTOTAL
                        //$sheet->mergeCells('N' . $i . ':O' . $i);
                        $sheet->cell('L' . $i, function ($cell) use ($total_ap) {
                            // manipulate the cel
                            $cell->setValue($total_ap);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        

                    }
                }   
                
                $i++;

                $sheet->mergeCells('A' . $i . ':K' . $i);
                $sheet->cell('A' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    $cell->setValue('TOTAL');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                });
                //TOTAL
                //$sheet->mergeCells('N' . $i . ':O' . $i);
                $sheet->cell('L' . $i, function ($cell) use ($acum_total) {
                    // manipulate the cel
                    $cell->setValue($acum_total);
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                });

                /*$i = $i + 7;
                $sheet->mergeCells('G' . $i . ':J' . $i);
                $sheet->cell('G' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('t', '', 'thin', '');

                });
                $i++;
                $sheet->mergeCells('G' . $i . ':J' . $i);
                $sheet->cell('G' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    $cell->setValue('FIRMA / SELLO PRESTADOR');
                    $cell->setAlignment('center');
                    $cell->setBorder('t', '', '', '');

                });*/
            });
        })->export('xlsx');

    }

    public function quitar_insumos_covid_2023() {
    
        $seguro = '2';
        $aniomes = ['012023', '022023'];
        $tipos = ['7', '9'];
        $codigos = ['70200003', '70200004'];
        $examenes = ['270051', '270052'];
        $insumos = ['265','331','339','340','324','325','326','335','336','328','330','334','338'];
    
        $cabeceras = Archivo_Plano_Cabecera::where('estado', '1')->whereIn('mes_plano',$aniomes)->where('id_seguro',$seguro)->whereNotIn('id_tipo_seguro', $tipos)->get();
        foreach($cabeceras as $cabecera){

            $detalle = $cabecera->detalles()->whereIn('codigo',$codigos)->first();
            if(!is_null($detalle)){
                //dd($detalle);
                //$examenes_detalles = $cabecera->detalles()->where('tipo','EX')->whereIn('codigo',$examenes);
                $examenes_detalles = Archivo_Plano_Detalle::where('id_ap_cabecera',$cabecera->id)->where('tipo','EX')->whereIn('codigo',$examenes)->get();
                //dd($examenes_detalles);
                foreach($examenes_detalles as $detalle){
                    $detalle->update([
                        'estado' => 0,
                        'clasificador' => 'COVID',
                    ]);
                }

                //$insumos_detalles = $cabecera->detalles()->where('tipo','IVC')->whereIn('codigo',$insumos);
                $insumos_detalles = Archivo_Plano_Detalle::where('id_ap_cabecera',$cabecera->id)->where('tipo','IVC')->whereIn('codigo',$insumos)->get();
                foreach($insumos_detalles as $detalle){
                    $detalle->update([
                        'estado' => 0,
                        'clasificador' => 'COVID',
                    ]);
                }
                $cabecera->update(['ip_creacion' => 'COVID']);

            }

        }
                
        return "ok";

    }

    
    
    public function actualizar_nuevo_codigo_med(){
        try {
            if ($this->rol()) {
                return response()->view('errors.404');
            }
            ini_set('max_execution_time', 300);
           
            DB::beginTransaction();
            //$total_filas = Excel::filter('chunk')->load('Lista_examen.xlsx')->setActiveSheetIndex(0)->getHighestRow();
            Excel::filter('chunk')->load('medicina_codigo_manta_act.xlsx')->chunk(100, function ($reader) {
                foreach ($reader as $book) {
                    //dd($book);
                    $id                 = $book->id;
                    $codigo_medicina    = $book->codigo_medicina;
                    $descripcion        = $book->descripcion;
                   
                    $item = ApProcedimiento::find($id);
                    if(!is_null($item)){
                        if(is_null($item->codigo_medicina)){
                        
                            $arr_item = array();
                            if($codigo_medicina != null or $codigo_medicina != ''){
                                $arr_item['codigo_medicina'] = $codigo_medicina;
                            }
                            if($descripcion != null or $descripcion != ''){
                                $arr_item['descripcion'] = $descripcion;
                            }
                            $arr_item['ip_creacion'] = 'AC260623';
                            //dd($book, $arr_item);
                            $item->update($arr_item);
                        }
                        
                        if($codigo_medicina != null or $codigo_medicina != ''){
                            $detalles = Archivo_Plano_Detalle::where('codigo',$item->codigo)->where('tipo','M')->get();
                            foreach($detalles as $detalle){
                                
                                $array2 = [
                                    'codigo_medicina'=> $codigo_medicina,
                                    'ip_creacion'       => 'AC260623',
                                ];
                                $detalle->update($array2);
                            }
                        }
                        if($descripcion != null or $descripcion != ''){
                            $detalles = Archivo_Plano_Detalle::where('codigo',$item->codigo)->where('tipo','M')->get();
                            foreach($detalles as $detalle){
                                
                                $array3 = [
                                    'descripcion'=> $descripcion,
                                    'ip_creacion'       => 'AC260623',
                                ];
                                $detalle->update($array3);
                            }
                        }
                    }

                }
                
            });
            DB::commit();
            return 'Registros procesados con exito';
        } catch (\Exception $e) {
            DB::rollBack();
            return ['error' => $e->getMessage(), 'linea' => $e->getLine()];
        }
    }

    public function crear_planilla($id_hc_procedimiento, $id_seguro)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $hc_procedimientos = hc_procedimientos::find($id_hc_procedimiento);
        $historia_clinica = $hc_procedimientos->historia;
        $paciente = $historia_clinica->paciente;
    
        $archivo_plano = Archivo_Plano_Cabecera::where('id_hc_procedimimentos', $id_hc_procedimiento)->where('id_seguro', $id_seguro)->where('estado', '1')->first();
        $anio = substr($historia_clinica->agenda->fechaini, 0, 4);
        $mes = substr($historia_clinica->agenda->fechaini, 5, 2);
        $id_nivel = null;
        $convenio = Convenio::where('id_seguro', $id_seguro)->where('id_empresa', $historia_clinica->agenda->id_empresa)->first();
        
        if (!is_null($convenio)) {
            $id_nivel = $convenio->id_nivel;
        }

        $doctor_ap = null;
        $doctor_seleccionado = Aud_Hc_Procedimientos::where('id_procedimientos_org', $id_hc_procedimiento)
            ->where('id_hc', $historia_clinica->hcid)
            ->first();
        if (!is_null($doctor_seleccionado)) {
            $doctor_ap = $doctor_seleccionado->id_doctor_examinador;
        } else {
            $doctor_seleccionado = hc_procedimientos::where('id', $id_hc_procedimiento)
                ->where('id_hc', $historia_clinica->hcid)
                ->first();
            if (!is_null($doctor_seleccionado)) {
                $doctor_ap = $doctor_seleccionado->id_doctor_examinador;
            }
        }

        if (is_null($archivo_plano)) {
            $parentesco = $this->equivalente_parentesco($historia_clinica->parentesco);

            if ($parentesco === 'TITULAR') {
                $persona_afiliada = $paciente;
            } else {
                $persona_afiliada = $paciente->usuario;
            }
            $nombres = trim(implode(' ', array_filter([
                $persona_afiliada->apellido1,
                !in_array($persona_afiliada->apellido2, ['N/A', '(N/A)', '.']) ? $persona_afiliada->apellido2 : null,
                $persona_afiliada->nombre1,
                !in_array($persona_afiliada->nombre2, ['N/A', '(N/A)', '.']) ? $persona_afiliada->nombre2 : null,
            ])));

            $id_tipo_seguro = $this->carga_tipo_seguro($historia_clinica);

            $id_dependencia = $this->carga_dependencia($historia_clinica);

            $cie10 = null; $presuntivo_def = null;
            $dxs = $this->carga_diagnosticos($historia_clinica);
            if(isset($dxs[0])){ //dd($dxs);
                $cie10 = $dxs[0]['cie10'];
                $presuntivo_def = $dxs[0]['pre_def'];
            }

            $arr_ar = [
                'id_paciente'           => $historia_clinica->id_paciente,
                'id_hc'                 => $historia_clinica->hcid,
                'id_hc_procedimimentos' => $id_hc_procedimiento,
                'id_usuario'            => $historia_clinica->id_usuario,
                'id_empresa'            => $historia_clinica->agenda->id_empresa,
                'ip_creacion'           => $ip_cliente,
                'ip_modificacion'       => $ip_cliente,
                'id_usuariocrea'        => $idusuario,
                'id_usuariomod'         => $idusuario,
                'parentesco'            => $parentesco,
                'mes_plano'             => $mes.$anio ,
                'id_nivel'              => $id_nivel,
                'id_seguro'             => $id_seguro,
                'fecha_ing'             => $historia_clinica->agenda->fechaini,
                'fecha_alt'             => $historia_clinica->agenda->fechaini,
                'id_tipo_seguro'        => $id_tipo_seguro,
                'id_cod_dep'            => $id_dependencia,
                'cie10'                 => $cie10,
                'presuntivo_def'        => $presuntivo_def,
                'nombres'               => $nombres,
                'id_doctor'             => $doctor_ap,

            ];
            $id_plano = Archivo_Plano_Cabecera::insertGetId($arr_ar);
                
            LogAuditoriaPublica::guardarLog($id_plano, $historia_clinica, $id_seguro,'FUNCION NUEVA'); 

            $archivo_plano = Archivo_Plano_Cabecera::find($id_plano);
        }else{
            if (is_null($archivo_plano->id_doctor) && !is_null($doctor_ap)) {
                $archivo_plano->update(['id_doctor' => $doctor_ap]);
                LogAuditoriaPublica::guardarLog_asigna_doctor($archivo_plano->id, 'CREA - ACT CABECERA');
            }
            LogAuditoriaPublica::guardarLog_visualizar($archivo_plano->id, $historia_clinica, $id_seguro, 'FUNCION NUEVA'); 
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
        $codigos_dev = Codigo_Derivacion::where('estado', '1')->orderBy('nombre')->get();
        $codigos_dep = Codigo_Dependencia::where('estado', '1')->orderBy('nombre_cod')->get();
        $empresas = Empresa::where('estado', '1')->get();
        $seguros_publicos = Seguro::where('inactivo', '1')->where('tipo', '0')->orderBy('nombre')->get();
        $lista = ApPlantilla::orderBy('descripcion', 'ASC')->get();


        $doctores = User::where('id_tipo_usuario', 3)->where('estado', '1')->get();

        return view('archivo_plano/planilla/nueva_planilla_paciente', ['doctores' => $doctores, 'archivo_plano' => $archivo_plano, 'seguros' => $seguros, 'tipo_seguros' => $tipo_seguros, 'empresas' => $empresas, 'codigos_dev' => $codigos_dev, 'codigos_dep' => $codigos_dep, 'seguros_publicos' => $seguros_publicos, 'txt_cie10' => $txt_cie10, 'lista' => $lista, 'historia_clinica' => $historia_clinica]);
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

    public function guardarItem(Request $request){

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        //dd($request->all());

        $id_ap       = $request->id_ap;
        //$fecha       = $request->v_fecha;
        $fecha       = substr($request->v_fecha, 6, 4).'-'.substr($request->v_fecha, 3, 2).'-'.substr($request->v_fecha, 0, 2);
        //$fecha       = date('Y-m-d', strtotime($fecha));dd($fecha);
        $id_nivel    = $request->id_nivel;
        $codigo      = $request->v_codigo;
        $descripcion = $request->v_descripcion;
        $cantidad    = $request->v_cantidad;
        $valor       = $request->v_precio;
        $tipo        = $request->v_tipo;

        $val_tmp_anest          = $request->vt_val_tmp_anest;
        $tiempo_Anest           = $request->v_tiempo_Anest;
        $modificador            = 0;
        $modificador2            = 0;
        if(isset($request->v_modificador2)){
            $modificador2 = 1;
        }
        if(isset($request->v_modificador)){
            $modificador = 1;
        }
       
        $ayudantia10            = 0;
        if(isset($request->v_ayudantia10)){
            $ayudantia10 = 1;
        }
        
        $ayudantia20            = 0;
        if(isset($request->v_ayudantia20)){
            $ayudantia20 = 1;
        }
        $porcentaje_honorario   = $request->v_porcent_honorario;
        $hono_Anest             = $request->v_hono_Anest;

        $valor       = round($valor,2);
        $valor       = $valor * $porcentaje_honorario / 100;
        $valor       = round($valor,2);

        $val_unit = $valor/(1+$request['v_porcent_10']);
        $val_unit = round($val_unit,2);

        $subtotal   = $cantidad * $val_unit;
        $valor_iva  = $subtotal * $request['v_iva'];
        $valor10    = $subtotal * $request['v_porcent_10'];
        $total      = $subtotal + $valor10 + $valor_iva;
        
        $iva_unitario = $val_unit * $valor_iva;
        $p10_unitario = $val_unit * $valor10;
        $iva_unitario = $this->redondea_menos_4_dec($iva_unitario);
        $p10_unitario = $this->redondea_menos_4_dec($p10_unitario);

        if($cantidad > 0){

            $input_item = [
                'id_ap_cabecera'        => $id_ap, 
                'fecha'                 => $fecha,
                'id_nivel'              => $id_nivel,
                'tipo'                  => $tipo, 
                'codigo'                => $codigo, 
                'descripcion'           => $descripcion, 
                'cantidad'              => $cantidad, 
                'valor'                 => $valor,   
                'subtotal'              => $subtotal,  
                'porcent_10'            => $request['v_porcent_10'],                       
                'porcentaje10'          => $valor10,
                'valor_unitario'        => $val_unit,
                'porcentaje_iva'        => $request['v_iva'],
                'iva'                   => $valor_iva,
                'porcentaje_honorario'  => $porcentaje_honorario,
                'total'                 => $total,
                'id_usuariocrea'        => $idusuario,
                'id_usuariomod'         => $idusuario,
                'ip_creacion'           => $ip_cliente,
                'ip_modificacion'       => $ip_cliente,
                'iva_unitario' => $iva_unitario,
                'p10_unitario' => $p10_unitario,

            ];
            
            Archivo_Plano_Detalle::insert($input_item);

            if($hono_Anest > 0){

                $valor2      = round($hono_Anest,2);
                $valor2      = round($valor2,2);

                $val_unit2 = $valor2/(1+$request['v_porcent_10']);
                $val_unit2 = round($val_unit2,2);

                $subtotal2   = $cantidad * $val_unit2;
                $valor_iva2  = $subtotal2 * $request['v_iva'];
                $valor10_2   = $subtotal2 * $request['v_porcent_10'];
                $total2      = $subtotal2 + $valor10_2 + $valor_iva2;
                
                $input_item2 = [
                    'id_ap_cabecera'        => $id_ap, 
                    'fecha'                 => $fecha,
                    'id_nivel'              => $id_nivel,
                    'tipo'                  => 'AN', 
                    'codigo'                => $codigo, 
                    'descripcion'           => $descripcion, 
                    'cantidad'              => $cantidad, 
                    'valor'                 => $valor2,   
                    'subtotal'              => $subtotal2,  
                    'porcent_10'            => $request['v_porcent_10'],                       
                    'porcentaje10'          => $valor10_2,
                    'valor_unitario'        => $val_unit2,
                    'porcentaje_iva'        => $request['v_iva'],
                    'iva'                   => $valor_iva2,
                    'porcentaje_honorario'  => 100,
                    'total'                 => $total2,
                    'id_usuariocrea'        => $idusuario,
                    'id_usuariomod'         => $idusuario,
                    'ip_creacion'           => $ip_cliente,
                    'ip_modificacion'       => $ip_cliente,
                    'iva_unitario' => $iva_unitario,
                    'p10_unitario' => $p10_unitario,
                ];
                
                Archivo_Plano_Detalle::insert($input_item2);
            }

            if($tiempo_Anest > 0){

                $valor3      = round($val_tmp_anest,2);
                $valor3      = round($valor3,2);

                $val_unit3 = $valor3/(1+$request['v_porcent_10']);
                $val_unit3 = round($val_unit3,2);

                $subtotal3   = $tiempo_Anest * $val_unit3;
                $valor_iva3  = $subtotal3 * $request['v_iva'];
                $valor10_3   = $subtotal3 * $request['v_porcent_10'];
                $total3      = $subtotal3 + $valor10_3 + $valor_iva3;
                
                $input_item3 = [
                    'id_ap_cabecera'        => $id_ap, 
                    'fecha'                 => $fecha,
                    'id_nivel'              => $id_nivel,
                    'tipo'                  => 'TA', 
                    'codigo'                => $codigo, 
                    'descripcion'           => 'TIEMPO DE ANESTESIA', 
                    'cantidad'              => $tiempo_Anest, 
                    'valor'                 => $valor3,   
                    'subtotal'              => $subtotal3,  
                    'porcent_10'            => $request['v_porcent_10'],                       
                    'porcentaje10'          => $valor10_3,
                    'valor_unitario'        => $val_unit3,
                    'porcentaje_iva'        => $request['v_iva'],
                    'iva'                   => $valor_iva3,
                    'porcentaje_honorario'  => 100,
                    'total'                 => $total3,
                    'id_usuariocrea'        => $idusuario,
                    'id_usuariomod'         => $idusuario,
                    'ip_creacion'           => $ip_cliente,
                    'ip_modificacion'       => $ip_cliente,
                    'iva_unitario' => $iva_unitario,
                    'p10_unitario' => $p10_unitario,
                ];
                
                Archivo_Plano_Detalle::insert($input_item3);
            }

            if($modificador2 == 1 ){

                $valor4      = $valor * 0.02;
                $valor4      = round($valor4,2);

                $val_unit4 = $valor4/(1+$request['v_porcent_10']);
                $val_unit4 = round($val_unit4,2);

                $subtotal4   = $cantidad * $val_unit4;
                $valor_iva4  = $subtotal4 * $request['v_iva'];
                $valor10_4   = $subtotal4 * $request['v_porcent_10'];
                $total4      = $subtotal4 + $valor10_4 + $valor_iva4;
                
                $input_item4 = [
                    'id_ap_cabecera'        => $id_ap, 
                    'fecha'                 => $fecha,
                    'id_nivel'              => $id_nivel,
                    'tipo'                  => 'MD', 
                    'codigo'                => $codigo, 
                    'descripcion'           => 'MODIFICACOR 2%', 
                    'cantidad'              => $cantidad, 
                    'valor'                 => $valor4,   
                    'subtotal'              => $subtotal4,  
                    'porcent_10'            => $request['v_porcent_10'],                       
                    'porcentaje10'          => $valor10_4,
                    'valor_unitario'        => $val_unit4,
                    'porcentaje_iva'        => $request['v_iva'],
                    'iva'                   => $valor_iva4,
                    'porcentaje_honorario'  => 100,
                    'total'                 => $total4,
                    'id_usuariocrea'        => $idusuario,
                    'id_usuariomod'         => $idusuario,
                    'ip_creacion'           => $ip_cliente,
                    'ip_modificacion'       => $ip_cliente,
                    'iva_unitario' => $iva_unitario,
                    'p10_unitario' => $p10_unitario,
                ];
                
                Archivo_Plano_Detalle::insert($input_item4);
            }

            if($modificador == 1 ){

                $valor4      = $valor * 0.05;
                $valor4      = round($valor4,2);

                $val_unit4 = $valor4/(1+$request['v_porcent_10']);
                $val_unit4 = round($val_unit4,2);

                $subtotal4   = $cantidad * $val_unit4;
                $valor_iva4  = $subtotal4 * $request['v_iva'];
                $valor10_4   = $subtotal4 * $request['v_porcent_10'];
                $total4      = $subtotal4 + $valor10_4 + $valor_iva4;
                
                $input_item4 = [
                    'id_ap_cabecera'        => $id_ap, 
                    'fecha'                 => $fecha,
                    'id_nivel'              => $id_nivel,
                    'tipo'                  => 'MD', 
                    'codigo'                => $codigo, 
                    'descripcion'           => 'MODIFICACOR 5%', 
                    'cantidad'              => $cantidad, 
                    'valor'                 => $valor4,   
                    'subtotal'              => $subtotal4,  
                    'porcent_10'            => $request['v_porcent_10'],                       
                    'porcentaje10'          => $valor10_4,
                    'valor_unitario'        => $val_unit4,
                    'porcentaje_iva'        => $request['v_iva'],
                    'iva'                   => $valor_iva4,
                    'porcentaje_honorario'  => 100,
                    'total'                 => $total4,
                    'id_usuariocrea'        => $idusuario,
                    'id_usuariomod'         => $idusuario,
                    'ip_creacion'           => $ip_cliente,
                    'ip_modificacion'       => $ip_cliente,
                    'iva_unitario' => $iva_unitario,
                    'p10_unitario' => $p10_unitario,
                ];
                
                Archivo_Plano_Detalle::insert($input_item4);
            }
            

            if($ayudantia10 == 1 ){

                $valor5      = $valor * 0.1;
                $valor5      = round($valor5,2);

                $val_unit5 = $valor5/(1+$request['v_porcent_10']);
                $val_unit5 = round($val_unit5,2);

                $subtotal5   = $cantidad * $val_unit5;
                $valor_iva5  = $subtotal5 * $request['v_iva'];
                $valor10_5   = $subtotal5 * $request['v_porcent_10'];
                $total5      = $subtotal5 + $valor10_5 + $valor_iva5;
                
                $input_item2 = [
                    'id_ap_cabecera'        => $id_ap, 
                    'fecha'                 => $fecha,
                    'id_nivel'              => $id_nivel,
                    'tipo'                  => 'A10', 
                    'codigo'                => $codigo, 
                    'descripcion'           => 'AYUDANTIA '.$descripcion, 
                    'cantidad'              => $cantidad, 
                    'valor'                 => $valor5,   
                    'subtotal'              => $subtotal5,  
                    'porcent_10'            => $request['v_porcent_10'],                       
                    'porcentaje10'          => $valor10_5,
                    'valor_unitario'        => $val_unit5,
                    'porcentaje_iva'        => $request['v_iva'],
                    'iva'                   => $valor_iva5,
                    'porcentaje_honorario'  => 100,
                    'total'                 => $total5,
                    'id_usuariocrea'        => $idusuario,
                    'id_usuariomod'         => $idusuario,
                    'ip_creacion'           => $ip_cliente,
                    'ip_modificacion'       => $ip_cliente,
                    'iva_unitario' => $iva_unitario,
                    'p10_unitario' => $p10_unitario,
                ];
                
                Archivo_Plano_Detalle::insert($input_item2);
            }

            if($ayudantia20 == 1 ){

                $valor6      = $valor * 0.2;
                $valor6      = round($valor6,2);

                $val_unit6 = $valor6/(1+$request['v_porcent_10']);
                $val_unit6 = round($val_unit6,2);

                $subtotal6   = $cantidad * $val_unit6;
                $valor_iva6  = $subtotal6 * $request['v_iva'];
                $valor10_6   = $subtotal6 * $request['v_porcent_10'];
                $total6      = $subtotal6 + $valor10_6 + $valor_iva6;
                
                $input_item2 = [
                    'id_ap_cabecera'        => $id_ap, 
                    'fecha'                 => $fecha,
                    'id_nivel'              => $id_nivel,
                    'tipo'                  => 'A20', 
                    'codigo'                => $codigo, 
                    'descripcion'           => 'AYUDANTIA '.$descripcion, 
                    'cantidad'              => $cantidad, 
                    'valor'                 => $valor6,   
                    'subtotal'              => $subtotal6,  
                    'porcent_10'            => $request['v_porcent_10'],                       
                    'porcentaje10'          => $valor10_6,
                    'valor_unitario'        => $val_unit6,
                    'porcentaje_iva'        => $request['v_iva'],
                    'iva'                   => $valor_iva6,
                    'porcentaje_honorario'  => 100,
                    'total'                 => $total6,
                    'id_usuariocrea'        => $idusuario,
                    'id_usuariomod'         => $idusuario,
                    'ip_creacion'           => $ip_cliente,
                    'ip_modificacion'       => $ip_cliente,
                    'iva_unitario' => $iva_unitario,
                    'p10_unitario' => $p10_unitario,
                ];
                
                Archivo_Plano_Detalle::insert($input_item2);
            }
            LogAuditoriaPublica::guardarLog_agrega_item_inv($id_ap,'AGREGA ITEM INDIVIDUAL MODIFICADOR');
        }    

    }
    
    public function index_factura_mesplano(){
        $facturas = ApArchivoFactura::where('estado','1')->paginate(20);
        $empresas = Empresa::join('convenio as c', 'c.id_empresa', 'empresa.id')
        ->where('c.id_seguro', '2')
        ->select('empresa.*')
        ->get();
        return view('archivo_plano.factura_mesplano.index',['empresas' => $empresas, 'facturas' => $facturas]);
    }

    public function store_archivo(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $id_empresa = $request['id_empresa'];
        $mes_plano = $request['mes_plano'];
        $codigo = date("ymdHis") . rand(100, 999);
        date_default_timezone_set('America/Guayaquil');
        
        $archivo = ApArchivoFactura::where('mes_plano',$mes_plano)->where('id_empresa',$id_empresa)->where('estado',1)->first();
        if(is_null($archivo)){
            $nombre_original = $request['archivo']->getClientOriginalName();
            $extension       = $request['archivo']->getClientOriginalExtension();
            $nuevo_nombre = 'D_FACTURAS_' .$id_empresa.'_'. $mes_plano.'_' . $codigo . '.' . $extension;
            $ruta = base_path() . '/storage/app/facturas_auditoria/' . $nuevo_nombre;
            $r1 = Storage::disk('facturas_auditoria')->put($nuevo_nombre, \File::get($request['archivo']));
            
            $input_archivo = [
                'mes_plano'      => $mes_plano,
                'id_empresa'     => $id_empresa,
                'nombre'         => $nuevo_nombre,
                'nombre_original' => $nombre_original,
                'ruta'            => $ruta,
                'estado'          => 1,
                'id_usuariocrea'  => $idusuario,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
            ];
            ApArchivoFactura::create($input_archivo);
            return ['status' => 'success', 'msj' => 'Guardado con exito'];
        }
        else{
            return ['status' => 'error', 'msj' => 'Ya se encuentra un archivo para este mes plano y esta empresa'];
        }
        
    }

    public function mostrar_pdf(Request $request)
    {
        $archivo= ApArchivoFactura::find($request->id);
        $path1 = storage_path() . "/app/facturas_auditoria/" . $archivo->nombre;
        return response()->file($path1);
    }

    public function eliminar_archivo($id)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $archivo = ApArchivoFactura::find($id);
        $arr = [
            'estado' => 0,
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod'   => $idusuario,
        ];
        $archivo->update($arr);
    }

    public function create()
    {
        $empresas = Empresa::join('convenio as c', 'c.id_empresa', 'empresa.id')
        ->where('c.id_seguro', '2')
        ->select('empresa.*')
        ->get();
        return view('archivo_plano/factura_mesplano/subir_archivo', ['empresas' => $empresas]);
    }

    public function search(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $constraints = [
            'id'             => $request['id'],
            'mes_plano'      => $request['mes_plano'],
            'id_empresa'    => $request['id_empresa'],
        ];
        
        $facturas = $this->doSearchingQuery($constraints);
        $empresa    = Empresa::find($request['id_empresa']);
        $empresas = Empresa::join('convenio as c', 'c.id_empresa', 'empresa.id')
        ->where('c.id_seguro', '2')
        ->select('empresa.*')
        ->get();
        return view('archivo_plano.factura_mesplano.index', ['empresas' => $empresas, 'facturas' => $facturas, 'searchingVals' => $constraints, 'empresa' => $empresa]);
    }

    private function doSearchingQuery($constraints)
    {
        $query  = ApArchivoFactura::query()->select('ap_archivo_factura.*');
        $fields = array_keys($constraints);

        $index = 0;

        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where('ap_archivo_factura.' . $fields[$index], 'like', '%' . $constraint . '%');
            }
            $index++;
        }
        $query = $query->where('ap_archivo_factura.estado', 1);
        return $query->paginate(20);
    }
   
    public function formulario_013_pdf($id_hc_procedimientos){

        $formulario013 = HcFormulario013::where('id_hc_procedimiento',$id_hc_procedimientos)->first();
        if(!is_null($formulario013)){
            $paciente = Paciente::find($formulario013->id_paciente);
            $empresa = Empresa::find($formulario013->id_empresa);
            $seguro = Seguro::find($paciente->id_seguro);
    
            $seguro_nombre = '';
            if(!is_null($seguro)){
                $seguro_nombre = strtoupper($seguro->nombre);
            }
    
            $edad = null;
            $fechaActual = new DateTime();
            if(!is_null($paciente)){
                $fechaNacimiento = new DateTime($paciente->fecha_nacimiento);
                $edad = $fechaNacimiento->diff($fechaActual);
                $age = $edad->y;
            }

            $diagnosticos = Aud_Hc_Cie10::where('hc_id_procedimiento',$formulario013->id_hc_procedimiento)->limit(5)->get();
            if(count($diagnosticos) == 0){
                $diagnosticos = Hc_Cie10::where('hc_id_procedimiento',$formulario013->id_hc_procedimiento)->limit(5)->get();
            }

            foreach($diagnosticos as $value_descripcion){
                $cie10 = Cie_10_3::where('id',$value_descripcion->cie10)->first();
            }

            $biopsias = Hc4_Auditoria_Biopsias::where('id_hc_formulario_013',$formulario013->id)->where('estado',1)->limit(5)->get();
            if(count($biopsias) == 0){
                $biopsias = Hc4_Biopsias::where('hc_id_procedimiento',$formulario013->id_hc_procedimiento)->where('estado',1)->limit(5)->get();
            }

            //dd($biopsias);
            $procedimiento = hc_procedimientos::find($formulario013->id_hc_procedimiento);
            $fecha_toma = date_format(new DateTime($formulario013->fecha_toma), 'd/m/Y'); 
            $fecha = date_format(new DateTime($procedimiento->created_at), 'Y-m-d'); 
            $hora = date_format(new DateTime($procedimiento->created_at), 'H:i:s');
            $doctor = User::find($procedimiento->id_doctor_examinador);
            $firma = null;
            $ruta_firma = "";
            if(!is_null($doctor)){
                $firma = Firma_Usuario::where('id_usuario',$doctor->id)->first();
                if(!is_null($firma)){
                    $ruta_firma = base_path().'/storage/app/avatars/' . $firma->nombre;
                }
            }
            //dd($doctor,$ruta_firma,$firma);
            $view = \View::make('hc_admision.formulario013.formulario013_pdf',compact('age','diagnosticos' ,'fecha_toma', 'value_descripcion','cie10','formulario013', 'paciente', 'empresa', 'seguro_nombre', 'biopsias', 'procedimiento', 'fecha','hora','doctor','ruta_firma'))->render();
            $pdf  = \App::make('dompdf.wrapper');
            $pdf->setOptions(['chroot'  => base_path('/')]);
            $pdf->setPaper('A4');
            $pdf->loadHTML($view);
            return $pdf->stream('HCU_013.pdf');
        }
        else{
            return 'No se encontro formulario 013';
        }
    }

    
    private function redondea_menos_4_dec($numero){
        
        if( $this->contarDecimales($numero) > 4 ){
            $numero = floor($numero * 10000) / 10000;
        }

        return $numero;

    }

    private function contarDecimales($numero) {
        $partes = explode('.', strval($numero));
        if (count($partes) < 2) {
            return 0; // Si no hay punto decimal, no hay decimales
        } else {
            return strlen($partes[1]); // La longitud de la parte después del punto decimal es el número de decimales
        }
    }

    public function carga_tipo_seguro($historia_clinica){

        $id_tipo_seguro = null;
        if(!is_null($historia_clinica->id_subseguro)){
            $subseguro = Subseguro::find($historia_clinica->id_subseguro);
            if(!is_null($subseguro)){
                $tipo_seguro = Tipo_Seguro::find($subseguro->id_tipo_seguro);
                if(!is_null($tipo_seguro)){
                    $id_tipo_seguro = $tipo_seguro->id;
                }
                
            }
            return $id_tipo_seguro;
            
        }

    }

    public function carga_dependencia($historia_clinica){

        $id_dependencia = null;
        if(!is_null($historia_clinica->agenda)){
            $especialidad = $historia_clinica->agenda->especialidad;
            if(!is_null($especialidad)){
                $id_dependencia = $especialidad->id_dependencia;
            }
           
            return $id_dependencia;
            
        }

    }

    public function carga_diagnosticos($historia){

        $dxs = [];

        $diagnosticos = $historia->diagnosticos;
        foreach($diagnosticos as $d){
            array_push($dxs,['cie10' => $d->cie10, 'pre_def' => $d->presuntivo_definitivo ] );
        }

        return $dxs;

    }

    public function actualizar_detalle_x_medicina( $id_codigo ){

        $item = ApProcedimiento::where('codigo', $id_codigo)->where('tipo','M')->first();
        //dd($item);
        if(!is_null($item)){

            $detalles = Archivo_Plano_Detalle::where('codigo',$item->codigo)->where('tipo','M')->whereNull('codigo_medicina')->get();
            foreach($detalles as $detalle){

                $array2 = [
                    'codigo_medicina'=> $item->codigo_medicina,
                    'ip_creacion'    => 'MD'.date('Y-m-d'),
                ];
                $detalle->update($array2);
            }
        }

        return "listo";

    }

    public function guardar_codigo($codigo, $id){

        $codigo_nuevo = '';

        $procedimiento = ApProcedimiento::where('codigo', $codigo)->where('tipo','M')->first();  
        if(!is_null($procedimiento)){
            $detalle = Archivo_Plano_Detalle::find($id);
            $detalle->update(['codigo_medicina' => $procedimiento->codigo_medicina]); 
            
            $codigo_nuevo = $procedimiento->codigo_medicina;
        }  

        return $codigo_nuevo;

    }


    public function actualiza_4decimal( $id_cabecera ){

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;

        $planilla_cabera = Archivo_Plano_Cabecera::find( $id_cabecera );
        $detalles        = $planilla_cabera->detalles;

        foreach( $detalles as $detalle ){

            $valor = 0;
            $xtipo = '';

            $tipo     = $detalle->tipo; 
            $codigo   = $detalle->codigo;
            $id_nivel = $planilla_cabera->id_nivel; 
            $cantidad = $detalle->cantidad;
            $id_ap    = $planilla_cabera->id_ap;

            $ap_procedimiento = Ap_Procedimiento::where('tipo',$tipo)->where('codigo',$codigo)->first();//dd($ap_procedimiento);
            if(!is_null($ap_procedimiento)){
                $tipo_ex = Ap_Tipo_Examen::where('tipo',$tipo)->first();//dd($tipo_ex);
                
                if(!is_null($tipo_ex)){
                    $xtipo = $tipo_ex->tipo_ex;
                }//dd($xtipo);
                if( $xtipo != '' ){

                    if( $xtipo == 'IMM' OR $xtipo == 'FAR' ){
                        $valor   = $ap_procedimiento->valor;
                        $valor   = round($valor,4);
                    }else{
                        
                        $ap_nivel = ApProcedimientoNivel::where('id_procedimiento',$ap_procedimiento->id)->where('cod_conv',$id_nivel)->first();//dd($ap_nivel);
                        if(!is_null($ap_nivel)){
                            $valor = $ap_nivel->uvr1 * $ap_nivel->prc1;
                            $valor = round($valor,4);
                        }   

                    }

                }    
            }
            
            if($valor > 0){
                
                if( $detalle->porcentaje_honorario == '50' and $detalle->tipo == 'P' ){

                    $valor = $valor / 2;
                    $valor = round($valor,4);

                }
                $val_unit = $valor/( 1 + $detalle->porcent_10 );
                $val_unit = round($val_unit,4);

                $subtotal   = $cantidad * $val_unit;
                $valor_iva  = $subtotal * $detalle->porcentaje_iva;
                $valor_iva  = round($valor_iva,4);

                $valor10    = $subtotal * $detalle->porcent_10;
                $valor10    = round($valor10,4);
                $total      = $subtotal + $valor10 + $valor_iva;

                $input_item = [
                      
                    'valor'                 => $valor,   
                    'subtotal'              => $subtotal,                         
                    'porcentaje10'          => $valor10,
                    'valor_unitario'        => $val_unit,
                    'iva'                   => $valor_iva,
                    'total'                 => $total,
                    'ip_creacion'           => '4D-141123',
                ];
                
                $detalle->update($input_item);
            }

        }

        return "ok";

    }

    public function actualiza_4decimal_empresa($empresa){

        $seguro = '2';
        $mes_plano = '062023';

        $archivo_plano_1 = [];
        $archivo_plano_2 = [];
        $archivo_plano_3 = [];
        $archivo_plano_4 = [];
        $archivo_plano_5 = [];
        $archivo_plano_6 = [];
        $archivo_plano_7 = [];
        $archivo_plano_8 = [];
        $archivo_plano_9 = [];
        $arr_cabeceras   = [];

        $cob_compar = null;
        $archivo_plano_1 = $this->obtener_cabecera('ACTIVO',$cob_compar,$empresa,$seguro,$mes_plano);//dd($archivo_plano_1);
        //array_push($arr_cabeceras,$archivo_plano_1);//dd($arr_cabeceras);
        $arr_cabeceras[0] = $archivo_plano_1;
        $archivo_plano_2 = $this->obtener_cabecera('JUBILADO',$cob_compar,$empresa,$seguro,$mes_plano);
        //array_push($arr_cabeceras,$archivo_plano_2);
        $arr_cabeceras[1] = $archivo_plano_2;
        $archivo_plano_3 = $this->obtener_cabecera('MONTEPIO',$cob_compar,$empresa,$seguro,$mes_plano);
        //array_push($arr_cabeceras,$archivo_plano_3);
        $arr_cabeceras[2] = $archivo_plano_3;
        $cob_compar = 3;//ISSFA-ACTIVOS
        $archivo_plano_4 = $this->obtener_cabecera('ACTIVO',$cob_compar,$empresa,$seguro,$mes_plano);
        //array_push($arr_cabeceras,$archivo_plano_4);
        $arr_cabeceras[3] = $archivo_plano_4;
        $archivo_plano_5 = $this->obtener_cabecera('JUBILADO',$cob_compar,$empresa,$seguro,$mes_plano);
        //array_push($arr_cabeceras,$archivo_plano_5);
        $arr_cabeceras[4] = $archivo_plano_5;
        $archivo_plano_6 = $this->obtener_cabecera('MONTEPIO',$cob_compar,$empresa,$seguro,$mes_plano);
        //array_push($arr_cabeceras,$archivo_plano_6);
        $arr_cabeceras[5] = $archivo_plano_6;
        $cob_compar = 6;//ISSPOL-ACTIVOS
        $archivo_plano_7 = $this->obtener_cabecera('ACTIVO',$cob_compar,$empresa,$seguro,$mes_plano);
        //array_push($arr_cabeceras,$archivo_plano_7);
        $arr_cabeceras[6] = $archivo_plano_7;
        $archivo_plano_8 = $this->obtener_cabecera('JUBILADO',$cob_compar,$empresa,$seguro,$mes_plano);
        //array_push($arr_cabeceras,$archivo_plano_8);
        $arr_cabeceras[7] = $archivo_plano_8;
        $archivo_plano_9 = $this->obtener_cabecera('MONTEPIO',$cob_compar,$empresa,$seguro,$mes_plano);
        //array_push($arr_cabeceras,$archivo_plano_9);
        $arr_cabeceras[8] = $archivo_plano_9;

        for($xv = 0; $xv < 9 ; $xv++){
            //dd($xkey);
            foreach($arr_cabeceras[$xv] as $value){
            
                $this->actualiza_4decimal($value->id_plano);  
                //dd($value->id_plano);  

            }
        }        
        return "ok";

    }
    public function actualiza_ames_plano(){

        try {

            //1307189140001	ROBLES MEDRANDA CARLOS ANTONIO
            //0992704152001	INSTITUTO ECUATORIANO DE ENFERMEDADES DIGESTIVAS GASTROCLINICA S.A

            //1391927177001	GASTROMANTAEC SA
            //2390058384001	GASTROSANTO SA
            $empresa   = '2390058384001';
            $seguro    = '2';
            $mes_plano = '062023';

            $cob_compar = null;
            $archivo_plano_1 = $this->obtener_cabecera('ACTIVO',$cob_compar,$empresa,$seguro,$mes_plano);//dd($archivo_plano_1);
                //array_push($arr_cabeceras,$archivo_plano_1);//dd($arr_cabeceras);
            $arr_cabeceras[0] = $archivo_plano_1;
            $archivo_plano_2 = $this->obtener_cabecera('JUBILADO',$cob_compar,$empresa,$seguro,$mes_plano);
            //array_push($arr_cabeceras,$archivo_plano_2);
            $arr_cabeceras[1] = $archivo_plano_2;
            $archivo_plano_3 = $this->obtener_cabecera('MONTEPIO',$cob_compar,$empresa,$seguro,$mes_plano);
            //array_push($arr_cabeceras,$archivo_plano_3);
            $arr_cabeceras[2] = $archivo_plano_3;
            $cob_compar = 3;//ISSFA-ACTIVOS
            $archivo_plano_4 = $this->obtener_cabecera('ACTIVO',$cob_compar,$empresa,$seguro,$mes_plano);
            //array_push($arr_cabeceras,$archivo_plano_4);
            $arr_cabeceras[3] = $archivo_plano_4;
            $archivo_plano_5 = $this->obtener_cabecera('JUBILADO',$cob_compar,$empresa,$seguro,$mes_plano);
            //array_push($arr_cabeceras,$archivo_plano_5);
            $arr_cabeceras[4] = $archivo_plano_5;
            $archivo_plano_6 = $this->obtener_cabecera('MONTEPIO',$cob_compar,$empresa,$seguro,$mes_plano);
            //array_push($arr_cabeceras,$archivo_plano_6);
            $arr_cabeceras[5] = $archivo_plano_6;
            $cob_compar = 6;//ISSPOL-ACTIVOS
            $archivo_plano_7 = $this->obtener_cabecera('ACTIVO',$cob_compar,$empresa,$seguro,$mes_plano);
            //array_push($arr_cabeceras,$archivo_plano_7);
            $arr_cabeceras[6] = $archivo_plano_7;
            $archivo_plano_8 = $this->obtener_cabecera('JUBILADO',$cob_compar,$empresa,$seguro,$mes_plano);
            //array_push($arr_cabeceras,$archivo_plano_8);
            $arr_cabeceras[7] = $archivo_plano_8;
            $archivo_plano_9 = $this->obtener_cabecera('MONTEPIO',$cob_compar,$empresa,$seguro,$mes_plano);
            //array_push($arr_cabeceras,$archivo_plano_9);
            $arr_cabeceras[8] = $archivo_plano_9;

            for($xv = 0; $xv < 9 ; $xv++){
                //dd($xkey);
                foreach($arr_cabeceras[$xv] as $value){
                
                    $this->corrige_medicinas_12_05($value->id_plano);
                
                }
            }

            
            DB::commit();
            return 'Registros procesados con exito';
        } catch (\Exception $e) {
            DB::rollBack();
            return ['error' => $e->getMessage(), 'linea' => $e->getLine()];
        }

    }    

    public function corrige_medicinas_12_05($id_plano){

            $cabecera = Archivo_Plano_Cabecera::find($id_plano);
            $detalles = $cabecera->detalles;
            //ip_modificacion = 'medicina_precio' LAS MEDICINAS DE WALTER
            $procedimientos = ApProcedimiento::where('ip_modificacion','medicina_precio')->get();
            foreach($procedimientos as $procedimiento){

                
                $detalle = $detalles->where('estado',1)->where('tipo','M')->where('codigo',$procedimiento->codigo)->first();
                if(!is_null($detalle)){

                    $cantidad   = $detalle->cantidad; 
                    $valor      = $procedimiento->valor;
                    //$val_unit = $valor/( 1 + $detalle->porcent_10 );
                    //$val_unit = round($val_unit,4);
                    $val_unit   = $valor;
                    //$val_unit = round($val_unit,4);
                    $subtotal   = $cantidad * $val_unit;
                    //$valor_iva  = $subtotal * $detalle->porcentaje_iva;
                    $valor_iva  = 0;
                    //$valor_iva  = round($valor_iva,4);
                    //$valor10    = $subtotal * $detalle->porcent_10;
                    $valor10    = 0;
                    //$valor10    = round($valor10,4);
                    $total      = $subtotal + $valor10 + $valor_iva;
    
                    $input_item = [
                          
                        'valor'                 => $valor,   
                        'subtotal'              => $subtotal,                         
                        'porcentaje10'          => $valor10,
                        'valor_unitario'        => $val_unit,
                        'iva'                   => $valor_iva,
                        'total'                 => $total,
                        'total_solicitado_usd'  => $total,
                        'ip_creacion'           => 'AC_MD_1205',
                    
                    ];
                    
                    $detalle->update($input_item);
                    
                }

            }
            //AC_MD_1205
            $procedimientos = ApProcedimiento::where('ip_creacion','AC_MD_1205')->get();
            foreach($procedimientos as $procedimiento){

                
                $detalle = $detalles->where('estado',1)->where('tipo','M')->where('codigo',$procedimiento->codigo)->first();
                if(!is_null($detalle)){

                    $cantidad   = $detalle->cantidad; 
                    $valor      = $procedimiento->valor;
                    //$val_unit = $valor/( 1 + $detalle->porcent_10 );
                    //$val_unit = round($val_unit,4);
                    $val_unit   = $valor;
                    //$val_unit = round($val_unit,4);
                    $subtotal   = $cantidad * $val_unit;
                    //$valor_iva  = $subtotal * $detalle->porcentaje_iva;
                    $valor_iva  = 0;
                    //$valor_iva  = round($valor_iva,4);
                    //$valor10    = $subtotal * $detalle->porcent_10;
                    $valor10    = 0;
                    //$valor10    = round($valor10,4);
                    $total      = $subtotal + $valor10 + $valor_iva;
    
                    $input_item = [
                          
                        'valor'                 => $valor,   
                        'subtotal'              => $subtotal,                         
                        'porcentaje10'          => $valor10,
                        'valor_unitario'        => $val_unit,
                        'iva'                   => $valor_iva,
                        'total'                 => $total,
                        'total_solicitado_usd'  => $total,
                        'ip_creacion'           => 'AC_MD_1205',
                    
                    ];
                    
                    $detalle->update($input_item);
                    
                }

            }


    }

    public function obtener_cabecera_total($empresa,$seguro,$mes_plano)
    {
        $tipo_seg = ['ACTIVO', 'JUBILADO', 'MONTEPIO'];

        $archivo_plano = Archivo_Plano_Cabecera::join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->where('archivo_plano_cabecera.id_seguro', $seguro)
            //->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            ->where('archivo_plano_cabecera.estado', '1')
            ->whereIn('tseg.tipo_principal', $tipo_seg)
            ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name') , 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'archivo_plano_cabecera.fecha_alt', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres','archivo_plano_cabecera.id as id_plano', 'archivo_plano_cabecera.id_hc_procedimimentos', 'archivo_plano_cabecera.id_cod_deriva', 'archivo_plano_cabecera.atencion','archivo_plano_cabecera.codigo_iess','archivo_plano_cabecera.fecha_autorizacion')
            ->orderby('full_name')
            ->orderby('archivo_plano_cabecera.fecha_ing')
            ->orderby('archivo_plano_cabecera.id')
            ->get();

        

        return $archivo_plano;

    }

    public function numera_carpetas_v2023_total($mes_plano, $seguro, $tipo_seg, $empresa)
    {

        $cabeceras = $this->obtener_cabecera_total($empresa,$seguro,$mes_plano);
        $x = 0;
        foreach($cabeceras as $cabecera){
            $x++;
            $cabecera = Archivo_Plano_Cabecera::find($cabecera->id_plano);
            $cabecera->update(['atencion' => $x]);
        }
        
        
        
        return "ok";
    }  

    //VERSION 2023 de Archivo Plano Total
    public function genera_ap_excel_v2023_Total(Request $request)
    {
        //ini_set('memory_limit', '1024M');
        //dd("sistemas");
        $mes_plano = $request['mes_plano'];
        $seguro     = $request->seguro;
        $tipo_seg   = $request->id_tipo_seguro;
        //$cob_compar = $request['id_cobertura_comp'];
        $empresa    = $request->id_empresa;

        $this->numera_carpetas_v2023_total($mes_plano, $seguro, $tipo_seg, $empresa);


        $texto_apellido_1 = null;
        $texto_apellido_2 = null;
        $texto_final      = null;
        $texto_fin        = null;
        $texto_nombre_1   = null;
        $texto_nombre_2   = null;
        $cob_compar = null;

        $caract_remplazar_1 = array("¨", "$", "%", "&", "/", "(", ")", "#", "@", "|", "!", "/", "+", "-", "}", "{", ">", "< ", ";", ",", ":",
            ".", "*", ">", "<", "[", "]", "?", "¿", "º", "~","°");

        $caract_remplazar_2 = array("Ñ", "ñ");

        //ACTIVOS

        Excel::create('Archivo Plano General ', function ($excel) use ($tipo_seg, $cob_compar, $empresa, $seguro, $mes_plano,$caract_remplazar_1, $caract_remplazar_2) {
            $excel->sheet(date('Y-m-d'), function ($sheet) use ($tipo_seg, $cob_compar, $empresa, $seguro, $mes_plano, $caract_remplazar_1, $caract_remplazar_2) {

                $i = 1; $x = 0; $id_temporal = 0;

                $cabeceras = $this->obtener_cabecera_total($empresa,$seguro,$mes_plano);

                $arr_hd = [ '76700', '91034', '91037', '395162', '395272', '91010', '91110', '91122' ];
                
              
                foreach($cabeceras as $value){
                   
                    $x++;
                    $detalles = $this->obtener_detalles_plano($value->id_plano);
                    
                    //dd($detalles);
                    $acum_hd = '';
                    foreach($detalles as $detalle){
                        $xv_detalle = Archivo_Plano_Cabecera::find($value->id_plano);

                        //if($detalle->codigo == '43259'){
                        //    dd($detalle);
                        //}
                        //$ap_procedimiento = Ap_Procedimiento::where('tipo', $detalle->tipo)->where('codigo', $detalle->codigo)->first();
                        $tipo_examen = '';
                        $ap_tipo_examen = Ap_Tipo_Examen::where('tipo',$detalle->tipo)->first();
                        if(!is_null($ap_tipo_examen)){
                            $tipo_examen = $ap_tipo_examen->tipo_ex;
                        }

                        $sheet->cell('A' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('515');
                        });

                        $sheet->cell('B' . $i, function ($cell) use ($value, $x) {
                            // manipulate the cel
                            //$cell->setValue($x);
                            $cell->setValue($value->atencion);
                        });

                        //$fecha_ing     = substr($value->fecha_ing, 0, 10);
                        $fecha_ing     = substr($detalle->fecha, 0, 10);
                        $fecha_ing_inv = date("d/m/Y", strtotime($fecha_ing));

                        $sheet->cell('C' . $i, function ($cell) use ($fecha_ing_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_ing_inv);
                        });

                        $sheet->cell('D' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->tiposeguro->tipo);
                        });

                        $sheet->cell('E' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_paciente);

                        });
                        $texto_apellido_1 = '';$texto_apellido_2 = '';$texto_final = '';$texto_fin = '';$texto_nombre_1 = '';$texto_nombre_2 = '';
                        $sheet->cell('F' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2, $texto_apellido_1, $texto_apellido_2, $texto_final, $texto_fin, $texto_nombre_1, $texto_nombre_2) {
                            if ($value->apellido1 != 'N/A' && $value->apellido1 != '(N/A)') {
                                $texto_apellido_1 = $value->apellido1;
                            }

                            if ($value->apellido2 != 'N/A' && $value->apellido2 != '(N/A)') {
                                $texto_apellido_2 = $value->apellido2;
                            }

                            if ($value->nombre1 != 'N/A' && $value->nombre1 != '(N/A)') {
                                $texto_nombre_1 = $value->nombre1;
                            }

                            if ($value->nombre2 != 'N/A' && $value->nombre2 != '(N/A)') {
                                $texto_nombre_2 = $value->nombre2;
                            }

                            $texto_final = $texto_apellido_1 . ' ' . $texto_apellido_2 . ' ' . $texto_nombre_1 . ' ' . $texto_nombre_2;

                            if($value->apellido2 == '(N/A)' || $value->apellido2 == ('N/A') || $value->apellido2 == "."){
                                $texto_final = $texto_apellido_1 . ' ' . $texto_nombre_1 . ' ' . $texto_nombre_2;
                            }

                            $texto_fin = str_replace($caract_remplazar_1, "", $texto_final);
                            $texto_fin = str_replace($caract_remplazar_2, "N", $texto_fin);
                            $texto_fin = $this->quitarTildesMayusculas($texto_fin);
                            $cell->setValue($texto_fin);

                            /*$cell->setValue($value->apellido1.' '.$value->apellido2.' '.$value->nombre1.' '.$value->nombre2);*/

                        });

                        $sheet->cell('G' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->paciente->sexo == 1) {
                                $cell->setValue('M');
                            } elseif ($value->paciente->sexo == 2) {
                                $cell->setValue('F');
                            }

                        });

                        $fecha_nac     = substr($value->paciente->fecha_nacimiento, 0, 10);
                        $fecha_nac_inv = date("d/m/Y", strtotime($fecha_nac));

                        $sheet->cell('H' . $i, function ($cell) use ($fecha_nac_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_nac_inv);

                        });
                        if ($value->paciente->fecha_nacimiento == null) {
                            $edad = 0;
                        } else {
                            $edad = Carbon::createFromDate(substr($value->paciente->fecha_nacimiento, 0, 4), substr($value->paciente->fecha_nacimiento, 5, 2), substr($value->paciente->fecha_nacimiento, 8, 2))->age;
                        }
                        $sheet->cell('I' . $i, function ($cell) use ($value, $edad) {
                            // manipulate the cel
                            $cell->setValue($edad);

                        });

                        

                        $tipo_medico = '1';$derivacion = 'CVAV';$sec_der = ''; $tipo_ex = '';$fecha_derivacion = ''; 
                        $hc_id_procedimientos = $value->id_hc_procedimimentos;
                        $hc_procedimiento = hc_procedimientos::find($hc_id_procedimientos);
                        if(!is_null($hc_procedimiento)){
                            $historia = $hc_procedimiento->historia;
                            $agenda   = $historia->agenda;   
                            if($agenda->proc_consul == 0){
                                $tipo_medico = '8';
                                $derivacion = 'CVCEC';
                                //$sec_der = '999999999';
                                $sec_der = $value->codigo_iess;
                                $tipo_ex = 'C/EXT';
                                if($detalle->tipo == 'IM'){
                                    $tipo_ex = 'IMAGEN';
                                }
                                if($detalle->tipo == 'EX'){
                                    $tipo_ex = 'LAB';
                                    }
                                //$fecha_derivacion = $fecha_ing_inv;
                                $fecha_derivacion = $value->fecha_autorizacion;
                                //$xv_detalle->update(['fecha_autorizacion' => $fecha_ing_inv]); 
                            }else{
                                //$sec_der = $agenda->cod_val;
                                $sec_der = $value->codigo_iess;
                                $fecha_derivacion = $value->fecha_autorizacion;
                                /*$arr_derivacion = explode('-',$sec_der);
                                if(count($arr_derivacion)>0){
                                    if(isset($arr_derivacion[1])  and isset($arr_derivacion[2]) and isset($arr_derivacion[3])){
                                        $fecha_derivacion = $arr_derivacion[1] .'-'. $arr_derivacion[2] .'-'. $arr_derivacion[3];
                                        $xv_detalle->update(['fecha_autorizacion' => $fecha_derivacion]);
                                    } 
                                }*/
                                $tipo_ex = 'PAQUE/AMBU';
                                $tipo_medico = '1';
                                if($detalle->tipo == 'IM'){
                                    $tipo_ex = 'IMAGEN';
                                }
                                if($detalle->tipo == 'EX'){
                                    //$tipo_ex = 'LAB';
                                }
                                if( $detalle->tipo == 'AN' or $detalle->tipo == 'TA' ){ 
                                    $tipo_medico = '6';
                                }
                                /*if(!is_null($agenda->fecha_val)){

                                    $fecha_derivacion = substr($agenda->fecha_val, 0, 10);
                                    $fecha_derivacion = date("d/m/Y", strtotime($fecha_derivacion));
                                
                                }*/
                                

                            }
                        }



                        if( in_array( $detalle->codigo, $arr_hd ) ){
                            
                            if($detalle->tipo == 'P'){
                                    
                                $tipo_ex = 'HD'; 
                                $acum_hd = 'HD';       
                                
                            }
                            
                        }
                        if( $detalle->codigo == '99204' and $acum_hd == '' ){
                            if( !is_null($detalle->cabecera->fx_hc_procedimiento->hc_procedimiento_f->where('id_procedimiento',1)->first()) ){
                                $tipo_ex = 'HD';    
                            }
                            if( !is_null($detalle->cabecera->fx_hc_procedimiento->hc_procedimiento_f->where('id_procedimiento',2)->first()) ){
                                $tipo_ex = 'HD';    
                            }
                            if( !is_null($detalle->cabecera->fx_hc_procedimiento->hc_procedimiento_f->where('id_procedimiento',3)->first()) ){
                                $tipo_ex = 'HD';    
                            }
                            if( !is_null($detalle->cabecera->fx_hc_procedimiento->hc_procedimiento_f->where('id_procedimiento',20)->first()) ){
                                $tipo_ex = 'HD';    
                            }
                        }
                        if($acum_hd == 'HD'){
                            if( $detalle->tipo == 'I' or $detalle->tipo == 'IV' or $detalle->tipo == 'IVC' or $detalle->tipo == 'P' or $detalle->tipo == 'S' or $tipo_examen == 'IMM'){
                                $tipo_ex = 'HD';
                            }
                        }    
                        

                        $sheet->cell('J' . $i, function ($cell) use ($tipo_ex) {
                            // manipulate the cel
                            $cell->setValue($tipo_ex);

                        });

                        $sheet->cell('K' . $i, function ($cell) use ($detalle, $tipo_examen) {
                            // manipulate the cel
                            if ($detalle->tipo == 'IV' || $detalle->tipo == 'I' || $detalle->tipo == 'IVC' || $tipo_examen == 'IMM' ) {
                                $cell->setValue('');
                            }elseif($detalle->tipo == 'M'){
                                if($detalle->codigo_medicina == null){
                                    
                                    $codigo_new = $this->guardar_codigo( $detalle->codigo , $detalle->id);
                                    $cell->setValue($codigo_new);
                                
                                }else{
                                    
                                    $cell->setValue($detalle->codigo_medicina);
                                
                                }
                                //$hoja->setCellValueExplicit('A1', $numeroLargo, PHPExcel_Cell_DataType::TYPE_NUMERIC);
                                
                            }elseif($detalle->tipo == 'EX'){
                                if($detalle->codigo_medicina != null){
                                    $cell->setValue($detalle->codigo_medicina);    
                                }else{
                                    $cell->setValue($detalle->codigo);
                                }
                                //$hoja->setCellValueExplicit('A1', $numeroLargo, PHPExcel_Cell_DataType::TYPE_NUMERIC);
                                
                            }else {
                                $cell->setValue($detalle->codigo);
                            }

                        });

                        //$sheet->setCellValueExplicit('K'.$i, $detalle->codigo_medicina, PHPExcel_Cell_DataType::TYPE_NUMERIC);
                        $sheet->getStyle('K'.$i)->getNumberFormat()->setFormatCode('0');

                        $sheet->cell('L' . $i, function ($cell) use ($detalle, $caract_remplazar_1, $caract_remplazar_2, $tipo_examen) {
                            // manipulate the

                            $descrip = str_replace($caract_remplazar_1, "", $detalle->descripcion);
                            $descrip = str_replace($caract_remplazar_2, "N", $descrip);
                            $descrip = $this->quitarTildesMayusculas($descrip);

                            if ($detalle->tipo == 'IV' || $detalle->tipo == 'I' || $detalle->tipo == 'IVC' || $tipo_examen == 'IMM') {
                            
                                $cell->setValue($descrip);

                            } else {

                                $cell->setValue('');

                            }

                            if($detalle->tipo == 'M'){
                                if($detalle->codigo_medicina == null){
                                    $cell->setValue($descrip);
                                }
                                //$hoja->setCellValueExplicit('A1', $numeroLargo, PHPExcel_Cell_DataType::TYPE_NUMERIC);
                            }
                        });

                        $sheet->cell('M' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->cie10);

                        });

                        $sheet->cell('N' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });

                        $sheet->cell('O' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });

                        $sheet->cell('P' . $i, function ($cell) use ($detalle) {
                            // manipulate the cel
                            $cell->setValue($detalle->cantidad);

                        });

                        $sheet->cell('Q' . $i, function ($cell) use ($detalle) {
                            // manipulate the cel
                            //$cell->setValue(round($detalle->valor, 2));
                            if($detalle->tipo == 'EX' or $detalle->tipo == 'S'){
                                $cell->setValue(round($detalle->valor, 4));
                            }else{    
                                $cell->setValue(round($detalle->valor_unitario, 4));
                            }

                        });

                        $sheet->cell('R' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('0');

                        });

                        $sheet->cell('S' . $i, function ($cell) use ($value) {
                            // manipulate the cel

                            if ($value->parentesco == 'TITULAR') {
                                $cell->setValue('T');

                            } elseif ($value->parentesco == 'CONYUGE') {
                                $cell->setValue('C');

                            } elseif ($value->parentesco == 'HIJO/HIJA') {
                                $cell->setValue('H');

                            } elseif ($value->parentesco == 'PARIENTE') {
                                $cell->setValue('X');

                            }

                        });

                        $sheet->cell('T' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_usuario);
                        });

                        $sheet->cell('U' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2) {
                            // manipulate the cel

                            $descrip_nombre = str_replace($caract_remplazar_1, "", $value->nombres);
                            $descrip_nombre = str_replace($caract_remplazar_2, "N", $descrip_nombre);
                            $descrip_nombre = $this->quitarTildesMayusculas($descrip_nombre);

                            $cell->setValue($descrip_nombre);

                        });

                        $sheet->cell('V' . $i, function ($cell) use ( $value ) {
                            // manipulate the cel
                            $derivacion = Codigo_Derivacion::find($value->id_cod_deriva);
                            if(!is_null($derivacion)){
                                $cell->setValue( $derivacion->codigo );
                            }else{
                                $cell->setValue( '' );
                            }
                            
                            

                        });

                        $sheet->cell('W' . $i, function ($cell) use ($value, $sec_der) {
                            // manipulate the cel
                            $cell->setValue($sec_der);

                        });

                        $sheet->cell('X' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('1');

                        });
                    
                        $sheet->cell('Y' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                            $cell->setValue('D');

                        });

                        $sheet->cell('Z' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('0');

                        });

                        $sheet->cell('AA' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });

                        $sheet->cell('AB' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });

                        $sheet->cell('AC' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });

                        $sheet->cell('AD' . $i, function ($cell) use ($detalle) {
                            // manipulate the cel
                            if($detalle->porcentaje_iva == 0.12){
                                $cell->setValue(12);    
                            }else{
                                $cell->setValue(0); 
                            }
                            

                        });

                        $nuevo_iva = $detalle->valor_unitario * $detalle->porcentaje_iva;
                        $sheet->cell('AE' . $i, function ($cell) use ($detalle, $nuevo_iva) {
                            // manipulate the cel
                            
                            $cell->setValue(round($nuevo_iva, 4));

                        });
                        //***CAMBIOS 2023*/////////
                        $sheet->cell('AF' . $i, function ($cell) use ($detalle,$nuevo_iva) {
                            // manipulate the cel
                            /////////////////////////////////
                            if($detalle->tipo == 'EX' or $detalle->tipo == 'S'){
                                $vtotal = $detalle->cantidad * $detalle->valor;
                                $cell->setValue(round($vtotal, 4));
                            }else{    
                                $vtotal = $detalle->cantidad * $detalle->valor + $nuevo_iva * $detalle->cantidad;
                                $cell->setValue(round($vtotal, 4));
                            }

                        });
                        $sheet->cell('AG' . $i, function ($cell) use ($detalle) {
                            // manipulate the cel
                            $valor_ag = $detalle->valor - $detalle->valor_unitario;
                            if($detalle->tipo == 'EX' or $detalle->tipo == 'S'){
                                $cell->setValue(0);
                            }else{    
                                $cell->setValue(round($valor_ag, 4));
                            }

                        });
                        $fecha_ing     = substr($value->fecha_ing, 0, 10);
                        $fecha_ing_inv = date("d/m/Y", strtotime($fecha_ing));

                        $sheet->cell('AH' . $i, function ($cell) use ($fecha_ing_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_ing_inv);
                        });
                        $fecha_alt     = substr($value->fecha_alt, 0, 10);
                        $fecha_alt_inv = date("d/m/Y", strtotime($fecha_alt));

                        $sheet->cell('AI' . $i, function ($cell) use ($fecha_alt_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_alt_inv);
                        });

                        $egreso = 1;
                        if($value->id_tipo_egreso != null) {
                            $egreso = $value->id_tipo_egreso;
                        }
                        $sheet->cell('AJ' . $i, function ($cell) use ($egreso) {
                            // manipulate the cel
                            $cell->setValue($egreso);
                        });
                        $cobertura = '';
                        if($value->cobertura == '3' or $value->cobertura == '6')
                        {
                            $cobertura = 'SI';
                        }else{
                            $cobertura = 'NO';
                        }
                        $sheet->cell('AK' . $i, function ($cell) use ($cobertura) {
                            // manipulate the cel
                            $cell->setValue($cobertura);
                        });
                        $tipo_cob = '';
                        if($value->cobertura == '3'){
                            $tipo_cob = 'ISSFA';
                        }
                        if($value->cobertura == '6'){
                            $tipo_cob = 'ISSPOL';
                        }

                        $sheet->cell('AL' . $i, function ($cell) use ($tipo_cob) {
                            // manipulate the cel
                            $cell->setValue($tipo_cob);
                        });
                        ///// ESTE DATO DEBE SALIR DEL PACIENTE ----- PILAS VT ------- ////
                        $discapacidad_cer = 'NO';       
                        $sheet->cell('AM' . $i, function ($cell) use ($discapacidad_cer) {
                            // manipulate the cel
                            $cell->setValue($discapacidad_cer);
                        });
                        $tipo_pres = 'P';
                        if($detalle->tipo == 'I' or $detalle->tipo == 'IV' or $detalle->tipo == 'IVC' or $tipo_examen == 'IMM'){
                            $tipo_pres = 'I';
                        }
                        if($detalle->tipo == 'M'){
                            $tipo_pres = 'M';
                        }

                        $sheet->cell('AN' . $i, function ($cell) use ($tipo_pres) {
                            // manipulate the cel
                            $cell->setValue($tipo_pres);
                        });

                        
                        $sheet->cell('AO' . $i, function ($cell) use ($tipo_medico) {
                            // manipulate the cel
                            $cell->setValue($tipo_medico);

                        });

                        
                        $sheet->cell('AQ' . $i, function ($cell) use ($fecha_derivacion) {
                            // manipulate the cel
                            $cell->setValue($fecha_derivacion);

                        });
                        //**     */
                        $sheet->cell('AR' . $i, function ($cell) use ($value) {
                                // manipulate the cel
                                $cell->setValue('F');

                        });
                        $i++; 
                    }     
                    
                }

                


            });

            $excel->getActiveSheet()->getColumnDimension("A")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("B")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("C")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("D")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("E")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("F")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("G")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("H")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("I")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("J")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("K")->setWidth(30)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("L")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("M")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("N")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("O")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("P")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("Q")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("R")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("S")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("T")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("U")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("V")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("W")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("X")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("Y")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("Z")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AA")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AB")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AC")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AD")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AE")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AF")->setWidth(11)->setAutosize(false);
            

        })->export('xlsx');
    }

    public function bitacora_pdf($id)
    {
        $this->recalcular_valor_ap_cab($id);

        $archivo_plano = Archivo_Plano_Cabecera::find($id);

        $empresa = Empresa::find($archivo_plano->id_empresa);

        $procedimiento = hc_procedimientos::find($archivo_plano->id_hc_procedimimentos);

        $convenio = Convenio::where('id_seguro',$archivo_plano->id_seguro)->where('id_empresa',$archivo_plano->id_empresa)->where('estado','1')->first();
       //dd($procedimiento->historia->agenda->paciente);

        $view = \View::make('archivo_plano.reportes.bitacora', compact('archivo_plano','procedimiento','empresa','convenio'))->render();
        $pdf  = \App::make('dompdf.wrapper');
        // $pdf->setPaper('A4');
        $pdf->setPaper('A4', 'landscape');
        $pdf->loadHTML($view);
        return $pdf->stream('bitacora.pdf');
    }

    public function recalcular_valor_ap_cab($id){

        $archivo_plano = Archivo_Plano_Cabecera::find($id);
        
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

        $medicinas = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')->where('apt.tipo_ex', 'FAR')->where('apd.estado', '1')->get();

        $insumos = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total', 'apd.valor_unitario', 'apd.porcentaje_iva')->where('apt.tipo_ex', 'IMM')->where('apd.estado', '1')->get();

        $laboratorio = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')->where('apt.tipo_ex', 'LAB')->where('apd.estado', '1')->get();

        $servicios_ins = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')->where('apt.tipo_ex', 'HOSP/QUIR')->where('apd.estado', '1')->get();

        $imagen = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')->where('apt.tipo_ex', 'IMA')->where('apd.estado', '1')->get();

        $equipos = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')->where('apt.tipo_ex', 'PRO/ESP')->where('apd.estado', '1')->get();
        
        $liquidacion_total=0;
        $total_hm = 0;
        foreach ($honor_medicos as $value1){
            $total_hm += $value1->total;
        }
        $tota_med = 0;
        foreach ($medicinas as $value2){
            $tota_med += $value2->total;
        }
        $totalins = 0; 
        foreach ($insumos as $value3){
            $total_item = round($value3->subtotal, 4) + round($value3->porcentaje10, 4) + ($value3->subtotal * $value3->porcentaje_iva);
            $totalins += $total_item;        
        }
        $total_lab = 0; 
        foreach ($laboratorio as $value4){
            $total_lab += $value4->total;
        }
        $total_imagen = 0; 
        foreach ($imagen as $value5){
            $total_imagen += $value5->total; 
        }       
        $total_serv = 0; 
        foreach ($servicios_ins as $value6){
            $total_serv += $value6->total; 
        }
        $total_equi = 0;
        foreach ($equipos as $value7){
            $total_equi += $value7->total; 
        }              

        $liquidacion_total = $total_equi + $total_serv + $total_imagen + $total_lab + $totalins + $tota_med + $total_hm;	
        //dd($liquidacion_total);
        if(!is_null($archivo_plano)){
            $archivo_plano->update([
                'valor'  => $liquidacion_total,
                'total_hm' => $total_hm,
                'total_ins' => $totalins,
                'total_lab' => $total_lab,
                'total_imagen' => $total_imagen,
                'total_serv' => $total_serv,
                'total_equi' => $total_equi,
                'leido'      => 1,
            ]);
        }
        
        return "elt";
    }
    public function index_log_auditoria($cabecera){

        $log_auditoria = LogAuditoriaPublica::where('id_cabecera',$cabecera)->orderBy('id', 'desc')->get();
       
        return view('archivo_plano.reportes.index_log_auditoria',['log_auditoria'=>$log_auditoria]);

    }
    public function busqueda_valores_por_empresa($request){

        $detalles = [];//dd($request->all());

        if($request->mes_plano != null AND $request->id_empresa != null AND $request->seguro != null ){
            //dd($request->all());

            $detalles = Archivo_Plano_Detalle::join('archivo_plano_cabecera as c','c.id','archivo_plano_detalle.id_ap_cabecera')
                ->join('tipo_seguro as ts','ts.id','c.id_tipo_seguro')
                ->join('ap_tipo_examen as te','te.tipo','archivo_plano_detalle.tipo')
                ->join('seguros as s','s.id','c.id_seguro')
                ->where('c.estado',1)
                ->where('archivo_plano_detalle.estado',1)
                ->whereIn('ts.tipo_principal', ['ACTIVO', 'JUBILADO', 'MONTEPIO'])
                ->where('te.tipo_ex','IMM')
                ->groupby('archivo_plano_detalle.tipo','archivo_plano_detalle.codigo','archivo_plano_detalle.descripcion','archivo_plano_detalle.valor','archivo_plano_detalle.valor_unitario')
                ->select('archivo_plano_detalle.tipo','archivo_plano_detalle.codigo','archivo_plano_detalle.descripcion','archivo_plano_detalle.valor','archivo_plano_detalle.valor_unitario','archivo_plano_detalle.ip_creacion','s.nombre');
                if($request->mes_plano != null){
                    $detalles = $detalles->where('c.mes_plano', $request->mes_plano);
                }  
                if($request->id_empresa != null){
                    $detalles = $detalles->where('c.id_empresa', $request->id_empresa);
                } 
                if($request->seguro != null){
                    $detalles = $detalles->where('c.id_seguro', $request->seguro);
                } 
                $detalles = $detalles->get();

        }

        //$detalles = $detalles->get();    
        //dd($detalles);
        return $detalles; 
    }
    public function reporte_valores_por_empresa(Request $request){
        $mes_plano = $request['mes_plano'];
        $id_empresa = $request['id_empresa'];
        $empresa      = $request->id_empresa;
        $seguro          = $request['seguro'];
        $empresas   = Empresa::where('estado', '1')->where('prioridad',1)->get();
        $seguros_publicos = Seguro::where('inactivo', '1')->where('tipo', '0')->orderBy('nombre')->get();
        //dd($request->all());

        $detalles = $this->busqueda_valores_por_empresa($request);

        return view('archivo_plano/reportes/index_reporte_valores', ['empresa' => $empresa,'mes_plano' => $mes_plano, 'id_empresa'=> $id_empresa,'empresas'=> $empresas, 'detalles' => $detalles,'seguros_publicos' => $seguros_publicos,'seguro' => $seguro]);

    }
    
    public function agrega_insumos_fungibles( $id, $tipo ){

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $marca = 'GRC_INS_ESP_2503';
        $cabecera = Archivo_Plano_Cabecera::find($id);
        $redo = '4';

        if($cabecera->cierre == '1'){
            return "err";
        }

        $fecha = date("Y-m-d", strtotime($cabecera->fecha_ing));
        

        $plantillas = ApPlantillaEspecial::where('tipo',$tipo)->where('id_empresa',$cabecera->id_empresa)->get();

        foreach ($plantillas as $plantilla){

            $procedimiento = Ap_Procedimiento::find($plantilla->id_ap_procedimiento);
            if(!is_null($procedimiento)){
                $p_iva = $procedimiento->iva;
            $p10 = $procedimiento->porcentaje10;
            
            $cantidad = $plantilla->cantidad;
            $valor = $plantilla->valor;
            $valor = round($valor,4);
            $valor_unitario = $valor / (1 + $p10); //280
            $valor_unitario = round($valor_unitario,4);
            $subtotal = $cantidad*$valor_unitario;//208
            $subtotal = round($subtotal,4);

            if($fecha >= '2024-04-01'){

                $p_iva = $procedimiento->iva_nuevo;    
            }

            //$porcentaje10 = $subtotal * 0.1;
            //$porcentaje10 = round($porcentaje10,4);
            //$iva = $subtotal * 0.12; //33,60
            //$iva = round($iva,4);

            $iva_unitario = $valor_unitario * $p_iva;
            $p10_unitario = $valor_unitario * $p10;

            if($cabecera->id_seguro == '5'){
                $iva_unitario = round($iva_unitario,4);
                $p10_unitario = round($p10_unitario,4);
            }else{

                $iva_unitario = $this->redondea_menos_4_dec($iva_unitario);
                $p10_unitario = $this->redondea_menos_4_dec($p10_unitario);
            }

            $porcentaje10 = $p10_unitario * $cantidad;
            $iva = $iva_unitario * $cantidad;

            $total = $subtotal + $porcentaje10 + $iva; //262,40
            $total = round($total,4);

            
            $porcent_clasif = null;$val_clasif = 0;$clasificador = null;
            if($cabecera->id_seguro == '5'){
                $redo = '2';
                //$val_unit = round($val_unit,2);
                //$iva_unitario = round($iva_unitario,2);
                //$p10_unitario = round($p10_unitario,2);
                $total = round($valor_unitario+ $iva_unitario + $p10_unitario,2);
                $total = $total * $cantidad;
                $porcent_clasif = Ap_Tipo_Examen::where('tipo',$procedimiento->tipo)->where('estado', '1')->first();
                if(!is_null($porcent_clasif)){
                    $val_clasif = $subtotal*$porcent_clasif->porcentaje_modif_msp;
                    $clasificador =  $porcent_clasif->clasificado;

                }
            }
            //$total_sol = $total;
            $total_sol = $total + $val_clasif;

            $array_detalle = [
                'valor' => $valor,
                'subtotal' => $subtotal,
                'valor_unitario'=> $valor_unitario,
                'porcentaje10' => $porcentaje10,
                'iva' => $iva,
                'total' => $total,
                'total_solicitado_usd' => $total,
                'ip_creacion' => $ip_cliente,
                'id_ap_cabecera' => $id,
                'fecha' => $cabecera->fecha_ing,
                'id_nivel' => $cabecera->id_nivel,
                'tipo' => $procedimiento->tipo,
                'codigo' => $procedimiento->codigo,
                'descripcion' => $procedimiento->descripcion . (
                        $cabecera->id_seguro == 5 || stripos($procedimiento->descripcion, 'DESCARTABLE') !== false
                            ? ''
                            : ' DESCARTABLE'
                ),
                'cantidad' => $cantidad,
                'porcent_10' => $p10,
                'porcentaje_iva' => $p_iva,
                'porcentaje_honorario' => '100',
                'estado' => '1',
                'clasif_porcentaje_msp' => '100',
                'total_solicitado_usd'  => round(($total),$redo),
                'valor_porcent_clasifi'  => round(($val_clasif),$redo),
                'clasificador' => $clasificador,
                'id_usuariocrea' =>  $idusuario,
                'id_usuariomod' => $idusuario,
                'ip_modificacion' => $ip_cliente,
                'iva_unitario' => $iva_unitario,
                'p10_unitario' => $p10_unitario,

            ];

            Archivo_Plano_Detalle::create($array_detalle);

            }
            
            

        }

        LogAuditoriaPublica::guardarLog_agrega_plantilla($id, 'AGREGA INSUMOS FUNGIBLES', 'I.FUNGIBLES DE: '.$tipo);

        return "ok";
        
    }
    public function importar_valores_plano( $empresa_dir ){

        /*
        1307189140001	ROBLES MEDRANDA CARLOS ANTONIO
        0992704152001	INSTITUTO ECUATORIANO DE ENFERMEDADES DIGESTIVAS GASTROCLINICA S.A
        */
        
        Excel::filter('chunk')->load('insumos_pub.xlsx')->chunk(600, function ($reader) use ($empresa_dir){
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $idusuario  = Auth::user()->id;
            $id_empresa = '';
            if( $empresa_dir == 'GRC062023' ){ 
                $id_empresa = '0992704152001';
            }
            if( $empresa_dir == 'CRM062023' ){ 
                $id_empresa = '1307189140001';
            }    
            
            foreach ($reader as $book) {
                $tipo = $book->tipo;
                $codigo = $book->codigo;
                $precio = $book->precio; 
                $precio = round($precio,4);   
                //dd($book);
                $detalles = Archivo_Plano_Detalle::where('tipo',$tipo)
                    ->where('codigo',$codigo)
                    ->where('fecha','>=','2023-06-01 00:00:00')
                    ->where('fecha','<','2023-07-01 00:00:00')
                    ->where('ip_creacion','<>','GRC062023')
                    ->where('ip_creacion','<>','CRM062023')
                    ->get();
                
                foreach($detalles as $detalle){

                    $cabecera = $detalle->cabecera;
                    if($cabecera->id_seguro == 2 and $cabecera->id_empresa == $id_empresa){ //IESS - GASTROCLINICA
                        if($cabecera->id_tipo_seguro != 7 and $cabecera->id_tipo_seguro != 9){ //SEGURO GENERAL

                            if($detalle->iva > 0){

                                $valor = $precio; //308
                                $valor = round($valor,4);
                                $valor_unitario = $valor *100/110; //280
                                $valor_unitario = round($valor_unitario,4);
                                $subtotal = $detalle->cantidad*$valor_unitario;//208
                                $subtotal = round($subtotal,4);
                                $porcentaje10 = $subtotal * 0.1;//20.8
                                $porcentaje10 = round($porcentaje10,2);
                                $iva = $subtotal * 0.12; //33,60
                                $total = $subtotal + $porcentaje10 + $iva; //262,40
                                $total = round($total,4);
                                $detalle->update([
                                    'valor' => $valor,
                                    'subtotal' => $subtotal,
                                    'valor_unitario'=> $valor_unitario,
                                    'porcentaje10' => $porcentaje10,
                                    'iva' => $iva,
                                    'total' => $total,
                                    'total_solicitado_usd' => $total,
                                    'ip_creacion' => $empresa_dir,
                                ]);
                            }else{
                                $valor = $precio;
                                $valor = round($valor,4);
                                $valor_unitario = $valor;
                            
                                $subtotal = $detalle->cantidad * $valor;
                                
                                $total = $subtotal;
                            
                                $detalle->update([ 
                                    'valor' => $valor,
                                    'subtotal' => $subtotal,
                                    'valor_unitario'=> $valor_unitario,
                                    'total' => $total,
                                    'total_solicitado_usd' => $total,
                                    'ip_creacion' => $empresa_dir,
                                ]);  

                            }
                        }    
                            
                    }

                }
            }

        });


    }
    function quitarTildesMayusculas($texto) {
        // Arrays de búsqueda y reemplazo
        $buscar = array('Á', 'É', 'Í', 'Ó', 'Ú', 'Ü', 'á', 'é', 'í', 'ó', 'ú', 'ü');
        $reemplazar = array('A', 'E', 'I', 'O', 'U', 'U', 'a', 'e', 'i','o','u','u');
    
        // Reemplazar caracteres
        $textoSinTildes = str_replace($buscar, $reemplazar, $texto);
    
        return $textoSinTildes;
    }

    public function iva_10porc_nuevo_reporte_consolidado_general(Request $request)
    {

        //dd("hi");
        $idUsuario = Auth::user()->id;

        //ini_set('memory_limit', '10024M');
        $mes_plano = $request['mes_plano'];
        $seg       = $request['seguro'];
        $seguro       = $request['seguro'];
        $tipo_seg  = $request['id_tipo_seguro'];
        $empresa   = $request['id_empresa'];

        $sub_cadena_1 = '';
        $sub_cadena_2 = '';

        $cantidad_1_activo          = 0;
        $cantidad_2_activo_conyugue = 0;
        $cantidad_0_1               = 0;
        $cantidad_2_6               = 0;
        $cantidad_7_17              = 0;
        $cantidad_jubilado          = 0;
        $cantidad_montepio          = 0;

        $archivo_plano_activo         = [];
        $archivo_plan_activo_conyugue = [];
        $archivo_plano_0_1            = [];
        $archivo_plano_2_6            = [];
        $archivo_plano_7_17           = [];
        $archivo_plano_jubilado       = [];
        $archivo_plano_montepio       = [];
        $archivo_plano_isspol_activo  = [];
        $archivo_plano_isspol_jubilado  = [];
        $archivo_plano_isspol_montepio  = [];
        $archivo_plano_issfa_activo  = [];
        $archivo_plano_isspol_jubilado  = [];
        $archivo_plano_isspol_montepio  = [];
        $archivo_plano_issfa_jubilado = [];
        $archivo_plano_issfa_montepio = [];
        $texto_nombre = null;

        $caract_remplazar_1 = array(
            "¨",
            "$",
            "%",
            "&",
            "/",
            "(",
            ")",
            "#",
            "@",
            "|",
            "!",
            "/",
            "+",
            "-",
            "}",
            "{",
            ">",
            "< ",
            ";",
            ",",
            ":",
            ".",
            "*",
            ">",
            "<",
            "[",
            "]",
            "?",
            "¿",
            "º",
            "~"
        );

        $caract_remplazar_2 = array("Ñ", "ñ");

        if (!is_null($mes_plano)) {
            $sub_cadena_1 = substr($mes_plano, 0, 2);
        }

        if (!is_null($mes_plano)) {
            $sub_cadena_2 = substr($mes_plano, 2, 6);
        }

        $inf_empresa = Empresa::where('id', $empresa)->where('estado', '1')->first();

        //REPORTE CONSOLIDADO IESS GENERAL
        //ACTIVOS
        $cob_compar = null;
        $arr_plano = [];
        $archivo_plano_1 = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->where('archivo_plano_cabecera.id_seguro', $seguro)
            ->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            ->where('archivo_plano_cabecera.estado', '1')
            ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd', 'apd.id_ap_cabecera', 'archivo_plano_cabecera.id')
            ->join('ap_tipo_examen as tipo_ex', 'tipo_ex.tipo', 'apd.tipo')
            ->where('apd.estado', '1')
            ->where('tseg.tipo_principal', 'ACTIVO')
            ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.valor_unitario', 'apd.subtotal', 'archivo_plano_cabecera.id as id_cab', 'archivo_plano_cabecera.numero_paciente', 'archivo_plano_cabecera.id_cobertura_comp')
            ->orderby('full_name')
            ->orderby('archivo_plano_cabecera.fecha_ing')
            ->orderby('tipo_ex.orden_plano')
            ->get();

        array_push($arr_plano, $archivo_plano_1);

        $cob_compar = null; //JUBILADO
        $archivo_plano_2 = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->where('archivo_plano_cabecera.id_seguro', $seguro)
            ->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            ->where('archivo_plano_cabecera.estado', '1')
            ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd', 'apd.id_ap_cabecera', 'archivo_plano_cabecera.id')
            ->join('ap_tipo_examen as tipo_ex', 'tipo_ex.tipo', 'apd.tipo')
            ->where('apd.estado', '1')
            ->where('tseg.tipo_principal', 'JUBILADO')
            ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.valor_unitario', 'apd.subtotal', 'archivo_plano_cabecera.id as id_cab', 'archivo_plano_cabecera.numero_paciente', 'archivo_plano_cabecera.id_cobertura_comp')
            ->orderby('archivo_plano_cabecera.id_tipo_seguro')
            ->orderby('full_name')
            ->orderby('archivo_plano_cabecera.fecha_ing')
            ->orderby('tipo_ex.orden_plano')
            ->get();

        array_push($arr_plano, $archivo_plano_2);

        $cob_compar = null; //MONTEPIO
        $archivo_plano_2a = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->where('archivo_plano_cabecera.id_seguro', $seguro)
            ->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            ->where('archivo_plano_cabecera.estado', '1')
            ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd', 'apd.id_ap_cabecera', 'archivo_plano_cabecera.id')
            ->join('ap_tipo_examen as tipo_ex', 'tipo_ex.tipo', 'apd.tipo')
            ->where('apd.estado', '1')
            ->where('tseg.tipo_principal', 'MONTEPIO')
            ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.valor_unitario', 'apd.subtotal', 'archivo_plano_cabecera.id as id_cab', 'archivo_plano_cabecera.numero_paciente', 'archivo_plano_cabecera.id_cobertura_comp')
            ->orderby('archivo_plano_cabecera.id_tipo_seguro')
            ->orderby('full_name')
            ->orderby('archivo_plano_cabecera.fecha_ing')
            ->orderby('tipo_ex.orden_plano')
            ->get();

        array_push($arr_plano, $archivo_plano_2a);

        $cob_compar = 3; //ISSFA-ACTIVOS
        $archivo_plano_3 = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->where('archivo_plano_cabecera.id_seguro', $seguro)
            ->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            ->where('archivo_plano_cabecera.estado', '1')
            ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd', 'apd.id_ap_cabecera', 'archivo_plano_cabecera.id')
            ->join('ap_tipo_examen as tipo_ex', 'tipo_ex.tipo', 'apd.tipo')
            ->where('apd.estado', '1')
            ->where('tseg.tipo_principal', 'ACTIVO')
            ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.valor_unitario', 'apd.subtotal', 'archivo_plano_cabecera.id as id_cab', 'archivo_plano_cabecera.numero_paciente', 'archivo_plano_cabecera.id_cobertura_comp')
            ->orderby('full_name')
            ->orderby('archivo_plano_cabecera.fecha_ing')
            ->orderby('tipo_ex.orden_plano')
            ->get();

        array_push($arr_plano, $archivo_plano_3);

        $cob_compar = 3; //ISSFA-JUBILADO
        $archivo_plano_4 = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->where('archivo_plano_cabecera.id_seguro', $seguro)
            ->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            ->where('archivo_plano_cabecera.estado', '1')
            ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd', 'apd.id_ap_cabecera', 'archivo_plano_cabecera.id')
            ->join('ap_tipo_examen as tipo_ex', 'tipo_ex.tipo', 'apd.tipo')
            ->where('apd.estado', '1')
            ->where('tseg.tipo_principal', 'JUBILADO')
            ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.valor_unitario', 'apd.subtotal', 'archivo_plano_cabecera.id as id_cab', 'archivo_plano_cabecera.numero_paciente', 'archivo_plano_cabecera.id_cobertura_comp')
            ->orderby('archivo_plano_cabecera.id_tipo_seguro')
            ->orderby('full_name')
            ->orderby('archivo_plano_cabecera.fecha_ing')
            ->orderby('tipo_ex.orden_plano')
            ->get();

        array_push($arr_plano, $archivo_plano_4);

        $cob_compar = 3; //ISSFA-MONTEPIO
        $archivo_plano_4a = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->where('archivo_plano_cabecera.id_seguro', $seguro)
            ->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            ->where('archivo_plano_cabecera.estado', '1')
            ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd', 'apd.id_ap_cabecera', 'archivo_plano_cabecera.id')
            ->join('ap_tipo_examen as tipo_ex', 'tipo_ex.tipo', 'apd.tipo')
            ->where('apd.estado', '1')
            ->where('tseg.tipo_principal', 'MONTEPIO')
            ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.valor_unitario', 'apd.subtotal', 'archivo_plano_cabecera.id as id_cab', 'archivo_plano_cabecera.numero_paciente', 'archivo_plano_cabecera.id_cobertura_comp')
            ->orderby('archivo_plano_cabecera.id_tipo_seguro')
            ->orderby('full_name')
            ->orderby('archivo_plano_cabecera.fecha_ing')
            ->orderby('tipo_ex.orden_plano')
            ->get();

        array_push($arr_plano, $archivo_plano_4a);

        $cob_compar = 6; //ISSPOL-ACTIVOS
        $archivo_plano_5 = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->where('archivo_plano_cabecera.id_seguro', $seguro)
            ->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            ->where('archivo_plano_cabecera.estado', '1')
            ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd', 'apd.id_ap_cabecera', 'archivo_plano_cabecera.id')
            ->join('ap_tipo_examen as tipo_ex', 'tipo_ex.tipo', 'apd.tipo')
            ->where('apd.estado', '1')
            ->where('tseg.tipo_principal', 'ACTIVO')
            ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.valor_unitario', 'apd.subtotal', 'archivo_plano_cabecera.id as id_cab', 'archivo_plano_cabecera.numero_paciente', 'archivo_plano_cabecera.id_cobertura_comp')
            ->orderby('full_name')
            ->orderby('archivo_plano_cabecera.fecha_ing')
            ->orderby('tipo_ex.orden_plano')
            ->get();

        array_push($arr_plano, $archivo_plano_5);

        $cob_compar = 6; //ISSPOL-JUBILADO
        $archivo_plano_6 = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->where('archivo_plano_cabecera.id_seguro', $seguro)
            ->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            ->where('archivo_plano_cabecera.estado', '1')
            ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd', 'apd.id_ap_cabecera', 'archivo_plano_cabecera.id')
            ->join('ap_tipo_examen as tipo_ex', 'tipo_ex.tipo', 'apd.tipo')
            ->where('apd.estado', '1')
            ->where('tseg.tipo_principal', 'JUBILADO')
            ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.valor_unitario', 'apd.subtotal', 'archivo_plano_cabecera.id as id_cab', 'archivo_plano_cabecera.numero_paciente', 'archivo_plano_cabecera.id_cobertura_comp')
            ->orderby('archivo_plano_cabecera.id_tipo_seguro')
            ->orderby('full_name')
            ->orderby('archivo_plano_cabecera.fecha_ing')
            ->orderby('tipo_ex.orden_plano')
            ->get();

        array_push($arr_plano, $archivo_plano_6);

        $cob_compar = 6; //ISSPOL-MONTEPIO
        $archivo_plano_6a = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->where('archivo_plano_cabecera.id_seguro', $seguro)
            ->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            ->where('archivo_plano_cabecera.estado', '1')
            ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd', 'apd.id_ap_cabecera', 'archivo_plano_cabecera.id')
            ->join('ap_tipo_examen as tipo_ex', 'tipo_ex.tipo', 'apd.tipo')
            ->where('apd.estado', '1')
            ->where('tseg.tipo_principal', 'MONTEPIO')
            ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.valor_unitario', 'apd.subtotal', 'archivo_plano_cabecera.id as id_cab', 'archivo_plano_cabecera.numero_paciente', 'archivo_plano_cabecera.id_cobertura_comp')
            ->orderby('archivo_plano_cabecera.id_tipo_seguro')
            ->orderby('full_name')
            ->orderby('archivo_plano_cabecera.fecha_ing')
            ->orderby('tipo_ex.orden_plano')
            ->get();

        array_push($arr_plano, $archivo_plano_6a);

        Excel::create('ReporteConsolidadoIESS', function ($excel) use ($inf_empresa, $sub_cadena_1, $sub_cadena_2, $cantidad_1_activo, $cantidad_2_activo_conyugue, $cantidad_0_1, $cantidad_2_6, $cantidad_7_17, $cantidad_jubilado, $cantidad_montepio, $archivo_plano_activo, $archivo_plano_jubilado, $archivo_plano_montepio, $archivo_plano_isspol_activo, $archivo_plano_isspol_jubilado, $archivo_plano_isspol_montepio, $archivo_plano_issfa_activo, $archivo_plano_issfa_jubilado, $archivo_plano_issfa_montepio, $caract_remplazar_1, $caract_remplazar_2, $texto_nombre, $idUsuario, $arr_plano) {
            $excel->sheet('ReporteConsolidadoIESS', function ($sheet) use ($inf_empresa, $sub_cadena_1, $sub_cadena_2, $cantidad_1_activo, $cantidad_2_activo_conyugue, $cantidad_0_1, $cantidad_2_6, $cantidad_7_17, $cantidad_jubilado, $cantidad_montepio, $archivo_plano_activo, $archivo_plano_jubilado, $archivo_plano_montepio, $archivo_plano_isspol_activo, $archivo_plano_isspol_jubilado, $archivo_plano_isspol_montepio, $archivo_plano_issfa_activo, $archivo_plano_issfa_jubilado, $archivo_plano_issfa_montepio, $caract_remplazar_1, $caract_remplazar_2, $texto_nombre, $idUsuario, $arr_plano) {

                $sheet->setColumnFormat(array(
                    'I' => '$### ### ### ##0.00',
                    'K' => '$### ### ### ##0.00',
                    'M' => '$### ### ### ##0.00',
                    'N' => '$### ### ### ##0.00',
                    'O' => '$### ### ### ##0.00',
                ));

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
                    $cell->setValue('PLANILLA CONSOLIDADO DE PRESTACION DE SERVICIO');
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
                $sheet->mergeCells('J11:P11');
                $sheet->cell('J11', function ($cell) use ($sub_cadena_2) {
                    // manipulate the cel
                    $cell->setValue('Año:' . ($sub_cadena_2));
                    $cell->setAlignment('left');
                    $cell->setFontWeight('bold');
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

                $sheet->mergeCells('B12:G12');
                $sheet->cell('B12', function ($cell) use ($inf_empresa) {
                    $cell->setValue('NOMBRE DE PACIENTE/USUARIO');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('H12', function ($cell) use ($inf_empresa) {
                    $cell->setValue('TIPO DE SEGURO');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('I12:J12');
                $sheet->cell('I12', function ($cell) use ($inf_empresa) {
                    $cell->setValue('BASE0');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('K12:L12');
                $sheet->cell('K12', function ($cell) use ($inf_empresa) {
                    $cell->setValue('BASE12');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('M12', function ($cell) use ($inf_empresa) {
                    $cell->setValue('VALOR IVA 12%');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('N12', function ($cell) use ($inf_empresa) {
                    $cell->setValue('GASTO ADM');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('O12:P12');
                $sheet->cell('O12', function ($cell) use ($inf_empresa) {
                    $cell->setValue('SUBTOTAL:');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $i           = 13;
                $base        = 0;
                $id_temporal = 0;
                $x           = 1;
                $sub_total   = 0;
                $iva         = 0;
                $total       = 0;

                $total_activo     = 0;
                $iva_activo       = 0;
                $sub_total_activo = 0;

                $total_conyugue     = 0;
                $iva_conyugue       = 0;
                $sub_total_conyugue = 0;

                $total_0_1     = 0;
                $iva_0_1       = 0;
                $sub_total_0_1 = 0;

                $total_2_6     = 0;
                $iva_2_6       = 0;
                $sub_total_2_6 = 0;

                $total_7     = 0;
                $iva_7       = 0;
                $sub_total_7 = 0;

                $total_jub     = 0;
                $iva_jub       = 0;
                $sub_total_jub = 0;


                $total_montepio     = 0;
                $iva_montepio       = 0;
                $sub_total_montepio = 0;

                $total_general     = 0;
                $sub_total_general = 0;
                $iva_general       = 0;

                $acum_bas_0 = 0;
                $acum_bas_iva = 0;
                $acum_v_iva = 0;
                $acum_gasto_amd = 0;
                $value      = null;

                //MUESTRA ACTIVO  SG
                $base_0          = 0;
                $sum             = 0;
                $sum_bas_0       = 0;
                $sum_bas_iva     = 0;
                $sum_v_iva       = 0;
                $sum_gasto_amd   = 0;
                $total_m_iva     = 0;
                $nombre_anterior = '';
                $tipo_seguro_ant = '';
                $nro_anterior = '';
                $id_temporal = 0;
                $cob_ant = '';
                $new_iva = 0;
                $new_v_iva = 0;

                foreach ($arr_plano as $archivo_plano) {

                    foreach ($archivo_plano as $value) {


                        if ($value->id_paciente != $id_temporal) {


                            if ($id_temporal != 0) {

                                //Escribo en excel
                                //No
                                $sheet->cell('A' . $i, function ($cell) use ($value, $nro_anterior) {
                                    // manipulate the cel

                                    $cell->setValue($nro_anterior);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $sheet->mergeCells('B' . $i . ':G' . $i);
                                $sheet->cell('B' . $i, function ($cell) use ($value, $nombre_anterior, $idUsuario) {

                                    $cell->setValue($nombre_anterior);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                //TIPO DE SEGURO
                                $sheet->cell('H' . $i, function ($cell) use ($value, $tipo_seguro_ant) {
                                    // manipulate the cel

                                    $cell->setValue($tipo_seguro_ant);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                //BASE0
                                $sheet->mergeCells('I' . $i . ':J' . $i);
                                $sheet->cell('I' . $i, function ($cell) use ($sum_bas_0) {
                                    // manipulate the cel
                                    $cell->setValue($sum_bas_0);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                //BASE12
                                $sheet->mergeCells('K' . $i . ':L' . $i);
                                $sheet->cell('K' . $i, function ($cell) use ($sum_bas_iva) {
                                    // manipulate the cel
                                    $cell->setValue($sum_bas_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                //VALOR IVA 12%
                                $sheet->cell('M' . $i, function ($cell) use ($sum_v_iva) {
                                    // manipulate the cel
                                    $cell->setValue($sum_v_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                //GASTO ADM
                                $sheet->cell('N' . $i, function ($cell) use ($sum_gasto_amd) {
                                    // manipulate the cel
                                    $cell->setValue($sum_gasto_amd);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                                //SUBTOTAL
                                $sheet->mergeCells('O' . $i . ':P' . $i);
                                $sheet->cell('O' . $i, function ($cell) use ($total_m_iva) {
                                    // manipulate the cel
                                    $cell->setValue($total_m_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                });

                                $total_activo += $total_m_iva;
                                $iva_activo += $sum_v_iva;
                                $sub_total_activo = $total_activo - $iva_activo;

                                //Fin de Escritura del Excel
                                $acum_bas_0 += $sum_bas_0;
                                $acum_bas_iva += $sum_bas_iva;
                                $acum_gasto_amd += $sum_gasto_amd;
                                $acum_v_iva += $sum_v_iva;



                                $base_0          = 0;
                                $sum             = 0;
                                $sum_bas_0       = 0;
                                $sum_bas_iva     = 0;
                                $sum_v_iva       = 0;
                                $sum_gasto_amd   = 0;
                                $total_m_iva     = 0;
                                $new_iva = 0;
                                $nombre_anterior = '';
                                $tipo_seguro_ant = '';
                                $cob_ant = '';
                                $new_v_iva = 0;
                                $x++;
                                $i++;
                            }

                            $id_temporal = $value->id_paciente;
                        }



                        $tipo_examen = '';
                        $ap_tipo_examen = Ap_Tipo_Examen::where('tipo', $value->tipo)->first();
                        if (!is_null($ap_tipo_examen)) {
                            $tipo_examen = $ap_tipo_examen->tipo_ex;
                        }
                        //VALOR DE Q
                        $cantidad = $value->cantidad;
                        $valor = round($value->valor, 2);

                        if ($tipo_examen == 'IMM') {
                            $nuevo_iva = $value->subtotal * $value->porcentaje_iva;
                            $nuevo_iva = $nuevo_iva / $value->cantidad;
                            //$new_subtotal = $value->valor * $value->cantidad;
                            //$nuevo_iva = $new_subtotal * $value->porcentaje_iva;
                            //$nuevo_iva = $nuevo_iva / $value->cantidad;
                        } else {
                            $nuevo_iva = $value->valor_unitario * $value->porcentaje_iva;
                            //$nuevo_iva = $value->valor * $value->porcentaje_iva;
                        }

                        $nuevo_iva = round($nuevo_iva, 4);

                        //Si porcentaje_iva == 0  BASE_0
                        if ($value->porcentaje_iva == 0) {
                            $sum_bas_0 += $cantidad * $valor;
                           
                        }
                        //Si porcentaje_iva == 0.12  BASE_IVA
                        $new_iva = 0;
                        if ($value->porcentaje_iva > 0) {
                            $sum_bas_iva += $cantidad * $value->valor_unitario;
                            //$sum_bas_iva += $cantidad * $value->valor;
                            /*$new_v_iva = $cantidad *  $value->valor;
                            $new_iva = $new_v_iva * $value->porcentaje_iva;
                            $new_iva = $new_iva / $value->cantidad;*/
                            $new_iva = $valor * $value->porcentaje_iva;
                            $new_iva = $new_iva * $value->cantidad;
                            $new_iva =  round($new_iva, 4);
                            

                            $sum_gasto_amd += $cantidad * ($valor - $value->valor_unitario);
                            //$sum_gasto_amd += ($cantidad * $valor) * 0.1;
                        }

                        //$sum_v_iva += $cantidad * $nuevo_iva;
                        
                        $sum_v_iva +=  $new_iva;

                        //  GAST_ADM_10

                        //TOTAL_M_IVA
                        $total_m_iva = $sum_bas_0 + $sum_bas_iva + $sum_v_iva + $sum_gasto_amd;

                        //NOMBRE DE PACIENTE/USUARIO
                        $nombre_anterior = '';
                        if ($value->paciente->apellido1 != 'N/A' && $value->paciente->apellido1 != '(N/A)') {
                            $nombre_anterior = $value->paciente->apellido1;
                        }
                        if ($value->paciente->apellido2 != 'N/A' && $value->paciente->apellido2 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->apellido2;
                        }
                        if ($value->paciente->nombre1 != 'N/A' && $value->paciente->nombre1 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->nombre1;
                        }

                        if ($value->paciente->nombre2 != 'N/A' && $value->paciente->nombre2 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->nombre2;
                        }
                        $nro_anterior = $value->numero_paciente;

                        $nombre_anterior = str_replace($caract_remplazar_1, "", $nombre_anterior);
                        $nombre_anterior = str_replace($caract_remplazar_2, "N", $nombre_anterior);


                        $tipo_seguro_ant = $value->tiposeg;
                        if ($value->id_cobertura_comp == '3') { //ISSFA
                            $cob_ant = '-ISSFA';
                        }
                        if ($value->id_cobertura_comp == '6') { //ISSFA
                            $cob_ant = '-ISSPOL';
                        }
                        $tipo_seguro_ant = $tipo_seguro_ant . $cob_ant;
                    }
                }

                //Escribo en excel
                //No
                $sheet->cell('A' . $i, function ($cell) use ($x) {
                    // manipulate the cel
                    $cell->setValue($x);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                //NOMBRE DE PACIENTE/USUARIO
                $sheet->mergeCells('B' . $i . ':G' . $i);
                $sheet->cell('B' . $i, function ($cell) use ($nombre_anterior) {
                    // manipulate the cel
                    $cell->setValue($nombre_anterior);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                //TIPO DE SEGURO
                $sheet->cell('H' . $i, function ($cell) use ($tipo_seguro_ant) {
                    // manipulate the cel

                    $cell->setValue($tipo_seguro_ant);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                //BASE0
                $sheet->mergeCells('I' . $i . ':J' . $i);
                $sheet->cell('I' . $i, function ($cell) use ($sum_bas_0) {
                    // manipulate the cel
                    $cell->setValue($sum_bas_0);
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                //BASE12
                $sheet->mergeCells('K' . $i . ':L' . $i);
                $sheet->cell('K' . $i, function ($cell) use ($sum_bas_iva) {
                    // manipulate the cel
                    $cell->setValue($sum_bas_iva);
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                //VALOR IVA 12%
                $sheet->cell('M' . $i, function ($cell) use ($sum_v_iva) {
                    // manipulate the cel
                    $cell->setValue($sum_v_iva);
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                //GASTO ADM
                $sheet->cell('N' . $i, function ($cell) use ($sum_gasto_amd) {
                    // manipulate the cel
                    $cell->setValue($sum_gasto_amd);
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                //SUBTOTAL
                $sheet->mergeCells('O' . $i . ':P' . $i);
                $sheet->cell('O' . $i, function ($cell) use ($total_m_iva) {
                    // manipulate the cel
                    $cell->setValue($total_m_iva);
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                //Fin de Escritura del Excel
                $acum_bas_0 += $sum_bas_0;
                $acum_bas_iva += $sum_bas_iva;
                $acum_gasto_amd += $sum_gasto_amd;
                $acum_v_iva += $sum_v_iva;

                //Fin de Escritura del Excel
                $base_0          = 0;
                $sum             = 0;
                $sum_bas_0       = 0;
                $sum_bas_iva     = 0;
                $sum_v_iva       = 0;
                $sum_gasto_amd   = 0;
                $total_m_iva     = 0;
                $nombre_anterior = '';
                $tipo_seguro_ant = '';
                $x++;
                $i++;





                $sheet->mergeCells('A' . $i . ':N' . $i);
                $sheet->cell('A' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    $cell->setValue('SUBTOTAL:');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                }); //dd($acum_bas_0 , $acum_bas_iva , $acum_gasto_amd);
                $subtotal_final = $acum_bas_0 + $acum_bas_iva + $acum_gasto_amd;
                $sheet->mergeCells('O' . $i . ':P' . $i);
                $sheet->cell('O' . $i, function ($cell) use ($value, $subtotal_final) {
                    // manipulate the cel
                    $cell->setValue($subtotal_final);
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $i++;

                $sheet->mergeCells('A' . $i . ':N' . $i);
                $sheet->cell('A' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    $cell->setValue('IVA:');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('O' . $i . ':P' . $i);
                $sheet->cell('O' . $i, function ($cell) use ($value, $acum_v_iva) {
                    // manipulate the cel
                    $cell->setValue($acum_v_iva);
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $i++;

                $sheet->mergeCells('A' . $i . ':N' . $i);
                $sheet->cell('A' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    $cell->setValue('TOTAL');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $total_final = $subtotal_final + $acum_v_iva;
                $sheet->mergeCells('O' . $i . ':P' . $i);
                $sheet->cell('O' . $i, function ($cell) use ($value, $total_final) {
                    // manipulate the cel
                    $cell->setValue($total_final);
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $i = $i + 7;
                $sheet->mergeCells('G' . $i . ':J' . $i);
                $sheet->cell('G' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('t', '', 'thin', '');
                });
                $i++;
                $sheet->mergeCells('G' . $i . ':J' . $i);
                $sheet->cell('G' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    $cell->setValue('FIRMA / SELLO PRESTADOR');
                    $cell->setAlignment('center');
                    $cell->setBorder('t', '', '', '');
                });
            });
        })->export('xlsx');
    }
    


}
