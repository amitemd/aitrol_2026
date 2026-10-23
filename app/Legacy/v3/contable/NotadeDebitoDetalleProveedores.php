<?php

namespace Sis_medico\v3\contable;
use Illuminate\Database\Eloquent\Model;

class NotadeDebitoDetalleProveedores extends Model
{
    private static $modelo = 'NotadeDebitoDetalleProveedores';
    protected $table = 'ct_detalle_debito_acreedores';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

}
