<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ComprobanteSecuencia extends Model
{
    private static $modelo = 'Comprobante_Secuencia';
    protected $table = 'ct_comprobante_secuencia';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    
}