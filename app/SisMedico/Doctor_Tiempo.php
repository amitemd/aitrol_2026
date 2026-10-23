<?php 

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Doctor_Tiempo extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'doctor_tiempo';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

   
}
