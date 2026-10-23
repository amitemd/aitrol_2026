<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class ConciliacionMes extends Model
{
    protected $table = 'ct_conciliacion_mes';
    protected $primaryKey  = 'id';
    protected $keyType = 'string';
}
