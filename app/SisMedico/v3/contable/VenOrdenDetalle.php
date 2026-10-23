<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VenOrdenDetalle extends Model
{
    private static $modelo = 'VenOrdenDetalle';
    protected $table = 'ct_ven_orden_detalle';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


  
}
