<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EgresosMasivos extends Model
{
    private static $modelo = 'EgresosMasivos';
    protected $table = 'ct_comprobante_egreso_masivo';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
}
