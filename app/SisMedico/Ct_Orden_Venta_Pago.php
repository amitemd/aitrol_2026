<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Orden_Venta_Pago extends Model
{
    //
    protected $table = 'ct_orden_venta_pago';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function orden()
    {
        return $this->belongsTo('Sis_medico\Ct_Orden_Venta', 'id_orden');

    }
    public function metodo()
    {
        return $this->belongsTo('Sis_medico\Ct_Tipo_Pago', 'tipo');

    }

    public function tarjeta()
    {
        return $this->belongsTo('Sis_medico\Ct_Tipo_Tarjeta', 'tipo_tarjeta');

    }

    public function ct_banco()
    {
        return $this->belongsTo('Sis_medico\Ct_Bancos', 'banco');

    }
}
