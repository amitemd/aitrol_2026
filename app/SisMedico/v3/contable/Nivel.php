<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Sis_medico\v3\contable\Clientes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Ct_Caja;
use Sis_medico\Http\Controllers\v3\contable\ClaveAcceso;
use Sis_medico\Venta_Kardex;

class Nivel extends Model
{
    private static $modelo = 'Nivel';
    protected $table = 'nivel';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $keyType = 'string';


   
}
