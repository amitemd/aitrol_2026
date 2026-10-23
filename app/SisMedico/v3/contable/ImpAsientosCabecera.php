<?php

namespace Sis_medico\v3\contable;

use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Database\Eloquent\Model;

class ImpAsientosCabecera extends Model
{
    private static $modelo = 'ImpAsientosCabecera';
    protected $table = 'ct_imp_asientos_cabecera';
    protected $primaryKey  = 'id';
    protected $keyType = 'string';

    
}
