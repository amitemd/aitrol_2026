<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class TipoPago extends Model
{
    protected $table = 'ct_tipo_pago';
    protected $primaryKey  = 'id';
    protected $keyType = 'string';

    public static function getTipoPago()
    {
        return TipoPago::where('estado', '1')->get();
    }
}