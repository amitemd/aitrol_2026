<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NivelAcademico extends Model
{
    private static $modelo = 'NivelAcademico';
    protected $table = 'ct_rh_nivel_academico';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public static function getNivelAcademico($id = '')
    {
        if ($id == '') {
            return NivelAcademico::all();
        } else {
            return NivelAcademico::find($id);
        }
    }
}
