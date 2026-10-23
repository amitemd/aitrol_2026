<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class VentaKardex extends Model
{
    private static $modelo = 'Venta Kardex';
    protected $table = 'venta_kardex';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at'; 
    const UPDATED_AT = 'updated_at';

}
