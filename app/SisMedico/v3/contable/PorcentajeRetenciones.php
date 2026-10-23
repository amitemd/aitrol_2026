<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PorcentajeRetenciones extends Model
{
    private static $modelo = 'Retenciones';
    protected $table = 'ct_porcentaje_retenciones';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    public static function getAllPorcentajes($tipo, $term = '')
    {
        return PorcentajeRetenciones::selectRaw('id as id,nombre as text')
            ->where('tipo', $tipo)
            ->where('nombre', 'like', '%' . $term . '%')->get();
    }
    public static function getPorcentajes($id = '')
    {
        if ($id == '') {
            return PorcentajeRetenciones::orderBy('tipo')
                ->get();
        } else {
            return PorcentajeRetenciones::find($id);
        }
    }

    public static function Geteverporcentajes($r)
    {   
       
        return PorcentajeRetenciones::where('tipo', $r)->where('estado', 1)->get();
    }
}
