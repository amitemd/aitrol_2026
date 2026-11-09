<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class PorcentajeImpuestoRenta extends Model
{
    private static $modelo = 'Retenciones';
    protected $table = 'ct_porcentaje_impuesto_renta';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
}
