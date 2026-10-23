<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Ventas_Recibo_Cobro extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_venta_recibo_cobro';

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
    public function recibo_cobro(){
        return $this->belongsTo('Sis_medico\Ct_Orden_Venta', 'id_recibo');
    }
}
