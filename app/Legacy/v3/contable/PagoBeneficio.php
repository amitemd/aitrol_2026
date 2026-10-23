<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PagoBeneficio extends Model
{
    private static $modelo = 'PagoBeneficio';
    protected $table = 'ct_rh_pago_beneficio';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public static function getPagoBeneficio($id = '')
    {
        if ($id == '') {
            return PagoBeneficio::all();
        } else {
            return PagoBeneficio::find($id);
        }
    }
}
