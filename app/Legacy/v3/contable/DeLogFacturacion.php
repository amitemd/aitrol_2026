<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class DeLogFacturacion extends Model
{
    private static $modelo = 'DeLogFacturacion';
    protected $table = 'de_log_facturacion';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
}
