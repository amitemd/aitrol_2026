<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class AmortizacionPrestamoLinea extends Model
{
    protected $table = 'amortizacion_prestamos_lineas';
    protected $guarded = [];
    protected $dates = ['fecha_primer_pago'];

    public function prestamo()
    {
        return $this->belongsTo(AmortizacionPrestamo::class, 'id_prestamo');
    }

    public function detalles()
    {
        return $this->hasMany(AmortizacionPrestamoDetalle::class, 'id_linea');
    }
}
