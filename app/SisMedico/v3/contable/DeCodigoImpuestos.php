<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DeCodigoImpuestos extends Model
{
    private static $modelo = 'DeCodigoImpuestos';
    protected $table = 'de_codigo_impuestos';
    protected $primaryKey  = 'id';

   
}
