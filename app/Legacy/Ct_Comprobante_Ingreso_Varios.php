<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Comprobante_Ingreso_Varios extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_comprobante_ingreso_varios';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    public function banco(){
        
        return $this->belongsTo('Sis_medico\Ct_Bancos','id_banco', 'id');
    }
    public function detalle(){
        
        return $this->hasMany('Sis_medico\Ct_Detalle_Comprobante_Ingreso_Varios','id_comprobante', 'id');
    }
    public function pago_ingresos(){
        
        return $this->hasMany('Sis_medico\Ct_Detalle_Pago_Ingreso_Varios','id_comprobante', 'id');
    }
    public function usuario(){
        
        return $this->belongsTo('Sis_medico\User','id_usuariocrea');
    }
    
}
