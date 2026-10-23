<?php 

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class DoctorConvenioEspecialidad extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'doctor_convenio_especialidad';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function doctor()
    {
        return $this->belongsTo('Sis_medico\User','id_doctor');
    }

    public function especialidad()
    {
        return $this->belongsTo('Sis_medico\Especialidad','id_especialidad');
    }

    public function convenio()
    {
        return $this->belongsTo('Sis_medico\Convenio','id_convenio');
    }
}
