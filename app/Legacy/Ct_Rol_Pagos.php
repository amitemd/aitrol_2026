<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Rol_Pagos extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_rol_pagos';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function ct_nomina()
    {
        return $this->belongsTo('Sis_medico\Ct_Nomina','id_nomina');
        
    }

    public function empresa()
    {
        return $this->belongsTo('Sis_medico\Empresa','id_empresa');
        
    }

    public function usuario()
    {
        return $this->belongsTo('Sis_medico\User','id_user');
        
    }

    public function detalle()
    {
        return $this->hasMany('Sis_medico\Ct_Detalle_Rol','id_rol');
        
    }

    public function tipo_rol()
    {
        return $this->belongsTo('Sis_medico\Ct_Tipo_Rol','id_tipo_rol');
        
    }

    public function prestamo_detalle()
    {
        return $this->hasMany('Sis_medico\Ct_Rh_Prestamos_Detalle','id_ct_rol_pagos');
        
    }

    public function saldo_detalle()
    {
        return $this->hasMany('Sis_medico\Ct_Rh_Saldos_Iniciales_Detalle','id_ct_rol_pagos');
        
    }

    public function otros_anticipos()
    {
        return $this->hasMany('Sis_medico\Ct_Rh_Otros_Anticipos','id_ct_rol');
        
    }

    public function cuotas_hipotecarios()
    {
        return $this->hasMany('Sis_medico\Ct_Rh_Cuotas_Hipotecarios','id_rol');
        
    }

    public function cuotas_quirografario()
    {
        return $this->hasMany('Sis_medico\Ct_Rh_Cuotas_Quirografario','id_rol');
        
    }

    public function formas_de_pago()
    {
        return $this->hasMany('Sis_medico\Ct_Rol_Forma_Pago','id_rol_pago');
        
    }

    public function rubros_detalles()
    {
        return $this->hasMany('Sis_medico\CtRhRolDetalle','id_rol');
        
    }

}
