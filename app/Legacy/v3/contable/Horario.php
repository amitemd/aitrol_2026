<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Horario extends Model
{
    private static $modelo = 'Horario';
    protected $table = 'ct_rh_horario';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public static function getHorario($id = '')
    {
        if ($id == '') {
            return Horario::all();
        } else {
            return Horario::find($id);
        }
    }
}
