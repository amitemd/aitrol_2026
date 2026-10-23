<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class ConciliacionBancaria extends Model
{
    protected $table = 'ct_conciliacion_bancaria';
    protected $primaryKey  = 'id';
    protected $keyType = 'string';
}
