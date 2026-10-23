<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Cruce_Cuentas_Clientes extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_cruce_cuentas_clientes';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    public function empresa()
    {
        return $this->belongsTo('Sis_medico\Empresa', 'id_empresa');
    }
    public function cliente()
    {
        return $this->belongsTo('Sis_medico\Ct_Clientes', 'id_cliente');
    }

    public function usuario(){
        
        return $this->belongsTo('Sis_medico\User','id_usuariocrea');
    }
    public function detalles(){
        
        return $this->hasMany('Sis_medico\Ct_Detalle_Cruce_Cuentas_Clientes','id_comprobante');
    }

}
