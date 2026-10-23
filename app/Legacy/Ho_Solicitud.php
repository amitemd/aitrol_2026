<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ho_Solicitud extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ho_solicitud';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function paciente()
    {
        return $this->belongsTo('Sis_medico\Paciente','id_paciente');
    }

    public function form008()
    {
        return $this->hasMany('Sis_medico\Ho_Form008', 'id_solicitud');
    }

    public function manchester()
    {
        return $this->hasMany('Sis_medico\Ho_Triaje_Manchester', 'id_ho_solicitud');
    }

    public function seguro()
    {
        return $this->belongsTo('Sis_medico\Seguro','id_seguro');
    }

    public function agenda()
    {
        return $this->belongsTo('Sis_medico\Agenda','id_agenda');
    }

    public function log()
    {
        return $this->hasMany('Sis_medico\Ho_Log_Solicitud', 'id_ho_solicitud');
    }


}