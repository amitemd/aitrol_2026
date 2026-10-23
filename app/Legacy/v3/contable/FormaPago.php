<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class FormaPago extends Model
{
    protected $table = 'ct_forma_pago';
    protected $primaryKey  = 'id';
    protected $keyType = 'string';

    public static function getFormasPago()
    {
        return FormaPago::where('estado', '1')->get();
    }

    public function tipoPago()
    {
        return $this->belongsTo('Sis_medico\v3\contable\TipoPago', 'tipo');
    }
}