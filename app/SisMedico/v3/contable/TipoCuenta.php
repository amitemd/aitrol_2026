<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TipoCuenta extends Model
{
    private static $modelo = 'TipoCuenta';
    protected $table = 'ct_rh_tipo_cuenta';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public static function getAllTiposCuentas($term = ''){
        return TipoCuenta::selectRaw('id as id,tipo_cuenta as text')
        ->where('estado',1)
        ->where('tipo_cuenta', 'like', '%' . $term . '%')->get();
    }
    public static function tipoCuentaCombo($id)
    {
        $combo = '';
        $tipo = TipoCuenta::find($id);
        if ($tipo != '') {
            $combo = '<option value="' . $tipo->id . '" selected>' . $tipo->tipo_cuenta . '</option>';
        }
        return $combo;
    }
}
