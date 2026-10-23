<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Detalle_Deposito_Bancario extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_detalle_deposito_bancario';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];


    public function tipo_pago(){
        
        return $this->belongsTo('Sis_medico\Ct_Tipo_Pago','tipo', 'id');
    }

    public function banc(){
        
        return $this->belongsTo('Sis_medico\Ct_Bancos','banco', 'id');
    }
    public function ingreso(){
        
        return $this->belongsTo('Sis_medico\Ct_Detalle_Pago_Ingreso','id_ingreso', 'id');
    }
    public function deposito_bancario(){
        
        return $this->belongsTo('Sis_medico\Ct_Deposito_Bancario','deposito_bancario_id');
    }
    
}
