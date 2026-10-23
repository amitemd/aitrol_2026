<?php

namespace Sis_medico\v3\contable;
use Illuminate\Database\Eloquent\Model;

class DetalleRubroProveedores extends Model
{
    private static $modelo = 'DetalleRubroProveedores';
    protected $table = 'ct_detalle_rubro_acreedores';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

}
