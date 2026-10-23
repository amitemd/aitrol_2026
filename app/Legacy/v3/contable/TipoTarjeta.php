<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class TipoTarjeta extends Model
{
    protected $table = 'ct_tipo_tarjeta';
    protected $primaryKey  = 'id';
    protected $keyType = 'string';

    public static function getTiposTarjeta()
    {
        return TipoTarjeta::where('estado', '1')->get();
    }
}