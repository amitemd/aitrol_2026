<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class AmortizacionPrestamoDetalle extends Model
{
    protected $table = 'amortizacion_prestamos_detalle';
    protected $guarded = [];
    protected $dates = ['fecha_pago', 'fecha_pago_real'];

    public function linea()
    {
        return $this->belongsTo(AmortizacionPrestamoLinea::class, 'id_linea');
    }
}
