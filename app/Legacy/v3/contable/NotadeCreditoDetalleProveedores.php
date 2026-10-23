<?php

namespace Sis_medico\v3\contable;
use Illuminate\Database\Eloquent\Model;

class NotadeCreditoDetalleProveedores extends Model
{
    private static $modelo = 'NotadeCreditoDetalleProveedores';
    protected $table = 'ct_detalle_credito_acreedores';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

}
