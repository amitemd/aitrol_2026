<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TipoProveedor extends Model
{
    private static $modelo = 'TipoProveedor';
    protected $table = 'tipoproveedor';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public static function getAllTiposProveedor($term = '')
    {
        return TipoProveedor::selectRaw('id as id,nombre as text')
            ->where('nombre', 'like', '%' . $term . '%')->get();
    }

    public static function getTipoProveedor($id = '')
    {
        if ($id != '') {
            return TipoProveedor::find($id);
        } else {
            return TipoProveedor::where('estado', 1)->get();
        }
    }
}
