<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Divisas extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_divisas';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public static function getMoneda($divisa = '')
    {
        $moneda = 'DOLAR';
        $divisa = Ct_Divisas::where('id', $divisa)->first();
        if ($divisa != '') {
            $moneda = $divisa->descripcion;
            if ($moneda == 'Dólares')
                $moneda = 'DOLAR';
        }
        return $moneda;
    }
}
