<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class AFacturaActivoCabecera extends Model
{
    private static $modelo = 'AF_Grupo';
    protected $table = 'af_factura_activo_cab';
    protected $primaryKey  = 'id';
    protected $keyType = 'string';

    

    public function datosproveedor()
    {
        return $this->belongsTo('Sis_medico\v3\contable\Proveedor', 'proveedor');
    }
   
    public function detalles()
    {
        return $this->hasMany('Sis_medico\v3\contable\AfFacturaActivoDetalle', 'fact_activo_id', 'id');
    }

}
