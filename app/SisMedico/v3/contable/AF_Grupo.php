<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\DB;

class AF_Grupo extends Model
{
    private static $modelo = 'AF_Grupo';
    protected $table = 'af_grupo';
    protected $primaryKey  = 'id';
    protected $keyType = 'string';

    public static function getAF_Grupo($id = '')
    {
        if ($id == '') {
            return AF_Grupo::get([
                    'id',
                    'nombre',
                ]);
        } else {
            return AF_Grupo::where('af_tipo.id', $id)
                ->first([
                    'id',
                    'nombre',
                ]);
        }
    }

}
