<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class MasterTipos extends Model
{
    protected $table = 'ct_master_tipos';
    protected $primaryKey  = 'id';
    protected $keyType = 'string';

    public static function getTiposComprobantes($tipo = '1')
    {
        return MasterTipos::where('estado', '1')->where('tipo', $tipo)->get([
            'codigo',
            'nombre'
        ]);
    }

    public static function getAllTiposComprobantes($term = '', $tipo = '1')
    {
        return MasterTipos::selectRaw('codigo as id,nombre as text')
            ->where('nombre', 'like', '%' . $term . '%')
            ->where('tipo', $tipo)->get(['nombre', 'id']);
    }
}
