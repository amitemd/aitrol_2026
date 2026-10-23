<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class DetalleAcreedores extends Model
{
    private static $modelo = 'DetalleAcreedores';
    protected $table = 'ct_detalle_acreedores';
    protected $primaryKey  = 'id';
}
