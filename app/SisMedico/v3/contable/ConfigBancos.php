<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ConfigBancos extends Model
{
    private static $modelo = 'ConfigBancos';
    protected $table = 'ct_configuracion_bancos';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public static function getBancos($id = '')
    {
        if ($id == '') {
            return DB::table('ct_bancos')->where('estado', 1)->get();
        } else {
            return DB::table('ct_bancos')->find($id);
        }
    }
    public static function getAllBancos($term = '')
    {
        return ConfigBancos::selectRaw('id as id,nombre as text')
            ->where('nombre', 'like', '%' . $term . '%')->get(['nombre', 'id']);
    }
    public static function bancoCombo($id)
    {
        $combo = '';
        $banco = ConfigBancos::find($id);
        if ($banco != '') {
            $combo = '<option value="' . $banco->id . '" selected>' . $banco->nombre . '</option>';
        }
        return $combo;
    }
}
