<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Rh_Acumulados extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_rh_acumulados';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function obtiene_sucursal()
    {
        return $this->belongsTo('Sis_medico\Ct_Sucursales', 'id_sucursal');
    }
    public function obtiene_detalles(){
        return $this->hasMany('Sis_medico\Ct_Rh_Acumulados_Detalle', 'id_ct_rh_acumulados');
    }

 public function ct_nomina()
    {
        return $this->belongsTo('Sis_medico\Ct_Nomina','id_nomina');
        
    }



}
