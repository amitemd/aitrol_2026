<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DetalleComprobanteEgreso extends Model
{
    private static $modelo = 'DetalleComprobanteEgreso';
    protected $table = 'ct_detalle_comprobante_egreso';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    public function compras()
    {
        return $this->belongsTo('Sis_medico\v3\contable\Compras', 'id_compra','id');
    }

    
}
