<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;

class DetalleCruceCuentasClientes extends Model
{
    private static $modelo = 'DetalleCruceCuentasClientes';
    protected $table = 'ct_detalle_cruce_cuentas_clientes';
    protected $primaryKey  = 'id';

  
}
