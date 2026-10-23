<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class DetalleRetenciones extends Model
{
    private static $modelo = 'DetalleRetenciones';
    protected $table = 'ct_detalle_retenciones';
    protected $primaryKey  = 'id';

    public static function getDetalles($id)
    {
        return DetalleRetenciones::where('id_retenciones', $id)->get();
    }
}
