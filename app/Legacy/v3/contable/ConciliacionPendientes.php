<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class ConciliacionPendientes extends Model
{
    protected $table = 'ct_conciliacion_pendientes';
    protected $primaryKey  = 'id';
    protected $keyType = 'string';
}
