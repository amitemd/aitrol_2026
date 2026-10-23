<?php 

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class DoctorConsultorio extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'doctor_consultorio';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

   
}
