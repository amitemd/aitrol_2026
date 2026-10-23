<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\v3\LogAsiento;
use Sis_medico\Plan_Cuentas_Empresa;

class ImportacionesCompras extends Model
{
    protected $table = 'ct_importaciones_compras';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';

    public static function getImportaciones($id = '')
    {
        if ($id == '') {
            return ImportacionesCompras::join('proveedor as p', 'ct_importaciones_compras.proveedor', 'p.id')
                ->join('users as uc', 'ct_importaciones_compras.id_usuariocrea', '=', 'uc.id')
                ->leftJoin('users as um', 'ct_importaciones_compras.id_usuariomod', '=', 'um.id')
                ->get([
                    'ct_importaciones_compras.id',
                    'ct_importaciones_compras.numero',
                    'p.razonsocial',
                    'ct_importaciones_compras.observacion',
                    'ct_importaciones_compras.fecha',
                    'ct_importaciones_compras.secuencia_factura',
                    'ct_importaciones_compras.serie',
                    'ct_importaciones_compras.id_usuariocrea',
                    'ct_importaciones_compras.estado',
                    DB::raw('CONCAT(SUBSTRING(uc.nombre1, 1, 1), ". ", uc.apellido1) AS usuariocrea'),
                    DB::raw('IF(ct_importaciones_compras.estado=0, CONCAT(SUBSTRING(um.nombre1, 1, 1), ". ", um.apellido1), "") AS usuariomod'),
                ]);
        } else {
        }
    }
}
