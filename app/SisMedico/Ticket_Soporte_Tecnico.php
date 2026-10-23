<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ticket_Soporte_Tecnico extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ticket_soporte_tecnico';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];


    public function nombre()
    {
        return $this->belongsTo('Sis_medico\User', 'usuario_solicitante');
    }
    public function nombre1()
    {
        return $this->belongsTo('Sis_medico\User', 'id_usuariocrea');
    }
    public function u_responsable()
    {
        return $this->belongsTo('Sis_medico\User', 'responsable');
    }
}
