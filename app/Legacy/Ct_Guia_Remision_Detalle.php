<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Guia_Remision_Detalle extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_remision_detalle';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public static function getDetalles($id)
    {
        return Ct_Guia_Remision_Detalle::where('id_cabecera_remision', $id)->get();
    }
    public function detalleProducto()
    {
        return $this->belongsTo('Sis_medico\Ct_productos', "id_producto");
    }
}
