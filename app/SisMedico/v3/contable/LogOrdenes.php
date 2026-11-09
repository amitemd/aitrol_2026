<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class LogOrdenes extends Model
{
    private static $modelo = 'Log Ordenes';
    protected $table = 'log_ordenes';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at'; 
    const UPDATED_AT = 'updated_at';

}
