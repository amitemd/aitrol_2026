<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class DeLogErrores extends Model
{
    private static $modelo = 'DeLogErrores';
    protected $table = 'de_log_error';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
}
