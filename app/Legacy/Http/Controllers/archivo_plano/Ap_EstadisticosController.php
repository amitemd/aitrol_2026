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
use Sis_medico\agenda;
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
use Sis_medico\Ap_Tipo_Seg;
use Sis_medico\Ap_Agrupado;
use Sis_medico\ApProcedimientoNivel;
use Excel;
use PHPExcel_Style_NumberFormat;
use Carbon\Carbon;
use Cookie;
use PHPExcel_Worksheet_Drawing;

class Ap_EstadisticosController extends Controller
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

    private function rol(){
        $rolUsuario = Auth::user()->id_tipo_usuario;      
        if(in_array($rolUsuario, array(1, 20, 22)) == false){
          return true;
        }
    }

   
    public function honorarios (Request $request){

        if($this->rol()){
            return response()->view('errors.404');
        }

        $aniomes = $request->mes_plano;
        if($aniomes==null){
            $aniomes = date('mY',strtotime('-1 month',strtotime(date('Y-m-d'))));
            //$aniomes = 102020;
        }

        $convenios = Convenio::orwhere('id_seguro',2)->orwhere('id_seguro',5)->get();
        $array = [];$i=1;
        foreach ($convenios as $convenio) {
            
            $honor_medicos_activos = Db::table('archivo_plano_detalle as apd')
            ->join('ap_tipo_examen as apt','apt.tipo','apd.tipo')
            ->join('archivo_plano_cabecera as ap','ap.id','apd.id_ap_cabecera')
            ->join('paciente as p','p.id','ap.id_paciente')
            ->join('tipo_seguro as ts','ts.id','ap.id_tipo_seguro')
            /*->where(function ($query) {
            $query->where('ap.id_tipo_seguro','1')
                    ->orwhere('ap.id_tipo_seguro','2')
                    ->orwhere('ap.id_tipo_seguro','3')
                    ->orwhere('ap.id_tipo_seguro','4')
                    ->orwhere('ap.id_tipo_seguro','5');
            })*/
            //->where('ap.id_tipo_seguro','6') //jubilado
            //->where('ap.id_tipo_seguro','7') //jubilado campesino
            //->where('ap.id_tipo_seguro','8') //montepio
            ->where('mes_plano', $aniomes)
            ->where('apt.tipo_ex','HME')
            ->where('ap.estado','1')
            ->where('apd.estado','1')
            ->where('ap.id_seguro',$convenio->id_seguro)
            ->where('ap.id_empresa',$convenio->id_empresa)
            ->select(DB::raw('CONCAT(p.apellido1, p.apellido2, p.nombre1, p.nombre2) AS full_name'),'p.*','apd.tipo','apt.tipo_ex','apd.fecha','apd.descripcion','apd.codigo','apd.cantidad','apd.valor','apd.subtotal','apd.porcentaje10','apd.iva','apd.total','ts.nombre as tsnombre','ap.id_paciente','p.apellido1','p.apellido2','p.nombre1','p.nombre2','ap.fecha_ing','ap.nom_procedimiento','apd.porcentaje_honorario','apd.clasif_porcentaje_msp','ap.id_nivel')
            ->orderby('full_name')
            ->orderby('ap.fecha_ing', 'asc')
            ->get();

            $acodigo =[
                '93000','99253','99205','99005','99202','99203','99204','93005','202','203','204', '99213'    
            ];
            $ahanna = [
                '91034', '91037', '91010', '91110', '91122', '91013'     
            ];
            $consultas = [
                '99202', '99213'     
            ];
            $acum_robles = 0; $acum_hannah = 0;$aprb=[];$x=0;$acum_con=0;
            foreach ($honor_medicos_activos as $value) {
                if(!in_array($value->codigo, $acodigo)){
                    if( $value->tipo!='TA' && $value->tipo!='AN'){
                        $k_valor = 0;$val_total = 0;$val_unit = 0;$subtotal = 0;$valor10 = 0;$valor_iva  = 0;$total = 0;$val= 0;$val_porce =0;
                        $val_codigo = '0';
                        if($value->codigo == '70200004')
                        {
                            $val_codigo = '45380'; 
                            $val_total = 65.5;
                            $aprb[$x] = $value;  
                        }elseif($value->codigo == '70200003'){
                            $val_codigo = '43239';
                            $val_total = 34;
                            $aprb[$x] = $value;
                        }
                        if($val_codigo!='0'){
                            /*$val_proc = ApProcedimiento::where('codigo', $val_codigo)
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
                            $val_total = $total;
                            $aprb[$x] = $value;*/
                        }else{
                            $val_total = $value->total;
                            $aprb[$x] = $value;
                        } 
                        if (in_array($value->codigo, $ahanna)){
                            $acum_hannah += $val_total;    
                        }else{
                            $acum_robles += $val_total;    
                        }
                        $x++;
                    }
                }
                if(in_array($value->codigo,$consultas)){
                    $acum_con += $value->total;        
                }     
            }
            //dd($aprb);
            $array[$i] = [
                'id_seguro' => $convenio->id_seguro,
                'seguro'    => $convenio->seguro->nombre,
                'id_empresa'=> $convenio->id_empresa,
                'empresa'   => $convenio->empresa->nombre_corto,
                'acum_rob'  => $acum_robles,
                'acum_han'  => $acum_hannah,
                'acum_con'  => $acum_con,
            ];
            $i++;
            
        }
        //dd($array);
        return view('archivo_plano/reportes/honorarios',['honorarios' => $array, 'mes_plano' => $aniomes]);

    }

 


  

   
    
}
