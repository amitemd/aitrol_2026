<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Cruce_Valores_Cliente extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_cruce_valores_cliente';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    public function cliente()
    {
        return $this->belongsTo('Sis_medico\Ct_Clientes','id_cliente' ,'identificacion');
    }
    public function detalles()
    {
        return $this->hasMany('Sis_medico\Ct_Detalle_Cruce_Clientes','id_comprobante' ,'id');
    }
    public function usuario(){
        
        return $this->belongsTo('Sis_medico\User','id_usuariocrea');
    }

}
