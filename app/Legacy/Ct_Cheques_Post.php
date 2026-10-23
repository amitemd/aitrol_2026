<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;
class Ct_Cheques_Post extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_cheques_post';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function cliente(){
        
        return $this->belongsTo('Sis_medico\Ct_Clientes','id_cliente', 'identificacion');
    }
    public function pago_ingresos(){
        
        return $this->hasMany('Sis_medico\Ct_Detalle_Pago_Ingreso','id_comprobante', 'id');
    }
    public function detalles(){
        
        return $this->belongsTo('Sis_medico\Ct_Detalle_Cheque_Post','id_comprobante', 'id');
    }
    public function usuario(){
        
        return $this->belongsTo('Sis_medico\User','id_usuariocrea');
    }
    
}