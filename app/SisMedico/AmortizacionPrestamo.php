<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class AmortizacionPrestamo extends Model
{
    protected $table = 'amortizacion_prestamos';
    protected $guarded = [];
    protected $dates = ['fecha_desembolso', 'fecha_primer_pago'];

    public function detalles()
    {
        return $this->hasMany(AmortizacionPrestamoDetalle::class, 'id_prestamo');
    }

    public function lineas()
    {
        return $this->hasMany(AmortizacionPrestamoLinea::class, 'id_prestamo');
    }
}
