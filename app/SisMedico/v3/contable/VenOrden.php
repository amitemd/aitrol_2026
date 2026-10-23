<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VenOrden extends Model
{
    private static $modelo = 'CtVenOrden';
    protected $table = 'ct_ven_orden';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    public function detalles()
    {
        return $this->hasMany('Sis_medico\v3\contable\VenOrdenDetalle', 'id_ct_ven_orden');
    }
}
