<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Detalle_Pago_Ingreso_Varios extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_detalle_pago_ingreso_varios';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    public function banco(){
        
        return $this->belongsTo('Sis_medico\Ct_Bancos','id_banco', 'id');
    }

    public function tarjeta(){
        
        return $this->belongsTo('Sis_medico\Ct_Tipo_Tarjeta','id_tipo_tarjeta', 'id');
    }
    
}
