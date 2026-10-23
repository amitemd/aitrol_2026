<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Paises extends Model
{
    private static $modelo = 'Paises';
    protected $table = 'pais';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public static function getPaises($id = '')
    {
        if ($id == '') {
            return Paises::all();
        } else {
            return Paises::find($id);
        }
    }
}
