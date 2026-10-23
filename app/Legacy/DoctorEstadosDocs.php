<?php 

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class DoctorEstadosDocs extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'doctor_estados_docs';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    

}
