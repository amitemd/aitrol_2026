<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ho_Log_Solicitud extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ho_log_solicitud';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function agenda()
    {
        return $this->belongsTo('Sis_medico\Agenda','id_agenda');
    }

    public function paso()
    {
        return $this->belongsTo('Sis_medico\Ho_estado_paso','estado_paso');
    }
    

}