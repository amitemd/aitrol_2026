<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Plan_Cuentas_Empresa extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'plan_cuentas_empresa';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function plan_cuentas()
    {
        return $this->hasMany('Sis_medico\Plan_Cuentas', 'id_plan', 'id');
    }

    public function padre()
    {
        return $this->belongsTo('Sis_medico\Plan_Cuentas_Empresa', 'id_padre','plan')->where('id_empresa',session()->get('id_empresa'));
    }
    public function hijos()
    {
        return $this->hasMany('Sis_medico\Plan_Cuentas_Empresa','id_padre','plan')->where('id_empresa',session()->get('id_empresa'));
    }
    public function hijos_2()
    {
        return $this->hasMany('Sis_medico\Plan_Cuentas_Empresa','id_padre','id_plan')->where('id_empresa',session()->get('id_empresa'));
    }
    public function modifica()
    {
        return $this->belongsTo('Sis_medico\User', 'id_usuariomod');
    }
}
