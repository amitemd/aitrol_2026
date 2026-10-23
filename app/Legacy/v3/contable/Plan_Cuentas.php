<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class Plan_Cuentas extends Model
{
    protected $table = 'plan_cuentas';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    protected $keyType = 'string';

  

    public static function getCuentas($r,$id = '')
    {
        
        $id_empresa = $r->session()->get('id_empresa');
        $cuentas = Plan_Cuentas::where('p.estado', 2)->join('plan_cuentas_empresa as p', 'p.id_plan', 'plan_cuentas.id')->where('p.id_empresa', $id_empresa)->select('plan_cuentas.id', 'p.plan as plan', 'p.nombre as nombre')->get();

        return $cuentas;
    }


}
