<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Examen_Orden_Seguimiento extends Model
{
    protected $table = 'examen_orden_seguimiento';

    protected $guarded = [];

    public function orden()
    {
        return $this->belongsTo('Sis_medico\Examen_Orden', 'id_examen_orden');
    }

    public function usuario_crea()
    {
        return $this->belongsTo('Sis_medico\User', 'id_usuariocrea');
    }
}
