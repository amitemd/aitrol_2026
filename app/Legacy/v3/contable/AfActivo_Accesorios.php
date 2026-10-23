<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\DB;

class AfActivo_Accesorios extends Model
{
    private static $modelo = 'af_activo_accesorios';
    protected $table = 'af_activo_accesorios';
    protected $primaryKey  = 'id';
    protected $keyType = 'string';

   

}
