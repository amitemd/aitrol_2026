<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use Sis_medico\User;

class ClinicalProtocolStatisticsService
{
    public function monthlyProduction()
    {

        $doctores       = User::where('id_tipo_usuario', '3')->where('estado', '1')->orderBy('nombre1')->get();
        $seguros        = \Sis_medico\Seguro::query()->from('seguros as s')->where('s.inactivo', '1')->get();
        $procedimientos = DB::table('hc_procedimientos as hp')->join('historiaclinica as h', 'h.hcid', 'hp.id_hc')->join('agenda as a', 'a.id', 'h.id_agenda')->join('paciente as p', 'p.id', 'h.id_paciente')->wherenull('id_procedimiento_completo')->where('hp.estado', '1')->whereYear('a.fechaini', '2019')->whereMonth('a.fechaini', '09')->select(DB::raw('YEAR(a.fechaini) as year, MONTH(a.fechaini) as month'), 'a.fechaini', 'h.id_paciente', 'p.apellido1', 'p.apellido2', 'p.nombre1', 'p.nombre2', 'hp.id as hpid', 'hp.id_seguro', 'a.id_empresa as aempresa', 'hp.id_empresa as hpempresa', 'a.id as agenda', 'hp.estimado_minimo')->orderBy('a.id')->get();

        //MARCA QUIEN NO TIENE PROCEDIMIENTO FINAL
        /*foreach ($procedimientos as $val) {
        $px = Hc_Procedimiento_Final::where('id_hc_procedimientos',$val->hpid)->get();
        if($px->count()==0){
        $arr=[
        'estado' => '0',
        'ip_creacion' => 'SIN_PROC'
        ];
        hc_procedimientos::find($val->hpid)->update($arr);
        }
        }*/
        //ACTUALIZA EMPRESA
        /*foreach ($procedimientos as $val) {
        $px = Hc_Procedimiento_Final::where('id_hc_procedimientos',$val->hpid)->get();
        if($px->count()>0){
        $arr=[
        'id_empresa' => $val->aempresa,
        ];
        hc_procedimientos::find($val->hpid)->update($arr);
        }
        } */
        //carga valores estimados
        /*foreach ($procedimientos as $val) {

        $px = Hc_Procedimiento_Final::where('id_hc_procedimientos',$val->hpid)->get();
        if($px->count()=='1'){
        if($val->id_seguro=='1'){

        $valor = DB::table('estimado_procedimientos_convenios')->where('id_seguro','1')->where('id_procedimiento',$px['0']->id_procedimiento)->first();//particulares

        if(!is_null($valor)){
        $arr=[
        'estimado_minimo' => $valor->valor,
        ];
        hc_procedimientos::find($val->hpid)->update($arr);
        }
        }else{
        $seguro = Seguro::find($val->id_seguro);
        if($seguro->tipo=='0'){
        $valor = DB::table('estimado_procedimientos_convenios')->where('id_seguro','2')->where('id_procedimiento',$px['0']->id_procedimiento)->first();

        if(!is_null($valor)){
        $arr=[
        'estimado_minimo' => $valor->valor,
        ];
        hc_procedimientos::find($val->hpid)->update($arr);
        }

        }
        }

        }
        if($px->count()>'1'){
        if($val->id_seguro=='1'){
        $arr=[
        'estimado_minimo' => '999',
        ];
        hc_procedimientos::find($val->hpid)->update($arr);

        }else{
        $seguro = Seguro::find($val->id_seguro);
        if($seguro->tipo=='0'){
        $txt_pro = '';$cont = 0;
        foreach ($px as $pt) {
        if($cont == 0){
        $txt_pro = $pt->id_procedimiento;
        $cont ++;
        }else{
        $txt_pro = $txt_pro.'+'.$pt->id_procedimiento;
        }
        }
        //dd($txt_pro);
        $valor = DB::table('estimado_procedimientos_convenios')->where('id_seguro','2')->where('id_procedimiento',$txt_pro)->first();

        if(!is_null($valor)){
        $arr=[
        'estimado_minimo' => $valor->valor,
        ];
        hc_procedimientos::find($val->hpid)->update($arr);
        }

        }
        }

        }
        else{

        }
        }*/

        /*
        foreach ($procedimientos as $val){//AGREGA PROCEDIMIENTOS SECUNDARIOS
        $pentax = Pentax::where('id_agenda', $val->agenda)->first();
        if(!is_null($pentax)){
        $id_principal=0;$id_secundario = 0;
        $pproc = PentaxProc::where('id_pentax',$pentax->id)->get();
        //dd($pproc);
        foreach($pproc as $p1){

        if($p1->procedimiento->id_grupo_procedimiento != null){
        $id_principal=$p1->id_procedimiento;
        }
        if($id_principal != 0){
        $id_secundario=$p1->id_procedimiento;
        if($id_secundario!=0 && $id_principal!=0){

        $px_sec = Hc_Procedimiento_Final::where('id_hc_procedimientos',$val->hpid)->where('id_procedimiento',$id_secundario)->first();
        if(is_null($px_sec)){
        $px_pri = Hc_Procedimiento_Final::where('id_hc_procedimientos',$val->hpid)->where('id_procedimiento',$id_principal)->first();
        if(!is_null($px_pri)){
        //dd($id_secundario,$id_principal);
        $input_pro_final = [
        'id_hc_procedimientos' => $val->hpid,
        'id_procedimiento' => $id_secundario,
        'id_usuariocrea' => '0922290697',
        'ip_modificacion' => 'SISTEMAS',
        'id_usuariomod' => '0922290697',
        'ip_creacion' => 'SISTEMAS',
        ];

        Hc_Procedimiento_Final::create($input_pro_final);

        }
        }
        }
        }

        }

        }
        } */

        return view('hc_admision.procedimientos.estadisticos', ['procedimientos' => $procedimientos, 'nombres' => null, 'doctores' => $doctores, 'seguros' => $seguros, 'id_doctor1' => null, 'id_seguro' => null]);
    }

}
