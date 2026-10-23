<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Bodegas extends Model
{
    private static $modelo = 'bodegas';
    protected $table = 'ct_bodegas';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public static function traerBodegas($id = '')
    {
        if ($id == '') {
            return  Bodegas::where('estado', '1')->where('id_empresa', session("id_empresa"))->get();
        } else {
            return Bodegas::where('id', $id)->first();
        }
    }
}
