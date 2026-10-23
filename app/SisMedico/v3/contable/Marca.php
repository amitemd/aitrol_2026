<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    private static $modelo = 'marca';
    protected $table = 'marca';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public static function getMarcaCombo($term)
    {
        return Marca::where('estado',1)
            ->where(function ($q) use ($term) {
                $q->orWhere('nombre', 'like', '%' . $term . '%');
                $q->orWhere('id', 'like', '%' . $term . '%');
            })->get([
                'id',
                'nombre',
            ]);
    }
    public static function getMarcas($id = '')
    {
        if ($id == '') {
            return Marca::where('estado',1)->get();
        } else {
            return Marca::find($id);
        }
    }
}
