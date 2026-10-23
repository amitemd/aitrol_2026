<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class ImpGastos extends Model
{
    protected $table = 'ct_imp_gastos';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
}
