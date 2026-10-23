<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Nomina extends Model
{
    //
    protected $table = 'ct_nomina';
    
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /*public function empresa()
    {
        return $this->belongsTo('Sis_medico\Empresa','id_empresa')->with('empresa');
        
    }*/

    /*public function usuario()
    {
        return $this->belongsTo('Sis_medico\User','id_user')->with('usuario');
        
    }*/

    public function user()
    {
        return $this->belongsTo('Sis_medico\User','id_user');
    }

    public function roles()
    {
        return $this->hasMany('Sis_medico\Ct_Rol_Pagos','id_nomina');   
    }

    public function aportepersonal()
    {
        return $this->belongsTo('Sis_medico\Ct_Rh_Valores','aporte_personal');
    }
     public function empresa()
    {
        return $this->belongsTo('Sis_medico\Empresa','id_empresa');
    }
    public function sucursales()
    {
        return $this->belongsTo('Sis_medico\Ct_Sucursales','id_sucursal');
    }

}
