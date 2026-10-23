<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class ProductosPaquete extends Model
{
    private static $modelo = 'Productos Paquete';
    protected $table = 'ct_productos_paquete';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at'; 
    const UPDATED_AT = 'updated_at';

}
