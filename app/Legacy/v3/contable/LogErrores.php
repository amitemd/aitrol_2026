<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LogErrores extends Model
{
    private static $modelo = 'LogErrores';
    protected $table = 'de_log_error';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public static function setErrorLog($datos)
    {
        $estado = '';
        DB::beginTransaction();
        try {
            LogErrores::insert($datos);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            $estado = 'Error: ' . $e->getMessage();
        }
        return $estado;
    }
}
