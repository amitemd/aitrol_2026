<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\DB;


class ActivosFijosArchivos extends Model
{
    private static $modelo = 'ActivosFijosArchivos';
    protected $table = 'activo_fijo_archivos';
    protected $primaryKey  = 'id';
    protected $keyType = 'string';
}
