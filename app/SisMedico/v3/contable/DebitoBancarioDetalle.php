<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\User;

class DebitoBancarioDetalle extends Model
{
    private static $modelo = 'Debito Bancario Detalle';
    protected $table = 'ct_debito_bancario_detalle';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $keyType = 'string';

    
}
