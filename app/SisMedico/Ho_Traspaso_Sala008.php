<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ho_Traspaso_Sala008 extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ho_traspaso_sala008';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function hospital()
    {
        return $this->belongsTo('Sis_medico\Ho_Establecimientos','id_establecimiento');
    }
    public function paciente()
    {
        return $this->belongsTo('Sis_medico\Paciente','id_paciente');
    }
    public function condiciones()
    {
        return $this->belongsTo('Sis_medico\Ho_Condiciones','id_condicion');
    }
    public function sala()
    {
        return $this->belongsTo('Sis_medico\Sala','id_sala');
    }
    public function doctor()
    {
        return $this->belongsTo('Sis_medico\User','id_doctor');
    }
    public function solicitud()
    {
        return $this->belongsTo('Sis_medico\Ho_Solicitud','id_solicitud');
    }
}
