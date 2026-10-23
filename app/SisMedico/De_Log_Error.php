<?php

namespace Sis_medico;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class De_Log_Error extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'de_log_error';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public static function getErrores($id)
    {
        return De_Log_Error::where('id_de_documento_electronico')->get();
    }
    public static function setErrorLog($datos)
    {
        $estado = '';
        DB::beginTransaction();
        try {
            De_Log_Error::insert($datos);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            $estado = 'Error: ' . $e->getMessage();
        }
        return $estado;
    }
}
