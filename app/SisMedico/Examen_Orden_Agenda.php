<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Examen_Orden_Agenda extends model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'examen_orden_agenda';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
    public function orden()
    {
        return $this->belongsTo('Sis_medico\Examen_Orden','id_orden');
    }

    public function agenda()
    {
        return $this->belongsTo('Sis_medico\Agenda','id_agenda');
    }
    
    public function crea()
    {
        return $this->belongsTo('Sis_medico\User','id_usuariocrea');
    }
    public function modifica()
    {
        return $this->belongsTo('Sis_medico\User','id_usuariomod');
    }
    

}