<?php

namespace Sis_medico;

use Illuminate\Contracts\Session\Session;
use Illuminate\Database\Eloquent\Model;
use Sis_medico\Http\Requests\Request;

class Ct_Comprobante_Ingreso extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_comprobante_ingreso';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    public function cliente(){
        return $this->belongsTo('Sis_medico\Ct_Clientes','id_cliente', 'identificacion');
    }
    public function detalle(){
        
        return $this->hasMany('Sis_medico\Ct_Detalle_Comprobante_Ingreso','id_comprobante', 'id');
    }
    public function pago_ingresos(){
        
        return $this->hasMany('Sis_medico\Ct_Detalle_Pago_Ingreso','id_comprobante', 'id');
    }
    public function usuario(){
        
        return $this->belongsTo('Sis_medico\User','id_usuariocrea');
    }
    
}
