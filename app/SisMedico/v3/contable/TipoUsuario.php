<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Tipo;

class TipoUsuario extends Model
{
    private static $modelo = 'TipoUsuario';
    protected $table = 'tipousuario';
    //protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public static function  getTipos($id = '')
    {
        if ($id == '') {
            return TipoUsuario::all();
        } else {
            return TipoUsuario::find($id);
        }
    }

}
