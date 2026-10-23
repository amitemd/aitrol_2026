<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LogContable extends Model
{
    private static $modelo = 'LogContable';
    protected $table = 'log_contable';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at'; 
    const UPDATED_AT = 'updated_at';

    public static function getAsientosAnulados(){
        $id_empresa = Session('id_empresa');
        $log = LogContable::selectRaw('COUNT(id_ant) as cantidad, id_ant')
            ->groupBy('id_ant')->get();
        $ids = array();
        foreach ($log as $value) {
            if ($value->cantidad > 1) {
                array_push($ids, $value->id_ant);
            }
        }
        return LogContable::whereIn('id_ant', $ids)
            ->join('ct_asientos_cabecera as cab', 'cab.id', 'log_contable.id_ant')
            ->join('ct_asientos_cabecera as cab2', function ($q) use ($id_empresa) {
                $q->on('cab2.id', 'log_contable.id_referencia')
                    ->where('cab2.id_empresa', '=', $id_empresa);
            })
            ->join('users as us', 'us.id', 'log_contable.id_usuariocrea')
            ->where('cab.id_empresa', $id_empresa)
            ->select('log_contable.*', DB::raw('CONCAT(us.nombre1, " ", us.apellido1) as nombre_usuario'), DB::Raw("(SELECT COUNT(lc.id_referencia) FROM log_contable lc 
            join `ct_asientos_cabecera` as `cab3` on `cab3`.`id` = lc.id_referencia AND cab3.id_empresa = $id_empresa  
            WHERE lc.id_ant = log_contable.id_ant
            GROUP BY lc.id_ant) as aux"))
            ->orderBy('log_contable.id_ant', 'ASC')
            ->get();
    }

    public static function DetalleLogContable($r)
    {
        $tablaHtml = '';
        $data = LogContable::where('id_ant', $r->id)
            ->orWhere('id_referencia', $r->id)
            ->first([
                'log_contable.id_ant',
                'log_contable.id_referencia',
                'log_contable.id'
            ]);
            if(!is_null($data)){
                if($data->id_ant == $r->id){
                    $tablaHtml .= "<label style='font-size: 15px' class='label label-danger'> El asiento se encuentra anulado. </label><br>";
                    $tablaHtml .= "<label style='font-size: 15px' class='label label-danger'> El asiento que se creo por anulacion es: <a href='javascript:;' onclick='visualizarAsiento(".$data->id_referencia.");'>" .$data->id_referencia."</a> </label>";
            
                }else if($data->id_referencia == $r->id){
                    $tablaHtml .= '<label style="font-size: 15px" class="label label-danger"> El asiento que se creo por anulacion. </label><br>';
                    $tablaHtml .= '<label style="font-size: 15px" class="label label-danger"> El asiento anulado es: <a href="javascript:;" onclick="visualizarAsiento('.$data->id_ant.');">' .$data->id_ant.'</a> </label>';
                }
            }
            
        return ['table' => $tablaHtml];
    }

}
