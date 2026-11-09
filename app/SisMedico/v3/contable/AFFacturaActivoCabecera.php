<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;

class AFFacturaActivoCabecera extends Model
{
    private static $modelo = 'Activo Fijo Factura Activo Cabecera';
    protected $table = 'af_factura_activo_cab';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $keyType = 'string';

}
