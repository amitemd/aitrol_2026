<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Orden_documento extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'orden_documento';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function usuario()
    {
        return $this->belongsTo('Sis_medico\Examen_Orden', 'id_examen_orden');
    }

    public function usuariocrea()
    {
        return $this->belongsTo('Sis_medico\User', 'id_usuariocrea');
    }

    public function documentos()
    {
        return $this->belongsTo('Sis_medico\Labsm_documentos', 'tipo_documento');
    }



}
