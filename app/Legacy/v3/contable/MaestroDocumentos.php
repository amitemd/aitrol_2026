<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MaestroDocumentos extends Model
{
    protected $table = 'de_maestro_documentos';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    public static function getIdMaestrosDocumentos($tipo)
    {
        $id = MaestroDocumentos::where('codigo', $tipo)->first(['id']);
        return $id->id;
    }
}
