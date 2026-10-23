<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Deposito_Bancario extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_deposito_bancario';

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

        return $this->belongsTo('Sis_medico\User', 'id_usuariocrea');
    }
    public function modifica()
    {

        return $this->belongsTo('Sis_medico\User', 'id_usuariomod');
    }

    public function detalles(){
        
        return $this->hasMany('Sis_medico\Ct_Detalle_Deposito_Bancario', 'deposito_bancario_id');
    }
    
}
