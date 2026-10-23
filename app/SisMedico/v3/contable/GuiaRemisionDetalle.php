<?php

namespace Sis_medico\v3\contable;

use Sis_medico\Http\Controllers\ApiFacturacionController;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Exception;

class GuiaRemisionDetalle extends Model
{
    private static $modelo = 'GuiaRemisionDetalle';
    protected $table = 'ct_remision_detalle';
    protected $primaryKey  = 'id';
    protected $keyType = 'string';


    public function detalleProducto()
    {
        return $this->belongsTo('Sis_medico\v3\contable\Productos', "id_producto");
    }

}
