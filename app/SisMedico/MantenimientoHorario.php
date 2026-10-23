<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class MantenimientoHorario extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mantenimiento_horario';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    
    protected $guarded = [];
    public function horario()
    {
        return $this->belongsTo('Sis_medico\Sala','id_sala');
    }
    public function encargado()
    {
        return $this->belongsTo('Sis_medico\User','id_encargado');
    }
}
