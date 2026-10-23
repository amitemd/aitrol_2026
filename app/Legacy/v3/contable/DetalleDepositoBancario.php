<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;

class DetalleDepositoBancario extends Model
{
    private static $modelo = 'DetalleDepositoBancario';
    protected $table = 'ct_detalle_deposito_bancario';
    protected $primaryKey  = 'id';

  
}
