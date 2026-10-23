<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Area extends Model
{
    private static $modelo = 'Area';
    protected $table = 'ct_rh_area';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public static function getArea($id = '')
    {
        if ($id == '') {
            return Area::all();
        } else {
            return Area::find($id);
        }
    }
}
