<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;
use Sis_medico\Ct_Rol_Pagos;
use Sis_medico\CtRhRubrosDetalleNomina;
use Illuminate\Support\Facades\Auth;

class CtRhRolDetalle extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_rh_rol_detalle';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public static function crea_detalle_rol($id_rol){

        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;

        $rol = Ct_Rol_Pagos::find($id_rol);
        if(!is_null($rol)){
            $rubros = CtRhRubrosDetalleNomina::where('estado','1')->get();
            foreach($rubros as $rubro){
                $array_rubro = [
                    'id_rubros_detalle_nomina' => $rubro->id,
                    'id_rol' => $id_rol,
                    'descripcion' => $rubro->descripcion,
                    'valor' => 0,
                    'ingreso_egreso' => $rubro->ingreso_egreso,
                    'debe_haber' => $rubro->debe_haber,
                    'editable' => $rubro->editable,
                    'marca' => $rubro->marca,
                    'estado' => '1',
                    'ip_creacion' => $ip_cliente, 
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                ];
                CtRhRolDetalle::create($array_rubro);
                    
            }    
        }

    }
    
}