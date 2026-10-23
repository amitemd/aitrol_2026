<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class Termino extends Model
{
    protected $table = 'ct_termino';
    protected $primaryKey  = 'id';
    protected $keyType = 'string';

    public static function traerDias($id)
    {
        $dias = self::find($id);
        if ($dias != '') {
            return $dias->dias;
        } else {
            return 0;
        }
    }
    public static function getTerminos($id = '')
    {
        if ($id == '') {
            return Termino::where('estado', '1')->get();
        } else {
            return Termino::find($id);
        }
    }

    public static function getAllTerminos($term = '')
    {
        return Termino::selectRaw('dias as id,nombre as text')
            ->where('nombre', 'like', '%' . $term . '%')
            ->get(['dias', 'text']);
    }
}