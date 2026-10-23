<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class TicketPermiso extends Model
{
    protected $table = 'ticket_permiso';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function nombre()   
    {
        return $this->belongsTo('Sis_medico\User', 'cedula');
    }
}
