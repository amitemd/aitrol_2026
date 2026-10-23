<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class RolAsiento extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rol_asiento';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function cuentas()
    {
        return $this->hasMany('Sis_medico\RolAsientoCuentas','id_rol_asiento');
    }
    
    public function obtiene_sucursal()
    {
        return $this->belongsTo('Sis_medico\Ct_Sucursales', 'id_sucursal');
    }
    public function rubros()
    {
        return $this->hasMany('Sis_medico\RolAsientoRubros','id_rol_asiento');
    }

}
