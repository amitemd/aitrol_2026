<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EstadoCivil extends Model
{
    private static $modelo = 'EstadoCivil';
    protected $table = 'ct_rh_estado_civil';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public static function getEstadoCivil($id = '')
    {
        if ($id == '') {
            return EstadoCivil::all();
        } else {
            return EstadoCivil::find($id);
        }
    }

}
