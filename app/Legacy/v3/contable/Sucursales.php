<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;
use Sis_medico\Ct_ventas;
use Sis_medico\Plan_Cuentas;
use Sis_medico\v3\contable\Clientes;

class Sucursales extends Model
{
    private static $modelo = 'Sucursales';
    protected $table = 'ct_sucursales';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $keyType = 'string';

    public static function getSucursales($id = '')
    {
        if ($id == '') {
            return Sucursales::where('estado', 1)
                ->where('id_empresa', session('id_empresa'))
                ->get();
        } else {
            return Sucursales::where('id', $id)->first();
        }
    }
    public static function getSucursal($id){
        return Sucursales::where('estado', 1)
        ->where('id_empresa', session('id_empresa'))
        ->first();
    }

    public static function getSucursalNomina($id = '')
    {
        
        if ($id == '') {
            //dd("VVV");
            return  Sucursales::where('estado', 1)
                ->where('id_empresa', session('id_empresa'))
                ->where('uso_nomina','1')
                ->get();
           //dd($sucursal);
        } elseif($id != '') {
            return Sucursales::where('id', $id)->first();
        }else{
            return [];
        }
    }
}
