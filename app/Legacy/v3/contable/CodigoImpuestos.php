<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CodigoImpuestos extends Model
{
    private static $modelo = 'CodigoImpuestos';
    protected $table = 'de_codigo_impuestos';
    protected $primaryKey  = 'id';
    protected $keyType = 'string';

    public static function getCodigoRetenciones($tipo)
    {
        if ($tipo == 'RENTA') {
            return PorcentajeRetenciones::where('tipo', 2)->get();
        } else {
            return PorcentajeRetenciones::where('tipo', 1)->get();
        }
    }
}
