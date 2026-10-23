<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Detalle_Pago_Ingreso extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_detalle_pago_ingreso';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    
    public function cabecera_ingreso(){
        
        return $this->belongsTo('Sis_medico\Ct_Comprobante_Ingreso','id_comprobante');
    }
    public function cabecera_ingresov(){
        
        return $this->belongsTo('Sis_medico\Ct_Comprobante_Ingreso_Varios','id_comprobante');
    }

    public function banco(){
        
        return $this->belongsTo('Sis_medico\Ct_Bancos','id_banco', 'id');
    }

    public function tarjeta(){
        
        return $this->belongsTo('Sis_medico\Ct_Tipo_Tarjeta','id_tipo_tarjeta', 'id');
    }

    public function tipo_pago(){
        
        return $this->belongsTo('Sis_medico\Ct_Tipo_Pago','id_tipo', 'id');
    }
    public function detalle_ingreso(){
        
        return $this->belongsTo('Sis_medico\Ct_Detalle_Comprobante_Ingreso','id_comprobante', 'id_comprobante');
    }
    
}
