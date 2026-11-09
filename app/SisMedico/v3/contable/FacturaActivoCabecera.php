<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class FacturaActivoCabecera extends Model
{
    private static $modelo = 'factura activo cabecera';
    protected $table = 'af_factura_activo_cab';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    public function detalles()
    {
        return $this->hasMany('Sis_medico\v3\contable\AfFacturaActivoDetalle', 'fact_activo_id');
    }
   
}
