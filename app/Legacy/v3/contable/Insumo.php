<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class Insumo extends Model
{
    private static $modelo = 'insumo';
    protected $table = 'producto';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public static function getInsumoCombo($term)
    {
        return Insumo::select('id', 'nombre as text')
            ->where('estado',1)
            ->where(function ($q) use ($term) {
                $q->orWhere('nombre', 'like', '%' . $term . '%');
                $q->orWhere('id', 'like', '%' . $term . '%');
            })->get();
    }
    public static function getInsumos($id = '')
    {
        if ($id == '') {
            return Insumo::where('estado',1)->get();
        } else {
            return Insumo::find($id);
        }
    }
}
