<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Plan_Cuentas extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'plan_cuentas';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    protected $keyType = 'string';

    public function padre()
    {
        return $this->belongsTo('Sis_medico\Plan_Cuentas', 'id_padre');
    }
    public function hijos()
    {
        return $this->hasMany('Sis_medico\Plan_Cuentas', 'id_padre', 'id');
    }
    public function modifica()
    {
        return $this->belongsTo('Sis_medico\User', 'id_usuariomod');
    }
    public function pempresa()
    {
        return $this->belongsTo('Sis_medico\Plan_Cuentas_Empresa', 'id','id_plan')->where('id_empresa',session()->get('id_empresa'));
    }
}
