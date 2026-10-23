<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class Procedimiento extends Model
{
    private static $modelo = 'procedimiento';
    protected $table = 'procedimiento';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public static function getProcedimientoCombo($term)
    {
        return Procedimiento::select('id', 'nombre as text')
            ->where('estado',1)
            ->where(function ($q) use ($term) {
                $q->orWhere('nombre', 'like', '%' . $term . '%');
                $q->orWhere('id', 'like', '%' . $term . '%');
            })->get();
    }
    public static function getProcedimientos($id = '')
    {
        if ($id == '') {
            return Procedimiento::where('estado',1)->get();
        } else {
            return Procedimiento::find($id);
        }
    }
}
