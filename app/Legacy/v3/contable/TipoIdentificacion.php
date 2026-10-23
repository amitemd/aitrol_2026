<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TipoIdentificacion extends Model
{
    private static $modelo = 'TipoIdentificacion';
    protected $table = 'new_db_tipo_identificacion';
    protected $primaryKey  = 'id_tipo_identificacion';
    const CREATED_AT = 'created_at_tipo_identificacion';
    const UPDATED_AT = 'updated_at_tipo_identificacion';

    public static function getTipoIdentificacion($id = '')
    {
        if ($id == '') {
            return TipoIdentificacion::all();
        } else {
            return TipoIdentificacion::find($id);
        }
    }
    public static function getAllTiposIdentificacion($term){
        return TipoIdentificacion::selectRaw('cod_sri_tipo_identificacion as id,tipo_identificacion as text')
            ->where('tipo_identificacion', 'like', '%' . $term . '%')->get();
    }
    public static function tipoIdentificacionCombo($id)
    {
        $combo = 'error';
        $tipo = TipoIdentificacion::find($id);
        $combo = '<option value="' . $tipo->cod_sri_tipo_identificacion . '" selected>' . $tipo->tipo_identificacion . '</option>';
        return $combo;
    }
}
