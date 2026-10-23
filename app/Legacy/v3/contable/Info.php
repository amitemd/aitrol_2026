<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class Info extends Model
{
    private static $modelo = 'Info';
    protected $table = 'new_db_info';
    protected $primaryKey  = 'id_info';
    const CREATED_AT = 'created_at_info';
    public static function getInfo()
    {
        $arrayMensajesGenerales = [];
        try {
            return  Info::orderBy('created_at_info', 'desc')->limit(1)->first();
        } catch (\Exception $e) {
            $dato = explode('\'', $e->getMessage());
            $arrayRespuesta = [
                'status' => 'error',
                'code' => 500,
                'model' => Info::$modelo,
                'table' => $dato[1],
                'message' => 'La empresa no tiene configurado el catalogo general',
            ];
            array_push($arrayMensajesGenerales, $arrayRespuesta);
        }
        $data['mensajesGenerales'] = $arrayMensajesGenerales;
    }
}
