<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Detalle_Comprobante_Ingreso_Varios extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_detalle_comprobante_ingreso_varios';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function ventas()
    {
        return $this->belongsTo('Sis_medico\Ct_Ventas', 'id_factura');
    }

}