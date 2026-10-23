<?php

namespace Sis_medico\v3\contable;

use DB;
use Illuminate\Database\Eloquent\Model;

class DetalleAts extends Model
{
    private static $modelo = 'Ats';
    protected $table = 'ct_detalle_ats';
    protected $primaryKey  = 'id';
    protected $keyType = 'string';
}
