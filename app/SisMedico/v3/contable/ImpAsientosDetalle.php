<?php

namespace Sis_medico\v3\contable;

use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Database\Eloquent\Model;

class ImpAsientosDetalle extends Model
{
    private static $modelo = 'ImpAsientosDetalle';
    protected $table = 'ct_imp_asientos_detalle';
    protected $primaryKey  = 'id';
    protected $keyType = 'string';

    
}
