<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\DB;

class AF_BodegaSerieColor extends Model
{
    private static $modelo = 'AF_BodegaSerieColor';
    protected $table = 'af_bodega_serie_color';
    protected $primaryKey  = 'id';
    protected $keyType = 'string';

    public static function buscarResponsables($term)
    {
        return AF_BodegaSerieColor::selectRaw('nombre as id, nombre as text')
        ->where('nombre', 'like', '%' . $term . '%')
        ->where('estado', '1')
        ->where('tipo', '3')
        ->get();
    }

    public static function getColor($term)
    {
        return AF_BodegaSerieColor::selectRaw('nombre as id, nombre as text')
        ->where('nombre', 'like', '%' . $term . '%')
        ->where('estado', '1')
        ->where('tipo', '1')
        ->get();
    }

    public static function getSerie($term)
    {
        return AF_BodegaSerieColor::selectRaw('nombre as id, nombre as text')
        ->where('nombre', 'like', '%' . $term . '%')
        ->where('estado', '1')
        ->where('tipo', '2')
        ->get();
    }

}
