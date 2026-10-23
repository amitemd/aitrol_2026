<?php 

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Orden extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'orden';

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

    public function paciente()
    {
        return $this->belongsTo('Sis_medico\Paciente','id_paciente');
    }

    public function orden_tipo()
    {
        return $this->hasMany('Sis_medico\Orden_Tipo','id_orden');   
    }

   
}
