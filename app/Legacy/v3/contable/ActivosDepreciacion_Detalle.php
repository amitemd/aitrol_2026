<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\DB;

class ActivosDepreciacion_Detalle extends Model
{
    private static $modelo = 'ActivosDepreciacion_Detalle';
    protected $table = 'af_depreciacion_detalle';
    protected $primaryKey  = 'id';
    protected $keyType = 'string';

    public static function getDepreciacionDetalle($id)
    {
        return ActivosDepreciacion_Detalle::where('af_depreciacion_detalle.activo_id', $id)
        ->join('af_depreciacion_cabecera as adc','adc.id','af_depreciacion_detalle.depreciacion_cabecera_id')
        ->where('adc.estado', 1)
        ->where('adc.id_empresa',session('id_empresa'))
        ->select('adc.*', 'af_depreciacion_detalle.valordepreciacion')
        ->get();
    }

    public static function geDetHistorialDep($id)
    {
        return ActivosDepreciacion_Detalle::where('depreciacion_cabecera_id',$id)
        ->join('af_activo as a', 'a.id', 'af_depreciacion_detalle.activo_id')
        ->select('af_depreciacion_detalle.*', 'a.codigo', 'a.nombre')
        ->get();
    }
}