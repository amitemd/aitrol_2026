<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AsientosCabecera extends Model
{
    protected $table = 'ct_asientos_cabecera';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $keyType = 'string';


    public function detalles()
    {
        return $this->hasMany('Sis_medico\v3\contable\AsientosDetalles', 'id_asiento_cabecera');
    }


    public static function getMayorizacion()
    {
        return AsientosCabecera::select(
            'ct_asientos_cabecera.id',
            'ct_asientos_cabecera.fecha_asiento',
            'plan_cuentas_empresa.nombre as nombre_empresa',
            'plan_cuentas.nombre as nombre_cuenta',
            'plan_cuentas_empresa.plan',
            'ct_asientos_cabecera.observacion',
            'ct_asientos_detalle.descripcion',
            'ct_asientos_detalle.debe',
            'ct_asientos_detalle.haber',
            \DB::raw('CONCAT(SUBSTRING(users_crea.nombre1, 1, 1), ". ", users_crea.apellido1) as usuariocrea'),
            \DB::raw('CONCAT(SUBSTRING(users_mod.nombre1, 1, 1), ". ", users_mod.apellido1) as usuariomod')
        )
            ->join('ct_asientos_detalle', 'ct_asientos_cabecera.id', 'ct_asientos_detalle.id_asiento_cabecera')
            ->join('plan_cuentas', 'ct_asientos_detalle.id_plan_cuenta', 'plan_cuentas.id')
            ->join('plan_cuentas_empresa', function ($q) {
                $q->on('plan_cuentas.id', 'plan_cuentas_empresa.id_plan')
                    ->on('plan_cuentas_empresa.id_empresa', 'ct_asientos_cabecera.id_empresa');
            })
            ->join('users as users_crea', 'ct_asientos_cabecera.id_usuariocrea', 'users_crea.id')
            ->join('users as users_mod', 'ct_asientos_cabecera.id_usuariomod', 'users_mod.id')
            ->where('ct_asientos_cabecera.id_empresa', session('id_empresa'))
            ->whereYear('ct_asientos_cabecera.fecha_asiento', session('periodo'))
            ->orderBy('plan_cuentas_empresa.plan', 'ASC')
            ->get();
    }
    public static function getLibroDiario($r)
    {
        return AsientosCabecera::select(
            'ct_asientos_cabecera.aparece_sri',
            'ct_asientos_cabecera.id',
            'ct_asientos_cabecera.fecha_asiento',
            'plan_cuentas_empresa.nombre as nombre_empresa',
            'plan_cuentas.nombre as nombre_cuenta',
            'plan_cuentas_empresa.plan',
            'ct_asientos_cabecera.observacion',
            'ct_asientos_detalle.descripcion',
            'ct_asientos_detalle.debe',
            'ct_asientos_detalle.haber',
            'ct_asientos_cabecera.created_at',
            'ct_asientos_cabecera.updated_at',
            DB::raw('SUM(ct_asientos_detalle.debe) OVER () AS total_debe'),
            DB::raw('SUM(ct_asientos_detalle.haber) OVER () AS total_haber'),
            DB::raw('SUM(ct_asientos_detalle.debe) OVER (PARTITION BY ct_asientos_detalle.id_asiento_cabecera) AS total_debe_'),
            DB::raw('SUM(ct_asientos_detalle.haber) OVER (PARTITION BY ct_asientos_detalle.id_asiento_cabecera) AS total_haber_'),
            DB::raw('CONCAT(SUBSTRING(users_crea.nombre1, 1, 1), ". ", users_crea.apellido1) as usuariocrea'),
            DB::raw('IF(ct_asientos_detalle.estado=0, CONCAT(SUBSTRING(users_crea.nombre1, 1, 1), ". ", users_mod.apellido1), "") AS usuariomod'),
            DB::raw('(SUM(ct_asientos_detalle.debe) OVER (PARTITION BY ct_asientos_detalle.id_asiento_cabecera) = SUM(ct_asientos_detalle.haber) OVER (PARTITION BY ct_asientos_detalle.id_asiento_cabecera)) AS cuadrado')
        )
            ->join('ct_asientos_detalle', 'ct_asientos_cabecera.id', 'ct_asientos_detalle.id_asiento_cabecera')
            ->join('plan_cuentas', 'ct_asientos_detalle.id_plan_cuenta', 'plan_cuentas.id')
            ->join('plan_cuentas_empresa', function ($q) {
                $q->on('plan_cuentas.id', 'plan_cuentas_empresa.id_plan')
                    ->on('plan_cuentas_empresa.id_empresa', 'ct_asientos_cabecera.id_empresa');
            })
            ->join('users as users_crea', 'ct_asientos_cabecera.id_usuariocrea', 'users_crea.id')
            ->join('users as users_mod', 'ct_asientos_cabecera.id_usuariomod', 'users_mod.id')
            ->where('ct_asientos_cabecera.id_empresa', session('id_empresa'))
            ->whereBetween('ct_asientos_cabecera.fecha_asiento', [$r->d . ' 00:00:00', $r->h . ' 23:59:59'])
            ->get();
    }

    public static function getAsientoCabecera($r)
    {
        return AsientosCabecera::leftjoin('users as u', 'ct_asientos_cabecera.id_usuariocrea', 'u.id')
            ->where('ct_asientos_cabecera.id', $r->id)
            ->first([
                'ct_asientos_cabecera.id',
                DB::raw('CONCAT(u.nombre1, " ", u.apellido1) as nombre_crea'),
                'ct_asientos_cabecera.created_at',
                'ct_asientos_cabecera.modulo',
                'ct_asientos_cabecera.valor',
                DB::raw('if(ct_asientos_cabecera.estado=0, "Inactivo", "Activo") AS estado'),
                'ct_asientos_cabecera.aparece_sri as sri',
                'ct_asientos_cabecera.observacion'
            ]);
    }
}
