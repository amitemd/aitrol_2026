<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class Divisas extends Model
{
    protected $table = 'ct_divisas';
    protected $primaryKey  = 'id';
    protected $keyType = 'string';

    public static function getDivisas($id = '')
    {
        if ($id == '') {
            return Divisas::all();
        } else {
            return Divisas::where('id', $id)->get();
        }
    }

    public static function traerDivisas($id = '')
    {
        return Divisas::where('estado', 1)->get();
    }
}
