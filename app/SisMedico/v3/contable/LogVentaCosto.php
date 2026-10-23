<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class LogVentaCosto extends Model
{
    private static $modelo = 'Log Venta Costo';
    protected $table = 'log_venta_costo';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
}
