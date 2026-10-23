<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Comprobante_Egreso_Solicitud extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_comprobante_egreso_solicitud';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function proveedor(){
       
        return $this->belongsTo('Sis_medico\Proveedor','id_proveedor','id');
        //return $this->belongsTo('Sis_medico\Ct_Acreedores', 'id');
    }
    public function bancoa()
    {
        return $this->belongsTo('Sis_medico\Ct_Caja_Banco', 'id_caja_banco');
    }
    public function detalles()
    {
        return $this->hasMany('Sis_medico\Ct_Detalle_Comprobante_Egreso_Solicitud', 'id_comprobante');
    }
    public function usuario(){
        
        return $this->belongsTo('Sis_medico\User','id_usuariocrea');
    }
    public function usuariomod(){
        
        return $this->belongsTo('Sis_medico\User','id_usuariomod');
    }
    public function asiento_cabecera(){
        return $this->belongsTo('Sis_medico\Ct_Asientos_Cabecera','id_asiento_cabecera');
    }
    public function tipo_pago(){
        return $this->belongsTo('Sis_medico\Ct_Tipo_Pago','id_pago');
    }

}
