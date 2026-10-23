<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Transferencia_Bancaria extends Model
{
    //
    protected $table = 'ct_transferencia_bancaria';
    
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function empresas()
    {
        return $this->belongsTo('Sis_medico\Empresa','empresa');
    }
    public function CuentaOrigen()
    {
        return $this->belongsTo('Sis_medico\Plan_Cuentas','id_cuenta_origen');
    }
    public function CuentaDestino()
    {
        return $this->belongsTo('Sis_medico\Plan_Cuentas','id_cuenta_destino');
    }
    public function usuario()
    {
        return $this->belongsTo('Sis_medico\User','id_usuariocrea');
    }
}
