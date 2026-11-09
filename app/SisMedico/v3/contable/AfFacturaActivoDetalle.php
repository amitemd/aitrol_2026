<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class AfFacturaActivoDetalle extends Model
{
    private static $modelo = 'AF_Grupo';
    protected $table = 'af_factura_activo_det';
    protected $primaryKey  = 'id';
    protected $keyType = 'string';


    public function activo()
    {
        return $this->belongsTo('Sis_medico\v3\contable\AfActivo', 'activo_id', 'id');
    }

}
