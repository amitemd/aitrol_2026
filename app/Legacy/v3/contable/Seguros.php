<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;
use Sis_medico\Ct_ventas;
use Sis_medico\Plan_Cuentas;
use Sis_medico\v3\contable\Clientes;

class Seguros extends Model
{
    private static $modelo = 'Seguros';
    protected $table = 'seguros';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $keyType = 'string';

    public static function getSeguroCombo($id)
    {
        return Convenios::join('nivel as n', 'convenio.id_nivel', 'n.id')
            ->where('id_seguro', $id)
            ->where('id_empresa', session('id_empresa'))
            ->get([
                'n.id',
                'n.nombre',
            ]);
    }
    public static function getSegurosCombo($term)
    {
        return Seguros::select('id', 'nombre as text')
            ->where(function ($q) use ($term) {
                $q->orWhere('nombre', 'like', '%' . $term . '%');
                $q->orWhere('id', 'like', '%' . $term . '%');
            })
            ->where('inactivo', 1)
            ->get();
    }
    public static function getSeguros($id = '')
    {
        if ($id == '') {
            return Seguros::where('inactivo',1)->get();
        } else {
            return Seguros::find($id);
        }
    }
}
