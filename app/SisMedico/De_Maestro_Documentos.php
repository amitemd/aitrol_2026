<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class De_Maestro_Documentos extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'de_maestro_documentos';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public static function getIdMaestroDocuemnto($tipo)
    {
        $id = De_Maestro_Documentos::where('codigo', $tipo)->first(['id']);
        return $id->id;
    }
}