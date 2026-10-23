<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Caja extends Model
{
    private static $modelo = 'Caja';
    protected $table = 'ct_caja';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public static function getPuntoEmision($id = '')
    {
        if ($id == '') {
            return Caja::where('id_empresa', session('id_empresa'))
                ->where('estado', 1)
                ->get();
        } else {
            return Caja::where('id', $id)->first();
        }
    }
}
