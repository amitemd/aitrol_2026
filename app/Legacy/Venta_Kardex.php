<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Venta_Kardex extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'venta_kardex';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];


    public function venta()
    {
        return $this->belongsTo('Sis_medico\Ct_ventas', 'id_venta');
    }


    public function inv_cab_movimiento()
    {
        return $this->belongsTo('Sis_medico\InvCabMovimientos', 'id_cab_moviento');
    }

    public function asiento_costo()
    {
        return $this->belongsTo('Sis_medico\Log_Venta_Costo', 'id_venta', 'id_venta');
    }
}
