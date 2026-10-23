<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PSpell\Config;

class ConfigSistema extends Model
{
    private static $modelo = 'ConfigSistema';
    protected $table = 'new_db_config_sistema';
    protected $primaryKey  = 'id_config_sistema';
    const CREATED_AT = 'created_at_config_sistema';
    const UPDATED_AT = 'updated_at_config_sistema';

    public static function getConfiguraciones()
    {
        $arrayMensajesGenerales = [];
        try {
            return ConfigSistema::where('id_usuario_config_sistema', Auth::user()->id)->first();
        } catch (Exception $e) {
            $dato = explode('\'', $e->getMessage());
            $arrayRespuesta = [
                'status' => 'error',
                'code' => 500,
                'model' => ConfigSistema::$modelo,
                'table' => $dato[1],
                'message' => 'La empresa no tiene configurado el catalogo general',
            ];
            array_push($arrayMensajesGenerales, $arrayRespuesta);
        }
        $data['mensajesGenerales'] = $arrayMensajesGenerales;
    }
}
